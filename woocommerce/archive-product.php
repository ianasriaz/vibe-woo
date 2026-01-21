<?php
/**
 * Custom Shop & Archive Template
 *
 * Bold Tailwind layout for WooCommerce archives.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<section class="space-y-12">
    <header class="space-y-6">
        <div class="flex flex-wrap items-center gap-3 text-xs font-bold uppercase tracking-[0.28em] text-gray-500">
            <?php woocommerce_breadcrumb( array(
                'wrap_before' => '<nav class="flex flex-wrap items-center gap-2">',
                'wrap_after'  => '</nav>',
                'delimiter'   => '<span class="text-gray-400">/</span>',
            ) ); ?>
        </div>
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div class="space-y-2">
                <p class="text-xs font-black uppercase tracking-[0.28em] text-accent">Shop Bold</p>
                <h1 class="text-4xl md:text-5xl font-black uppercase leading-none tracking-tight"><?php woocommerce_page_title(); ?></h1>
                <div class="max-w-3xl prose prose-sm text-gray-600">
                    <?php do_action( 'woocommerce_archive_description' ); ?>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm font-semibold uppercase tracking-[0.16em]">
                <div class="px-4 py-2 border-2 border-black">
                    <?php woocommerce_result_count(); ?>
                </div>
                <div class="min-w-[240px] border-2 border-black px-4 py-2 bg-gray-50">
                    <?php woocommerce_catalog_ordering(); ?>
                </div>
            </div>
        </div>
    </header>

    <?php if ( woocommerce_product_loop() ) : ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>
        </div>

        <div class="mt-12 flex items-center justify-between flex-wrap gap-4 text-sm font-semibold uppercase tracking-[0.16em]">
            <?php woocommerce_pagination(); ?>
        </div>
    <?php else : ?>
        <?php do_action( 'woocommerce_no_products_found' ); ?>
    <?php endif; ?>
</section>
