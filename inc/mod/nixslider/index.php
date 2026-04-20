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
        Hooks::attach('init', function () {
            AdminMenu::add([
                'id' => 'nixslider',
                'label' => _('Nixslider'),
                'icon' => 'bi bi-images',
                'url' => 'index.php?page=mods&mod=nixslider',
                'access' => 1,
                'position' => 'external',
                'order' => 12,
            ]);
        });

        Hooks::attach('post_content_filter', array('Nixslider', 'parseShortcode'));
        Hooks::attach('footer_load_lib', array('Nixslider', 'loadAssets'));
    }

    /**
     * Parses the [nixslider] shortcode within the content.
     *
     * @param string|array $content The content to filter.
     * @return string Modified content with slider HTML.
     */
    public static function parseShortcode($content)
    {
        // Handle array if passed from Hooks::filter (GeniXCMS standard)
        if (is_array($content)) {
            $content = isset($content[0]) ? $content[0] : '';
        }

        // Basic validation
        if (!is_string($content) || empty($content)) {
            return $content;
        }

        // Pre-processing: Support common encoded brackets or quotes that might exist 
        // if the theme calls filters in a non-standard order.
        $tag = 'nixslider';
        if (strpos($content, '[' . $tag) === false && strpos($content, '&#91;' . $tag) === false) {
            return $content;
        }

        // Ensure Shortcode library is active
        if (!class_exists('Shortcode')) {
            return $content;
        }

        // Use core Shortcode parser
        return Shortcode::parse($tag, $content, function ($attrs) {
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
            
            // Allow shortcode attributes to override stored DB settings
            $rounded = isset($attrs['rounded']) ? $attrs['rounded'] : (isset($slider['rounded']) ? $slider['rounded'] : 'on');
            $arrow = isset($attrs['arrow']) ? $attrs['arrow'] : (isset($slider['arrow']) ? $slider['arrow'] : 'on');
            $bullet = isset($attrs['bullet']) ? $attrs['bullet'] : (isset($slider['bullet']) ? $slider['bullet'] : 'on');
            $speed = isset($attrs['speed']) ? $attrs['speed'] : (isset($slider['speed']) ? $slider['speed'] : '5000');
            $transition = isset($attrs['transition']) ? $attrs['transition'] : (isset($slider['transition']) ? $slider['transition'] : 'fade');

            $classes = ['nixslider-container'];
            if ($rounded === 'off') $classes[] = 'nixslider-rounded-off';
            if ($transition === 'slide') $classes[] = 'nixslider-transition-slide';
            else $classes[] = 'nixslider-transition-fade';

            $html = '<div class="' . implode(' ', $classes) . '" id="nixslider-' . $cleanId . '" data-speed="' . Typo::int($speed) . '">';
            $html .= '<div class="nixslider-wrapper" style="height: ' . $imgHeight . ';">';
            
            foreach ($slider['images'] as $index => $img) {
                $active = $index === 0 ? 'active' : '';
                $html .= '<div class="nixslider-slide ' . $active . '">';
                $html .= '<img src="' . Typo::cleanX($img['url']) . '" alt="' . Typo::Xclean($img['title'] ?? '') . '">';
                if (!empty($img['title']) || !empty($img['caption'])) {
                    $html .= '<div class="nixslider-caption">';
                    if (!empty($img['title'])) $html .= '<h3>' . Typo::Xclean($img['title']) . '</h3>';
                    if (!empty($img['caption'])) $html .= '<p>' . Typo::Xclean($img['caption']) . '</p>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
            
            if (count($slider['images']) > 1) {
                if ($arrow !== 'off') {
                    $html .= '<a class="nixslider-prev" onclick="moveNixslider(\'' . $cleanId . '\', -1)">&#10094;</a>';
                    $html .= '<a class="nixslider-next" onclick="moveNixslider(\'' . $cleanId . '\', 1)">&#10095;</a>';
                }
                
                if ($bullet !== 'off') {
                    $html .= '<div class="nixslider-dots">';
                    foreach ($slider['images'] as $index => $img) {
                        $active = $index === 0 ? 'active' : '';
                        $html .= '<span class="nixslider-dot ' . $active . '" onclick="currentNixslider(\'' . $cleanId . '\', ' . $index . ')"></span>';
                    }
                    $html .= '</div>';
                }
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
