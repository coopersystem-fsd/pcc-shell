<?php

global $wpgmza_pro_version;
global $wpgmza_pro_string;

$wpgmza_pro_string = "pro";

if(!function_exists('wpgmza_require_once'))
{
	function wpgmza_require_once($filename)
	{
		if(!file_exists($filename))
			throw new Exception("Fatal error: wpgmza_require_once(): Failed opening required '$filename'");
		
		require_once($filename);
	}
}

add_action('admin_notices', 'wpgmza_show_ugm_incompatible_notice');
function wpgmza_show_ugm_incompatible_notice()
{
	global $wpgmza;
	global $wpgmza_ugm_version;
	
	if(empty($wpgmza_ugm_version))
		return;
	
	if(version_compare($wpgmza_ugm_version, '3.02', '>=') || $wpgmza->settings->engine != 'open-layers')
		return;
	
	?>
	<div class="notice notice-error">
		<p>
			<?php
			_e('<strong>WP Google Maps Pro:</strong> User Generated Markers add-on 3.01 and below is not compatible with OpenLayers. Please either switch engine to Google under Maps &rarr; Settings, or update User Generated Markers to 3.02 or above', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

add_action('admin_notices', 'wpgmza_show_gold_incompatible_notice');
function wpgmza_show_gold_incompatible_notice()
{
	global $wpgmza;
	global $wpgmza_gold_version;
	
	if(empty($wpgmza_gold_version))
		return;
	
	if(version_compare($wpgmza_gold_version, '4.11', '>=') || $wpgmza->settings->engine != 'open-layers')
		return;
	
	?>
	<div class="notice notice-error">
		<p>
			<?php
			_e('<strong>WP Google Maps Pro:</strong> Gold Add-on versions 4.11 and below are not compatible with OpenLayers. Please update to Gold 4.11 or above to use Gold features with the OpenLayers engine.', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

register_activation_hook(WPGMZA_PRO_FILE, function() {
	
	// NB: Would cause a fatal error because of dependencies
	//wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-plugin.php');
	// WPGMZA\ProPlugin::onActivate();
	
	require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-database.php');
		
	$db = new \WPGMZA\ProDatabase();
	$db->install();
	
});

register_deactivation_hook(WPGMZA_PRO_FILE, function() {
	
	// NB: Would cause a fatal error because of dependencies
	// wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-plugin.php');
	// WPGMZA\ProPlugin::onDeactivate();
	
});

add_action('plugins_loaded', function() {
	
	// Register Pro classes with auto-loader
	function wpgmza_pro_load()
	{
		wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.pro-plugin.php');
		
		global $wpgmza_auto_loader;
		
		if(!$wpgmza_auto_loader)
			return;
		
		$wpgmza_auto_loader->registerClassesInPath(plugin_dir_path(__FILE__) . 'includes/');
	}
	
	if(method_exists('WPGMZA\\Plugin', 'preloadIsInDeveloperMode') && WPGMZA\Plugin::preloadIsInDeveloperMode())
		wpgmza_pro_load();
	else
		try{
			wpgmza_pro_load();
		}catch(Exception $e) {
			
			add_action('admin_notices', function() use ($e) {
				
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong>
						<?php
						_e('WP Google Maps', 'wp-google-maps');
						?></strong>:
						<?php
						_e('The Pro add-on cannot be registered due to a fatal error. This is usually due to missing files. Please re-install the Pro add-on. Technical details are as follows: ', 'wp-google-maps');
						echo $e->getMessage();
						?>
					</p>
				</div>
				<?php
				
			});
			
		}
	
}, 1);

global $wpgmza_current_map_cat_selection;
global $wpgmza_current_map_shortcode_data;
global $wpgmza_current_map_type;

global $wpgmza_p;
global $wpgmza_t;
$wpgmza_p = true;
$wpgmza_t = "pro";

global $wpdb;

global $wpgmza_count;
$wpgmza_count = 0;

global $wpgmza_post_nonce;
$wpgmza_post_nonce = md5(time());

global $WPGMZA_TABLE_NAME_MARKERS;
$WPGMZA_TABLE_NAME_MARKERS = $wpdb->prefix . 'wpgmza';

global $wpdb;
global $wpgmza_tblname_datasets;
$wpgmza_tblname_datasets = $wpdb->prefix . "wpgmza_datasets";

global $wpgmza_tblname_circles;
$wpgmza_tblname_circles = $wpdb->prefix . "wpgmza_circles";

global $wpgmza_tblname_rectangles;
$wpgmza_tblname_rectangles = $wpdb->prefix . "wpgmza_rectangles";

/*global $WPGMZA_TABLE_NAME_CATEGORIES;
$WPGMZA_TABLE_NAME_CATEGORIES = $wpdb->prefix . 'wpgmza_categories';

global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES = $wpdb->prefix . 'wpgmza_markers_has_categories';*/

global $wpgmza_override;
$wpgmza_override = array();

global $wpgmza_shortcode_atts_by_map_id;
$wpgmza_shortcode_atts_by_map_id = array();

$plugin_dir_path = plugin_dir_path(__FILE__);

global $wpgmza_default_store_locator_radii;
$wpgmza_default_store_locator_radii = array(1,5,10,25,50,75,100,150,200,300);

wpgmza_require_once($plugin_dir_path . 'includes/compatibility/functions.legacy-pro.php');

// TODO: Favour autoloaders in the future
wpgmza_require_once($plugin_dir_path . 'includes/3rd-party-integration/class.wp-migrate-db-integration.php');
wpgmza_require_once($plugin_dir_path . 'includes/3rd-party-integration/class.acf.php');

wpgmza_require_once($plugin_dir_path . 'includes/class.category.php');

wpgmza_require_once($plugin_dir_path . "includes/legacy/page.legacy-import-export.php");

wpgmza_require_once($plugin_dir_path . "includes/page.categories.php");
wpgmza_require_once($plugin_dir_path . "includes/page.wizard.php");
wpgmza_require_once($plugin_dir_path . "includes/class.legacy-marker-listing.php");

wpgmza_require_once($plugin_dir_path . "includes/import-export/page.import-export.php");
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/page.custom-fields.php');

wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-fields.php');
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-marker-fields.php');
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-field-filter-widget.php');
wpgmza_require_once($plugin_dir_path . 'includes/custom-fields/class.custom-field-filter-controller.php');

wpgmza_require_once($plugin_dir_path . 'includes/class.pro-map.php');
wpgmza_require_once($plugin_dir_path . 'includes/class.pro-marker-filter.php');

// Google API Loader
if(!function_exists('wpgmza_enqueue_scripts'))
{
	function wpgmza_enqueue_scripts()
	{
		global $wpgmza_google_maps_api_loader;
		
		if(!class_exists('WPGMZA\\GoogleMapsAPILoader'))
			return;
		
		$wpgmza_google_maps_api_loader = new WPGMZA\GoogleMapsAPILoader();
		$wpgmza_google_maps_api_loader->registerGoogleMaps();
		
		if(isset($_GET['page']) && preg_match('/wp-google-maps/', $_GET['page']))
			$wpgmza_google_maps_api_loader->enqueueGoogleMaps();
	}
	
	add_action('wp_enqueue_scripts', 'wpgmza_enqueue_scripts');
	add_action('admin_enqueue_scripts', 'wpgmza_enqueue_scripts');
}

add_action('init', function() {
	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu' && isset($_GET['map_id']))
	{
		// NB: This is a temporary workaround to show notices from the map, it's done here because the map edit page renders after the admin_notices action. This will be moved
		WPGMZA\Map::createInstance($_GET['map_id']);
	}
});

add_action('admin_head', 'wpgmaps_upload_csv');
add_action('init', 'wpgmza_register_pro_version');

function wpgmaps_pro_activate() { 

    wpgmza_cURL_response_pro("activate");
    wpgmaps_handle_db_pro();
    if (function_exists("wpgmaps_handle_directory")) { wpgmaps_handle_directory(); }
	// Setup import schedules.
    WPGMZA\import_get_schedule();
}

function wpgmaps_pro_deactivate() {
	// Clear all import schedules.
	$crons = _get_cron_array();
	if ( ! empty( $crons ) ) {
		$unset_cron = false;
		foreach ( $crons as $timestamp => $cron ) {
			if ( isset( $crons[ $timestamp ]['wpgmza_import_cron'] ) ) {
				$unset_cron = true;
				unset( $crons[ $timestamp ]['wpgmza_import_cron'] );
				if ( empty( $crons[ $timestamp ] ) ) {
					unset( $crons[ $timestamp ] );
				}
			}
		}
		if ( $unset_cron ) {
			_set_cron_array( $crons );
		}
	}
	wpgmza_cURL_response_pro("deactivate");
}

function wpgmza_user_can_edit_maps() {

	$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

	if ( isset( $wpgmza_settings['wpgmza_settings_access_level'] ) ) {
		$access_level = $wpgmza_settings['wpgmza_settings_access_level'];
	} else {
		$access_level = "manage_options";
	}

	return current_user_can( $access_level );

}

function wpgmza_update_basic_v6_notice() {
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php
			_e('<strong>WP Google Maps Pro:</strong> Experiencing issues? We strongly recommend that you update WP Google Maps (Basic) to Version 8.0.0 in the plugins menu', 'wp-google-maps');
			?>
		</p>
	</div>
	<?php
}

function wpgmza_register_pro_version() {
    global $wpgmza_pro_version;
    global $wpgmza_pro_string;
    global $wpgmza_t;
    global $wpgmza_version;
	
	if(version_compare($wpgmza_version, '7.0.0', '<'))
		add_action('admin_notices', 'wpgmza_update_basic_v6_notice');
      
	// TODO: This should use the admin_post hooks
	if(wpgmza_user_can_edit_maps() && isset($_GET['action']))
	{
		switch($_GET['action']) {
			
			case 'wpgmza_csv_export':
				$export = new WPGMapsImportExport();
				$export->export_markers();
				break;
				
			case 'export_single_map':
				$export = new WPGMapsImportExport();
				$export->export_map( (int)$_GET['mid'] );
				break;
			
			case 'export_all_maps':
				$export = new WPGMapsImportExport();
				$export->export_map();
				break;
				
			case 'export_polygons':
				$export = new WPGMapsImportExport();
				$export->export_polygons();
				break;
			
			case 'export_polylines':
				$export = new WPGMapsImportExport();
				$export->export_polylines();
				break;
				
			case 'import_polylines':
				$export = new WPGMapsImportExport();
				$export->import_polylines();
				break;
				
			case 'import_polygons':
				$export = new WPGMapsImportExport();
				$export->import_polygons();
				break;
			
		}
	}

}

function wpgmza_pro_update_control()
{
	trigger_error("Deprecated as of 8.0.19");
}

/* deprecated from 6.02 */
//add_action('wp_enqueue_scripts','wpgmaps_user_styles_pro');
function wpgmaps_user_styles_pro() {
		global $short_code_active;
		if ($short_code_active) {
			/* only show styles on pages that contain the shortcode for the map */
			global $wpgmza_pro_version;
       		//wp_register_style( 'wpgmaps-style-pro', plugins_url('css/wpgmza_style_pro.css', __FILE__), array(), $wpgmza_pro_version);
       		//wp_enqueue_style( 'wpgmaps-style-pro' );


       	}
}

/**
 * @deprecated Since 8.0.10
 */ 
function wpgmaps_handle_db_pro() {

}

/*function wpgmza_pro_menu() {
	
    global $wpgmza_pro_version;
    global $wpgmza_p_version;
    global $wpgmza_post_nonce;
    global $wpgmza_tblname_maps;
    global $wpdb;
	global $wpgmza;
	
	if(!$wpgmza)
		return; // Bail out, we're running and older (incompatible) version of Basic

	$real_post_nonce = wp_create_nonce('wpgmza');

	wpgmza_require_once(plugin_dir_path(__FILE__) . 'includes/class.marker-library-dialog.php');
	
    $handle = 'avia-google-maps-api';
    $list = 'enqueued';
    if (wp_script_is( $handle, $list )) {
        wp_deregister_script('avia-google-maps-api');
    }
	
    if($_GET['action'] == "wizard")
	{
    	wpgmaps_wizard_layout();
    }
    
	if($_GET['action'] == 'new')
	{
		$map = WPGMZA\Map::createInstance();
		$url = admin_url("admin.php?page=wp-google-maps-menu&action=edit&map_id={$map->id}");
		
		echo "<p>";
		echo sprintf(__("Please click <a href='%s'>here</a> if you are not redirected within a few seconds", "wp-google-maps"), esc_attr($url));
		echo "</p>";
		
		echo "<script>window.location.href = '$url';</script>";
		
		return;
	}
	
    if (isset($_GET['map_id'])) {
        
        if (function_exists("wpgmaps_marker_permission_check")) { wpgmaps_marker_permission_check(); }
        if (function_exists("google_maps_api_key_warning")) { google_maps_api_key_warning(); }

		$mapEditPage = new WPGMZA\ProMapEditPage();
		echo $mapEditPage->html;
		
		return;
    }

}

/**
 * This function takes field data from POST and updates the marker field data with it
 * @return void
 */
function wpgmza_update_marker_custom_fields($marker_id, $field_data)
{
	$custom_fields = new WPGMZA\CustomMarkerFields($marker_id);
	
	for($i = 0; $i < count($_POST['custom_fields']); $i++)
	{
		$field_data = $_POST['custom_fields'][$i];
		$custom_fields->{$field_data['field_id']} = stripslashes($field_data['value']);
	}
}

function wpgmaps_action_callback_pro() {
    // NB: Deprecated as of 8.1.0
}

function wpgmza_return_pro_add_ons() {
    $wpgmza_ret = "";
    if (function_exists("wpgmza_register_gold_version")) { $wpgmza_ret .= wpgmza_gold_addon_display(); } else { $wpgmza_ret  .= ""; }
    if (function_exists("wpgmza_register_ugm_version")) { $wpgmza_ret .= wpgmza_ugm_addon_display_mapspage(); } else { $wpgmza_ret  .= ""; }
    return $wpgmza_ret;
}


function wpgmaps_tag_pro( $atts ) {
	
	if(!wpgmza_is_basic_compatible())
		return wpgmza_get_basic_incompatible_notice();

	global $wpgmza;
	global $short_code_active;
	global $wpdb;
	global $wpgmza_shortcode_atts_by_map_id;
	
	global $wpgmza_google_maps_api_loader;
	
	$short_code_active = true;
	if($wpgmza_google_maps_api_loader)
		$wpgmza_google_maps_api_loader->enqueueGoogleMaps();
	
	wpgmza_localize_category_data();

	wpgmza_enqueue_fontawesome();
	
	if(!isset($atts['id']))
	{
		// Let's use the first ID
		$atts['id'] = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}wpgmza_maps LIMIT 1");
	}
	
	$overrides = array_merge(array(), $atts);
	unset($overrides['id']);
	
	try{
		$map = WPGMZA\Map::createInstance($atts['id'], $overrides);
	}catch(\Exception $e) {
		
		if($e->getMessage() != 'Invalid ID')
			throw $e;
		
		echo '
			<div class="notice notice-error">
				<p>
					' . __('The map ID you have entered does not exist. Please enter a map ID that exists.', 'wp-google-maps') . '
				</p>
			</div>
		';
		
		return "";
	}


	$map->shortcodeAttributes = $atts;
	
	$mashup_ids_attributes = '';
	if(isset($atts['mashup_ids']))
		$mashup_ids_attributes = "data-mashup-ids='{$atts['mashup_ids']}'";
	
	wp_register_style('wpgmaps-admin-style', plugins_url('css/wpgmaps-admin.css', __FILE__));
	wp_enqueue_style('wpgmaps-admin-style');

	wp_enqueue_script('wpgmza_canvas_layer_options', plugin_dir_url(__FILE__) . 'lib/CanvasLayerOptions.js', array('wpgmza_api_call'));
	wp_enqueue_script('wpgmza_canvas_layer', plugin_dir_url(__FILE__) . 'lib/CanvasLayer.js', array('wpgmza_api_call'));
	
	$stmt = $wpdb->prepare("SELECT `map_title` FROM `".$wpdb->prefix.'wpgmza_maps'."` WHERE `id` = %d AND `active` = 0", array($atts['id']));
	$result = $wpdb->get_row($stmt);
	
	if( $result == null ){
		return("<p>".__('The map ID you have entered does not exist. Please enter a map ID that exists.', 'wp-google-maps')."</p>");
	}
	
	$additionalClasses = "";
	if(!empty($atts['classname']))
		$additionalClasses = $atts['classname'];
	
	$wpgmza_shortcode_atts_by_map_id[$atts['id']] = $atts;

	global $wpgmza_pro_version;

    global $wpgmza_current_map_id;
    global $wpgmza_current_map_cat_selection;
    global $wpgmza_current_map_shortcode_data;
    global $wpgmza_current_map_type;
    global $wpgmza_current_mashup;
    global $wpgmza_mashup_ids;
    global $wpgmza_mashup_all;
    global $wpgmza_override;
    $wpgmza_current_mashup = false;
    extract( shortcode_atts( array(
        'id' => '1',
        'mashup' => false,
        'mashup_ids' => false,
        'cat' => 'all',
        'type' => 'default',
        'parent_id' => false,
        'lat' => false,
        'lng' => false
    ), $atts ) );
    
    
    /* first check if we are using custom fields to generate the map */
    if (isset($atts['lng']) && isset($atts['lat']) && isset($atts['parent_id']) && $atts['lat'] && $atts['lng']) {
        $atts['id'] = $atts['parent_id']; /* set the main ID as the specified parent id */
        $wpgmza_current_map_id = $atts['parent_id'];
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lat'] = $atts['lat'];
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lng'] = $atts['lng'];
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['parent_id'] = $atts['parent_id'];
        $wpgmza_using_custom_meta = true;
        
    } else {
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lat'] = false;
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['lng'] = false;
        $wpgmza_current_map_shortcode_data[$wpgmza_current_map_id]['parent_id'] = false;
        $wpgmza_using_custom_meta = false;
    }    
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

    if (isset($atts['mashup']))
	{
		// $wpgmza_mashup = $atts['mashup'];
		$wpgmza_mashup = true;
	}

    if (isset($atts['parent_id'])) { $wpgmza_mashup_parent_id = $atts['parent_id']; }

    if (isset($wpgmza_mashup_ids) && $wpgmza_mashup_ids == "ALL") {

    } else {
        if (isset($atts['mashup_ids'])) {
            $wpgmza_mashup_ids[$atts['id']] = explode(",",$atts['mashup_ids']);
        }
    }
	
    if (isset($wpgmza_mashup)) { $wpgmza_current_mashup = true; }

    if (isset($wpgmza_mashup)) {
        $wpgmza_current_map_id = $wpgmza_mashup_parent_id;
        $res = wpgmza_get_map_data($wpgmza_mashup_parent_id);
    } else {
        $wpgmza_current_map_id = $atts['id'];
        
        
        if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
        } else {
            /* only check if marker file exists if they are using the XML method */
            wpgmza_check_if_marker_file_exists($wpgmza_current_map_id);
        }
        
        $res = wpgmza_get_map_data($atts['id']);
    }
	
    if (!isset($atts['cat']) || $atts['cat'] == "all" || $atts['cat'] == "0") {
        $wpgmza_current_map_cat_selection[$wpgmza_current_map_id] = 'all';
    } else {
        $wpgmza_current_map_cat_selection[$wpgmza_current_map_id] = explode(",",$atts['cat']);
    }
    

    if (!isset($atts['type']) || $atts['type'] == "default" || $atts['type'] == "") {
        $wpgmza_current_map_type[$wpgmza_current_map_id] = '';
    } else {
        $wpgmza_current_map_type[$wpgmza_current_map_id] = $atts['type'];
    }
	
	$map_other_settings = maybe_unserialize($res->other_settings);
	$res->other_settings = $map_other_settings;
	
	$iw_output = "";
	$iw_custom_styles ="";
    /* handle new modern infowindow HTML output */
	
	$infoWindowType = 1;
	if(isset($wpgmza_settings['wpgmza_iw_type']) && (int)$wpgmza_settings['wpgmza_iw_type'] != -1)
		$infoWindowType = (int)$wpgmza_settings['wpgmza_iw_type'];
	if(isset($map_other_settings['wpgmza_iw_type']) && (int)$map_other_settings['wpgmza_iw_type'] != -1)
		$infoWindowType = (int)$map_other_settings['wpgmza_iw_type'];
	
	$map_id = $atts['id'];
	
    if ($infoWindowType >= 1) {

		$mapCSSSelector = "[data-map-id='$map_id']";

    	/* Enqueue Modern Styles */

    	wp_enqueue_style("wpgmza_modern_base", plugin_dir_url(__FILE__) . "css/wpgmza_style_pro_modern_base.css", array(), $wpgmza_pro_version );

    	switch($infoWindowType){
    		case 2: //Modern Plus
    			wp_enqueue_style("wpgmza_modern_plus", plugin_dir_url(__FILE__) . "css/wpgmza_style_pro_modern_plus.css");
    			break;
    		case 3: //Circular
				wp_enqueue_style("wpgmza_modern_circular", plugin_dir_url(__FILE__) . "css/wpgmza_style_pro_modern_circular.css");
    			break;
    	}

    	if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }
 
    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_modern_infowindow { background-color: " . (!empty($map_other_settings['iw_primary_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_primary_color']) : "#2A3744") . "; }";
    	
    	if($infoWindowType !== 1){
    		$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_title { color: " . (!empty($map_other_settings['iw_text_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_text_color']) : "#ffffff") . "; }";
    	} else{
    		$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_title { ";
    		$iw_custom_styles .=  "		color: " . (!empty($map_other_settings['iw_text_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_text_color']) : "#ffffff") . "; " ;
    		$iw_custom_styles .=  "		background-color: " . (!empty($map_other_settings['iw_accent_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_accent_color']) : "#252F3A") . ";";
    		$iw_custom_styles .=  " }";
    	}

    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_description { color: " . (!empty($map_other_settings['iw_text_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_text_color']) : "#ffffff") . "; }";
    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_iw_address_p { color: " . (!empty($map_other_settings['iw_text_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_text_color']) : "#ffffff") . "; }";


    	$iw_custom_styles .=  "$mapCSSSelector .wpgmza_button { ";
    	$iw_custom_styles .=  "			color: " . (!empty($map_other_settings['iw_text_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_text_color']) : "#ffffff") . ";";
    	$iw_custom_styles .=  "			background-color: " . (!empty($map_other_settings['iw_accent_color']) ? "#" . str_replace('#', '', $map_other_settings['iw_accent_color']) : "#252F3A") . ";";
    	$iw_custom_styles .=  " }";
	}
	
    	if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }

    	$iw_output = "<div id='wpgmza_iw_holder_".$wpgmza_current_map_id."' style='display:none;'>";

    	$iw_output .= 	"<div class='wpgmza_modern_infowindow_inner wpgmza_modern_infowindow_inner_".$wpgmza_current_map_id."'>";
    	$iw_output .= 		"<div class='wpgmza_modern_infowindow_close'> x </div>";

    	$iw_output .= 		"<div class='wpgmza_iw_image'>";
    	$iw_output .= 			"<img src='' style='max-width:100% !important;' class='wpgmza_iw_marker_image' />";
    	
    	$iw_output .= 			"<div class='wpgmza_iw_title'>";
    	$iw_output .= 				"<p class='wpgmza_iw_title_p'></p>";
    	$iw_output .= 			"</div>";

    	$iw_output .= 			"";
    	$iw_output .= 		"</div>";
    	$iw_output .= 		"<div class='wpgmza_iw_address'>";
    	$iw_output .= 			"<p class='wpgmza_iw_address_p'></p>";
    	$iw_output .= 		"</div>";
    	$iw_output .= 		"<div class='wpgmza_iw_description'>";
    	$iw_output .= 			"<p class='wpgmza_iw_description_p'></p>";
    	$iw_output .= 		"</div>";
    	$iw_output .= 		"<div class='wpgmza_iw_buttons'>";
    	$iw_output .= 			"<a href='#' class='wpgmza_button wpgmza_left wpgmza_directions_button'>".__("Directions","wp-google-maps")."</a>";
    	$iw_output .= 			"<a href='#' class='wpgmza_button wpgmza_right wpgmza_more_info_button'>$wpgmza_settings_infowindow_link_text</a>";
    	$iw_output .= 		"</div>";
    	$iw_output .= 	"</div>";
    	$iw_output .= "</div>";


    //}
   
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_category'])) { $hide_category_column = $wpgmza_settings['wpgmza_settings_markerlist_category']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_icon'])) { $hide_icon_column = $wpgmza_settings['wpgmza_settings_markerlist_icon']; }
	if (isset($wpgmza_settings['wpgmza_settings_markerlist_link'])) { $hide_link_column = $wpgmza_settings['wpgmza_settings_markerlist_link']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_title'])) { $hide_title_column = $wpgmza_settings['wpgmza_settings_markerlist_title']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_address'])) { $hide_address_column = $wpgmza_settings['wpgmza_settings_markerlist_address']; }
    if (isset($wpgmza_settings['wpgmza_settings_markerlist_description'])) { $hide_description_column = $wpgmza_settings['wpgmza_settings_markerlist_description']; }
    if (isset($wpgmza_settings['wpgmza_settings_filterbycat_type'])) { $filterbycat_type = $wpgmza_settings['wpgmza_settings_filterbycat_type']; } else { $filterbycat_type = false; }
    if (!$filterbycat_type) { $filterbycat_type = 1; }
    
    $map_width_type = stripslashes($res->map_width_type);
    $map_height_type = stripslashes($res->map_height_type);
    if (!isset($map_width_type)) { $map_width_type = "px"; }
    if (!isset($map_height_type)) { $map_height_type = "px"; }
    if ($map_width_type == "%" && intval($res->map_width) > 100) { $res->map_width = 100; }
    if ($map_height_type == "%" && intval($res->map_height) > 100) { $res->map_height = 100; }

    
    $map_align = $map->wpgmza_map_align;
    if (!$map_align || $map_align == "" || $map_align == "1") { $map_align = "float:left;"; }
    else if ($map_align == "2") { $map_align = "margin-left:auto !important; margin-right:auto !important; align:center;"; }
    else if ($map_align == "3") { $map_align = "float:right;"; }
    else if ($map_align == "4") { $map_align = "clear:both;"; }
    $map_style = "style=\"display:block; overflow:auto; width:".$res->map_width."".$map_width_type."; height:".$res->map_height."".$map_height_type."; $map_align\"";
    global $short_code_active;
    $short_code_active = true;
    
	
	// The settings are about to be written to an element here
	// Before that happens, let's see what the value of $res->kml is
	if(!empty($res->kml)){
        $site_url = site_url();
        $res->kml = str_replace("{site_url}", $site_url, $res->kml);
    }
	
	$scriptLoader = new \WPGMZA\ScriptLoader(true);
	$scriptLoader->enqueueScripts();
	
	// Using DOMDocument here to properly format the data-settings attribute
	$document = new WPGMZA\DOMDocument();
	$document->loadHTML('<div id="debug"></div>');
	
	$el = $document->querySelector("#debug");
	
	if(isset($res->other_settings) && is_string($res->other_settings))
	{
		$temp = clone $res;
		$temp->other_settings = unserialize($res->other_settings);
		
		$el->setAttribute('data-settings', json_encode($temp));
	}
	else
		$el->setAttribute('data-settings', json_encode($res));
	
	$html = $document->saveHTML();
	
	if(preg_match('/data-settings=".+"/', $html, $m) || preg_match('/data-settings=\'.+\'/', $html, $m))
	{
		$map_attributes = $m[0];
	}
	else
	{
		// Fallback if for some reason we can't match the attribute string
		$escaped = esc_attr(json_encode($res));
		$attr = str_replace('\\\\%', '%', $escaped);
		$attr = stripslashes($attr);
		$map_attributes = "data-settings='" . $attr . "'";
	}
	
	$map_attributes .= " data-map-id='" . $map->id . "'";
	
	// Using DOMDocument here to properly format the data-shortcode-attributes attribute
	$document = new WPGMZA\DOMDocument();
	$document->loadHTML('<div id="debug"></div>');
	
	$el = $document->querySelector("#debug");
	$el->setAttribute('data-shortcode-attributes', json_encode($atts));
	
	$html = $document->saveHTML();
	
	if(preg_match('/data-shortcode-attributes=".+"/', $html, $m) || preg_match('/data-shortcode-attributes=\'.+\'/', $html, $m))
	{
		$map_attributes .= ' ' . $m[0];
	}
	else
	{
		// Fallback if for some reason we can't match the attribute string
		$escaped = esc_attr(json_encode($atts));
		$attr = str_replace('\\\\%', '%', $escaped);
		$attr = stripslashes($attr);
		$map_attributes = " data-shortcode-attributes='" . $attr . "'";
	}

	wp_enqueue_style( 'wpgmaps-style-pro', plugins_url('css/wpgmza_style_pro.css', __FILE__), array(), $wpgmza_pro_version );

	if(!empty($wpgmza->settings->user_interface_style))
	{
		switch($wpgmza->settings->user_interface_style)
		{
			case "legacy":
			case "modern":
			case "default":
				wp_enqueue_style('wpgmza_legacy_modern_pro_style', plugin_dir_url(__FILE__) . 'css/styles/legacy-modern.css', $wpgmza_pro_version);
				break;
		}
	}
	
	$wpgmaps_extra_css = ".wpgmza_map img { max-width:none; }
        .wpgmza_widget { overflow: auto; }";
    wp_add_inline_style( 'wpgmaps-style-pro', $wpgmaps_extra_css );
	wp_add_inline_style( 'wpgmaps-style-pro', $iw_custom_styles );


    $wpgmza_main_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_main_settings['wpgmza_custom_css']) && $wpgmza_main_settings['wpgmza_custom_css'] != "") { 
    	/**
    	 *  Removed from pro, fully managed by basic, this causes duplicated CSS
    	*/
		// TODO: Slashes should be stripped on input really, however please bear in mind removing this call may break CSS for existing users. A version check is in order here
		/*
		$style = html_entity_decode(stripslashes($wpgmza_main_settings['wpgmza_custom_css']));
        wp_add_inline_style( 'wpgmaps-style-pro', $style );
        */
    }

    global $wpgmza_short_code_array;
    $wpgmza_short_code_array[] = $wpgmza_current_map_id;
    
    
    $filterbycat = $res->filterbycat;
    $map_width = $res->map_width;
    $map_width_type = $res->map_width_type;
    // for marker list
    $default_marker = $res->default_marker;

    if (isset($atts['zoom'])) {
        $zoom_override = $atts['zoom'];
        if (!isset($wpgmza_override['zoom'])) {
        	$wpgmza_override['zoom'] = array();
        }
        $wpgmza_override['zoom'][$wpgmza_current_map_id] = $zoom_override;
    }    

     if (isset($atts['new_window_link'])) {
        $new_window_link = $atts['new_window_link'];
        $wpgmza_override['new_window_link'][$wpgmza_current_map_id] = $new_window_link;
    }
	
    $show_location = $res->show_user_location;
    
	$use_location_from = "";
	$use_location_to = "";
	
    if ($default_marker) { $default_marker = "<img src='".$default_marker."' />"; } else { $default_marker = "<img src='".wpgmaps_get_plugin_url()."/images/marker.png' />"; }
  
    $wpgmza_marker_list_output = "";
    $wpgmza_marker_filter_output = "";
    // Filter by category
    

   	/**
	 * Handle 'category' filter override attribute
	 */
    if (isset($atts['enable_category'])) { 
    	$filterbycat = intval($atts['enable_category']);
    }

    
    if ($filterbycat == 1) {
        
		$wpgmza_marker_filter_output .= "<div class='wpgmza-marker-listing-category-filter' data-map-id='$wpgmza_current_map_id' id='wpgmza_filter_".$wpgmza_current_map_id."' style='text-align:left; margin-bottom:0px;'><span>".__("Filter by","wp-google-maps")."</span>";

		if (intval($filterbycat_type) == 2)
		{	
            $wpgmza_marker_filter_output .= "<div style=\"overflow:auto; display:block; width:100%; height:auto; margin-top:10px;\">";
            
            $wpgmza_marker_filter_output .= $map->categoryFilterWidget->html;
			
            $wpgmza_marker_filter_output .= "</div>";
		}
		else
            $wpgmza_marker_filter_output .= $map->categoryFilterWidget->html;
			
		$wpgmza_marker_filter_output .= "</div>";
    }
			
    $wpgmza_marker_datatables_output = "";
    if (isset($hide_category_column) && $hide_category_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_category { display: none !important; }</style>"; }
    if (isset($hide_icon_column) && $hide_icon_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_marker { display: none; }</style>"; }
    if (isset($hide_title_column) && $hide_title_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_title { display: none; }</style>"; }
    if (isset($hide_address_column) && $hide_address_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_address { display: none; }</style>"; }
    if (isset($hide_description_column) && $hide_description_column == "yes") { $wpgmza_marker_datatables_output .= "<style>.wpgmza_table_description { display: none; }</style>"; }
    
    $sl_data = "";

	if($map->storeLocator)
		$sl_data = $map->storeLocator->html;
	
	$columns = implode(', ', wpgmza_get_marker_columns());
	
	if(isset($map_other_settings['list_markers_by']) && $map_other_settings['list_markers_by'] == '6') {
		
		switch($res->order_markers_by)
		{
			case 2:
				$order_by = "title";
				break;
				
			case 3:
				$order_by = "address";
				break;
				
			case 4:
				$order_by = "desc";
				break;
				
			case 5:
				$order_by = "category";
				break;
				
			case 6:
				$order_by = "priority";
				break;
			
			default:
				$order_by = "id";
				break;
		}
		
		$order_dir = ($res->order_markers_choice == '2' ? 'DESC' : 'ASC');
		
		$where = "WHERE map_id = " . (int)$atts['id'];
		
		if(!empty($atts['mashup_ids']))
			$where = "WHERE map_id IN (" . implode(', ', array_map('intval', explode(',', $atts['mashup_ids']))) . ")";
		
		$where .= ' AND approved = 1';
		
		if($order_by == 'priority')
		{
			$qstr = "SELECT {$wpdb->prefix}wpgmza.id 
				FROM `{$wpdb->prefix}wpgmza` 
				LEFT JOIN {$wpdb->prefix}wpgmza_categories ON SUBSTRING_INDEX(category, ',', 1) = {$wpdb->prefix}wpgmza_categories.id 
				$where
				ORDER BY priority $order_dir";
		}
		else
		{
			$qstr = "SELECT id FROM {$wpdb->prefix}wpgmza $where ORDER BY $order_by $order_dir";
		}
		
		$marker_id_order = $wpdb->get_col($qstr);
		
		wp_enqueue_script('wpgmza_dummy', plugin_dir_url(WPGMZA_PRO_FILE) . 'dummy.js');
		
		wp_localize_script('wpgmza_dummy', 'wpgmza_modern_marker_listing_marker_order_by_id_for_map_' . (int)$atts['id'], $marker_id_order);
		
		do_action('wpgmza_modern_marker_listing_marker_order', (int)$atts['id'], $marker_id_order);
	}	

	
    if ((!empty($map_other_settings['list_markers_by']) && !isset($map_other_settings['wpgmza_listmarkers_by'])) || !empty($map_other_settings['wpgmza_listmarkers_by'])) {
		
		$style = !empty($map_other_settings['wpgmza_listmarkers_by']) ? $map_other_settings['wpgmza_listmarkers_by'] : $map_other_settings['list_markers_by'];
		$params = array(
			'map_id'	=> $wpgmza_current_map_id
		);

		if($wpgmza_current_mashup)
			$params['mashup_ids'] = $wpgmza_mashup_ids[$wpgmza_current_map_id];
		
		$listing = WPGMZA\MarkerListing::createInstanceFromStyle($style, $wpgmza_current_map_id);

		$listing->setAjaxParameters($params);
		
		$wpgmza_marker_list_output = $listing->html();
		
    } else {
    	
        if ($res->listmarkers == 1 && $res->listmarkers_advanced == 1) {
            if ($wpgmza_current_mashup) {
                $wpgmza_marker_list_output .= wpgmza_return_marker_list($wpgmza_mashup_parent_id,false,$map_width.$map_width_type,$wpgmza_current_mashup,$wpgmza_mashup_ids[$atts['id']]);
            } else {
                $wpgmza_marker_list_output .= wpgmza_return_marker_list($wpgmza_current_map_id,false,$map_width.$map_width_type,false);
            }
        }
        else if ($res->listmarkers == 1 && $res->listmarkers_advanced == 0) {
            global $wpdb;
            global $wpgmza_tblname;

            // marker sorting functionality
            if ($res->order_markers_by == 1) { $order_by = "id"; }
            else if ($res->order_markers_by == 2) { $order_by = "title"; }
            else if ($res->order_markers_by == 3) { $order_by = "address"; }
            else if ($res->order_markers_by == 4) { $order_by = "description"; }
            else if ($res->order_markers_by == 5) { $order_by = "category"; }
            else { $order_by = "id"; }
            if ($res->order_markers_choice == 1) { $order_choice = "ASC"; }
            else { $order_choice = "DESC"; }

            if ($wpgmza_current_mashup) {

                $wpgmza_cnt = 0;
                $sql_string1 = "";
                if ($wpgmza_mashup_ids[$atts['id']][0] == "ALL") {
                    $wpgmza_sql1 ="SELECT $columns FROM $wpgmza_tblname ORDER BY `$order_by` $order_choice";
                } else {
                    $wpgmza_id_cnt = count($wpgmza_mashup_ids[$atts['id']]);
                    foreach ($wpgmza_mashup_ids[$atts['id']] as $wpgmza_map_id) {
						
						$wpgmza_map_id = (int)$wpgmza_map_id;
						
                        $wpgmza_cnt++;
                        if ($wpgmza_cnt == 1) { $sql_string1 .= "`map_id` = '$wpgmza_map_id' "; }
                        elseif ($wpgmza_cnt > 1 && $wpgmza_cnt < $wpgmza_id_cnt) { $sql_string1 .= "OR `map_id` = '$wpgmza_map_id' "; }
                        else { $sql_string1 .= "OR `map_id` = '$wpgmza_map_id' "; }

                    }
                    $wpgmza_sql1 ="SELECT $columns FROM $wpgmza_tblname WHERE $sql_string1 ORDER BY `$order_by` $order_choice";
                }
            } else {
                $wpgmza_sql1 ="SELECT $columns FROM $wpgmza_tblname WHERE `map_id` = '" . intval($wpgmza_current_map_id) . "' ORDER BY `$order_by` $order_choice";
            }

            $results = $wpdb->get_results($wpgmza_sql1);

            $wpgmza_marker_list_output .= "
                    <div style='clear:both;'>
                    <table id=\"wpgmza_marker_list\" class=\"wpgmza_marker_list_class\" cellspacing=\"0\" cellpadding=\"0\" style='width:".$map_width."".$map_width_type."'>
                    <tbody>
            ";

            $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
			if (isset($wpgmza_settings['wpgmza_settings_image_resizing']) && $wpgmza_settings['wpgmza_settings_image_resizing'] == 'yes') { $wpgmza_image_resizing = true; } else { $wpgmza_image_resizing = false; }
            if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_image_height = $wpgmza_settings['wpgmza_settings_image_height']; } else { $wpgmza_image_height = false; }
            if (isset($wpgmza_settings['wpgmza_settings_image_height'])) { $wpgmza_image_height = $wpgmza_settings['wpgmza_settings_image_height']."px"; } else { $wpgmza_image_height = false; }
            if (isset($wpgmza_settings['wpgmza_settings_image_width'])) { $wpgmza_image_width = $wpgmza_settings['wpgmza_settings_image_width']."px"; } else { $wpgmza_image_width = false; }
            if (!$wpgmza_image_height || !isset($wpgmza_image_height)) { $wpgmza_image_height = "auto"; }
            if (!$wpgmza_image_width || !isset($wpgmza_image_width)) { $wpgmza_image_width = "auto"; }
            $wmcnt = 0;
            foreach ( $results as $result ) {
                $wmcnt++;
                $img = $result->pic;
                $wpgmaps_id = $result->id;
                $link = $result->link;
                $icon = $result->icon;
                $wpgmaps_lat = $result->lat;
                $wpgmaps_lng = $result->lng;
                $wpgmaps_address = $result->address;
            	/* added in 5.52 - phasing out timthumb */
            	/* timthumb completely removed in 5.54 */
                /*if ($wpgmza_use_timthumb == "" || !isset($wpgmza_use_timthumb)) {
					$pic = "<img src='".wpgmaps_get_plugin_url()."/timthumb.php?src=".$result->pic."&h=".$wpgmza_image_height."&w=".$wpgmza_image_width."&zc=1' />";
                } else {*/
		            if (!$img) { $pic = ""; } else {
		        		if ($wpgmza_image_resizing) {
		                    $pic = "<img src='".$result->pic."' class='wpgmza_map_image' style=\"margin:5px; height:".$wpgmza_image_height."px; width:".$wpgmza_image_width.".px\" />";
		                } else {
		                    $pic = "<img src='".$result->pic."' class='wpgmza_map_image' style=\"margin:5px;\" />";
		                }
                   	}
                /*}*/
                if (!$icon) { $icon = $default_marker; } else { $icon = "<img src='".$result->icon."' />"; }
                if ($d_enabled == "1") {
                    $wpgmaps_dir_text = "<br />
						<a href=\"javascript:void(0);\" 
							id=\"$wpgmza_current_map_id\" 
							data-map-id=\"$wpgmza_current_map_id\"
							title=\"".__("Get directions to","wp-google-maps")." ".$result->title."\" 
							class=\"wpgmza_gd\" 
							wpgm_addr_field=\"".$wpgmaps_address."\" 
							gps=\"$wpgmaps_lat,$wpgmaps_lng\"
							>".__("Directions","wp-google-maps")."</a>";
                } else { $wpgmaps_dir_text = ""; }
                if ($result->description) {
                    $wpgmaps_desc_text = "<br />".$result->description."";
                } else {
                    $wpgmaps_desc_text = "";
                }
                if ($wmcnt%2) { $oddeven = "wpgmaps_odd"; } else { $oddeven = "wpgmaps_even"; }


                $wpgmza_marker_list_output .= "
                    <tr id=\"wpgmza_marker_".$result->id."\" mid=\"".$result->id."\" mapid=\"".$result->map_id."\" class=\"wpgmaps_mlist_row $oddeven\">
                        <td height=\"40\" class=\"wpgmaps_mlist_marker\">".$icon."</td>
                        <td class=\"wpgmaps_mlist_pic\" style=\"width:".($wpgmza_image_width+20)."px;\">$pic</td>
                        <td  valign=\"top\" align=\"left\" class=\"wpgmaps_mlist_info\">
                            <strong><a href=\"javascript:openInfoWindow($wpgmaps_id);\" id=\"wpgmaps_marker_$wpgmaps_id\" title=\"".stripslashes($result->title)."\">".stripslashes($result->title)."</a></strong>
                            ".stripslashes($wpgmaps_desc_text)."
                            $wpgmaps_dir_text
                        </td>

                    </tr>";
            }
            $wpgmza_marker_list_output .= "</tbody></table></div>";

        } else { $wpgmza_marker_list_output = ""; }
    }

	global $wpgmza;
	
	$dbox_option = $res->dbox;
	
	if($map->isDirectionsEnabled()){
		$dbox_div = $map->directionsBox->html;
	}else{
		$dbox_div = "";
	}
		
    if ($dbox_option == "5" || $dbox_option == "1" || !isset($dbox_option)) {
        

        if ($wpgmza_current_mashup) {
            $wpgmza_anchors = $wpgmza_mashup_ids[$atts['id']];
        } else {
            $wpgmza_anchors = $wpgmza_current_map_id;
        }


        $ret_msg = "
            $wpgmza_marker_datatables_output
            ".wpgmaps_check_approval_string()."
            ".wpgmaps_return_marker_anchors($wpgmza_anchors)."
            <a name='map".$wpgmza_current_map_id."'></a>
            $wpgmza_marker_filter_output
            ".apply_filters("wpgooglemaps_filter_map_output","",$wpgmza_current_map_id)."
            ".(empty($map->wpgmza_store_locator_position) ? "$sl_data" : "")."
            ".(!empty($map->wpgmza_marker_listing_position) ? "$wpgmza_marker_list_output" : "")."
            ".apply_filters("wpgooglemaps_filter_map_div_output","<div class=\"wpgmza_map $additionalClasses\" $mashup_ids_attributes id=\"wpgmza_map_".$wpgmza_current_map_id."\" $map_style $map_attributes> </div>",$wpgmza_current_map_id)."
            ".(!empty($map->wpgmza_store_locator_position) ? "$sl_data" : "")."
            ".(empty($map->wpgmza_marker_listing_position) ? "$wpgmza_marker_list_output" : "")."   
        ";

        if ($map->isDirectionsEnabled()) {
        	$ret_msg .= "<div style=\"display:block; width:100%;\">
				
				$dbox_div
				
				<div id=\"wpgmaps_directions_notification_".$wpgmza_current_map_id."\" style=\"display:none;\">".__("Fetching directions...","wp-google-maps")."...</div>
				
				<div id=\"wpgmaps_directions_reset_".$wpgmza_current_map_id."\" style=\"display:none;\">

					<a href='javascript:void(0)' onclick='wpgmza_reset_directions(".$wpgmza_current_map_id.");' id='wpgmaps_reset_directions' title='".__("Reset directions","wp-google-maps")."'>".__("Reset directions","wp-google-maps")."</a>
					<br />
					<a href='javascript: ;' id='wpgmaps_print_directions_".$wpgmza_current_map_id."' target='_blank' title='".__("Print directions","wp-google-maps")."'>".__("Print directions","wp-google-maps")."</a>
				</div>
				
				<div id=\"directions_panel_".$wpgmza_current_map_id."\"></div>

			</div>";
        }

    } else {
        if ($wpgmza_current_mashup) {
            $wpgmza_anchors = $wpgmza_mashup_ids[$atts['id']];
        } else {
            $wpgmza_anchors = $wpgmza_current_map_id;
        }

        
        $ret_msg = "
			$wpgmza_marker_datatables_output

			<div style=\"display:block; width:100%;\">

				$dbox_div
			
				<div id=\"wpgmaps_directions_notification_".$wpgmza_current_map_id."\" style=\"display:none;\">".__("Fetching directions...","wp-google-maps")."...</div>
				<div id=\"wpgmaps_directions_reset_".$wpgmza_current_map_id."\" style=\"display:none;\">

					<a href='javascript:void(0)' onclick='wpgmza_reset_directions(".$wpgmza_current_map_id.");' id='wpgmaps_reset_directions' title='".__("Reset directions","wp-google-maps")."'>".__("Reset directions","wp-google-maps")."</a>
					<br />
					<a href='javascript: ;' id='wpgmaps_print_directions_".$wpgmza_current_map_id."' target='_blank' title='".__("Print directions","wp-google-maps")."'>".__("Print directions","wp-google-maps")."</a>
				</div>
			
				<div id=\"directions_panel_".$wpgmza_current_map_id."\"></div>
			
			</div>

			$wpgmza_marker_filter_output
			".(empty($map->wpgmza_store_locator_position) ? "$sl_data" : "")."
			".(!empty($map->wpgmza_marker_listing_position) ? "$wpgmza_marker_list_output" : "")."

			".wpgmaps_return_marker_anchors($wpgmza_anchors)."
            <a name='map".$wpgmza_current_map_id."'></a>

			".apply_filters("wpgooglemaps_filter_map_div_output","<div class=\"wpgmza_map $additionalClasses\" id=\"wpgmza_map_".$wpgmza_current_map_id."\" $map_style $map_attributes $mashup_ids_attributes> </div>", $wpgmza_current_map_id)."   
			".(!empty($map->wpgmza_store_locator_position) ? "$sl_data" : "")."

			".(empty($map->wpgmza_marker_listing_position) ? "$wpgmza_marker_list_output" : "")."
			

        ";

    }

    if (function_exists("wpgmza_register_ugm_version")) {
        $ugm_enabled = $res->ugm_enabled;
        if ($ugm_enabled == 1) {

     		if (isset($atts['disable_vgm_form']) && $atts['disable_vgm_form'] == '1') {
     			/* do nothing */
     		} else {
     			/* Thanks to AVdev for the suggestions to add redirect support here */
     			$redirect_to = false;
				if (isset($atts['redirect_to'])){
					$redirect_to = $atts['redirect_to'];
				}
				
				$ret_msg .= wpgmaps_ugm_user_form($wpgmza_current_map_id, $redirect_to, false);
            }
        }
    }
    
    
    if ($wpgmza_using_custom_meta) {
        /* we're using meta fields to generate the map, ignore default functionality */
        
        $ret_msg = "
            ".apply_filters("wpgooglemaps_filter_map_div_output","<div class=\"wpgmza_map $additionalClasses\" id=\"wpgmza_map_".$wpgmza_current_map_id."\" $map_style $map_attributes $mashup_ids_attributes> </div>", $wpgmza_current_map_id)."

            ";
    }
    

    




    if (isset($atts['marker'])) {
        $wpgmza_focus_marker = $atts['marker'];
        if (!isset($wpgmza_override['marker'])) {
        	$wpgmza_override['marker'] = array();
        }
        $wpgmza_override['marker'][$wpgmza_current_map_id] = $wpgmza_focus_marker;
    }    

	if(empty($wpgmza->settings->disable_autoptimize_compatibility_fix))
	{
		// Autoptimize fix, bypass CSS where our map is present as large amounts of inline JS (our localized data) crashes their plugin. Added at their advice.
		add_filter('autoptimize_filter_css_noptimize', '__return_true');
	}
	
    return $ret_msg;
}

