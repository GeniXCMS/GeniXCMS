<?php
/**
 * GxEditor Debug Tool - Temporary file for debugging
 * Delete after use
 */
include_once '../../../inc/lib/Db.class.php';
include_once '../../../inc/lib/Config.class.php';
Config::load();

// Simple DB connect
$pdo = Db::connect();

// Get last 3 posts with content
$stmt = $pdo->query("SELECT id, title, content FROM posts ORDER BY id DESC LIMIT 3");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<html><body style="font-family:monospace; padding:20px;">';
echo '<h2>GxEditor Content Debug</h2>';

foreach ($posts as $post) {
    echo '<h3>Post ID: ' . $post['id'] . ' - ' . htmlspecialchars($post['title']) . '</h3>';
    echo '<strong>Content (raw):</strong>';
    echo '<pre style="background:#f0f0f0; padding:10px; overflow:auto; max-height:300px;">' . htmlspecialchars($post['content']) . '</pre>';
    
    // Check for rows
    if (strpos($post['content'], 'class="row') !== false) {
        echo '<p style="color:green;">✓ Contains GRID (row class found)</p>';
    }
    if (strpos($post['content'], 'col-12') !== false) {
        echo '<p style="color:green;">✓ Contains col-12 class</p>';
    }
    if (strpos($post['content'], 'class^="col"') !== false) { // won't match, just for info
        echo '<p style="color:green;">✓ Contains col-* class</p>';
    }
    
    // Check col classes present
    preg_match_all('/class="([^"]*col[^"]*)"/', $post['content'], $matches);
    if (!empty($matches[1])) {
        echo '<p><strong>Col classes found:</strong>';
        foreach (array_unique($matches[1]) as $cls) {
            echo '<br>- ' . htmlspecialchars($cls);
        }
        echo '</p>';
    }
    echo '<hr>';
}
echo '</body></html>';
