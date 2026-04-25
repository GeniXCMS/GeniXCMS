# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.0] - 2026-04-22

### Added
- **Offline-Ready Admin Panel**: All external CDN dependencies for the admin dashboard are now bundled locally under `assets/js/vendor/`, `assets/css/vendor/`, and `assets/fonts/`. The CMS is fully usable without an internet connection — ideal for local development and offline environments.
- **Local Vendor Bundle**: Downloaded and committed the following libraries at pinned versions: jQuery 3.7.1, jQuery UI 1.13.2, Bootstrap 5.3.3 (CSS + JS bundle), Bootstrap Icons 1.11.3 (CSS + fonts), Font Awesome 6.7.2 (CSS + webfonts), jQuery TagsInput 1.3.6, jsVectorMap 1.5.3 (+ world map), Chart.js 4.4.1.
- **`OFFLINE_MODE` Constant**: New constant in `config.php`. Set `true` to load all framework assets from local files; `false` (default) uses CDN. Switchable with one line.
- **`DEVELOPER_MODE` Constant**: New constant in `config.php`. Set `true` to reveal the Dev Tools menu (Asset Inspector + Hook Inspector) in the admin panel. Hidden by default in production.
- **Asset Inspector** (`?page=devtools-assets`): Developer tool that lists every registered and enqueued asset with type, source (local vs CDN badge), position, priority, dependencies, and enqueue status. Supports filtering by type, context, and search.
- **Hook Inspector** (`?page=devtools-hooks`): Developer tool that lists all registered hooks and their attached callbacks. Closures show the source file and line number via `ReflectionFunction`. Supports filtering by name and callback status.
- **`ADMIN_THEME` Constant**: New constant in `config.php` to select the active admin panel theme. Defaults to `'default'`.
- **Ribbon Admin Theme** (`gxadmin/themes/ribbon/`): MS Office / WPS-style ribbon toolbar layout. Each top-level menu item becomes a tab; items without children render as a large icon button, items with children render their children as icon buttons. Client-side tab switching via `history.pushState`, responsive fallback, and per-tab hooks (`admin_ribbon_tab_{id}`).
- **Moluka Admin Theme** (`gxadmin/themes/moluka/`): Clean SaaS-style layout with white sidebar, avatar + online status, treeview nav, gradient footer card, and accent color `#00A3EA`. Dashboard features a large greeting header, teal subtitle, and pill quick-action buttons.
- **Fresco Admin Theme** (`gxadmin/themes/fresco/`): Compact financial-dashboard style layout. White sidebar with section labels, lime-green accent `#c8f04a` for active states, minimal top header with search bar and action buttons. All nav active detection uses the same strict URL param matching as moluka and ribbon.
- **Admin Menu Icons**: All children in `AdminMenu::bootCoreMenu()` now have Bootstrap Icons assigned — Posts, Categories, Tags, Users, ACL, Themes, Menus, Widgets, System Health, Updates, and all Settings sub-pages.
- **Nixomers Menu Icons**: All 15 children in the Nixomers admin menu now have contextual icons (Dashboard, Orders, Analytics, Transactions, Stock, Products, Categories, Brands, Suppliers, etc.).
- **Admin Theme System**: `gxadmin/themes/` restructured — existing theme moved to `gxadmin/themes/default/`. `Theme::admin()`, `Theme::auth()`, and `Theme::install()` now resolve files from the active `ADMIN_THEME` with automatic fallback to `default`.
- **Chart.js Offline Support**: Chart.js 4.4.1 registered in `Asset::registerCore()` respecting `OFFLINE_MODE`. `UiBuilder` lazy-load and `Nixomers` analytics page both use the centralized registration instead of hardcoded CDN URLs.
- **CSP Whitelist for Common Analytics**: Default Content Security Policy now includes trusted domains for Google Analytics / GTM, Google Ads, Meta / Facebook Pixel, and Histats across `script-src`, `connect-src`, and `frame-src` directives — no manual CSP editing needed for the most common tracking integrations.
- **Go Service Detection & Optional Install**: `settings-api.php` now detects whether the `go-service/` folder exists at root. If missing: Active Driver dropdown is disabled and locked, Auto PHP Fallback checkbox is disabled, and a warning banner with a "Download Go Service" button is shown. If present: a green status badge confirms installation. When `api_backend=go` is saved but folder is absent, it auto-reverts to `php`.
- **Go Service One-Click Download**: New `UpdatesAjax::installGoService()` action fetches the Go service package info from `genixcms.web.id/api/v1/download/go-service`, downloads the ZIP to `assets/cache/`, extracts it into `go-service/` at root (stripping top-level folder prefix), then reloads the page. Download is restricted to `genixcms.web.id` domain only.
- **Offline Font Support**: Downloaded Inter font (weights 300–900) as local `.woff2` files to `assets/fonts/inter/`. Created `assets/css/vendor/inter-local.css` with `@font-face` declarations pointing to local files. `Asset::registerCore()` now registers `inter-font` conditionally — local CSS when `OFFLINE_MODE=true`, Google Fonts CDN when `false`. All admin theme headers (moluka, ribbon, fresco, prodify, default) and auth pages (login, register, forgotpass) wrap their Google Fonts `<link>` tags in `<?php if (!OFFLINE_MODE): ?>` guards.
- **Improved Admin Dashboard**: Added Content Health progress bars (Publish Rate, Comment Approval, User Activity), New Members list with Gravatar avatars, System Info panel (PHP version, CMS version, DB driver, memory limit, disk usage bar, uptime), and Top Content ranking with medal colors. All post/user titles decoded via `Typo::Xclean()`. Username in greeting uses accent color via `var(--gx-primary)`.
- **Admin Menu Icons**: All children in `AdminMenu::bootCoreMenu()` and modules (`nix_confirmation`, `nix_fulfillment`, `gxeditor`) now have Bootstrap Icons assigned.

