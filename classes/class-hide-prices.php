<?php
/**
 * Allows pricing to be set per quanity on a product.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices;

/**
 * Class Hide_Prices
 *
 * @since 1.0
 */
class Hide_Prices {

	/**
	 * Hooks.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'woocommerce_get_price_html', array( $this, 'override_price' ), 9999, 2 );
		add_action( 'woocommerce_get_variation_price_html', array( $this, 'override_price' ), 9999, 2 );
		add_action( 'woocommerce_is_purchasable', array( $this, 'remove_add_to_cart' ), 9999, 2 );
		add_filter( 'woocommerce_show_variation_price', '__return_true' );
		add_filter( 'woocommerce_variation_prices', array( $this, 'remove_prices_from_variation_range' ), 10, 1 );
	}

	/**
	 * Check if we should override the price for a specific product.
	 *
	 * @since 1.0
	 *
	 * @param string $price_label The current price label.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public function override_price( $price_label, $product ) {
		if ( is_admin() ) {
			return $price_label;
		}

		$hide_price = get_post_meta( $product->get_id(), Settings::HIDE_PRICE, true );

		// Check if this is a variable product.
		if ( $product->is_type( 'variation' ) ) {
			$parent_id         = wp_get_post_parent_id( $product->get_id() );
			$parent_hide_price = get_post_meta( $parent_id, Settings::HIDE_PRICE, true );

			// If the parent product option is enabled, hide the price.
			if ( ! empty( $parent_hide_price ) ) {
				$hide_price = $parent_hide_price;
			}
		}

		if ( ! empty( $hide_price ) ) {
			// Hide the price.
			return '';
		}

		return $price_label;
	}

	/**
	 * Remove the add to cart button if enabled.
	 *
	 * @since 1.0
	 *
	 * @param bool   $is_purchasable Can the product be purchased.
	 * @param object $product The product object.
	 */
	public function remove_add_to_cart( $is_purchasable, $product ) {
		$hide_cart = get_post_meta( $product->get_id(), Settings::HIDE_ADD_TO_CART, true );

		// Check if this is a variable product.
		if ( $product->is_type( 'variation' ) ) {
			$parent_hide_cart = get_post_meta( wp_get_post_parent_id( $product->get_id() ), Settings::HIDE_ADD_TO_CART, true );

			// If the parent product option is enabled, hide the add to cart button.
			if ( ! empty( $parent_hide_cart ) ) {
				$hide_cart = $parent_hide_cart;
			}
		}

		if ( ! empty( $hide_cart ) ) {
			$is_purchasable = false;
		}

		return $is_purchasable;
	}

	/**
	 * If a variation product is being hidden, we should remove it from the
	 * price range so that it's not accidently shown on the front end.
	 *
	 * @since 1.0
	 *
	 * @param array $prices An array of variation prices.
	 */
	public function remove_prices_from_variation_range( $prices ) {
		if ( is_admin() ) {
			return $prices;
		}

		if ( ! empty( $prices['price'] ) && is_array( $prices['price'] ) ) {
			foreach ( $prices['price'] as $product_id => $product_prices ) {
				$hide_price = get_post_meta( $product_id, Settings::HIDE_PRICE, true );
				$product    = wc_get_product( $product_id );

				// Check if this is a variable product.
				if ( $product->is_type( 'variation' ) ) {
					$parent_hide_price = get_post_meta( wp_get_post_parent_id( $product->get_id() ), Settings::HIDE_PRICE, true );

					// If the parent product option is enabled, remove the price from the range.
					if ( ! empty( $parent_hide_price ) ) {
						$hide_price = $parent_hide_price;
					}
				}

				if ( ! empty( $hide_price ) ) {
					unset( $prices['price'][ $product_id ] );
					unset( $prices['regular_price'][ $product_id ] );
					unset( $prices['sale_price'][ $product_id ] );
				}
			}
		}

		return $prices;
	}
}
