package config

import (
	"fmt"
	"os"
	"strings"
)

type Config struct {
	Port         string
	GinMode      string
	GXSecret     string
	GXAPIKey     string
	SiteURL      string
	SiteRoot     string
	MediaURL     string
	MediaDir     string
	MediaBackend string
	FTPHost      string
	FTPPort      string
	FTPUser      string
	FTPPass      string
	FTPPath      string
	FTPURL       string
	S3Key        string
	S3Secret     string
	S3Bucket     string
	S3Region     string
	S3Endpoint   string
	S3Path       string
	DB           DBConfig
}

type DBConfig struct {
	Driver string // mysql, postgres, sqlite3
	Host   string
	Port   string
	Name   string
	User   string
	Pass   string
	File   string // Untuk SQLite
}

func (d DBConfig) DSN() string {
	switch d.Driver {
	case "postgres":
		return fmt.Sprintf("host=%s port=%s user=%s password=%s dbname=%s sslmode=disable",
			d.Host, d.Port, d.User, d.Pass, d.Name,
		)
	case "sqlite3":
		return d.File
	default: // mysql
		return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?charset=utf8mb4&parseTime=True&loc=Local",
			d.User, d.Pass, d.Host, d.Port, d.Name,
		)
	}
}

func Load() *Config {
	siteURL := getEnv("SITE_URL", "http://localhost")
	mediaURL := getEnv("MEDIA_URL", "")
	if mediaURL == "" {
		mediaURL = strings.TrimRight(siteURL, "/") + "/assets/media"
	}

	return &Config{
		Port:         getEnv("PORT", "8080"),
		GinMode:      getEnv("GIN_MODE", "release"),
		GXSecret:     getEnv("GX_SECRET", ""),
		GXAPIKey:     getEnv("GX_API_KEY", ""),
		SiteURL:      siteURL,
		SiteRoot:     getEnv("SITE_ROOT", ""),
		MediaURL:     mediaURL,
		MediaDir:     getEnv("MEDIA_DIR", "../assets/media"),
		MediaBackend: getEnv("MEDIA_STORAGE_BACKEND", "local"),
		FTPHost:      getEnv("MEDIA_FTP_HOST", ""),
		FTPPort:      getEnv("MEDIA_FTP_PORT", "21"),
		FTPUser:      getEnv("MEDIA_FTP_USER", ""),
		FTPPass:      getEnv("MEDIA_FTP_PASS", ""),
		FTPPath:      getEnv("MEDIA_FTP_PATH", "/"),
		FTPURL:       getEnv("MEDIA_FTP_URL", ""),
		S3Key:        getEnv("MEDIA_S3_KEY", ""),
		S3Secret:     getEnv("MEDIA_S3_SECRET", ""),
		S3Bucket:     getEnv("MEDIA_S3_BUCKET", ""),
		S3Region:     getEnv("MEDIA_S3_REGION", "us-east-1"),
		S3Endpoint:   getEnv("MEDIA_S3_ENDPOINT", ""),
		S3Path:       getEnv("MEDIA_S3_PATH", ""),
		DB: DBConfig{
			Driver: getEnv("DB_DRIVER", "mysql"),
			Host:   getEnv("DB_HOST", "127.0.0.1"),
			Port:   getEnv("DB_PORT", "3306"),
			Name:   getEnv("DB_NAME", "genixcms"),
			User:   getEnv("DB_USER", "root"),
			Pass:   getEnv("DB_PASS", ""),
			File:   getEnv("DB_FILE", "genixcms.db"),
		},
	}
}

func getEnv(key, fallback string) string {
	if v, ok := os.LookupEnv(key); ok && v != "" {
		return v
	}
	return fallback
}
