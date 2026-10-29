<?php
/**
 * Plugin Name: EPM Inner Page Typography and Layout
 * Description: Responsive typography and card layout for inner pages only.
 */
defined('ABSPATH') || exit;
add_action('wp_enqueue_scripts', function () {
    // Do not enqueue anything on the protected front page or shared templates.
    if (!is_page() || is_front_page() || (int)get_queried_object_id() === 5) return;
    $file=__DIR__.'/epm-inner-page-layout.css';
    wp_enqueue_style('epm-inner-page-layout',plugins_url('epm-inner-page-layout.css',__FILE__),[],(string)filemtime($file));
    wp_enqueue_style('epm-feature-cards',plugins_url('epm-feature-cards.css',__FILE__),['epm-inner-page-layout'],(string)filemtime(__DIR__.'/epm-feature-cards.css'));
    wp_enqueue_style('epm-hero-headings',plugins_url('epm-hero-headings.css',__FILE__),['epm-feature-cards'],(string)filemtime(__DIR__.'/epm-hero-headings.css'));
    wp_enqueue_script('epm-feature-cards',plugins_url('epm-feature-cards.js',__FILE__),[],(string)filemtime(__DIR__.'/epm-feature-cards.js'),true);
},100);
