<?php
/**
 * Plugin Name: EPM Sitewide White Design
 * Description: Shared white sections, distinct photographic cards and responsive spacing.
 */
defined('ABSPATH') || exit;
add_filter('body_class', function ($classes) {
    if (is_page()) $classes[] = 'epm-sitewide';
    return $classes;
});
add_action('wp_enqueue_scripts', function () {
    if (!is_page()) return;
    foreach (['epm-sitewide-images', 'epm-sitewide'] as $name) {
        wp_enqueue_style($name, plugins_url($name.'.css', __FILE__), [], (string) filemtime(__DIR__.'/'.$name.'.css'));
    }
}, 1500);
add_action('elementor/frontend/before_render', function ($element) {
    if (!is_page()) return;
    $page = get_queried_object_id();
    $document = \Elementor\Plugin::$instance->documents->get_current();
    if (!$document || (int) $document->get_main_id() !== $page) return;
    static $map;
    if ($map === null) $map = json_decode(file_get_contents(__DIR__.'/epm-sitewide-images.json'), true);
    if (isset($map[$page][$element->get_id()])) $element->add_render_attribute('_wrapper', 'class', 'epm-site-card');
    $settings = $element->get_settings();
    if ($element->get_type() === 'container' && !empty($settings['background_image']['url']) && !$element->get_children()) {
        $element->add_render_attribute('_wrapper', 'class', 'epm-site-media');
    }
});
add_filter('elementor/widget/render_content', function ($content, $widget) {
    if (!is_page() || !class_exists('WP_HTML_Tag_Processor')) return $content;
    $html = new WP_HTML_Tag_Processor($content);
    while ($html->next_tag('img')) {
        $src = (string) $html->get_attribute('src');
        if (!preg_match('~(?:invixium-logo|Invixium-Logo)~i', $src)) continue;
        $html->set_attribute('src', plugins_url('epm-light-assets/invixium-logo-black.png', __FILE__));
        $html->set_attribute('alt', 'Invixium');
        $html->set_attribute('width', 640);
        $html->set_attribute('height', 360);
        $html->remove_attribute('srcset');
        $html->remove_attribute('sizes');
    }
    return $html->get_updated_html();
}, 100, 2);
