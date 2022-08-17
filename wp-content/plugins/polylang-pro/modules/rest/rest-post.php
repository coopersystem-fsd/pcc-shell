<?php
/**
 * @package Polylang-Pro
 */

/**
 * Expose terms language and translations in the REST API
 *
 * @since 2.2
 */
class PLL_REST_Post extends PLL_REST_Translated_Object {
	/**
	 * @var PLL_Filters_Sanitization
	 */
	public $filters_sanitization;

	/**
	 * Constructor
	 *
	 * @since 2.2
	 *
	 * @param PLL_REST_API $rest_api      Instance of PLL_REST_API
	 * @param array        $content_types Array of arrays with post types as keys and options as values
	 */
	public function __construct( &$rest_api, $content_types ) {
		parent::__construct( $rest_api, $content_types );

		$this->type           = 'post';
		$this->setter_id_name = 'ID';

		add_action( 'parse_query', array( $this, 'parse_query' ), 1 );
		add_action( 'add_attachment', array( $this, 'set_media_language' ) );

		foreach ( array_keys( $content_types ) as $post_type ) {
			add_filter( "rest_prepare_{$post_type}", array( $this, 'prepare_response' ), 10, 3 );
		}

		// Use rest_pre_dispatch_filter to be sure to get translations_table parameter in time.
		add_filter( 'rest_pre_dispatch', array( $this, 'get_rest_query_params' ), 10, 3 );
		// Use rest_pre_dispatch_filter to get the right language locale and initialize correctly sanitization filters.
		add_filter( 'rest_pre_dispatch', array( $this, 'set_filters_sanitization' ), 10, 3 );
	}

	/**
	 * Filters the query per language according to the 'lang' parameter
	 *
	 * @since 2.6.9
	 *
	 * @param WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		if ( $this->can_filter_query( $query ) ) {
			$pll_query = new PLL_Query( $query, $this->model );
			$pll_query->query->set( 'lang', $this->request['lang'] ); // Set query vars "lang" with the REST parameter value; fix #405 and #384
			$pll_query->filter_query( $this->model->get_language( $this->request['lang'] ) ); // fix #493
		}
	}

	/**
	 * Whether or not the given query is filterable by language.
	 *
	 * @since 3.2
	 *
	 * @param WP_Query $query The query to check.
	 * @return boolean
	 */
	protected function can_filter_query( $query ) {
		$query_post_types           = ! empty( $query->query['post_type'] ) ? (array) $query->query['post_type'] : array( 'post' );
		$allowed_post_types         = array_keys( $this->content_types );
		$allowed_queried_post_types = array_intersect( $query_post_types, $allowed_post_types );

		return isset( $this->request['lang'] ) && ! empty( $allowed_queried_post_types );
	}

	/**
	 * Allows to share the post slug across languages.
	 * Modifies the REST response accordingly.
	 *
	 * @since 2.3
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Post object.
	 * @param WP_REST_Request  $request  Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_response( $response, $post, $request ) {
		global $wpdb;
		$data = $response->get_data();

		if ( ! empty( $data['slug'] ) && in_array( $request->get_method(), array( 'POST', 'PUT' ) ) ) {
			$params     = $request->get_params();
			$attributes = $request->get_attributes();

			if ( ! empty( $params['slug'] ) ) {
				$requested_slug = $params['slug'];
			} elseif ( is_array( $attributes['callback'] ) && 'create_item' === $attributes['callback'][1] ) {
				// Allow sharing slug by default when creating a new post.
				$requested_slug = sanitize_title( $post->post_title );
			}

			if ( isset( $requested_slug ) && $post->post_name !== $requested_slug ) {
				$slug = wp_unique_post_slug( $requested_slug, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
				if ( $slug !== $data['slug'] && $wpdb->update( $wpdb->posts, array( 'post_name' => $slug ), array( 'ID' => $post->ID ) ) ) {
					$data['slug'] = $slug;
					$response->set_data( $data );
				}
			}
		}
		return $response;
	}

	/**
	 * Adds the translations_table REST field only when the request is called for the block editor.
	 *
	 * @see WP_REST_Server::dispatch()
	 *
	 * @since 2.6
	 *
	 * @param mixed           $result  Response to replace the requested version with. Can be anything
	 *                                 a normal endpoint can return, or null to not hijack the request.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return mixed
	 */
	public function get_rest_query_params( $result, $server, $request ) {
		if ( current_user_can( 'edit_posts' ) && null !== $request->get_param( 'is_block_editor' ) ) {
			// When it's a post request on a new post type you need to save the language really chosen by the user before any process. Fix #505
			if ( $this->is_save_post_request( $request->get_param( 'id' ), $request ) && ! empty( $request->get_param( 'lang' ) ) ) {
				$this->model->post->set_language( $request->get_param( 'id' ), $request->get_param( 'lang' ) );
			}

			// Save the translations earlier.
			if ( $this->is_save_post_request( $request->get_param( 'id' ), $request ) && ! empty( $request->get_param( 'translations' ) ) ) {
				$this->save_translations( $request->get_param( 'translations' ), get_post( $request->get_param( 'id' ) ) );
			}

			foreach ( array_keys( $this->content_types ) as $post_type ) {
				register_rest_field(
					$this->get_rest_field_type( $post_type ),
					'translations_table',
					array(
						'get_callback'    => array( $this, 'get_translations_table' ),
						'schema'          => array(
							'translations_table' => __( 'Translations table', 'polylang-pro' ),
							'type'               => 'object',
						),
					)
				);
			}
		}

		return $result;
	}

