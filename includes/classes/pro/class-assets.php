<?php
/**
 * Enqueue Assets.
 * 
 * @since 0.1
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices\Pro;

/**
 * Class Assets
 *
 * @since 0.1
 */
class Assets {

    /**
     * Handle for enqueuing JS assets.
     * 
     * @since 0.1
     */
    const JS_HANDLE = 'wpgks-hide-price';

    /**
     * Handle for enqueuing CSS assets.
     * 
     * @since 0.1
     */
    const CSS_HANDLE = 'wpgks-hide-price';

    /**
     * Hooks.
     * 
     * @since 0.1
     */
    public function hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ], 9999 );
    }

    /**
	 * Enqueue the required JS scripts.
	 *
	 * @since 0.1
	 */
    public function enqueue_scripts() {
        $screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'product' === $screen->post_type ) {
			wp_enqueue_script( self::JS_HANDLE, HIDE_PRICES_URL . 'assets/js/hide-prices.js', [ 'jquery' ], '0.1' );
		}
    }
    
    /**
	 * Enqueue the required CSS scripts.
	 *
	 * @since 0.1
	 */
    public function enqueue_styles() {
        $screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'product' === $screen->post_type ) {
			wp_enqueue_style( self::CSS_HANDLE, HIDE_PRICES_URL . 'assets/css/hide-prices.css', [], '0.1' );
		}
    }
}
