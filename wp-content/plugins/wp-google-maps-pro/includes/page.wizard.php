<?php
/* Maps 6.0 Wizard*/


function wpgmaps_wizard_layout() {

    if (isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu-categories') {
        add_action('admin_print_scripts', 'wpgmaps_admin_wizard_scripts');
    }

    ?>
    <div class='wrap'>
    <h1><?php _e("Select a Map Type (beta)", "wp-google-maps"); ?></h1>

    <style>.wpgmza-support-notice{display:none !important;}</style>
        <div class='wide'>
            <script>
                jQuery(document).ready(function(){
                    jQuery('.wpgmza-listing-wizard-1').click(function(){
                        if (jQuery(this).attr('id') == 'wpgmza_wizard_c_btn') {
                            return;
                        }
                        jQuery('.wpgmza-listing-wizard-1').fadeIn('fast');
                        jQuery('.wpgmza-listing-wizard-2').hide();
                        jQuery(this).hide();
                        jQuery(this).next(".wpgmza-listing-wizard-2").fadeIn('fast');
                    });

                    jQuery('#wpgmza_wizard_sl_btn').click(function(){
                        var data = {
                            map_title : "Locator",
                            store_locator_enabled : "1",
                        };

                        if(jQuery('#wpgmza-wizard-sl-distance').is(':checked')){
                            data.wpgmza_store_locator_distance = "1";
                        }

                        if(jQuery("#wpgmza-wizard-sl-bounce").is(":checked")){
                            data.wpgmza_store_locator_bounce = "1";
                        }

                        if(jQuery("#wpgmza-wizard-sl-hide").is(":checked")){
                            data.wpgmza_store_locator_hide_before_search = "1";
                        }

                        wpgmzaCreateWizardMapRest(data);

                    });

                    jQuery('#wpgmza_wizard_gd_btn').click(function(){
                        var data = {
                            map_title : "Directions",
                            directions_enabled : "1",
                            default_to : jQuery('#wpgmza-wizard-gd-to-address').val()
                        };

                        wpgmzaCreateWizardMapRest(data);

                    });

                    jQuery('#wpgmza_wizard_ml_btn').click(function(){
                        var data = {
                            map_title : "Listing",
                            wpgmza_listmarkers_by : jQuery('#wpgmza-wizard-ml-list-by-select').val()
                        };

                        wpgmzaCreateWizardMapRest(data);
                    });

                    jQuery('#wpgmza_wizard_c_btn').click(function(){
                        wpgmzaCreateWizardMapRest();
                    });

                    <?php do_action("wpgmza_wizard_jquery_action", 10);?>

                });

                function wpgmzaCreateWizardMapRest(data){
                    if(typeof data === 'undefined'){
                        data = {
                            map_title:      WPGMZA.localized_strings.new_map,
                            map_start_lat:  36.778261,
                            map_start_lng:  -119.4179323999,
                            map_start_zoom: 3
                        };
                    }

                    WPGMZA.restAPI.call("/maps/", {
                        method: "POST",
                        data: data,
                        success: function(response, status, xhr) {
                            window.location.href = window.location.href = "admin.php?page=wp-google-maps-menu&action=edit&map_id=" + response.id;
                        }
                    });
                }

                /**
                 * Deprecated: 2020-12-22
                 * Note: We now use the REST API to generate the map, before redirecting to the editor
                */
                function updateLink(buttonID, optionsArray){
                    var queryString = "?page=wp-google-maps-menu&action=new-wizard";
                    var valuesArray = new Array();
                    queryString += "&wpgmza_keys=";

                    var otherSettings = "";

                    for(i = 0; i < optionsArray.length; i++){
                        if(jQuery(optionsArray[i]).attr('wpgmza-other-setting')){
                            //Handle this differently
                            
                            if(jQuery(optionsArray[i]).attr('wpgmza-dropdown') == "true"){
                                otherSettings += jQuery(optionsArray[i]).attr('wpgmza-key') + "@" + (parseInt(jQuery(optionsArray[i] + " option:selected").attr('value'))) + (i < optionsArray.length -1 ? "@" : "");
                                
                            }else{
                                otherSettings += jQuery(optionsArray[i]).attr('wpgmza-key') + "@" +  (jQuery(optionsArray[i]).attr('checked') ? "1" : "0") + (i < optionsArray.length -1 ? "@" : ""); //Add key to other setting array
                            }
                            //console.log(jQuery(optionsArray[i]).attr('wpgmza-key'));
                        }else{
                            queryString += jQuery(optionsArray[i]).attr('wpgmza-key') + "," ; //Add key
                            valuesArray.push(jQuery(optionsArray[i]).attr('checked'));
                        }
                    }

                     //Now add 'OTHER SETTINGS'
                    
                    queryString += "other_settings";
                    

                    queryString += "&wpgmza_values=";
                    for(i = 0; i < valuesArray.length; i++){
                        if(jQuery(optionsArray[i]).attr('type') == "checkbox"){
                            queryString += (valuesArray[i] ? "1" : "0") + (i < valuesArray.length-1 || otherSettings !== "" ? ",": ""); //Add key
                        }else if(jQuery(optionsArray[i]).attr('type') == "text"){
                            queryString += (jQuery(optionsArray[i]).val()) + (i < valuesArray.length-1 || otherSettings !== "" ? ",": "");
                        }else if(jQuery(optionsArray[i]).attr('wpgmza-dropdown') == "true"){
                            queryString += (parseInt(jQuery(optionsArray[i] + " option:selected").attr('value'))) + (i < valuesArray.length-1 || otherSettings !== "" ? ",": "");
                        }
                    }

                     //Now add 'OTHER SETTINGS'
                    if(otherSettings != ""){
                        queryString += otherSettings;
                    }
                    
                    jQuery(buttonID).attr('url', queryString);
                }

            </script>

            <?php
                $wpgmza_wizard_content = "<div id='wpgmza-wizard-options' class='wpgmza-flex'>";
                $wpgmza_wizard_content = apply_filters("wpgmza_wizard_content_filter", $wpgmza_wizard_content, 10, 1) ;
                $wpgmza_wizard_content .= '</div>';
                echo $wpgmza_wizard_content;
            ?> 

        </div>
    </div>

    <?php
    

}

