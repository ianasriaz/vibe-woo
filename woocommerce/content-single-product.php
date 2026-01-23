<?php
/**
 * Single Product Template
 * Uses WooCommerce native hooks and templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_single_product' );
?>

<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product' ); ?>>
    <div class="grid gap-12 lg:grid-cols-2">
        <!-- Product Gallery -->
        <div>
            <?php
            /**
             * woocommerce_product_images hook.
             *
             * @hooked woocommerce_show_product_images - 20
             */
            do_action( 'woocommerce_product_images' );
            ?>
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <!-- Title -->
            <h1 class="text-3xl lg:text-4xl font-bold uppercase leading-tight tracking-tight">
                <?php the_title(); ?>
            </h1>

            <!-- Rating -->
            <?php
            /**
             * woocommerce_single_product_summary hook.
             *
             * @hooked woocommerce_template_single_rating - 10
             */
            do_action( 'woocommerce_single_product_summary' );
            ?>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="mt-20">
        <?php
        /**
         * woocommerce_after_single_product_summary hook.
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_upsell_display - 15
         * @hooked woocommerce_output_related_products - 20
         */
        do_action( 'woocommerce_after_single_product_summary' );
        ?>

                <!-- Size Chart Link -->
                <div class="text-right pt-2">
                    <a href="#size-chart" class="text-xs font-semibold uppercase tracking-wide hover:opacity-60 transition-opacity">Size chart →</a>
                </div>
            </div>

            <!-- Meta Info -->
            <div class="space-y-2 text-xs text-gray-600 uppercase tracking-wide font-semibold">
                <?php if ( $product->get_sku() ) : ?>
                    <div>SKU: <span class="font-normal"><?php echo esc_html( $product->get_sku() ); ?></span></div>
                <?php endif; ?>
                <?php echo wc_get_stock_html( $product ); ?>
                <?php
                $categories = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
                if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
                    ?>
                    <div>CATEGORIES: <span class="font-normal"><?php echo esc_html( implode( ', ', $categories ) ); ?></span></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-20 border-t-2 border-black pt-12">
        <?php woocommerce_output_product_data_tabs(); ?>
    </div>

    <!-- Related & Upsells -->
    <div class="mt-20">
        <?php woocommerce_upsell_display( 4, 4 ); ?>
    </div>
    <div class="mt-12">
        <?php woocommerce_output_related_products(); ?>
    </div>
</article>

<?php do_action( 'woocommerce_after_single_product' ); ?>

