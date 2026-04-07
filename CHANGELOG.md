# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2026-04-07

### Added
- **9 Customizable Admin Sections**: Hero, Expertise (4 cards), Advantages (3 items), Validation (3 testimonials), Posts, Colors, Typography, Social, and Advanced settings.
- **CSS Variables System**: Dynamic color injection via CSS custom properties for seamless theme customization.
- **Extended Menu System**: New `itemClass` parameter in `Menus::getMenu()` for custom menu item styling per-theme.
- **Bootstrap CSS Dequeue**: Frontend now loads TailwindCSS exclusively, preventing Bootstrap style conflicts.
- **Dynamic Builder Pro Upgrade**: Overhauled the visual editor with a premium dark theme, integrated elFinder asset manager, and a new high-quality block library (Hero, Pricing, FAQ, Features).
- **Responsive Builder Workspace**: Added device simulation toggles (Desktop, Tablet, Mobile) to the builder header for effortless responsive design.
- **Team Section Widget**: A premium, responsive 4-column grid with custom 3:4 aspect ratio containers and grayscale-to-color hover transitions.
- **Dynamic Builder Documentation**: Comprehensive User and Developer guides integrated into the official GeniXCMS documentation site.
- **Heading Component Integration**: Upgraded static headings in standard sections to use the dynamic `heading-component`, allowing on-the-fly H1-H6 level adjustment.

### Changed - Core Asset & Menu Systems
- **Asset::dequeue() Method**: Added new method to the Asset class for removing assets from the current page queue dynamically.
- **Menu Generation Logic**: Updated `Menus::getMenu()` to conditionally apply Bootstrap classes only when `$bsnav = true`, supporting theme-specific styling.
- **Bootstrap Frontend Loading**: Bootstrap CSS is now conditional and not loaded on the frontend by default (configurable per theme).

### Fixed - Dynamic Builder UI/UX
- **Block Category Toggle**: Resolved a major UI bug where block categories would not collapse; fixed by removing an `!important` CSS override in `builder.css`.
- **Image & Text Editability**: Standardized Team and Hero images as `type: 'image'` and explicitly defined sub-elements in "Hero Cyber Frontier" as `type: 'text'` for reliable editor interaction.
- **Canvas Aspect Ratios**: Implemented a dedicated `canvas.css` layer for the editor iframe to ensure custom ratios (like 3:4) and interactive hover effects are rendered accurately.
- **Image Widget Defaults**: Standardized the core Image block to be full-width, float-none, and border-radius: 0 for a cleaner "out-of-the-box" design experience.

### Fixed - Theme Styling & UI
- **Validation Card Variables**: Fixed undefined Latte variables for validation card styling (changed from snake_case to camelCase for consistency).
- **Posts Card Border Styling**: Border styling now applied via CSS instead of relying on Tailwind arbitrary opacity syntax.
- **Card Border Radius Consistency**: Posts cards now use `rounded-3xl` matching validation and expertise cards for unified design.


## [2.0.1] - 2026-04-05

### Fixed
- **elFinder Initialization Conflict**: Resolved a critical naming conflict in the `Asset` manager where the Summernote editor's internal script ID was overwriting global file manager utilities, causing "ReferenceError: elfinderDialog2 is not defined" when selecting featured images.
- **Featured Image (Hero Asset) URL**: Fixed a hardcoded, insecure URL for the featured image file manager in `pages_form.php`. It now correctly uses the system-standard `Url::ajax('elfinder')` with session-based tokens.
- **Dependency Loading Order**: Optimized the loading sequence for elFinder components, ensuring `jquery-ui` and the core file manager JavaScript are fully available before initializing custom UI buttons.
- **Global Function Scoping**: Migrated editor and file manager helper functions (`elfinderDialog`, `elfinderDialog2`, `gxcodeBtn`) to the global `window` scope for reliable accessibility across all admin forms.
- **Admin UI Polish**: Corrected inconsistent spacing and border rendering in the page and post creation forms for a more unified "GeniXCMS 2.0" aesthetic.

### Changed
- **Version Bump**: Officially updated core version to **2.0.1** to reflect these critical stability fixes in the 2.0 series.
- **Asset Registration**: Moved elFinder helper utilities to the page header to ensure core JS functions are defined before any user interaction occurs.

## [2.0.0] - 2026-04-05

