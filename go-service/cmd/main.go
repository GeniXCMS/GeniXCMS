package main

import (
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"

	"github.com/genixcms/go-service/internal/config"
	"github.com/genixcms/go-service/internal/database"
	"github.com/genixcms/go-service/internal/handlers"
	"github.com/genixcms/go-service/internal/middleware"
	"github.com/genixcms/go-service/internal/models"
)

func main() {
	// Load .env (silent fail in production where env vars are injected directly)
	if err := godotenv.Load(); err != nil {
		log.Println("[go-service] No .env file found, using system environment variables.")
	}

	cfg := config.Load()
	gin.SetMode(cfg.GinMode)

	// Database — shared with GeniXCMS, zero schema changes
	db := database.Connect(cfg.DB.Driver, cfg.DB.DSN())

	// Repositories
	postRepo := &models.PostRepository{DB: db, SiteURL: cfg.SiteURL}
	catRepo  := &models.CategoryRepository{DB: db}

	// Handlers
	postHandler := &handlers.PostHandler{Repo: postRepo}
	catHandler  := &handlers.CategoryHandler{Repo: catRepo}
	tagHandler  := &handlers.TagHandler{DB: db}
	statsHandler := &handlers.StatsHandler{DB: db}
	versionHandler := &handlers.VersionHandler{VersionFile: "../VERSION"}
	searchHandler := &handlers.SearchHandler{DB: db}
	dynHandler  := &handlers.DynamicHandler{DB: db}
	mediaHandler := &handlers.MediaManagerHandler{
		MediaDir:   cfg.MediaDir,
		MediaURL:   cfg.MediaURL,
		Backend:    cfg.MediaBackend,
		FTPHost:    cfg.FTPHost,
		FTPPort:    cfg.FTPPort,
		FTPUser:    cfg.FTPUser,
		FTPPass:    cfg.FTPPass,
		FTPPath:    cfg.FTPPath,
		FTPURL:     cfg.FTPURL,
		S3Key:      cfg.S3Key,
		S3Secret:   cfg.S3Secret,
		S3Bucket:   cfg.S3Bucket,
		S3Region:   cfg.S3Region,
		S3Endpoint: cfg.S3Endpoint,
		S3Path:     cfg.S3Path,
	}
	userHandler := &handlers.UserHandler{DB: db, Driver: cfg.DB.Driver}
	updateHandler := &handlers.UpdateHandler{SiteRoot: cfg.SiteRoot}
	nixHandler  := &handlers.NixomersHandler{DB: db}

	// Router
	r := gin.New()
	r.Use(gin.Logger(), gin.Recovery())
	r.Use(middleware.CORS())

	// Health check — for monitoring and GeniXCMS to detect if Go is alive
	r.GET("/health", func(c *gin.Context) {
		if err := db.Ping(); err != nil {
			c.JSON(http.StatusServiceUnavailable, gin.H{"status": "error", "message": "DB unreachable"})
			return
		}
		c.JSON(200, gin.H{"status": "ok", "service": "genixcms-go-api"})
	})

	// API routes — same path structure as GeniXCMS PHP API
	// Protected by internal secret (X-GX-Secret header from PHP proxy)
	api := r.Group("/api", middleware.InternalAuth(cfg.GXSecret))
	{
		// Posts: GET /api/posts  |  GET /api/posts/:id
		api.GET("/posts",     postHandler.Index)
		api.GET("/posts/:id", postHandler.Show)
		api.POST("/posts",    postHandler.Submit)
		api.PUT("/posts/:id", postHandler.Update)
		api.DELETE("/posts/:id", postHandler.Delete)

		// Categories: GET /api/categories  |  GET /api/categories/:id
		api.GET("/categories",     catHandler.Index)
		api.GET("/categories/:id", catHandler.Show)

		// Tags: GET /api/tags
		api.GET("/tags", tagHandler.Index)

		// Stats: GET /api/stats
		api.GET("/stats", statsHandler.Index)

		// Version: GET /api/version
		api.GET("/version", versionHandler.Index)

		// Search: GET /api/search?q=keyword
		api.GET("/search", searchHandler.Index)

		// ── Dynamic Resource Engine (Fallback) ───────────────────────────
		api.GET("/:resource",     dynHandler.Index)
		api.GET("/:resource/:id", dynHandler.Show)
		// ─────────────────────────────────────────────────────────────────
	}

	// AJAX routes — for proxying heavy read AJAX requests
	// Map same logic as API for consistency, can be extended for specific AJAX handlers
	ajax := r.Group("/ajax", middleware.InternalAuth(cfg.GXSecret))
	{
		// Map specific core handlers to AJAX too for optimized performance
		ajax.GET("/posts",         postHandler.Index)
		ajax.GET("/posts/:id",     postHandler.Show)
		ajax.GET("/categories",    catHandler.Index)
		ajax.GET("/categories/:id", catHandler.Show)
		ajax.GET("/tags",          tagHandler.Index)
		ajax.GET("/stats",         statsHandler.Index)
		ajax.GET("/version",       versionHandler.Index)
		ajax.GET("/search",        searchHandler.Index)

		// Nixomers: GET /ajax/nixomers/list_orders, /ajax/nixomers/list_transactions
		ajax.GET("/nixomers/list_orders", nixHandler.ListOrders)
		ajax.GET("/nixomers/list_transactions", nixHandler.ListTransactions)
		ajax.GET("/nixomers/notifications", nixHandler.Notifications)

		// User AJAX: GET /ajax/user/list_users
		ajax.GET("/user/list_users", userHandler.ListUsers)

		// Media Manager: GET /ajax/media-manager/get_media_page
		ajax.GET("/media-manager/get_media_page", mediaHandler.GetMediaPage)

		// Updates: GET /ajax/updates
		ajax.GET("/updates", updateHandler.Index)

		// ── Dynamic AJAX Engine (Fallback) ───────────────────────────────
		ajax.GET("/:resource",     dynHandler.Index)
		ajax.GET("/:resource/:id", dynHandler.Show)
		// ─────────────────────────────────────────────────────────────────
	}

	log.Printf("[go-service] Starting on :%s (mode: %s)\n", cfg.Port, cfg.GinMode)
	if err := r.Run(":" + cfg.Port); err != nil {
		log.Fatalf("[go-service] Failed to start: %v", err)
	}
}
