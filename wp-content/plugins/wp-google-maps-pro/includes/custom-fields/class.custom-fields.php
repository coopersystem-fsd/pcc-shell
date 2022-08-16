<?php

namespace WPGMZA;

require_once(plugin_dir_path(__FILE__) . 'class.custom-field-filter.php');

/**
 * This class deals with custom fields in general, as opposed to custom fields concerning a specific object.
 */
 
class CustomFields implements \IteratorAggregate, \JsonSerializable, \Countable
{
	private static $installed = null;
	private $fields;
	
	public function __construct($map_id=null)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		
		if(!CustomFields::installed())
			CustomFields::install();
		
		$qstr = "SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS";
		
		if($map_id != null)
		{
			$map_id = (int)$map_id;
			
			$qstr .= " WHERE id IN (SELECT field_id FROM $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS WHERE map_id=$map_id)";
		} 

		$qstr .= " ORDER BY stack_order ASC";
		
		$this->fields = $wpdb->get_results($qstr);
	}
	
	/**
	 * Installs the DB tables for custom fields
	 * @return void
	 */
	public static function install()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
			
		dbDelta("
			CREATE TABLE `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(128) NOT NULL,
				`icon` VARCHAR(128) NOT NULL,
				`attributes` TEXT,
				`widget_type` VARCHAR(128) DEFAULT 'none',
				`display_in_infowindows` BOOLEAN DEFAULT 1,
				`display_in_marker_listings` BOOLEAN DEFAULT 1,
				`stack_order` int(11),
				PRIMARY KEY  (id),
				UNIQUE KEY name (name)
			);
		");
		
		dbDelta("
			CREATE TABLE `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS` (
				`field_id` int(11) NOT NULL,
				`object_id` int(11) NOT NULL,
				`value` TEXT,
				PRIMARY KEY  (`field_id`, `object_id`)
			);
		");
		
		dbDelta("
			CREATE TABLE `$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS` (
				`map_id` int(11) NOT NULL,
				`field_id` int(11) NOT NULL,
				PRIMARY KEY  (`map_id`, `field_id`)
			);
		");
		
		CustomFields::$installed = true;
	}
	
	/**
	 * Returns true if the custom fields tables are installed, caches the 
	 * result of the DB query for performance.
	 * TODO: Modify this to count instead so that adding polygon tables etc. will be done
	 * @return bool
	 */
	public static function installed()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		if(CustomFields::$installed !== null)
			return CustomFields::$installed;
		
		$customFieldsTableInstalled 				= $wpdb->get_var("SHOW TABLES LIKE '$WPGMZA_TABLE_NAME_CUSTOM_FIELDS'");
		$markerHasCustomFieldsTableInstalled		= $wpdb->get_var("SHOW TABLES LIKE '$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS'");
		$mapsHasCustomFieldFiltersTableInstalled	= $wpdb->get_var("SHOW TABLES LIKE '$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS'");
		$markersHasCustomFieldsTableInstalled		= $wpdb->get_var("SHOW TABLES LIKE '$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS'");
		
		CustomFields::$installed = (
			!empty($customFieldsTableInstalled) &&
			!empty($markerHasCustomFieldsTableInstalled) &&
			!empty($mapsHasCustomFieldFiltersTableInstalled) &&
			!empty($markersHasCustomFieldsTableInstalled)
		);
		
		return CustomFields::$installed;
	}
	
	/**
	 * Get iterator for looping over fields with foreach
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->fields);
	}
	
	/**
	 * Gets the fields to be serialized as JSON, useful for export
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->fields;
	}
	
	
	public function count() {
		return count($this->fields);
	}
	
}