function wpgmaps_admin_wizard_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('jquery-ui-core');

}

add_filter("wpgmza_wizard_content_filter", "wpgmza_wizard_item_control_sl");
function wpgmza_wizard_item_control_sl($content){
    $content .= "
        <div class='wpgmza-listing-comp wpgmza-listing-wizard'>
            <div class='wpgmza-card wpgmza-card-border__hover'>
                <div class='wpgmza-listing-wizard-1'>
                    <div class='wpmgza-listing-1-icon'>
                        <i class='fa fa-building-o'></i>
                    </div>  
                    <h2 style='text-align:center' class='wpgmza-wizard-option__title'>".__("Store Locator", "wp-google-maps")."</h2>
                </div>
                <div class='wpgmza-listing-wizard-2' style='display:none;'>
                    <div style='font-size:18px' class='wpgmza-wizard-option__info-title'><i class='fa fa-building-o'></i> ".__("Store Locator", "wp-google-maps")."</div>
                    <div>
                        <input type='text' wpgmza-key='map_title' style='display:none' id='wpgmza-wizard-sl-title' value='Store Locator Map'>
                        <input type='checkbox' wpgmza-other-setting='true' wpgmza-key='store_locator_enabled' style='display:none' id='wpgmza-wizard-sl-enabled' checked>
                        <table style='width:100%'>
                            <tr>
                                <td>
                                    ".__("Show distance in:", "wp-google-maps")."
                                </td>
                                <td style='text-align:right;'>
                                    <div class='switch'>
                                        <input type='checkbox' wpgmza-other-setting='true' wpgmza-key='store_locator_distance' class='cmn-toggle cmn-toggle-yes-no' id='wpgmza-wizard-sl-distance'><label style='width:66px !important' for='wpgmza-wizard-sl-distance' data-on='".__("Miles", "wp-google-maps")."' data-off='".__("Kilometers", "wp-google-maps")."'></label>
                                    </div> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    ".__("Show bouncing icon:", "wp-google-maps")."
                                </td>
                                <td style='text-align:right;'>
                                     <div class='switch'>
                                        <input type='checkbox' wpgmza-other-setting='true' wpgmza-key='store_locator_bounce' class='cmn-toggle cmn-toggle-round-flat' id='wpgmza-wizard-sl-bounce'><label for='wpgmza-wizard-sl-bounce'></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    ".__("Hide markers until search is done:", "wp-google-maps")."
                                </td>
                                <td style='text-align:right;'>
                                    <div class='switch'>
                                        <input type='checkbox' wpgmza-other-setting='true' wpgmza-key='store_locator_hide_before_search' class='cmn-toggle cmn-toggle-round-flat' id='wpgmza-wizard-sl-hide'><label for='wpgmza-wizard-sl-hide'></label>
                                    </div> 
                                </td>
                            </tr>
                        </table>              
                    </div>
                <button style='position:absolute;bottom:5px;' class='wpgmza_createmap_btn' id='wpgmza_wizard_sl_btn' url=''>".__("Create Map", "wp-google-maps")."</button>
            </div>
            </div>
        </div>
    ";
    return $content;
}

