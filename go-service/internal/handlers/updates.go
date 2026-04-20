package handlers

import (
    "encoding/json"
    "fmt"
    "io"
    "net/http"
    "os"
    "path/filepath"
    "regexp"
    "strconv"
    "strings"
    "time"

    "github.com/gin-gonic/gin"
    "github.com/genixcms/go-service/internal/response"
)

type UpdateHandler struct {
    SiteRoot string
}

type updateCheckItem struct {
    ID      string `json:"id"`
    Type    string `json:"type"`
    Version string `json:"version"`
}

type updateLatestPayload struct {
    Version     string `json:"version"`
    DownloadURL string `json:"download_url"`
}

type marketplaceCheckResponse struct {
    Data map[string]updateLatestPayload `json:"data"`
}

func (h *UpdateHandler) Index(c *gin.Context) {
    siteRoot := strings.TrimSpace(c.GetHeader("X-GX-Site-Root"))
    if siteRoot == "" {
        siteRoot = strings.TrimSpace(h.SiteRoot)
    }

    if siteRoot == "" {
        response.Error(c, http.StatusBadRequest, "Site root is not available for updates lookup")
        return
    }

    siteRoot = filepath.Clean(siteRoot)
    modList := h.loadModules(siteRoot)
    themeList := h.loadThemes(siteRoot)

    siteVersion := h.readSiteVersion(siteRoot)

    latestCore, err := h.fetchLatestCore()
    if err != nil {
        fmt.Printf("[go-service] updates: core lookup failed: %s\n", err.Error())
        latestCore = map[string]any{
            "version":      "",
            "download_url": "#",
            "changelog":    "",
        }
    }

    core := map[string]any{
        "v_latest":    latestCore["version"],
        "can_update":  versionCompare(siteVersion, asString(latestCore["version"])),
        "download_url": latestCore["download_url"],
        "changelog":   latestCore["changelog"],
    }

    updates, err := h.fetchMarketplaceUpdates(modList, themeList)
    if err != nil {
        fmt.Printf("[go-service] updates: marketplace lookup failed: %s\n", err.Error())
        updates = map[string]updateLatestPayload{}
    }

    modUpdates := make(map[string]map[string]any)
    for _, mod := range modList {
        vLatest := mod.Version
        canUpdate := false
        downloadURL := "#"
        if latest, ok := updates[mod.ID]; ok {
            if latest.Version != "" {
                vLatest = latest.Version
                canUpdate = versionCompare(mod.Version, latest.Version)
            }
            if latest.DownloadURL != "" {
                downloadURL = latest.DownloadURL
            }
        }
        modUpdates[mod.ID] = map[string]any{
            "v_latest":    vLatest,
            "can_update":  canUpdate,
            "download_url": downloadURL,
        }
    }

    themeUpdates := make(map[string]map[string]any)
    for _, theme := range themeList {
        vCurrent := theme.Version
        vLatest := vCurrent
        canUpdate := false
        downloadURL := "#"
        if latest, ok := updates[theme.ID]; ok {
            if latest.Version != "" {
                vLatest = latest.Version
                canUpdate = versionCompare(theme.Version, latest.Version)
            }
            if latest.DownloadURL != "" {
                downloadURL = latest.DownloadURL
            }
        }
        themeUpdates[theme.ID] = map[string]any{
            "v_latest":    vLatest,
            "can_update":  canUpdate,
            "download_url": downloadURL,
        }
    }

    c.JSON(http.StatusOK, gin.H{
        "status": "success",
        "core":   core,
        "mods":   modUpdates,
        "themes": themeUpdates,
    })
}

func (h *UpdateHandler) fetchLatestCore() (map[string]any, error) {
    apiURL := "https://genixcms.web.id/api/v1/download/latest"
    req, err := http.NewRequest(http.MethodGet, apiURL, nil)
    if err != nil {
        return nil, err
    }
    req.Header.Set("User-Agent", "GeniXCMS/GoService")

    client := &http.Client{Timeout: 15 * time.Second}
    resp, err := client.Do(req)
    if err != nil {
        return nil, err
    }
    defer resp.Body.Close()

    body, err := io.ReadAll(resp.Body)
    if err != nil {
        return nil, err
    }

    var payload struct {
        Data map[string]any `json:"data"`
    }
    if err := json.Unmarshal(body, &payload); err != nil {
        return nil, err
    }

    latestCore := payload.Data
    if latestCore == nil {
        return map[string]any{
            "version":      "",
            "download_url": "#",
            "changelog":    "",
        }, nil
    }

    return map[string]any{
        "version":      asString(latestCore["version"]),
        "download_url": asString(latestCore["download_url"]),
        "changelog":    asString(latestCore["changelog"]),
    }, nil
}

