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
        )
    );

    $class_attribute = implode( ' ', array_unique( $styled_classes ) );

    return preg_replace( '/class="([^"]*)"/', 'class="' . esc_attr( $class_attribute ) . '"', $html );
}

/**
 * Keep header cart count and mini-cart in sync via fragments.
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'vibe_woo_cart_fragments' );
function vibe_woo_cart_fragments( $fragments ) {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        return $fragments;
    }

    ob_start();
    ?>
    <span class="js-vibe-cart-count absolute -top-1 -right-1 flex items-center justify-center min-w-[1.25rem] h-5 px-1 bg-black text-white text-xs font-bold rounded-full"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
    <?php
    $fragments['.js-vibe-cart-count'] = ob_get_clean();

    ob_start();
    woocommerce_mini_cart();
    $fragments['#vibe-mini-cart-contents'] = ob_get_clean();

    return $fragments;
}

/**
 * Hide WooCommerce added to cart messages (drawer opens instead).
 */
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

/**
 * Auto-create or update WooCommerce pages with shortcodes on theme activation.
 */
function vibe_woo_create_pages() {
	if ( class_exists( 'WooCommerce' ) ) {
		$pages = array(
			'shop'      => '[woocommerce_shop]',
			'cart'      => '[woocommerce_cart]',
			'checkout'  => '[woocommerce_checkout]',
			'my-account' => '[woocommerce_my_account]',
		);

		foreach ( $pages as $page_type => $shortcode ) {
			$page_id = wc_get_page_id( $page_type );
			
			if ( ! $page_id || $page_id < 1 ) {
				// Page doesn't exist, create it
				$page = array(
					'post_title'   => ucfirst( str_replace( '-', ' ', $page_type ) ),
					'post_content' => $shortcode,
					'post_status'  => 'publish',
					'post_type'    => 'page',
				);
				$new_page_id = wp_insert_post( $page );
				
				// Update WooCommerce setting
				update_option( 'woocommerce_' . $page_type . '_page_id', $new_page_id );
			} else {
				// Page exists, ensure it has the shortcode
				$page = get_post( $page_id );
				if ( $page && strpos( $page->post_content, $shortcode ) === false ) {
					wp_update_post( array(
						'ID'           => $page_id,
						'post_content' => $shortcode,
					) );
				}
			}
		}
	}
}
add_action( 'after_setup_theme', 'vibe_woo_create_pages' );

/**
 * Lightweight header interactions (mobile nav + search modal + cart drawer).
 */
