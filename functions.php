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
 * Auto-create WooCommerce pages if they don't exist.
 * Runs once on theme activation.
 */
function vibe_woo_create_pages() {
	// Only run if WooCommerce is active and pages haven't been created yet
	if ( ! class_exists( 'WooCommerce' ) || get_option( 'vibe_woo_pages_created' ) ) {
		return;
	}

	// Install default WooCommerce pages using WooCommerce's own function
	if ( function_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
		update_option( 'vibe_woo_pages_created', '1' );
	}
}
add_action( 'after_switch_theme', 'vibe_woo_create_pages' );

/**
 * Ensure WooCommerce endpoints are flushed on theme activation
 */
function vibe_woo_flush_rewrite_rules() {
	if ( class_exists( 'WooCommerce' ) ) {
		flush_rewrite_rules();
	}
}
add_action( 'after_switch_theme', 'vibe_woo_flush_rewrite_rules' );

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

            // Initialize WooCommerce variations on page load
            jQuery(document).ready(function($) {
                // Only initialize if variations form exists
                if ($('form.variations_form').length) {
                    $('form.variations_form').wc_variation_form();
                }
            });
        }

        // Buy Now functionality
        window.buyNow = function(productId) {
            const form = document.querySelector('.single-product form.cart, .single-product form.variations_form');
            if (!form) {
                console.error('Cart form not found');
                return;
            }

            // For variable products, check if variation is selected
            if (form.classList.contains('variations_form')) {
                const variationId = form.querySelector('input[name="variation_id"]');
                if (!variationId || !variationId.value) {
                    alert('Please select product options before proceeding');
                    return;
                }
            }

            // Get quantity
            const qtyInput = form.querySelector('input[name="quantity"]');
            const quantity = qtyInput ? qtyInput.value : 1;

            // Add to cart and redirect to checkout
            if (typeof jQuery !== 'undefined' && typeof wc_add_to_cart_params !== 'undefined') {
                const formData = new FormData(form);
                formData.append('add-to-cart', productId);

                jQuery.ajax({
                    type: 'POST',
                    url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response && !response.error) {
                            // Redirect to checkout
                            window.location.href = '<?php echo esc_url( wc_get_checkout_url() ); ?>';
                        } else {
                            alert('Error adding product to cart');
                        }
                    },
                    error: function() {
                        alert('Error adding product to cart');
                    }
                });
            } else {
                // Fallback: submit form and redirect
                form.submit();
                setTimeout(function() {
                    window.location.href = '<?php echo esc_url( wc_get_checkout_url() ); ?>';
                }, 500);
            }
        };
    });
    </script>
    <?php
}