### Added
- **GxEditor Modular Block Architecture**: Implemented a global `GxEditor.registerBlock` API, enabling developers to dynamically register custom visual blocks with unique rendering and serialization logic.
- **Nested Container Support (Elementor-Style)**: Introduced a fully recursive editor architecture allowing infinite nesting of blocks within grid cells, card bodies, and other layout containers.
- **GxEditor "Classic Mode"**: A new text-centric interface for the core editor, featuring a refined formatting toolbar and distraction-free writing experience.
- **Smart Admin Filtering & Dashboards**: Implemented robust, context-aware filtering for Posts, Pages, and Users management interfaces, accompanied by compact statistical summary cards.
- **Theme-Specific Error Templates**: The core rendering engine now supports and prioritizes theme-specific `404.latte` or `404.php` templates for a unified branding experience.
- **Centralized Asset Management System**: Finalized the core `Asset` class architecture, providing a robust register/enqueue system with automatic dependency resolution (e.g., ensuring `bootstrap-js` always loads after `jquery`).
- **Programmatic Asset Enqueuing**: Modernized theme development by integrating `Asset::register()` and `Asset::enqueue()`, replacing manual HTML script/link tags for cleaner, more maintainable templates.
- **Secure Marketplace Download API**: New API endpoints for digital products with license validation and domain locking support.
- **Monthly Archive Templates**: Created dedicated, premium-styled `archive` templates for both the **GneeX** and **Default** themes, ensuring seamless monthly content navigation.
- **Archive List Widget**: Introduced the "Archive List" as a selectable core widget, allowing administrators to dynamically place monthly navigation in sidebars and footers.
- **Clean Blog 404 Page**: Implemented a custom-designed, photographic 404 template for the **Clean Blog** theme with integrated search functionality.
- **The Musk Navigation Support**: Fully implemented dynamic header and footer menu support for **The Musk** (Tailwind CSS) theme with corrected menu ID mappings.
- **Modular Admin Menu System**: Introduced the `AdminMenu` class, allowing modules to dynamically register their own navigation entries and sub-menus in the dashboard without core modifications.
- **Enhanced Module Handler**: Overhauled the core `Mod` class for more robust loading, activation, and management of third-party extensions with improved asset support.
- **Versatile Shortcode Engine**: Expanded the core `Shortcode` class with advanced content parsing and stripping capabilities, ensuring consistent behavior across theme previews and SEO meta tags.
- **Programmable Cron Registry**: Introduced a virtual cron system (`Cron.class.php`) enabling themes and modules to schedule recurring tasks (hourly, daily, weekly) and one-time events without requiring server-level crontab access.
- **One-Token-Per-Session Model**: Re-engineered the `Token` class for high reliability, eliminating race conditions in complex AJAX environments by adopting a persistent session-based token strategy.
- **Self-Healing CSRF Protection**: Implemented a robust token validation system that automatically promotes valid legacy tokens to primary state, ensuring secure and stable operations for long-running sessions.
- **Security Auditing Engine**: Introduced the `Security` class to providing automated scanning of ZIP uploads (themes/modules). It detects dangerous PHP execution patterns and suspicious Javascript obfuscation, ensuring safer installation of third-party extensions.

### Changed
- **Stable Version Milestone**: Officially bumped core from `2.0.0-alpha` to **2.0.0 stable**.
- **GxEditor Grid Persistence**: Overhauled the grid system to handle highly complex, deeply nested JSON structures with improved data integrity.
- **Token Security (elFinder)**: Implemented stable, session-aware token validation for elFinder to prevent "Token NOT FOUND" errors in long-running AJAX sessions.
- **Routing Standardization**: Migrated all core modules to use standardized `Url::mod()` and `Url::api()` for consistent, SEO-friendly link generation.
- **Options Retrieval**: Refactored `Options::v()` to return an empty string (`''`) instead of `null` for missing options, preventing downstream type-safety failures in modern PHP environments.
- **Clean Blog Layouts**: Optimized the footer menu in the **Clean Blog** theme with centered alignment and refined typography.
- **Gitignore Policies**: Added `inc/migrations/` to the global ignore list for better repository hygiene.

### Fixed
- **GxEditor Interaction Layouts**: Resolved UI positioning bugs for context menus and the block-picker search interface.
- **Icon Persistence**: Fixed logic for correctly parsing and rendering Icon blocks after editor reloads.
- **Thumbnail Fallback Logic**: Standardized "no image" placeholder paths to ensure themes always serve regional assets for missing post media.
- **PHP 8.1+ Compatibility**: Hardened core and themes against `TypeError` by ensuring `json_decode()` always receives string inputs, even when requested database options are missing.
- **Archive Routing (Month Padding)**: Fixed a critical routing bug for non-smart URLs where single-digit months caused 404 errors. Months are now consistently zero-padded (e.g., `04`) to match the database storage format.
- **Dynamic Category Titles**: Resolved an issue in the **Default** and **Clean Blog** themes where the literal word "Category" was displayed instead of the actual dynamic category name.
- **404 View Stability**: Fixed a layout bug in the **Default** theme where hardcoded logo dimensions in the error controller caused an oversized header (200px) on 404 pages.

### Removed
- **Floating Frontend Customizer**: Decommissioned the temporary GneeX customizer UI in favor of the formal, centralized OptionsBuilder in the admin dashboard.


## [2.0.0-alpha] - 2026-03-28

### Added
- **Unified Database Abstraction Layer (DAL)**: Re-engineered the core `Db` class for high-performance PDO operations, supporting multiple database backends including **MySQL/MariaDB**, **PostgreSQL**, and **SQLite3**.
- **Modern Admin Dashboard Layout**: A complete redesign of the administration interface (`gxadmin`) featuring a minimalist, industrial ERP-style layout, high information density, and refined typography.
- **Content Security Policy (CSP) Support**: Integrated robust security headers into core templates and `.htaccess` to mitigate XSS and data injection attacks.
- **New Tailwind-Based Theme: TheMusk**: Introduced a modern, high-contrast, minimalist editorial theme built from the ground up with **Tailwind CSS**.
- **GeniX CLI (Terminal Control)**: Introduced a powerful Command Line Interface (`genix`) located in the root directory for automated site management and database maintenance.
- **OptionsBuilder & UIBuilder Engine**: A new, reactive UI framework for building premium administration panels with integrated CSS generation and design tokens.
- **Latte Template Engine Integration**: Switched the core templating architecture to **Latte**, providing secure, performance-oriented rendering for all themes and modules.
- **Security Hardening**: Enforced PDO Prepared Statements globally and integrated **HTMLPurifier** for comprehensive XSS protection.

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