	/**
	 * Initialize correctly sanitization filters with the correct language locale.
	 *
	 * @see WP_REST_Server::dispatch()
	 *
	 * @since 2.9
	 *
	 * @param mixed           $result  Response to replace the requested version with. Can be anything
	 *                                 a normal endpoint can return, or null to not hijack the request.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return mixed
	 */
	public function set_filters_sanitization( $result, $server, $request ) {
		if ( current_user_can( 'edit_posts' ) ) {
			if ( ! empty( $request->get_param( 'lang' ) ) ) {
				$language = $this->model->get_language( sanitize_key( $request->get_param( 'lang' ) ) );
			} elseif ( ! empty( $request->get_param( 'id' ) ) ) {
				// Otherwise we need to get the language from the post itself.
				$language = $this->model->post->get_language( (int) $request->get_param( 'id' ) );
			}

			if ( ! empty( $language ) ) {
				$this->filters_sanitization = new PLL_Filters_Sanitization( $language->locale );
			}
		}

		return $result;
	}

	/**
	 * Check if the request is a REST API post type request for saving
	 *
	 * @since 2.7.3
	 *
	 * @param string          $post_id The post id.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return bool True if the request saves a post.
	 */
	public function is_save_post_request( $post_id, $request ) {
		$post_type_rest_bases = wp_list_pluck( get_post_types( array( 'show_in_rest' => true ), 'objects' ), 'rest_base' );

		// Some rest_base could be not defined and WordPress return false. The post type name is taken as rest_base.
		$post_type_rest_bases = array_merge(
			array_filter( $post_type_rest_bases ), // Get rest_base really defined.
			array_keys(  // Otherwise rest_base equals to the post type name.
				array_filter(
					$post_type_rest_bases,
					function( $value ) {
						return ! $value;
					}
				)
			)
		);
		// Pattern to verify the request route.
		$post_type_pattern = '#(' . implode( '|', array_values( $post_type_rest_bases ) ) . ')/' . $request->get_param( 'id' ) . '#';
		return preg_match( "$post_type_pattern", $request->get_route() ) && 'PUT' === $request->get_method();
	}

	/**
	 * Returns the post translations table
	 *
	 * @since 2.6
	 *
	 * @param array $object Post array
	 * @return array
	 */
	public function get_translations_table( $object ) {
		$return = array();

		// When we come from a post new creation
		$from_post_id = $this->get_from_post_id();

		foreach ( $this->model->get_languages_list() as $language ) {
			// If the request isn't from a source post creation, then get the translated post in the correct language.
			if ( ! empty( $from_post_id ) ) {
				$tr_id = $this->model->post->get( $from_post_id, $language );
			} else {
				$tr_id = $this->model->post->get_translation( $object[ $this->getter_id_name ], $language );
			}

			$return[ $language->slug ] = $this->get_translation_table_data( $object[ $this->getter_id_name ], $tr_id, $language );

			/**
			 * Filters the REST translations table.
			 *
			 * @since 2.6
			 *
			 * @param array        $row      Datas in a translations table row
			 * @param int          $id       Source post id.
			 * @param PLL_Language $language Translation language
			 */
			$return = apply_filters( 'pll_rest_translations_table', $return, $object[ $this->getter_id_name ], $language );
		}

		return $return;
	}

	/**
	 * Generates links, language information and translated posts for a given language into a translation table.
	 *
	 * @since 3.2
	 *
	 * @param int          $id               The id of the existing post to get datas for the translations table element.
	 * @param int          $tr_id            The id of the translated post for the given language if exists.
	 * @param PLL_Language $language         The given language object.
	 * @return array The translation data of the given language.
	 */
	public function get_translation_table_data( $id, $tr_id, $language ) {
		$translation_data = array();

		$translation_data['lang'] = $language;

		$link = $this->links->get_new_post_translation_link( $id, $language, 'keep ampersand' );
		$translation_data['links']['add_link'] = $link;

		// If a translation of the given post exist in the desired language, then we can add the edit link and the translated post information.
		if ( ! empty( $tr_id ) ) {
			$translated_post = get_post( $tr_id, ARRAY_A );
			$translation_data['links']['edit_link'] = get_edit_post_link( $tr_id, 'keep ampersand' );
			$translation_data['translated_post'] = array(
				'id'    => $translated_post['ID'],
				'title' => $translated_post['post_title'],
			);
		}

		return $translation_data;
	}

	/**
	 * Returns the post id of the post that we come from to create a translation.
	 *
	 * @since 3.2
	 *
	 * @return int The post id of the original post.
	 */
	public function get_from_post_id() {
		// When we come from a post new creation.
		return isset( $_GET['from_post'] ) ? (int) $_GET['from_post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Assigns the language to the edited media.
	 *
	 * When a media is edited in the block image, a new media is created and we need to set the language from the original one.
	 *
	 * @see https://make.wordpress.org/core/2020/07/20/editing-images-in-the-block-editor/ the new WordPress 5.5 feature: Editing Images in the Block Editor.
	 *
	 * @since 2.8
	 *
	 * @param int $post_id Post id.
	 * @return void
	 */
	public function set_media_language( $post_id ) {
		if ( ! empty( $this->request['id'] ) && $post_id !== $this->request['id'] ) {
			$this->model->post->set_language( $post_id, $this->model->post->get_language( $this->request['id'] ) );
		}
	}
}
