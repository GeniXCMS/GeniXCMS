# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0-alpha] - 2026-03-28

### Added
- **GeniX CLI (Terminal Control)**: Introduced a powerful Command Line Interface (`genix`) located in the root directory. Allows developers to manage site configurations, clear caches, and perform database tasks directly from the terminal.
- **OptionsBuilder & UIBuilder Engine**: A new, self-contained UI framework for building premium administration panels. Features include:
    - Tabbed sidebar navigation for theme/module settings.
    - Integrated CSS Generation Engine for real-time frontend typography and color mapping.
    - Reactive design tokens supporting glassmorphism and modern UI depth.
- **Unified Database Abstraction Layer (DAL)**: Complete re-engineering of the `Db` class for PDO. Now supports:
    - **MySQL/MariaDB** (optimized for 8.0+ / 10.4+).
    - **PostgreSQL** (native driver support).
    - **SQLite3** (ideal for lightweight, zero-config deployments).
- **Modern Dashboard Experience**: Revamped the entire administration UI (`gxadmin`) with a minimalist, industrial ERP-style layout, improved information density, and refined typography.
- **Latte Template Engine**: Full architectural integration of the [Latte](https://latte.nette.org/) engine, bringing secure, performance-oriented templating to all core themes and modules.
- **Premium Themes Suite**:
    - **GneeX**: Our flagship industrial-modern theme with advanced magazine layouts and glassmorphic UI components.
    - **TheMusk**: A high-contrast, minimalist editorial theme built with **Tailwind CSS**.
- **Module Enhancements**:
    - **New Modules**: Added specialized modules for dynamic content building and enhanced site analytics.
    - **Module Standardization**: Updated all core modules to support the new UIBuilder framework for a consistent admin experience.
- **Security Hardening**:
    - Hardened `.htaccess` and `nginx.conf` with critical security headers (CSP, X-Frame-Options, HSTS).
    - PDO Prepared Statements now enforced for all new theme-level and module-level queries.
    - **HTMLPurifier** integration for comprehensive XSS protection.

### Changed
- **Archive Layout Proportions**: Optimized the category, tag, and author pages for a better reading experience using a balanced **col-md-4/8** ratio.
- **Thumbnail Resolution**: Enlarged archive thumbnails to a minimum height of **200px - 260px** for a more impactful editorial look.
- **Architecture**: Decoupled design-specific styles from the core framework, migrating them to decentralized theme stylesheets for better maintainability.
- **Global Copyright**: Extended copyright protections across the entire codebase for the **2023-2026** period.

### Fixed
- **Database Compatibility**: Resolved a long-standing issue where `RAND()` queries failed in SQLite environments by implementing a driver-aware random ordering logic.
- **Module Stability**: Fixed several "Trying to get property of non-object" and "TypeError" bugs in core modules (`Comments`, `Image`, `Posts`).
- **Thumbnail Generator**: Standardized on high-resolution `large` (800px) outputs to prevent blurry images in enlarged layouts.

---
*GeniXCMS - Re-engineered for the modern web with a focus on speed, security, and premium editorial aesthetics.*
