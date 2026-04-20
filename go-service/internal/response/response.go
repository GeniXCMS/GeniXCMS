package response

import "github.com/gin-gonic/gin"

// Success sends a standardized success response — identical format to GeniXCMS PHP.
func Success(c *gin.Context, data any, message ...string) {
	msg := "Operation successful"
	if len(message) > 0 {
		msg = message[0]
	}
	c.JSON(200, gin.H{
		"status":  "success",
		"message": msg,
		"data":    data,
	})
}

// Error sends a standardized error response — identical format to GeniXCMS PHP.
func Error(c *gin.Context, code int, message string) {
	c.JSON(code, gin.H{
		"status":  "error",
		"code":    code,
		"message": message,
	})
}

// Paginated wraps paginated data with meta info — identical format to GeniXCMS PHP.
func Paginated(c *gin.Context, data any, total, limit, offset int) {
	c.JSON(200, gin.H{
		"status": "success",
		"data":   data,
		"total":  total,
		"limit":  limit,
		"offset": offset,
	})
}
