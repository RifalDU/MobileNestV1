<?php
/**
 * Brand Logo Configuration
 * Uses Simpleicons CDN for reliable, consistent brand logos
 * CDN URL: https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/
 */

$brand_logos = [
    'Apple' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/apple.svg',
        'alt' => 'Apple Logo'
    ],
    'Samsung' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/samsung.svg',
        'alt' => 'Samsung Logo'
    ],
    'Xiaomi' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/xiaomi.svg',
        'alt' => 'Xiaomi Logo'
    ],
    'OPPO' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/oppo.svg',
        'alt' => 'OPPO Logo'
    ],
    'Vivo' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/vivo.svg',
        'alt' => 'Vivo Logo'
    ],
    'Realme' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/realme.svg',
        'alt' => 'Realme Logo'
    ]
];

/**
 * Get brand logo URL
 * @param string $brand_name - The name of the phone brand
 * @return string - The CDN URL of the logo
 */
function get_brand_logo_url($brand_name) {
    global $brand_logos;
    
    if (isset($brand_logos[$brand_name]['image_url'])) {
        return $brand_logos[$brand_name]['image_url'];
    }
    
    // Return generic phone icon if brand not found
    return 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/phone.svg';
}

/**
 * Get brand logo HTML
 * @param string $brand_name - The name of the phone brand
 * @param array $attributes - Additional HTML attributes (class, style, etc)
 * @return string - HTML img tag with logo
 */
function get_brand_logo_html($brand_name, $attributes = []) {
    global $brand_logos;
    
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = isset($brand_logos[$brand_name]['alt']) ? $brand_logos[$brand_name]['alt'] : 'Brand Logo';
    
    // Default attributes
    $default_class = 'brand-logo';
    $class = isset($attributes['class']) ? $attributes['class'] : $default_class;
    $style = isset($attributes['style']) ? $attributes['style'] : 'width: 50px; height: 50px;';
    
    return sprintf(
        '<img src="%s" alt="%s" class="%s" style="%s" loading="lazy">',
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
 * @return array|null - Array with 'image_url', 'alt' or null if not found
 */
function get_brand_logo_data($brand_name) {
    global $brand_logos;
    return isset($brand_logos[$brand_name]) ? $brand_logos[$brand_name] : null;
}

/**
 * Get brand logo with fallback
 * @param string $brand_name - The name of the phone brand
 * @param string $fallback_color - Fallback background color (hex)
 * @return string - HTML with logo or icon
 */
function get_brand_logo_with_fallback($brand_name, $fallback_color = '#f0f0f0') {
    $logo_data = get_brand_logo_data($brand_name);
    
    if (!$logo_data) {
        return sprintf(
            '<div style="width: 50px; height: 50px; background-color: %s; border-radius: 50%%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666; font-size: 12px;">%s</div>',
            htmlspecialchars($fallback_color),
            htmlspecialchars(substr($brand_name, 0, 2))
        );
    }
    
    return sprintf(
        '<img src="%s" alt="%s" style="width: 50px; height: 50px; object-fit: contain;" loading="lazy">',
        htmlspecialchars($logo_data['image_url']),
        htmlspecialchars($logo_data['alt'])
    );
}
?>
