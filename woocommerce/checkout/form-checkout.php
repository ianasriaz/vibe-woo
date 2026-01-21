<?php
/**
 * Checkout Form
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<div class="max-w-7xl mx-auto px-4 md:px-6 py-12">
	<h1 class="text-4xl font-bold uppercase mb-8">Checkout</h1>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="grid gap-8 lg:grid-cols-[1fr_400px]" id="customer_details">
				<div class="checkout-billing-shipping">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>

				<div class="checkout-order-review">
					<div class="border-2 border-black p-6">
						<h3 class="text-xl font-bold uppercase mb-6">Your Order</h3>
						<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
						<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

						<div id="order_review" class="woocommerce-checkout-review-order">
							<?php do_action( 'woocommerce_checkout_order_review' ); ?>
						</div>

						<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
					</div>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

	</form>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>
