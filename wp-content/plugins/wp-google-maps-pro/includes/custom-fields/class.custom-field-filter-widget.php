<?php

namespace WPGMZA;

require_once(plugin_dir_path(__FILE__) . 'class.custom-field-filter.php');

class CustomFieldFilterWidget
{
	protected $filter;
	
	public function __construct($filter)
	{
		$this->filter = $filter;
	}
	
	public function getAttributes()
	{
		// TODO: Add filter field attribtues too
		$result = array(
			'data-wpgmza-filter-widget-class'	=> get_class($this),
			'data-map-id'						=> $this->filter->getMapID(),
			'data-field-id'						=> $this->filter->getFieldID()
		);
		
		$fieldAttributes = $this->filter->getFieldData()->attributes;
		
		if(is_string($fieldAttributes))
			$fieldAttributes = json_decode($fieldAttributes);
		
		foreach($fieldAttributes as $key => $value)
			$result[$key] = $value;
		
		return $result;
	}
	
	public function getAttributesString()
	{
		$attributes = $this->getAttributes();
		$items = array();
		
		foreach($attributes as $name => $value)
			$items[] = $name . '="' . htmlspecialchars($value) . '"';
			
		return implode(' ', $items);
	}
	
	public function html()
	{
		return '';
	}
}

add_filter('wpgmza_get_custom_field_filter_widget', 'WPGMZA\\get_custom_field_filter_widget', 100);

function get_custom_field_filter_widget($filter)
{
	if($filter instanceof CustomFieldFilterWidget)
		return $filter;	// An external filter has already created the widget
	
	$dir = plugin_dir_path(__DIR__);
	
	switch($filter->getFieldData()->widget_type)
	{
		case 'text':
			require_once("{$dir}custom-field-filter-widgets/class.text.php");
			return new CustomFieldFilterWidget\Text($filter);
			break;
			
		case 'dropdown':
			require_once("{$dir}custom-field-filter-widgets/class.dropdown.php");
			return new CustomFieldFilterWidget\Dropdown($filter);
			break;
			
		case 'checkboxes':
			require_once("{$dir}custom-field-filter-widgets/class.checkboxes.php");
			return new CustomFieldFilterWidget\Checkboxes($filter);
			break;

		case 'time':
			require_once("{$dir}custom-field-filter-widgets/class.time.php");
			return new CustomFieldFilterWidget\Time($filter);
			break;

		case 'date':
			require_once("{$dir}custom-field-filter-widgets/class.date.php");
			return new CustomFieldFilterWidget\Date($filter);
			break;
		
		default:
			return new CustomFieldFilterWidget($filter);
	}
}

add_filter('wpgooglemaps_filter_map_div_output', 'WPGMZA\\add_custom_filter_widgets');

function add_custom_filter_widgets($html)
{
	$document = new DOMDocument();
	$document->loadHTML($html);
	$element = $document->querySelector('.wpgmza_map');
	
	if(!$element)
	{
		trigger_error('No map element found to add custom field filters to', E_USER_WARNING);
		return $html;
	}
	
	if(!preg_match('/\d+/', $element->getAttribute('id'), $m))
		return $html;
	
	$map_id = (int)$m[0];
	
	$custom_fields = new CustomFields($map_id);
	
	if(count($custom_fields) == 0)
		return $html;
	
	$widget_html = '<div class="wpgmza-filter-widgets" data-map-id="' . $map_id . '">';
	
	foreach($custom_fields as $field)
	{
		$filter = apply_filters('wpgmza_get_custom_field_filter', $field->id, $map_id);
		
		$widget = apply_filters('wpgmza_get_custom_field_filter_widget', $filter);
		$widget_html .= $widget->html();
	}
	
	$widget_html .= '<button type="button" class="wpgmza-reset-custom-fields">' . __('Reset', 'wp-google-maps') . '</button>';
	
	$widget_html .= '</div>';
	
	return $widget_html . $html;
}