function wpgmaps_check_approval_string() {
    if (isset($_POST['wpgmza_approval'] ) && $_POST['wpgmza_approval'] == "1") {
        return "<p class='wpgmza_marker_approval_msg'>".__("Thank you. Your marker is awaiting approval.","wp-google-maps")."</p>";

    }
}

function wpgmza_apply_setting_overrides($input, $atts = null)
{
	if(empty($input))
		return $input;
	
	if(is_object($input))
		$input = (array)$input;
	
	if(!is_array($input))
		throw new Exception("Input must be an array");
	
	if(!empty($atts))
		$input = array_merge($input, $atts);
	
	if(!empty($_GET))
	{
		$clone = array_merge(array(), $_GET);
		unset($clone['id']);
		
		$input = array_merge($input, $clone);
	}
	
	return $input;
}

function wpgmaps_user_javascript_pro($atts = false) {

    global $short_code_active;

	global $wpgmza_count;
	$wpgmza_count++;
	if ($wpgmza_count >1) {  } else {
	global $wpgmza_current_map_id;
	global $wpgmza_short_code_array;
	global $wpgmza_current_mashup;
	global $wpgmza_pro_version;
	
	global $wpgmza_current_map_cat_selection;
	global $wpgmza_current_map_shortcode_data;
	global $wpgmza_current_map_type;
	
	if ($wpgmza_current_mashup) { $wpgmza_current_mashup_string = "true"; } else { $wpgmza_current_mashup_string = "false"; }
	
	global $wpgmza_mashup_ids;
	if (isset($wpgmza_mashup_ids)) {
		if (isset($wpgmza_mashups_ids) && $wpgmza_mashups_ids == "ALL") {
			$wpgmza_mashup_ids = wpgmza_return_all_map_ids();
		}
	}
	
	$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
	
	global $wpgmza_google_maps_api_loader;
	$wpgmza_google_maps_api_loader->enqueueGoogleMaps();
	
	global $wpgmza_pro_version;
	$ajax_nonce = wp_create_nonce("wpgmza");
	
	// If wpgmza_do_not_enqueue_datatables is set, do not load datatables.
	if (empty($wpgmza_settings['wpgmza_do_not_enqueue_datatables'])) {
		wp_register_script('wpgmaps_datatables', plugins_url(plugin_basename(dirname(__FILE__)))."/js/jquery.dataTables.min.js", true);
		wp_enqueue_script( 'wpgmaps_datatables' );
		
		wp_register_script('wpgmaps_datatables-responsive', plugins_url(plugin_basename(dirname(__FILE__)))."/js/dataTables.responsive.js", true);
		wp_enqueue_script( 'wpgmaps_datatables-responsive' );

		wp_register_style('wpgmaps_datatables_style', plugins_url(plugin_basename(dirname(__FILE__)))."/css/data_table_front.css", array(), $wpgmza_pro_version);
		wp_enqueue_style( 'wpgmaps_datatables_style' );
		wp_register_style('wpgmaps_datatables_responsive-style', plugin_dir_url(__FILE__) . "lib/dataTables.responsive.css", array(), $wpgmza_pro_version);
		wp_enqueue_style( 'wpgmaps_datatables_responsive-style' );
	}

	if ($include_owl || true) {
		
		/*wp_register_script('owl_carousel', plugin_dir_url(__FILE__) .'js/owl.carousel.min.js', array(), $wpgmza_pro_version.'p' , false);
		wp_enqueue_script( 'owl_carousel' );
		wp_register_style('owl_carousel_style', plugin_dir_url(__FILE__) .'css/owl.carousel.css', array(), $wpgmza_pro_version);
		wp_enqueue_style( 'owl_carousel_style' );
		wp_register_style('owl_carousel_style_theme', plugin_dir_url(__FILE__) .'css/owl.theme.css', array(), $wpgmza_pro_version);
		wp_enqueue_style( 'owl_carousel_style_theme' );*/
		
		if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'sky') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_sky.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'sun') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_sun.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'earth') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_earth.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'monotone') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_monotone.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'pinkpurple') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_pinkpurple.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'white') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_white.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else if (isset($wpgmza_settings['wpgmza_settings_carousel_markerlist_theme']) && $wpgmza_settings['wpgmza_settings_carousel_markerlist_theme'] == 'black') { 
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_black.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		} else {
			wp_register_style('owl_carousel_style_theme_select', plugin_dir_url(__FILE__) .'css/carousel_sky.css', array(), $wpgmza_pro_version);
			wp_enqueue_style( 'owl_carousel_style_theme_select' );
		}
		
	}
	
	
	global $wpgmza;
	$wpgmza->loadScripts();

	do_action("wpgooglemaps_hook_user_js_after_core");
	
            
	if ( function_exists( "wpgmaps_ugm_activate" ) ) {
		global $wpgmza_ugm_version;
		$wpgmza_vgmc = floatval(str_replace(".","",$wpgmza_ugm_version));
		
		if ($wpgmza_vgmc < 300) {
			/* only load this if the version is less than 3.00 */
			wp_enqueue_script('wpgmaps_ugm_core', plugins_url('wp-google-maps-ugm') .'/js/ugm-core.js', array('wpgmaps_core'), $wpgmza_ugm_version.'vgm' , false);

		}
		
	}
	
		if (function_exists("wpgmza_return_marker_url")) {
			if (get_option("wpgmza_xml_url") == "") {
				add_option("wpgmza_xml_url",'{uploads_dir}/wp-google-maps/');
			}
			$xml_marker_url = wpgmza_return_marker_url();
		} else {
			if (get_option("wpgmza_xml_url") == "") {
				$upload_dir = wp_upload_dir();
				add_option("wpgmza_xml_url",$upload_dir['baseurl'].'/wp-google-maps/');
			}
			$xml_marker_url = get_option("wpgmza_xml_url");
		}

		if (is_multisite()) { 
			global $blog_id;
			$wurl = $xml_marker_url.$blog_id."-";

			$wurl = preg_replace('#^http?:#', '', $wurl);
			$wurl = preg_replace('#^https?:#', '', $wurl);

		}
		else {
			$wurl = $xml_marker_url;

			$wurl = preg_replace('#^http?:#', '', $wurl);
			$wurl = preg_replace('#^https?:#', '', $wurl);
			
		}
	}
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_markerurl', $wurl);

	
	
	if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = false; }
	if (!$wpgmza_settings_infowindow_link_text) { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }
	
	
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_more_details', $wpgmza_settings_infowindow_link_text);
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_get_dir', apply_filters( "wpgmza_filter_change_get_directions_string", __( "Get directions", "wp-google-maps" ) ) );
	
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_km_away', apply_filters( "wpgmza_filter_change_km_away_string", __( "km away", "wp-google-maps" ) ) );
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_m_away', apply_filters( "wpgmza_filter_change_miles_away_string", __( "miles away", "wp-google-maps" ) ) );
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_directions', apply_filters( "wpgmza_filter_change_directions_string", __( "Directions", "wp-google-maps" ) ) );
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_more_info', $wpgmza_settings_infowindow_link_text );
	//wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_error1', __("Please fill out both the \"from\" and \"to\" fields","wp-google-maps") );
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_lang_getting_location', __('Getting your current location address...','wp-google-maps') );

	wp_localize_script( 'wpgmaps_core', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

	if (function_exists("wpgmaps_ugm_activate")) {
		/* VGM variables */
		wp_localize_script( 'wpgmaps_core', 'vgm_human_error_string', __("Please prove that you are human by checking the checkbox above","wp-google-maps") );
		$ajax_nonce_ugm = wp_create_nonce("wpgmza_ugm");
		wp_localize_script( 'wpgmaps_core', 'wpgmaps_nonce', $ajax_nonce_ugm );
	}

	$ajax_nonce_pro = wp_create_nonce("wpgmza_pro_ugm");
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_pro_nonce', $ajax_nonce_pro );
	wp_localize_script( 'wpgmaps_core', 'wpgmaps_plugurl', wpgmaps_get_plugin_url() );
	
	if (function_exists("wpgmaps_gold_activate")) { 
		wp_localize_script( 'wpgmaps_core', 'wpgm_g_e', '1' );
	} else {
		wp_localize_script( 'wpgmaps_core', 'wpgm_g_e', '0' );
	}

}

