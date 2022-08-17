<?php

namespace WPGMZA;

require_once(plugin_dir_path(__FILE__) . 'class.category-tree-node.php');

abstract class CategoryTree extends CategoryTreeNode
{
	const SOURCE_NATIVE			= "native";
	const SOURCE_WORDPRESS		= "wordpress";
	
	public static $source		= CategoryTree::SOURCE_NATIVE;
	
	protected $map;
	
	public function __construct()
	{
		CategoryTreeNode::__construct();
	}
	
	public static function createInstance($map=null)
	{
		global $wpgmza;
		
		$override = null;
		
		if($override = apply_filters('wpgmza_create_WPGMZA\\CategoryTree', $override))
			return $override;
		
		switch($wpgmza->settings->categoryTreeSource)
		{
			case CategoryTree::SOURCE_WORDPRESS:
				return new CategoryTreeWordPress();
				break;
			
			default:
				return new CategoryTreeNative($map);
				break;
		}
	}
	
	protected function isCircular($target)
	{
		for($node = $target->parent; $node != null; $node = $node->parent)
		{
			if($node->id == $target->id)
				return true;
			
			if($node->parent && $node->parent->id == $node->id)
				return true;
		}
		
		return false;
	}
	
	public function getFilteringOperator()
	{
		global $wpgmza;
		
		switch($wpgmza->settings->wpgmza_settings_cat_logic)
		{
			case "1":
				$operator = "AND";
				break;
				
			default:
				$operator = "OR";
				break;
		}
		
		return $operator;
	}
	
	public function getMarkerIDFieldName($query)
	{
		if(empty($query->integrationSource))
			return "id";
		
		return $query->integrationSource->getCategoryFilteringClauseMarkerIDFieldName();
	}
	
	abstract public function getManyToManyTableName();
	abstract public function getManyToManyMarkerIDFieldName();
	abstract public function getCategoryIDFieldName();
	
	public function applyFilteringClauseToQuery($query, $categories)
	{
		global $wpdb;
		
		if(empty($categories))
			return;
		
		if(is_int($categories))
			$categories = array($categories);
		
		$placeholders	= implode(',', array_fill(0, count($categories), '%d'));
		
		if(empty($categories))
			return;
		
		$categoryIDs	= array();
		
		foreach($categories as $category)
		{
			$categoryIDs[]	= $category;
			$node			= $this->getChildByID($category);
			
			if(!$node)
				continue;
			
			foreach($node->getDescendants() as $descendant)
				$categoryIDs[] = $descendant->id;
		}
		
		$imploded	= implode(',', array_unique($categoryIDs));
		$queries	= array();
		
		$operator						= $this->getFilteringOperator();
		$markerIDFieldName				= $this->getMarkerIDFieldName($query);
		$manyToManyMarkerIDFieldName	= $this->getManyToManyMarkerIDFieldName();
		$categoryIDFieldName			= $this->getCategoryIDFieldName();
		$manyToManyTableName			= $this->getManyToManyTableName();
		
		for($i = 0; $i < count($categoryIDs); $i++)
		{
			$queries[] = "
				$markerIDFieldName IN 
				(
					SELECT $manyToManyMarkerIDFieldName
					FROM $manyToManyTableName
					WHERE $categoryIDFieldName = %d
				)
			";
			
			$query->params[] = $categoryIDs[$i];
		}
		
		$query->where['categories'] = "(" . implode(" $operator ", $queries) . ")";
	}
}
