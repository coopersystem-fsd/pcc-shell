<?php
/**
 * @package Polylang-Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
};

add_action(
	'after_setup_theme',
	function() {
		/**
		 * This must be checked only after the theme is loaded (not earlier than 'after_setup_theme') because some
		 * themes include ACF.
		 */
		if ( ! defined( 'ACF_VERSION' ) || version_compare( ACF_VERSION, '5.7.11', '<' ) ) {
			// Run only for ACF >= 5.7.11.
			return;
		}

		if ( ! did_action( 'pll_init' ) ) {
			// Run only if Polylang (and its API) is loaded.
			return;
		}

		add_action( 'init', array( PLL_Integrations::instance()->acf = new PLL_ACF(), 'init' ) );
	}
);
