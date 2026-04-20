package database

import (
	"log"
	"time"

	_ "github.com/go-sql-driver/mysql"
	_ "github.com/lib/pq"
	_ "modernc.org/sqlite"
	"github.com/jmoiron/sqlx"
)

func Connect(driver, dsn string) *sqlx.DB {
	// Compatibility: Map sqlite3 (CGO) to sqlite (Pure Go)
	if driver == "sqlite3" {
		driver = "sqlite"
	}
	db, err := sqlx.Connect(driver, dsn)
	if err != nil {
		log.Fatalf("[go-service] Failed to connect to %s: %v", driver, err)
	}

	db.SetMaxOpenConns(25)
	db.SetMaxIdleConns(10)
	db.SetConnMaxLifetime(5 * time.Minute)

	log.Printf("[go-service] Database (%s) connected.\n", driver)
	return db
}
