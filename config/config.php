<?php
/**
 * Configs used throughout the plugin.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices;

define( 'HIDE_PRICES_VERSION', '1.0' );
define( 'HIDE_PRICES_DIR', trailingslashit( dirname( __DIR__ ) ) );
define( 'HIDE_PRICES_URL', trailingslashit( plugins_url( '', HIDE_PRICES_DIR . 'hide-prices.php' ) ) );
define( 'HIDE_PRICES_BASENAME', plugin_basename( HIDE_PRICES_DIR . 'hide-prices.php' ) );
