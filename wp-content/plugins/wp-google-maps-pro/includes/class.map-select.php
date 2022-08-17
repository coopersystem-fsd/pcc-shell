<?php

namespace WPGMZA;

class MapSelect extends DOMDocument
{
	public function __construct($name=null)
	{
		DOMDocument::__construct();
		
		global $wpdb;
		
		$this->maps = $wpdb->get_results("SELECT id, map_title FROM {$wpdb->prefix}wpgmza_maps WHERE active=0");
		
		$this->loadHTML("<select/>");
		$select = $this->querySelector('select');
		
		if($name)
			$select->setAttribute('name', $name);
		
		foreach($this->maps as $map)
		{
			$option = $this->createElement('option');
			
			$option->setAttribute('value', $map->id);
			$option->appendText($map->map_title);
			
			$select->appendChild($option);
		}
	}
	
	public function html()
	{
		$options = "";
		foreach($this->maps as $map)
			$options .= "<option value='{$map->id}'>" . htmlentities($map->map_title) . "</option>";
		
		$html = '<select name="map">' . $options . '</select>';
		
		return $html;
	}
}