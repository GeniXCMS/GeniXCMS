package handlers

import (
	"context"
	"fmt"
	"net"
	"net/http"
	"os"
	"path"
	"path/filepath"
	"sort"
	"strconv"
	"strings"
	"time"

	"github.com/aws/aws-sdk-go-v2/aws"
	awsconfig "github.com/aws/aws-sdk-go-v2/config"
	"github.com/aws/aws-sdk-go-v2/service/s3"
	"github.com/gin-gonic/gin"
	"github.com/jlaffaye/ftp"
	"github.com/genixcms/go-service/internal/response"
)

type MediaManagerHandler struct {
	MediaDir   string
	MediaURL   string
	Backend    string
	FTPHost    string
	FTPPort    string
	FTPUser    string
	FTPPass    string
	FTPPath    string
	FTPURL     string
	S3Key      string
	S3Secret   string
	S3Bucket   string
	S3Region   string
	S3Endpoint string
	S3Path     string
}

func (h *MediaManagerHandler) GetMediaPage(c *gin.Context) {
	dir := sanitizeMediaDir(c.DefaultQuery("dir", ""))
	userFolder := sanitizeMediaDir(c.DefaultQuery("user_folder", ""))
	offset, _ := strconv.Atoi(c.DefaultQuery("offset", "0"))
	limit, _ := strconv.Atoi(c.DefaultQuery("limit", "24"))

	if userFolder != "" {
		dir = sanitizeMediaDir(path.Join(userFolder, dir))
	}

	backend := strings.ToLower(strings.TrimSpace(h.Backend))
	var (
		items []map[string]any
		err   error
	)

	switch backend {
	case "ftp":
		items, err = h.listFTP(c, dir)
	case "s3":
		items, err = h.listS3(c, dir)
	default:
		items, err = h.listLocal(c, dir)
	}

	if err != nil {
		response.Error(c, http.StatusInternalServerError, err.Error())
		return
	}

	if limit > 0 {
		end := offset + limit
		if offset > len(items) {
			items = []map[string]any{}
		} else if end < len(items) {
			items = items[offset:end]
		} else {
			items = items[offset:]
		}
	} else if offset > 0 {
		if offset >= len(items) {
			items = []map[string]any{}
		} else {
			items = items[offset:]
		}
	}

	response.Success(c, items)
}

func (h *MediaManagerHandler) listLocal(c *gin.Context, dir string) ([]map[string]any, error) {
	mediaRoot := h.MediaDir
	if !filepath.IsAbs(mediaRoot) {
		mediaRoot = filepath.Join(".", mediaRoot)
	}
	mediaRoot = filepath.Clean(mediaRoot)

	targetPath := filepath.Join(mediaRoot, filepath.FromSlash(dir))
	targetPath = filepath.Clean(targetPath)

	if rel, err := filepath.Rel(mediaRoot, targetPath); err != nil || strings.HasPrefix(rel, "..") {
		return nil, fmt.Errorf("Invalid media directory")
	}

	if _, err := os.Stat(targetPath); os.IsNotExist(err) {
		if err := os.MkdirAll(targetPath, 0o777); err != nil {
			return nil, fmt.Errorf("Cannot create media directory")
		}
	} else if err != nil {
		return nil, fmt.Errorf("Media path error: %w", err)
	}

	entries, err := os.ReadDir(targetPath)
	if err != nil {
		return nil, fmt.Errorf("Failed to read media directory: %w", err)
	}

	items := make([]map[string]any, 0, len(entries))
	baseURL := strings.TrimRight(h.getMediaBaseURL(c), "/")

	for _, entry := range entries {
		name := entry.Name()
		if name == "." || name == ".." {
			continue
		}

		relPath := path.Clean(path.Join(dir, name))
		isDir := entry.IsDir()
		if !isDir {
			ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(name), "."))
			if !isAllowedMediaExt(ext) {
				continue
			}
		}

		itemURL := baseURL + "/" + strings.TrimLeft(relPath, "/")
		fileType := getMediaFileType(name, isDir)
		icon := getMediaFileIcon(name, isDir)
		thumb := itemURL
		if isDir {
			thumb = ""
		}

		info, _ := entry.Info()
		size := int64(0)
		modified := ""
		if info != nil {
			size = info.Size()
			modified = info.ModTime().Format("2006-01-02 15:04:05")
		}

		items = append(items, map[string]any{
			"name":        name,
			"path":        relPath,
			"url":         itemURL,
			"thumb":       thumb,
			"thumb_tiles": thumb,
			"is_dir":      isDir,
			"extension":   strings.ToLower(strings.TrimPrefix(filepath.Ext(name), ".")),
			"size":        size,
			"modified":    modified,
			"type":        fileType,
			"icon":        icon,
		})
	}

	sort.SliceStable(items, func(i, j int) bool {
		iDir := items[i]["is_dir"].(bool)
		jDir := items[j]["is_dir"].(bool)
		if iDir != jDir {
			return iDir
		}
		iName := strings.ToLower(items[i]["name"].(string))
		jName := strings.ToLower(items[j]["name"].(string))
		return iName < jName
	})

	return items, nil
}