### Fixed (continued)
- **settings-api.php Script Remnant**: Removed orphaned JavaScript fragment (`$'; let result = ''...`) left over from a partial string replacement in the old `generateSecret` function — it was being rendered as visible text on the page.
- **EditorJS Tools Not Loading**: `editorjs-header`, `editorjs-list`, `editorjs-image`, `editorjs-quote`, `editorjs-table` were registered but never enqueued — added all as dependencies of `editorjs-init` so Asset manager loads them automatically.
- **EditorJS Offline Support**: Downloaded all EditorJS tool bundles (core, header, list, image, quote, table, delimiter) to `assets/js/vendor/editorjs/`. `Editor::editorjs()` now registers local paths when `OFFLINE_MODE=true`, CDN when false.
- **EditorJS Image Upload**: Replaced complex elFinder-based uploader with direct POST to `SaveimageAjax`. Fixed field name detection (accepts `image`, `file`, or `upload`). Added proper PHP upload error code mapping with human-readable messages. Fixed auth to not require `Http::validateUrl()`. Added `upload_max_filesize` error reporting so users know when file is too large.

### Changed
- **Asset Registration**: `Asset::registerCore()` now points all framework URLs to local paths or CDN based on `OFFLINE_MODE`. No CDN calls are made for core admin assets when offline mode is active.
- **Font Paths**: Bootstrap Icons CSS patched to use relative paths pointing to `assets/fonts/bootstrap-icons/`. Font Awesome CSS already uses correct relative `../webfonts/` paths.
- **Ribbon Menu Active Detection**: `rbUrlMatches()` now compares all query params defined in a child URL (including `sel`, `act`, `type`, `view`) against the current request — prevents false-positive active states when multiple items share the same `page` or `mod` param.
- **Ribbon Button Labels**: `white-space: normal` + `word-break: break-word` applied to `.rb-btn` so long labels wrap to two lines instead of overflowing.
- **Moluka Nav Active Detection**: Child items use full URL param matching (page + mod + sel + act + type) — same logic as ribbon, prevents all children activating simultaneously.
- **Nav Active Detection — Strict Negative Guards**: `rbUrlMatches()` / `$pdUrlMatches` / `$frUrlMatches` now enforce that if a child URL does not define `type`, `mod`, `act`, or `sel`, the current URL must also not have those params. Eliminates cross-contamination between menu items sharing the same `page` param (e.g. `?page=posts` vs `?page=posts&type=nixomers` vs `?page=posts&act=add&type=nixomers`).
- **Dashboard Quick-Action Buttons**: Replaced theme-specific `mk-quick-btn` classes with inline styles using `var(--gx-primary, #00A3EA)` — buttons now render correctly across all admin themes.
- **Moluka Topbar**: Changed from `padding: 14px 28px 0` / transparent background to `height: 52px` / white background with border — topbar now sits flush at the top like a proper header bar.
- **`ADMIN_DIR` Constant Usage**: Replaced all hardcoded `'gxadmin/'` URL strings in `NixomersAjax.class.php`, `Ajax.class.php`, `UserProfile.class.php`, `media-manager/index.php`, `gneex/function.php`, `madxion/function.php`, and `MdoTheme.class.php` with `ADMIN_DIR` constant. Install `step4.php` intentionally left as-is (config not yet available during install).
- **Dashboard Greeting**: Replaced old welcome header with modern layout — date line, large bold greeting, teal subtitle, pill quick-action buttons, and accent orb.
- **CSP Analytics Domains**: Expanded from CDN-only to include all major analytics and advertising platforms by default.

