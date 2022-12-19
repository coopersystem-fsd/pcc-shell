<?php
/**
 * WP Google Maps Pro Import / Export API
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;

/**
 * Import and export classes.
 */

$path = plugin_dir_path( __FILE__ );

require_once( $path . 'class.import.php' );
require_once( $path . 'class.import-csv.php' );
require_once( $path . 'class.import-airtables.php' );
require_once( $path . 'class.import-gpx.php' );
require_once( $path . 'class.import-json.php' );
require_once( $path . 'class.import-geojson.php' );
require_once( $path . 'class.import-kml.php' );
require_once( $path . 'class.export.php' );
require_once( $path . 'class.backup.php' );

add_action( 'admin_init', 'WPGMZA\\load_advanced_menu_page_hooks' );
/**
 * Attach actions to load-{page} hook for the Advanced menu page.
 */
function load_advanced_menu_page_hooks() {

	add_action( 'load-' . sanitize_title( __( 'Maps', 'wp-google-maps' ) ) . '_page_wp-google-maps-menu-advanced', 'WPGMZA\\import_export_download' );

}

/**
 * Export downloading processing.
 */
function import_export_download() {

	// Export download.
	if ( wpgmza_user_can_edit_maps() && isset( $_GET['action'], $_GET['export_nonce'] ) &&
	     wp_verify_nonce( $_GET['export_nonce'], 'wpgmza_export_file' ) && 'export_json' === $_GET['action'] ) {

		$export_args = array(
			'maps'			=> isset( $_GET['maps'] ) ? explode( ',', $_GET['maps'] ) : array(),
			'categories'	=> isset( $_GET['categories'] ) ? true : false,
			'customfields'	=> isset( $_GET['customfields'] ) ? true : false,
			'ratings'		=> isset( $_GET['ratings'] ) ? true : false,
			'markers'		=> isset( $_GET['markers'] ) ? true : false,
			'circles'		=> isset( $_GET['circles'] ) ? true : false,
			'polygons'		=> isset( $_GET['polygons'] ) ? true : false,
			'polylines'		=> isset( $_GET['polylines'] ) ? true : false,
			'rectangles'	=> isset( $_GET['rectangles'] ) ? true : false,
			'datasets'		=> isset( $_GET['datasets'] ) ? true : false
		);

		$export = new Export( $export_args );
		$export->download();
		die();

	}

	wp_enqueue_script( 'wp-util' );
	wp_enqueue_script( 'jquery-ui-slider' );

}

add_filter( 'wp_check_filetype_and_ext', 'WPGMZA\\import_wp_check_filetype_and_ext', 10, 4 );
function import_wp_check_filetype_and_ext($types, $file, $filename, $mimes)
{
	$ext = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
	
	switch($ext)
	{
		case 'json':
			
			$types['ext'] = 'json';
			$types['type'] = 'application/json';
			
			break;
		
		case 'csv':
			
			$types['ext'] = 'json';
			$types['type'] = 'application/json';
			
			break;
			
		case 'geojson':
			
			$types['ext'] = 'geojson';
			$types['type'] = 'application/json';
			
			break;
		case 'kml':
		case 'gpx':
			
			$types['ext'] = $ext;
			$types['type'] = 'application/xml';
			
			break;
		
		default:
			break;
	}
	
	return $types;
}

add_action( 'wp_ajax_wpgmza_import_upload', 'WPGMZA\\import_ajax_handle_upload' );
/**
 * Import AJAX handle upload file.
 */
