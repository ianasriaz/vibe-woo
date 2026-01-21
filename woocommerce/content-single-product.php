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
                <div class="space-y-4">
                    <?php
                    $attributes = $product->get_variation_attributes();
                    foreach ( $attributes as $attribute_name => $options ) {
                        $attribute_label = wc_attribute_label( $attribute_name );
                        ?>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold uppercase tracking-wide"><?php echo esc_html( $attribute_label ); ?></label>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ( $options as $option ) : ?>
                                    <label class="relative">
                                        <input type="radio" name="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" value="<?php echo esc_attr( $option ); ?>" class="sr-only peer" />
                                        <span class="block px-4 py-2 border-2 border-gray-300 cursor-pointer text-sm font-semibold uppercase tracking-wide peer-checked:border-black peer-checked:bg-black peer-checked:text-white transition-all">
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

            <!-- Add to Cart -->
            <div class="pt-6 border-t border-gray-200 space-y-4">
                <?php woocommerce_template_single_add_to_cart(); ?>

                <!-- WhatsApp & Size Chart -->
                <div class="flex items-center justify-between gap-4 text-sm">
                    <a href="https://wa.me/?text=Hi%20I%20am%20interested%20in%20<?php echo esc_attr( urlencode( $product->get_name() ) ); ?>" target="_blank" rel="noopener" class="flex items-center gap-2 px-4 py-3 border-2 border-green-500 text-green-600 font-semibold uppercase tracking-wide hover:bg-green-50 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-9.746 9.798c0 2.734.732 5.41 2.124 7.738L3.505 21.952l8.126-2.135a9.847 9.847 0 004.746 1.194h.004c5.411 0 9.746-4.335 9.746-9.746 0-2.605-.635-5.061-1.746-7.24A9.753 9.753 0 0011.051 6.979"/>
                        </svg>
                        WhatsApp
                    </a>
                    <a href="#size-chart" class="text-gray-700 hover:text-black font-semibold underline">Size chart</a>
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

