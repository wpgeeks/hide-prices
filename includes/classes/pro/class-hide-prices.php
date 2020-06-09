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
        add_action( 'woocommerce_process_product_meta', [ $this, 'save_simple_settings' ] );
        add_action( 'woocommerce_save_product_variation', [ $this, 'save_variable_settings' ], 10, 2 );
        add_action( 'woocommerce_get_price_html', [ $this, 'override_price' ], 9999, 2 );
        add_action( 'woocommerce_get_variation_price_html', [ $this, 'override_price' ], 9999, 2 );
        add_action( 'woocommerce_is_purchasable', [ $this, 'remove_add_to_cart' ], 9999, 2 );
        add_filter( 'woocommerce_show_variation_price', '__return_true' );
        add_filter( 'woocommerce_variation_prices', [ $this, 'remove_prices_from_variation_range' ], 10, 2 );
    }

    /**
     * Render the settings for the simple product tab.
     * 
     * @since 0.1
     */
    public function render_settings() {
        echo wp_kses_post( '<div class="options_group show_if_simple">' );
 
        $this->render_setting_fields();
    
        echo wp_kses_post( '</div>' );
    }

    /**
     * Render the settings for the variable product tab.
     * 
     * @since 0.1
     * 
     * @param int    $index The variation index.
     * @param array  $variation_data The variation data.
     * @param object $variation The variation object.
     */
    public function render_variable_settings( $index, $variation_data, $variation ) {
        $this->render_setting_fields( false, $variation->ID, $index );
    }

    /**
     * Render the actual settings fields for all tabs.
     * 
     * @since 0.1
     * 
     * @param bool $simple Display for simple product or variation (false).
     */
    public function render_setting_fields( $simple = true, $id = null, $index = null ) {
        if ( empty( $id ) ) {
            $id = get_the_ID();
        }

        $id_suffix   = '';
        $name_suffix = '';

        if ( ! $simple ) {
            $id_suffix   = $index;
            $name_suffix = sprintf( '[%s]', $index );
        }

        $wrapper_classes_single      = $simple ? '' : 'form-row form-row-full';
        $wrapper_classes_split_first = $simple ? '' : 'form-row form-row-first';
        $wrapper_classes_split_last  = $simple ? '' : 'form-row form-row-last';
        
        // Additional classes for variations.
        $wrapper_classes = $simple ? $wrapper_classes_split_first : sprintf( '%s %s_field', $wrapper_classes_split_first, self::HIDE_PRICE );
        woocommerce_wp_checkbox(
            [
                'id'            => self::HIDE_PRICE . $id_suffix,
                'name'          => self::HIDE_PRICE . $name_suffix,
                'value'         => get_post_meta( $id, self::HIDE_PRICE, true ),
                'wrapper_class' => $wrapper_classes . ' options',
                'class'         => sprintf( '%s_form_field', self::HIDE_PRICE ),
                'label'         => __( 'Hide the price', 'hide-prices' ),
                'desc_tip'      => $simple ? false : true,
                'description'   => __( 'Enabling this option will hide the price from the product page', 'hide-prices' ),
            ]
        );

       // Additional classes for variations.
       $wrapper_classes = $simple ? $wrapper_classes_split_last : sprintf( '%s %s_field', $wrapper_classes_split_last, self::HIDE_ADD_TO_CART );
       woocommerce_wp_checkbox(
            [
                'id'            => self::HIDE_ADD_TO_CART . $id_suffix,
                'name'          => self::HIDE_ADD_TO_CART . $name_suffix,
                'value'         => get_post_meta( $id, self::HIDE_ADD_TO_CART, true ),
                'wrapper_class' => $wrapper_classes . ' options',
                'class'         => sprintf( '%s_form_field', self::HIDE_ADD_TO_CART ),
                'label'         => $simple ? __( 'Hide add to cart button', 'hide-prices' ) : __( 'Disable add to cart button', 'hide-prices' ) ,
                'desc_tip'      => $simple ? false : true,
                'description'   => $simple ? 
                    __( 'Enabling this option will hide the add to cart button from the product page', 'hide-prices' ) :
                    __( 'Enabling this option will disable the add to cart button on the product page', 'hide-prices' ),
            ]
        );

        // Additional classes for variations.
        $wrapper_classes = $simple ? $wrapper_classes_split_first : sprintf( '%s %s_field', $wrapper_classes_split_first, self::REPLACE_PRICE );
        woocommerce_wp_checkbox(
            [
                'id'            => self::REPLACE_PRICE . $id_suffix,
                'name'          => self::REPLACE_PRICE . $name_suffix,
                'value'         => get_post_meta( $id, self::REPLACE_PRICE, true ),
                'wrapper_class' => $wrapper_classes . ' options',
                'class'         => sprintf( '%s_form_field', self::REPLACE_PRICE ),
                'label'         => __( 'Show custom price label', 'hide-prices' ),
                'desc_tip'      => $simple ? false : true,
                'description'   => __( 'This will show a custom label or button if enabled', 'hide-prices' ),
            ]
        );

        // Additional classes for variations.
        $wrapper_classes = $simple ? $wrapper_classes_split_first : sprintf( '%s %s_field', $wrapper_classes_split_first, self::REPLACEMENT_TYPE );
        woocommerce_wp_select(
            [
                'id'            => self::REPLACEMENT_TYPE . $id_suffix,
                'name'          => self::REPLACEMENT_TYPE . $name_suffix,
                'value'         => get_post_meta( $id, self::REPLACEMENT_TYPE, true ),
                'wrapper_class' => empty( get_post_meta( $id, self::REPLACE_PRICE, true ) ) ? $wrapper_classes . ' hidden' : $wrapper_classes,
                'class'         => sprintf( '%s_form_field', self::REPLACEMENT_TYPE ),
                'label'         => __( 'Replace price with', 'hide-price' ),
                'options'       => [
                    'label'  => __( 'Label', 'hide-price' ),
                    'button' => __( 'Button', 'hide-price' ),
                ],
            ]
        );

        // Additional classes for variations.
        $wrapper_classes = $simple ? $wrapper_classes_split_last : sprintf( '%s %s_field', $wrapper_classes_split_last, self::REPLACEMENT_TEXT );
        woocommerce_wp_text_input(
            [
                'id'            => self::REPLACEMENT_TEXT . $id_suffix,
                'name'          => self::REPLACEMENT_TEXT . $name_suffix,
                'value'         => get_post_meta( $id, self::REPLACEMENT_TEXT, true ),
                'wrapper_class' => empty( get_post_meta( $id, self::REPLACE_PRICE, true ) ) ? $wrapper_classes . ' hidden' : $wrapper_classes,
                'class'         => sprintf( '%s_form_field', self::REPLACEMENT_TEXT ),
                'label'         => __( 'Label / button text', 'hide-price' ),
                'description'   => __( 'Text to show on the label or button replacing the price', 'hide-price' ),
            ]
        );

        // Additional classes for variations.
        $wrapper_classes = $simple ? $wrapper_classes_single : sprintf( '%s %s_field', $wrapper_classes_single, self::REPLACEMENT_URL );
        woocommerce_wp_text_input(
            [
                'id'            => self::REPLACEMENT_URL . $id_suffix,
                'name'          => self::REPLACEMENT_URL . $name_suffix,
                'value'         => get_post_meta( $id, self::REPLACEMENT_URL, true ),
                'wrapper_class' => empty( get_post_meta( $id, self::REPLACE_PRICE, true ) ) || 'button' !== get_post_meta( $id, self::REPLACEMENT_TYPE, true ) ? $wrapper_classes . ' hidden' : $wrapper_classes,
                'class'         => sprintf( '%s_form_field', self::REPLACEMENT_URL ),
                'label'         => __( 'Button URL', 'hide-price' ),
                'description'   => __( 'URL for the button', 'hide-price' ),
            ]
        );
    }

    /**
     * Save the settings for a simple product.
     * 
     * @since 0.1
     * 
     * @param int $product_id The WooCommerce product ID.
     */
    public function save_simple_settings( $product_id ) {
        $this->save_settings( $product_id );
    }

    /**
     * Save the settings for the variable product.
     * 
     * @since 0.1
     * 
     * @param int $product_id The WooCommerce product ID.
     * @param int $index The form index for the variation.
     */
    public function save_variable_settings( $product_id, $index ) {
        $this->save_settings( $product_id, $index );
    }
    
    /**
     * Save the settings.
     * 
     * @since 0.1
     * 
     * @param int $product_id The WooCommerce product ID.
     * @param int $index The form index for the variation.
     */
    public function save_settings( $product_id, $index = null ) {
        $this->update_value( $product_id, self::HIDE_PRICE, $index, 'string' );
        $this->update_value( $product_id, self::HIDE_ADD_TO_CART, $index, 'string' );
        $this->update_value( $product_id, self::REPLACE_PRICE, $index, 'string' );
        $this->update_value( $product_id, self::REPLACEMENT_TYPE, $index, 'string' );
        $this->update_value( $product_id, self::REPLACEMENT_TEXT, $index, 'string' );
        $this->update_value( $product_id, self::REPLACEMENT_URL, $index, 'url' );
    }

    /**
     * Update a post meta value. This method allows us to update a value
     * if it's set on a simple product or variable product (which uses an
     * index to pass through the form field value). It also passes on
     * sanitization based on type.
     * 
     * @since 0.1
     * 
     * @param int    $product_id The WooCommerce product ID.
     * @param string $key The form field name (usually the setting ID).
     * @param int    $index For variable products, the index value of the product being saved.
     * @param string $type The data type for handling sanitization.
     */
    public function update_value( $product_id, $key, $form_index = null, $type = 'string' ) {
        if ( null === $form_index && ! empty( $_POST[ $key ] ) ) { // Check for null as the form index can be 0 and empty won't work.
            update_post_meta( $product_id, $key, $this->get_and_sanitize_field( $key, $type ) );
        } elseif ( $form_index >= 0 && ! empty( $_POST[ $key ][ $form_index ] ) ) {
            update_post_meta( $product_id, $key, $this->get_and_sanitize_field( [ $key, $form_index ], $type ) );
        } else {
            delete_post_meta( $product_id, $key );
        }
    }

    /**
     * Get a field value and santizes it based on type. Also adds the ability
     * to get and santize a field with an index.
     * 
     * @since 0.1
     * 
     * @param string $value The value to sanitze.
     * @param string $type The type of data we're dealing with.
     */
    public function get_and_sanitize_field( $key, $type = 'string' ) {
        if ( is_array( $key ) ) {
            $index = $key[1];
            $key   = $key[0];
        }

        switch ( $type ) {
            case 'url':
                return null === $index ? esc_url_raw( wp_unslash( $_POST[ $key ] ) ) : esc_url_raw( wp_unslash( $_POST[ $key ][ $index ] ) );
                break;
            default:
                return null === $index ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : sanitize_text_field( wp_unslash( $_POST[ $key ][ $index ] ) );
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
        if ( is_admin() ) {
            return $price_label;
        }

        $hide_price = get_post_meta( $product->get_id(), self::HIDE_PRICE, true );

        if ( ! empty( $hide_price ) ) {
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

    /**
     * If a variation product is being hidden, we should remove it from the
     * price range so that it's not accidently shown on the front end.
     * 
     * @since 0.1
     * 
     * @param array  $prices An array of variation prices.
     * @param object $product The parent product.
     */
    public function remove_prices_from_variation_range( $prices, $product ) {
        if ( is_admin() ) {
            return $prices;
        }

        if ( ! empty( $prices['price'] ) && is_array( $prices['price'] ) ) {
            foreach ( $prices['price'] as $product_id => $product_prices ) {
                $hide_price = get_post_meta( $product_id, self::HIDE_PRICE, true );

                if ( $hide_price ) {
                    unset( $prices['price'][ $product_id ] );
                    unset( $prices['regular_price'][ $product_id ] );
                    unset( $prices['sale_price'][ $product_id ] );
                }
            }
        }
        
        return $prices;
    }
}
