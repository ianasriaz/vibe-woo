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
<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'space-y-12', $product ); ?>>
    <div class="grid gap-12 lg:grid-cols-[1.2fr_1fr]">
        <div class="space-y-6">
            <div class="relative border-4 border-black bg-gray-50 overflow-hidden">
                <?php if ( $product->is_on_sale() ) : ?>
                    <span class="absolute left-4 top-4 z-10 bg-accent text-white text-xs font-black uppercase tracking-[0.24em] px-3 py-1">Sale</span>
                <?php endif; ?>
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
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                        <a href="<?php echo esc_url( wp_get_attachment_url( $attachment_id ) ); ?>" class="block border-2 border-black/30 hover:border-black transition-colors">
                            <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail', false, array( 'class' => 'w-full h-full object-cover' ) ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="space-y-3">
                <p class="text-xs font-black uppercase tracking-[0.28em] text-accent">New Drop</p>
                <h1 class="text-4xl md:text-5xl font-black uppercase leading-tight tracking-tight"><?php the_title(); ?></h1>
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                    <?php echo wc_get_stock_html( $product ); ?>
                    <?php if ( $product->get_sku() ) : ?>
                        <span class="uppercase tracking-[0.16em] font-semibold text-gray-500">SKU: <?php echo esc_html( $product->get_sku() ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="text-3xl font-black text-gray-900">
                    <?php woocommerce_template_single_price(); ?>
                </div>
                <?php if ( $rating_html ) : ?>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <?php echo $rating_html; ?>
                        <span class="uppercase tracking-[0.12em] font-semibold text-gray-500"><?php echo esc_html( $product->get_rating_count() ); ?> reviews</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                <?php echo apply_filters( 'the_content', $product->get_short_description() ); ?>
            </div>

            <div class="space-y-4 border-y-2 border-black py-6">
                <?php woocommerce_template_single_add_to_cart(); ?>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.16em] text-gray-600">
                    <span class="px-3 py-1 bg-gray-100 border border-black">Categories: <?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), ', ' ) ); ?></span>
                    <?php $tags = wc_get_product_tag_list( $product->get_id(), ', ' ); ?>
                    <?php if ( $tags ) : ?>
                        <span class="px-3 py-1 bg-gray-100 border border-black">Tags: <?php echo wp_kses_post( $tags ); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-6">
                <?php woocommerce_output_product_data_tabs(); ?>
            </div>
        </div>
    </div>

    <div class="space-y-12 pt-12 border-t-4 border-black">
        <?php woocommerce_upsell_display( 4, 4 ); ?>
        <?php woocommerce_output_related_products(); ?>
    </div>
</article>

<?php do_action( 'woocommerce_after_single_product' ); ?>
