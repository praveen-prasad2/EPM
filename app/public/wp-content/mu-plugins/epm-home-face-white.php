<?php
/**
 * Plugin Name: EPM Home and Face Reader White Surfaces
 * Description: White content sections for the home and four face recognition pages.
 */
defined('ABSPATH') || exit;

function epm_home_face_white_enabled() {
    return is_front_page() || is_page([
        'face-recognition-readers',
        'face-recognition-readers-idemia',
        'face-recognition-readers-hid',
        'face-recognition-readers-invixium',
    ]);
}

add_filter('body_class', function ($classes) {
    if (epm_home_face_white_enabled()) $classes[] = 'epm-home-face-white';
    return $classes;
});

add_action('wp_enqueue_scripts', function () {
    if (!epm_home_face_white_enabled()) return;
    wp_enqueue_style('epm-home-face-white', plugins_url('epm-home-face-white.css', __FILE__), [], (string) filemtime(__DIR__ . '/epm-home-face-white.css'));
}, 1300);

add_filter('elementor/widget/render_content', function ($content, $widget) {
    if (!is_front_page() || $widget->get_id() !== '88f3675') return $content;
    $html = new WP_HTML_Tag_Processor($content);
    while ($html->next_tag('img')) {
        $html->set_attribute('src', plugins_url('epm-light-assets/our-strengths-line.png', __FILE__));
        $html->set_attribute('width', 6582);
        $html->set_attribute('height', 2376);
        $html->set_attribute('alt', 'Our strengths: experience, awards, people, expertise and technical support');
        $html->remove_attribute('srcset');
        $html->remove_attribute('sizes');
    }
    return $html->get_updated_html();
}, 50, 2);
