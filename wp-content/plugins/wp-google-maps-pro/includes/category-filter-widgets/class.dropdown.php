<?php

namespace WPGMZA\CategoryFilterWidget;

class Dropdown extends \WPGMZA\CategoryFilterWidget
{
	public function __construct($map, $options=null)
	{
		\WPGMZA\CategoryFilterWidget::__construct($map, $options);
	}
	
	public function load()
	{
		global $wpgmza;
		
		$this->document->loadHTML("<select></select>");
		$this->select = $this->document->querySelector("select");
		
		$this->build($this->map->categoryTree);
		
		if($wpgmza->settings->useLegacyHTML)
		{
			$this->select->setAttribute("mid", $this->map->id);
			$this->select->setAttribute("id", "wpgmza_filter_select");
			$this->select->setAttribute("name", "wpgmza_filter_select");
		}
	}
	
	public function select($category_id)
	{
		$this->document->querySelector("select")->setValue($category_id);
	}
	
	protected function build($node)
	{
		$option = $this->document->createElement('option');
		$option->setAttribute("value", $node->id);
		
		for($i = $node->getDepth(); $i > 0; $i--)
		{
			$nbsp = $this->document->createEntityReference("nbsp");
			$option->appendChild($nbsp);
		}
		
		$option->import($node->category_name);
		
		if($this->showMarkerCount)
			$option->appendText(" ({$node->marker_count})");
		
		$this->select->appendChild($option);
		
		foreach($node->children as $child)
			$this->build($child);
	}
}