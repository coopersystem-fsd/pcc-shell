<?php
/**
 * @package Polylang-Pro
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Expose terms language and translations in the REST API for templates in particular by filtering the queries.
 *
 * @since 3.2
 */
class PLL_FSE_REST_Template extends PLL_REST_Post {

	/**
	 * Value to indicate the template add link can be used.
	 *
	 * @var string
	 */
	const CAN_CREATE_TEMPLATE = 'CAN_CREATE_TEMPLATE';

	/**
	 * Value to indicate the template delete link can be used.
	 *
	 * @var string
	 */
	const CAN_DELETE_TEMPLATE = 'CAN_DELETE_TEMPLATE';

	/**
	 * The post type of the template.
	 *
	 * @var string
	 */
	private $post_type = '';

	/**
	 * Constructor
	 *
	 * @since 3.2
	 *
	 * @param PLL_REST_API  $rest_api      Instance of PLL_REST_API.
	 * @param array<string> $content_types Array with post types as keys and values.
	 * @return void
	 */
	public function __construct( PLL_REST_API &$rest_api, array $content_types ) {
		parent::__construct( $rest_api, $content_types );

		$this->setter_id_name = 'wp_id';
		$this->getter_id_name = 'wp_id';
	}

	/**
	 * Adds the corresponding hooks.
	 *
	 * @since 3.2
	 *
	 * @return self
	 */
	public function init() {
		add_filter( 'rest_dispatch_request', array( $this, 'get_current_post_type_from_route' ), 10, 2 );
		add_filter( 'get_edit_post_link', array( $this, 'override_edit_post_link' ), 10, 3 );
		return $this;
	}

	/**
	 * Returns the post id of the post that we come from to create a translation.
	 *
	 * Override the parent method because template translation is done with a REST API call instead of post-new.php like for the other post types.
	 *
	 * @since 3.2
	 *
	 * @return int The post id of the original post.
	 */
	public function get_from_post_id() {
		return (int) $this->request->get_param( 'from_post' );
	}

	/**
	 * Overloads parent's method to add the delete link and default language into a translation table data for template post types.
	 * See PLL_REST_Post::generate_translation_data.
	 *
	 * @since 3.2
	 *
	 * @param int          $id               The id of the existing post to get datas for the translations table element.
	 * @param int          $tr_id            The id of the translated post for the given language if exists.
	 * @param PLL_Language $language         The given language object.
	 * @return array The translation data of the given language.
	 */
	public function get_translation_table_data( $id, $tr_id, $language ) {
		$translation_data = parent::get_translation_table_data( $id, $tr_id, $language );

		// When no template part exist in DB, we need to return a non-empty value in the add_link item.
		if ( ! empty( $this->post_type ) ) {
			$template_post_type = get_post_type_object( $this->post_type );
			if ( ! empty( $template_post_type ) ) {
				if ( current_user_can( $template_post_type->cap->create_posts ) ) {
					$translation_data['links']['add_link'] = self::CAN_CREATE_TEMPLATE;
				}
				// Verify the post is translated in the given language and the user can delete posts to add the delete link.
				if ( current_user_can( $template_post_type->cap->delete_posts ) ) {
					$translation_data['links']['delete_link'] = self::CAN_DELETE_TEMPLATE;
				}
			}
			// Gets the template id in the `theme // post name` format to be able to delete it from the UI.
			if ( ! empty( $tr_id ) ) {
				$templates = get_block_templates( array( 'wp_id' => $tr_id ), $this->post_type );
				if ( ! empty( $templates ) ) {
					$template = reset( $templates );
					$translation_data['template']['id'] = $template->id;
				}
			}
		}

		$translation_data['is_default_lang'] = $language->slug === $this->model->options['default_lang'];

		return $translation_data;
	}
	/**
	 * Overrides the edit link from WordPress for template parts.
	 * By default it is "https://example.com/wp-admin/post.php?post=X&action=edit", which is not pointing to the Site Editor.
	 * Returns "https://example.com/wp-admin/site-editor.php?postId=theme-slug//template-slug&postType=wp_template_part" instead.
	 *
	 * @since 3.2
	 *
	 * @param string $link    The edit link.
	 * @param int    $post_id Post ID.
	 * @param string $context The link context. If set to 'display' then ampersands
	 *                        are encoded.
	 * @return string The overriden edit link.
	 */
	public function override_edit_post_link( $link, $post_id, $context ) {
		// We do not use the 'display' to generate the edit link in translation table: see PLL_REST_Post::get_translations_table().
		if ( 'display' === $context ) {
			return $link;
		}

		if ( ! PLL_FSE_Tools::is_template_post_type( get_post_type( $post_id ) ) ) {
			// Not a template post type.
			return $link;
		}

		$templates = get_block_templates( array( 'wp_id' => $post_id ), 'wp_template_part' );

		if ( empty( $templates ) ) {
			return $link;
		}

		$template = reset( $templates );

		if ( $template->wp_id !== $post_id ) {
			return $link;
		}

		return add_query_arg(
			array(
				'postId'    => rawurlencode( $template->id ), // postId refers to the WP_Block_Template id (i.e. "theme-slug//template-slug").
				'postType'  => $template->type,
			),
			admin_url( 'site-editor.php' )
		);
	}

