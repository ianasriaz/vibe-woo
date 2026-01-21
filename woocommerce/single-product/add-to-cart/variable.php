<?php
/**
 * Variable product add to cart
 *
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $product->get_available_variations() );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php else : ?>
		<div class="variations space-y-6 border-t border-b border-gray-200 py-6">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<div class="attribute-group">
					<label class="text-xs font-bold uppercase tracking-widest block mb-3">
						<?php echo wc_attribute_label( $attribute_name ); ?>
					</label>
					<div class="flex flex-wrap gap-2">
						<?php
						foreach ( $options as $option ) :
							$checked = sanitize_title( isset( $_POST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? sanitize_post_data( wp_unslash( $_POST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : $product->get_variation_default_attribute( $attribute_name ) );
							$id       = 'attribute_' . sanitize_title( $attribute_name ) . '_' . sanitize_title( $option );
							?>
							<input 
								type="radio" 
								name="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" 
								value="<?php echo esc_attr( $option ); ?>" 
								id="<?php echo esc_attr( $id ); ?>"
								class="hidden peer/attr"
								<?php checked( $checked, sanitize_title( $option ) ); ?>
							/>
							<label 
								for="<?php echo esc_attr( $id ); ?>"
								class="px-4 py-2 border-2 border-gray-300 text-sm font-bold uppercase tracking-wide cursor-pointer transition-all peer-checked/attr:border-black peer-checked/attr:bg-black peer-checked/attr:text-white"
							>
								<?php echo esc_html( $option ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>

		<div class="flex gap-4 items-center mb-6 mt-6">
			<span class="text-xs font-bold uppercase tracking-widest">QTY:</span>
			<?php
			woocommerce_quantity_input(
				array(
					'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
					'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
					'input_value' => isset( $_POST['quantity'] ) ? wc_sanitize_quantity( sanitize_post_data( wp_unslash( $_POST['quantity'] ) ) ) : $product->get_min_purchase_quantity(),
				)
			);
			?>
		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="w-full py-3 bg-black text-white font-bold uppercase tracking-widest text-sm transition-all hover:bg-gray-900 mb-3 single_add_to_cart_button button">
			<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
		</button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
