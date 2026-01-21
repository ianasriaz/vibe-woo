<?php
/**
 * Mini-cart
 *
 * Custom template matching reference design
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item flex gap-4 p-5 border-b border-gray-100', $cart_item, $cart_item_key ) ); ?>">
					<?php if ( empty( $product_permalink ) ) : ?>
						<div class="flex-shrink-0 w-20 h-20 border border-gray-200">
							<?php echo $thumbnail; ?>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( $product_permalink ); ?>" class="flex-shrink-0 w-20 h-20 border border-gray-200">
							<?php echo $thumbnail; ?>
						</a>
					<?php endif; ?>

					<div class="flex-1 min-w-0">
						<div class="flex items-start justify-between gap-2 mb-2">
							<div class="flex-1">
								<?php if ( empty( $product_permalink ) ) : ?>
									<h3 class="text-sm font-semibold text-gray-900 leading-tight"><?php echo wp_kses_post( $product_name ); ?></h3>
								<?php else : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>" class="text-sm font-semibold text-gray-900 hover:text-gray-700 leading-tight block">
										<?php echo wp_kses_post( $product_name ); ?>
									</a>
								<?php endif; ?>
								<div class="text-sm font-bold text-gray-900 mt-1"><?php echo $product_price; ?></div>
							</div>
						</div>

						<?php
						$variation_data = wc_get_formatted_variation( $cart_item['variation'], true );
						if ( $variation_data ) {
							echo '<div class="text-xs text-gray-600 uppercase mb-2">' . wp_kses_post( $variation_data ) . '</div>';
						}
						?>

						<div class="flex items-center justify-between gap-3 mt-3">
							<div class="flex items-center border border-gray-300">
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
											'classes'      => array( 'input-text', 'qty', 'text', 'vibe-mini-cart-qty' ),
										),
										$_product,
										false
									);

									echo apply_filters( 'woocommerce_widget_cart_item_quantity', $product_quantity, $cart_item, $cart_item_key );
								}
								?>
							</div>
							<?php
							echo apply_filters(
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="text-sm underline text-gray-700 hover:text-gray-900 remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">Remove</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									esc_attr__( 'Remove this item', 'woocommerce' ),
									esc_attr( $product_id ),
									esc_attr( $cart_item_key ),
									esc_attr( $_product->get_sku() )
								),
								$cart_item_key
							);
							?>
						</div>
					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<div class="px-5 py-4 border-b border-gray-200">
		<label for="order-note" class="block text-sm font-semibold text-gray-900 mb-2">Add order note</label>
		<textarea id="order-note" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 focus:outline-none focus:border-gray-400" placeholder="How can we help you?"></textarea>
	</div>

	<div class="woocommerce-mini-cart__buttons p-5 space-y-3 bg-white">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="block w-full text-center bg-black text-white px-6 py-3.5 font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-colors">View Cart</a>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="block w-full text-center bg-black text-white px-6 py-3.5 font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-colors">Checkout</a>
	</div>

<?php else : ?>

	<div class="p-8 text-center">
		<p class="woocommerce-mini-cart__empty-message text-gray-600"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
