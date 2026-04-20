package handlers

import (
	"log"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"github.com/genixcms/go-service/internal/models"
	"github.com/genixcms/go-service/internal/response"
)

// PostHandler handles /api/posts/* routes.
type PostHandler struct {
	Repo    *models.PostRepository
}

// Index → GET /api/posts
// Supports: ?type=post&status=1&cat=0&q=keyword&limit=20&offset=0
func (h *PostHandler) Index(c *gin.Context) {
	filters := map[string]string{
		"type":   c.DefaultQuery("type", "post"),
		"status": c.DefaultQuery("status", ""),
		"cat":    c.DefaultQuery("cat", "0"),
		"q":      c.DefaultQuery("q", ""),
	}
	
	limit, _  := strconv.Atoi(c.DefaultQuery("limit", "20"))
	offset, _ := strconv.Atoi(c.DefaultQuery("offset", "0"))

	if limit > 200 { limit = 200 } // Cap max for admin use

	posts, total, err := h.Repo.Find(filters, limit, offset)
	if err != nil {
		log.Printf("[go-service] Error finding posts: %v", err)
		response.Error(c, http.StatusInternalServerError, "Failed to fetch posts: "+err.Error())
		return
	}
	response.Paginated(c, posts, total, limit, offset)
}

// Show → GET /api/posts/:id
func (h *PostHandler) Show(c *gin.Context) {
	id := c.Param("id")

	// Compatibility: redirect action=list_posts style requests to Index
	if id == "list_posts" || c.Query("action") == "list_posts" {
		h.Index(c)
		return
	}

	var post *models.Post
	var err  error

	// Try numeric ID first
	if numID, err2 := strconv.Atoi(id); err2 == nil {
		post, err = h.Repo.ByID(numID)
	} else {
		post, err = h.Repo.BySlug(id)
	}

	if err != nil || post == nil {
		response.Error(c, http.StatusNotFound, "Post not found")
		return
	}

	// Attach params
	params, _ := h.Repo.Params(post.ID)
	paramMap := map[string]string{}
	for _, p := range params {
		paramMap[p.Name] = p.Value
	}

	c.JSON(200, gin.H{
		"status":  "success",
		"message": "Operation successful",
		"data": gin.H{
			"post":   post,
			"params": paramMap,
		},
	})
}

// Submit → POST /api/posts  (write operations — proxied back to PHP for hook safety)
func (h *PostHandler) Submit(c *gin.Context) {
	response.Error(c, http.StatusNotImplemented,
		"Write operations are handled by GeniXCMS PHP backend. Switch api_backend to 'php' for writes.")
}

// Update → PUT /api/posts/:id
func (h *PostHandler) Update(c *gin.Context) {
	h.Submit(c)
}

// Delete → DELETE /api/posts/:id
func (h *PostHandler) Delete(c *gin.Context) {
	h.Submit(c)
}
