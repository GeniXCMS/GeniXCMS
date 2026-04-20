# GeniXCMS Go API Service

High-performance read API service for GeniXCMS. Supports **MySQL, PostgreSQL, and SQLite** using the same database as your PHP installation.

## Architecture

```
Client Request
      │
      ▼
GeniXCMS PHP (Api::dispatch)
      │
      ├─ api_backend = "php"  →  {Resource}Api class (existing PHP)
      │
      └─ api_backend = "go"   →  proxyToGo() → Go Service → Database
                                                              (shared)
```

## Quick Start

```bash
# 1. Install Go (≥1.22)
# https://go.dev/dl/

# 2. Configure environment
cp .env.example .env
# Edit .env with your DB credentials

# 3. Install dependencies
go mod tidy

# 4. Run service
go run cmd/main.go

# 5. Enable in GeniXCMS admin
# Settings → API → Backend: "go"
```

## Go Service Configuration (.env)

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_DRIVER` | `mysql` | Database driver: `mysql`, `postgres`, or `sqlite3` |
| `DB_HOST` | `127.0.0.1` | Database host (for MySQL/Postgres) |
| `DB_PORT` | `3306` | Database port (MySQL: 3306, Postgres: 5432) |
| `DB_NAME` | `genixcms` | Database name |
| `DB_USER` | `root` | Database username |
| `DB_PASS` | | Database password |
| `DB_FILE` | | Path to `.db` file (Required if `DB_DRIVER=sqlite3`) |
| `PORT` | `8080` | Port where Go service will listen |
| `GIN_MODE` | `release` | `debug` or `release` |
| `GX_SECRET` | | Shared secret (MUST match `go_service_secret` in GeniXCMS) |
| `GX_API_KEY` | | Your GeniXCMS API Key (optional for direct access) |
| `SITE_URL` | `http://localhost` | Base URL of your site |
| `MEDIA_URL` | `.../assets/media` | Base URL for media assets |

## GeniXCMS Administrative Options

| Option | Value | Description |
|--------|-------|-------------|
| `api_backend` | `php` / `go` | Which backend handles API requests |
| `go_service_url` | `http://localhost:8080` | Go service base URL |
| `go_service_secret` | `random-string` | Shared secret (must match `GX_SECRET` in Go .env) |
| `go_service_fallback` | `on` / `off` | Auto-fallback to PHP if Go is down |

## Endpoints

Same contract as GeniXCMS PHP API — response format identical:

```json
{ "status": "success", "message": "...", "data": {...} }
{ "status": "error",   "code": 404,      "message": "..." }
```

| Method | Path | Description |
|--------|------|-------------|
| GET | `/health` | Health check (DB ping) |
| GET | `/api/posts` | List posts (`?type=&limit=&offset=`) |
| GET | `/api/posts/:id` | Get post by ID or slug |
| GET | `/api/categories` | List categories (`?type=`) |
| GET | `/api/categories/:id` | Get category by slug |
| POST/PUT/DELETE | `/api/*` | Returns 501 (Not Implemented) — use PHP for writes |

## Adding New Resources

1. Create model in `internal/models/`
2. Create handler in `internal/handlers/`
3. Register route in `cmd/main.go`

```go
// cmd/main.go
api.GET("/pages", pageHandler.Index)
```

## Production Deployment via Systemd

```ini
[Unit]
Description=GeniXCMS Go API Service
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html/go-service
ExecStart=/var/www/html/go-service/genix-api
Restart=always
EnvironmentFile=/var/www/html/go-service/.env

[Install]
WantedBy=multi-user.target
```
