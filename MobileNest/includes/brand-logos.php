<?php
/**
 * Brand Logo Configuration
 * Contains CDN URLs for all smartphone brand logos
 * Used across the application for consistent branding
 */

$brand_logos = [
    'Apple' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/us.svg',
        'alt' => 'Apple Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg'
    ],
    'Xiaomi' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/cn.svg',
        'alt' => 'Xiaomi Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Xiaomi_logo.svg/256px-Xiaomi_logo.svg.png'
    ],
    'Samsung' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/kr.svg',
        'alt' => 'Samsung Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Samsung_Logo.svg/220px-Samsung_Logo.svg.png'
    ],
    'Vivo' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/cn.svg',
        'alt' => 'Vivo Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Vivo_logo.svg/220px-Vivo_logo.svg.png'
    ],
    'Realme' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/in.svg',
        'alt' => 'Realme Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Realme_logo.svg/220px-Realme_logo.svg.png'
    ],
    'OPPO' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/cn.svg',
        'alt' => 'OPPO Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/OPPO_LOGO.svg/220px-OPPO_LOGO.svg.png'
    ],
    'iPhone' => [
        'url' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.11.0/flags/4x3/us.svg',
        'alt' => 'iPhone Logo',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg'
    ]
];

/**
 * Get brand logo URL
 * @param string $brand_name - The name of the phone brand
 * @param string $type - 'image_url' for brand logo or 'url' for country flag
 * @return string - The CDN URL of the logo
 */
function get_brand_logo_url($brand_name, $type = 'image_url') {
    global $brand_logos;
    
    if (isset($brand_logos[$brand_name][$type])) {
        return $brand_logos[$brand_name][$type];
    }
    
    // Return default icon if brand not found
    return 'https://via.placeholder.com/50?text=Phone';
}

/**
 * Get brand logo HTML
 * @param string $brand_name - The name of the phone brand
 * @param array $attributes - Additional HTML attributes (class, style, etc)
 * @return string - HTML img tag with logo
 */
function get_brand_logo_html($brand_name, $attributes = []) {
    global $brand_logos;
    
    if (!isset($brand_logos[$brand_name])) {
        return '<img src="https://via.placeholder.com/50?text=Phone" alt="Phone Logo" class="brand-logo" style="width: 50px; height: 50px;">';
    }
    
    $logo_url = $brand_logos[$brand_name]['image_url'];
    $alt_text = $brand_logos[$brand_name]['alt'];
    
    // Default attributes
    $default_class = 'brand-logo';
    $class = isset($attributes['class']) ? $attributes['class'] : $default_class;
    $style = isset($attributes['style']) ? $attributes['style'] : 'width: 50px; height: 50px;';
    
    return sprintf(
        '<img src="%s" alt="%s" class="%s" style="%s">',
        htmlspecialchars($logo_url),
        htmlspecialchars($alt_text),
        htmlspecialchars($class),
        htmlspecialchars($style)
    );
}

/**
 * Get all available brands
 * @return array - Array of brand names
 */
function get_all_brands() {
    global $brand_logos;
    return array_keys($brand_logos);
}

/**
 * Get brand logo array data
 * @param string $brand_name - The name of the phone brand
 * @return array|null - Array with 'url', 'alt', 'image_url' or null if not found
 */
function get_brand_logo_data($brand_name) {
    global $brand_logos;
    return isset($brand_logos[$brand_name]) ? $brand_logos[$brand_name] : null;
}
?>
