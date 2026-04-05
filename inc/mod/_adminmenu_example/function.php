<?php
/**
 * Contoh penggunaan AdminMenu::add() di dalam sebuah modul GeniXCMS.
 *
 * Letakkan kode ini di dalam file function.php atau index.php modul Anda.
 * AdminMenu akan otomatis tersedia karena sudah di-autoload oleh sistem.
 */

// ─── Contoh 1: Menu sederhana di bagian "External" (default) ───────────────
AdminMenu::add([
    'id'       => 'my_shop',
    'label'    => _('My Shop'),
    'icon'     => 'bi bi-cart3',
    'url'      => 'index.php?page=mods&mod=my_shop',
    'access'   => 3,          // Level Author ke atas yang bisa melihat
    'position' => 'external', // Tampil di bagian "External" sidebar
    'order'    => 10,
]);

// ─── Contoh 2: Menu dengan sub-item (dropdown/collapsible) ─────────────────
AdminMenu::add([
    'id'       => 'inventory',
    'label'    => _('Inventory'),
    'icon'     => 'bi bi-box-seam',
    'url'      => '#',
    'access'   => 2,          // Level Editor ke atas
    'position' => 'external',
    'order'    => 20,
    'children' => [
        [
            'label' => _('Products'),
            'icon'  => 'bi bi-box',
            'url'   => 'index.php?page=mods&mod=inventory&tab=products',
        ],
        [
            'label' => _('Categories'),
            'icon'  => 'bi bi-folder',
            'url'   => 'index.php?page=mods&mod=inventory&tab=categories',
        ],
        [
            'label' => _('Stock Report'),
            'icon'  => 'bi bi-bar-chart',
            'url'   => 'index.php?page=mods&mod=inventory&tab=stock',
        ],
    ],
]);

// ─── Contoh 3: Injeksi ke bagian "Main Navigation" ─────────────────────────
AdminMenu::add([
    'id'       => 'analytics',
    'label'    => _('Analytics'),
    'icon'     => 'bi bi-graph-up-arrow',
    'url'      => 'index.php?page=mods&mod=analytics',
    'access'   => 1,          // Level Supervisor ke atas
    'position' => 'main',     // Muncul di Main Navigation
    'order'    => 5,
]);

// ─── Contoh 4: Hapus menu yang sudah terdaftar (jika perlu) ────────────────
// AdminMenu::remove('my_shop');

// ─── Contoh 5: Menambahkan Sub Menu ke Menu yang sudah ada (Posts) ─────────
// Gunakan AdminMenu::addChild('id_parent', [...])
AdminMenu::addChild('posts', [
    'label'  => _('My Custom Sub'),
    'url'    => 'index.php?page=posts&tab=custom',
    'icon'   => 'bi bi-star',
    'access' => 4, // 4 = Author kebawah bisa akses (User Level check)
]);
