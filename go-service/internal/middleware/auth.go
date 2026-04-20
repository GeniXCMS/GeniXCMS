package middleware

import (
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/genixcms/go-service/internal/response"
)

// InternalAuth validates the X-GX-Secret header for requests coming from GeniXCMS proxy.
// This prevents direct external access bypassing PHP auth.
func InternalAuth(secret string) gin.HandlerFunc {
	return func(c *gin.Context) {
		if secret == "" {
			// No secret configured — allow all (dev mode)
			c.Next()
			return
		}
		incoming := c.GetHeader("X-GX-Secret")
		if incoming != secret {
			response.Error(c, http.StatusUnauthorized, "Unauthorized: invalid internal secret")
			c.Abort()
			return
		}
		c.Next()
	}
}

// CORS adds permissive CORS headers for direct browser access (optional, disable in prod).
func CORS() gin.HandlerFunc {
	return func(c *gin.Context) {
		c.Header("Access-Control-Allow-Origin", "*")
		c.Header("Access-Control-Allow-Methods", "GET, POST, PUT, PATCH, DELETE, OPTIONS")
		c.Header("Access-Control-Allow-Headers", "GX-API-KEY, X-GX-Secret, Content-Type, Authorization")
		if c.Request.Method == "OPTIONS" {
			c.AbortWithStatus(204)
			return
		}
		c.Next()
	}
}

// APIKey validates GX-API-KEY header or query param for external callers.
func APIKey(validKey string) gin.HandlerFunc {
	return func(c *gin.Context) {
		if validKey == "" {
			c.Next()
			return
		}
		key := c.GetHeader("GX-API-KEY")
		if key == "" {
			key = c.Query("api_key")
		}
		if key != validKey {
			response.Error(c, http.StatusUnauthorized, "Unauthorized: invalid API key")
			c.Abort()
			return
		}
		c.Next()
	}
}
