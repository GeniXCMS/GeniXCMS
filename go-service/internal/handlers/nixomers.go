package handlers

import (
	"database/sql"
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/jmoiron/sqlx"
	"github.com/genixcms/go-service/internal/response"
)

type NixomersHandler struct {
	DB *sqlx.DB
}

type NixOrder struct {
	ID               int      `db:"id" json:"id"`
	OrderID          *string  `db:"order_id" json:"order_id"`
	CustomerName     *string  `db:"customer_name" json:"customer_name"`
	CustomerEmail    *string  `db:"customer_email" json:"customer_email"`
	CustomerPhone    *string  `db:"customer_phone" json:"customer_phone"`
	ShippingCity     *string  `db:"shipping_city" json:"shipping_city"`
	ShippingProvince *string  `db:"shipping_province" json:"shipping_province"`
	Total            float64  `db:"total" json:"total"`
	Status           *string  `db:"status" json:"status"`
	Date             *string  `db:"date" json:"date"`
	PaymentStatus    *string  `db:"payment_status" json:"payment_status"`
	PaymentMethod    *string  `db:"payment_method" json:"payment_method"`
}

type NixTransaction struct {
	ID          int      `db:"id" json:"id"`
	TransID     *string  `db:"trans_id" json:"trans_id"`
	OrderID     *string  `db:"order_id" json:"order_id"`
	Type        *string  `db:"type" json:"type"`
	Description *string         `db:"description" json:"description"`
	Amount      sql.NullFloat64 `db:"amount" json:"amount"`
	Fee         sql.NullFloat64 `db:"fee" json:"fee"`
	Tax         sql.NullFloat64 `db:"tax" json:"tax"`
	Net         sql.NullFloat64 `db:"net" json:"net"`
	Method      *string         `db:"method" json:"method"`
	Status      *string  `db:"status" json:"status"`
	Date        *string  `db:"date" json:"date"`
}

func (h *NixomersHandler) ListOrders(c *gin.Context) {
	num, _ := strconv.Atoi(c.DefaultQuery("num", "10"))
	offset, _ := strconv.Atoi(c.DefaultQuery("offset", "0"))
	q := c.Query("q")
	status := c.DefaultQuery("status", "all")
	sort := c.DefaultQuery("sort", "newest")

	// Base Query with join to latest transaction only
	// Explicitly select columns to avoid "missing destination name" errors with sqlx
	query := `SELECT o.id, o.order_id, o.customer_name, o.customer_email, o.customer_phone, 
			         o.shipping_city, o.shipping_province, o.total, o.status, o.date,
			         t.status as payment_status, t.method as payment_method 
			  FROM nix_orders o 
			  LEFT JOIN (
				  SELECT order_id, status, method, MAX(id) as max_id 
				  FROM nix_transactions 
				  GROUP BY order_id
			  ) t ON o.order_id = t.order_id`
	
	where := []string{}
	args := []interface{}{}

	if q != "" {
		where = append(where, "(o.customer_name LIKE ? OR o.order_id LIKE ? OR o.customer_email LIKE ?)")
		args = append(args, "%"+q+"%", "%"+q+"%", "%"+q+"%")
	}

	if status != "all" {
		where = append(where, "o.status = ?")
		args = append(args, status)
	}

	fullQuery := query
	if len(where) > 0 {
		fullQuery += " WHERE "
		for i, w := range where {
			if i > 0 {
				fullQuery += " AND "
			}
			fullQuery += w
		}
	}

	// Count Total - use the same WHERE filters as the list query
	var total int
	countSQL := "SELECT COUNT(*) FROM nix_orders o"
	if len(where) > 0 {
		countSQL += " WHERE " + strings.Join(where, " AND ")
	}
	err := h.DB.Get(&total, countSQL, args...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "Failed to count: "+err.Error())
		return
	}

	// Order By
	switch sort {
	case "oldest":
		fullQuery += " ORDER BY o.id ASC"
	case "highest":
		fullQuery += " ORDER BY o.total DESC"
	case "lowest":
		fullQuery += " ORDER BY o.total ASC"
	default:
		fullQuery += " ORDER BY o.id DESC"
	}

	// Limit Offset
	fullQuery += " LIMIT ? OFFSET ?"
	args = append(args, num, offset)

	orders := []NixOrder{}
	err = h.DB.Select(&orders, fullQuery, args...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "SQL Error: "+err.Error())
		return
	}

	response.Paginated(c, orders, total, num, offset)
}

