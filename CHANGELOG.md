# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0-alpha] - 2026-03-28

### Added
- **Latte Template Engine**: Fully integrated the Latte engine for more secure and expressive templating across all themes.
- **Multi-Database Support**: Enhanced the `Db` class to support **MySQL**, **PostgreSQL**, and **SQLite3** seamlessly.
- **Premium Themes**:
    - **GneeX**: A modern, industrial magazine-style theme featuring Glassmorphism, premium layouts, and refined typography.
    - **TheMusk**: A minimalist, high-contrast theme built with **Tailwind CSS**, focusing on a contemporary bento-grid aesthetic.
- **Security Hardening**:
    - Robust `.htaccess` and `nginx.conf` configurations with security headers (CSP, X-Frame-Options, etc.).
    - Strict `GX_LIB` check on all system files to prevent direct execution.
    - PDO Prepared Statements integration for theme-level queries to prevent SQL Injection.
- **Modern UI Components**:
    - Redesigned comments section with support for nested replies and premium meta-styling.
    - Enhanced sidebar widgets with image hover effects and sophisticated tag cloud designs.
    - New "full-width" magazine panels for more impactful homepage stories.

### Changed
- **Archive Layouts**: Refined the category, tag, and author pages with better proportions (col-md-4/8) and enlarged thumbnails (200px - 260px) for a more professional editorial feel.
- **Global Copyright**: Updated all project files to reflect the 2023-2026 copyright period.
- **Code Refactoring**: Cleaned up inline CSS and migrated theme-specific styles to centralized `style.css` files for easier maintenance.

### Fixed
- **SQLite Compatibility**: Resolved a critical SQL error in the `TheMusk` theme where `ORDER BY RAND()` was used instead of the SQLite-compatible `ORDER BY RANDOM()`.
- **Thumbnail Stability**: Ensured consistent high-quality thumbnail generation by standardizing on `large` (800px) resolution for premium layouts.

---
*GeniXCMS - Build faster, stay secure, look premium.*
