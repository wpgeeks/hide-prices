<?php
/**
 * Configs used throughout the plugin.
 *
 * @since 0.1
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices;

define( 'HIDE_PRICES_DIR', trailingslashit( dirname( dirname( dirname( __FILE__ ) ) ) ) );
define( 'HIDE_PRICES_URL', trailingslashit( plugins_url( 'hide-prices', dirname( dirname( dirname( __FILE__ ) ) ) ) ) );
