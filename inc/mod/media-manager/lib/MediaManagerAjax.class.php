<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Media Manager AJAX
 *
 * Handles AJAX requests for the Media Manager module.
 * 
 * @since 2.3.0
 */
class MediaManagerAjax
{
    /**
     * Dispatches the request to the appropriate method.
     */
    public function index($param = null)
    {
        $this->_loadMediaManager();
        $action = $_REQUEST['action'] ?? '';
        if (method_exists($this, $action)) {
            return $this->$action($param);
        }
        return Ajax::error(404, "Action '$action' not found in MediaManagerAjax");
    }

    /**
     * Loads the MediaManager class from the module.
     */
    private function _loadMediaManager()
    {
        if (!class_exists('MediaManager')) {
            require_once GX_PATH . '/inc/mod/media-manager/lib/MediaManager.class.php';
        }
        MediaManager::init();
    }

    /**
     * Handles file uploads.
     */
    public function upload($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $subdir = Typo::cleanX($_POST['dir'] ?? '');
        $userFolder = Typo::cleanX($_POST['user_folder'] ?? '');
        if ($userFolder) {
            $userFolder = str_replace(['..', './', '../'], '', $userFolder);
            $subdir = rtrim($userFolder, '/') . '/' . ltrim($subdir, '/');
        }
        $result = MediaManager::uploadFile($_FILES['file'] ?? null, rtrim($subdir, '/'));
        return Ajax::response($result);
    }

    /**
     * Handles image transforms (saving).
     */
    public function save_image($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $file = Typo::cleanX($_POST['file'] ?? '');
        $userFolder = Typo::cleanX($_POST['user_folder'] ?? '');
        if ($userFolder) {
            $userFolder = str_replace(['..', './', '../'], '', $userFolder);
            $file = rtrim($userFolder, '/') . '/' . ltrim($file, '/');
        }
        $result = MediaManager::saveImage($_POST['image'] ?? '', rtrim($file, '/'));
        return Ajax::response($result);
    }

    /**
     * Handles deleting a media item.
     */
    public function delete_media($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $file = Typo::cleanX($_POST['file'] ?? '');
        $userFolder = Typo::cleanX($_POST['user_folder'] ?? '');
        if ($userFolder) {
            $userFolder = str_replace(['..', './', '../'], '', $userFolder);
            $file = rtrim($userFolder, '/') . '/' . ltrim($file, '/');
        }
        $result = MediaManager::deleteMedia(rtrim($file, '/'));
        return Ajax::response($result);
    }

    /**
     * Handles renaming a media item.
     */
    public function rename_media($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $file = Typo::cleanX($_POST['file'] ?? '');
        $newName = Typo::cleanX($_POST['new_name'] ?? '');
        $userFolder = Typo::cleanX($_POST['user_folder'] ?? '');
        if ($userFolder) {
            $userFolder = str_replace(['..', './', '../'], '', $userFolder);
            $file = rtrim($userFolder, '/') . '/' . ltrim($file, '/');
            $newName = rtrim($userFolder, '/') . '/' . ltrim($newName, '/');
        }
        $result = MediaManager::renameMedia(rtrim($file, '/'), rtrim($newName, '/'));
        return Ajax::response($result);
    }

    /**
     * Handles bulk deletion of media items.
     */
    public function bulk_delete($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $files = $_POST['files'] ?? [];
        $result = MediaManager::bulkDelete($files);
        return Ajax::response($result);
    }

    /**
     * Handles syncing the storage backend.
     */
    public function sync_storage($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $result = MediaManager::syncStorage();
        return Ajax::response($result);
    }

    /**
     * Retrieves a page of media items for infinite scroll or listing.
     */
    public function get_media_page($param = null)
    {
        if (!Ajax::auth($param) || !User::access(2)) {
            return Ajax::error(401, "Unauthorized");
        }
        $subdir = Typo::cleanX($_POST['dir'] ?? '');
        $userFolder = Typo::cleanX($_POST['user_folder'] ?? '');
        if ($userFolder) {
            $userFolder = str_replace(['..', './', '../'], '', $userFolder);
            $subdir = rtrim($userFolder, '/') . '/' . ltrim($subdir, '/');
        }
        $offset = intval($_REQUEST['offset'] ?? 0);
        $limit = intval($_REQUEST['limit'] ?? 24);
        $result = MediaManager::listFiles($subdir, $offset, $limit);
        return Ajax::response(['status' => 'success', 'data' => $result]);
    }
}