func (h *UpdateHandler) fetchMarketplaceUpdates(mods, themes []updateCheckItem) (map[string]updateLatestPayload, error) {
    checkList := make([]updateCheckItem, 0, len(mods)+len(themes))
    checkList = append(checkList, mods...)
    checkList = append(checkList, themes...)

    apiURL := "https://genixcms.web.id/api/v1/marketplace/check-update"
    bodyBytes, err := json.Marshal(checkList)
    if err != nil {
        return nil, err
    }

    req, err := http.NewRequest(http.MethodPost, apiURL, strings.NewReader(string(bodyBytes)))
    if err != nil {
        return nil, err
    }
    req.Header.Set("Content-Type", "application/json")
    req.Header.Set("User-Agent", "GeniXCMS/GoService")

    client := &http.Client{Timeout: 20 * time.Second}
    resp, err := client.Do(req)
    if err != nil {
        return nil, err
    }
    defer resp.Body.Close()

    if resp.StatusCode != http.StatusOK {
        return nil, fmt.Errorf("unexpected marketplace status: %d", resp.StatusCode)
    }

    data, err := io.ReadAll(resp.Body)
    if err != nil {
        return nil, err
    }

    var payload marketplaceCheckResponse
    if err := json.Unmarshal(data, &payload); err != nil {
        return nil, err
    }

    if payload.Data == nil {
        return map[string]updateLatestPayload{}, nil
    }

    return payload.Data, nil
}

func (h *UpdateHandler) loadModules(root string) []updateCheckItem {
    modDir := filepath.Join(root, "inc", "mod")
    return h.scanDirectory(modDir, "module")
}

func (h *UpdateHandler) loadThemes(root string) []updateCheckItem {
    themeDir := filepath.Join(root, "inc", "themes")
    return h.scanDirectory(themeDir, "theme")
}

func (h *UpdateHandler) scanDirectory(dirPath, itemType string) []updateCheckItem {
    items := []updateCheckItem{}
    entries, err := os.ReadDir(dirPath)
    if err != nil {
        return items
    }

    for _, entry := range entries {
        if !entry.IsDir() {
            continue
        }
        name := entry.Name()
        itemPath := filepath.Join(dirPath, name)
        if itemType == "module" {
            candidate := filepath.Join(itemPath, "index.php")
            if _, err := os.Stat(candidate); err == nil {
                items = append(items, updateCheckItem{ID: name, Type: itemType, Version: h.parseModuleVersion(candidate)})
            }
            continue
        }

        if itemType == "theme" {
            candidate := filepath.Join(itemPath, "themeinfo.php")
            if _, err := os.Stat(candidate); err == nil {
                items = append(items, updateCheckItem{ID: name, Type: itemType, Version: "1.0.0"})
            }
        }
    }

    return items
}

func (h *UpdateHandler) parseModuleVersion(filePath string) string {
    data, err := os.ReadFile(filePath)
    if err != nil {
        return "0.0.0"
    }

    re := regexp.MustCompile(`\* Version: (.*)\s*\*`)
    match := re.FindStringSubmatch(string(data))
    if len(match) >= 2 {
        return strings.TrimSpace(match[1])
    }
    return "0.0.0"
}

func (h *UpdateHandler) readSiteVersion(root string) string {
    versionFile := filepath.Join(root, "VERSION")
    data, err := os.ReadFile(versionFile)
    if err != nil {
        return ""
    }
    return strings.TrimSpace(string(data))
}

func asString(value any) string {
    if value == nil {
        return ""
    }
    switch v := value.(type) {
    case string:
        return v
    case fmt.Stringer:
        return v.String()
    default:
        return fmt.Sprint(v)
    }
}

func versionCompare(current, latest string) bool {
    return compareVersion(current, latest) < 0
}

func compareVersion(current, latest string) int {
    current = strings.TrimSpace(current)
    latest = strings.TrimSpace(latest)
    if current == latest {
        return 0
    }

    currentParts := strings.Split(current, ".")
    latestParts := strings.Split(latest, ".")
    length := len(currentParts)
    if len(latestParts) > length {
        length = len(latestParts)
    }

    for i := 0; i < length; i++ {
        cur := parseVersionPart(currentParts, i)
        lat := parseVersionPart(latestParts, i)
        if cur < lat {
            return -1
        }
        if cur > lat {
            return 1
        }
    }

    return 0
}

func parseVersionPart(parts []string, index int) int {
    if index >= len(parts) {
        return 0
    }
    part := strings.TrimSpace(parts[index])
    value, err := strconv.Atoi(part)
    if err != nil {
        return 0
    }
    return value
}
