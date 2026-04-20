package handlers

import (
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/genixcms/go-service/internal/models"
	"github.com/genixcms/go-service/internal/response"
)

// CategoryHandler handles /api/categories/* routes.
type CategoryHandler struct {
	Repo *models.CategoryRepository
}

// Index → GET /api/categories?type=post
func (h *CategoryHandler) Index(c *gin.Context) {
	catType := c.DefaultQuery("type", "")
	cats, err := h.Repo.All(catType)
	if err != nil {
		response.Error(c, http.StatusInternalServerError, "Failed to fetch categories")
		return
	}
	response.Success(c, cats)
}

// Show → GET /api/categories/:id
func (h *CategoryHandler) Show(c *gin.Context) {
	slug := c.Param("id")
	cat, err := h.Repo.BySlug(slug)
	if err != nil {
		response.Error(c, http.StatusNotFound, "Category not found")
		return
	}
	response.Success(c, cat)
}
