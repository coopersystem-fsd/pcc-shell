<?php
/**
 * @package Polylang-Pro
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class for FSE modules.
 *
 * @since 3.2
 */
abstract class PLL_FSE_Abstract_Module {

	/**
	 * Instance of `PLL_Model`.
	 *
	 * @var PLL_Model
	 */
	protected $model;

	/**
	 * Plugin's options.
	 *
	 * @var array<mixed>
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @since 3.2
	 *
	 * @param  PLL_Base $polylang Instance of the main Polylang object, passed by reference.
	 * @return void
	 */
	public function __construct( PLL_Base &$polylang ) {
		$this->model   = &$polylang->model;
		$this->options = &$polylang->options;
	}

	/**
	 * Returns the default language.
	 *
	 * @since 1.0
	 *
	 * @return PLL_Language|null
	 */
	protected function get_default_language() {
		if ( empty( $this->options['default_lang'] ) || ! is_string( $this->options['default_lang'] ) ) {
			return null;
		}

		$def_lang = $this->model->get_language( sanitize_key( $this->options['default_lang'] ) );

		return empty( $def_lang ) ? null : $def_lang;
	}

	/**
	 * Returns the list of the slugs of enabled languages.
	 *
	 * @since 1.0
	 *
	 * @return array<string>
	 */
	protected function get_languages_slugs() {
		return $this->model->get_languages_list( array( 'fields' => 'slug' ) );
	}
}