add_filter("wpgmza_wizard_content_filter", "wpgmza_wizard_item_control_gd",10,1);
function wpgmza_wizard_item_control_gd($content){
    $content .= "
        <div class='wpgmza-listing-comp wpgmza-listing-wizard'>
            <div class='wpgmza-card wpgmza-card-border__hover'>
                <div class='wpgmza-listing-wizard-1'>
                    <div class='wpmgza-listing-1-icon'>
                        <i class='fa fa-compass'></i>
                    </div> 
                        <h2 style='text-align:center' class='wpgmza-wizard-option__title'>".__("Directions", "wp-google-maps")."</h2>
                 </div>
                    <div class='wpgmza-listing-wizard-2' style='display:none;'>
                        <div style='font-size:18px' class='wpgmza-wizard-option__info-title'><i class='fa fa-compass'></i> ".__("Directions", "wp-google-maps")."</div> 
                        <div>
                            <input type='text' wpgmza-key='map_title' style='display:none' id='wpgmza-wizard-gd-title' value='Directions Map'>
                            <input type='checkbox' wpgmza-key='directions_enabled' style='display:none' id='wpgmza-wizard-gd-enabled' checked >

                            <table style='width:100%'>
                                <tr>
                                    <td>
                                        ".__("Default 'To' Address:", "wp-google-maps")."
                                    </td>
                                    <td style='text-align:right;'>
  
                                            <input type='text' wpgmza-key='default_to' id='wpgmza-wizard-gd-to-address' value='' placeholder='".__("Enter Address", "wp-google-maps")."'>
                                        
                                    </td>
                                </tr>
                            </table>
                             
                             
                        </div>
                        <button style='position:absolute;bottom:5px;' class='wpgmza_createmap_btn' id='wpgmza_wizard_gd_btn' url=''>".__("Create Map", "wp-google-maps")."</button>
                </div>
            </div>
        </div>
    ";
    return $content;
}

add_filter("wpgmza_wizard_content_filter", "wpgmza_wizard_item_control_ml",10,1);
function wpgmza_wizard_item_control_ml($content){
    $content .= "
        <div class='wpgmza-listing-comp wpgmza-listing-wizard'>
           <div class='wpgmza-card wpgmza-card-border__hover'>
                <div class='wpgmza-listing-wizard-1'>
                    <div class='wpmgza-listing-1-icon'>
                        <i class='fa fa-list'></i>
                    </div>  
                    <h2 style='text-align:center' class='wpgmza-wizard-option__title'>".__("Marker Listing", "wp-google-maps")."</h2>
                </div>
                <div class='wpgmza-listing-wizard-2' style='display:none;'>
                    <div style='font-size:18px' class='wpgmza-wizard-option__info-title'><i class='fa fa-list'></i> ".__("Marker Listing", "wp-google-maps")."</div> 
                        <div>
                            <input type='text' wpgmza-key='map_title' style='display:none' id='wpgmza-wizard-ml-title' value='Marker Listing Map'>

                            <table style='width:100%'>
                                <tr>
                                    <td>
                                        ".__("Marker Listing Style", "wp-google-maps")."
                                    </td>
                                    <td style='text-align:right;'>
  
                                            <select id='wpgmza-wizard-ml-list-by-select' wpgmza-dropdown='true' wpgmza-other-setting='true' wpgmza-key='list_markers_by'>
                                                <option value='1'>".__("Basic Table", "wp-google-maps")."</option>
                                                <option value='4'>".__("Basic List", "wp-google-maps")."</option>
                                                <option value='2' selected>".__("Advanced Table", "wp-google-maps")."</option>
                                                <option value='3'>".__("Carousel", "wp-google-maps")."</option>
                                            </select>
                                        
                                    </td>
                                </tr>
                            </table>
                             
                             
                        </div>
                   <button style='position:absolute;bottom:5px;' class='wpgmza_createmap_btn' id='wpgmza_wizard_ml_btn' url=''>".__("Create Map", "wp-google-maps")."</button>
                </div>
            </div>
        </div>
    ";
    return $content;
}