function import_ajax_handle_upload() {
	
	add_filter('upload_mimes', 'WPGMZA\\import_mimes');
	
	if ( ! wpgmza_user_can_edit_maps() || ! current_user_can( 'upload_files' ) ) {

		wp_send_json_error( __( "You don't have permission to upload files.", 'wp-google-maps' ) );

	}

	if ( ! isset( $_FILES['wpgmaps_import_file'] ) || ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file upload or failed security check.', 'wp-google-maps' ) );

	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {

		require_once( ABSPATH . 'wp-admin/includes/file.php' );

	}

	$overrides = array(
		'test_form' => false,
		'mimes'     => import_mimes(),
	);

	$upload = wp_handle_upload( $_FILES['wpgmaps_import_file'], $overrides );

	if ( isset( $upload['error'] ) ) {

		wp_send_json_error( $upload['error'] );

	}

	$id = wp_insert_attachment( array(
		'post_title'     => basename( $upload['file'] ),
		'post_content'   => $upload['url'],
		'post_mime_type' => $upload['type'],
		'guid'           => $upload['url'],
		'context'        => 'wpgmaps-import',
		'post_status'    => 'private',
	), $upload['file'] );

	if ( $id > 0 ) {

		wp_send_json_success( array(
			'id'    => $id,
			'title' => basename( $upload['file'] ),
		) );

	}

	wp_send_json_error( __( 'Unable to add file to database.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmza_import_delete', 'WPGMZA\\import_ajax_handle_delete' );
/**
 * Import AJAX delete file handle.
 */
function import_ajax_handle_delete() {

	if ( ! wpgmza_user_can_edit_maps() || ! isset( $_POST['import_id'], $_POST['wpgmaps_security'] ) ||
	     ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file specified or failed security check.', 'wp-google-maps' ) );

	}

	$id = absint( $_POST['import_id'] );

	if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

		wp_send_json_error( __( 'Deletion not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

	}

	wp_delete_attachment( $id, true );

	wp_send_json_success( array(
		'id' => $id,
	) );
}

add_action( 'wp_ajax_wpgmza_import_file_options', 'WPGMZA\\import_ajax_file_options' );
/**
 * Import AJAX retrieve options html for import file.
 */
function import_ajax_file_options() {

	if ( ! empty( $_POST['schedule_id'] ) ) {

		$import_schedule = get_option( 'wpgmza_import_schedule' );
		$import_options = $import_schedule[ $_POST['schedule_id'] ]['options'];
		$import_options['start'] = get_date_from_gmt( date( 'Y-m-d H:i:s', $import_schedule[ $_POST['schedule_id'] ]['start'] ), 'Y-m-d' );
		$import_options['interval'] = $import_schedule[ $_POST['schedule_id'] ]['interval'];

		if ( ! empty( $import_schedule[ $_POST['schedule_id'] ]['import_id'] ) ) {

			$_POST['import_id'] = $import_schedule[ $_POST['schedule_id'] ]['import_id'];

		}

		if ( ! empty( $import_schedule[ $_POST['schedule_id'] ]['import_url'] ) ) {

			$_POST['import_url'] = $import_schedule[ $_POST['schedule_id'] ]['import_url'];

		}
	} else {

		$import_options = array();

	}

	if(!wpgmza_user_can_edit_maps() ||
		!isset( $_POST['wpgmaps_security'] ) ||
		!wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ))
	{
		wp_send_json_error( __( 'Security check failed.', 'wp-google-maps' ) );
	}
	
	if(
		!isset( $_POST['import_id'] ) && 
		!isset( $_POST['import_url'] ) &&
		!isset( $_POST['import_integration'] )
		)
	{
		wp_send_json_error( __( 'No file, URL or integration specified.', 'wp-google-maps' ) );
	}

	$import_mimes = import_mimes();

	if ( ! empty( $_POST['import_id'] ) && is_numeric( $_POST['import_id'] ) ) {

		$id = absint( $_POST['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			wp_send_json_error( __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $_POST['import_url'] ) ) {

		$import_file      = '';
		$import_file_url  = $_POST['import_url'];
		$extension        = pathinfo( $import_file_url, PATHINFO_EXTENSION );
		$google_sheets_id = array();

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$import_file_url = "https://docs.google.com/spreadsheets/d/{$google_sheets_id[1]}/gviz/tq?tqx=out:csv";
			$extension = 'csv';

		} elseif (strpos($import_file_url, 'api.airtable') !== FALSE ) { 
			// Airtable Integration
			$airtable_api_key = !empty($_POST['import_airtable_api_key']) ? trim($_POST['import_airtable_api_key']) : '';
			$import_file_url = trim($import_file_url) . "?api_key=" . $airtable_api_key;
			$extension = 'airtables'; 
		} else {
			switch(strtolower($extension)){
				case "csv":
				case "gpx":
				case "kml":
					// TODO: Really should fetch the head and test the content-type
					$extension = strtolower($extension);
					break;
				default:
					$extension = "json";	// Assume JSON
					break;
			}
		}
		
	}
	
	if(!empty( $extension ) || isset($_POST['import_integration']) ) {
		
		if(isset($_POST['import_integration']))
			$import_class = stripslashes( $_POST['import_integration'] );
		else
			$import_class = 'WPGMZA\\Import' . strtoupper( $extension );
		
		if ( class_exists( $import_class ) ) {
			
			$import = null;

			try {
				
				$import = new $import_class( $import_file, $import_file_url, $import_options );
				$import->prepare();

				$options_html = $import->admin_options();
				$notices_html = $import->get_admin_notices();
				
				wp_send_json_success( array(
					'id'			=> empty( $id ) ? 0 : $id,
					'url'			=> empty( $import_file_url ) ? '' : $import_file_url,
					'options_html'	=> $options_html,
					'notices_html'	=> $notices_html
				) );

			} catch ( \Exception $e ) {
				
				$response = array(
					'message'	=> $e->getMessage(),
				);
				
				if($import)
				{
					$response['log'] = $import->getLogText();
				
					if($import->getLoggedResponse())
					{
						$response['response'] = htmlentities($import->getLoggedResponse());
						$response['rawResponse'] = $import->getLoggedResponse();
					}
				}

				wp_send_json_error( $response );

			}
		}
	}

	wp_send_json_error( __( 'Unable to import file.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmza_import', 'WPGMZA\\import_ajax_import' );
/**
 * Import AJAX do import.
 */
function import_ajax_import() {

	if ( ! wpgmza_user_can_edit_maps() || ( ! isset( $_POST['import_id'] ) && ! isset( $_POST['import_url'] ) ) ||
	     ! isset( $_POST['wpgmaps_security'] ) || ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_mimes = import_mimes();

	if ( ! empty( $_POST['import_id'] ) && is_numeric( $_POST['import_id'] ) ) {

		$id = absint( $_POST['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			wp_send_json_error( __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $_POST['import_url'] ) ) {

		$import_file      = '';
		$import_file_url  = esc_url_raw( $_POST['import_url'] );
		$extension        = pathinfo( $import_file_url, PATHINFO_EXTENSION );
		$google_sheets_id = array();

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$import_file_url = "https://docs.google.com/spreadsheets/d/{$google_sheets_id[1]}/gviz/tq?tqx=out:csv";
			$extension = 'csv';

		} else if(strpos($import_file_url, 'api.airtable') !== FALSE ) { 
			// Airtable Integration
			$import_file_url = trim($_POST['import_url']);
			$extension = 'airtables';

		} else if($extension == 'php'){
			$extension = 'json';	// Assume JSON
		}
	}

	if ( ! empty( $extension ) /*&& array_key_exists( strtolower( $extension ), $import_mimes )*/ ) {

		$import_class = 'WPGMZA\\Import' . strtoupper( $extension );

		if ( class_exists( $import_class ) ) {

			try {

				$notices = array();

				$import = new $import_class( $import_file, $import_file_url, $_POST['options'] );
				
				$import->prepare();
				$import->import();

				$delete = 0;

				if ( ! empty( $id ) && isset( $_POST['options']['delete'] ) ) {

					wp_delete_attachment( $id, true );
					$delete = 1;

				}

				wp_send_json_success( array(
					'id'  => empty( $id ) ? 0 : $id,
					'url' => empty( $import_file_url ) ? '' : $import_file_url,
					'del' => $delete,
					'notices' => $import->get_admin_notices()
				) );

			} catch ( \Exception $e ) {

				wp_send_json_error( $e->getMessage() );

			}
		}
	}

	wp_send_json_error( __( 'Unable to import file.', 'wp-google-maps' ) );

}

add_action( 'wpgmza_import_cron', 'WPGMZA\\import_cron_import' );
/**
 * Import CRON.
 *
 * @param string $schedule_id Schedule id to import.
 */
function import_cron_import( $schedule_id ) {

	$import_schedule = get_option( 'wpgmza_import_schedule' );

	if ( ! isset( $import_schedule[ $schedule_id ] ) ) {

		wp_clear_scheduled_hook( 'wpgmza_import_cron', array( $schedule_id ) );
		return;

	}

	$import_schedule[ $schedule_id ]['last_run_message'] = __( 'Last Run', 'wp-google-maps' ) . ': ' . current_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . ' ';

	$import_mimes = import_mimes();

	if ( ! empty( $import_schedule[ $schedule_id ]['import_id'] ) && is_numeric( $import_schedule[ $schedule_id ]['import_id'] ) ) {

		$id = absint( $import_schedule[ $schedule_id ]['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			$import_schedule[ $schedule_id ]['last_run_message'] .= __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' );
			update_option( 'wpgmza_import_schedule', $import_schedule );
			return;

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $import_schedule[ $schedule_id ]['import_url'] ) ) {

		$import_file      = '';
		$import_file_url  = esc_url_raw( $import_schedule[ $schedule_id ]['import_url'] );
		$extension        = pathinfo( $import_file_url, PATHINFO_EXTENSION );
		$google_sheets_id = array();

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$import_file_url = "https://docs.google.com/spreadsheets/d/{$google_sheets_id[1]}/gviz/tq?tqx=out:csv";
			$extension = 'csv';

		} else if (strpos($import_file_url, 'api.airtable') !== FALSE ) { 
			// Airtable Integration
			$import_file_url = trim($import_schedule[ $schedule_id ]['import_url']);
			$extension = 'airtables';
		}
		
		if(empty($extension)){
			$extension = 'json';
		}
		
	}
	
	if(empty($extension))
		$extension = 'json';	// Assume JSON

	if ( ! empty( $extension ) /*&& array_key_exists( strtolower( $extension ), $import_mimes )*/ ) {

		$import_class = 'WPGMZA\\Import' . strtoupper( $extension );

		if ( class_exists( $import_class ) ) {
			
			$import = null;

			try {

				set_time_limit( 1200 );
				
				$import = new $import_class( $import_file, $import_file_url, $import_schedule[ $schedule_id ]['options'] );
				
				$import->prepare();
				$import->import();

				$import_schedule[ $schedule_id ]['last_run_message'] .= __( 'Import completed.', 'wp-google-maps' );
				
				$import_schedule[ $schedule_id ]['last_log'] = $import->getLogText();
				$import_schedule[ $schedule_id ]['last_response'] = $import->getLoggedResponse();
				
				update_option( 'wpgmza_import_schedule', $import_schedule );
				
				return;

			} catch ( \Exception $e ) {

				$import_schedule[ $schedule_id ]['last_run_message'] .= $e->getMessage();
				
				$import_schedule[ $schedule_id ]['last_log'] = $import->getLogText();
				$import_schedule[ $schedule_id ]['last_response'] = $import->getLoggedResponse();
				
				update_option( 'wpgmza_import_schedule', $import_schedule );
				
				return;

			}
			
			
		}
		
	}

	$import_schedule[ $schedule_id ]['last_run_message'] .= __( 'Unable to import file.', 'wp-google-maps' );
	update_option( 'wpgmza_import_schedule', $import_schedule );

}

add_action('admin_init', function() {
	
	global $wpgmza;
	
	if(!$wpgmza || !$wpgmza->isUserAllowedToEdit())
		return;
	
	if(!isset($_GET['action']))
		return;
	
	if($_GET['action'] != 'view-import-log' && $_GET['action'] != 'view-import-response')
		return;
	
	$schedule_id = $_GET['schedule_id'];
	
	$import_schedules = get_option('wpgmza_import_schedule');
	
	if(empty($import_schedules[ $schedule_id ]))
	{
		http_response_code(404);
		echo "Invalid Schedule ID";
		exit;
	}
	
	header('Content-type: text/plain');
	
	if($_GET['action'] == 'view-import-log')
	{
		echo preg_replace('/<br(\/?)>/', "\r\n", $import_schedules[$schedule_id]['last_log']);
	}
	else
	{
		echo $import_schedules[$schedule_id]['last_response'];
	}
	
	exit;
	
});

/**
 * Import allowed mime types.
 */
function import_mimes() {

	return array( 
		'txt|asc|c|cc|h|srt|csv|json'	=> 'text/plain',
		'csv' 		 					=> 'text/csv',
		'gpx' 		 					=> 'application/xml',
		// 'json'		 					=> 'application/json',
		'kml' 		 					=> 'application/xml',
		'geojson'						=> "text/plain"
	);

}

add_action( 'wpgmza_admin_advanced_options_tabs', 'WPGMZA\\import_export_admin_tabs' );
/**
 * Import/export admin page tabs.
 */
function import_export_admin_tabs() {

	?>
	<li><a href="#import-tab"><?php esc_html_e( 'Import' , 'wp-google-maps' ); ?></a></li>
	<li><a href="#schedule-tab"><?php esc_html_e( 'Schedule', 'wp-google-maps' ); ?></a></li>
	<li><a href="#export-tab"><?php esc_html_e( 'Export' , 'wp-google-maps' ); ?></a></li>
	<li><a href="#backups-tab"><?php esc_html_e( 'Backups' , 'wp-google-maps' ); ?></a></li>
	<?php

}

add_filter( 'cron_schedules', 'WPGMZA\\import_cron_schedules' );
/**
 * Adds custom cron schedules.
 *
 * @param array $schedules An array of non-default cron schedules.
 * @return array Filtered array of non-default cron schedules.
 */
function import_cron_schedules( $schedules ) {

	$schedules['weekly'] = array(
		'interval' => WEEK_IN_SECONDS,
		'display'  => __( 'Once Weekly', 'wp-google-maps' ),
	);

	$schedules['monthly'] = array(
		'interval' => MONTH_IN_SECONDS,
		'display'  => __( 'Once Monthly', 'wp-google-maps' ),
	);

	return $schedules;

}

/**
 * Get maps list helper function.
 *
 * @global wpdb   $wpdb                WordPress database class.
 * @global string $wpgmza_tblname_maps Maps database table name.
 *
 * @param string     $context  Context of the list, used to create ids and classes.
 * @param array|bool $selected Array of selected map ids.
 * @return array|string Table rows and columns of maps. Array of map ids if $content passed as 'ids'.
 */
function import_export_get_maps_list( $context, $selected = false ) {

	static $maps = null;

	if ( null === $maps ) {

		global $wpdb;
		global $wpgmza_tblname_maps;

		$maps = $wpdb->get_results( "SELECT `id`, `map_title` FROM `$wpgmza_tblname_maps` WHERE `active`=0 ORDER BY `id` DESC" );

	}

	if ( empty( $maps ) ) {

		return 'ids' === $context ? array() : '';

	}

	$ret = 'ids' === $context ? array() : '';

	$context = sanitize_html_class( $context );

	foreach ( $maps as $map ) {

		$id = intval( $map->id );

		if ( 'ids' === $context ) {

			$ret[] = $id;

		} else {

			$title = esc_html( stripslashes( $map->map_title ) );
			$ret .= "<tr style='display:block;width:100%;'><td style='width:2.2em;'><div class='switch'><input id='maps_{$context}_{$id}' type='checkbox' value='{$id}' class='maps_{$context} cmn-toggle cmn-toggle-round-flat' " . ( false === $selected ? 'checked' : ( is_array( $selected ) && in_array( $id, $selected, true ) ? 'checked' : '' ) ) . "><label for='maps_{$context}_{$id}'></label></div></td><td style='width:80px;'>{$id}</td><td>{$title}</td></tr>";
		}
	}

	return $ret;

}

add_action( 'wp_ajax_wpgmza_import_schedule', 'WPGMZA\\import_ajax_schedule' );
/**
 * AJAX schedule an import CRON event.
 */
function import_ajax_schedule() {

	if ( ! wpgmza_user_can_edit_maps() || ( ! isset( $_POST['import_id'] ) && ! isset( $_POST['import_url'] ) ) || 
	     ! isset( $_POST['wpgmaps_security'], $_POST['options'] ) || ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No file specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_mimes = import_mimes();

	if ( is_numeric( $_POST['import_id'] ) ) {

		$id = absint( $_POST['import_id'] );

		if ( $id < 1 || get_post_meta( $id, '_wp_attachment_context', true ) !== 'wpgmaps-import' ) {

			wp_send_json_error( __( 'Importing not allowed. File is not a valid WP Google Maps import upload.', 'wp-google-maps' ) );

		}

		$import_file     = get_attached_file( $id );
		$import_file_url = wp_get_attachment_url( $id );
		$extension       = pathinfo( $import_file, PATHINFO_EXTENSION );

	} elseif ( ! empty( $_POST['import_url'] ) ) {

		$import_file     = '';
		$import_file_url = esc_url_raw( $_POST['import_url'] );
		$extension       = pathinfo( $import_file_url, PATHINFO_EXTENSION );

		if ( preg_match( '@/spreadsheets/d/([a-zA-Z0-9-_]+)@', $import_file_url, $google_sheets_id ) ) {

			$extension = 'csv';

		} else if (strpos($import_file_url, 'api.airtable') !== FALSE ) { 
			// Airtable Integration
			$extension = 'airtables';
		} else {
			$extension = 'json';	// Assume JSON
		}
	}

	if ( ! empty( $extension ) /*&& array_key_exists( strtolower( $extension ), $import_mimes )*/ ) {

		$import_schedule = get_option( 'wpgmza_import_schedule' );

		if ( empty( $import_schedule ) || ! is_array( $import_schedule ) ) {

			$import_schedule = array();

		}

		if ( ! empty( $_POST['schedule_id'] ) ) {

			$schedule_id = $_POST['schedule_id'];

		} else {

			$schedule_id = md5( ( ! empty( $import_file ) ? $import_file : ( ! empty( $import_file_url ) ? $import_file_url : '' ) ) . time() );

		}

		$start    = get_gmt_from_date( $_POST['start'], 'U' );
		$interval = sanitize_text_field( $_POST['interval'] );
		$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );
		
		if ( false !== $next_run ) {

			if ( ! isset( $import_schedule[ $schedule_id ]['start'], $import_schedule[ $schedule_id ]['interval'] ) ||
				 $import_schedule[ $schedule_id ]['start'] !== $start || $import_schedule[ $schedule_id ]['interval'] !== $interval ) {

				wp_clear_scheduled_hook( 'wpgmza_import_cron', array( $schedule_id ) );
				$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

			}
		}

		if ( isset( $import_schedule[ $schedule_id ] ) ) {

			unset( $import_schedule[ $schedule_id ] );

		}

		$import_schedule = array(
			$schedule_id => array(
				'start'      => $start,
				'interval'   => $interval,
				'title'      => sanitize_text_field( ! empty( $import_file ) ? basename( $import_file ) : $import_file_url ),
				'options'    => $_POST['options'],
				'import_id'  => ! empty( $id ) ? $id : 0,
				'import_url' => $import_file_url,
		) ) + $import_schedule;

		update_option( 'wpgmza_import_schedule', $import_schedule );

		if ( false === $next_run ) {

			wp_schedule_event( $import_schedule[ $schedule_id ]['start'], $import_schedule[ $schedule_id ]['interval'], 'wpgmza_import_cron', array( $schedule_id ) );
			$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		}

		if ( ! empty( $next_run ) ) {

			$import_schedule[ $schedule_id ]['next_run'] = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_run ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
			$import_schedule[ $schedule_id ]['schedule_id'] = $schedule_id;
			wp_send_json_success( $import_schedule[ $schedule_id ] );

		}
	} // End if().

	wp_send_json_error( __( 'Unable to schedule import.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmza_import_delete_schedule', 'WPGMZA\\import_ajax_delete_schedule' );
/**
 * AJAX delete import CRON schedule.
 */
function import_ajax_delete_schedule() {

	if ( ! wpgmza_user_can_edit_maps() || ! isset( $_POST['schedule_id'], $_POST['wpgmaps_security'] ) ||
	     ! wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ) ) {

		wp_send_json_error( __( 'No scheduled import specified or failed security check.', 'wp-google-maps' ) );

	}

	$import_schedule = get_option( 'wpgmza_import_schedule' );

	if ( ! isset( $import_schedule[ $_POST['schedule_id'] ] ) ) {

		wp_send_json_error( __( 'Scheduled import not found.', 'wp-google-maps' ) );

	}

	wp_clear_scheduled_hook( 'wpgmza_import_cron', array( $_POST['schedule_id'] ) );
	$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $_POST['schedule_id'] ) );

	if ( false === $next_run ) {

		unset( $import_schedule[ $_POST['schedule_id'] ] );
		update_option( 'wpgmza_import_schedule', $import_schedule );
		wp_send_json_success( array(
			'schedule_id' => $_POST['schedule_id'],
		) );

	}

	wp_send_json_error( __( 'Unable to remove scheduled import.', 'wp-google-maps' ) );

}

add_action( 'wp_ajax_wpgmaps_get_import_progress', 'WPGMZA\\import_get_progress' );
/**
 * AJAX get import progress.
 */
function import_get_progress() {

	@session_start();

	$key = 'wpgmza_import_progress_' . $_POST['wpgmaps_security'];
	$json = (object) array( 'progress' => 0.0 );

	if ( isset( $_SESSION[ $key ] ) ) {

		$json = $_SESSION[ $key ];

	}

	session_write_close();
	wp_send_json_success( $json );

}

/**
 * Get import CRON schedule.
 *
 * @return array Array of scheduled imports.
 */
function import_get_schedule() {

	$import_schedule = get_option( 'wpgmza_import_schedule' );

	if ( empty( $import_schedule ) || ! is_array( $import_schedule ) ) {

		return array();

	}

	foreach ( $import_schedule as $schedule_id => $schedule ) {

		$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		if ( false === $next_run ) {

			wp_schedule_event( $schedule['start'], $schedule['interval'], 'wpgmza_import_cron', array( $schedule_id ) );
			$next_run = wp_next_scheduled( 'wpgmza_import_cron', array( $schedule_id ) );

		}

		if ( ! empty( $next_run ) ) {

			$next_run = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_run ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );

		}

		$import_schedule[ $schedule_id ]['next_run'] = $next_run;

	}

	return $import_schedule;

}



/**
 * Script and localized variables to pass to page
 */
add_action('admin_enqueue_scripts', function() {
	
	global $wpgmza;
	
	$version = 'null';
	
	if(!empty($wpgmza) && method_exists($wpgmza, 'getProVersion'))
		$version = $wpgmza->getProVersion();
	
	wp_enqueue_script('wpgmza-import-export-page', plugin_dir_url(dirname(__DIR__)) . 'js/import-export-page.js', $version);
	
	wp_enqueue_script('exif-js', plugin_dir_url(dirname(__DIR__)) . 'lib/exif.js', $version);
	
});

/**
 * Data to pass to JavaScript
 */
add_filter('wpgmza_plugin_get_localized_data', function($arr) {
	
	return array_merge($arr, array(
	
		'import_security_nonce'		=> wp_create_nonce('wpgmaps_import'),
		'export_security_nonce'		=> wp_create_nonce('wpgmza_export_file')
	
	));
	
});

/**
 * Localized strings to pass to page
 */
add_filter('wpgmza_localized_strings', function($arr) {
	
	return array_merge($arr, array(
	
		'please_select_a_file_to_upload'			=> __('Please select a file to upload.', 'wp-google-maps'),
		'import_reservedwordsfix'					=> __('Import', 'wp-google-maps'),
		'delete_reservedwordsfix'					=> __('Delete', 'wp-google-maps'),
		'back_to_import_data' 						=> __('Back to Import Data', 'wp-google-maps'),
		'are_you_sure_you_wish_to_delete_this_file' => __('Are you sure you wish to delete this file?', 'wp-google-maps' ),
		'file_deleted'								=> __('File deleted.', 'wp-google-maps'),
		'please_enter_a_url_to_import_from'			=> __('Please enter a URL to import from.', 'wp-google-maps'),
		'back_to_import_data'						=> __('Back to Import Data', 'wp-google-maps'),
		'loading_import_options'					=> __( 'Loading import options...', 'wp-google-maps' ),
		'are_you_sure_you_wish_to_delete_this_scheduled_import' => __( 'Are you sure you wish to delete this scheduled import?', 'wp-google-maps' ),
		'scheduled_import_deleted'					=> __( 'Scheduled import deleted.', 'wp-google-maps' ),
		'please_select_at_least_one_map_to_export'	=> __( 'Please select at least one map to export.', 'wp-google-maps' )
	));
	
});

add_action( 'wpgmza_admin_advanced_options', 'WPGMZA\\import_export_admin_options' );

/**
 * Import/export admin page options.
 */
function import_export_admin_options() {

	$import_mimes = import_mimes();
	$import_accepts_attr = '';
	$import_accepts      = __( 'Accepts', 'wp-google-maps' ) . ': ';
	foreach ( $import_mimes as $ext => $mime ) {
		$import_accepts_attr .= "$mime,.$ext,";
		$import_accepts      .= "*.$ext, ";
	}
	$import_accepts_attr = rtrim( $import_accepts_attr, ',' );
	$import_accepts      = rtrim( $import_accepts, ', ' );

	$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
	$size  = size_format( $bytes );
	
	$import_files = new \WP_Query( array( 
		'post_type'      => 'attachment',
		'meta_key'       => '_wp_attachment_context',
		'meta_value'     => 'wpgmaps-import',
		'posts_per_page' => - 1,
	) );
	
	$import_schedule = import_get_schedule();
	
	$maps_html = import_export_get_maps_list( 'export' );
	
	// Start building HTML document
	$document = new DOMDocument();
	$document->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/import-export/import-export.html.php');
	
	$document->populate(array(
		'max_upload_size'		=> $size,
		'import_accepts'		=> $import_accepts
	));
	
	// Accepts attribute on input
	if($input = $document->querySelector('input[name="wpgmaps_import_file"]'))
		$input->setAttribute('accept', $import_accepts_attr . ',.json');
	
	// Hide file list if no items are present
	if(($el = $document->querySelector('#wpgmaps_import_file_list')) && $import_files->found_posts < 1)
		$el->setInlineStyle('display', 'none');
	
	// Add import file table rows
	$table = $document->querySelector('#wpgmap_import_file_list_table');
	$template = $table->querySelector('tbody>tr');
	$container = $template->parentNode;
	$template->remove();
	
	foreach ( $import_files->posts as $import_file )
	{
		$tr = $template->cloneNode(true);
		
		$tr->populate($import_file);
		$tr->setAttribute('id', 'import-list-item-' . $import_file->ID);
		
		foreach($tr->querySelectorAll('a') as $a)
			$a->setAttribute('data-import-id', $import_file->ID);
		
		$container->appendChild($tr);
	}
	
	// Hide import schedule if no schedules are present
	if(($el = $document->querySelector('#wpgmaps_import_schedule_list')) && empty($import_schedule))
		$el->setInlineStyle('display', 'none');
	
	// Add import schedule table rows
	$table = $document->querySelector('#wpgmap_import_schedule_list_table');
	$template = $table->querySelector('tbody>tr');
	$container = $template->parentNode;
	$template->remove();
	
	foreach ( $import_schedule as $schedule_id => $schedule )
	{
		$tr = $template->cloneNode(true);
		
		if ( empty( $schedule['next_run'] ) )
			$schedule['status'] = __('No schedule found', 'wp-google-maps');
		else
			$schedule['status'] = __('Next schedule run', 'wp-google-maps') . ': ' . $schedule['next_run'];
		
		if ( ! empty( $schedule['last_run_message'] ) )
			$schedule['status'] .= " " . $schedule['last_run_message'];
		
		$tr->populate($schedule);
		$tr->setAttribute('id', $schedule_id);
		
		foreach($tr->querySelectorAll('a') as $a)
			$a->setAttribute('data-schedule-id', $schedule_id);
		
		$container->appendChild($tr);
	}
	
	// Import target map panel
	if($panel = $document->querySelector('#wpgmza-import-target-map-panel'))
	{
		$table = $panel->querySelector('.wpgmza-listing');
		
		$maps = import_export_get_maps_list( 'export' );
		
		if ( empty( $maps ) )
		{
			$panel->appendText(__('No maps available for export.', 'wp-google-maps'));
			$table->remove();
		}
		else
		{
			$panel->querySelector('tbody')->import($maps_html);
		}
	}

	$document = apply_filters('wpgmza_import_export_document', $document);
	
	// JPEG map select
	if($panel = $document->querySelector('#import_from_bulk_jpeg'))
	{
		$select = new MapSelect('map_id');
		$select = $document->importNode($select->querySelector('select'), true);
		
		$panel->prepend($select);
	}
	
	echo $document->html;


}


add_action( 'wpgmza_import_export_document', 'WPGMZA\\import_export_backup_panel' );

/**
 * Import/export admin page options.
 */
function import_export_backup_panel($document) {
	$backup = new Backup();

	$lastNotice = "";
	if(!empty($_GET['backup_nonce'])){
		if(wp_verify_nonce($_GET['backup_nonce'], 'wpgmza_backup_nonce')){
			if(!empty($_GET['delete_backup'])){
				if($backup->deleteBackup($_GET['delete_backup'])){
					$lastNotice = __("Backup deleted", "wp-google-maps");
				}
			} else if(!empty($_GET['create_backup'])){
				$backup->createBackup();
				$lastNotice = __("Backup created", "wp-google-maps");
			}
		}
	}

	if(!empty($lastNotice)){
		$document->querySelector('.wpgmza_backup_notice')->import($lastNotice);
	} else {
		$document->querySelector('.wpgmza_backup_notice')->remove();
	}

	$backupNonce = wp_create_nonce("wpgmza_backup_nonce");
	if($panel = $document->querySelector('#wpgmza_backups_content')){
		$backups = $backup->getBackupFiles();
		if(!empty($backups)){
			foreach ($backups as $file) {
				$backupRow = new DOMDocument();
				$backupRow->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/import-export/backup-item.html.php');
				
				$date = date('Y-m-d H:i');

				if(!empty($file['date'])){
					if(!empty($file['date']['year'])){
						$dateString = $file['date']['year'];
						if(!empty($file['date']['month'])){
							$dateString .= "-" . $file['date']['month'];
							if(!empty($file['date']['day'])){
								$dateString .= "-" . $file['date']['day'];

								if(!empty($file['date']['hour'])){
									$dateString .= " " . $file['date']['hour'];
									if(!empty($file['date']['min'])){
										$dateString .= ":" . $file['date']['min'];
									}
								}

								$date = date('j M, Y @ H:i', strtotime($dateString));
							}
						}

					}
				}

				$backupRow->populate(array(
					'title' => $file['pretty'],
					'date' => $date,
					'alias' => $file['flag_alias'] . " Backup"
				));

				$backupRow->querySelector('.download_btn')->setAttribute('href', $file['url']);
				$backupRow->querySelector('.delete_btn')->setAttribute('href', "?page=wp-google-maps-menu-advanced&delete_backup=" . str_replace(".json", "", $file['filename']) . "&backup_nonce=" . $backupNonce . "#backups-tab");

				$panel->import($backupRow);
			}
		}

		$document->querySelector('#wpgmza_new_backup_btn')->setAttribute('href', "?page=wp-google-maps-menu-advanced&create_backup=true&backup_nonce=" . $backupNonce . "#backups-tab");
	}

	return $document;
}