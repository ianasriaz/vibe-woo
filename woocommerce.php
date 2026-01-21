<?php

get_header( 'shop' );

?>

<main id="primary" class="site-main py-12 md:py-20 px-4 md:px-6">
	<div class="container mx-auto max-w-7xl">
		<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs)
		 * @hooked woocommerce_breadcrumb - 20
		 * @hooked WC_Structured_Data::generate_website_schema() - 30
		 */
		do_action( 'woocommerce_before_main_content' );
		?>

		<?php
		if ( is_singular( 'product' ) ) {
			wc_get_template_part( 'content', 'single-product' );
		} elseif ( is_cart() ) {
			woocommerce_content();
		} elseif ( is_checkout() ) {
			woocommerce_content();
		} elseif ( is_account_page() ) {
			woocommerce_content();
		} else {
			wc_get_template( 'archive-product.php' );
		}
		?>

		<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs)
		 */
		do_action( 'woocommerce_after_main_content' );
		?>
	</div>
</main>

<?php
get_footer( 'shop' );