	/**
	 * Returns the slug of the language assigned to the given post.
	 * Overrides the parent method.
	 *
	 * @since 3.2
	 *
	 * @param array<mixed> $object Post array.
	 * @return string|false        Template's language slug. Default language slug if no language is assigned to the
	 *                             template yet. False on failure.
	 */
	public function get_language( $object ) {
		if ( ! empty( $object[ $this->getter_id_name ] ) ) {
			$language = $this->model->{$this->type}->get_language( $object[ $this->getter_id_name ] );

			if ( ! empty( $language ) ) {
				return $language->slug;
			}
		}

		$lang = $this->model->get_language( $this->model->options['default_lang'] );

		if ( ! empty( $lang ) ) {
			return $lang->slug;
		}

		return false;
	}

	/**
	 * Filters templates by language.
	 *
	 * @since 3.2
	 *
	 * @param  WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		if ( ! empty( $query->query_vars['post_name__in'] ) && is_array( $query->query_vars['post_name__in'] ) ) {
			// Do not filter query for a single item.
			return;
		}

		if ( isset( $query->query_vars['lang'] ) && empty( $query->query_vars['lang'] ) ) {
			// We've been asking not to filter by language.
			return;
		}

		if ( ! PLL_FSE_Tools::is_template_query( $query ) ) {
			// Not a template part query.
			return;
		}

		// Filter a templates list query by the default language.
		if ( ! empty( $this->request ) ) {
			$lang = $this->request->get_param( 'lang' );
		}

		if ( empty( $lang ) ) {
			$lang = $this->model->options['default_lang'];
		}

		$this->load_untranslated_templates_in_current_language( $lang );

		$pll_query = new PLL_Query( $query, $this->model );
		$pll_query->query->set( 'lang', $lang );
		$pll_query->filter_query( $this->model->get_language( $lang ) );
	}

	/**
	 * Adds the templates without translation to the templates block list.
	 *
	 * @param string $current_lang The current language.
	 * @return void
	 */
	public function load_untranslated_templates_in_current_language( $current_lang ) {
		if ( ! empty( $this->request ) ) {
			$include_untranslated = $this->request->get_param( 'include_untranslated' );
		}

		if ( empty( $include_untranslated ) ) {
			return;
		}

		if ( $current_lang === $this->model->options['default_lang'] ) {
			return;
		}

		add_filter( 'rest_pre_echo_response', array( $this, 'add_untranslated_templates_to_rest_result' ), 10, 3 );
	}

	/**
	 * Add untranslated templates in the current language to the REST result.
	 *
	 * @param array           $result  Response data to send to the client.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return array result array with added untranslated templates in the current language.
	 */
	public function add_untranslated_templates_to_rest_result( $result, $server, $request ) {
		$template_route = new PLL_FSE_REST_Route( $request->get_route() );
		if ( ! $template_route->is_template_route() ) {
			return $result;
		}

		$current_lang = $this->model->get_language( $this->request->get_param( 'lang' ) );
		if ( empty( $current_lang ) ) {
			// No current language.
			return $result;
		}

		$def_lang = $this->model->get_language( $this->model->options['default_lang'] );
		if ( empty( $def_lang ) ) {
			// No default language.
			return $result;
		}

		$untranslated_posts_in_current_lang = $this->model->post->get_untranslated( $this->post_type, $current_lang, $def_lang );
		if ( empty( $untranslated_posts_in_current_lang ) ) {
			return $result;
		}

		$slugs = wp_list_pluck( $untranslated_posts_in_current_lang, 'post_name' );

		// Gets the templates controller.
		$template_post_type = get_post_type_object( $this->post_type );
		if ( empty( $template_post_type ) ) {
			// No template post type.
			return $result;
		}

		$rest_controller = $template_post_type->get_rest_controller();
		if ( empty( $rest_controller ) ) {
			// No REST API controller for this post type.
			return $result;
		}

		$templates = array();
		foreach ( get_block_templates( array( 'slug__in' => $slugs ), $this->post_type ) as $template ) {
			// Turn WP_Block_Template into REST response.
			$data = $rest_controller->prepare_item_for_response( $template, $request );
			if ( ! is_wp_error( $data ) ) {
				$templates[] = $rest_controller->prepare_response_for_collection( $data );
			}
		}

		return array_merge( $result, $templates );
	}

	/**
	 * Returns the current post type from the REST route
	 *
	 * @since 3.2
	 *
	 * @param  mixed           $result  Response to replace the requested version with. Can be anything a normal
	 *                                  endpoint can return, or null to not hijack the request.
	 * @param  WP_REST_Request $request Request used to generate the response.
	 * @return mixed                    Unchanged value.
	 */
	public function get_current_post_type_from_route( $result, $request ) {
		if ( ! $request instanceof WP_REST_Request ) {
			return $result;
		}

		$route = new PLL_FSE_REST_Route( $request->get_route() );

		if ( ! $route->is_template_route() ) {
			return $result;
		}

		$params = $request->get_params();

		if ( ! empty( $params['postType'] ) && is_string( $params['postType'] ) && PLL_FSE_Tools::is_template_post_type( $params['postType'] ) ) {
			$this->post_type = $params['postType'];
		} else {
			$post_type = $route->get_post_type();

			if ( ! empty( $post_type ) ) {
				$this->post_type = $post_type;
			}
		}

		return $result;
	}
}
