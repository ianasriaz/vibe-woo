<?php
/**
 * Cart Page
 *
 * @package Vibe_Woo
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="woocommerce-cart">
	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<div class="overflow-x-auto">
			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents w-full border-collapse" cellspacing="0">
				<thead class="bg-gray-50 border-b-2 border-black">
					<tr>
						<th class="product-remove px-4 py-4 text-left font-bold uppercase text-sm tracking-wider">&nbsp;</th>
						<th class="product-thumbnail px-4 py-4 text-left font-bold uppercase text-sm tracking-wider">&nbsp;</th>
						<th class="product-name px-4 py-4 text-left font-bold uppercase text-sm tracking-wider">Product</th>
						<th class="product-price px-4 py-4 text-left font-bold uppercase text-sm tracking-wider">Price</th>
						<th class="product-quantity px-4 py-4 text-left font-bold uppercase text-sm tracking-wider">Quantity</th>
						<th class="product-subtotal px-4 py-4 text-left font-bold uppercase text-sm tracking-wider">Subtotal</th>
					</tr>
				</thead>
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item border-b border-gray-200', $cart_item, $cart_item_key ) ); ?>">

								<td class="product-remove px-4 py-6">
									<?php
										echo apply_filters(
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove text-2xl text-gray-400 hover:text-red-600" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_html__( 'Remove this item', 'woocommerce' ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() )
											),
											$cart_item_key
										);
									?>
								</td>

								<td class="product-thumbnail px-4 py-6">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 100, 100 ) ), $cart_item, $cart_item_key );

									if ( ! $product_permalink ) {
										echo $thumbnail;
									} else {
										printf( '<a href="%s" class="block w-24 h-24 border border-gray-200">%s</a>', esc_url( $product_permalink ), $thumbnail );
									}
									?>
								</td>

								<td class="product-name px-4 py-6" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', '<span class="font-semibold">' . $_product->get_name() . '</span>', $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', '<a href="' . esc_url( $product_permalink ) . '" class="font-semibold hover:text-gray-600">' . $_product->get_name() . '</a>', $cart_item, $cart_item_key ) );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

									echo wc_get_formatted_cart_item_data( $cart_item );

									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
									}
									?>
								</td>

								<td class="product-price px-4 py-6 font-bold" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
									?>
								</td>

								<td class="product-quantity px-4 py-6" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
									<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '0',
												'product_name' => $_product->get_name(),
											),
											$_product,
											false
										);
									}

									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
									?>
								</td>

								<td class="product-subtotal px-4 py-6 font-bold" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
									?>
								</td>
							</tr>
							<?php
						}
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>

					<tr>
						<td colspan="6" class="actions px-4 py-6 bg-gray-50">
							<div class="flex items-center justify-between flex-wrap gap-4">
								<button type="submit" class="button bg-black text-white px-6 py-3 font-bold uppercase text-sm tracking-wide hover:bg-gray-800" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

								<?php do_action( 'woocommerce_cart_actions' ); ?>

								<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
							</div>
						</td>
					</tr>

					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>
		</div>
		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>
</div>

<div class="cart-collaterals mt-12">
	<div class="cart_totals max-w-md ml-auto">
		<?php
			do_action( 'woocommerce_cart_collaterals' );
		?>
	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
