<?php
/**
 * @package Polylang-Pro
 */

/**
 * Language switcher block for navigation.
 *
 * @since 3.2
 */
class PLL_Navigation_Language_Switcher_Block extends PLL_Abstract_Language_Switcher_Block {

	/**
	 * Adds the required hooks specific to the navigation langague switcher.
	 *
	 * @since 3.2
	 *
	 * @return self
	 */
	public function init() {
		parent::init();

		add_action( 'rest_api_init', array( $this, 'register_switcher_menu_item_options_meta_rest_field' ) );

		return $this;
	}

	/**
	 * Returns the navigation language switcher block name with the Polylang's namespace.
	 *
	 * @since 3.2
	 *
	 * @return string The block name.
	 */
	protected function get_block_name() {
		return 'polylang/navigation-language-switcher';
	}

	/**
	 * Renders the `polylang/navigation-language-switcher` block on server.
	 *
	 * @since 3.1
	 *
	 * @param array $attributes The block attributes.
	 * @return string The HTML string output to serve.
	 */
	public function render( $attributes ) {
		$attributes = $this->set_attributes_for_block( $attributes );

		$attributes['classes'] = isset( $attributes['className'] ) ? $attributes['className'] : '';

		$switcher = new PLL_Switcher();
		$switcher_items = $switcher->the_languages( $this->links, array_merge( $attributes, array( 'raw' => true ) ) );
		$language_navigation_output = '';

		$top_level_item = $this->find_current_lang_item( $switcher_items );

		if ( $attributes['dropdown'] && $top_level_item ) {
			$language_navigation_output = $this->render_link_item( $top_level_item, $attributes, $switcher_items );
		} else {
			foreach ( $switcher_items as $switcher_item ) {
				$language_navigation_output .= $this->render_link_item( $switcher_item, $attributes );
			}
		}

		if ( empty( $language_navigation_output ) ) {
			return '';
		}

		// As WordPress won't render our polylang/navigation-language-switcher (see: https://github.com/WordPress/WordPress/blob/5.9/wp-includes/blocks/navigation.php#L488-L491)
		// We have to do it ourselves.
		return sprintf( '<ul class="wp-block-navigation__container">%s</ul>', $language_navigation_output );
	}

	/**
	 * Register switcher menu item meta options as a REST API field.
	 *
	 * @since 3.2
	 *
	 * @return void
	 */
	public function register_switcher_menu_item_options_meta_rest_field() {
		register_post_meta(
			'nav_menu_item',
			'_pll_menu_item',
			array(
				'object_subtype' => 'nav_menu_item',
				'description'    => __( 'Language switcher settings', 'polylang-pro' ),
				'single'         => true,
				'show_in_rest'   => array(
					'schema' => array(
						'type'                 => 'object',
						'additionalProperties' => array(
							'type' => 'boolean',
						),
					),
				),
			)
		);
	}

	/**
	 * Renders language switcher in dropdown mode for navigation block. Applies recursively for dropdown.
	 * Partial copy of the WordPress functions render_block_core_navigation_link() and render_block_core_navigation_submenu().
	 * See: https://github.com/WordPress/wordpress-develop/blob/5.9.0/src/wp-includes/blocks/navigation-link.php#L116-L125 and https://github.com/WordPress/wordpress-develop/blob/5.9.0/src/wp-includes/blocks/navigation-submenu.php#L116-L125
	 *
	 * @since 3.2
	 *
	 * @param array<mixed> $switcher_item Raw element of a language switcher.
	 * @param array<mixed> $attributes    The attributes of the language switcher.
	 * @param array<mixed> $inner_items   Elements of the submenu, used for dropdown. Default to empty array.
	 * @return string                     The rendered switcher.
	 */
	protected function render_link_item( $switcher_item, $attributes, $inner_items = array() ) {
		$has_submenu = ! empty( $inner_items ) && $attributes['dropdown'] && $switcher_item['current_lang'];
		$is_active   = $switcher_item['current_lang'];

		$wp_classes = array(
			$has_submenu ? 'wp-block-navigation-submenu has-child open-on-hover-click' : 'wp-block-navigation-link', // Adds mandatory classes for dropdown menu rendering in the top level element.
			'wp-block-navigation-item',
		);
		$user_classes = array(
			isset( $attributes['className'] ) ? $attributes['className'] : '',
		);
		$classes = array_merge(
			isset( $switcher_item['classes'] ) ? $switcher_item['classes'] : array(),
			$wp_classes,
			$user_classes
		);
		$css_classes = trim( implode( ' ', $classes ) );

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $css_classes,
			)
		);
		$html = '<li ' . $wrapper_attributes . '><a class="wp-block-navigation-item__content" ';

		// Start appending HTML attributes to anchor tag.
		$html .= ' href="' . esc_url( $switcher_item['url'] ) . '"';

		if ( $is_active ) {
			$html .= ' aria-current="page"';
		}

		if ( isset( $switcher_item['locale'] ) ) {
			$html .= ' hreflang="' . esc_attr( $switcher_item['locale'] ) . '"';

			$html .= ' lang="' . esc_attr( $switcher_item['locale'] ) . '"';
		}
		// End appending HTML attributes to anchor tag.

		// Start anchor tag content.
		$html .= '>' .
			// Wrap title with span to isolate it from submenu icon.
			'<span class="wp-block-navigation-item__label">';

		if ( ! empty( $attributes['show_flags'] ) ) {
			$html .= $switcher_item['flag'];
		}

		if ( ! empty( $attributes['show_names'] ) ) {
			$html .= esc_html( $switcher_item['name'] );
		}

		$html .= '</span>';

		$html .= '</a>';
		// End anchor tag content.

		// Start inner content for submenu.
		if ( $has_submenu ) {
			$aria_label = esc_attr__( 'Language switcher submenu.', 'polylang-pro' );

			$html .= '<button aria-label="' . $aria_label . '" class="wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle" aria-expanded="false">' . block_core_navigation_submenu_render_submenu_icon() . '</button>';

			$inner_blocks_html = '';
			foreach ( $inner_items as $inner_item ) {
				$inner_blocks_html .= $this->render_link_item( $inner_item, $attributes );
			}

			$html .= sprintf(
				'<ul class="wp-block-navigation__submenu-container">%s</ul>',
				$inner_blocks_html
			);
		}
		// End inner content for submenu.

		$html .= '</li>';

		return $html;
	}

	/**
	 * Returns the current language item from the switcher elements.
	 *
	 * @since 3.2
	 *
	 * @param  array<mixed> $switcher_items An array of raw switcher items.
	 * @return array<mixed>|false           The item in the current language if found, false otherwise.
	 */
	protected function find_current_lang_item( $switcher_items ) {
		$filtered_items = wp_list_filter( $switcher_items, array( 'current_lang' => true ) );

		return reset( $filtered_items );
	}
}
