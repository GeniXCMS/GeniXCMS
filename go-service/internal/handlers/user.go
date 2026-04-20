package handlers

import (
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/jmoiron/sqlx"
)

type UserHandler struct {
	DB     *sqlx.DB
	Driver string
}

type userRecord struct {
	ID       int    `db:"id" json:"id"`
	UserID   string `db:"userid" json:"userid"`
	Email    string `db:"email" json:"email"`
	JoinDate string `db:"join_date" json:"join_date"`
	Status   string `db:"status" json:"status"`
	Group    int    `db:"group" json:"group"`
	Country  string `db:"country" json:"country"`
}

func (h *UserHandler) ListUsers(c *gin.Context) {
	num := 10
	if n := c.Query("num"); n != "" {
		if parsed, err := strconv.Atoi(n); err == nil && parsed > 0 {
			num = parsed
		}
	}

	offset := 0
	if o := c.Query("offset"); o != "" {
		if parsed, err := strconv.Atoi(o); err == nil && parsed >= 0 {
			offset = parsed
		}
	}

	q := strings.TrimSpace(c.Query("q"))
	group := strings.TrimSpace(c.Query("group"))
	status := strings.TrimSpace(c.Query("status"))

	userTable := quoteIdent(h.Driver, "user")
	userDetailTable := quoteIdent(h.Driver, "user_detail")
	groupCol := quoteIdent(h.Driver, "group")

	whereClauses := make([]string, 0)
	args := make([]any, 0)

	if q != "" {
		whereClauses = append(whereClauses, fmt.Sprintf("(u.userid LIKE ? OR u.email LIKE ?)") )
		args = append(args, "%"+q+"%", "%"+q+"%")
	}

	if group != "" {
		whereClauses = append(whereClauses, fmt.Sprintf("u.%s = ?", groupCol))
		args = append(args, group)
	}

	if status != "" {
		whereClauses = append(whereClauses, "u.status = ?")
		args = append(args, status)
	}

	whereSQL := ""
	if len(whereClauses) > 0 {
		whereSQL = " WHERE " + strings.Join(whereClauses, " AND ")
	}

	countSQL := fmt.Sprintf(
		"SELECT COUNT(*) FROM %s u LEFT JOIN %s ud ON u.userid = ud.userid%s",
		userTable, userDetailTable, whereSQL,
	)

	var total int
	countQuery := h.DB.Rebind(countSQL)
	if err := h.DB.Get(&total, countQuery, args...); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "message": "Failed to count users"})
		return
	}

	querySQL := fmt.Sprintf(
		"SELECT u.id, u.userid, u.email, u.join_date, u.status, u.%s AS %s, COALESCE(ud.country, '') AS country FROM %s u LEFT JOIN %s ud ON u.userid = ud.userid%s ORDER BY u.id DESC LIMIT ? OFFSET ?",
		groupCol, groupCol, userTable, userDetailTable, whereSQL,
	)

	querySQL = h.DB.Rebind(querySQL)
	args = append(args, num, offset)
	users := []userRecord{}
	if err := h.DB.Select(&users, querySQL, args...); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "message": "Failed to fetch users"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"data":   users,
		"total":  total,
		"limit":  num,
		"offset": offset,
	})
}

func quoteIdent(driver, identifier string) string {
	cleaned := strings.ReplaceAll(identifier, "`", "``")
	if strings.EqualFold(driver, "postgres") {
		return `"` + strings.ReplaceAll(identifier, `"`, `""`) + `"`
	}
	return "`" + cleaned + "`"
}