add_action( 'wp_footer', 'vibe_woo_header_interactions', 30 );
function vibe_woo_header_interactions() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Announcement bar rotation
        var announcements = [
            'Free shipping on order above Rs.5000',
            'Hastle free exchange policy',
            'Pay cash on delivery'
        ];
        var currentIndex = 0;
        var announcementText = document.querySelector('[data-announcement-text]');
        var prevBtn = document.querySelector('[data-announcement-prev]');
        var nextBtn = document.querySelector('[data-announcement-next]');

        function updateAnnouncement(index) {
            if (announcementText) {
                announcementText.textContent = announcements[index];
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                currentIndex = (currentIndex - 1 + announcements.length) % announcements.length;
                updateAnnouncement(currentIndex);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                currentIndex = (currentIndex + 1) % announcements.length;
                updateAnnouncement(currentIndex);
            });
        }

        // Auto-rotate every 5 seconds
        setInterval(function() {
            currentIndex = (currentIndex + 1) % announcements.length;
            updateAnnouncement(currentIndex);
        }, 5000);

        // Mobile navigation toggle
        var navToggle = document.querySelector('[data-nav-toggle]');
        var mobileNav = document.querySelector('[data-mobile-nav]');

        if (navToggle && mobileNav) {
            navToggle.addEventListener('click', function () {
                mobileNav.classList.toggle('hidden');
                var expanded = navToggle.getAttribute('aria-expanded') === 'true';
                navToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            });
        }

        // Search modal toggle
        var searchToggle = document.querySelector('[data-search-toggle]');
        var searchModal = document.querySelector('[data-search-modal]');
        var searchClose = document.querySelector('[data-search-close]');

        function openSearch() {
            if (searchModal) {
                searchModal.classList.remove('hidden');
                document.documentElement.classList.add('overflow-hidden');
                var searchInput = searchModal.querySelector('input[type="search"]');
                if (searchInput) {
                    setTimeout(function() { searchInput.focus(); }, 100);
                }
            }
        }

        function closeSearch() {
            if (searchModal) {
                searchModal.classList.add('hidden');
                document.documentElement.classList.remove('overflow-hidden');
            }
        }

        if (searchToggle) {
            searchToggle.addEventListener('click', openSearch);
        }

        if (searchClose) {
            searchClose.addEventListener('click', closeSearch);
        }

        if (searchModal) {
            searchModal.addEventListener('click', function(e) {
                if (e.target === searchModal) {
                    closeSearch();
                }
            });
        }

        // Cart drawer toggle
        var cartToggle = document.querySelector('[data-cart-toggle]');
        var cartDrawer = document.getElementById('vibe-cart-drawer');
        var cartOverlay = document.querySelector('[data-cart-overlay]');
        var cartCloseButtons = document.querySelectorAll('[data-cart-close]');

        function openCart() {
            if (!cartDrawer || !cartOverlay) return;
            cartDrawer.classList.remove('translate-x-full');
            cartOverlay.classList.remove('hidden');
            if (cartToggle) cartToggle.setAttribute('aria-expanded', 'true');
            document.documentElement.classList.add('overflow-hidden');
        }

        function closeCart() {
            if (!cartDrawer || !cartOverlay) return;
            cartDrawer.classList.add('translate-x-full');
            cartOverlay.classList.add('hidden');
            if (cartToggle) cartToggle.setAttribute('aria-expanded', 'false');
            document.documentElement.classList.remove('overflow-hidden');
        }

        if (cartToggle) {
            cartToggle.addEventListener('click', openCart);
        }

        if (cartOverlay) {
            cartOverlay.addEventListener('click', closeCart);
        }

        cartCloseButtons.forEach(function (btn) {
            btn.addEventListener('click', closeCart);
        });

        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape') {
                closeCart();
                closeSearch();
            }
        });

        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('added_to_cart', function () {
                openCart();
            });

            // Initialize WooCommerce variations
            jQuery(document).ready(function($) {
                if ($('.variations_form').length > 0) {
                    $('form.variations_form').each(function() {
                        $(this).wc_variation_form();
                    });
                }

                // Handle radio button changes for custom swatches
                $('.attribute-radio').on('change', function() {
                    const $form = $(this).closest('form.variations_form');
                    const formData = {};
                    
                    $form.find('.attribute-radio:checked').each(function() {
                        const attrName = $(this).data('attribute_name');
                        const attrValue = $(this).val();
                        formData['attribute_' + attrName.replace('pa_', '')] = attrValue;
                    });

                    // Trigger variation change
                    $form.find('.variations').trigger('change');
                });
            });
        }

        // Buy Now functionality
        window.buyNow = function(productId) {
            // Get the form on the page
            const form = document.querySelector('.single-product form.cart');
            if (form) {
                // Create a hidden submit button and trigger it
                const addToCartBtn = form.querySelector('[name="add-to-cart"]');
                if (addToCartBtn) {
                    // Add a temporary redirect after add-to-cart
                    const tempAttribute = form.getAttribute('data-redirect-to-checkout');
                    form.setAttribute('data-redirect-to-checkout', 'true');
                    addToCartBtn.click();
                    
                    // After a short delay, redirect to checkout
                    setTimeout(function() {
                        const checkoutUrl = document.querySelector('a[href*="/checkout"]');
                        if (checkoutUrl) {
                            window.location.href = checkoutUrl.href;
                        } else {
                            // Fallback to WooCommerce checkout URL
                            const baseUrl = window.location.origin;
                            const pathArray = window.location.pathname.split('/');
                            window.location.href = baseUrl + '/?p=checkout' || baseUrl + '/checkout/';
                        }
                    }, 500);
                }
            }
        };
    });
    </script>
    <?php
}