function wpgmaps_upload_csv() {
    if (!function_exists("wpgmaps_activate")) {
        //echo "<div id='message' class='updated' style='padding:10px; '><span style='font-weight:bold; color:red;'>".__("WP Google Maps","wp-google-maps").":</span> ".__("Please ensure you have <strong>both</strong> the <strong>Basic</strong> and <strong>Pro</strong> versions of WP Google Maps installed and activated at the same time in order for the plugin to function correctly.","wp-google-maps")."<br /></div>";
    }
    
    if (isset($_POST['wpgmza_uploadcsv_btn'])) {

		check_ajax_referer( 'wpgmza', 'real_post_nonce' );
	
		if(!current_user_can('administrator'))
		{
			http_response_code(401);
			exit;
		}

    	if( isset( $_FILES['wpgmza_csvfile'] ) ){

    		$import = new WPGMapsImportExport();
    		$import->import_markers();

        } else if ( isset( $_FILES['wpgmza_csv_map_import'] ) ){

        	$import = new WPGMapsImportExport();
    		$import->import_maps();

        }  else if ( isset( $_FILES['wpgmza_csv_polygons_import'] ) ){

        	$import = new WPGMapsImportExport();
    		$import->import_polygons();

        }  else if ( isset( $_FILES['wpgmza_csv_polylines_import'] ) ){

        	$import = new WPGMapsImportExport();
    		$import->import_polylines();

        } 
    }

}

