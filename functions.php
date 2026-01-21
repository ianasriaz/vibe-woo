<?php
/**
 * Vibe Woo Bold functions and definitions
 */

if ( ! function_exists( 'vibe_woo_setup' ) ) :
    function vibe_woo_setup() {
        // Add support for WooCommerce
        add_theme_support( 'woocommerce', array(
            'thumbnail_image_width' => 600,
            'single_image_width'    => 900,
            'product_grid'          => array(
                'default_rows'    => 3,
                'min_rows'        => 1,
                'max_rows'        => 10,
                'default_columns' => 3,
                'min_columns'     => 1,
                'max_columns'     => 6,
            ),
        ) );

        // Add support for standard WP features
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
        register_nav_menus( array(
            'menu-1'      => __( 'Primary Menu', 'vibe-woo' ),
            'footer-menu' => __( 'Footer Menu', 'vibe-woo' ),
        ) );
        
        // WooCommerce Gallery Support
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
    }
endif;
add_action( 'after_setup_theme', 'vibe_woo_setup' );

/**
 * Enqueue scripts and styles.
 */
function vibe_woo_scripts() {
    // Enqueue Tailwind CSS (Assuming a compiled version for production)
    wp_enqueue_style( 'vibe-woo-style', get_stylesheet_uri(), array(), '1.0.0' );
    
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'vibe_woo_scripts' );

/**
 * WooCommerce Specific Optimizations
 */

// Remove default WooCommerce styles if you want full control with Tailwind
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// Bold & Simple: Customize the "Add to Cart" button classes (Example)
add_filter( 'woocommerce_loop_add_to_cart_link', 'vibe_woo_add_to_cart_classes', 10, 2 );
function vibe_woo_add_to_cart_classes( $html, $product ) {
    if ( false === strpos( $html, 'class="' ) ) {
        return $html;
    }

    preg_match( '/class="([^"]*)"/', $html, $matches );
    $existing_classes = isset( $matches[1] ) ? explode( ' ', $matches[1] ) : array();
    $existing_classes = array_filter( array_map( 'trim', $existing_classes ) );

    $styled_classes = array_merge(
        $existing_classes,
        array(
            'bg-black',
            'text-white',
            'px-6',
            'py-3',
            'uppercase',
            'font-black',
            'tracking-[0.22em]',
            'hover:bg-gray-900',
            'transition-all',
            'duration-300',
            'shadow-[6px_6px_0_#000]',
        )
    );

    $class_attribute = implode( ' ', array_unique( $styled_classes ) );

    return preg_replace( '/class="([^"]*)"/', 'class="' . esc_attr( $class_attribute ) . '"', $html );
}
