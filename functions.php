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
 * Can be triggered manually via admin.
 */
function vibe_woo_create_pages() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Define pages to create
	$pages = array(
		'shop' => array(
			'name'    => 'shop',
			'title'   => 'Shop',
			'content' => ''
		),
		'cart' => array(
			'name'    => 'cart',
			'title'   => 'Cart',
			'content' => '[woocommerce_cart]'
		),
		'checkout' => array(
			'name'    => 'checkout',
			'title'   => 'Checkout',
			'content' => '[woocommerce_checkout]'
		),
		'myaccount' => array(
			'name'    => 'my-account',
			'title'   => 'My Account',
			'content' => '[woocommerce_my_account]'
		)
	);

	foreach ( $pages as $key => $page ) {
		$page_id = get_option( 'woocommerce_' . $key . '_page_id' );
		
		// Check if page exists and is published
		if ( ! $page_id || get_post_status( $page_id ) !== 'publish' ) {
			// Create the page
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $page['name'],
				'post_title'     => $page['title'],
				'post_content'   => $page['content'],
				'comment_status' => 'closed'
			);
			
			$page_id = wp_insert_post( $page_data );
			
			if ( $page_id ) {
				update_option( 'woocommerce_' . $key . '_page_id', $page_id );
			}
		}
	}
}
add_action( 'after_switch_theme', 'vibe_woo_create_pages' );

// Add admin notice with button to create pages manually
add_action( 'admin_notices', 'vibe_woo_admin_notice_missing_pages' );
function vibe_woo_admin_notice_missing_pages() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	
	// Check if cart or checkout page is missing
	$cart_page_id = get_option( 'woocommerce_cart_page_id' );
	$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
	
	if ( ! $cart_page_id || get_post_status( $cart_page_id ) !== 'publish' || 
	     ! $checkout_page_id || get_post_status( $checkout_page_id ) !== 'publish' ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong>WooCommerce Pages Missing!</strong> Click the button below to create Cart and Checkout pages.</p>
			<p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=vibe-create-wc-pages' ) ); ?>" class="button button-primary">Create WooCommerce Pages</a>
			</p>
		</div>
		<?php
	}
}

// Add admin page to create WooCommerce pages
add_action( 'admin_menu', 'vibe_woo_add_admin_page' );
function vibe_woo_add_admin_page() {
	add_submenu_page(
		null,
		'Create WooCommerce Pages',
		'Create WooCommerce Pages',
		'manage_options',
		'vibe-create-wc-pages',
		'vibe_woo_create_pages_callback'
	);
}

function vibe_woo_create_pages_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	vibe_woo_create_pages();
	flush_rewrite_rules();
	
	wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=page_setup' ) );
	exit;
}

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

            // Handle mini-cart remove button AJAX
            jQuery(document.body).on('click', '.remove_from_cart_button', function(e) {
                e.preventDefault();
                
                const $removeLink = jQuery(this);
                const cartItemKey = $removeLink.data('cart_item_key');
                const nonce = jQuery('input[name="woocommerce-cart-nonce"]').val();
                
                $removeLink.css('opacity', '0.5');
                
                jQuery.ajax({
                    type: 'POST',
                    url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_from_cart'),
                    data: {
                        'cart_item_key': cartItemKey,
                        'security': nonce
                    },
                    success: function(response) {
                        if (response.fragments) {
                            // Update mini-cart
                            jQuery('#vibe-mini-cart-contents').replaceWith(response.fragments['#vibe-mini-cart-contents']);
                            
                            // Update cart count
                            jQuery('.js-vibe-cart-count').replaceWith(response.fragments['.js-vibe-cart-count']);
                            
                            // Trigger WooCommerce event
                            jQuery(document.body).trigger('removed_from_cart', [response.fragments, response.cart_hash]);
                        }
                    },
                    error: function() {
                        $removeLink.css('opacity', '1');
                    }
                });
            });

            // Initialize WooCommerce variations on page load
            jQuery(document).ready(function($) {
                // Only initialize if variations form exists
                if ($('form.variations_form').length) {
                    $('form.variations_form').wc_variation_form();
                    
                    // Convert select dropdowns to radio buttons
                    $('.variations select').each(function() {
                        const $select = $(this);
                        const $row = $select.closest('tr');
                        const attributeName = $select.attr('name');
                        const $valueCell = $row.find('td.value');
                        
                        if (!$valueCell.find('input[type="radio"]').length) {
                            // Create radio buttons container
                            const $radioContainer = $('<div class="radio-swatches"></div>');
                            
                            // Get all options except the placeholder
                            $select.find('option').each(function() {
                                const $option = $(this);
                                const value = $option.val();
                                const text = $option.text();
                                
                                if (value) { // Skip empty placeholder option
                                    const radioId = attributeName + '_' + value.replace(/[^a-zA-Z0-9]/g, '_');
                                    const $radio = $('<input type="radio" name="' + attributeName + '" value="' + value + '" id="' + radioId + '">');
                                    const $label = $('<label for="' + radioId + '">' + text + '</label>');
                                    
                                    // Check if this option is selected
                                    if ($option.is(':selected')) {
                                        $radio.prop('checked', true);
                                    }
                                    
                                    $radioContainer.append($radio).append($label);
                                }
                            });
                            
                            // Insert radio buttons before select
                            $select.before($radioContainer);
                            
                            // Handle radio button changes
                            $radioContainer.find('input[type="radio"]').on('change', function() {
                                const selectedValue = $(this).val();
                                $select.val(selectedValue).trigger('change');
                            });
                            
                            // Handle select changes (from WooCommerce variation logic)
                            $select.on('change', function() {
                                const selectedValue = $(this).val();
                                $radioContainer.find('input[type="radio"][value="' + selectedValue + '"]').prop('checked', true);
                            });
                        }
                    });
                }
            });
        }

        // Buy Now functionality
        window.buyNow = function(productId) {
            if (typeof jQuery === 'undefined') {
                alert('jQuery is required');
                return;
            }

            const $ = jQuery;
            const form = $('.single-product form.cart, .single-product form.variations_form').first();
            
            if (!form.length) {
                alert('Product form not found');
                return;
            }

            // For variable products, check if variation is selected
            if (form.hasClass('variations_form')) {
                const variationId = form.find('input[name="variation_id"]').val();
                if (!variationId || variationId === '0' || variationId === 0) {
                    alert('Please select product options');
                    return;
                }
            }

            // Get form data
            const formData = new FormData(form[0]);
            formData.set('add-to-cart', productId);

            // Disable button to prevent double clicks
            const buyNowBtn = $('.single-product button[onclick*="buyNow"]');
            buyNowBtn.prop('disabled', true).text('ADDING...');

            // Add to cart via AJAX
            $.ajax({
                type: 'POST',
                url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response && !response.error && response.fragments) {
                        // Update cart fragments
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                        
                        // Redirect to checkout
                        window.location.href = wc_add_to_cart_params.cart_url.replace('cart', 'checkout');
                    } else {
                        alert('Could not add product to cart. Please try again.');
                        buyNowBtn.prop('disabled', false).text('BUY NOW');
                    }
                },
                error: function() {
                    alert('Error adding product to cart. Please try again.');
                    buyNowBtn.prop('disabled', false).text('BUY NOW');
                }
            });
        };
    });
    </script>
    <?php
}
