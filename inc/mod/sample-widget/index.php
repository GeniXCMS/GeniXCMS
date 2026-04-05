<?php
/**
 * Name: Sample Widget Module
 * Desc: Modul contoh yang berfungsi sebagai widget dinamis.
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id
 * License: MIT License
 * Icon: bi bi-cpu
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class SampleWidget
{
    public static function init()
    {
        // Mendaftarkan widget ke sistem hooks Widget (jika ingin otomatis)
        // Atau cukup panggil class ini dari sistem Widget.
        Hooks::attach('sample_widget_render', array('SampleWidget', 'render'));
    }

    public static function render()
    {
        $html = '
        <div class="sample-widget-content text-center py-3">
            <div class="mb-3">
                <i class="bi bi-rocket-takeoff text-primary display-4"></i>
            </div>
            <h5 class="fw-bold">Module Widget</h5>
            <p class="small text-muted">Ini adalah contoh widget yang dimuat dari sebuah module GeniXCMS.</p>
            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="alert(\'Hello from Module!\')">Click Me</button>
        </div>';
        return $html;
    }
}

// Inisialisasi widget
SampleWidget::init();
