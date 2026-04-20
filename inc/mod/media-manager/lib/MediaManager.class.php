<?php
/**
 * Media Manager Core Library
 */
class MediaManager {

    private static $mediaDir;
    private static $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mp3', 'wav', 'pdf', 'zip', 'svg', 'txt'];
    private static $backend;

    public static function init() {
        if (empty(self::$mediaDir)) {
            self::$backend = Options::v('media_storage_backend') ?: 'local';
            self::$mediaDir = Options::v('media_local_path') ?: 'assets/media';
            self::$mediaDir = rtrim(self::$mediaDir, '/');
        }
    }

    /**
     * Build a safe, OS-agnostic absolute path.
     * Converts all backslashes to forward slashes and collapses duplicate slashes.
     */
    private static function normPath(string ...$parts): string {
        $path = implode('/', array_map(fn($p) => rtrim(str_replace('\\', '/', $p), '/'), $parts));
        return preg_replace('#/+#', '/', $path);
    }

    public static function getBackend() {
        self::init();
        return self::$backend;
    }

    public static function listFiles($subdir = '', $offset = 0, $limit = -1) {
        self::init();
        $files = [];
        if (self::$backend == 'local') {
            $files = self::listLocal($subdir);
        } elseif (self::$backend == 'ftp') {
            $files = self::listFtp($subdir);
        } elseif (self::$backend == 's3') {
            $files = self::listS3($subdir);
        }

        if ($limit > 0) {
            return array_slice($files, $offset, $limit);
        }
        return $files;
    }