func (h *MediaManagerHandler) listFTP(c *gin.Context, dir string) ([]map[string]any, error) {
	if strings.TrimSpace(h.FTPHost) == "" {
		return nil, fmt.Errorf("FTP host not configured")
	}

	conn, err := h.getFtpConn()
	if err != nil {
		return nil, err
	}
	defer conn.Quit()

	remoteDir := strings.TrimRight(h.FTPPath, "/")
	if remoteDir == "" {
		remoteDir = "/"
	}
	if dir != "" {
		remoteDir = strings.TrimRight(remoteDir, "/") + "/" + dir
	}

	entries, err := conn.List(remoteDir)
	if err != nil {
		return nil, fmt.Errorf("FTP list failed: %w", err)
	}

	items := make([]map[string]any, 0, len(entries))
	baseURL := strings.TrimRight(h.getMediaBaseURL(c), "/")
	if baseURL == "" {
		baseURL = strings.TrimRight(h.FTPURL, "/")
	}

	for _, entry := range entries {
		name := entry.Name
		if name == "." || name == ".." {
			continue
		}

		isDir := entry.Type == ftp.EntryTypeFolder
		ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(name), "."))
		if !isDir && !isAllowedMediaExt(ext) {
			continue
		}

		relPath := path.Clean(path.Join(dir, name))
		itemURL := baseURL + "/" + strings.TrimLeft(relPath, "/")
		fileType := getMediaFileType(name, isDir)
		icon := getMediaFileIcon(name, isDir)
		thumb := itemURL
		if isDir {
			thumb = ""
		}

		modified := entry.Time.Format("2006-01-02 15:04:05")
		items = append(items, map[string]any{
			"name":        name,
			"path":        relPath,
			"url":         itemURL,
			"thumb":       thumb,
			"thumb_tiles": thumb,
			"is_dir":      isDir,
			"extension":   ext,
			"size":        entry.Size,
			"modified":    modified,
			"type":        fileType,
			"icon":        icon,
		})
	}

	sort.SliceStable(items, func(i, j int) bool {
		iDir := items[i]["is_dir"].(bool)
		jDir := items[j]["is_dir"].(bool)
		if iDir != jDir {
			return iDir
		}
		iName := strings.ToLower(items[i]["name"].(string))
		jName := strings.ToLower(items[j]["name"].(string))
		return iName < jName
	})

	return items, nil
}

