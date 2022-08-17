<?php

namespace WPGMZA\CustomFieldFilterWidget;

require_once(plugin_dir_path(__DIR__) . 'custom-fields/class.custom-field-filter-widget.php');

class Dropdown extends \WPGMZA\CustomFieldFilterWidget
{
	public function __construct($filter)
	{
		\WPGMZA\CustomFieldFilterWidget::__construct($filter);
	}
	
	public function html()
	{
		$attributes = $this->getAttributesString();
		
		$html = "<select $attributes>";
		
		$html .= '<option value="" disabled selected style="display: none;">' . 
			htmlspecialchars($this->filter->getFieldData()->name) . 
			'</option>';
		
		foreach($this->filter->getFieldValues() as $value)
		{
			$html .= '<option>' . htmlspecialchars($value) . '</option>';
		}
		
		$html .= '</select>';
		
		return $html;
	}
}
