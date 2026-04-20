package handlers

import (
	"net/http"
	"os"
	"strings"

	"github.com/gin-gonic/gin"
)

type VersionHandler struct {
	VersionFile string
}

func (h *VersionHandler) Index(c *gin.Context) {
	version, err := os.ReadFile(h.VersionFile)
	if err != nil {
		c.JSON(http.StatusOK, gin.H{"status": "ok", "version": "2.3.0"}) // Fallback
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"status":  "ok",
		"version": strings.TrimSpace(string(version)),
	})
}
