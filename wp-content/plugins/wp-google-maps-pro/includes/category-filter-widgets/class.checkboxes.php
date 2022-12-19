<?php

namespace WPGMZA\CategoryFilterWidget;

class Checkboxes extends \WPGMZA\CategoryFilterWidget
{
	public function __construct($map, $options=null)
	{
		\WPGMZA\CategoryFilterWidget::__construct($map, $options);
	}
	
	public function load()
	{
		global $wpgmza;
		
		$this->document->loadHTML('<div></div>');
		$this->div = $this->document->querySelector("div");
		
		if($wpgmza->settings->useLegacyHTML)
		{
			$this->div->addClass("wpgmza_cat_checkbox_holder wpgmza_cat_checkbox_{$this->map->id}");
		}
		
		$this->build($this->map->categoryTree, $this->div);
	}
	
	public function select($category_id)
	{
		$input = $this->document->querySelector("input[value='$category_id']");
		
		if(!$input)
		{
			trigger_error("No category with ID $category_id found", E_USER_WARNING);
			return;
		}
		
		$input->setAttribute("checked", "checked");
	}
	
	protected function build($node, $element)
	{
		global $wpgmza;
		
		if(empty($node->children))
			return;
		
		$ul = $this->document->createElement('ul');
		
		if($wpgmza->settings->useLegacyHTML)
		{
			$ul->addClass('wpgmza_cat_ul wpgmza_cat_checkbox_item_holder');
			
			if(!($node instanceof \WPGMZA\CategoryTree))
				$ul->addClass('wpgmza_cat_ul_child');
		}
		
		foreach($node->children as $child)
		{
			$li		= $this->document->createElement("li");
			$input	= $this->document->createElement("input");
			$label	= $this->document->createElement("label");
			
			$input->setAttribute("type", "checkbox");
			$input->setAttribute("value", $child->id);
			
			$label->import($child->category_name);
			
			if($this->showMarkerCount)
				$label->appendText(" ({$child->marker_count})");
			
			$li->appendChild($input);
			$li->appendChild($label);
			$ul->appendChild($li);
			
			if($wpgmza->settings->useLegacyHTML)
			{
				$li->addClass("wpgmza_cat_checkbox_item_holder wpgmza_cat_checkbox_item_holder_{$child->id}");
				
				$input->addClass("wpgmza_checkbox");
				$input->setAttribute('id', "wpgmza_cat_checkbox_{$child->id}");
				$input->setAttribute('name', 'wpgmza_cat_checkbox');
				$input->setAttribute('mid', $this->map->id);
				
				$label->setAttribute("for", "wpgmza_cat_checkbox_{$child->id}");
			}
			
			$this->build($child, $li);
		}
		
		$element->appendChild($ul);
	}
}