package handlers

import (
	"fmt"
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/jmoiron/sqlx"
)

type TagHandler struct {
	DB *sqlx.DB
}

func (h *TagHandler) Index(c *gin.Context) {
	term := c.DefaultQuery("term", "")
	postType := c.DefaultQuery("type", "post")

	tagType := "tag"
	if postType != "post" {
		tagType = fmt.Sprintf("%s_tag", postType)
	}

	query := h.DB.Rebind("SELECT name FROM cat WHERE type = ? AND name LIKE ? ORDER BY name ASC")
	var tags []string
	err := h.DB.Select(&tags, query, tagType, "%"+term+"%")
	
	if err != nil {
		c.JSON(http.StatusOK, []string{})
		return
	}

	c.JSON(http.StatusOK, tags)
}
