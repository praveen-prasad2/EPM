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
    // This page contains spacer-only paragraphs/divs inside its editorial copy.
    // Remove those from rendered text without changing saved Elementor content.
    if (is_page(3335) && $widget->get_name() === 'text-editor') {
        $content = preg_replace('~<(p|div)\b[^>]*>(?:\s|&nbsp;|&#160;|\x{00a0}|<br\s*/?>)*</\1>~iu', '', $content);
    }
    if (!is_page() || !class_exists('WP_HTML_Tag_Processor')) return $content;
    $html = new WP_HTML_Tag_Processor($content);
    while ($html->next_tag('img')) {
        $src = (string) $html->get_attribute('src');
        if (is_page(3246) && preg_match('~\/iCAM7_main-1(?:-\d+x\d+)?\.webp(?:\?.*)?$~i', $src)) {
            $html->set_attribute('src', plugins_url('epm-light-assets/iris-id-icam7-main.png', __FILE__));
            $html->set_attribute('alt', 'Man using an advanced iCAM7 series iris scanner');
            $html->set_attribute('width', 1933);
            $html->set_attribute('height', 814);
            $html->remove_attribute('srcset');
            $html->remove_attribute('sizes');
        }
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

// Elementor may serve cached widget HTML without running widget render filters.
add_filter('the_content', function ($content) {
    if (!is_page(3563)) return $content;
    $images = [
        'smart-card-crescendo-piv.png' => 'HID Crescendo PIV card',
        'smart-card-activid.png' => 'HID ActivID DisplayCard',
        'smart-card-iclass-prox.png' => 'HID iCLASS SE / Prox cards',
    ];
    $gallery = '<div class="epm-smart-card-gallery">';
    foreach ($images as $file => $alt) {
        $gallery .= '<img src="'.esc_url(plugins_url('epm-light-assets/'.$file, __FILE__)).'" alt="'.esc_attr($alt).'" width="1635" height="962" decoding="async" loading="lazy">';
    }
    $gallery .= '</div>';
    return preg_replace('~<img\b[^>]*\bsrc=["\'][^"\']*/Screenshot-2026-08-28-144011(?:-\d+x\d+)?\.png[^"\']*["\'][^>]*>~i', $gallery, $content, 1);
}, 100);

add_filter('the_content', function ($content) {
    if (!is_page(3246) || !class_exists('WP_HTML_Tag_Processor')) return $content;
    $html = new WP_HTML_Tag_Processor($content);
    while ($html->next_tag('img')) {
        if (!preg_match('~\/iCAM7_main-1(?:-\d+x\d+)?\.webp(?:\?.*)?$~i', (string) $html->get_attribute('src'))) continue;
        $html->set_attribute('src', plugins_url('epm-light-assets/iris-id-icam7-main.png', __FILE__));
        $html->set_attribute('alt', 'Man using an advanced iCAM7 series iris scanner');
        $html->set_attribute('width', 1933);
        $html->set_attribute('height', 814);
        $html->remove_attribute('srcset');
        $html->remove_attribute('sizes');
    }
    return $html->get_updated_html();
}, 100);