    private static function listLocal($subdir) {
        $fullPath = self::normPath(GX_PATH, self::$mediaDir, $subdir);
        
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $files = [];
        $items = scandir($fullPath);
        
        // Define media extensions ONLY
        $mediaExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mov', 'mp3', 'wav', 'ogg', 'pdf', 'zip', 'txt'];

        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;

            $itemPath = $fullPath . '/' . $item;
            $relPath = $subdir ? $subdir . '/' . $item : $item;
            $url = Site::$url . self::$mediaDir . '/' . $relPath;

            $is_dir = is_dir($itemPath);
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            
            // Filter: Only Folders or defined Media Extensions
            if (!$is_dir && !in_array($ext, $mediaExt)) continue;

            $is_img = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $thumb = $u = $url;
            $thumb_tiles = $url;

            if ($is_img) {
                // For Local, use Smart URL via Url::thumb
                $u = Url::thumb(self::$mediaDir . '/' . $relPath);
                $thumb = $u;
                $thumb_tiles = Url::thumb(self::$mediaDir . '/' . $relPath, 'crop', '250x250');
            }

            $files[] = [
                'name' => $item,
                'path' => $relPath,
                'url' => $u,
                'thumb' => $thumb,
                'thumb_tiles' => $thumb_tiles,
                'is_dir' => $is_dir,
                'extension' => $ext,
                'size' => $is_dir ? 0 : filesize($itemPath),
                'modified' => filemtime($itemPath),
                'type' => self::getFileType($ext, $is_dir),
                'icon' => self::getFileIcon($ext, $is_dir)
            ];
        }
        return self::sortFiles($files);
    }

    private static function listFtp($subdir) {
        $conn = self::getFtpConn();
        if (!$conn) return [];

        $path = Options::v('media_ftp_path') . '/' . $subdir;
        $path = rtrim($path, '/');
        $items = ftp_mlsd($conn, $path);
        $files = [];

        $mediaExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mov', 'mp3', 'wav', 'ogg', 'pdf', 'zip', 'txt'];

        if ($items) {
            foreach ($items as $item) {
                if ($item['name'] == '.' || $item['name'] == '..') continue;

                $is_dir = ($item['type'] == 'dir');
                $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));

                // Filter: Only Folders or defined Media Extensions
                if (!$is_dir && !in_array($ext, $mediaExt)) continue;

                $relPath = $subdir ? $subdir . '/' . $item['name'] : $item['name'];
                $url = rtrim(Options::v('media_ftp_url'), '/') . '/' . $relPath;

                $files[] = [
                    'name' => $item['name'],
                    'path' => $relPath,
                    'url' => $url,
                    'thumb' => $url,
                    'thumb_tiles' => $url,
                    'is_dir' => $is_dir,
                    'extension' => $ext,
                    'size' => $is_dir ? 0 : ($item['size'] ?? 0),
                    'modified' => strtotime($item['modify'] ?? 'now'),
                    'type' => self::getFileType($ext, $is_dir),
                    'icon' => self::getFileIcon($ext, $is_dir)
                ];
            }
        }
        ftp_close($conn);
        return self::sortFiles($files);
    }

    private static function listS3($subdir) {
        try {
            $s3 = self::getS3Client();
            $bucket = Options::v('media_s3_bucket');
            $prefix = Options::v('media_s3_path') ? rtrim(Options::v('media_s3_path'), '/') . '/' : '';
            $prefix .= $subdir ? rtrim($subdir, '/') . '/' : '';

            $results = $s3->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => $prefix,
                'Delimiter' => '/'
            ]);

            $files = [];
            $mediaExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mov', 'mp3', 'wav', 'ogg', 'pdf', 'zip', 'txt'];

            // Folders (CommonPrefixes)
            if (isset($results['CommonPrefixes'])) {
                foreach ($results['CommonPrefixes'] as $cp) {
                    $name = basename(rtrim($cp['Prefix'], '/'));
                    $relPath = $subdir ? $subdir . '/' . $name : $name;
                    $files[] = [
                        'name' => $name,
                        'path' => $relPath,
                        'url' => '#',
                        'is_dir' => true,
                        'extension' => '',
                        'size' => 0,
                        'modified' => time(),
                        'type' => 'folder',
                        'icon' => self::getFileIcon('', true)
                    ];
                }
            }

            // Files (Contents)
            if (isset($results['Contents'])) {
                foreach ($results['Contents'] as $object) {
                    $name = basename($object['Key']);
                    if (empty($name)) continue; // It's the directory itself
                    
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    // Filter: Only defined Media Extensions
                    if (!in_array($ext, $mediaExt)) continue;

                    $relPath = $subdir ? $subdir . '/' . $name : $name;
                    $url = $s3->getObjectUrl($bucket, $object['Key']);

                    $files[] = [
                        'name' => $name,
                        'path' => $relPath,
                        'url' => $url,
                        'thumb' => $url,
                        'thumb_tiles' => $url,
                        'is_dir' => false,
                        'extension' => $ext,
                        'size' => $object['Size'],
                        'modified' => strtotime($object['LastModified']),
                        'type' => self::getFileType($ext, false),
                        'icon' => self::getFileIcon($ext, false)
                    ];
                }
            }
            return self::sortFiles($files);
        } catch (\Exception $e) {
            return [];
        }
    }

    private static function getS3Client() {
        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => Options::v('media_s3_region') ?: 'us-east-1',
            'credentials' => [
                'key'    => Options::v('media_s3_key'),
                'secret' => Options::v('media_s3_secret'),
            ],
            'use_path_style_endpoint' => true
        ]);
    }

    private static function getFtpConn() {
        $host = Options::v('media_ftp_host');
        $port = Options::v('media_ftp_port') ?: 21;
        $user = Options::v('media_ftp_user');
        $pass = Options::v('media_ftp_pass');

        $conn = ftp_connect($host, $port);
        if ($conn && ftp_login($conn, $user, $pass)) {
            ftp_pasv($conn, true);
            return $conn;
        }
        return false;
    }

    private static function sortFiles($files) {
        usort($files, function($a, $b) {
            if ($a['is_dir'] && !$b['is_dir']) return -1;
            if (!$a['is_dir'] && $b['is_dir']) return 1;
            return strcasecmp($a['name'], $b['name']);
        });
        return $files;
    }

    public static function getFileType($ext, $is_dir) {
        if ($is_dir) return 'folder';
        $img = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $vid = ['mp4', 'webm', 'ogg', 'mov'];
        $aud = ['mp3', 'wav', 'ogg'];
        $doc = ['pdf', 'txt'];
        $arc = ['zip'];

        if (in_array($ext, $img)) return 'image';
        if (in_array($ext, $vid)) return 'video';
        if (in_array($ext, $aud)) return 'audio';
        if (in_array($ext, $doc)) return 'document';
        if (in_array($ext, $arc)) return 'archive';
        return 'file';
    }

    public static function getFileIcon($ext, $is_dir) {
        if ($is_dir) return 'bi bi-folder-fill text-warning';

        switch ($ext) {
            case 'pdf':  return 'bi bi-file-earmark-pdf text-danger';
            case 'zip':  return 'bi bi-file-earmark-zip text-warning';
            case 'txt':  return 'bi bi-file-earmark-text text-secondary';
            case 'mp4': case 'webm': case 'mov': return 'bi bi-play-btn-fill text-danger';
            case 'mp3': case 'wav': case 'ogg': return 'bi bi-music-note-beamed text-info';
        }

        switch (self::getFileType($ext, $is_dir)) {
            case 'image':  return 'bi bi-image text-primary';
            default:       return 'bi bi-file-earmark text-muted';
        }
    }

    public static function formatSize($bytes) {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }

    public static function uploadFile($file, $subdir = '') {
        self::init();
        
        $fileName = basename($file['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedExt)) {
            return ['status' => 'error', 'message' => 'File type not allowed.'];
        }

        // Processing locally first (always required for Intervention Image)
        $tmpLocal = GX_PATH . '/assets/cache/' . time() . '_' . $fileName;
        if (move_uploaded_file($file['tmp_name'], $tmpLocal)) {
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                self::processImage($tmpLocal);
            }

            if (self::$backend == 'local') {
                return self::uploadLocal($tmpLocal, $fileName, $subdir);
            } elseif (self::$backend == 'ftp') {
                return self::uploadFtp($tmpLocal, $fileName, $subdir);
            } elseif (self::$backend == 's3') {
                return self::uploadS3($tmpLocal, $fileName, $subdir);
            }
        }

        return ['status' => 'error', 'message' => 'Failed to process upload.'];
    }

    private static function uploadLocal($tmp, $name, $subdir) {
        $targetDir = self::normPath(GX_PATH, self::$mediaDir, $subdir) . '/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $targetFile = $targetDir . $name;
        if (file_exists($targetFile)) {
            $name = pathinfo($name, PATHINFO_FILENAME) . '_' . time() . '.' . pathinfo($name, PATHINFO_EXTENSION);
            $targetFile = $targetDir . $name;
        }

        if (rename($tmp, $targetFile)) {
            return ['status' => 'success', 'message' => 'Uploaded to local storage.', 'file' => $name];
        }
        // Fallback to copy+unlink (cross-device move, e.g. /tmp to web root)
        if (copy($tmp, $targetFile)) {
            @unlink($tmp);
            return ['status' => 'success', 'message' => 'Uploaded to local storage.', 'file' => $name];
        }
        return ['status' => 'error', 'message' => 'Failed to move to target local folder.'];
    }

    private static function uploadFtp($tmp, $name, $subdir) {
        $conn = self::getFtpConn();
        if (!$conn) return ['status' => 'error', 'message' => 'FTP Connection failed.'];

        $remoteDir = rtrim(Options::v('media_ftp_path'), '/') . '/' . $subdir;
        $remoteDir = rtrim($remoteDir, '/');
        
        // Ensure directory exists
        @ftp_mkdir($conn, $remoteDir);
        
        $remoteFile = $remoteDir . '/' . $name;
        if (ftp_put($conn, $remoteFile, $tmp, FTP_BINARY)) {
            unlink($tmp);
            ftp_close($conn);
            return ['status' => 'success', 'message' => 'Uploaded to FTP server.', 'file' => $name];
        }
        
        ftp_close($conn);
        return ['status' => 'error', 'message' => 'FTP Upload failed.'];
    }

    private static function uploadS3($tmp, $name, $subdir) {
        try {
            $s3 = self::getS3Client();
            $bucket = Options::v('media_s3_bucket');
            $remoteKey = Options::v('media_s3_path') ? rtrim(Options::v('media_s3_path'), '/') . '/' : '';
            $remoteKey .= $subdir ? rtrim($subdir, '/') . '/' : '';
            $remoteKey .= $name;

            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $remoteKey,
                'SourceFile' => $tmp,
                'ACL'    => 'public-read'
            ]);

            unlink($tmp);
            return ['status' => 'success', 'message' => 'Uploaded to S3.', 'file' => $name];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'S3 Upload failed: ' . $e->getMessage()];
        }
    }

    public static function processImage($fullPath) {
        $manager = new \Intervention\Image\ImageManager(\Intervention\Image\Drivers\Gd\Driver::class);
        try {
            $image = $manager->read($fullPath);
            $modified = false;

            // Auto Resize
            if (Options::v('media_autoresize_image') == 'on') {
                $maxWidth = Options::v('media_autoresize_width') ?: 1200;
                if ($image->width() > $maxWidth) { $image->scale(width: $maxWidth); $modified = true; }
            }

            // Permanent Watermark (applied before upload/save)
            if (Options::v('media_use_watermark') == 'on') {
                $watermark_image = Options::v('media_watermark_image');
                $watermark_position = Options::v('media_watermark_position') ?: 'bottom-right';
                $watermark_opacity = Options::v('media_watermark_opacity') ?: 50;
                
                $wmPath = GX_PATH . '/' . $watermark_image;
                if ($watermark_image != '' && file_exists($wmPath)) {
                    $image->place($wmPath, $watermark_position, 0, 0, $watermark_opacity);
                    $modified = true;
                }
            }

            if ($modified) $image->save($fullPath);
            if (Options::v('media_autogenerate_webp') == 'on') Image::convertWebp($fullPath);
        } catch (\Exception $e) {}
    }

    public static function deleteMedia($file) {
        self::init();
        if (self::$backend == 'local') {
            return self::deleteLocal($file);
        } elseif (self::$backend == 'ftp') {
            return self::deleteFtp($file);
        } elseif (self::$backend == 's3') {
            return self::deleteS3($file);
        }
        return ['status' => 'error', 'message' => 'Delete not supported for current backend.'];
    }

    private static function deleteLocal($file) {
        $allowedRoot = self::normPath(GX_PATH, self::$mediaDir);
        $fullPath    = self::normPath(GX_PATH, self::$mediaDir, $file);

        // Security: ensure the resolved path is inside the media directory
        if (strpos($fullPath . '/', $allowedRoot . '/') !== 0) {
            return ['status' => 'error', 'message' => 'Access denied.'];
        }

        if (file_exists($fullPath)) {
            if (is_dir($fullPath)) {
                if (Files::delTree($fullPath)) return ['status' => 'success', 'message' => 'Folder deleted.'];
                return ['status' => 'error', 'message' => 'Failed to delete folder.'];
            } else {
                if (unlink($fullPath)) return ['status' => 'success', 'message' => 'File deleted.'];
                return ['status' => 'error', 'message' => 'Failed to delete file (check permissions).'];
            }
        }
        return ['status' => 'error', 'message' => 'File not found: ' . $file];
    }

    private static function deleteFtp($file) {
        $conn = self::getFtpConn();
        if (!$conn) return ['status' => 'error', 'message' => 'FTP Connection failed.'];
        $remoteFile = rtrim(Options::v('media_ftp_path'), '/') . '/' . $file;
        if (@ftp_delete($conn, $remoteFile) || @ftp_rmdir($conn, $remoteFile)) {
            ftp_close($conn);
            return ['status' => 'success', 'message' => 'Deleted from FTP.'];
        }
        ftp_close($conn);
        return ['status' => 'error', 'message' => 'Failed to delete from FTP.'];
    }

    private static function deleteS3($file) {
        try {
            $s3 = self::getS3Client();
            $bucket = Options::v('media_s3_bucket');
            $remoteKey = Options::v('media_s3_path') ? rtrim(Options::v('media_s3_path'), '/') . '/' : '';
            $remoteKey .= $file;

            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $remoteKey
            ]);
            return ['status' => 'success', 'message' => 'Deleted from S3.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'S3 delete failed.'];
        }
    }

    public static function renameMedia($file, $newName) {
        self::init();
        if (self::$backend == 'local') {
            $oldPath = self::normPath(GX_PATH, self::$mediaDir, $file);
            $newPath = self::normPath(GX_PATH, self::$mediaDir, dirname($file), basename($newName));
            if (file_exists($oldPath) && rename($oldPath, $newPath)) return ['status' => 'success', 'message' => 'Renamed.'];
        }
        return ['status' => 'error', 'message' => 'Rename not supported or failed.'];
    }

    public static function saveImage($data, $targetFile) {
        self::init();
        if (self::$backend != 'local') return ['status' => 'error', 'message' => 'Editing only supported for local storage.'];

        $fullPath = self::normPath(GX_PATH, self::$mediaDir, $targetFile);
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = base64_decode(substr($data, strpos($data, ',') + 1));
            if (file_put_contents($fullPath, $data)) return ['status' => 'success', 'message' => 'Saved.'];
        }
        return ['status' => 'error', 'message' => 'Failed to save.'];
    }

    public static function bulkDelete($files) {
        if (!is_array($files)) return ['status' => 'error', 'message' => 'Invalid data.'];
        foreach ($files as $file) { self::deleteMedia($file); }
        return ['status' => 'success', 'message' => 'Bulk delete process complete.'];
    }

    public static function syncStorage() {
        self::init();
        
        $fullPath = GX_PATH . '/' . self::$mediaDir;
        // 1. Ensure local folder exists 
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        // 2. Re-process all local images (if backend is local or we have local copies)
        // This allows bulk watermarking update
        $directory = new RecursiveDirectoryIterator($fullPath);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    self::processImage($file->getPathname());
                }
            }
        }

        // 3. Clear Thumb Cache 
        $cacheDir = GX_PATH . '/' . self::$mediaDir . '/cache/thumbs';
        if (is_dir($cacheDir)) {
            Files::delTree($cacheDir);
            mkdir($cacheDir, 0777, true);
        }

        return ['status' => 'success', 'message' => 'Storage synchronized, images re-processed, and cache cleared.'];
    }
}
