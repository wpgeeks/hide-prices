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
     * Handle for enqueuing assets.
     * 
     * @since 0.1
     */
    const ASSETS_HANDLE = 'wpgks-hide-price';

    /**
     * Hooks.
     * 
     * @since 0.1
     */
    public function hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
	 * Enqueue the required scripts.
	 *
	 * @since 0.1
	 */
    public function enqueue_scripts() {
        $screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'product' === $screen->post_type ) {
			wp_enqueue_script( self::ASSETS_HANDLE, HIDE_PRICES_URL . 'assets/js/hide-prices.js', [ 'jquery' ], '0.1' );
		}
	}
}
