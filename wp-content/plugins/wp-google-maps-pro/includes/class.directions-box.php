<?php

namespace WPGMZA;

class DirectionsBox extends DOMDocument
{
	public function __construct($map, $shortcodeAttributes=null)
	{
		global $wpgmza;
		
		if(!($map instanceof Map))
			throw new \Exception('Argument must be an instance of \\WPGMZA\\Map');
		
		DOMDocument::__construct();
		
		$this->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/directions-box.html.php');
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		$element = $this->querySelector('.wpgmza-directions-box');
		$element->setAttribute('data-map-id', $map->id);
		
		// Legacy variables
		$default_from = empty($map->default_from) ? "" : $map->default_from;
		if(!empty($map->overrides['directions_from']))
			$default_from = $map->overrides['directions_from'];
		$element->querySelector("input.wpgmza-directions-from")->setAttribute('value', $default_from);
		
		$default_to = empty($map->default_to) ? "" : $map->default_to;
		if(!empty($map->overrides['directions_to']))
			$default_to = $map->overrides['directions_to'];
		$element->querySelector("input.wpgmza-directions-to")->setAttribute('value', $default_to);
		
		$auto = !empty($map->overrides['directions_auto']) && $map->overrides['directions_auto'] == "true";
		
		$width = !empty($map->dbox_width) ? $map->dbox_width : '50';
		$width_type = empty($map->wpgmza_dbox_width_type) ? '%' : $map->wpgmza_dbox_width_type;
		$element->setInlineStyle("width", "{$width}{$width_type}");
		
		$placement = $map->dbox;

		if(empty($wpgmza->settings->user_interface_style) || $wpgmza->settings->user_interface_style != "bear-bones"){
			switch($placement){
				case "1":
					if($wpgmza->settings->user_interface_style == "modern"){
						//Modern interface requires this as it is embedded in the map
						$element->setInlineStyle("float", "left");
						$element->setInlineStyle("overflow", "auto");
					} else {
						$element->setInlineStyle("display", "none");
						$element->setInlineStyle("clear", "both");
					}
					break;
				
				case "2":
					$element->setInlineStyle("float", "left");
					$element->setInlineStyle("overflow", "auto");
					break;
				
				case "3":
					$element->setInlineStyle("float", "right");
					$element->setInlineStyle("overflow", "auto");
					break;
				
				case "4":
					$element->setInlineStyle("float", "none");
					$element->setInlineStyle("overflow", "auto");
					$element->setInlineStyle("clear", "both");
					break;
				
				case "5":
					$element->setInlineStyle("float", "none");
					$element->setInlineStyle("overflow", "auto");
					$element->setInlineStyle("clear", "both");
					break;
				
				default:
					$element->setInlineStyle("display", "none");
					break;

			}
		}
		
		if($wpgmza->settings->engine == "open-layers")
			$element->querySelector('option[value="transit"]')->remove();
		
		if($wpgmza->settings->useLegacyHTML)
		{
			$element->addClass('wpgmaps_directions_outer_div');
			$element->setAttribute('id', 'wpgmaps_directions_edit_' . $map->id);

			if(!empty($map->directions_box_style))
			{
				$element->addClass('style-' . $map->directions_box_style);
			}
			
			$element->querySelector('.wpgmza-directions-box-inner')->setAttribute('id', "wpgmaps_directions_editbox_{$map->id}");
			
			$element->querySelector('label.wpgmza-travel-mode')->setAttribute('for', "wpgmza_dir_type_{$map->id}");
			
			$element->querySelector('select.wpgmza-travel-mode')->setAttribute('name', "wpgmza_dir_type_{$map->id}");
			$element->querySelector('select.wpgmza-travel-mode')->setAttribute('id', "wpgmza_dir_type_{$map->id}");
			
			$element->querySelector('.wpgmza-show-directions-options')->setAttribute('id', "wpgmza_show_options_{$map->id}");
			$element->querySelector('.wpgmza-hide-directions-options')->setAttribute('id', "wpgmza_hide_options_{$map->id}");
			
			$element->querySelector('.wpgmza-directions-options')->addClass("wpgmza-form-field wpgmza-form-field--no-pad wpgmza_dir_options");
			
			foreach($element->querySelectorAll('a') as $a)
			{
				$a->setAttribute('mapid', $map->id);
			}
			
			$arr = array(
				'input.wpgmza-avoid-tolls'		=> "wpgmza_tolls_{$map->id}",
				'input.wpgmza-avoid-highways'	=> "wpgmza_highways_{$map->id}",
				'input.wpgmza-avoid-ferries'	=> "wpgmza_ferries_{$map->id}"
			);
			
			foreach($arr as $selector => $value)
			{
				$element->querySelector($selector)->setAttribute("name", $value);
				$element->querySelector($selector)->setAttribute("id", $value);
				
				$label = $element->parentNode;
				$label->setAttribute("for", $value);
				$label->setAttribute("id", $value);
			}
			
			foreach($element->querySelectorAll('.wpgmza-directions-options>label') as $label)
				$label->addClass("class", "wpgmza-form-field__label");
			
			$element->querySelector("div.wpgmza-directions-from")->addClass('wpgmza-form-field');
			$element->querySelector(".wpgmza-directions-from > label")->setAttribute('for', "wpgmza_input_from_{$map->id}");
			$element->querySelector(".wpgmza-directions-from > label")->addClass("wpgmza-form-field__label");
			$element->querySelector("input.wpgmza-directions-from")->setAttribute('id', "wpgmza_input_from_{$map->id}");
			
			$element->querySelector('div.wpgmza-waypoint-via')->addClass("wpgmza-form-field wpgmza-form-field--no-pad wpgmaps_via wpgmaps_template");
			$element->querySelector("input.wpgmza-waypoint-via")->addClass('wpgmza-form-field__input wpgmaps_via');
			
			$element->querySelector("div.wpgmza-add-waypoint")->addClass("wpgmza-form-field wpgmza-form-field--no-pad wpgmaps_add_waypoint");
			
			$element->querySelector("div.wpgmza-directions-to")->addClass("wpgmza-form-field wpgmaps_to_row");
			
			$element->querySelector("div.wpgmza-directions-to")->addClass('wpgmza-form-field wpgmaps_to_row');
			$element->querySelector(".wpgmza-directions-to > label")->setAttribute('for', "wpgmza_input_to_{$map->id}");
			$element->querySelector(".wpgmza-directions-to > label")->addClass("wpgmza-form-field__label");
			$element->querySelector("input.wpgmza-directions-to")->setAttribute('id', "wpgmza_input_to_{$map->id}");
			if(!empty($map->directions_box_style) && ($map->directions_box_style == "legacy" || $map->directions_box_style == "modern"))
			{
				$element->querySelector("input.wpgmza-directions-from")->setInlineStyle("width", "80%");
				$element->querySelector("input.wpgmza-directions-to")->setInlineStyle("width", "80%");
			}
			
			$element->querySelector(".wpgmza-directions-buttons")->addClass("wpgmza-form-field wpgmaps_to_row");
			$element->querySelector(".wpgmza-directions-buttons input")->addClass("wpgmaps_get_directions");
			$element->querySelector(".wpgmza-directions-buttons input")->setAttribute("id", $map->id);
		}
	}
}