func (h *NixomersHandler) ListTransactions(c *gin.Context) {
	num, _ := strconv.Atoi(c.DefaultQuery("num", "15"))
	offset, _ := strconv.Atoi(c.DefaultQuery("offset", "0"))
	q := c.Query("q")
	typeFilter := c.DefaultQuery("type", "all")
	from := c.Query("start_date")
	to := c.Query("end_date")

	baseQuery := `SELECT id, trans_id, order_id, type, description, amount, fee, tax, net, method, status, date FROM nix_transactions`
	countQuery := `SELECT COUNT(*) FROM nix_transactions`
	where := []string{}
	args := []interface{}{}

	if q != "" {
		where = append(where, `(description LIKE ? OR trans_id LIKE ? OR order_id LIKE ?)`)
		args = append(args, "%"+q+"%", "%"+q+"%", "%"+q+"%")
	}

	if typeFilter != "all" {
		where = append(where, "`type` = ?")
		args = append(args, typeFilter)
	}

	if from != "" {
		where = append(where, "date >= ?")
		args = append(args, from+" 00:00:00")
	}

	if to != "" {
		where = append(where, "date <= ?")
		args = append(args, to+" 23:59:59")
	}

	if len(where) > 0 {
		filter := " WHERE " + strings.Join(where, " AND ")
		baseQuery += filter
		countQuery += filter
	}

	var total int
	err := h.DB.Get(&total, countQuery, args...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "Count Error: "+err.Error())
		return
	}

	baseQuery += " ORDER BY id DESC LIMIT ? OFFSET ?"
	args = append(args, num, offset)

	rowsResult, err := h.DB.Queryx(baseQuery, args...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "SQL Error: "+err.Error())
		return
	}
	defer rowsResult.Close()

	transactions := make([]map[string]interface{}, 0)
	for rowsResult.Next() {
		m := map[string]interface{}{}
		if err := rowsResult.MapScan(m); err != nil {
			response.Error(c, http.StatusInternalServerError, "SQL Scan Error: "+err.Error())
			return
		}
		transactions = append(transactions, m)
	}

	headers := []map[string]any{
		{"content": "Txn ID / Ref", "class": "ps-4 py-3"},
		{"content": "Type"},
		{"content": "Description"},
		{"content": "Gross Amount"},
		{"content": "Fee"},
		{"content": "Tax"},
		{"content": "Net Amount"},
		{"content": "Method / Status"},
		{"content": "Date"},
		{"content": "Action", "class": "pe-4 text-center"},
	}

	rows := make([][]map[string]any, 0, len(transactions))
	for _, t := range transactions {
		transactionType := strings.ToUpper(getString(t["type"], "UNKNOWN"))
		badgeColor := "danger"
		if transactionType == "INCOME" {
			badgeColor = "success"
		}

		status := getString(t["status"], "")
		statusColor := "secondary"
		statusLabel := "Pending"
		switch status {
		case "completed":
			statusColor = "success"
			statusLabel = "completed"
		case "refunded":
			statusColor = "warning"
			statusLabel = "refunded"
		case "cancelled":
			statusColor = "danger"
			statusLabel = "cancelled"
		case "":
			statusColor = "secondary"
			statusLabel = "Pending"
		default:
			statusColor = "secondary"
			statusLabel = status
		}

		id := getInt(t["id"])
		transID := getString(t["trans_id"], "-")
		orderID := getString(t["order_id"], "-")
		description := getString(t["description"], "-")
		method := getString(t["method"], "Manual")
		date := getString(t["date"], "-")
		amount := getFloatString(t["amount"])
		fee := getFloatString(t["fee"])
		tax := getFloatString(t["tax"])
		net := getFloatString(t["net"])

		row := []map[string]any{
			{"content": fmt.Sprintf("<div><strong class=\"text-primary\">#TX-%05d</strong><br><small class=\"text-muted extra-small\">%s</small></div>", id, transID), "class": "ps-4"},
			{"content": fmt.Sprintf("<span class=\"badge bg-%s bg-opacity-10 text-%s rounded-pill px-3 py-2 fw-bold text-uppercase small\">%s</span>", badgeColor, badgeColor, transactionType)},
			{"content": fmt.Sprintf("<div><strong>%s</strong><br><small class=\"text-muted\">Ref: Order #%s</small></div>", description, orderID)},
			{"content": fmt.Sprintf("<span class=\"fw-bold text-dark\">%s</span>", amount)},
			{"content": fmt.Sprintf("<span class=\"text-danger extra-small\">-%s</span>", fee)},
			{"content": fmt.Sprintf("<span class=\"text-warning extra-small\">-%s</span>", tax)},
			{"content": fmt.Sprintf("<span class=\"fw-bold text-%s\">%s</span>", badgeColor, net)},
			{"content": fmt.Sprintf("<div><span class=\"badge bg-light text-dark border px-2 py-1 small rounded-3 mb-1\">%s</span><br><span class=\"text-%s extra-small fw-bold text-uppercase\">%s</span></div>", method, statusColor, statusLabel)},
			{"content": date},
			{"content": fmt.Sprintf("<div class=\"dropdown\"><button class=\"btn btn-light btn-sm rounded-circle border shadow-none\" data-bs-toggle=\"dropdown\"><i class=\"bi bi-three-dots-vertical\"></i></button><ul class=\"dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2\" style=\"z-index: 9999;\" data-bs-boundary=\"viewport\"><li><a class=\"dropdown-item rounded-3 small fw-bold\" href=\"#\">View Detail</a></li><li><a class=\"dropdown-item rounded-3 small fw-bold text-danger\" href=\"#\">Delete Record</a></li></ul></div>"), "class": "text-center pe-4"},
		}

		rows = append(rows, row)
	}

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"headers": headers,
		"data":    rows,
		"total":   total,
		"limit":   num,
		"offset":  offset,
	})
}

