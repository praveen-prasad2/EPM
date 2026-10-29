<?php
/**
 * Plugin Name: EPM Seven Selected Pages
 * Description: White surfaces and distinct card imagery, restricted to the seven requested pages.
 */
defined('ABSPATH') || exit;
function epm_seven_page_enabled() {
    return is_page([2454,2464,2466,2883,2925,3196,3221]);
}
add_filter('body_class',function($classes){
    if(epm_seven_page_enabled())$classes[]='epm-seven-pages';
    return $classes;
});
add_action('wp_enqueue_scripts',function(){
    if(!epm_seven_page_enabled())return;
    wp_enqueue_style('epm-seven-pages',plugins_url('epm-seven-pages.css',__FILE__),[],(string)filemtime(__DIR__.'/epm-seven-pages.css'));
    wp_enqueue_script('epm-seven-pages',plugins_url('epm-seven-pages.js',__FILE__),[],(string)filemtime(__DIR__.'/epm-seven-pages.js'),true);
},1200);
add_action('elementor/frontend/before_render',function($element){
    if(!epm_seven_page_enabled())return;
    static $map;
    if($map===null)$map=json_decode(file_get_contents(__DIR__.'/epm-seven-pages-images.json'),true);
    $page=get_queried_object_id();
    // Never alter shared header/footer or embedded documents.
    $document=\Elementor\Plugin::$instance->documents->get_current();
    if(!$document || (int)$document->get_main_id()!==$page)return;
    $entry=$map[$page][$element->get_id()]??null;
    if($entry){
        $element->add_render_attribute('_wrapper','class','epm-seven-card');
        $element->add_render_attribute('_wrapper','style','--epm-seven-photo:url("'.esc_url(plugins_url($entry['asset'],__FILE__)).'");');
    }
    $settings=$element->get_settings();
    if($element->get_type()==='container' && !empty($settings['background_image']['url']) && !$element->get_children()) {
        $element->add_render_attribute('_wrapper','class','epm-seven-media');
    }
});
add_filter('elementor/widget/render_content',function($content,$widget){
    // Use supplied artwork and screenshots without stale attachment srcsets.
    $logo = null;
    if (is_page(3196) && $widget->get_id() === 'd3e838c') {
        $logo = ['invixium-logo.png', 'Invixium', 1350, 757];
    } elseif (is_page(3221) && strpos($content, 'logo_biostarx-1') !== false) {
        $logo = ['biostarx-logo.png', 'BioStar X', 676, 174];
    } elseif (is_page(2925) && $widget->get_id() === 'ae9ec1d') {
        $logo = ['nedap-logo.png', 'Nedap', 852, 852];
    } elseif (is_page(2883)) {
        $spica_images = [
            '965bb7e' => ['spica-visual-monitoring.png', 'Spica visual monitoring and space occupancy dashboard', 1165, 1350],
            '701a7bf' => ['spica-alarm-management.png', 'Spica alarm management details', 1608, 978],
            '37c8541' => ['spica-event-log.png', 'Spica event log', 1673, 940],
            '1fe490e' => ['spica-audit-log.png', 'Spica application audit log', 1673, 940],
        ];
        $logo = $spica_images[$widget->get_id()] ?? null;
    }
    if ($logo && class_exists('WP_HTML_Tag_Processor')) {
        $html = new WP_HTML_Tag_Processor($content);
        while ($html->next_tag('img')) {
            $html->set_attribute('src', plugins_url('epm-light-assets/'.$logo[0], __FILE__));
            $html->set_attribute('alt', $logo[1]);
            $html->set_attribute('width', $logo[2]);
            $html->set_attribute('height', $logo[3]);
            $html->remove_attribute('srcset');
            $html->remove_attribute('sizes');
        }
        $content = $html->get_updated_html();
    }
    if(!is_page(2464))return $content;
    $classes=(string)$widget->get_settings('_css_classes');
    if(strpos($classes,'epm-feature-card')===false)return $content;
    $content=str_replace(
        ['vehicleidentificationand','forvehicles','blacklistmanagement','bottlenecksand','parkingoperations'],
        ['vehicle identification and','for vehicles','blacklist management','bottlenecks and','parking operations'],
        $content
    );
    return preg_replace('/<\/p>\s*<p[^>]*>/i',' ',$content);
},40,2);
