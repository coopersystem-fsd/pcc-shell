<?php

namespace WPGMZA\CustomFieldFilterWidget;

require_once(plugin_dir_path(__DIR__) . 'custom-fields/class.custom-field-filter-widget.php');

class Text extends \WPGMZA\CustomFieldFilterWidget
{
	public function __construct($filter)
	{
		\WPGMZA\CustomFieldFilterWidget::__construct($filter);
	}
	
	public function html()
	{
		$attributes = $this->getAttributesString();
		
		return "<input 
			$attributes
			placeholder='" . htmlspecialchars($this->filter->getFieldData()->name) . "'
			/>";
	}
}