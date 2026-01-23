<?php
/**
 * Vibe Woo Bold - Clean Theme Setup
 * Relies on native WooCommerce/WordPress functionality
 */

if ( ! function_exists( 'vibe_woo_setup' ) ) :
	function vibe_woo_setup() {
		// WooCommerce support
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

		// Gallery support
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Standard WordPress features
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
		
		// Navigation menus
		register_nav_menus( array(
			'primary'       => __( 'Primary Menu', 'vibe-woo' ),
			'footer-menu'   => __( 'Footer Menu', 'vibe-woo' ),
		) );
	}
endif;
add_action( 'after_setup_theme', 'vibe_woo_setup' );

/**
 * Enqueue styles and scripts
 */
function vibe_woo_enqueue() {
	wp_enqueue_style( 'vibe-woo-style', get_stylesheet_uri(), array(), '1.0.0' );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'vibe_woo_enqueue' );

/**
 * Remove WooCommerce default styles - we use Tailwind
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