### Fixed
- **`curl_close()` Deprecation (PHP 8.0+)**: Replaced all 15 occurrences of `curl_close($ch)` across `inc/lib/` and `inc/mod/` with `unset($ch)`. `@curl_close()` in `Http.class.php` also fixed (error suppressor not valid on `unset`).
- **Null Array Key Deprecation**: `$locations[$l->location_id]` in `nixomers/options/stock.php` — `null` location_id now cast to empty string before use as array key.
- **Nixomers Fatal TypeError**: `Url::user(null, 'purchase')` crash when admin panel accessed unauthenticated — `registerDropdownItem` now guarded with `!empty($sessionUsername)`.
- **Moluka Treeview Dropdown**: Fixed CSS adjacent sibling selector not working — replaced with JS-toggled `.open` class on both trigger and `nextElementSibling`.
- **Moluka/Fresco/Ribbon JS Function Names**: Fixed leftover `pdOpenSidebar`/`pdCloseSidebar` function names (from prodify rename) in moluka footer — renamed to `mkOpenSidebar`/`mkCloseSidebar`.
- **Chart.js Lazy-Load URL**: `json_encode()` was wrapping the URL in quotes causing `script.src = '"https://..."'` — replaced with direct PHP string concatenation.
- **BOM (Byte Order Mark) Removal**: Removed UTF-8 BOM from all admin theme files (`moluka`, `fresco`, `ribbon`, `prodify`) and affected lib files (`Ajax.class.php`, `UserProfile.class.php`, `NixomersAjax.class.php`) that were written via PowerShell `WriteAllText` with BOM encoding. BOM caused browser to misparse `<head>` and inject `&#xFEFF;` text nodes into `<body>`.
- **System Core**: Bumped GeniXCMS to version **2.4.0**.

## [2.3.0] - 2026-04-20

