<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Store Catalog AJAX Controller
 * Interrop for NixCatalog::ajaxCatalog()
 */

if (class_exists('NixCatalog')) {
    NixCatalog::ajaxCatalog();
} else {
    Ajax::error(404, 'NixCatalog class not found');
}
