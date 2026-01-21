<?php get_header(); ?>

<main id="primary" class="site-main py-20 px-6">
    <div class="container mx-auto">
        <?php
        if ( is_singular( 'product' ) ) {
            wc_get_template_part( 'content', 'single-product' );
        } else {
            wc_get_template( 'archive-product.php' );
        }
        ?>
    </div>
</main>

<?php get_footer(); ?>
