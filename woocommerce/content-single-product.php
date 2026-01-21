<?php
/**
 * Custom single product layout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

// Ensure product context exists to avoid fatal errors when Woo globals are missing
if ( empty( $product ) || ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
    return;
}

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}

$gallery_ids       = $product->get_gallery_image_ids();
$primary_image_id  = $product->get_image_id();
$rating_html       = wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() );
?>
<article id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
    <div class="grid gap-12 lg:grid-cols-2">
        <!-- Product Images -->
        <div class="space-y-6">
            <div class="bg-gray-50 overflow-hidden">
                <div class="aspect-square flex items-center justify-center">
                    <?php
                    if ( $primary_image_id ) {
                        echo wp_get_attachment_image( $primary_image_id, 'large', false, array( 'class' => 'w-full h-full object-cover' ) );
                    } else {
                        echo wc_placeholder_img( 'full' );
                    }
                    ?>
                </div>
            </div>

            <?php if ( ! empty( $gallery_ids ) ) : ?>
                <div class="grid grid-cols-4 gap-3">
                    <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                        <a href="<?php echo esc_url( wp_get_attachment_url( $attachment_id ) ); ?>" class="block border border-gray-200 hover:border-gray-400 transition-colors">
                            <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail', false, array( 'class' => 'w-full h-full object-cover' ) ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <!-- Rating -->
            <?php if ( $rating_html ) : ?>
                <div class="flex items-center gap-2 text-sm">
                    <?php echo $rating_html; ?>
                    <span class="text-gray-600">(<?php echo esc_html( $product->get_rating_count() ); ?>)</span>
                </div>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="text-3xl lg:text-4xl font-bold uppercase leading-tight tracking-tight">
                <?php the_title(); ?>
            </h1>

            <!-- Price -->
            <div class="text-2xl font-bold">
                <?php woocommerce_template_single_price(); ?>
            </div>

            <!-- Description -->
            <?php if ( $product->get_short_description() ) : ?>
                <div class="text-gray-600 text-sm leading-relaxed">
                    <?php echo wp_kses_post( $product->get_short_description() ); ?>
                </div>
            <?php endif; ?>

            <!-- Variations/Attributes -->
            <?php if ( $product->is_type( 'variable' ) ) : ?>
                <div class="space-y-6 border-t border-b border-gray-200 py-6">
                    <?php
                    $attributes = $product->get_variation_attributes();
                    foreach ( $attributes as $attribute_name => $options ) {
                        $attribute_label = wc_attribute_label( $attribute_name );
                        ?>
                        <div class="space-y-3">
                            <label class="text-xs font-bold uppercase tracking-widest"><?php echo esc_html( $attribute_label ); ?></label>
                            <div class="flex flex-wrap gap-3">
                                <?php foreach ( $options as $option ) : ?>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" value="<?php echo esc_attr( $option ); ?>" class="swatch-input" />
                                        <span class="swatch-label">
                                            <?php echo esc_html( $option ); ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Quantity & Add to Cart -->
            <div class="space-y-4 pt-6">
                <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
                    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

                    <div class="flex gap-3 items-center mb-4">
                        <label for="qty-<?php echo esc_attr( $product->get_id() ); ?>" class="text-xs font-bold uppercase tracking-widest whitespace-nowrap">Qty:</label>
                        <input type="number" id="qty-<?php echo esc_attr( $product->get_id() ); ?>" name="quantity" value="1" min="1" max="<?php echo esc_attr( $product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : '' ); ?>" class="qty-input" />
                    </div>

                    <?php if ( ! $product->is_type( 'variable' ) ) : ?>
                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" />
                    <?php endif; ?>

                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single-add-to-cart-btn">
                        ADD TO CART
                    </button>

                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                </form>

                <!-- Buy Now Button -->
                <button type="button" class="buy-now-btn" onclick="buyNow(<?php echo esc_attr( $product->get_id() ); ?>)">
                    BUY NOW
                </button>

                <!-- Size Chart Link -->
                <div class="text-right">
                    <a href="#size-chart" class="text-xs font-semibold uppercase tracking-wide hover:text-gray-600 transition-colors">Size chart →</a>
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

