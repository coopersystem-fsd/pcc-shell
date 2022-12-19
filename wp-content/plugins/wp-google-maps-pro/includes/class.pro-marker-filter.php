<?php

namespace WPGMZA;

$dir = wpgmza_get_basic_dir();

wpgmza_require_once($dir . 'includes/class.factory.php');
wpgmza_require_once($dir . 'includes/class.marker-filter.php');

class ProMarkerFilter extends MarkerFilter
{
	protected $_keywords;
	protected $_categories;
	protected $_map_id;
	protected $_mashupIDs;
	protected $_customFields;
	protected $_includeUnapproved;
	protected $_acf_post_id;
	protected $_bounds;
	
	public function __construct($options=null)
	{
		MarkerFilter::__construct($options);
	}
	
	public function __get($name)
	{
		if(property_exists($this, "_$name"))
			return $this->{"_$name"};
		
		return $this->{$name};
	}
	
	public function __set($name, $value)
	{
		global $wpgmza;
		
		MarkerFilter::__set($name, $value);
		
		switch($name)
		{
			case "map_id":
				$this->loadMap();
				break;
			
			case "mashup_ids":
				$this->_mashupIDs = $value;
				break;
			
			case "includeUnapproved":
				if(!$wpgmza->isUserAllowedToEdit())
					throw new \Exception('Permission denied');
				break;
		}
	}
	
	protected function loadMap()
	{
		$this->map = new Map($this->_map_id);
	}
	
	protected function applyKeywordsClause($query, $context=Query::WHERE)
	{
		global $wpdb;
		
		if(!$this->_keywords)
			return;
		
		$keywords = '%' . $wpdb->esc_like("{$this->_keywords}") . '%';
		
		$query->{$context}['keywords'] = "
			(
				title LIKE %s
				OR
				description LIKE %s
				OR
				address LIKE %s
			)
		";
		
		$query->params[] = $keywords;
		$query->params[] = $keywords;
		$query->params[] = $keywords;
	}
	
	protected function applyMapIDClause($query)
	{
		if(!empty($this->_mashupIDs))
		{
			$ids = array_merge($this->_mashupIDs);
			
			if(!empty($this->_map_id))
				$ids[] = $this->_map_id;
			
			$placeholders = implode(',', array_fill(0, count($ids), "%d"));
			
			$query->where['mashup_ids'] = "map_id IN ($placeholders)";
			
			foreach($ids as $id)
				$query->params[] = $id;
				
			return;
		}
		
		if(!empty($this->_map_id))
		{
			$query->where['map_id'] = 'map_id = %d';
			$query->params[] = $this->_map_id;
		}
	}
	
	protected function applyApprovedClause($query)
	{
		if($this->_includeUnapproved)
			return;
		
		$query->where['approved'] = 'approved = 1';
	}
	
	/*protected function applyKeywordsClause($query)
	{
		global $wpdb;
		global $mapBlockPlugin;
		
		if(!ProMarkerFilter::$cachedSearchableColumnsByTableName)
			ProMarkerFilter::$cachedSearchableColumnsByTableName = array();
		
		$tableName = $this->getTableName();
		
		if(!isset(ProMarkerFilter::$cachedSearchableColumnsByTableName[$tableName]))
		{
			ProMarkerFilter::$cachedSearchableColumnsByTableName[$tableName] = array();
			
			$columns = $wpdb->get_results("SHOW COLUMNS FROM $tableName");
			
			foreach($columns as $col)
			{
				if(preg_match('/varchar|text/i', $col->Type))
					ProMarkerFilter::$cachedSearchableColumnsByTableName[$tableName][] = $col->Field;
			}
		}
		
		$keywords = $this->keywords;
		
		if(empty($keywords))
			return;
		
		$operator = "LIKE";
		
		if($this->_map->keyword_filter_enable_regular_expressions)
		{
			// Only allow the REGEXP operator if keywords is a valid regular expression
			if(preg_match($keywords, null) !== false)
				$operator = "REGEXP";
		}
		
		$columns = ProMarkerFilter::$cachedSearchableColumnsByTableName[$tableName];
		$columns = array_map(function($input) {
			return "`$input` LIKE %s";
		}, $columns);
		
		$like = '%' . $wpdb->esc_like("{$keywords}") . '%';
		
		$query->where['keywords'] = "
			(
				" . implode(' OR ', $columns) . "
			)
		";
		
		for($i = 0; $i < count($columns); $i++)
			$query->params[] = $like;
	}*/
	
