<?php

namespace WPGMZA;

abstract class CategoryFilterWidget extends \WPGMZA\Factory
{
	const TYPE_DROPDOWN		= 1;
	const TYPE_CHECKBOXES	= 2;
	
	protected $_map;
	protected $_document;
	protected $_options;
	
	public function __construct($map, $options=null)
	{
		if(!($map instanceof \WPGMZA\Map))
			throw new \Exception("Argument must be an instance of \\WPGMZA\\Map");
		
		if($options)
			$this->_options = $options;
		else
			$this->_options = array();
		
		$this->_map = $map;
		$this->_document = new DOMDocument();
		
		$this->load();
		
		if(!empty($this->map->shortcodeAttributes['cat']))
			$this->select($this->map->shortcodeAttributes['cat']);
	}
	
	public abstract function load();
	public abstract function select($category_id);
	
	public function __get($name)
	{
		global $wpgmza;
		
		switch($name)
		{
			case "map":
			case "document":
			case "options":
				return $this->{"_$name"};
				break;
			
			case "html":
				return $this->document->html;
				break;
			
			case "showMarkerCount":
				return !empty($wpgmza->settings->wpgmza_settings_cat_display_qty) && $wpgmza->settings->wpgmza_settings_cat_display_qty == "yes";
				break;
		}
	}
}

add_filter('wpgmza_create_WPGMZA\\CategoryFilterWidget', function($map, $options=null) {
	
	global $wpgmza;
	
	switch($wpgmza->settings->wpgmza_settings_filterbycat_type)
	{
		case \WPGMZA\CategoryFilterWidget::TYPE_CHECKBOXES:
			return new \WPGMZA\CategoryFilterWidget\Checkboxes($map);
			break;
		
		default:
			return new \WPGMZA\CategoryFilterWidget\Dropdown($map);
			break;
	}
	
});