func floatOrZero(value sql.NullFloat64) float64 {
	if !value.Valid {
		return 0
	}
	return value.Float64
}

func getString(value interface{}, fallback string) string {
	switch v := value.(type) {
	case string:
		if v == "" {
			return fallback
		}
		return v
	case []byte:
		s := string(v)
		if s == "" {
			return fallback
		}
		return s
	case nil:
		return fallback
	default:
		return fallback
	}
}

func getFloatString(value interface{}) string {
	switch v := value.(type) {
	case float64:
		return fmt.Sprintf("%.2f", v)
	case float32:
		return fmt.Sprintf("%.2f", v)
	case int:
		return fmt.Sprintf("%.2f", float64(v))
	case int64:
		return fmt.Sprintf("%.2f", float64(v))
	case []byte:
		s := string(v)
		if s == "" {
			return "0.00"
		}
		f, err := strconv.ParseFloat(s, 64)
		if err != nil {
			return "0.00"
		}
		return fmt.Sprintf("%.2f", f)
	case string:
		if v == "" {
			return "0.00"
		}
		f, err := strconv.ParseFloat(v, 64)
		if err != nil {
			return "0.00"
		}
		return fmt.Sprintf("%.2f", f)
	case sql.NullFloat64:
		if !v.Valid {
			return "0.00"
		}
		return fmt.Sprintf("%.2f", v.Float64)
	default:
		return "0.00"
	}
}

func getInt(value interface{}) int {
	switch v := value.(type) {
	case int:
		return v
	case int64:
		return int(v)
	case float64:
		return int(v)
	case []byte:
		s := string(v)
		i, err := strconv.Atoi(s)
		if err != nil {
			return 0
		}
		return i
	case string:
		i, err := strconv.Atoi(v)
		if err != nil {
			return 0
		}
		return i
	default:
		return 0
	}
}

type NixNotification struct {
	ID         int    `db:"id" json:"id"`
	Type       string `db:"type" json:"type"`
	Title      string `db:"title" json:"title"`
	Message    string `db:"message" json:"message"`
	URL        string `db:"url" json:"url"`
	TargetRole string `db:"target_role" json:"target_role"`
	CreatedAt  string `db:"created_at" json:"created_at"`
}

func (h *NixomersHandler) Notifications(c *gin.Context) {
	username := c.Query("username")
	roles := c.Query("roles") // Comma separated roles from PHP
	if roles == "" {
		roles = "all"
	}
	roleList := strings.Split(roles, ",")

	// Build the IN clause for roles
	queryRoles := "n.target_role IN ("
	args := []interface{}{}
	for i, r := range roleList {
		if i > 0 {
			queryRoles += ","
		}
		queryRoles += "?"
		args = append(args, r)
	}
	queryRoles += ")"
	args = append(args, username) // Extra arg for the LEFT JOIN join condition

	// Count unread: We need a direct query because SQLX JOIN with extra conditions is tricky in Get
	var count int
	countQuery := fmt.Sprintf(`SELECT COUNT(n.id) FROM nix_notifications n 
		LEFT JOIN nix_notifications_read nr ON n.id = nr.notification_id AND nr.username = ?
		WHERE %s AND nr.id IS NULL`, queryRoles)
	
	// Prepare args for count: username first because it's in the JOIN condition
	countArgs := []interface{}{username}
	countArgs = append(countArgs, args[:len(args)-1]...)

	err := h.DB.Get(&count, countQuery, countArgs...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "Count Error: "+err.Error())
		return
	}

	// Fetch latest 5
	latest := []NixNotification{}
	fetchQuery := fmt.Sprintf(`SELECT n.* FROM nix_notifications n 
		LEFT JOIN nix_notifications_read nr ON n.id = nr.notification_id AND nr.username = ?
		WHERE %s AND nr.id IS NULL 
		ORDER BY n.id DESC LIMIT 5`, queryRoles)

	err = h.DB.Select(&latest, fetchQuery, countArgs...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "Fetch Error: "+err.Error())
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"count":  count,
		"latest": latest,
	})
}
