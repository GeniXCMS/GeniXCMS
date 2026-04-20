# GeniXCMS v2.3.0
> **Latest Update**: Nixomers Financial Intelligence & Premium Confirmation UI v2.3.0

[![Latest Stable Version](https://poser.pugx.org/genix/cms/v/stable)](https://packagist.org/packages/genix/cms) [![License](https://poser.pugx.org/genix/cms/license)](https://packagist.org/packages/genix/cms) [![Documentation Status](https://readthedocs.org/projects/genixcms/badge/?version=latest)](http://genixcms.readthedocs.org/en/latest/?badge=latest)

**GeniXCMS** is a powerful, lightweight PHP-based Content Management System and Framework (**CMSF**). Re-engineered for the modern web with a focus on speed, security, and premium editorial aesthetics.

---

### 🌟 Key Features in v2.x

- **Multi-DB Support**: Flexible database abstraction layer supporting **MySQL**, **PostgreSQL**, and **SQLite3**.
- **Nixomers Ecosystem (v2.3.0+)**:
  - **Financial Intelligence**: Deep recalculation engine that synchronizes order items, taxes, and shipping with transaction ledgers for accurate Net Income reporting.
  - **Granular Tracking (Optional)**: Programmatic unit-level tracking (Serial Numbers, QC) that can be toggled per-order to optimize performance.
  - **Ultra-Premium Checkouts**: Premium UI components with Glassmorphism, Dual-Card layouts, and interactive step-based workflows.
- **Dynamic Builder (v2.1.0+)**: A professional-grade visual editor with drag-and-drop mechanics, premium block library, and real-time layout synchronization.
- **GxEditor v1.2.1 — Modular Engine**:
  - Next-gen block-based editor with custom visual blocks and **Nested Grid Containers**.
  - **Classic Mode** with a full rich-text toolbar (Bold, Italic, Tables, Math/LaTeX, Source View).
  - **Image Selection & Resize** — Click any image to get an inline toolbar for alignment, replacement, and drag-to-resize.
  - **Smart Paste Handler** — Cleans inline styles from Word/Google Docs pastes to maintain editor integrity.
  - **Source Code View** with persistent dark mode theme.
  - Fully integrated with **elFinder** media manager for seamless image insertion.
- **Centralized Asset Management**: Robust `Asset` class for programmatic enqueuing with **Automatic Dependency Resolution**.
- **Reactive Archive Synchronization**: Archive sidebar counts update instantly on post create/update/delete via hook-based cache invalidation.
- **Latte Template Engine**: Fully integrated [Latte](https://latte.nette.org/) for secure, clean, and blazingly fast templating.
- **Premium Editorial Themes**:
  - **GneeX** (v2.1.1): Industrial-modern design with Glassmorphism, premium magazine layouts, and responsive typography.
  - **TheMusk** (v1.0.2): Minimalist, high-contrast bento-grid layout using **Tailwind CSS**.
  - **BSMag** (v2.1.1): Magazine-style theme with dark mode support and NixSlider integration.
  - **Default** (v2.1.0): Clean, extensible base theme.
- **Enhanced Security**:
  - **Token-Based CSRF Protection**: All forms and AJAX requests are protected with time-based CSRF tokens.
  - Out of the box **Prepared Statements** for SQL Injection prevention.
  - Hardened `.htaccess` and `nginx.conf` configurations included.
  - **HTMLPurifier** integration for robust XSS protection.
- **Developer-Friendly**: Modular architecture with **Hooks/Filters** system, **UiBuilder** & **OptionsBuilder** for programmable dashboards, and a powerful **CLI tool**.

---

### 🛠 Tech Stack

**GeniXCMS** leverages industry-standard FOSS tools:
- **Framework Core**: PHP 8.2+
- **Templating**: Latte 3.1+
- **Editor Core**: GxEditor v1.2.1 (Modular Block Engine)
- **Asset Management**: GeniX `Asset` Class (with dependency resolution)
- **Styling**: Bootstrap 5.3, Tailwind CSS (for modern themes), Vanilla CSS (Design Tokens).
- **Image Processing**: Intervention Image v3.
- **Components**: elFinder, AOS (Animate on Scroll), Swiper.js, FlexSlider, KaTeX (Math).

### ⚡ High-Performance Go API Service (v2.3.0+)

**GeniXCMS** includes an optional high-performance Go microservice that significantly boosts read operations for API and AJAX requests. The Go service shares the same database as your PHP installation and supports **MySQL, PostgreSQL, and SQLite3**.

#### Key Benefits:
- **Lightning-Fast Search**: Dedicated search engine for rapid text-matching across posts.
- **Universal Dynamic Resources**: Smart handler serving data from any database table automatically.
- **Hybrid Backend**: Supports both RESTful API and core AJAX read operations.
- **Smart Switching**: Toggle Go service for Posts, Categories, and dashboard listings.
- **Automatic Fallback**: Seamlessly reverts to PHP if the Go service is unreachable.

#### Quick Setup:
1. Install Go (≥1.22): [go.dev/dl](https://go.dev/dl/)
2. Configure environment: Copy `go-service/.env.example` to `go-service/.env` and edit with your DB credentials.
3. Install dependencies: `cd go-service && go mod tidy`
4. Run service: `go run cmd/main.go`
5. Enable in GeniXCMS admin: Settings → API → Backend: "go"

### ☁️ Cloud Storage Support (S3-Compatible)

**GeniXCMS** supports cloud storage integration with S3-compatible services (AWS S3, DigitalOcean Spaces, etc.) through a lightweight Async AWS S3 library. This feature is completely optional and only loads when S3 credentials are configured.

#### Benefits:
- **Reduced Bundle Size**: Uses `async-aws/s3` (~2MB) instead of full AWS SDK (~50MB+).
- **Better Performance**: Async operations with minimal memory footprint.
- **S3-Compatible**: Works with AWS S3, DigitalOcean Spaces, and other S3-compatible services.

For detailed configuration, see Media Manager settings in the admin panel.

### 📋 Requirements

* **Webserver**: Apache (with mod_rewrite) or Nginx.
* **PHP**: 8.1 or newer.
    - Extensions: GD, cURL, OpenSSL, Imagick, Intl, mbstring, XML, PDO (with mysql/sqlite/pgsql drivers).
* **Database**: MySQL 5.7+, MariaDB 10.3+, PostgreSQL 12+, or SQLite 3.

### 🚀 Installation & Setup

#### 1. Quick Start (Manual)
1. Clone or upload the repository to your root directory.
2. Ensure the following directories are writable (**775** or **777**):
   - `inc/config`
   - `inc/themes`
   - `inc/mods`
   - `assets/images/uploads`
   - `assets/cache`
   - `assets/media/images`
   - `assets/media/videos`
   - `assets/media/audios`
   
3. Visit your domain in the browser (e.g., `http://yoursite.com`).
4. Follow the **Installation Wizard** to set up your site and database.

#### 2. Advanced Security (Optional)
- **Apache**: The provided `.htaccess` is pre-configured with security headers and folder protection.
- **Nginx**: Refer to `nginx.conf.txt` for the recommended secure server-block configuration.

---

### ⬆️ Upgrading to v2.x

Upgrading from v1.x involves significant core changes.
1. Backup your `inc/config/config.php` and database.
2. In your `config.php`, ensure `SITE_ID` is defined.
3. Replace all files with the v2.x version.
4. Run `upgrade.php` from your browser.
5. Review theme migrations if using custom templates.

---

### 📄 License

**GeniXCMS** is released under the [**MIT License**](LICENSE).

### 🌐 Links & Community

- **Official Website**: [genixcms.web.id](https://genixcms.web.id/)
- **Documentation**: [genixcms.web.id/mod/docs.html](https://genixcms.web.id/mod/docs.html)
- **Demo**: [demo.genixcms.web.id](https://demo.genixcms.web.id/)
- **GitHub**: [GeniXCMS Organization](https://github.com/GeniXCMS)

---
*Developed with ❤️ by the GeniXCMS Community.*
