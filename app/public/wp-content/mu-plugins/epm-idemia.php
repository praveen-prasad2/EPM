<?php
/**
 * Plugin Name: EPM IDEMIA VisionPass Page
 * Description: Scoped VisionPass presentation based on the supplied IDEMIA reference.
 */
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    if (!is_page(3263)) return;
    wp_enqueue_style('epm-idemia', plugins_url('epm-idemia.css', __FILE__), [], (string) filemtime(__DIR__.'/epm-idemia.css'));
}, 1600);

add_filter('the_content', function ($content) {
    if (!is_page(3263) || (int) get_the_ID() !== 3263 || is_admin() || is_feed()) return $content;
    if (isset($_GET['elementor-preview'])) return $content;
    $asset = static function ($file) { return esc_url(plugins_url('epm-light-assets/'.$file, __FILE__)); };
    $features = [
        ['Advanced Facial Recognition', 'features/b01c04497a0d-clear.svg'],
        ['Enhanced Security', 'photography/pexels-13007861.jpg'],
        ['Fast & Seamless Access', 'photography/pexels-5225436.jpg'],
        ['Flexible Authentication Options', 'photography/pexels-13657444.jpg'],
        ['Smart Enterprise Integration', 'features/d1aac1e81455.webp'],
    ];
    $benefits = [
        ['Unique performance', 'Thanks to the pairing of a state-of-the-art optical set combining 3D/IR cameras with IDEMIA’s latest advances in AI and image processing, VisionPass provides a high level of security and user convenience.', 'photography/pexels-8554382.jpg'],
        ['Reliable security', 'These facial recognition devices incorporate IDEMIA’s latest spoofing detection mechanisms and are capable of coping with face changes, including hairstyle, glasses and helmets.', 'features/security-operations.webp'],
        ['Easy deployment', 'VisionPass devices are suitable for all environments (IP65 + IK07). They also offer seamless integration with PAC software and MorphoManager.', 'photography/pexels-13657523.jpg'],
    ];
    ob_start(); ?>
    <div class="epm-idemia">
        <section class="epm-idemia-hero" aria-labelledby="epm-idemia-title">
            <img src="<?= $asset('idemia/visionpass-hero.png') ?>" alt="IDEMIA VisionPass facial recognition readers in a modern entrance" width="1920" height="1193" fetchpriority="high">
            <div class="epm-idemia-hero-inner"><h1 id="epm-idemia-title">IDEMIA</h1></div>
        </section>
        <section class="epm-idemia-intro epm-idemia-wrap" aria-labelledby="epm-idemia-product">
            <div class="epm-idemia-brand"><img src="<?= $asset('idemia/idemia-logo.png') ?>" alt="IDEMIA" width="697" height="459" loading="lazy"></div>
            <p class="epm-idemia-eyebrow">Facial Recognition Access Control</p>
            <h2 id="epm-idemia-product">VisionPass SP</h2>
            <p class="epm-idemia-subtitle">Providing a premium access control experience with the VisionPass range</p>
            <div class="epm-idemia-product">
                <img src="<?= $asset('idemia/visionpass-range.png') ?>" alt="The three IDEMIA VisionPass facial recognition reader models" width="499" height="327" loading="lazy">
                <div><p>IDEMIA reveals VisionPass SP, its latest facial recognition solution for access control.</p><p>IDEMIA has enriched its product offer with VisionPass SP, a sleek and compact terminal with an eco-friendly design that provides accurate and fair matching results.</p></div>
            </div>
            <div class="epm-idemia-copy">
                <p>IDEMIA, the leader in biometrics for access control solutions, has enriched its product offer with VisionPass SP, the latest generation of facial recognition access control solution.</p>
                <p>In 2020, IDEMIA launched VisionPass, a robust and reliable device that thwarts all kinds of spoofing attempts. VisionPass SP (Simplified Profile) builds on this established range.</p>
                <p>With all the essential features and capabilities of the field-proven VisionPass, the SP version provides accurate and fair matching results in less than one second, for all users, in any lighting. This improves the user experience while maintaining a high level of security.</p>
            </div>
            <h2 class="epm-idemia-features-title">Main Features</h2>
            <div class="epm-idemia-features">
                <?php foreach ($features as [$title, $photo]): ?>
                    <article class="epm-idemia-photo-card" style="--card-photo:url('<?= $asset($photo) ?>')"><h3><?= esc_html($title) ?></h3></article>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="epm-idemia-banner" aria-label="VisionPass SP technology">
            <div class="epm-idemia-wrap">
                <p>VisionPass SP is a sleek and compact terminal developed to ease integration into various environments. With an eco-friendly design, it offers automatic deep-sleep mode capability, significantly reducing power consumption.</p>
                <p>To build VisionPass SP, IDEMIA paired its latest advances in artificial intelligence and facial recognition algorithms with a state-of-the-art optical set combining visible, infrared and 3D stereovision to meet the expectations of the most demanding clients.</p>
                <p>Integrating the latest cybersecurity standards, VisionPass SP embeds a secure-by-default configuration to enforce security in the field and help prevent cyber-attacks.</p>
                <p>VisionPass SP was presented at the Intersec 2024 tradeshow in Dubai and is available through IDEMIA’s partner network.</p>
            </div>
        </section>
        <section class="epm-idemia-benefits epm-idemia-wrap" aria-label="VisionPass benefits">
            <div class="epm-idemia-benefit-grid">
                <?php foreach ($benefits as [$title, $copy, $photo]): ?>
                    <article class="epm-idemia-photo-card" style="--card-photo:url('<?= $asset($photo) ?>')"><h3><?= esc_html($title) ?></h3><p><?= esc_html($copy) ?></p></article>
                <?php endforeach; ?>
            </div>
            <a class="epm-idemia-contact" href="<?= esc_url(home_url('/contact/')) ?>">Contact Us</a>
        </section>
    </div>
    <?php return ob_get_clean();
}, 200);
