<?php

namespace WPGMZA;

require_once(plugin_dir_path(__FILE__) . 'class.custom-fields.php');
require_once(plugin_dir_path(__FILE__) . 'class.custom-marker-fields.php');

// TODO: Extend factory
class CustomFieldFilter
{
	private $field_id;
	private $map_id;
	private $values;
	private $field_data;
	
	private static $cached_widget_types_by_filter_id;
	
	public function __construct($field_id, $map_id)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		// TODO: Not sure why this has to be set here, seems some things are out of sequence
		$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS = $wpdb->prefix . "wpgmza_markers_has_custom_fields";
		
		if(!is_numeric($field_id))
			throw new \Exception('Invalid field ID');
		
		$this->field_id = $field_id;
		$this->map_id = $map_id;
		
		$results = $wpdb->get_results("SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS WHERE id = " . (int)$field_id);
		if(!empty($results))
			$this->field_data = $results[0];
		
		$qstr = "SELECT value 
			FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS 
			WHERE object_id IN (
				SELECT id FROM {$wpdb->prefix}wpgmza WHERE map_id = %d
			) AND field_id = %d
			AND LENGTH(value) > 0
			GROUP BY value";
		
		$stmt = $wpdb->prepare($qstr, array($map_id, $field_id));
		$this->values = $wpdb->get_col($stmt);
	}
	
	public static function setEnabledFilters($map_id, $field_ids)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		
		$qstr = "DELETE FROM $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS WHERE map_id=%d";
		$params = array($map_id);
		
		// Remove redundant rows
		if(!empty($field_ids))
		{
			$field_ids = array_map('intval', $field_ids);
			$imploded = implode(',', $field_ids);
			
			$qstr .= " AND field_id NOT IN ($imploded)";
		}
		
		$stmt = $wpdb->prepare($qstr, $params);
		
		$wpdb->query($stmt);
		
		// Insert the new ones
		$qstr = "INSERT INTO $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS 
			(map_id, field_id) 
			VALUES 
			(%d, %d) 
			ON DUPLICATE KEY UPDATE field_id=%d";
			
		foreach($field_ids as $field_id)
		{
			$stmt = $wpdb->prepare($qstr, array(
				$map_id,
				$field_id,
				$field_id
			));
			
			$wpdb->query($stmt);
		}
	}
	
	public static function getWidgetType($field_id)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		if(CustomFieldFilter::$cached_widget_types_by_filter_id)
			return CustomFieldFilter::$cached_widget_types_by_filter_id[$field_id];
		
		CustomFieldFilter::$cached_widget_types_by_filter_id = array();
		
		$results = $wpdb->get_results("SELECT id, widget_type FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");
		
		foreach($results as $obj)
			CustomFieldFilter::$cached_widget_types_by_filter_id[$obj->id] = $obj->widget_type;
		
		return CustomFieldFilter::$cached_widget_types_by_filter_id[$field_id];
	}
	
	public function getMapID()
	{
		return $this->map_id;
	}
	
	public function getFieldID()
	{
		return $this->field_id;
	}
	
	public function getFieldValues()
	{
		return $this->values;
	}
	
	public function getFieldData()
	{
		return $this->field_data;
	}
	
	public function getFilteringSQL($value)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		$qstr = "SELECT object_id AS id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS 
			WHERE field_id = %d";
			
		$params = array(
			$this->field_id
		);
		
		if(is_array($value))
		{
			$parts = array();
			
			foreach($value as $el)
			{
				$parts[] = 'value LIKE %s';
				$params[] = $el;
			}
			
			$qstr .= ' AND (' . implode(' OR ', $parts) . ')';
		}
		else
		{
			$qstr .= ' AND value LIKE %s';
			$params[] = '%' . $value . '%';
		}
		
		$qstr .= " AND object_id IN (
				SELECT id FROM {$wpdb->prefix}wpgmza WHERE map_id=%d
			)";
		$params[] = $this->map_id;
		
		$stmt = $wpdb->prepare($qstr, $params);
		
		return $stmt;
	}

	public function getBetweenFilteringSQL($value1, $value2)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		$qstr = "SELECT object_id AS id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS 
			WHERE field_id = %d";
			
		$params = array(
			$this->field_id
		);
		
		$qstr .= ' AND value BETWEEN %s AND %s';
			$params[] = $value1;
			$params[] = $value2;
		
		$qstr .= " AND object_id IN (
				SELECT id FROM {$wpdb->prefix}wpgmza WHERE map_id=%d
			)";
		$params[] = $this->map_id;
		$stmt = $wpdb->prepare($qstr, $params);
		
		return $stmt;
	}
}

add_filter('wpgmza_get_custom_field_filter', 'WPGMZA\\get_custom_field_filter', 100, 2);

function get_custom_field_filter($field_id, $map_id)
{
	if($field_id instanceof CustomFieldFilter)
		return $field_id;
	
	return new CustomFieldFilter($field_id, $map_id);
}

