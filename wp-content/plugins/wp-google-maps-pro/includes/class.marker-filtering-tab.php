<?php

namespace WPGMZA;

require_once(plugin_dir_path(__DIR__) . 'includes/custom-fields/class.custom-fields.php');

class MarkerFilteringTab extends DOMDocument
{
	public function __construct($map)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		
		DOMDocument::__construct();

		$this->map = $map;
		
		$this->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/marker-filtering-tab.html.php');
		
		$stmt = $wpdb->prepare("SELECT field_id FROM $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS WHERE map_id=%d", array(
			$map->id
		));
		
		$checked_fields	= $wpdb->get_col($stmt);
		$custom_fields	= new CustomFields();
		$ul				= $this->querySelector('ul');
		
		if(!count($custom_fields))
		{
			$ul->remove();
			return;
		}
		
		if($element = $this->querySelector('#wpgmza-marker-filtering-tab-no-custom-fields-warning'))
			$element->remove();
		
		foreach($custom_fields as $field)
		{
			$checked	= (array_search($field->id, $checked_fields) !== false ? "checked='checked'" : '');
			$disabled	= ($field->widget_type == 'none');
			$title		= __('Toggle filter', 'wp-google-maps');
			
			if($disabled)
			{
				$title		= __('No widget type selected', 'wp-google-maps');
				$checked	= false;
			}
			
			$li		= $this->createElement('li');
			$input	= $this->createElement('input');
			
			$input->setAttribute('type', 'checkbox');
			$input->setAttribute('title', $title);
			$input->setAttribute('name', "enable_filter_custom_field_{$field->id}");
			$input->setAttribute('class', 'wpgmza-enable-custom-field-filter');
			
			if($checked)
				$input->setAttribute('checked', 'checked');
			
			if($disabled)
				$input->setAttribute('readonly', 'readonly');
			
			$li->appendChild($input);
			$li->appendText($field->name);
			
			if($disabled)
			{
				$p = $this->createElement('p');
				$p->addClass('notice notice-warning');
				$p->setInlineStyle('display', 'none');
				$p->appendText(__('You must choose a widget type for this field to enable filtering on it', 'wp-google-maps'));
				
				$li->appendChild($p);
			}
			
			$ul->appendChild($li);
		}
	}
	
	public function onSubmit()
	{
		wpgmza_require_once(WPGMZA_PRO_DIR_PATH . 'includes/custom-fields/class.custom-field-filter.php');
		
		// Enabled filters
		$field_ids = array();
		
		foreach($_POST as $key => $value)
		{
			$m = null;
			
			if(!preg_match('/^enable_filter_custom_field_(\d+)/', $key, $m))
				continue;
			
			$field_ids[] = (int)$m[1];
		}
		
		CustomFieldFilter::setEnabledFilters($this->map->id, $field_ids);
	}
}