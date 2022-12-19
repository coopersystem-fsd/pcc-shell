<?php

namespace WPGMZA;

class CategoryTreeWordPress extends CategoryTree
{
	public function __construct()
	{
		CategoryTree::__construct();
		
		$this->id = 0;
		$this->category_name = __('All Categories', 'wp-google-maps');
		
		$this->build($this);
	}
	
	private function build($parent)
	{
		$args = array(
			"hide_empty"		=> false,
			"orderby"			=> "name",
			"order"				=> "ASC",
			"parent"			=> $parent->id
		);
		
		if($categories = get_categories($args))
		{
			foreach($categories as $category)
			{
				$node = new CategoryTreeNode();
				
				$node->id				= $category->term_id;
				$node->category_name	= $category->name;
				$node->marker_count		= $category->count;
				
				$node->parent			= $parent;
				$parent->children		[]= $node;
				
				$this->build($node);
			}
		}
	}
	
	public function getManyToManyMarkerIDFieldName()
	{
		return "object_id";
	}
	
	public function getCategoryIDFieldName()
	{
		return "term_taxonomy_id";
	}
	
	public function getManyToManyTableName()
	{
		global $wpdb;
		return "{$wpdb->prefix}term_relationships";
	}
}