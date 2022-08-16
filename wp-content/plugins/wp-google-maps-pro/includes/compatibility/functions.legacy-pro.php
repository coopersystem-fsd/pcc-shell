<?php

function wpgmza_return_all_map_ids() {
    global $wpdb;
    global $wpgmza_tblname_maps;
    $sql = "SELECT `id` FROM `".$wpgmza_tblname_maps."` WHERE `active` = 0";
    $results = $wpdb->get_results($sql);
    $tarr = array();
    foreach ($results as $result) {
        array_push($tarr,$result->id);
    }
    return $tarr;

}

function wpgmza_localize_category_data()
{
	global $wpdb;
	global $wpgmza_tblname_categories;
	
	$data = array();
	$categories = $wpdb->get_results("SELECT * FROM $wpgmza_tblname_categories");
	
	foreach($categories as $category) {
		$category->category_icon = (empty($category->category_icon) ? WPGMAPS_DIR . 'images/marker.png' : $category->category_icon);
		$data[$category->id] = $category;
	}
	
	wp_enqueue_script('wpgmza_dummy', plugin_dir_url(WPGMZA_PRO_FILE) . 'dummy.js');
	wp_localize_script('wpgmza_dummy', 'wpgmza_category_data', $data);
}

if(!function_exists('wpgmza_get_marker_columns'))
{
    function wpgmza_get_marker_columns()
    {
        global $wpdb;
		global $wpgmza;
        global $wpgmza_tblname;
        global $wpgmza_pro_version;
        
        $useSpatialData = empty($wpgmza_pro_version) || version_compare('7.0', $wpgmza_pro_version, '>=');
        
        $columns = $wpdb->get_col("SHOW COLUMNS FROM $wpgmza_tblname");
        
        if($useSpatialData)
        {
            if(($index = array_search('lat', $columns)) !== false)
                array_splice($columns, $index, 1);
            if(($index = array_search('lng', $columns)) !== false)
                array_splice($columns, $index, 1);
        }
        
        for($i = count($columns) - 1; $i >= 0; $i--)
            $columns[$i] = '`' . trim($columns[$i], '`') . '`';
        
        if($useSpatialData)
        {
            $columns[] = "{$wpgmza->spatialFunctionPrefix}X(latlng) AS lat";
            $columns[] = "{$wpgmza->spatialFunctionPrefix}Y(latlng) AS lng";
        }
        
        return $columns;
    }
}

function wpgmaps_return_marker_anchors($mid) {
	/* deprecated in 6.09 - causes irrelevant anchors (for each marker) to be displayed on the map only for the event of clicking on the marker and centering the page to the top of the map. A single anchor can achieve the same */
	return "";
}