### Added
- **High-Performance Go Search**: Introduced a dedicated search engine in Go that performs rapid text-matching across posts, significantly faster than traditional PHP-based LIKE queries.
- **Universal Dynamic Resource Engine (Go)**: The Go microservice now features a smart dynamic handler capable of serving data from ANY database table (including third-party modules) automatically, while maintaining strict security blacklists for core sensitive data.
- **Unified Hybrid Backend (Go-Powered)**: Expanded the high-performance Go-based microservice to support both RESTful API and core AJAX read operations. This significantly reduces latency for frontend data fetching.
- **Smart Switch System (API & AJAX)**: Administrators can now toggle the Go service for the entire ecosystem (GET requests), inclusive of Posts, Categories, and dynamic dashboard listings.
- **Multi-DB Support for Go**: The Go microservice now natively supports MySQL, PostgreSQL, and SQLite3, ensuring full compatibility with the existing GeniXCMS ecosystem.
- **Dedicated API Service Dashboard**: Created a separate administration panel for API configuration, decoupling it from general settings for better clarity.
- **Internal Security Handshake**: Implemented a shared-secret authorization mechanism to secure high-speed proxy requests between PHP and the Go service.
- **Modernized API Key Management**: Added auto-generation features for both Public RESTful Tokens and Shared Authorization Secrets with a refreshed UI.
- **Smart PHP Fallback**: Introduced a reactive fallback system that automatically reverts to the PHP engine if the Go microservice becomes unreachable.
- **Media Manager Isolation (v1.3.0)**: Implemented `GX_USER_FOLDER` support to strictly isolate user assets into their own subdirectories. The file manager now treats the user's folder as a virtual root, preventing unauthorized directory traversal.
- **Extended File Support**: Media Manager now natively supports `zip`, `pdf`, and `txt` file types with context-aware Bootstrap Icons and improved grid previews.
- **Cross-Platform Path Security**: Enhanced `MediaManager` core with a new `normPath()` helper and traversal protection, ensuring consistent and secure file operations across Linux and Windows hosting environments.
- **Centralized Media AJAX Engine**: Fully refactored the media controller into a dedicated `MediaManagerAjax` class, standardizing all file operations (upload, delete, rename, list) via the core AJAX dispatcher.
- **Programmatic User Profile Extensions**: Overhauled the frontend `UserProfile` class with new `registerSection` and `registerDropdownItem` methods. Theme and module developers can now dynamically add new profile pages (e.g., `/user/.../finance`) and append menu links without touching core template files.
- **Dynamic Profile Template Routing**: The CMS now seamlessly detects and loads custom profile layout templates (e.g., `user-finance.latte`) straight from the theme folder, eliminating restrictive layout wrappers for complex module pages.
- **Profile Context Badges**: Upgraded the standard `user-profile.latte` Recent Posts board with custom badge labels so viewers can instantly identify post context (e.g., PAGE vs POST vs PRODUCT).
- **Recent Orders Profile Hook**: Modules like Nixomers can now hook into `user_profile_overview_extra` or `user_profile_sidebar_extra` to natively render their own data metrics directly onto the user's dashboard.
- **Core Canonical URL Enforcement**: Implemented a global SEO guard in `PostControl` that validates requested paths and enforces 301 Moved Permanently redirects (or 404s) to the canonical URL, effectively eliminating duplicate content issues.
- **Custom Product URL Architecture**: Introduced a `/product/` prefix for Nixomers e-commerce items through a modular `post_url` hook and corresponding router definitions.
- **Premium 404 Error Page (Artisan Atelier)**: Created a custom, high-end 404 error template for the Artisan Atelier theme, featuring animated blob masks, radial design tokens, and converted navigation CTAs.
- **GxMedia Selector Integration**: Modernized the GxEditor media integration by replacing legacy dialogs with the native, modular GxMedia selector across Posts and Pages forms.
- **Media Selector AJAX Upload**: Integrated a high-performance AJAX upload feature directly into the Media Selector dialog with real-time grid refresh.
- **Adaptive Sticky Header (UiBuilder)**: Implemented a premium "Elevated Header" system in UiBuilder that transitions from transparent to a compact sticky state upon scrolling, featuring smooth CSS transitions and auto-scroll detection.
- **Smart Media Location Logic**: Enhanced `MediaManager` to intelligently differentiate between Local and Cloud (S3/FTP) storage.
- **Nixomers Ecosystem Overhaul**: Implemented a "Ultra-Premium" design system for the `nix_confirmation` module, featuring glassmorphism (`.gx-glass`), deep shadows, and professional dual-card layout.
- **Financial Intelligence (Recalculate Engine)**: Introduced a deep re-calculation system in Nixomers. It programmatically re-scans order items, reapplies current tax rates, updates total orders, and synchronizes the transaction ledger to ensure accurate Net Income (Gross - Fee - Tax - Shipping).
- **Granular Inventory Control**: Refined the inventory tracking system by making granular unit tracking (per-item records) optional. Administrators now have full control to "Sync Granular Tracking" only for required orders, optimizing database performance for high-volume stores.
- **UiBuilder Logic Loops**: Introduced natively integrated `'loop'` capabilities within the UiBuilder schema framework, significantly streamlining form generation and reducing monolithic string buffers.
- **Premium CSS Utilities**: Expanded the core `genixcms.css` with advanced flexbox gaps, seamless input group borders, soft background shades, and smooth micro-animations.
- **UiBuilder Module Versioning**: Expanded the `header` component configuration schema in UiBuilder with a new optional `version` tag.
- **Nixslider Architecture Modernization**: Refactored the Nixslider backend payload decoupling monolithic HTML string buffering into discrete `UiBuilder` `'loop'` configurations. Resolved PHP 8.2 interpolation deprecations.
- **Nixomers AJAX Standardization**: Fully refactored the Nixomers module's AJAX routing to utilize the centralized `Ajax` class architecture. Consolidated all legacy endpoint hijacking into a single, modular `NixomersAjax` class.
- **Improved AJAX Resource Resolution**: Optimized the core `Ajax` dispatcher to automatically resolve PascalCase class names for module-based resources, enabling seamless routing for underscored module names.
- **Refined Shipping Engine Integration**: Enhanced the shipping regional search logic to support both KiriminAja and API.CO.ID within the standardized AJAX framework.
- **Centralized System Updates Engine**: Introduced a dedicated `UpdatesAjax` class that harmonizes system update checks (core, modules, themes) through the core AJAX dispatcher. Go service also provides a high-performance `UpdateHandler` that scans local module/theme directories and fetches marketplace updates with graceful fallback when external APIs are unavailable.
- **Admin User List Via Go Service**: Expanded Go service with a new `UserHandler` that provides lightning-fast user listing with support for role-based filtering (group, status, search). The PHP bridge automatically decorates raw user data into dashboard-ready HTML rows with avatars, badges, and actions.
- **Enhanced AJAX URL Generation**: Improved `Url::ajax()` method to intelligently handle both action-less endpoints (e.g., `/ajax/updates`) and action-based endpoints (e.g., `/ajax/user/list_users`), eliminating malformed URL generation and simplifying frontend code.
- **Unified Go-Supported Resource Tracking**: Extended the core Go-supported resource list to include `updates` and `user`, enabling seamless switching between PHP and Go backends without additional configuration.
- **Graceful API Fallback Mechanism**: Implemented robust error handling in Go handlers (Updates, User) to return valid fallback responses (empty data arrays, version defaults) instead of HTTP errors when external services are unreachable, ensuring the admin panel remains responsive even during API outages.

