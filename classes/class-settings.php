<?php
/**
 * Settings for the plugin.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices;

/**
 * Class Settings
 *
 * @since 1.0
 */
class Settings {

	/**
	 * Option name for hiding the price.
	 *
	 * @since 1.0
	 */
	const HIDE_PRICE = 'hide_prices_hide_price';

	/**
	 * Option name for hiding add to cart.
	 *
	 * @since 1.0
	 */
	const HIDE_ADD_TO_CART = 'hide_prices_hide_add_to_cart';

	/**
	 * Hooks.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'render_settings' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_simple_settings' ) );
	}

	/**
	 * Render the settings for the simple product tab.
	 *
	 * @since 1.0
	 */
	public function render_settings() {
		echo wp_kses_post( '<div class="options_group">' );

		$this->render_setting_fields();

		echo wp_kses_post( '</div>' );
	}

	/**
	 * Render the actual settings fields for all tabs.
	 *
	 * @since 1.0
	 *
	 * @param bool $simple Display for simple product or variation (false).
	 * @param int  $id The ID of the setting.
	 */
	public function render_setting_fields( $simple = true, $id = null ) {
		if ( empty( $id ) ) {
			$id = get_the_ID();
		}

		woocommerce_wp_checkbox(
			array(
				'id'            => self::HIDE_PRICE,
				'name'          => self::HIDE_PRICE,
				'value'         => get_post_meta( $id, self::HIDE_PRICE, true ),
				'wrapper_class' => 'options',
				'class'         => sprintf( '%s_form_field', self::HIDE_PRICE ),
				'label'         => __( 'Hide the price', 'hide-prices' ),
				'desc_tip'      => $simple ? false : true,
				'description'   => __( 'Enabling this option will hide the price from the product page', 'hide-prices' ),
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'            => self::HIDE_ADD_TO_CART,
				'name'          => self::HIDE_ADD_TO_CART,
				'value'         => get_post_meta( $id, self::HIDE_ADD_TO_CART, true ),
				'wrapper_class' => 'options',
				'class'         => sprintf( '%s_form_field', self::HIDE_ADD_TO_CART ),
				'label'         => $simple ? __( 'Hide add to cart button', 'hide-prices' ) : __( 'Disable add to cart button', 'hide-prices' ),
				'desc_tip'      => $simple ? false : true,
				'description'   => $simple ?
					__( 'Enabling this option will hide the add to cart button from the product page', 'hide-prices' ) :
					__( 'Enabling this option will disable the add to cart button on the product page', 'hide-prices' ),
			)
		);
	}

	/**
	 * Save the settings for a simple product.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id The WooCommerce product ID.
	 */
	public function save_simple_settings( $product_id ) {
		$this->save_settings( $product_id );
	}

	/**
	 * Save the settings.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id The WooCommerce product ID.
	 * @param int $index The form index for the variation.
	 */
	public function save_settings( $product_id, $index = null ) {
		$this->update_value( $product_id, self::HIDE_PRICE, $index, 'string' );
		$this->update_value( $product_id, self::HIDE_ADD_TO_CART, $index, 'string' );
	}

	/**
	 * Update a post meta value. This method allows us to update a value
	 * if it's set on a simple product or variable product (which uses an
	 * index to pass through the form field value). It also passes on
	 * sanitization based on type.
	 *
	 * @since 1.0
	 *
	 * @param int    $product_id The WooCommerce product ID.
	 * @param string $key The form field name (usually the setting ID).
	 * @param int    $form_index For variable products, the index value of the product being saved.
	 * @param string $type The data type for handling sanitization.
	 */
	public function update_value( $product_id, $key, $form_index = null, $type = 'string' ) {
		// Check for null as the form index can be 0 and empty won't work.
		if ( null === $form_index && ! empty( $_POST[ $key ] ) ) { // @phpcs:ignore
			update_post_meta( $product_id, $key, $this->get_and_sanitize_field( $key, $type ) );
		} elseif ( $form_index >= 0 && ! empty( $_POST[ $key ][ $form_index ] ) ) { // @phpcs:ignore
			update_post_meta( $product_id, $key, $this->get_and_sanitize_field( array( $key, $form_index ), $type ) );
		} else {
			delete_post_meta( $product_id, $key );
		}
	}

	/**
	 * Get a field value and santizes it based on type. Also adds the ability
	 * to get and santize a field with an index.
	 *
	 * @since 1.0
	 *
	 * @param string $key The key to sanitze.
	 * @param string $type The type of data we're dealing with.
	 */
	public function get_and_sanitize_field( $key, $type = 'string' ) {
		if ( is_array( $key ) ) {
			$index = $key[1];
			$key   = $key[0];
		}

		if ( empty( $_POST[ $key ][ $index ] ) ) { // @phpcs:ignore
			return '';
		}

		switch ( $type ) {
			case 'url':
				return null === $index ? esc_url_raw( wp_unslash( $_POST[ $key ] ) ) : esc_url_raw( wp_unslash( $_POST[ $key ][ $index ] ) ); // @phpcs:ignore
			default:
				return null === $index ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : sanitize_text_field( wp_unslash( $_POST[ $key ][ $index ] ) ); // @phpcs:ignore
		}
	}
}
