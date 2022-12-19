<?php

namespace WPGMZA;

class Category
{
	public static function doesMarkersHasCategoriesTableExist()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		$stmt = $wpdb->prepare("SHOW TABLES LIKE %s", array($WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES));
		$table = $wpdb->get_var($stmt);
		
		return ($table ? true : false);
	}
	
	public static function installMarkerHasCategoriesTable()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		if(Category::doesMarkersHasCategoriesTableExist())
			return;
		
		$wpdb->query("CREATE TABLE `$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES` (
				marker_id int(11) NOT NULL,
				category_id int(11) NOT NULL,
				PRIMARY KEY  (marker_id, category_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");		
	}
	
	public static function getOrderBy($tableName="")
	{
		global $wpgmza;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		
		$setting = (empty($wpgmza->settings->order_categories_by) ? 'priority' : $wpgmza->settings->order_categories_by);
		
		if(empty($tableName))
			$tableName = $WPGMZA_TABLE_NAME_CATEGORIES;
		
		switch($setting)
		{
			case 'category_name':
				return "$tableName.category_name ASC";
				break;
			
			case 'id':
				return "$tableName.id ASC";
				break;
			
			default:	
				return "$tableName.priority DESC, $tableName.category_name ASC";
				break;
		}
	}
	
	/**
	 * This function completely rebuilds the markers-has-categories table
	 * from the legacy marker "category" field
	 */
	public static function rebuildTableFromLegacyField($options=null)
	{
		global $wpdb;
		global $wpgmza_tblname;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		Category::installMarkerHasCategoriesTable();
		
		if(!$wpgmza_tblname)
			return; // NB: Basic not activated, marker table name is unknown
		
		if(!$options)
			$options = array();
		
		$map_id = null;
		$marker_id = null;
		
		if(!empty($options['map']))
			$map_id = $options['map']->id;
		
		if(!empty($options['marker']))
			$marker_id = $options['marker']->id;
		
		if(!empty($options['marker_id']))
			$marker_id = $options['marker_id'];
		
		// Delete old relationships
		if($map_id)
			$where = " WHERE marker_id IN (SELECT id FROM $wpgmza_tblname WHERE map_id=" . (int)$map_id . ")";
		else if($marker_id)
			$where = " WHERE marker_id=" . (int)$marker_id;
		else
			$where = "";
		
		$qstr = "DELETE FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES $where";
		
		$wpdb->query($qstr);
		
		// Rebuild relationships
		if($map_id)
			$where = " WHERE map_id=" . (int)$map_id;
		else if($marker_id)
			$where = " WHERE id=" . (int)$marker_id;
		
		$qstr = "SELECT id, category FROM $wpgmza_tblname $where";
		
		$markers = $wpdb->get_results($qstr);
		
		foreach($markers as $marker)
		{
			if(empty($marker->category))
				continue;
			
			$categories = explode(',', $marker->category);
			
			foreach($categories as $category_id)
			{
				if(array_search($category_id, $categories) === false)
					continue;
				
				$qstr = "INSERT INTO $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES (marker_id, category_id) VALUES (%d, %d) ON DUPLICATE KEY UPDATE marker_id=marker_id";
				
				$stmt = $wpdb->prepare($qstr, array($marker->id, $category_id));
				
				$wpdb->query($stmt);
			}
		}
	}
}

add_action('init', function() {
	// First time, for upgrading users and new users
	if(!Category::doesMarkersHasCategoriesTableExist())
		Category::rebuildTableFromLegacyField();
}, 100);

/*add_action('wpgmza_marker_saved', function($marker) {
	Category::rebuildTableFromLegacyField(array(
		'marker' => $marker
	));
});

add_action('wpgmza_marker_deleted', function($marker_id) {
	Category::rebuildTableFromLegacyField(array(
		'marker_id' => $marker_id
	));
});

add_action('wpgmza_categories_saved',	array('WPGMZA\\Category', 'rebuildTableFromLegacyField'));
add_action('wpgmza_category_deleted',	array('WPGMZA\\Category', 'rebuildTableFromLegacyField'));
add_action('wpgmza_import_complete',	array('WPGMZA\\Category', 'rebuildTableFromLegacyField'));*/
