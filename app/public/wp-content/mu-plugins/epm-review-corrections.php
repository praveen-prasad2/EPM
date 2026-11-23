<?php
/**
 * Plugin Name: EPM Reviewed Content Corrections
 * Description: Reviewed product copy, accessible category navigation and corrected reader catalogue.
 */
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('epm-review-corrections', plugins_url('epm-review-corrections.css', __FILE__), [], (string) filemtime(__DIR__.'/epm-review-corrections.css'));
}, 1600);

function epm_review_fragment($doc, $html) {
    $fragment_doc = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $fragment_doc->loadHTML('<?xml encoding="UTF-8"><div id="epm-fragment">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $fragment = $doc->createDocumentFragment();
    foreach ((new DOMXPath($fragment_doc))->query('//*[@id="epm-fragment"]')->item(0)->childNodes as $child) {
        $fragment->appendChild($doc->importNode($child, true));
    }
    return $fragment;
}

function epm_review_set_html($element, $html) {
    while ($element->firstChild) $element->removeChild($element->firstChild);
    $element->appendChild(epm_review_fragment($element->ownerDocument, $html));
}

function epm_review_product($name, $image, $copy, $features, $link) {
    $src = plugins_url('epm-light-assets/review/'.$image, __FILE__);
    $html = '<section class="epm-review-product"><div><img src="'.esc_url($src).'" alt="'.esc_attr($name).' reader" loading="lazy" decoding="async"></div><div><h2>'.esc_html($name).'</h2>'.$copy.'<ul>';
    foreach ($features as $feature) $html .= '<li>'.esc_html($feature).'</li>';
    return $html.'</ul><a href="'.esc_url($link).'">Manufacturer product information</a></div></section>';
}

function epm_review_reader_catalogue($page) {
    if ($page === 3618) {
        $html = epm_review_product('uPASS Go', 'nedap-upass-go.png', '<p>The successor to the discontinued uPASS Reach uses passive UHF RFID for vehicle access at car parks, gated communities and staff entrances.</p>', ['Read range up to 10 metres with a compatible UHF windshield tag; actual range depends on installation and environment.', 'Adjustable reading range and support for EPC Gen2 V2 tags.', 'Wiegand and OSDP integration options.'], 'https://www.nedapidentification.com/products/upass/upass-go/');
        $html .= epm_review_product('uPASS Target', 'nedap-upass-target.png', '<p>A long-range UHF reader for demanding vehicle-access layouts, including logistics sites and industrial entrances.</p>', ['Read range up to 15 metres with a compatible UHF windshield tag; installation and environment affect performance.', 'Supports an additional antenna for wider coverage or entry and exit arrangements.', 'Compatible with passive UHF tags and access-control integration.'], 'https://www.nedapidentification.com/products/uhf-readers/upass-target/');
    } else {
        $html = epm_review_product('Nedap NVITE', 'nedap-nvite.png', '<p>A multi-technology reader for QR codes, mobile credentials and cards. It supports visitor access and mixed-credential installations.</p><p>Nedap has announced an NVITE end-of-life date of 30 June 2027 and lists Way QR as its alternative. Contact us to discuss existing installations and replacement options.</p>', ['QR-code scanning for visitor and temporary access.', 'Supports compatible smartcards, proximity cards, BLE and NFC credentials.', 'Confirm credential and controller compatibility for the intended installation.'], 'https://www.nedapidentification.com/products/mobile-and-card-solutions-readers/nvite/');
        $html .= epm_review_product('Nedap Way QR', 'nedap-way-qr.png', '<p>Way QR combines QR-code scanning with card and mobile identification for visitor and temporary access.</p>', ['QR, card and mobile credentials in one reader.', 'Suitable for projects moving from NVITE to the Nedap Way reader family.', 'Select the credential and integration configuration with your installer.'], 'https://www.nedapidentification.com/products/mobile-and-card-solutions-readers/way-qr/');
        $html .= '<section class="epm-review-note"><h2>Other QR-capable readers</h2><p><a href="'.esc_url(get_permalink(3370)).'">Suprema BioStation 3</a> combines facial authentication with QR/barcode support. Suprema X-Station 2 is another QR/barcode-capable terminal; supported credentials vary by model. Contact us to compare devices for your access-control platform.</p></section>';
    }
    return $html.'<p class="epm-review-contact"><a href="'.esc_url(get_permalink(1775)).'">Discuss your reader requirements</a></p>';
}

function epm_review_directory() {
    $groups = [
        'Enterprise Access Control Software' => [['SPICA',2883],['NEDAP',2925],['INVIXIUM',3196],['SUPREMA',3221]],
        'Face Recognition Readers' => [['SUPREMA — BioEntry W3 and BioStation 3',3370],['IDEMIA — VisionPass',3406],['HID — Amico',3430],['INVIXIUM — IXM ROSTO and TFACE',3459]],
        'Touchless Biometric Readers' => [['IRIS ID — iris readers',3246],['IDEMIA — MorphoWave fingerprint readers',3263]],
        'Touch-Based Biometric Readers' => [['IDEMIA — optical fingerprint readers',3493],['SUPREMA — optical fingerprint readers',3524],['HID — multispectral fingerprint readers',3300]],
        'Cards & Credentials' => [['Smart Contactless Cards',3563],['Mobile Virtual Credentials',3584],['Fingerprint Cards',3335]],
        'ID Card Readers' => [['HID Smart Card Readers',3599],['NEDAP Long-Range Readers',3618],['HID Long-Range Readers',3637],['QR Code Readers',3651],['Wireless Readers',3672]],
        'Time & Attendance Solutions' => [['Attendance Tablets',3284],['WebTA',3314],['Time & Space',3708]],
        'Visitor Management Solutions' => [['Visitor & Contractor Management Software',3753],['SPICA Visitor & Contractor Management Software',3728]],
        'People Counting & Occupancy' => [['People Counting & Occupancy Management',3690]],
    ];
    $html = '<section class="epm-review-directory"><h2>Access Control and Workspace Solutions</h2><p>Explore access-control software, readers, credentials and workplace management systems.</p><div class="epm-review-directory-grid">';
    foreach ($groups as $title => $items) {
        $html .= '<section><h3>'.esc_html($title).'</h3><ul>';
        foreach ($items as [$label,$id]) $html .= '<li><a href="'.esc_url(get_permalink($id)).'">'.esc_html($label).'<span aria-hidden="true"> →</span></a></li>';
        $html .= '</ul></section>';
    }
    return $html.'</div></section>';
}

function epm_review_content($content) {
    if (!$content || !class_exists('DOMDocument') || strpos($content, 'data-elementor-id=') === false) return $content;
    static $config;
    if ($config === null) $config = json_decode(file_get_contents(__DIR__.'/epm-review-corrections.json'), true);
    $doc = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="epm-review-root">'.$content.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($doc);
    foreach ($xpath->query('//*[@data-elementor-id]') as $root) {
        if ($root->hasAttribute('data-epm-reviewed')) continue;
        $page = (int) $root->getAttribute('data-elementor-id');
        $settings = $config[$page] ?? [];
        $root->setAttribute('data-epm-reviewed', '1');
        foreach ($settings['replace'] ?? [] as $from => $to) {
            foreach ($xpath->query('.//text()[not(ancestor::script) and not(ancestor::style)]', $root) as $text) {
                $text->nodeValue = str_replace($from, $to, $text->nodeValue);
            }
        }
        foreach ($settings['widgets'] ?? [] as $id => $html) {
            $widget = $xpath->query('.//*[@data-id="'.$id.'"]', $root)->item(0);
            if (!$widget) continue;
            $heading = $xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," elementor-heading-title ")]', $widget)->item(0);
            $container = $xpath->query('./div[contains(@class,"elementor-widget-container")]', $widget)->item(0);
            epm_review_set_html($heading ?: ($container ?: $widget), $html);
        }
        foreach ($settings['remove'] ?? [] as $id) {
            $element = $xpath->query('.//*[@data-id="'.$id.'"]', $root)->item(0);
            if ($element) $element->parentNode->removeChild($element);
        }
        foreach ($settings['buttons'] ?? [] as $id => $button) {
            foreach ($xpath->query('.//*[@data-id="'.$id.'"]//a', $root) as $anchor) {
                $anchor->setAttribute('href', home_url($button['url']));
                $label = $xpath->query('.//*[contains(@class,"elementor-button-text")]', $anchor)->item(0);
                if ($label) $label->nodeValue = $button['text'];
            }
        }
        if (!empty($settings['tail'])) {
            $start = $xpath->query('./*[@data-id="'.$settings['tail'].'"]', $root)->item(0);
            if ($start) {
                while ($start) { $next = $start->nextSibling; $root->removeChild($start); $start = $next; }
                $root->appendChild(epm_review_fragment($doc, epm_review_reader_catalogue($page)));
            }
        }
        if (!empty($settings['directory'])) {
            $hero = $xpath->query('./*[@data-id]', $root)->item(0);
            while ($hero && $hero->nextSibling) $root->removeChild($hero->nextSibling);
            $root->appendChild(epm_review_fragment($doc, epm_review_directory()));
        }
        if ($page === 3524) {
            $root->appendChild(epm_review_fragment($doc, '<section class="epm-review-note"><h2>BioStation 2a and lifecycle guidance</h2><p>BioStation 2a is Suprema’s fingerprint-authentication terminal, not a facial-recognition reader. BioStation A2 and L2 are listed in Suprema’s lifecycle notices. Contact us for a current fingerprint reader or support for a legacy installation.</p><p><a href="https://docs.supremainc.com/en/reference/compares/fingerprint-devices-funcs">Compare Suprema fingerprint devices</a></p></section>'));
        }
        // Correct rendered links too, including cached Elementor menu widgets.
        $menu = [2485=>['AI Solutions',5233],1689=>['Queue Management Software and Systems Dubai',1647],1741=>['Smart Self-Service KIOSK',1623],1904=>['Customer Experience',5778],1905=>['Customer Feedback Touch Screens',1879],3802=>['QR Code Readers',3651],650=>['Clients',4104]];
        foreach ($menu as $id => [$label,$target]) {
            foreach ($xpath->query('.//li[contains(concat(" ",normalize-space(@class)," ")," menu-item-'.$id.' ")]/a', $root) as $anchor) {
                $anchor->setAttribute('href', get_permalink($target));
                foreach (iterator_to_array($anchor->childNodes) as $child) {
                    if ($child->nodeType === XML_TEXT_NODE || $child->nodeName === 'br') $anchor->removeChild($child);
                }
                $anchor->insertBefore($doc->createTextNode($label), $anchor->firstChild);
            }
        }
        foreach ($xpath->query('.//a', $root) as $anchor) {
            $href = $anchor->getAttribute('href');
            if (strpos($href, '/qr-code-editors') !== false) $anchor->setAttribute('href', home_url('/qr-code-readers/'));
            if (trim($anchor->textContent) === 'Contact Us' && (!$href || $href === '#')) $anchor->setAttribute('href', get_permalink(1775));
        }
        if ($page === 641) {
            foreach ($xpath->query('.//text()[contains(.,"Emirates photo marketing")]', $root) as $text) $text->nodeValue = str_replace('Emirates photo marketing LLC.', 'ePm Systems and Solutions · Emirates Photo Marketing LLC', $text->nodeValue);
            foreach ([647,649] as $id) {
                foreach (iterator_to_array($xpath->query('.//li[contains(concat(" ",normalize-space(@class)," ")," menu-item-'.$id.' ")]', $root)) as $li) $li->parentNode->removeChild($li);
            }
            foreach ($xpath->query('.//li[contains(concat(" ",normalize-space(@class)," ")," menu-item-648 ")]/a', $root) as $anchor) $anchor->setAttribute('href', home_url('/wp-sitemap.xml'));
        }
        if (in_array($page, [12,641], true)) {
            foreach (iterator_to_array($xpath->query('.//a[contains(@class,"elementor-social-icon")]', $root)) as $anchor) {
                $href = trim($anchor->getAttribute('href'));
                if (!$href || $href === '#') {
                    $parent = $anchor->parentNode;
                    if ($parent->nodeName === 'span') $parent->parentNode->removeChild($parent);
                    else $parent->removeChild($anchor);
                }
            }
        }
        // Supply useful descriptions for product imagery, without replacing existing alt text.
        $last_heading = get_the_title($page);
        foreach ($xpath->query('.//*[@data-widget_type]', $root) as $widget) {
            $h = $xpath->query('.//*[self::h1 or self::h2 or self::h3]', $widget)->item(0);
            if ($h && !preg_match('/^(Main Features|Features|Applications|Recommended|Required)/i', trim($h->textContent))) $last_heading = trim($h->textContent);
            foreach ($xpath->query('.//img', $widget) as $image) {
                if (trim($image->getAttribute('alt')) === '') $image->setAttribute('alt', $last_heading.' — product illustration');
            }
        }
    }
    $wrapper = $xpath->query('//*[@id="epm-review-root"]')->item(0);
    $html = '';
    foreach ($wrapper->childNodes as $child) $html .= $doc->saveHTML($child);
    return $html;
}
add_filter('elementor/frontend/the_content', 'epm_review_content', 90);
add_filter('the_content', 'epm_review_content', 90);

// Keep the corrected QR URL canonical while retaining the existing page and old-link redirect.
add_filter('request', function ($vars) {
    if (($vars['pagename'] ?? '') === 'qr-code-readers') { unset($vars['pagename']); $vars['page_id'] = 3651; }
    return $vars;
});
add_filter('page_link', function ($url, $id) { return (int) $id === 3651 ? home_url('/qr-code-readers/') : $url; }, 10, 2);
add_action('template_redirect', function () {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (rtrim((string) $path, '/') === '/qr-code-editors') { wp_safe_redirect(home_url('/qr-code-readers/'), 301); exit; }
});