	protected function applyCategoriesClause($query)
	{
		$categoryTree = CategoryTree::createInstance();
		$categoryTree->applyFilteringClauseToQuery($query, $this->categories);
	}
	
	protected function applyCustomFieldClause($query)
	{
		if(empty($this->_customFields))
			return;
		
		// TODO: This will not work for mashups
		$controller = apply_filters('wpgmza_get_custom_field_filter_controlller', array(
			'map_id'		=> $this->_map_id,
			'mashup_ids'	=> $this->_mashupIDs,
			'widgetData' 	=> $this->_customFields
		));
		
		$markerIDs = $controller->getFilteredMarkerIDs();
		$imploded = implode(', ', array_map('intval', $markerIDs));
		
		if(empty($markerIDs))
			$query->where['custom_fields'] = '0';
		else
			$query->where['custom_fields'] = "(id IN ($imploded))";
	}
	
	protected function applyBoundsClause($query)
	{
		global $wpgmza;
		
		if(empty($this->_bounds))
			return;
		
		$prefix					= $wpgmza->spatialFunctionPrefix;
		$query->where['bounds']	= "
			{$prefix}Contains(
				{$prefix}GeomFromText('POLYGON((%f %f, %f %f, %f %f, %f %f, %f %f))'),
				latlng
			)
		";
		
		$query->params			[]= $this->_bounds->north;
		$query->params			[]= $this->_bounds->west;
		
		$query->params			[]= $this->_bounds->south;
		$query->params			[]= $this->_bounds->west;
		
		$query->params			[]= $this->_bounds->south;
		$query->params			[]= $this->_bounds->east;
		
		$query->params			[]= $this->_bounds->north;
		$query->params			[]= $this->_bounds->east;
		
		$query->params			[]= $this->_bounds->north;
		$query->params			[]= $this->_bounds->west;
	}
	
	public function getColumns($fields=null)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		$result = MarkerFilter::getColumns($fields);
		
		if(!empty($this->_map_id))
		{
			$icon_sql = ProMarker::getIconSQL($this->map_id);
			
			if($result == array('*'))
			{
				// Can't override icon when * is used, need to explicitly select columns
				$columns 				= $wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS");
				$icon_index 			= array_search('icon', $columns);
				$columns[$icon_index] 	= $icon_sql;
				
				$result = array_values($columns);
			}
			else
			{
				// Only need to replace icon if it's been requested, do nothing otherwise
				$icon_index 			= array_search('icon', $result);
				
				if($icon_index !== false)
					$result[$icon_index] = $icon_sql;
			}
		}
		
		return $result;
	}
	
	public function getFilteredMarkers($fields=null)
	{
		$stripAllExceptID = false;
		
		// TODO: Only do this if integration is on. We need this for non-existant fields to filter
		if($fields == array('id') && !empty($this->map))
		{
			$stripAllExceptID = true;
			$fields = null;
		}
		
		$results = MarkerFilter::getFilteredMarkers($fields);
		
		if($stripAllExceptID)
		{
			$stripped = array();
			
			foreach($results as $result)
			{
				$stripped[] = array(
					'id' => $result->id
				);
			}
			
			$results = $stripped;
		}
		
		return $results;
	}
	
	protected function applyIntegrationQueryClauses($integrationQuery)
	{
		$this->applyRadiusClause($integrationQuery, Query::HAVING);
		$this->applyKeywordsClause($integrationQuery, Query::HAVING);
		$this->applyCategoriesClause($integrationQuery, Query::HAVING);
	}
		
	public function getQuery($fields=null)
	{
		$query = MarkerFilter::getQuery($fields);
		
		$this->applyMapIDClause($query);
		$this->applyApprovedClause($query);
		$this->applyKeywordsClause($query);
		$this->applyCategoriesClause($query);
		$this->applyCustomFieldClause($query);
		$this->applyBoundsClause($query);
		
		if(empty($this->excludeIntegrated))
		{
			foreach(apply_filters('wpgmza_get_integration_queries', array(), null, $this, null) as $integrationQuery)
			{
				$this->applyIntegrationQueryClauses($integrationQuery);
				$query->union[] = $integrationQuery;
			}
		}
		
		return $query;
	}
}

add_filter('wpgmza_create_WPGMZA\\MarkerFilter', function($options) {
	
	return new ProMarkerFilter($options);
	
}, 10, 1);
