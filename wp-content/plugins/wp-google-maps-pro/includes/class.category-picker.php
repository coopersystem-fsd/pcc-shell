<?php

namespace WPGMZA;

class CategoryPicker extends DOMDocument
{
	public function __construct($options)
	{
		global $wpgmza;
		
		if(empty($options))
			$options = array();
		
		DOMDocument::__construct();
		
		$this->loadHTML('<div class="wpgmza-category-picker"><input type="hidden" class="wpgmza-category-picker-input"/></div>');
		
		$input = $this->querySelector('input.wpgmza-category-picker-input');
		if(!empty($options['name']))
			$input->setAttribute('name', $options['name']);
		if(!empty($options['ajaxName']))
			$input->setAttribute('data-ajax-name', $options['ajaxName']);
		
		wp_enqueue_style('wpgmza-jstree', WPGMZA_PRO_DIR_URL . 'lib/themes/default/style.min.css', array(), $wpgmza->getProVersion());
		wp_enqueue_script('wpgmza-jstree', WPGMZA_PRO_DIR_URL . 'lib/jstree.min.js', array(), $wpgmza->getProVersion());
		
		$map = null;
		if(isset($options['map_id']))
			$map = Map::createInstance($options['map_id']);
		
		$this->categoryTree = CategoryTree::createInstance($map);
		$jsTreeData = $this->categoryTree->toJsTreeStructure();
		
		$this->querySelector('.wpgmza-category-picker')->setAttribute('data-js-tree-data', json_encode($jsTreeData));
	}
	
}