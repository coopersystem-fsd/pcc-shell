<?php

namespace WPGMZA;

class CustomFieldFilterController
{
	public $params;
	
	public function __construct($params)
	{
		$this->params = $params;
	}
	
	protected function getFilterQueries()
	{
		$params = $this->params;
		
		$map_id = $params['map_id'];
		$queries = array();
		
		foreach($params['widgetData'] as $widgetData)
		{
			if(!is_array($widgetData))
				$widgetData = (array)$widgetData;
			
			$field_id = $widgetData['field_id'];
			$filter = apply_filters('wpgmza_get_custom_field_filter', $field_id, $map_id);
			
			if(!empty($widgetData['value'])) {
				$queries[] = $filter->getFilteringSQL($widgetData['value']);
			}

			// Handle date ranges
			if(!empty($widgetData['type']))
			{
				switch ($widgetData['type'])
				{
					case 'date':
					
						$startDate = $widgetData['value_start'];
						$endDate = $widgetData['value_end'];
						
						if(!empty($startDate) && !empty($endDate))
							$queries[] = $filter->getBetweenFilteringSQL($startDate, $endDate);
						
						break;
						
					case 'time':
					
						$startTime = $widgetData['value_start'];
						$endTime = $widgetData['value_end'];
						
						if(!empty($startTime) && !empty($endTime))
							$queries[] = $filter->getBetweenFilteringSQL($startTime, $endTime);
						
						break;					
						
					default:
						break;
				}
				
			}
		}
		
		return $queries;
	}
	
	/**
	 * This function combines all the widget queries in a manner
	 * that emulates the INTERSECT operator (Not available in MySQL)
	 * @return string The query string
	 */
	protected function getQuery()
	{
		global $wpdb;
		
		$queries = $this->getFilterQueries();
		
		$mapIDClause = "map_id=" . (int)$this->params['map_id'];
		
		if(!empty($this->params['mashup_ids']))
		{
			$ids = array_map('intval', 
				array_merge(array($this->params['map_id']), $this->params['mashup_ids'])
			);
			
			$imploded = implode(',', $ids);
			$mapIDClause = "map_id IN ($imploded)";
		}
		
		if(empty($queries))
			return "SELECT id FROM {$wpdb->prefix}wpgmza WHERE $mapIDClause";
		
		$numQueries = count($queries);
		
		foreach($queries as $key => $qstr)
			$queries[$key] = "($qstr)";
		
		$body = implode(' UNION ALL ', $queries);
		
		$query = "
			SELECT temp.id FROM (
				$body
			) AS temp
			GROUP BY id HAVING COUNT(id) >= $numQueries
		";
		
		return apply_filters('wpgmza_custom_field_filter_controller_query', $query);
	}
	
	public function getFilteredMarkerIDs()
	{
		global $wpdb;
		
		$sql = $this->getQuery();
		$ids = $wpdb->get_col($sql);
		
		return apply_filters('wpgmza_custom_field_filter_controller_filtered_marker_ids', $ids);
	}
}

add_filter('wpgmza_get_custom_field_filter_controlller', 'WPGMZA\\get_custom_field_filter_controller');

function get_custom_field_filter_controller($params)
{
	return new CustomFieldFilterController($params);
}

add_action('wp_ajax_nopriv_wpgmza_custom_field_filter_get_filtered_marker_ids', 'WPGMZA\\custom_field_filter_get_filtered_marker_ids');
add_action('wp_ajax_wpgmza_custom_field_filter_get_filtered_marker_ids', 'WPGMZA\\custom_field_filter_get_filtered_marker_ids');

function custom_field_filter_get_filtered_marker_ids() {
	
	// NB: No security needed, this endpoint does not add any data
	
	$controller = apply_filters('wpgmza_get_custom_field_filter_controlller', $_POST);
	$filtered_marker_ids = $controller->getFilteredMarkerIDs();
	
	$result = (object)array(
		'marker_ids' => $filtered_marker_ids
	);
	
	wp_send_json($result);
	
	exit;
}
