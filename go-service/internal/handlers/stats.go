package handlers

import (
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/jmoiron/sqlx"
)

type StatsHandler struct {
	DB *sqlx.DB
}

func (h *StatsHandler) Index(c *gin.Context) {
	stats := make(map[string]interface{})

	// Execution of multiple counts
	stats["posts_total"] = h.getCount("SELECT COUNT(*) FROM posts")
	stats["posts_active"] = h.getCount("SELECT COUNT(*) FROM posts WHERE status = '1'")
	
	stats["categories_total"] = h.getCount("SELECT COUNT(*) FROM cat WHERE type = 'category'")
	
	stats["users_total"] = h.getCount("SELECT COUNT(*) FROM user WHERE `group` > 0")
	stats["users_active"] = h.getCount("SELECT COUNT(*) FROM user WHERE `group` > 0 AND status = '1'")
	
	stats["comments_total"] = h.getCount("SELECT COUNT(*) FROM comments")
	stats["comments_pending"] = h.getCount("SELECT COUNT(*) FROM comments WHERE status = '2'")

	c.JSON(http.StatusOK, stats)
}

func (h *StatsHandler) getCount(query string) int {
	var count int
	err := h.DB.Get(&count, query)
	if err != nil {
		return 0
	}
	return count
}
