<?php
/**
 * Autoload PHP classes.
 *
 * @since 0.1
 *
 * @package WPGeeks\Plugin\HidePrices
 */

namespace WPGeeks\Plugin\HidePrices;

spl_autoload_register(
	function ( $class ) {
		$path = dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'includes/classes';
		$file = strtolower( str_replace( 'WPGeeks\\Plugin\\HidePrices\\', '', $class ) );

		// Class paths and name.
		$file  = str_replace( '_', '-', $file );
		$parts = explode( '\\', $file );

		foreach ( $parts as $index => $part ) {
			if ( count( $parts ) - 1 === $index ) {
				$type = 'class';

				if ( preg_match( '/traits/i', $class ) ) {
					$type = 'trait';
				}

				/*
				 * If we're dealing with a module (not a class in the module
				 * folder), add the module subfolder to the autoload URL.
				 */
				if ( preg_match( '/modules/i', $class ) ) {
					if ( file_exists( $path . DIRECTORY_SEPARATOR . $part ) ) {
						$type = sprintf(
							'%s%sclass',
							$part,
							DIRECTORY_SEPARATOR
						);
					}
				}

				$part = sprintf( '%s-%s.php', $type, $part );
			}

			$path .= sprintf( '%s%s', DIRECTORY_SEPARATOR, $part );
		}

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);
