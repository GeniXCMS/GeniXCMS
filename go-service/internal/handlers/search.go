package handlers

import (
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/jmoiron/sqlx"
)

type SearchHandler struct {
	DB *sqlx.DB
}

func (h *SearchHandler) Index(c *gin.Context) {
	q := c.DefaultQuery("q", "")
	if q == "" {
		c.JSON(http.StatusOK, []string{})
		return
	}

	q = "%" + strings.TrimSpace(q) + "%"
	limit := c.DefaultQuery("limit", "20")

	// Search in Posts (Title & Content)
	query := h.DB.Rebind("SELECT id, title, content, type, date FROM posts WHERE (title LIKE ? OR content LIKE ?) AND status = '1' ORDER BY date DESC LIMIT ?")
	
	rows, err := h.DB.Queryx(query, q, q, limit)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "message": err.Error()})
		return
	}
	defer rows.Close()

	var results []map[string]interface{}
	for rows.Next() {
		row := make(map[string]interface{})
		rows.MapScan(row)
		
		// Clean binary data and snippet the content
		if content, ok := row["content"].([]byte); ok {
			strContent := string(content)
			if len(strContent) > 150 {
				row["content"] = strContent[:150] + "..."
			} else {
				row["content"] = strContent
			}
		}
		if title, ok := row["title"].([]byte); ok {
			row["title"] = string(title)
		}
		
		results = append(results, row)
	}

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"query":  c.Query("q"),
		"data":   results,
	})
}
