<?php
/**
 * Custom product card for loops
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$rating_html = wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() );
?>
<article <?php wc_product_class( 'group relative flex flex-col gap-4 border-4 border-black p-5 bg-white shadow-[10px_10px_0_#000] transition-transform duration-300 hover:-translate-y-2', $product ); ?>>
    <a href="<?php the_permalink(); ?>" class="block overflow-hidden border-4 border-black">
        <div class="relative aspect-square bg-gray-50 overflow-hidden">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="absolute left-3 top-3 z-10 bg-accent text-white text-xs font-black uppercase tracking-[0.24em] px-3 py-1">Sale</span>
            <?php endif; ?>
            <?php woocommerce_template_loop_product_thumbnail(); ?>
        </div>
    </a>

    <div class="flex-1 space-y-2">
        <a href="<?php the_permalink(); ?>" class="block space-y-2">
            <h2 class="text-xl font-black uppercase leading-none tracking-tight group-hover:text-accent transition-colors"><?php the_title(); ?></h2>
            <?php if ( $rating_html ) : ?>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <?php echo $rating_html; ?>
                    <span class="uppercase tracking-[0.12em] font-semibold text-gray-500"><?php echo esc_html( $product->get_rating_count() ); ?> reviews</span>
                </div>
            <?php endif; ?>
            <div class="text-lg font-black tracking-tight text-gray-900">
                <?php woocommerce_template_loop_price(); ?>
            </div>
        </a>
    </div>

    <div class="flex items-center justify-between pt-3">
        <a class="text-sm font-bold uppercase tracking-[0.2em] underline decoration-2 underline-offset-4 hover:text-accent" href="<?php the_permalink(); ?>">View Details</a>
        <div class="flex items-center gap-2">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
    </div>
</article>