### Changed
- **Premium User Profile Interface**: Completely redesigned the Agrifest theme's `user-profile.latte` and `user-settings.latte` into a modern Bento-grid layout utilizing Tailwind CSS, refined spacing, unified hover interactions, and responsive column structures.
- **Dropdown Logic Decoupling**: Refactored the core `header.latte` top navigation. Replaced bloated static HTML with a seamless `<foreach>` loop binding to `UserProfile::getDropdownItems()` to securely handle dynamic links driven by the CMS Core.
- **Profile Order Flow Adjustments**: Transitioned injected table modules (like Nixomers Orders) from the narrow left sidebar column (`col-span-4`) to the principal reading column (`col-span-8`) for an uncluttered horizontal layout.
- **Permanent Cloud Watermarking**: Shifted watermarking logic for S3/FTP assets to the upload phase, enabling direct CDN delivery with brand identity and eliminating server-side network bottlenecks.
- **Media Selector UI Polish**: Balanced the Media Selector dialog width and height for improved usability on various screen resolutions.
- **GeniXCMS UI Standardization**: Standardized modular UI views (UserProfile, Nixomers Purchase History) to exclusively utilize the framework-agnostic `genixcms.css` (`gx-*`) utility system. Replaced hardcoded Tailwind dependencies to ensure component harmony across all active themes (native integration accomplished in the Bakeshop theme).
- **Unified Thumbnail Strategy**: Standardized the use of `Url::thumb()` across all Bakeshop and Artisan Atelier templates (both `.latte` and `.php`) to optimize performance via the "on-the-fly" image processing engine.
- **Nixomers AJAX Modernization**: Migrated frontend catalog AJAX calls to the dedicated `nixomers` module namespace for better endpoint isolation.