function wpgmza_cURL_response_pro($action) {
    if (function_exists('curl_version')) {
        global $wpgmza_pro_version;
        global $wpgmza_pro_string;
        $request_url = "http://www.wpgmaps.com/api/rec.php?action=$action&dom=".$_SERVER['HTTP_HOST']."&ver=".$wpgmza_pro_version.$wpgmza_pro_string;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

}

function wpgmza_pro_advanced_menu() {
	
	global $wpgmza;
    global $wpgmza_post_nonce;
	
	$real_post_nonce = wp_create_nonce('wpgmza');
	
    $wpgmza_csv_marker = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=wpgmza_csv_export\" target=\"_BLANK\" title=\"".__("Download ALL marker data to a CSV file","wp-google-maps")."\">".__("Download ALL marker data to a CSV file","wp-google-maps")."</a>";
    $wpgmza_csv_map = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=export_all_maps\" target=\"_BLANK\" title=\"".__("Download ALL map data to a CSV file","wp-google-maps")."\">".__("Download ALL map data to a CSV file","wp-google-maps")."</a>";
    $wpgmza_csv_polygon = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=export_polygons\" target=\"_BLANK\" title=\"".__("Download ALL polygon data to a CSV file","wp-google-maps")."\">".__("Download ALL polygon data to a CSV file","wp-google-maps")."</a>";
    $wpgmza_csv_polyline = "<a class='button button-primary' href=\"?page=wp-google-maps-menu-advanced&action=export_polylines\" target=\"_BLANK\" title=\"".__("Download ALL polyline data to a CSV file","wp-google-maps")."\">".__("Download ALL polyline data to a CSV file","wp-google-maps")."</a>";

	?>
	<div class="wrap"><h1><?php esc_html_e( 'Advanced Options' , 'wp-google-maps' ); ?></h1>
		<script>
			jQuery(document).ready(function(){
			    jQuery('#wpgmza_geocode').on('change', function(){
			        if(jQuery(this).attr('checked')){
			            jQuery('#wpgmza_geocode_conditional').fadeIn();
			        }else{
			            jQuery('#wpgmza_geocode_conditional').fadeOut();
			        }
			    });
			});
		</script>
		<div id="wpgmaps_tabs">
			<ul>
				<?php
					/**
					 * Output advanced options tabs html.
					 *
					 * @since 7.0.0
					 */
					do_action( 'wpgmza_admin_advanced_options_tabs' );
				?>
				<li><a href="#tabs-1"><?php esc_html_e( 'Map Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#tabs-2"><?php esc_html_e( 'Marker Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#tabs-3"><?php esc_html_e( 'Polygon Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#tabs-4"><?php esc_html_e( 'Polyline Data', 'wp-google-maps' ); ?></a></li>
				<li><a href="#utilities"><?php esc_html_e( 'Utilities', 'wp-google-maps' ); ?></a></li>
			</ul>
			<?php
				/**
				 * Output advanced options html.
				 *
				 * @since 7.0.0
				 */
				do_action( 'wpgmza_admin_advanced_options' );
			?>
<?php echo "            <div id=\"tabs-1\">
            	<form enctype=\"multipart/form-data\" method=\"POST\">
				
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
	                
	                <strong style='font-size:18px'>".__("Upload Map CSV File","wp-google-maps")."</strong><br /><br />
	                
	                <input name=\"wpgmza_csv_map_import\" id=\"wpgmza_csv_map_import\" type=\"file\" style='display:none'/>
	                
	                <label for='wpgmza_csv_map_import' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                
	                <div class='switch'><input name=\"wpgmza_csvreplace_map\" id='wpgmza_csvreplace_map' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace_map'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                

	                <br /><input class='wpgmza_general_btn button button-primary' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_map</div>
	            </form>
            </div>
            <div id=\"tabs-2\">
                <form enctype=\"multipart/form-data\" method=\"POST\">
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
				
	                <strong style='font-size:18px'>".__("Upload Marker CSV File","wp-google-maps")."</strong><br /><br />
	                <input name=\"wpgmza_csvfile\" id=\"wpgmza_csvfile\" type=\"file\" style='display:none'/>
	                <label for='wpgmza_csvfile' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                <div class='switch'><input name=\"wpgmza_csvreplace\" id='wpgmza_csvreplace' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                <div class='switch'><input name=\"wpgmza_geocode\" id='wpgmza_geocode' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_geocode'></label></div> (Beta) ".__("Automatically geocode addresses to GPS co-ordinates if none are supplied","wp-google-maps")." <br>
	                
	                <br><div style='display:none;' id='wpgmza_geocode_conditional'><strong>".__("Google API Key (Required)","wp-google-maps").": </strong><input name=\"wpgmza_api_key\" type=\"text\" value=\"".get_option("wpgmza_geocode_api_key")."\" /> 
	                (".__("You will need a Google Maps Geocode API key for this to work. See <a href='https://developers.google.com/maps/documentation/geocoding/#Limits'>Geocoding Documentation</a>","wp-google-maps")."). <br> ".__("There is a 0.12second delay between each request","wp-google-maps")."<br /></div>
						<input class='wpgmza_general_btn button button-primary' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_marker</div>
	            </form>
            </div>
            <div id=\"tabs-3\">
            	<form enctype=\"multipart/form-data\" method=\"POST\">
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
	                
	                <strong style='font-size:18px'>".__("Upload Polygon CSV File","wp-google-maps")."</strong><br /><br />
	                
	                <input name=\"wpgmza_csv_polygons_import\" id=\"wpgmza_csv_polygons_import\" type=\"file\" style='display:none'/>
	                
	                <label for='wpgmza_csv_polygons_import' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                
	                <div class='switch'><input name=\"wpgmza_csvreplace_polygon\" id='wpgmza_csvreplace_polygon' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace_polygon'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                

	                <br /><input class='wpgmza_general_btn button button-primary' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_polygon</div>
	            </form>
            </div>
            <div id=\"tabs-4\">
            	<form enctype=\"multipart/form-data\" method=\"POST\">
					<input name='real_post_nonce' value='$real_post_nonce' type='hidden'/>
	                
	                <strong style='font-size:18px'>".__("Upload Polyline CSV File","wp-google-maps")."</strong><br /><br />
	                
	                <input name=\"wpgmza_csv_polylines_import\" id=\"wpgmza_csv_polylines_import\" type=\"file\" style='display:none'/>
	                
	                <label for='wpgmza_csv_polylines_import' class='wpgmza_file_select_btn'><i class='fa fa-download'></i> Select File</label><br />
	                
	                <input name=\"wpgmza_security\" type=\"hidden\" value=\"$wpgmza_post_nonce\" /><br /><br>
	                
	                <div class='switch'><input name=\"wpgmza_csvreplace_polyline\" id='wpgmza_csvreplace_polyline' class='cmn-toggle cmn-toggle-round-flat' type=\"checkbox\" value=\"Yes\" /> <label for='wpgmza_csvreplace_polyline'></label></div> ".__("Replace existing data with data in file","wp-google-maps")."<br />
	                

	                <br /><input class='wpgmza_general_btn button button-primary' type=\"submit\" name=\"wpgmza_uploadcsv_btn\" value=\"".__("Upload File","wp-google-maps")."\" />
	                
	                <div class='wpgmza-buttons__float-right'>$wpgmza_csv_polyline</div>
	            </form>
            </div>
			
			<div id='utilities'>
				<h2>
					" . __('Utilities', 'wp-google-maps');
	
	if(version_compare($wpgmza->getBasicVersion(), '8.0.4', '>='))
	{
		echo "<p>
					
			<button id='wpgmza-remove-duplicates' type='button' class='button button-primary' title='" . __('Delete all markers with matching coordinates, address, title, link and description', 'wp-google-maps') . "'>
				" . __('Remove duplicate markers', 'wp-google-maps') . "
			</button>
		
		</p>";
	}
	else
	{
		echo "<p>" . __('Please update the core plugin to 8.0.4 or above to use utilities.', 'wp-google-maps') . "</p>";
	}

	echo "
				</h2>

				<hr>
				<br>

				<h2>" . __("Import Log", "wp-google-maps") . "</h2>
				<textarea class='wpgmza_import_log_container' disabled>" . wpgmaps_get_import_logs() . "</textarea>
            </div>
			
            <br /><br /><a href='http://www.wpgmaps.com/documentation/exporting-and-importing-your-markers/' target='_BLANK'>".__("Need help? Read the documentation.","wp-google-maps")."</a><br />
        </div>
    ";


}

$wpgmaps_api_url = 'http://ccplugins.co/api-wpgmza-v8/';
$wpgmaps_plugin_slug = basename(dirname(__FILE__));

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'wpgmaps_check_for_plugin_update');

function wpgmaps_check_for_plugin_update($checked_data) {
	global $wpgmaps_api_url, $wpgmaps_plugin_slug, $wp_version, $wpgmza_pro_version, $wpgmza;
	
	// Comment out these two lines during testing.
	if (empty($checked_data->checked)){
		return $checked_data;
	}
		
	$args = array(
		'name' => 'WP Google Maps Pro add-on',
		'slug' => $wpgmaps_plugin_slug,
		'version' => trim( $wpgmza_pro_version ),
	);
	
	$request_string = array(
		'body' => array(
			'action' => 'basic_check', 
			'request' => serialize($args),
			'api-key' => md5(get_bloginfo('url'))
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);
	
	// Start checking for an update
	$raw_response = wp_remote_post($wpgmaps_api_url, $request_string);
	
	if (isset($raw_response)) {
		$response = false;
		if (!is_wp_error($raw_response) && !empty($raw_response['body']) && ($raw_response['response']['code'] == 200)){
			try{
				$response = @unserialize($raw_response['body']);
			} catch (Exception $e){

			} catch (Error $er){
				
			}
			
			/* 
			 * This shouldn't be dumping even in dev mode
			 * 
			 * Let's skip it, but leace the code in place, incase a dev needs it
			*/
			/*
				if(!is_null($wpgmza)){
					if($wpgmza->isInDeveloperMode() && !$response){
						var_dump($raw_response['body']);
					}
				}
			*/
		}
		
		if (is_object($response) && !empty($response)){
			// Feed the update data into WP updater
			$checked_data->response[$wpgmaps_plugin_slug .'/'. $wpgmaps_plugin_slug .'.php'] = $response;
		}
	}
	
	return $checked_data;
}

add_filter('plugins_api', 'wpgmaps_plugin_api_call', 10, 3);

function wpgmaps_plugin_api_call($def, $action, $args) {
	global $wpgmaps_plugin_slug, $wpgmaps_api_url, $wp_version;
	
	if (!isset($args->slug) || ($args->slug != $wpgmaps_plugin_slug))
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$wpgmaps_plugin_slug .'/'. $wpgmaps_plugin_slug .'.php'];
	$args->version = $current_version;
	
	$request_string = array(
			'body' => array(
				'action' => $action, 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	
	$request = wp_remote_post($wpgmaps_api_url, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		$res->name = 'WP Google Maps - Pro add-on';
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}

function wpgmaps_admin_styles_pro() {
    if (isset($_GET['page'])) {
        if(strpos($_GET['page'], "wp-google-maps") !== false){
            //wpgmza_enqueue_fontawesome();
			
            wp_register_style('wpgmaps-admin-style', plugins_url('css/wpgmaps-admin.css', __FILE__));
            wp_enqueue_style('wpgmaps-admin-style');
        }
    }
}

function wpgmza_content_filter($content) {

    $lat = get_post_meta( get_the_ID(), 'lat', true );
    $lng = get_post_meta( get_the_ID(), 'lng', true );
    $parent_id = get_post_meta( get_the_ID(), 'map_parent_id', true );
    $map_data = "";
    
    // check if the custom field has a value
    if(!empty($lat) && !empty($lng)){
       /* check if they have a parent ID set, if not, take first active available map ID */
       if (empty($parent_id) || !$parent_id) {
           global $wpdb;
           global $wpgmza_tblname_maps;

           $result = $wpdb->get_row(
            	"
                	SELECT *
                	FROM `$wpgmza_tblname_maps`
                	WHERE `active` = 0
                	ORDER BY `id` ASC
                	LIMIT 1
            	"
           	);

           	if ($result) {
                $parent_id = $result->id;
           	} else { 
           		$parent_id = false; 
           	}
       } 

       $map_data = do_shortcode("[wpgmza id='1' lat='$lat' lng='$lng' parent_id='$parent_id' mark_center='true']");
    }   
    
    
    return $content.$map_data;
}
add_filter( 'the_content', 'wpgmza_content_filter' );

function wpgmaps_list_maps_pro() 
{
	$adminMapDataTableOptions = array(
		"pageLength" => 25,
		 "order" => [[ 1, "desc" ]]
    );

	$adminMapDataTable = new \WPGMZA\AdminMapDataTable(null, $adminMapDataTableOptions);
	echo $adminMapDataTable->document->html;
}


/* Takes three arrays and filters default map data accordingly
 * Data Content Array -  Array with default values
 * Data Keys - Keys to override default values
 * Data Values - Values associated to each key in array
*/
function wpgmza_wizard_data_filter($wpgmza_map_data_content, $wpmgza_map_data_keys, $wpmgza_map_data_values){

    for($i = 0; $i < count($wpmgza_map_data_keys); $i++){
    	if($i < count($wpmgza_map_data_keys) -1){
    		$wpgmza_map_data_content[$wpmgza_map_data_keys[$i]] = $wpmgza_map_data_values[$i]; //Change value at index
    	} else {
    		//Deal with other settings here
    		$new_other_settings = explode("@", $wpmgza_map_data_values[$i]);
    		$other_settings_to_pass = array();

    		for($b = 0; $b <  count($new_other_settings); $b ++){
    			if($b % 2 == 0){
    				//Is key
    				$other_settings_to_pass[ $new_other_settings[ $b ] ] = $new_other_settings[ $b+1 ];
    			}
    		}
    		$wpgmza_map_data_content[$wpmgza_map_data_keys[$i]] = maybe_serialize($other_settings_to_pass);
    	}
    }
    return $wpgmza_map_data_content;
}

/**
 * Migrates text lat/lng columns into spatial latlng column if necessary
 * @return void
 */
if(!function_exists('wpgmza_migrate_spatial_data'))
{
	function wpgmza_migrate_spatial_data() {
		
		global $wpdb;
		global $wpgmza_tblname;
		
		if(empty($wpgmza_tblname))
			return;
		
		if(!$wpdb->get_var("SHOW COLUMNS FROM ".$wpgmza_tblname." LIKE 'latlng'"))
			$wpdb->query('ALTER TABLE '.$wpgmza_tblname.' ADD latlng POINT');
		
		if($wpdb->get_var("SELECT COUNT(id) FROM $wpgmza_tblname WHERE latlng IS NULL LIMIT 1") == 0)
			return; // Nothing to migrate
		
		$wpdb->query("UPDATE ".$wpgmza_tblname." SET latlng=PointFromText(CONCAT('POINT(', CAST(lat AS DECIMAL(18,10)), ' ', CAST(lng AS DECIMAL(18,10)), ')'))");
	}
	
	add_action('init', 'wpgmza_migrate_spatial_data', 1);
}

function wpgmza_upload_base64_image()
{
	global $wpgmza;
	
	// Load media functions
	wpgmza_require_once( ABSPATH . 'wp-admin/includes/file.php' );
	wpgmza_require_once( ABSPATH . 'wp-admin/includes/media.php' );
	wpgmza_require_once( ABSPATH . 'wp-admin/includes/image.php' );
	
	// Security checks
	check_ajax_referer( 'wpgmza', 'security' );
	
	if(!$wpgmza->isUserAllowedToEdit())
	{
		http_response_code(401);
		exit;
	}
	
	// Handle upload
	$upload_dir = wp_upload_dir();
	$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
	$base64_img = $_POST['data'];
	$image_data = preg_replace('/^data:.+?;base64,/', '', $base64_img);
	$image_data = base64_decode($image_data);
	
	$filename = uniqid('', true);
	
	switch($_POST['mimeType'])
	{
		case 'image/jpg':
		case 'image/jpeg':
			$filename .= '.jpg';
			break;
			
		default:
			$filename .= '.png';
			break;
	}
	
	$tmp_name = $upload_path . $filename;
	
	file_put_contents($tmp_name, $image_data);
	
	$file = array(
		'error'		=> 0,
		'tmp_name'	=> $tmp_name,
		'name'		=> $filename,
		'type'		=> $_POST['mimeType'],
		'size'		=> filesize($tmp_name)
	);
	
	$result = wp_handle_sideload($file, array('test_form' => false));
	
	$attachment	= array(
		'post_title' 		=> basename($result['file']),
		'post_content'		=> '',
		'post_status'		=> 'inherit',
		'post_mime_type'	=> $result['type']
	);
	
	$attachment_id = wp_insert_attachment(
		$attachment,
		$result['file']
	);
	
	$meta_data = wp_generate_attachment_metadata($attachment_id, $result['file']);
	
	wp_update_attachment_metadata($attachment_id, $meta_data);
	
	wp_send_json($result);
	exit;
}

add_action('wp_ajax_wpgmza_upload_base64_image', 'wpgmza_upload_base64_image');

function wpgmaps_get_import_logs(){
	$importLogPath = plugin_dir_path(__FILE__) . 'includes/import-export/import.log';
	if(file_exists($importLogPath)){
		try{
			$fileSizeLimit = 5242880; // Around 5MB
			if(filesize($importLogPath) > $fileSizeLimit){
				// File has gotten excessively large, destory the file now
				unlink($importLogPath);
			} else {
				return file_get_contents($importLogPath);
			}
		} catch (Exception $ex){

		} catch (Error $er){

		}
		return "Could not access import log";
	}

	return "No logs found...";
}

add_action("wpgmza_base_upgrade_hook", function(){
	global $wpgmza;
	if(empty($wpgmza->settings->disable_automatic_backups)){
		if(class_exists('WPGMZA\\Backup')){
			$backup = new WPGMZA\Backup();
			$backup->createBackup(false, WPGMZA\Backup::FLAG_TYPE_POST_UPDATE);
		}
	}
});