<?php

namespace WPGMZA;

global $wpdb;
global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;

$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS = $wpdb->prefix . 'wpgmza_markers_has_custom_fields';

require_once(__DIR__ . '/class.custom-map-object-fields.php');

class CustomMarkerFields extends CustomFeatureFields
{
	public function __construct($marker_id)
	{
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		CustomFeatureFields::__construct($marker_id, $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS);
	}
	
	public static function getCustomFieldValues($map_id, $field_id)
	{
		global $wpdb;
		global $wpgmaps_tblname;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		$qstr = "
			SELECT value
			FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS
			WHERE object_id IN (
				SELECT id FROM $wpgmaps_tblname WHERE map_id = %d
			)
			AND field_id = %d
			GROUP BY value
		";
		
		$params = array($map_id, $field_id);
		
		$stmt = $wpdb->prepare($qstr, $params);
		
		return $wpdb->get_results($stmt);
	}
	
}

// Hook into this filter to use your own subclass of CustomMarkerFields (for example, for custom layout)
add_filter('wpgmza_get_marker_custom_fields', 'WPGMZA\\get_marker_custom_fields');
function get_marker_custom_fields($marker_id)
{
	return new CustomMarkerFields($marker_id);
}
