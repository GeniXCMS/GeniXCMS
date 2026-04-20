# Changelog - Media Manager Module

All notable changes to the Media Manager module will be documented in this file.

## [1.3.0] - 2026-04-20
### Added
- `normPath()` private helper in `MediaManager` for cross-platform safe path building.
- Path traversal protection in `deleteLocal()` — verifies resolved path stays within `$mediaDir`.
- Support for `zip`, `pdf`, `txt` file types in upload, listing, and grid display.
- Distinct Bootstrap Icons per file type: `bi-file-earmark-pdf`, `bi-file-earmark-zip`, `bi-file-earmark-text`.
- Fallback `copy()+unlink()` in `uploadLocal()` for cross-device temp-to-webroot moves on Linux shared hosting.
- `userFolder` property on `GxMedia` object — captured once at `select()` time for consistent isolation across the entire modal session.
- `getEffectivePath()` method in `media-selector.js` for client-side user folder prefix resolution.
- `stripUserFolder()` and breadcrumb filtering so `GX_USER_FOLDER` acts as virtual root.

### Fixed
- **Critical**: `deleteLocal()`, `uploadLocal()`, `renameMedia()`, `saveImage()` and `listLocal()` used `str_replace(['//', '\\\\'], ['/', '\\'], $fullPath)` which introduced backslashes into paths on Linux, breaking all file operations.
- **Critical**: Admin `manager.php` view was sending all AJAX requests (`delete_media`, `upload`, `rename`, etc.) to `window.location.href` (the admin page) instead of `window.GX_AJAX_URL`. This meant the `MediaManagerAjax` controller was never reached from the admin panel.
- Asset key conflict: `marketplace/index.php` now uses `gxmedia-user-vars` (priority 25) instead of overwriting the base `gxmedia-vars`, ensuring `GX_USER_FOLDER` is always injected independently.
- Root breadcrumb now correctly resolves to `GX_USER_FOLDER` instead of the actual media root.

## [1.2.0] - 2026-04-20
### Added
- Implemented `GX_USER_FOLDER` support to isolate user assets into subdirectories.
- Added path stripping in breadcrumbs to support "virtual root" for user folders.
- Enhanced `MediaManagerAjax` to securely handle `user_folder` parameter across all actions.

## [1.1.0] - 2026-04-20
### Added
- Created `MediaManagerAjax.class.php` to centralize AJAX request handling.
- Integrated `Asset::register` for reliable injection of `GX_AJAX_URL` and `GX_TOKEN`.
- Added support for "all" context in asset loading to ensure media selector availability on frontend and admin.
- Implemented robust absolute path resolution in `media-selector.js` to fix routing issues on sub-folder module pages.

### Changed
- Standardized AJAX routing to use `Url::ajax('media-manager')` instead of relative `index.php` paths.
- Refactored `inc/mod/media-manager/index.php` to remove manual AJAX handlers, delegating to the new `MediaManagerAjax` class.
- Updated `AdminMenu` registration to use absolute URLs.

### Fixed
- Fixed critical bug where media selector AJAX requests were incorrectly prepended with `/mod/` on frontend pages.
- Resolved 404 errors during file upload and folder navigation in frontend contexts.
- Improved CSRF token consistency across frontend and admin.

## [1.0.0] - 2026-03-15
### Added
- Initial release of the High-Performance Modular Media Manager.
- Support for Local, FTP, and S3 storage backends.
- Integrated Intervention Image library for auto-optimization.
- Built-in Cropper.js for in-browser image editing.