func (h *MediaManagerHandler) listS3(c *gin.Context, dir string) ([]map[string]any, error) {
	if strings.TrimSpace(h.S3Bucket) == "" {
		return nil, fmt.Errorf("S3 bucket not configured")
	}

	cfg, err := awsconfig.LoadDefaultConfig(context.Background(), awsconfig.WithRegion(h.S3Region), awsconfig.WithEndpointResolverWithOptions(aws.EndpointResolverWithOptionsFunc(func(service, region string, options ...interface{}) (aws.Endpoint, error) {
		if service == s3.ServiceID && strings.TrimSpace(h.S3Endpoint) != "" {
			return aws.Endpoint{URL: strings.TrimRight(h.S3Endpoint, "/"), SigningRegion: h.S3Region}, nil
		}
		return aws.Endpoint{}, &aws.EndpointNotFoundError{}
	})))
	if err != nil {
		return nil, fmt.Errorf("S3 configuration failed: %w", err)
	}

	s3Client := s3.NewFromConfig(cfg, func(o *s3.Options) {
		o.UsePathStyle = true
	})

	prefix := strings.Trim(strings.TrimRight(h.S3Path, "/"), "/")
	if prefix != "" {
		prefix = prefix + "/"
	}
	if dir != "" {
		prefix = prefix + strings.TrimPrefix(dir, "/") + "/"
	}

	resp, err := s3Client.ListObjectsV2(context.Background(), &s3.ListObjectsV2Input{
		Bucket:    aws.String(h.S3Bucket),
		Prefix:    aws.String(prefix),
		Delimiter: aws.String("/"),
	})
	if err != nil {
		return nil, fmt.Errorf("S3 list failed: %w", err)
	}

	items := make([]map[string]any, 0, len(resp.Contents)+len(resp.CommonPrefixes))

	for _, cp := range resp.CommonPrefixes {
		key := aws.ToString(cp.Prefix)
		name := path.Base(strings.TrimSuffix(key, "/"))
		if name == "" {
			continue
		}
		relPath := path.Clean(path.Join(dir, name))
		items = append(items, map[string]any{
			"name":        name,
			"path":        relPath,
			"url":         "#",
			"thumb":       "",
			"thumb_tiles": "",
			"is_dir":      true,
			"extension":   "",
			"size":        int64(0),
			"modified":    time.Now().Format("2006-01-02 15:04:05"),
			"type":        "folder",
			"icon":        getMediaFileIcon("", true),
		})
	}

	for _, object := range resp.Contents {
		key := aws.ToString(object.Key)
		if key == prefix {
			continue
		}
		name := path.Base(key)
		if name == "" {
			continue
		}
		ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(name), "."))
		if !isAllowedMediaExt(ext) {
			continue
		}

		relPath := path.Clean(path.Join(dir, name))
		itemURL := h.buildS3ObjectURL(c, key)
		fileType := getMediaFileType(name, false)
		icon := getMediaFileIcon(name, false)

		items = append(items, map[string]any{
			"name":        name,
			"path":        relPath,
			"url":         itemURL,
			"thumb":       itemURL,
			"thumb_tiles": itemURL,
			"is_dir":      false,
			"extension":   ext,
			"size":        object.Size,
			"modified":    object.LastModified.Format("2006-01-02 15:04:05"),
			"type":        fileType,
			"icon":        icon,
		})
	}

	sort.SliceStable(items, func(i, j int) bool {
		iDir := items[i]["is_dir"].(bool)
		jDir := items[j]["is_dir"].(bool)
		if iDir != jDir {
			return iDir
		}
		iName := strings.ToLower(items[i]["name"].(string))
		jName := strings.ToLower(items[j]["name"].(string))
		return iName < jName
	})

	return items, nil
}

func (h *MediaManagerHandler) getFtpConn() (*ftp.ServerConn, error) {
	address := net.JoinHostPort(h.FTPHost, h.FTPPort)
	conn, err := ftp.Dial(address, ftp.DialWithTimeout(10*time.Second))
	if err != nil {
		return nil, err
	}

	if err := conn.Login(h.FTPUser, h.FTPPass); err != nil {
		conn.Quit()
		return nil, err
	}
	return conn, nil
}