add_filter("wpgmza_wizard_content_filter", "wpgmza_wizard_item_control_c",1,1);
function wpgmza_wizard_item_control_c($content){
    $content .= "
        <div class='wpgmza-listing-comp wpgmza-listing-wizard'>
            <div class='wpgmza-card wpgmza-card-border__hover'>
                <div class='wpgmza-listing-wizard-1' id='wpgmza_wizard_c_btn'>
                    <div class='wpmgza-listing-1-icon'>
                        <i class='fa fa-map-o'></i>
                    </div>  
                    <h2 style='text-align:center' class='wpgmza-wizard-option__title'>".__("Blank Map", "wp-google-maps")."</h2>
                </div>
                <div class='wpgmza-listing-wizard-2' style='display:none;'>

                </div>
            </div>
        </div>
    ";
    return $content;
}

function wpgmza_legacy_wizard_create_map()
{
	global $wpdb;
	global $wpgmza_tblname_maps;
	
	$def_data = get_option("WPGMZA_SETTINGS");
	
	if (isset($def_data->map_default_starting_lat)) { $data['map_default_starting_lat'] = $def_data->map_default_starting_lat; }
	if (isset($def_data->map_default_starting_lng)) { $data['map_default_starting_lng'] = $def_data->map_default_starting_lng; }
	if (isset($def_data->map_default_height)) { $data['map_default_height'] = $def_data->map_default_height; }
	if (isset($def_data->map_default_width)) { $data['map_default_width'] = $def_data->map_default_width; }
	if (isset($def_data->map_default_height_type)) { $data['map_default_height_type'] = stripslashes($def_data->map_default_height_type); }
	if (isset($def_data->map_default_width_type)) { $data['map_default_width_type'] =stripslashes($def_data->map_default_width_type); }
	if (isset($def_data->map_default_zoom)) { $data['map_default_zoom'] = $def_data->map_default_zoom; }
	if (isset($def_data->map_default_type)) { $data['map_default_type'] = $def_data->map_default_type; }
	if (isset($def_data->map_default_alignment)) { $data['map_default_alignment'] = $def_data->map_default_alignment; }
	if (isset($def_data->map_default_order_markers_by)) { $data['map_default_order_markers_by'] = $def_data->map_default_order_markers_by; }
	if (isset($def_data->map_default_order_markers_choice)) { $data['map_default_order_markers_choice'] = $def_data->map_default_order_markers_choice; }
	if (isset($def_data->map_default_show_user_location)) { $data['map_default_show_user_location'] = $def_data->map_default_show_user_location; }
	if (isset($def_data->map_default_directions)) { $data['map_default_directions'] = $def_data->map_default_directions; }
	if (isset($def_data->map_default_bicycle)) { $data['map_default_bicycle'] = $def_data->map_default_bicycle; }
	if (isset($def_data->map_default_traffic)) { $data['map_default_traffic'] = $def_data->map_default_traffic; }
	if (isset($def_data->map_default_dbox)) { $data['map_default_dbox'] = $def_data->map_default_dbox; }
	if (isset($def_data->map_default_dbox_width)) { $data['map_default_dbox_width'] = $def_data->map_default_dbox_width; }
	if (isset($def_data->map_default_default_to)) { $data['map_default_default_to'] = $def_data->map_default_default_to; }
	if (isset($def_data->map_default_marker)) { $data['map_default_marker'] = $def_data->map_default_marker; }


	if (isset($def_data['map_default_height_type'])) {
		$wpgmza_height_type = $def_data['map_default_height_type'];
	} else {
		$wpgmza_height_type = "px";
	}
	if (isset($def_data['map_default_width_type'])) {
		$wpgmza_width_type = $def_data['map_default_width_type'];
	} else {
		$wpgmza_width_type = "px";
	}
	
	if (isset($def_data['map_default_height'])) {
		$wpgmza_height = $def_data['map_default_height'];
	} else {
		$wpgmza_height = "400";
	}
	if (isset($def_data['map_default_width'])) {
		$wpgmza_width = $def_data['map_default_width'];
	} else {
		$wpgmza_width = "600";
	}
	if (isset($def_data['map_default_marker'])) {
		$wpgmza_def_marker = $def_data['map_default_marker'];
	} else {
		$wpgmza_def_marker = "0";
	}
	if (isset($def_data['map_default_alignment'])) {
		$wpgmza_def_alignment = $def_data['map_default_alignment'];
	} else {
		$wpgmza_def_alignment = "0";
	}
	if (isset($def_data['map_default_order_markers_by'])) {
		$wpgmza_def_order_markers_by = $def_data['map_default_order_markers_by'];
	} else {
		$wpgmza_def_order_markers_by = "0";
	}
	if (isset($def_data['map_default_order_markers_choice'])) {
		$wpgmza_def_order_markers_choice = $def_data['map_default_order_markers_choice'];
	} else {
		$wpgmza_def_order_markers_choice = "0";
	}
	if (isset($def_data['map_default_show_user_location'])) {
		$wpgmza_def_show_user_location = $def_data['map_default_show_user_location'];
	} else {
		$wpgmza_def_show_user_location = "0";
	}
	if (isset($def_data['map_default_directions'])) {
		$wpgmza_def_directions = $def_data['map_default_directions'];
	} else {
		$wpgmza_def_directions = "0";
	}
	if (isset($def_data['map_default_bicycle'])) {
		$wpgmza_def_bicycle = $def_data['map_default_bicycle'];
	} else {
		$wpgmza_def_bicycle = "0";
	}
	if (isset($def_data['map_default_traffic'])) {
		$wpgmza_def_traffic = $def_data['map_default_traffic'];
	} else {
		$wpgmza_def_traffic = "0";
	}
	if (isset($def_data['map_default_dbox'])) {
		$wpgmza_def_dbox = $def_data['map_default_dbox'];
	} else {
		$wpgmza_def_dbox = "0";
	}
	if (isset($def_data['map_default_dbox_wdith'])) {
		$wpgmza_def_dbox_width = $def_data['map_default_dbox_width'];
	} else {
		$wpgmza_def_dbox_width = "100";
	}
	if (isset($def_data['map_default_default_to'])) {
		$wpgmza_def_default_to = $def_data['map_default_default_to'];
	} else {
		$wpgmza_def_default_to = "";
	}
	if (isset($def_data['map_default_listmarkers'])) {
		$wpgmza_def_listmarkers = $def_data['map_default_listmarkers'];
	} else {
		$wpgmza_def_listmarkers = "0";
	}
	if (isset($def_data['map_default_listmarkers_advanced'])) {
		$wpgmza_def_listmarkers_advanced = $def_data['map_default_listmarkers_advanced'];
	} else {
		$wpgmza_def_listmarkers_advanced = "0";
	}
	if (isset($def_data['map_default_filterbycat'])) {
		$wpgmza_def_filterbycat = $def_data['map_default_filterbycat'];
	} else {
		$wpgmza_def_filterbycat = "0";
	}
	if (isset($def_data['map_default_type'])) {
		$wpgmza_def_type = $def_data['map_default_type'];
	} else {
		$wpgmza_def_type = "1";
	}

	if (isset($def_data['map_default_zoom'])) {
		$start_zoom = $def_data['map_default_zoom'];
	} else {
		$start_zoom = 5;
	}
	
	if (isset($def_data['map_default_ugm_access'])) {
		$ugm_access = $def_data['map_default_ugm_access'];
	} else {
		$ugm_access = 0;
	}
	
	if (isset($def_data['map_default_starting_lat']) && isset($def_data['map_default_starting_lng'])) {
		$wpgmza_lat = $def_data['map_default_starting_lat'];
		$wpgmza_lng = $def_data['map_default_starting_lng'];
	} else {
		$wpgmza_lat = "51.5081290";
		$wpgmza_lng = "-0.1280050";
	}

	$wpgmza_map_data_content = array(
		"map_title" => "New Map",
		"map_start_lat" => "$wpgmza_lat",
		"map_start_lng" => "$wpgmza_lng",
		"map_width" => "$wpgmza_width",
		"map_height" => "$wpgmza_height",
		"map_start_location" => "$wpgmza_lat,$wpgmza_lng",
		"map_start_zoom" => "$start_zoom",
		"default_marker" => "$wpgmza_def_marker",
		"alignment" => "$wpgmza_def_alignment",
		"styling_enabled" => "0",
		"styling_json" => "",
		"active" => "0",
		"directions_enabled" => "$wpgmza_def_directions",
		"default_to" => "",
		"type" => "$wpgmza_def_type",
		"kml" => "",
		"fusion" => "",
		"map_width_type" => "$wpgmza_width_type",
		"map_height_type" => "$wpgmza_height_type",
		"fusion" => "",
		"mass_marker_support" => "0",
		"ugm_enabled" => "0",
		"ugm_category_enabled" => "0",
		"ugm_access" => "$ugm_access",
		"bicycle" => "$wpgmza_def_bicycle",
		"traffic" => "$wpgmza_def_traffic",
		"dbox" => "$wpgmza_def_dbox",
		"dbox_width" => "$wpgmza_def_dbox_width",
		"listmarkers" => "$wpgmza_def_listmarkers",
		"listmarkers_advanced" => "$wpgmza_def_listmarkers_advanced",
		"filterbycat" => "$wpgmza_def_filterbycat",
		"order_markers_by" => "$wpgmza_def_order_markers_by",
		"order_markers_choice" => "$wpgmza_def_order_markers_choice",
		"show_user_location" => "$wpgmza_def_show_user_location",
		"other_settings" => 'a:3:{s:19:"store_locator_style";s:6:"modern";s:33:"wpgmza_store_locator_radius_style";s:6:"modern";s:20:"directions_box_style";s:6:"modern";}'
		);

	//Filter Array if the wizard is in use
	if($_GET['action'] == "new-wizard"){
		if(isset($_GET['wpgmza_keys']) && isset($_GET['wpgmza_values'])){
			$wpgmza_map_data_keys = explode(",", urldecode($_GET['wpgmza_keys']));
			$wpgmza_map_data_values = explode(",", urldecode($_GET['wpgmza_values']));

			$wpgmza_map_data_content = wpgmza_wizard_data_filter($wpgmza_map_data_content, $wpgmza_map_data_keys, $wpgmza_map_data_values);
		}
	}
	$wpdb->insert( $wpgmza_tblname_maps, $wpgmza_map_data_content);
	$lastid = $wpdb->insert_id;
	
	echo $wpdb->last_error;

	$_GET['map_id'] = $lastid;
	
	wp_redirect( admin_url('admin.php?page=wp-google-maps-menu&action=edit&map_id='.$lastid) );
	
	exit;
}

add_action('init', function() {
	
	if(isset($_GET['action']) && $_GET['action'] == 'new-wizard')
		wpgmza_legacy_wizard_create_map();
	
});

// NB: GDPR
/*add_filter("wpgmza_wizard_content_filter", "wpgmza_wizard_control_feedback",99,1);
function wpgmza_wizard_control_feedback($content){
    $content .= "
           <div class='update-nag update-blue'>
                Please consider giving us feedback on our new map wizard.<br><br>
                <a target='_blank' href='http://www.wpgmaps.com/contact-us/'>Share your thoughts</a>
            </div>
    ";
    return $content;
}
*/

