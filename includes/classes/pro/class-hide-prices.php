<?php
/**
 * Allows pricing to be set per quanity on a product.
 * 
 * @since 0.1
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices\Pro;

/**
 * Class Hide_Prices
 *
 * @since 0.1
 */
class Hide_Prices {

    /**
     * Option name for hiding the price.
     * 
     * @since 0.1
     */
    const HIDE_PRICE = 'wpgks_hp_hide_price';

    /**
     * Option name for hiding add to cart.
     * 
     * @since 0.1
     */
    const HIDE_ADD_TO_CART = 'wpgks_hp_hide_add_to_cart';

    /**
     * Option name for enabling the price replacement with a label or button.
     * 
     * @since 0.1
     */
    const REPLACE_PRICE = 'wpgks_hp_replace_price';

    /**
     * Option for what should we replace the price with.
     * 
     * @since 0.1
     */
    const REPLACEMENT_TYPE = 'wpgks_hp_replacement_type';

    /**
     * Option for replacement text.
     * 
     * @since 0.1
     */
    const REPLACEMENT_TEXT = 'wpgks_hp_replacement_text';

    /**
     * Option for replacement url.
     * 
     * @since 0.1
     */
    const REPLACEMENT_URL = 'wpgks_hp_replacement_url';

    /**
     * Hooks.
     * 
     * @since 0.1
     */
    public function hooks() {
        add_action( 'woocommerce_product_options_general_product_data', [ $this, 'render_settings' ] );
        add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'render_variable_settings' ], 10, 3 );
        add_action( 'woocommerce_process_product_meta', [ $this, 'save_settings' ] );
        add_action( 'woocommerce_get_price_html', [ $this, 'override_price' ], 9999, 2 );
        add_action( 'woocommerce_is_purchasable', [ $this, 'remove_add_to_cart' ], 9999, 2 );
    }

    /**
     * Render the options on the product data tab.
     * 
     * @since 0.1
     */
    public function render_settings() {
        echo wp_kses_post( '<div class="options_group show_if_simple">' );
 
        $this->render_setting_fields();
    
        echo wp_kses_post( '</div>' );
    }

    public function render_variable_settings( $loop, $variation_data, $variation ) {
        $this->render_setting_fields( false );
    }

    public function render_setting_fields( $simple = true ) {
        $classes             = $simple ? '' : 'form-row form-row-full';
        $classes_split_first = $simple ? '' : 'form-row form-row-first';
        $classes_split_last  = $simple ? '' : 'form-row form-row-last';
        
        woocommerce_wp_checkbox(
            [
                'id'            => self::HIDE_PRICE,
                'value'         => get_post_meta( get_the_ID(), self::HIDE_PRICE, true ),
                'wrapper_class' => $classes_split_first . ' options',
                'label'         => __( 'Hide the price', 'hide-prices' ),
                'desc_tip'      => true,
                'description'   => __( 'Enabling this option will hide the price from the product page', 'hide-prices' ),
            ]
        );

        woocommerce_wp_checkbox(
            [
                'id'            => self::HIDE_ADD_TO_CART,
                'value'         => get_post_meta( get_the_ID(), self::HIDE_ADD_TO_CART, true ),
                'wrapper_class' => $classes_split_last . ' options',
                'label'         => __( 'Hide add to cart button', 'hide-prices' ),
                'desc_tip'      => true,
                'description'   => __( 'Enabling this option will hide the add to cart button from the product page', 'hide-prices' ),
            ]
        );

        woocommerce_wp_checkbox(
            [
                'id'            => self::REPLACE_PRICE,
                'value'         => get_post_meta( get_the_ID(), self::REPLACE_PRICE, true ),
                'wrapper_class' => $classes_split_first . ' options',
                'label'         => __( 'Show custom price label', 'hide-prices' ),
                'desc_tip'      => true,
                'description'   => __( 'This will show a custom label or button if enabled', 'hide-prices' ),
            ]
        );

        woocommerce_wp_select(
            [
                'id'            => self::REPLACEMENT_TYPE,
                'value'         => get_post_meta( get_the_ID(), self::REPLACEMENT_TYPE, true ),
                'wrapper_class' => empty( get_post_meta( get_the_ID(), self::REPLACE_PRICE, true ) ) ? $classes_split_first . ' hidden' : $classes_split_first,
                'label'         => __( 'Replace price with', 'hide-price' ),
                'options'       => [
                    'label'  => __( 'Label', 'hide-price' ),
                    'button' => __( 'Button', 'hide-price' ),
                ],
            ]
        );

        woocommerce_wp_text_input(
            [
                'id'            => self::REPLACEMENT_TEXT,
                'value'         => get_post_meta( get_the_ID(), self::REPLACEMENT_TEXT, true ),
                'wrapper_class' => empty( get_post_meta( get_the_ID(), self::REPLACE_PRICE, true ) ) ? $classes_split_last . ' hidden' : $classes_split_last,
                'label'         => __( 'Label / button text', 'hide-price' ),
                'description'   => __( 'Text to show on the label or button replacing the price', 'hide-price' ),
            ]
        );

        woocommerce_wp_text_input(
            [
                'id'            => self::REPLACEMENT_URL,
                'value'         => get_post_meta( get_the_ID(), self::REPLACEMENT_URL, true ),
                'wrapper_class' => empty( get_post_meta( get_the_ID(), self::REPLACE_PRICE, true ) ) || 'button' !== get_post_meta( get_the_ID(), self::REPLACEMENT_TYPE, true ) ? $classes . ' hidden' : $classes,
                'label'         => __( 'Button URL', 'hide-price' ),
                'description'   => __( 'URL for the label or button', 'hide-price' ),
            ]
        );
    }

    /**
     * Save the settings to the database.
     * 
     * @since 0.1
     * 
     * @param int $product_id The WooCommerce product ID.
     */
    public function save_settings( $product_id ) {
        if ( ! empty( $_POST[ self::HIDE_PRICE ] ) ) {
            update_post_meta( $product_id, self::HIDE_PRICE, 'yes' );
        } else {
            delete_post_meta( $product_id, self::HIDE_PRICE );
        }

        if ( ! empty( $_POST[ self::HIDE_ADD_TO_CART ] ) ) {
            update_post_meta( $product_id, self::HIDE_ADD_TO_CART, 'yes' );
        } else {
            delete_post_meta( $product_id, self::HIDE_ADD_TO_CART );
        }

        if ( ! empty( $_POST[ self::REPLACE_PRICE ] ) ) {
            update_post_meta( $product_id, self::REPLACE_PRICE, 'yes' );
        } else {
            delete_post_meta( $product_id, self::REPLACE_PRICE );
        }

        if ( ! empty( $_POST[ self::REPLACEMENT_TYPE ] ) ) {
            update_post_meta( $product_id, self::REPLACEMENT_TYPE, sanitize_text_field( wp_unslash( $_POST[ self::REPLACEMENT_TYPE ] ) ) );
        } else {
            delete_post_meta( $product_id, self::REPLACEMENT_TYPE, false );
        }

        if ( ! empty( $_POST[ self::REPLACEMENT_TEXT ] ) ) {
            update_post_meta( $product_id, self::REPLACEMENT_TEXT, sanitize_text_field( wp_unslash( $_POST[ self::REPLACEMENT_TEXT ] ) ) );
        } else {
            delete_post_meta( $product_id, self::REPLACEMENT_TEXT, false );
        }

        if ( ! empty( $_POST[ self::REPLACEMENT_URL ] ) ) {
            update_post_meta( $product_id, self::REPLACEMENT_URL, esc_url_raw( wp_unslash( $_POST[ self::REPLACEMENT_URL ] ) ) );
        } else {
            delete_post_meta( $product_id, self::REPLACEMENT_URL, false );
        }
    }

    /**
     * Check if we should override the price for a specific product.
     * 
     * @since 0.1
     * 
     * @param string $price_label The current price label.
     * @param object $product The product object.
     * 
     * @return string
     */
    public function override_price( $price_label, $product ) {
        $override = get_post_meta( $product->get_id(), self::HIDE_PRICE, true );

        if ( ! empty( $override ) ) {
            $replace = get_post_meta( $product->get_id(), self::REPLACE_PRICE, true );

            if ( ! empty( $replace ) ) {
                $type = get_post_meta( $product->get_id(), self::REPLACEMENT_TYPE, true );

                switch ( $type ) {
                    case 'button' :
                        return $this->render_button( $product );
                        break;
                    case 'label':
                        return get_post_meta( $product->get_id(), self::REPLACEMENT_TEXT, true );
                        break;
                }
            }

            // Default to just returning an empty string.
            return '';
        }

        return $price_label;
    }

    /**
     * Render a button for the price replacement.
     * 
     * @since 0.1
     * 
     * @param object $product The product object.
     */
    public function render_button( $product ) {
        $text = get_post_meta( $product->get_id(), self::REPLACEMENT_TEXT, true );
        $url  = get_post_meta( $product->get_id(), self::REPLACEMENT_URL, true );

        if ( empty( $text ) ) {
            return '';
        }

        if ( empty( $url ) ) {
            return $text;
        }

        return sprintf(
            '<a href="%1$s" class="button">%2$s</a>',
            esc_url( $url ),
            esc_html( $text )
        );
    }

    /**
     * Remove the add to cart button if enabled.
     * 
     * @since 0.1
     * 
     * @param bool   $is_purchasable Can the product be purchased?
     * @param object $product The product object.
     */
    public function remove_add_to_cart( $is_purchasable, $product ) {
        $hide = get_post_meta( $product->get_id(), self::HIDE_ADD_TO_CART, true );

        if ( ! empty( $hide ) ) {
            $is_purchasable = false;
        }

        return $is_purchasable;
    }
}
