<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        vibe: '#000000',
                        accent: '#FF3E00',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-white text-black antialiased'); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site min-h-screen flex flex-col">
    <!-- Top Announcement Bar -->
    <div class="bg-black text-white py-3 px-4 text-center relative">
        <button type="button" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 text-white opacity-70 hover:opacity-100 p-2" aria-label="Previous announcement" data-announcement-prev>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="mx-10 md:mx-14">
            <p class="text-xs md:text-sm font-semibold tracking-wide" data-announcement-text>Free shipping on order above Rs.5000</p>
        </div>
        <button type="button" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 text-white opacity-70 hover:opacity-100 p-2" aria-label="Next announcement" data-announcement-next>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- Main Header -->
    <header id="masthead" class="site-header bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between h-20 md:h-24">
                <!-- Logo (Left on Desktop, Left on Mobile) -->
                <div class="flex-shrink-0">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                        <img src="https://beta.ktfmultan.com/wp-content/uploads/2026/01/KHAWAJA-TEX-LOGO-PNG.webp" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="h-10 md:h-12 w-auto" loading="lazy" />
                    </a>
                </div>

                <!-- Desktop Navigation (Center) -->
                <nav class="hidden lg:flex items-center justify-center flex-1">
                    <ul class="flex items-center gap-8 xl:gap-12">
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-sm font-semibold uppercase tracking-wider hover:text-gray-600 transition-colors">Home</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/new-arrivals/' ) ); ?>" class="text-sm font-semibold uppercase tracking-wider hover:text-gray-600 transition-colors">New Arrivals</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/product-category/bedsheets/' ) ); ?>" class="text-sm font-semibold uppercase tracking-wider hover:text-gray-600 transition-colors">Bedsheets</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/product-category/towels/' ) ); ?>" class="text-sm font-semibold uppercase tracking-wider hover:text-gray-600 transition-colors">Towels</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/product-category/khes/' ) ); ?>" class="text-sm font-semibold uppercase tracking-wider hover:text-gray-600 transition-colors">Khes</a></li>
                    </ul>
                </nav>

                <!-- Right Icons (Desktop & Mobile) -->
                <div class="flex items-center gap-4 md:gap-6">
                    <!-- Search Icon -->
                    <button type="button" class="p-2 hover:bg-gray-100 rounded-full transition-colors" aria-label="Search" data-search-toggle>
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    <!-- Cart Icon -->
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <button type="button" class="relative p-2 hover:bg-gray-100 rounded-full transition-colors" aria-expanded="false" aria-controls="vibe-cart-drawer" data-cart-toggle>
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="js-vibe-cart-count absolute -top-1 -right-1 flex items-center justify-center min-w-[1.25rem] h-5 px-1 bg-black text-white text-xs font-bold rounded-full">
                            <?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?>
                        </span>
                        <span class="sr-only">Open cart</span>
                    </button>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggle -->
                    <button type="button" class="lg:hidden p-2 hover:bg-gray-100 rounded-full transition-colors" aria-label="Toggle navigation" aria-expanded="false" data-nav-toggle>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Panel -->
        <div class="lg:hidden border-t border-gray-200 hidden" data-mobile-nav>
            <nav class="container mx-auto px-4 py-6">
                <ul class="flex flex-col gap-4">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block text-sm font-semibold uppercase tracking-wider py-2 hover:text-gray-600 transition-colors">Home</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/new-arrivals/' ) ); ?>" class="block text-sm font-semibold uppercase tracking-wider py-2 hover:text-gray-600 transition-colors">New Arrivals</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/product-category/bedsheets/' ) ); ?>" class="block text-sm font-semibold uppercase tracking-wider py-2 hover:text-gray-600 transition-colors">Bedsheets</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/product-category/towels/' ) ); ?>" class="block text-sm font-semibold uppercase tracking-wider py-2 hover:text-gray-600 transition-colors">Towels</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/product-category/khes/' ) ); ?>" class="block text-sm font-semibold uppercase tracking-wider py-2 hover:text-gray-600 transition-colors">Khes</a></li>
                </ul>
            </nav>
        </div>

        <!-- Search Modal -->
        <div class="fixed inset-0 bg-black/50 z-50 hidden" data-search-modal>
            <div class="bg-white w-full max-w-3xl mx-auto mt-20 p-8 rounded-lg shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold uppercase tracking-wider">Search Products</h2>
                    <button type="button" class="p-2 hover:bg-gray-100 rounded-full" aria-label="Close search" data-search-close>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="relative">
                        <input type="search" id="woocommerce-product-search-field" class="w-full px-6 py-4 border-2 border-black text-lg focus:outline-none focus:border-gray-600" placeholder="Search for products..." value="<?php echo get_search_query(); ?>" name="s" />
                        <input type="hidden" name="post_type" value="product" />
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 px-6 py-2 bg-black text-white font-bold uppercase tracking-wider hover:bg-gray-800 transition-colors">Search</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <!-- Cart Overlay -->
    <div id="vibe-cart-overlay" class="hidden fixed inset-0 bg-black/40 z-40" data-cart-overlay></div>
    
    <!-- Cart Drawer -->
    <aside id="vibe-cart-drawer" class="fixed top-0 right-0 h-full w-full sm:w-[90vw] md:w-[28rem] bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h2 class="text-base font-bold uppercase tracking-wide">CART</h2>
            <button type="button" class="p-1 hover:bg-gray-100 rounded transition-colors" data-cart-close aria-label="Close cart">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="vibe-mini-cart-contents" class="flex-1 overflow-y-auto">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </aside>
    <?php endif; ?>
