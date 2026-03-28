# GeniXCMS v2.0.0-alpha
> **Latest Update**: Unified Database Support (MySQL, PostgreSQL, SQLite) & Modern Premium Themes.

[![Latest Stable Version](https://poser.pugx.org/genix/cms/v/stable)](https://packagist.org/packages/genix/cms) [![License](https://poser.pugx.org/genix/cms/license)](https://packagist.org/packages/genix/cms) [![Documentation Status](https://readthedocs.org/projects/genixcms/badge/?version=latest)](http://genixcms.readthedocs.org/en/latest/?badge=latest)

**GeniXCMS** is a powerful, lightweight PHP-based Content Management System and Framework (**CMSF**). Re-engineered for the modern web with a focus on speed, security, and premium editorial aesthetics.

---

### 🌟 Key Features in v2.x

- **Multi-DB Support**: Flexible database abstraction layer supporting **MySQL**, **PostgreSQL**, and **SQLite3** (Perfect for zero-config deployments).
- **Latte Template Engine**: Fully integrated [Latte](https://latte.nette.org/) for secure, clean, and blazingly fast templating.
- **Premium Editorial Themes**:
  - **GneeX**: Industrial-modern design with Glassmorphism, premium magazine layouts, and responsive typography.
  - **TheMusk**: Minimalist, high-contrast bento-grid layout using **Tailwind CSS**.
- **Enhanced Security**:
  - Out of the box **Prepared Statements** for SQL Injection prevention.
  - Hardened `.htaccess` and `nginx.conf` configurations included.
  - **HTMLPurifier** integration for robust XSS protection.
- **Modern UI Components**: Refined comments section with nesting, sleek sidebar widgets, and full-width magazine panels.
- **Developer-Friendly**: Modular architecture with **Hooks/Filters** system and **OptionsBuilder** for programmable dashboards.

---

### 🛠 Tech Stack

**GeniXCMS** leverages industry-standard FOSS tools:
- **Framework Core**: PHP 8.1+
- **Templating**: Latte 3.1+
- **Styling**: Bootstrap 5.3, Tailwind CSS (for modern themes), Vanilla CSS (Design Tokens).
- **Image Processing**: Intervention Image v3.
- **Components**: Summernote, elFinder, AOS (Animate on Scroll), Swiper.js.

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
- **Documentation**: [docs.genixcms.web.id](https://docs.genixcms.web.id/)
- **GitHub**: [GeniXCMS Organization](https://github.com/GeniXCMS)

---
*Developed with ❤️ by the GeniXCMS Community.*
