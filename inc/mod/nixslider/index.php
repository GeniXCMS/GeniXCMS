<?php
/**
 * Name: Nixslider
 * Desc: A custom GeniXCMS Module for professional image sliders
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS Team
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-images
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class Nixslider
{
    public function __construct()
    {
        AdminMenu::add([
            'id' => 'nixslider',
            'label' => _('Nixslider'),
            'icon' => 'bi bi-images',
            'url' => 'index.php?page=mods&mod=nixslider',
            'access' => 1,
            'position' => 'external',
            'order' => 12,
        ]);

        Hooks::attach('post_content_filter', array('Nixslider', 'parseShortcode'));
        Hooks::attach('footer_load_lib', array('Nixslider', 'loadAssets'));
    }

    public static function parseShortcode($content)
    {
        if (is_array($content)) {
            $content = isset($content[0]) ? $content[0] : '';
        }
        
        if (!is_string($content) || empty($content)) {
            return $content;
        }

        return Shortcode::parse('nixslider', $content, function ($attrs) {
            $id = isset($attrs['id']) ? $attrs['id'] : '';
            if (empty($id)) return '';

            $data = Options::get('nixslider_data');
            if (empty($data)) return '';

            $sliders = json_decode($data, true);
            if (!isset($sliders[$id])) return '';

            $slider = $sliders[$id];
            
            if (empty($slider['images'])) return '';

            $cleanId = Typo::cleanX($id);
            $imgHeight = isset($slider['height']) ? Typo::cleanX($slider['height']) : '400px';

            $html = '<div class="nixslider-container" id="nixslider-' . $cleanId . '">';
            $html .= '<div class="nixslider-wrapper" style="height: ' . $imgHeight . ';">';
            
            foreach ($slider['images'] as $index => $img) {
                $active = $index === 0 ? 'active' : '';
                $html .= '<div class="nixslider-slide ' . $active . '">';
                $html .= '<img src="' . Typo::cleanX($img['url']) . '" alt="' . Typo::cleanX($img['title']) . '" style="height: ' . $imgHeight . ';">';
                if (!empty($img['title']) || !empty($img['caption'])) {
                    $html .= '<div class="nixslider-caption">';
                    if (!empty($img['title'])) $html .= '<h3>' . Typo::cleanX($img['title']) . '</h3>';
                    if (!empty($img['caption'])) $html .= '<p>' . Typo::cleanX($img['caption']) . '</p>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
            
            if (count($slider['images']) > 1) {
                $html .= '<a class="nixslider-prev" onclick="moveNixslider(\'' . $cleanId . '\', -1)">&#10094;</a>';
                $html .= '<a class="nixslider-next" onclick="moveNixslider(\'' . $cleanId . '\', 1)">&#10095;</a>';
                
                $html .= '<div class="nixslider-dots">';
                foreach ($slider['images'] as $index => $img) {
                    $active = $index === 0 ? 'active' : '';
                    $html .= '<span class="nixslider-dot ' . $active . '" onclick="currentNixslider(\'' . $cleanId . '\', ' . $index . ')"></span>';
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
            
            return $html;
        });
    }

    public static function loadAssets()
    {
        $html = '<link rel="stylesheet" href="'.Site::$url.'/inc/mod/nixslider/assets/css/nixslider.css">';
        $html .= '<script src="'.Site::$url.'/inc/mod/nixslider/assets/js/nixslider.js"></script>';
        return $html;
    }
}

new Nixslider();