### Fixed
- **Smart URL Query Builder**: Fixed the logic in `Url::mod()` output handling within extensions (like Nixomers) ensuring gateway URLs don't break with malformed `&order_id=` querystrings when Smart URLs are active.
- **Template Array Conversion**: Fixed a persistent `Array to string conversion` bug when echoing filtered string outputs from `Hooks::filter` directly inside Latte template files.
- **GxEditor Stability**: Resolved persistent JavaScript syntax errors in `gxeditor.js` and ensured global accessibility for key interaction models.
- **Architecture Stability**: Resolved PHP 8.1+ deprecation warnings and undefined variable warnings in the admin header related to external module menus.
- **Asset Loading Logic**: Fixed a critical CSS loading bug in the confirmation module by implementing a robust URL detection for both `?mod=` and path-based Smart URLs.
- **JavaScript Upload Faults**: Resolved `TypeError` in the file upload interface by ensuring required DOM elements are present during initialization.
- **PHP 8.1+ Security Hardening**: Resolved deprecation warnings in `Typo::cleanX()` related to null parameter handling in `preg_replace_callback`.
- **Latte Template Stability**: Fixed "Undefined variable $website_lang" warnings in theme headers using null-coalescing fallbacks to system defaults.
- **Hook Argument Handling**: Resolved "Undefined array key" errors in Nixomers hooks by properly destructuring variadic arguments in `Hooks::filter` and `Hooks::run`.
- **Layout Parameter Persistence**: Fixed a routing bug where specific post layouts were bypassed when `SMART_URL` was active.
- **System Core**: Officially bumped GeniXCMS to version **2.3.0**.

## [2.2.1] - 2026-04-10

### Added
- **Nixslider CSS Customization Docs**: Added a new "How To Use" section in the Nixslider module admin panel, providing developers with clear CSS selectors and snippets for fine-tuning the slider's appearance.
- **Nixslider UI Polish**: Centered slider captions and implemented a Flexbox-based alignment system for a more professional and balanced visual presentation across all themes.
- **System Core**: Bumped system version to 2.2.1 and updated professional DocBlock headers across all core library classes (`inc/lib/`).

