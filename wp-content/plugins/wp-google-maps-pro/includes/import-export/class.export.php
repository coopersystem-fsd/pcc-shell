<?php
/**
 * WP Google Maps Pro Import / Export API: Export class
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;
 
/**
 * Exporter for WP Google Maps Pro
 *
 * This handles exporting of maps, categories, markers, circles,
 * polygons, polylines, rectangles and datasets.
 *
 * @since 7.0.0
 */
class Export {

	const JSON_VERSION = '1.01';

	/**
	 * Export arguments.
	 *
	 * @var array $export_args Documented in constructor.
	 */
	private $export_args = array();

	/**
	 * Export constructor.
	 *
	 * @param array $args {
	 *     Optional. Export arguments.
	 *
	 *     @type array $maps       Integer array of map ids to export.
	 *                             Empty array for all. Default empty.
	 *     @type bool  $categories Export categories? Default true.
	 *     @type bool  $markers    Export markers? Default true.
	 *     @type bool  $circles    Export circles? Default true.
	 *     @type bool  $polygons   Export polygons? Default true.
	 *     @type bool  $polylines  Export polylines? Default true.
	 *     @type bool  $rectangles Export rectangles? Default true.
	 *     @type bool  $datasets   Export datasets? Default true.
	 * }
	 */
	public function __construct( $args = array() ) {

		$export_args = wp_parse_args( $args, array(
			'maps'         => array(),
			'categories'   => true,
			'customfields' => true,
			'markers'      => true,
			'circles'      => true,
			'polygons'     => true,
			'polylines'    => true,
			'rectangles'   => true,
			'datasets'     => true,
			'ratings'      => false
		) );

		if ( is_array( $export_args['maps'] ) ) {

			$export_args['maps'] = $this->sanitize_map_ids( $export_args['maps'] );

		} else {

			$export_args['maps'] = array();

		}

		$this->export_args = $export_args;

	}

	/**
	 * Sanitize map ids.
	 *
	 * @param array $maps Integer array of map ids.
	 * @return array Integer array of map ids.
	 */
	private function sanitize_map_ids( $maps ) {

		$map_count = count( $maps );

		for ( $i = 0; $i < $map_count; $i++ ) {

			if ( ! is_numeric( $maps[ $i ] ) ) {

				unset( $maps[ $i ] );
				continue;

			}

			$maps[ $i ] = absint( $maps[ $i ] );

			if ( $maps[ $i ] < 1 ) {

				unset( $maps[ $i ] );

			}
		}

		return $maps;

	}

