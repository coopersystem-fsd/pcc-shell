<?php

namespace WPGMZA\Integration;

if(!class_exists('WPGMZA\\Integration\\Gutenberg'))
	return;

class ProGutenberg extends Gutenberg
{
	public function __construct()
	{
		Gutenberg::__construct();
		
		add_filter('wpgmza_plugin_get_localized_data', array(
			$this,
			'onPluginGetLocalizedData'
		));
	}
	
	public function onPluginGetLocalizedData($data)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		
		// TODO: Add deleted column, active = 0 is misleading. Deprecate this column
		$maps = $wpdb->get_results("SELECT id, map_title FROM $WPGMZA_TABLE_NAME_MAPS WHERE active = 0");
		
		$data['gutenbergData'] = array(
			'maps' => $maps
		);
		
		return $data;
	}
	
	public function onRender($attr)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		
		$mapID = '1';
		
		if(isset($attr['id']))
			$mapID = $attr['id'];
		else
			$mapID = $wpdb->get_var("SELECT id FROM $WPGMZA_TABLE_NAME_MAPS LIMIT 1");
		
		$output_attributes = array(
			'id' => $mapID
		);
		
		if(!empty($attr['mashup_ids']))
		{
			$mashup_ids = $attr['mashup_ids'];
			
			// Main ID isn't implicitly included, so include it here if it's not present already
			if(array_search($mapID, $mashup_ids) === false)
				$mashup_ids[] = $mapID;
			
			$output_attributes['mashup']		= 'true';
			$output_attributes['mashup_ids']	= implode(',', $mashup_ids);
			$output_attributes['parent_id']		= $mapID;
		}
		
		if(!empty($attr['className']))
		{
			$output_attributes['classname']		= $attr['className'];
		}
		
		
		if(!empty($attr['marker']) && $attr['marker'] != 'none')
			$output_attributes['marker'] = $attr['marker'];
		
		if(!empty($attr['zoom']))
			$output_attributes['zoom'] = $attr['zoom'];
		
		if(!empty($attr['cat']) && $attr['cat'] != 'none')
			$output_attributes['cat'] = $attr['cat'];
		
		$attributes_string = '';
		
		foreach($output_attributes as $key => $value)
		{
			$attributes_string .= " {$key}=\"" . addslashes($value) . "\"";
		}
		
		$string = "[wpgmza{$attributes_string}]";
		
		return $string;
	}
}

add_filter('wpgmza_create_WPGMZA\\Integration\\Gutenberg', function($input) {
	
	return new ProGutenberg();
	
}, 10, 1);