### Fixed
- **GxEditor & Media Management**: Resolved a critical bug where images selected via elFinder failed to render. Standardized the `Asset::elfinderDialog` function to support context-aware callbacks, ensuring seamless image insertion across "Classic" and "Block" modes.
- **GxEditor Source Mode**: Fixed high-contrast visibility bug in "View Source" mode by ensuring dark-mode persistence on focus and removing Bootstrap style conflicts.
- **Theme Version Synchronization**: Unified version numbers in `themeinfo.php` and `options.php` for all core themes to ensure accurate update tracking.
- **Real-time Archive Synchronization**: Resolved a bug where sidebar archive counts were inconsistent with actual posts due to a 24-hour cache delay. Implemented a reactive hook system in the `Archives` class that listens to post lifecycle events (Create, Update, Delete, Publish, Unpublish) to refresh archive data instantly.
- **Default Theme Featured Image**: Resolved a critical layout bug where featured images were rendered twice (once in the theme's custom header and once within the post body). Fixed by decoupling image insertion from the core `PostControl` content stream.
- **PHP Undefined Variable Fix**: Eliminated PHP warnings regarding `$site_footer` and `$site_url` in the rendering pipeline by ensuring consistent initialization in `BaseControl`.
- **Architecture: Rendering Pipeline Refinement**: Centralized `post_title_filter` execution and HTML entity decoding (`Typo::Xclean`) within `Posts::prepare()`. This ensures consistent title rendering across all frontend views while simplifying theme development by allowing templates to use raw variables (e.g., `{$p->title}`) without manual processing.
- **Theme Cleanup**: Refactored `default`, `bsmag`, `gneex`, and `themusk` themes to leverage the new core-level decoding, removing redundant manual filtering from templates.
- **Smart URL Thumbnail Fix**: Implemented a "recursive-safe" `Url::thumb()` logic that automatically strips existing thumbification parameters, resolving malformed URL bugs (e.g., `?thumb=?thumb=`) when `SMART_URL` is disabled.
- **Shortcode Decoding Fix**: Resolved a "double encoding" issue in Nixslider captions and titles by switching to `Typo::Xclean()` for proper HTML entity decoding.
- **Theme Rendering Standardization**: Migrated `default`, `themusk`, and `bsmag` themes to use the pre-processed `{$content}` variable, ensuring all shortcodes and filters are executed consistently.
- **BSMag Slider Reset**: Added a CSS reset for `.nixslider-slide img` in the BSMag theme to prevent global blog image styles (borders/padding) from distorting the slider layout.


## [2.2.0] - 2026-04-08

### Added
- **GxEditor Math Equation Editor**: Integrated KaTeX for professional LaTeX-based mathematical rendering. Features a real-time preview, custom Sigma-Root (&Sigma;&radic;) toolbar icon, and double-click editing support.
- **GxEditor Table Wizard**: Replaced legacy `prompt()` with a comprehensive Bootstrap-based modal for configurable table insertion (rows, columns, headers, and striped/bordered styles).
- **Dynamic Builder & GxEditor Synchronization**: Implemented a robust `change` event listener and `htmlToShortcode` integration, ensuring seamless data transfer between visual building and textual editing without data loss.
- **Improved Inline Toolbar**: Fixed and standardized the inline toolbar in "Classic Mode" for headings (H1-H3) and paragraphs using the correct `formatBlock` command architecture.
- **Programmatic CSP Whitelisting**: Moved `System::securityHeaders()` to occur after theme and module initialization, enabling developers to dynamically modify Content Security Policy rules via the `system_security_headers_args` hook.
- **BSMag Theme Dark Mode Extensions**: Implemented comprehensive dark mode support for `blog-post-meta` and the entire comments section (`custom-comment-wrap`), ensuring consistent editorial aesthetics across all color modes.
- **Dynamic Page Layouts**: Implemented a robust per-page layout selection system. Theme developers can now create `layout-*.latte` templates which are automatically detected and selectable in the admin dashboard.
- **Documentation**: Added comprehensive guide for creating custom theme layouts in `genixcms-docs/docs/how-to/create-theme.md`.

### Fixed
- **WYSIWYG Editor Registry**: Resolved a bug where `summernote` and `editorjs` would disappear from settings when the global editor toggle was off. Standardized initialization via a new `Editor::loadDefaults()` method.
- **Asset Loading Lifecycle**: Fixed a critical bug in `BaseControl` where premature `Site::footer()` calls in the constructor would clear the asset queue before rendering, causing missing scripts (like Bootstrap JS) on the frontend.
- **BSMag Theme Asset Paths**: Corrected broken path for `color-modes.js` (now v2.1.0) to correctly point to theme-internal assets, resolving MIME type errors and restoring theme switching functionality.
- **Admin UI Polish**: Corrected inconsistent spacing and border rendering in the page and post creation forms for a more unified "GeniXCMS 2.0" aesthetic.
- **Builder Compatibility (HTMLPurifier)**: Reconfigured `HTMLPurifier` in `Typo::cleanX()` to allow `<style>` tags and common CSS/Builder attributes (`class`, `id`, `style`, `data-*`). This prevents visual builders like GrapesJS from losing their styles upon saving.
- **GxEditor Layout Refinement**: Streamlined the color picker UI and refined block margins for a more balanced and professional authoring workspace.

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
- **Version Bump**: Officially updated core version to **2.2.1** and themes to **2.1.1** to reflect these critical stability fixes in the 2.0 series.
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