	/**
	 * Generates the JSON export file for download.
	 *
	 * @global string $wpgmza_pro_version WP Google Maps Pro version number.
	 */
	public function download() {

		global $wpgmza_pro_version;

		$site_name = sanitize_key( get_bloginfo( 'name' ) );

		if ( ! empty( $site_name ) ) {

			$site_name .= '.';

		}

		$file_name = $site_name . 'wpgooglemaps.' . current_time( 'Y-m-d' ) . '.json';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ), true );
		
		$pretty_print = version_compare( PHP_VERSION, '5.4', '>=' ) && defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : 0;

		echo wp_json_encode($this->get_data(), $pretty_print );

	}

	public function get_json(){
		$pretty_print = version_compare( PHP_VERSION, '5.4', '>=' ) && defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : 0;
		return wp_json_encode($this->get_data(), $pretty_print );
	}
	
	public function get_data()
	{
		global $wpgmza_pro_version;
		
		return  array(
			'creator'      => 'WPGoogleMapsPro',
			'version'      => $wpgmza_pro_version,
			'json_version' => Export::JSON_VERSION,
			
			'maps'         => $this->get_maps_data(),
			'categories'   => $this->get_categories_data(),
			'customfields' => $this->get_custom_fields_data(),
			'markers'      => $this->get_markers_data(),
			'circles'      => $this->get_circles_data(),
			'polygons'     => $this->get_polygons_data(),
			'polylines'    => $this->get_polylines_data(),
			'rectangles'   => $this->get_rectangles_data(),
			'datasets'     => $this->get_datasets_data()
		);
	}

	/**
	 * Get maps data.
	 *
	 * @global wpdb   $wpdb                WordPress database object.
	 * @global string $wpgmza_tblname_maps Maps database table name.
	 *
	 * @return array Maps database rows as associative array.
	 */
	private function get_maps_data() {

		global $wpdb;
		global $wpgmza_tblname_maps;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_maps` WHERE $where AND active = '0'", ARRAY_A );
		
		foreach($results as $key => $array)
		{
			$filter_custom_field_names = $wpdb->get_col("
				SELECT name 
				FROM $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS
				JOIN $WPGMZA_TABLE_NAME_CUSTOM_FIELDS ON field_id = id
				WHERE map_id=" . (int)$array['id']
			);
			
			if(empty($filter_custom_field_names))
				continue;
			
			$results[$key]['filter_custom_field_names'] = $filter_custom_field_names;
		}

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get categories data.
	 *
	 * @global wpdb   $wpdb                         WordPress database object.
	 * @global string $wpgmza_tblname_category_maps Categories maps database table name.
	 * @global string $wpgmza_tblname_categories    Categories database table name.
	 *
	 * @return array Categories database rows as associative array.
	 */
	private function get_categories_data() {

		if ( ! $this->export_args['categories'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_category_maps;
		global $wpgmza_tblname_categories;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : "`$wpgmza_tblname_category_maps`.`map_id` = 0 OR `$wpgmza_tblname_category_maps`.`map_id` IN (" . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `$wpgmza_tblname_categories`.*, `$wpgmza_tblname_category_maps`.`map_id` FROM `$wpgmza_tblname_categories` JOIN `$wpgmza_tblname_category_maps` ON `$wpgmza_tblname_categories`.`id` = `$wpgmza_tblname_category_maps`.`cat_id` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get custom fields data.
	 *
	 * @global wpdb   $wpdb                         WordPress database object.
	 * @global string $wpgmza_tblname_category_maps Categories maps database table name.
	 * @global string $wpgmza_tblname_categories    Categories database table name.
	 *
	 * @return array Custom fields database rows as associative array.
	 */
	private function get_custom_fields_data() {

		if ( ! $this->export_args['customfields'] ) {

			return array();

		}

		return new CustomFields();
		
	}

	/**
	 * Get markers data.
	 *
	 * @global wpdb   $wpdb           WordPress database object.
	 * @global string $wpgmza_tblname Markers database table name.
	 *
	 * @return array Markers database rows as associative array.
	 */
	private function get_markers_data() {

		if ( ! $this->export_args['markers'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname;
		global $WPGMZA_TABLE_NAME_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';
		
		// Pre-fetch all ratings
		
		$ratings = array();
		
		// TODO: Move this to Gold, use hooks
		if($this->export_args['ratings'])
		{
			$results = $wpdb->get_results("SELECT $WPGMZA_TABLE_NAME_RATINGS.*, marker_id FROM $WPGMZA_TABLE_NAME_RATINGS JOIN $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS ON rating_id=id WHERE $where", ARRAY_A);
			
			foreach($results as $arr)
			{
				$marker_id = $arr['marker_id'];
				unset($arr['marker_id']);
				
				if(!isset($ratings[$marker_id]))
					$ratings[$marker_id] = array();
				
				$ratings[$marker_id][] = $arr;
			}
		}
		
		// Fetch markers
		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname` WHERE $where", ARRAY_A );
		$results_cat = $wpdb->get_results( "SELECT * FROM `$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES`", ARRAY_A );
		
		foreach($results as $key => $marker)
		{
			// Unset binary column
			unset($results[$key]['latlng']);
			
			// Unset 
			$customMarkerFields = new CustomMarkerFields($marker['id']);
			$categories = array();
			
			if(count($customMarkerFields) > 0){
				$results[$key]['custom_fields_data'] = $customMarkerFields;
			}

			foreach($results_cat as $key1 => $category){
				if($results[$key]['id'] == $results_cat[$key1]['marker_id']){
					$results[$key]['category'] = 
					$categories[] = $results_cat[$key1]['category_id'];
				}

			}

			$results[$key]['category'] = implode(', ', $categories);
			
			// Get ratings
			if(!empty($ratings[$marker['id']])){
				$results[$key]['ratings'] = $ratings[$marker['id']];
			}
		}

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get circles data.
	 *
	 * @global wpdb   $wpdb                   WordPress database object.
	 * @global string $wpgmza_tblname_circles Circles database table name.
	 *
	 * @return array Circles database rows as associative array.
	 */
	private function get_circles_data() {

		if ( ! $this->export_args['circles'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza;
		global $wpgmza_tblname_circles;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results(
			"SELECT `id`, `map_id`, `name`, {$wpgmza->spatialFunctionPrefix}X(`center`) AS `centerX`, {$wpgmza->spatialFunctionPrefix}Y(`center`) AS `centerY`, `radius`, `color`, `opacity` 
			FROM `$wpgmza_tblname_circles` 
			WHERE $where", 
			ARRAY_A
		);

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get polygons data.
	 *
	 * @global wpdb   $wpdb                WordPress database object.
	 * @global string $wpgmza_tblname_poly Polygons database table name.
	 *
	 * @return array Polygons database rows as associative array.
	 */
	private function get_polygons_data() {

		if ( ! $this->export_args['polygons'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_poly;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_poly` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get polylines data.
	 *
	 * @global wpdb   $wpdb                     WordPress database object.
	 * @global string $wpgmza_tblname_polylines Polylines database table name.
	 *
	 * @return array Polylines database rows as associative array.
	 */
	private function get_polylines_data() {

		if ( ! $this->export_args['polylines'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_polylines;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_polylines` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get rectangles data.
	 *
	 * @global wpdb   $wpdb                      WordPress database object.
	 * @global string $wpgmza_tblname_rectangles Rectangles database table name.
	 *
	 * @return array Rectangles database rows as associative array.
	 */
	private function get_rectangles_data() {

		if ( ! $this->export_args['rectangles'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza;
		global $wpgmza_tblname_rectangles;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT `id`, `map_id`, `name`, {$wpgmza->spatialFunctionPrefix}X(`cornerA`) AS `cornerAX`, {$wpgmza->spatialFunctionPrefix}Y(`cornerA`) AS `cornerAY`, ST_X(`cornerB`) AS `cornerBX`, ST_Y(`cornerB`) AS `cornerBY`, `color`, `opacity` FROM `$wpgmza_tblname_rectangles` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}

	/**
	 * Get datasets data.
	 *
	 * @global wpdb   $wpdb                    WordPress database object.
	 * @global string $wpgmza_tblname_datasets Datasets database table name.
	 *
	 * @return array Datasets database rows as associative array.
	 */
	private function get_datasets_data() {

		if ( ! $this->export_args['datasets'] ) {

			return array();

		}

		global $wpdb;
		global $wpgmza_tblname_datasets;

		$where = empty( $this->export_args['maps'] ) ? '1=1' : '`map_id` IN (' . implode( ',', $this->export_args['maps'] ) . ')';

		$results = $wpdb->get_results( "SELECT * FROM `$wpgmza_tblname_datasets` WHERE $where", ARRAY_A );

		return empty( $results ) ? array() : $results;

	}
}
