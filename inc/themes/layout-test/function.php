<?php
/**
 * Layout Test Theme — function.php
 * Registers and enqueues all theme assets programmatically via the Asset class.
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// ── Enqueue Core Assets ────────────────────────────────────────────────
Asset::enqueue('bootstrap-css');
Asset::enqueue('bootstrap-icons');
Asset::enqueue('bootstrap-js');


