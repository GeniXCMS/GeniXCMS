package handlers

import (
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/jmoiron/sqlx"
	"github.com/genixcms/go-service/internal/response"
)

type DynamicHandler struct {
	DB *sqlx.DB
}

func (h *DynamicHandler) Index(c *gin.Context) {
	resource := c.Param("resource")
	
	// Plug n Play Mapping from Header (passed by PHP Proxy)
	// This allows PHP modules to tell Go which table to query
	targetTable := c.GetHeader("X-GX-Table")
	if targetTable == "" {
		targetTable = resource
	}

	// Security: simple sanitization to prevent table name injection
	targetTable = strings.ReplaceAll(targetTable, ";", "")
	targetTable = strings.ReplaceAll(targetTable, " ", "")

	num, _ := strconv.Atoi(c.DefaultQuery("num", "10"))
	offset, _ := strconv.Atoi(c.DefaultQuery("offset", "0"))
	q := c.Query("q")

	baseQuery := fmt.Sprintf("SELECT * FROM %s", targetTable)
	countQuery := fmt.Sprintf("SELECT COUNT(*) FROM %s", targetTable)
	args := []interface{}{}

	if q != "" {
		// Simple dynamic search for common column names if q is provided
		// Developers can expand this logic
		baseQuery += " WHERE (title LIKE ? OR name LIKE ? OR content LIKE ?)"
		countQuery += " WHERE (title LIKE ? OR name LIKE ? OR content LIKE ?)"
		args = append(args, "%"+q+"%", "%"+q+"%", "%"+q+"%")
	}

	// Count Total
	var total int
	err := h.DB.Get(&total, countQuery, args...)
	if err != nil {
		response.Error(c, http.StatusNotFound, "Resource or Table not found: "+err.Error())
		return
	}

	// Fetch Data using Generic Map to support any table structure (Plug n Play)
	baseQuery += " LIMIT ? OFFSET ?"
	fetchArgs := append(args, num, offset)
	
	rows, err := h.DB.Queryx(baseQuery, fetchArgs...)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "Query Error: "+err.Error())
		return
	}
	defer rows.Close()

	var results []map[string]interface{}
	for rows.Next() {
		row := make(map[string]interface{})
		err := rows.MapScan(row)
		if err != nil {
			continue
		}
		
		// Clean up potential byte slice issues from some drivers
		for k, v := range row {
			if b, ok := v.([]byte); ok {
				row[k] = string(b)
			}
		}
		results = append(results, row)
	}

	response.Paginated(c, results, total, num, offset)
}

func (h *DynamicHandler) Show(c *gin.Context) {
	resource := c.Param("resource")
	id := c.Param("id")
	
	targetTable := c.GetHeader("X-GX-Table")
	if targetTable == "" {
		targetTable = resource
	}

	result := make(map[string]interface{})
	err := h.DB.QueryRowx("SELECT * FROM "+targetTable+" WHERE id = ?", id).MapScan(result)
	if err != nil {
		response.Error(c, http.StatusNotFound, "Record not found")
		return
	}

	// Clean up byte slices
	for k, v := range result {
		if b, ok := v.([]byte); ok {
			result[k] = string(b)
		}
	}

	response.Success(c, result)
}
