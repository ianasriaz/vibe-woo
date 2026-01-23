<?php

get_header();

?>

<main id="primary" class="site-main py-12 md:py-20 px-4 md:px-6">
	<div class="container mx-auto max-w-7xl">
		<?php
		do_action( 'woocommerce_before_main_content' );
		
		// Let WooCommerce render its content
		woocommerce_content();
		
		do_action( 'woocommerce_after_main_content' );
		?>
	</div>
</main>

<?php
get_footer();

