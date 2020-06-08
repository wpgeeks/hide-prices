<?php
/**
 * Singleton trait to be used by classes.
 *
 * @since 0.1
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices\Traits;

/**
 * Trait Singleton
 *
 * @since 0.1
 *
 * @codeCoverageIgnore
 *
 * @package WPGeeks\Plugin\HidePrices\Traits
 */
trait Singleton {

	/**
	 * The object instance.
	 *
	 * @since 0.1
	 *
	 * @var object|null Instance of object.
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since 0.1
	 */
	protected function __construct() {}

	/**
	 * No cloning of the object.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since 0.1
	 */
	final protected function __clone() {}

	/**
	 * Get an instance of the object.
	 *
	 * @since 0.1
	 *
	 * @codeCoverageIgnore
	 *
	 * @return object
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