func (h *MediaManagerHandler) buildS3ObjectURL(c *gin.Context, key string) string {
	baseURL := strings.TrimRight(h.getMediaBaseURL(c), "/")
	if baseURL != "" {
		return baseURL + "/" + strings.TrimLeft(key, "/")
	}

	endpoint := strings.TrimRight(h.S3Endpoint, "/")
	if endpoint != "" {
		return endpoint + "/" + strings.TrimLeft(h.S3Bucket+"/"+key, "/")
	}

	if h.S3Bucket != "" {
		region := strings.TrimSpace(h.S3Region)
		if region == "" {
			region = "us-east-1"
		}
		return fmt.Sprintf("https://%s.s3.%s.amazonaws.com/%s", h.S3Bucket, region, strings.TrimLeft(key, "/"))
	}

	return strings.TrimLeft(key, "/")
}

func (h *MediaManagerHandler) getMediaBaseURL(c *gin.Context) string {
	mediaURL := c.Request.Header.Get("X-GX-MEDIA-URL")
	if mediaURL != "" {
		return strings.TrimRight(mediaURL, "/")
	}

	if strings.ToLower(strings.TrimSpace(h.Backend)) == "ftp" && strings.TrimSpace(h.FTPURL) != "" {
		return strings.TrimRight(h.FTPURL, "/")
	}

	if strings.ToLower(strings.TrimSpace(h.Backend)) == "s3" && strings.TrimSpace(h.MediaURL) != "" {
		return strings.TrimRight(h.MediaURL, "/")
	}

	siteURL := c.Request.Header.Get("X-GX-Site-URL")
	if siteURL != "" {
		return strings.TrimRight(siteURL, "/") + "/assets/media"
	}

	if h.MediaURL == "" || strings.Contains(h.MediaURL, "http://localhost") || strings.Contains(h.MediaURL, "http://127.0.0.1") {
		return strings.TrimRight(h.MediaURL, "/")
	}
	return strings.TrimRight(h.MediaURL, "/")
}

func sanitizeMediaDir(dir string) string {
	dir = strings.ReplaceAll(dir, "\\", "/")
	dir = strings.TrimPrefix(dir, "/")
	dir = strings.TrimPrefix(dir, "./")
	dir = strings.TrimPrefix(dir, "../")
	dir = path.Clean("/" + dir)
	if dir == "/" {
		return ""
	}
	return strings.TrimPrefix(dir, "/")
}

func isAllowedMediaExt(ext string) bool {
	if ext == "" {
		return false
	}
	allowed := []string{"jpg", "jpeg", "png", "gif", "webp", "svg", "mp4", "webm", "mov", "mp3", "wav", "ogg", "pdf", "zip", "txt"}
	for _, a := range allowed {
		if ext == a {
			return true
		}
	}
	return false
}

func getMediaFileType(name string, isDir bool) string {
	if isDir {
		return "directory"
	}
	ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(name), "."))
	switch ext {
	case "jpg", "jpeg", "png", "gif", "webp", "svg":
		return "image"
	case "mp4", "webm", "mov":
		return "video"
	case "mp3", "wav", "ogg":
		return "audio"
	case "pdf":
		return "document"
	case "zip":
		return "archive"
	default:
		return "file"
	}
}

func getMediaFileIcon(name string, isDir bool) string {
	if isDir {
		return "bi bi-folder-fill text-warning"
	}
	ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(name), "."))
	switch ext {
	case "jpg", "jpeg", "png", "gif", "webp", "svg":
		return "bi bi-file-image"
	case "mp4", "webm", "mov":
		return "bi bi-file-play"
	case "mp3", "wav", "ogg":
		return "bi bi-file-earmark-music"
	case "pdf":
		return "bi bi-file-earmark-pdf text-danger"
	case "zip":
		return "bi bi-file-earmark-zip"
	default:
		return "bi bi-file-earmark"
	}
}
