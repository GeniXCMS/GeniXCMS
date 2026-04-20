# Media Manager Module

High-performance, modular media management and image editing tool for GeniXCMS.

## Overview

The Media Manager module provides a unified interface for managing digital assets across different storage backends (Local, FTP, S3). It includes a native JavaScript selector for easy integration into posts, pages, and third-party modules.

## Architecture

The module follows a service-controller pattern:

- **Core Library (`lib/MediaManager.class.php`)**: Handles file system operations, multi-backend support, image processing (via Intervention Image), and synchronization.
- **AJAX Controller (`lib/MediaManagerAjax.class.php`)**: Standardized entry point for all asynchronous operations.
- **UI Builder Integration (`options/manager.php`)**: Admin view powered by `UiBuilder` for consistent GeniXCMS aesthetics.
- **Frontend Selector (`assets/js/media-selector.js`)**: A framework-agnostic component that provides a beautiful modal interface for asset selection.

## Features

- **Multi-Backend Support**: Seamlessly switch between Local, FTP, and Amazon S3 storage.
- **User-Restricted Folders**: Support for `GX_USER_FOLDER` to isolate user assets.
- **Image Transformation**: Built-in editor (via Cropper.js) for cropping and scaling images directly in the browser.
- **Automatic Optimization**: Auto-resize on upload and optional WebP conversion.
- **Hardened Security**: Integrated CSRF protection via `GX_TOKEN` and strict access control (`User::access(2)`).
- **Infinite Scroll**: Efficiently browse thousands of assets with lazy-loading grid view.
- **Global Integration**: Easily triggers via `GxMedia.select(callback)` from any JavaScript context.

## Integration Guide

### 1. PHP Integration
To load the Media Manager assets (CSS/JS) and global variables in your module or theme:

```php
// In your module's init hook or theme's function.php
Asset::enqueue('gxmedia-selector-js');
```

### 2. JavaScript Usage
To trigger the media selector from your custom script:

```javascript
if (typeof GxMedia !== 'undefined') {
    GxMedia.select(function(url) {
        console.log("Selected Asset URL:", url);
        // Do something with the URL (e.g., update an input field or preview image)
    });
}
```

### 3. User Restricted Folders
To restrict a user to a specific subdirectory within the media directory, define `GX_USER_FOLDER` before loading the media scripts. The Media Manager will treat this folder as a virtual root.

```javascript
window.GX_USER_FOLDER = 'uploads/username';
```

## API & Configuration

### Global Variables
The following variables are injected into the window object when the module is active:

- `GX_AJAX_URL`: The absolute endpoint for Media Manager AJAX requests.
- `GX_TOKEN`: Security token for request validation.
- `GX_MEDIA_DIR`: The relative path to the media storage directory.
- `GX_MEDIA_SELECTOR`: The active selector identifier (default: `media-manager`).
- `GX_USER_FOLDER`: (Optional) Restricts operations to a specific subfolder.

### AJAX Actions
Requests sent to `GX_AJAX_URL` require a `POST` method and a valid `token`. Supported actions:

| Action | Description | Parameters |
| :--- | :--- | :--- |
| `get_media_page` | List assets with pagination | `dir`, `offset`, `limit`, `user_folder` |
| `upload` | Upload a new file | `file`, `dir`, `user_folder` |
| `save_image` | Save edited image (base64) | `image`, `file`, `user_folder` |
| `delete_media` | Delete a file or folder | `file`, `user_folder` |
| `rename_media` | Rename an asset | `file`, `new_name`, `user_folder` |
| `bulk_delete` | Delete multiple assets | `files[]`, `user_folder` |
| `sync_storage` | Re-process watermarks/thumbs | - |

## Requirements

- GeniXCMS 2.0.0+
- PHP 8.1+
- GD Library or Imagick extension
- Intervention Image 3.0+

---
Developed by GeniXCMS. Licensed under MIT.
