<?php

namespace WPGMZA\MarkerListing;

class AdvancedTable extends \WPGMZA\MarkerDataTable
{
	private $integratedRecords;
	private $isUsingIntegratedMarkers = false;
	
	public function __construct($map_id=null)
	{
		global $wpgmza;
		
		\WPGMZA\MarkerDataTable::__construct();
		
		// TODO: Review this. Is it needed? This may need to be added for broken DOM workaround
		// $this->element->setAttribute('data-wpgmza-marker-listing', "true");
		
		// Temporary workaround for map ID not passed through datatables endpoint
		if($map_id == null && isset($_REQUEST['wpgmzaDataTableRequestData']))
			$map_id = (int)$_REQUEST['wpgmzaDataTableRequestData']['map_id'];
		
		$this->map_id = $map_id;
		$this->map = \WPGMZA\Map::createInstance($map_id);
		
		// Initial sort
		$name = null;
		
		switch($this->map->order_markers_by)
		{
			case \WPGMZA\MarkerListing::ORDER_BY_TITLE:
				$name = "title";
				break;
			
			case \WPGMZA\MarkerListing::ORDER_BY_ADDRESS:
				$name = "address";
				break;
			
			case \WPGMZA\MarkerListing::ORDER_BY_DESCRIPTION:
				$name = "description";
				break;
				
			case \WPGMZA\MarkerListing::ORDER_BY_CATEGORY:
				$name = "category";
				break;
		}
		
		switch($this->map->order_markers_choice)
		{
			case \WPGMZA\MarkerListing::ORDER_DESC:
				$direction = "desc";
				break;
			
			default:
				$direction = "asc";
				break;
		}
		
		$columns	= $this->getColumns();
		$keys		= array_keys($columns);
		$position	= array_search($name, $keys);
			
		if($position !== false)
		{
			$json = json_encode(array(array(
				$position,
				$direction
			)));
			
			$this->element->setAttribute('data-order-json', $json);
		}
		
		// Table classes
		$table = $this->element->querySelector("table");
		$table->addClass('responsive');
		$table->addClass('wpgmza_table');
		
		if($wpgmza->settings->useLegacyHTML)
		{
			$this->element->setAttribute("id", "wpgmza_marker_holder_$map_id");
			$this->element->addClass("wpgmza_marker_holder");
			$this->element->setInlineStyle("width", "100%");
			
			$table->setAttribute("id", "wpgmza_table_$map_id");
			$table->removeClass("display");
			$table->setAttribute("cellspacing", "0");
			$table->setAttribute("cellpadding", "0");
			$table->setInlineStyle("width", "100%");
			
			foreach($table->querySelectorAll("thead>[data-wpgmza-column-name]") as $th)
			{
				$name = $th->getAttribute("data-wpgmza-column-name");
				
				switch($name)
				{
					case "icon":
						$name = "marker";
						break;
					
					case "title":
						$th->addClass("all");
						break;
				}
				
				$th->addClass("wpgmza_table_$name");
			}
			
			if($tfoot = $table->querySelector('tfoot'))
				$tfoot->remove();
			
			//$tbody = $this->element->ownerDocument->createElement('tbody');
			//$table->appendChild($tbody);
		}
	}
	
	protected function getFeatureType()
	{
		return "marker";
	}
	
	protected function getColumns()
	{
		global $wpdb;
		global $wpgmza;
		
		$settings = $wpgmza->settings;
		$result = array();
		
		if(empty($settings->wpgmza_settings_markerlist_icon))
			$result['icon'] = '';
		
		if(empty($settings->wpgmza_settings_markerlist_title))
			$result['title'] = __('Title', 'wp-google-maps');
		
		if(empty($settings->wpgmza_settings_markerlist_category))
			$result['category'] = __('Category', 'wp-google-maps');

		if(empty($settings->wpgmza_settings_markerlist_address))
			$result['address'] = __('Address', 'wp-google-maps');
		
		if(empty($settings->wpgmza_settings_markerlist_description))
			$result['description'] = __('Description', 'wp-google-maps');
		
		if(empty($settings->wpgmza_settings_markerlist_link))
			$result['link'] = __('Link', 'wp-google-maps');
		
		// TODO: It might be more efficient to put this in a class property in the constructor
		$customFields = new \WPGMZA\CustomFields();
		
		foreach($customFields as $field) {
			if ($field->display_in_marker_listings) {
				$result["custom_field_" . $field->id] = __($field->name, 'wp-google-maps');
			}
		}
		
		return $result;
	}
	
	protected function getCustomFieldColumnNames()
	{
		$result = array();
		
		// TODO: It might be more efficient to put this in a class property in the constructor
		$customFields = new \WPGMZA\CustomFields();
		
		foreach($customFields as $field)
			$result[] = "custom_field_" . $field->id;
			
		return $result;
	}
	
	/**
	 * Exclude custom_field_* fields from search clause - they need to be in HAVING, not WHERE
	 * @return array
	 */
	protected function getSearchClause($input_params, &$query_params, $exclude_columns=null)
	{
		if(!$exclude_columns)
			$exclude_columns = array();
		
		// TODO: Removed, this stops search from working with store locator, not quite sure why this was added in the first place.
		//if(isset($input_params['overrideMarkerIDs']) || isset($input_params['markerIDs']))
			//return "";
		
		if(empty($input_params['search']['value']))
			return "";
		
		$exclude_columns = array_merge($exclude_columns, $this->getCustomFieldColumnNames());
		
		$sql = \WPGMZA\MarkerDataTable::getSearchClause($input_params, $query_params, $exclude_columns);
		
		if(empty($sql))
			return $sql;
		
		// TODO: This will be implemented as a trait in the future, when we drop support for PHP 5.3
		$sql = \WPGMZA\ProAdminMarkerDataTable::appendCategoryAndCustomFieldSearchClauses($sql, $input_params, $query_params);
		
		return $sql;
	}
	
	protected function getLinkText()
	{
		global $wpgmza;
		
		$text = __('More Details', 'wp-google-maps');
						
		if(!empty($wpgmza->settings->wpgmza_settings_infowindow_link_text))
			$text = __($wpgmza->settings->wpgmza_settings_infowindow_link_text, 'wp-google-maps');
		
		return $text;
	}
	
	// TODO: This is duplicated from admin marker table. Use a trait when PHP 5.4 is the minimum version
	protected function filterColumns(&$columns, $input_params)
	{
		global $wpgmza;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		foreach($columns as $key => $value)
		{
			$name = $this->getColumnNameByIndex($key);
			
			if(preg_match('/^custom_field_(\d+)$/', $name, $m))
			{
				$field_id = (int)$m[1];
				
				$columns[$key] = "(
					SELECT value 
					FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS
					WHERE field_id=$field_id
					AND object_id=$WPGMZA_TABLE_NAME_MARKERS.id
				) AS $name";
			}
			else
				switch($name)
				{
					case 'icon':
						
						$columns[$key] = \WPGMZA\ProMarker::getIconSQL($this->map_id, true);
					
						break;
					
					case 'category':
						
						$columns[$key] = "(
							SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
							FROM $WPGMZA_TABLE_NAME_CATEGORIES
							WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
								SELECT category_id
								FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
								WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
							)
						) AS category";
							
						break;
					
					case 'link':
					
						$text = $this->getLinkText();
						$text = esc_sql($text);
					
						$columns[$key] = "(
							CASE WHEN LENGTH(link) > 0
							THEN CONCAT('<a target=\"_BLANK\" href=\"', REPLACE(link, '\"', '&quot;'), '\">$text</a>') 
							ELSE ''
							END
						)
						AS link";
					
						break;
				}
		}
		
		$columns[] = 'id';
		
		if($this->map->order_markers_by == \WPGMZA\MarkerListing::ORDER_BY_DISTANCE && !empty($input_params['filteringParams']['center']))
		{
			$lat = floatval($input_params['filteringParams']['center']['lat']) / 180 * 3.1415926;
			$lng = floatval($input_params['filteringParams']['center']['lng']) / 180 * 3.1415926;
			
			$columns['distance'] = "
				(
					6371 *
				
					2 *
				
					ATAN2(
						SQRT(
							POW( SIN( ( ({$wpgmza->spatialFunctionPrefix}X(latlng) / 180 * 3.1415926) - $lat ) / 2 ), 2 ) +
							COS( {$wpgmza->spatialFunctionPrefix}X(latlng) / 180 * 3.1415926 ) * COS( $lat ) *
							POW( SIN( ( ({$wpgmza->spatialFunctionPrefix}Y(latlng) / 180 * 3.1415926) - $lng ) / 2 ), 2 )
						),
						
						SQRT(1 - 
							(
								POW( SIN( ( ({$wpgmza->spatialFunctionPrefix}X(latlng) / 180 * 3.1415926) - $lat ) / 2 ), 2 ) +
								COS( {$wpgmza->spatialFunctionPrefix}X(latlng) / 180 * 3.1415926 ) * COS( $lat ) *
								POW( SIN( ( ({$wpgmza->spatialFunctionPrefix}Y(latlng) / 180 * 3.1415926) - $lng ) / 2 ), 2 )
							)
						)
					)
				) AS distance
			";
		}
		
		return $columns;
	}
	
	protected function getHavingClause($input_params, &$query_params, $exclude_columns=null)
	{
		global $wpdb;
		
		// TODO: Can't filter on custom fields at the moment - we'd need to intersect I believe. Otherwise both WHERE and HAVING are applied with AND logic
		return '';
		
		if(empty($input_params['search']['value']))
			return "";
		
		$columns = array_keys($this->getColumns());
		$customFieldColumnNames = $this->getCustomFieldColumnNames();
		
		$exclude = array_diff($columns, $customFieldColumnNames);
		
		if(empty($exclude))
			return "";
		
		return \WPGMZA\MarkerDataTable::getSearchClause($input_params, $query_params, $exclude);
	}
	
	protected function buildCountQueryString($input_params, &$count_query_params)
	{
		global $wpdb;
		
		$numIntegratedRecords = 0;
		
		$queryStrings 	= array();
		
		$markerFilter 	= new \WPGMZA\MarkerFilter();
		$markerFilter->fields = array('COUNT(*)');
		
		if(!empty($input_params['map_id']))
		{
			$markerFilter->map 		= \WPGMZA\Map::createInstance($input_params['map_id']);
			$markerFilter->map_id 	= $input_params['map_id'];
		}
		
		foreach(apply_filters('wpgmza_get_integration_queries', array(), array('COUNT(*)'), $markerFilter, $input_params) as $query)
		{
			$integrationQueryParams = array();
			
			$having = \WPGMZA\MarkerDataTable::getSearchClause($input_params, $integrationQueryParams);
			
			$stmt = $query->build();
			
			$numIntegratedRecords += $wpdb->get_var($stmt);
		}
		
		$count_where = $this->getWhereClause($input_params, $count_query_params, true);
		
		return "SELECT COUNT(id) + $numIntegratedRecords FROM {$this->table_name} WHERE $count_where";
	}
	
	protected function getSQLAfterWhere($input_params, &$query_params)
	{
		global $wpdb;
		
		$queryStrings 	= array();
		$columns 		= $this->getColumns();
		
		$fields			= array_keys($columns);
		$fields[]		= 'id';
		
		$markerFilter 	= new \WPGMZA\MarkerFilter();
		$markerFilter->fields = array_keys($columns);
		
		if(!empty($input_params['markerIDs']))
			$markerFilter->markerIDs = explode(',', $input_params['markerIDs']);
		
		//if(!empty($where))
			//$where = "AND $where";
		
		if(!empty($input_params['map_id']))
		{
			$markerFilter->map 		= \WPGMZA\Map::createInstance($input_params['map_id']);
			$markerFilter->map_id 	= $input_params['map_id'];
		}
		
		$integrationQueries = apply_filters('wpgmza_get_integration_queries', array(), $fields, $markerFilter, $input_params);
		
		if(!empty($integrationQueries))
			$this->isUsingIntegratedMarkers = true;
		
		foreach($integrationQueries as $query)
		{
			$integrationQueryParams = array();
			
			$having = \WPGMZA\MarkerDataTable::getSearchClause($input_params, $integrationQueryParams);
			
			if(!empty($having))
			{
				$query->having[] = $having;
				
				foreach($integrationQueryParams as $param)
					$query->params[] = $param;
			}
			
			$queryStrings[] = "UNION ALL (" . $query->build() . ")";
		}
		
		return implode(" ", $queryStrings);
	}
	
	protected function getWhereClause($input_params, &$query_params, $clause_for_total=false)
	{
		$clauses = \WPGMZA\MarkerDataTable::getWhereClause($input_params, $query_params, $clause_for_total);
		
		if(!empty($input_params['markerIDs']))
		{
			//$placeholder = "%d";
			
			// Integrated markers have non-numeric ID's, account for that here
			//$integratedRecords = $this->getIntegratedRecords();
			//if(!empty($integratedRecords))
				$placeholder = "%s";
			
			$markerIDs = explode(',', $input_params['markerIDs']);
			$count = count($markerIDs);
			$placeholders = implode(',', array_fill(0, $count, $placeholder));
			
			$clauses .= " AND id IN ($placeholders)";
			
			foreach($markerIDs as $id)
				array_push($query_params, $id);
		}
		else if(isset($input_params['markerIDs']))
			return '0';
		
		return $clauses;
	}
	
	protected function isListingOrderOverridden($input_params)
	{
		$useDefaultOrderBy = true;
			
		if(isset($input_params['overrideListingOrderSettings']))
		{

			switch($input_params['overrideListingOrderSettings']){
				case "false":
					// NB: Some PHP installations will evaluate string "false" as true in the cases below, account for this here by doing nothing.
					break;
				
				case 1:
				case "1":
				case true:
				case "true":
					$useDefaultOrderBy = false;
					break;
			}

			if($useDefaultOrderBy && $input_params['overrideListingOrderSettings'] === true){
				// For some reason compressed paths sometimes require a conditional, and the switch fails, no reason that explais it simple. 
				// This is a temporary patch, this will be reworked in V9.0.0
				$useDefaultOrderBy = false;
			}
		}

		return !$useDefaultOrderBy;
	}
	
	protected function getOrderBy($input_params, $column_keys)
	{
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		global $WPGMZA_TABLE_NAME_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		
		if(!$this->isListingOrderOverridden($input_params))
		{
			// Use marker listing setting on initial draw
			switch($this->map->order_markers_by)
			{
				case \WPGMZA\MarkerListing::ORDER_BY_TITLE:
					return "title";
					break;
					
				case \WPGMZA\MarkerListing::ORDER_BY_ADDRESS:
					return "address";
					break;
					
				case \WPGMZA\MarkerListing::ORDER_BY_DESCRIPTION:
					return "description";
					break;
					
				case \WPGMZA\MarkerListing::ORDER_BY_CATEGORY:
					return "category";
					break;
					
				case \WPGMZA\MarkerListing::ORDER_BY_CATEGORY_PRIORITY:
					return "(
						SELECT MAX(priority) FROM $WPGMZA_TABLE_NAME_CATEGORIES
						WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
							SELECT category_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
							WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
						)
					)";
					break;
					
				case \WPGMZA\MarkerListing::ORDER_BY_DISTANCE:
					if(!empty($input_params['filteringParams']['center']))
						return "distance";
					break;
				
				case \WPGMZA\MarkerListing::ORDER_BY_RATING:
				
					// TODO: Add checks for Gold 5
				
					return "(
						SELECT AVG(amount)
						FROM $WPGMZA_TABLE_NAME_RATINGS
						WHERE $WPGMZA_TABLE_NAME_RATINGS.id IN (
							SELECT $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS.rating_id
							FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS
							WHERE marker_id=$WPGMZA_TABLE_NAME_MARKERS.id
						)
					)";
					break;
					
				default:
					return "id";
					return "$WPGMZA_TABLE_NAME_MARKERS.id";
					break;
			}
		}
		
		return \WPGMZA\MarkerDataTable::getOrderBy($input_params, $column_keys);
	}
	
	protected function getOrderDirection($input_params)
	{
		if(!$this->isListingOrderOverridden($input_params))
		{
			// Use marker listing setting on initial draw
			$dir = (empty($this->map->order_markers_choice) ? \WPGMZA\MarkerListing::ORDER_DESC : $this->map->order_markers_choice);
			
			return ($dir == \WPGMZA\MarkerListing::ORDER_DESC ? 'DESC' : 'ASC');
		}
		
		return \WPGMZA\MarkerDataTable::getOrderDirection($input_params);
	}
	
	protected function filterOrderClause($clause)
	{
		// TODO: Add warning that sticky does not presently work with sticky. We can get around this in the future by selecting sticky explicitly, then removing it for getRecords
		
		if($this->isUsingIntegratedMarkers)
			return $clause;
		
		return "sticky DESC, $clause";
	}
	
	public function getRecords($input_params)
	{
		$records = \WPGMZA\MarkerDataTable::getRecords($input_params);
		
		// NB: The following removes the "distance" column from each row, as datatables is not expecting the extra column server side
		for($index = 0; $index < count($records->data); $index++)
		{
			$last = count($records->data[$index]) - 1;
			
			if($this->map->order_markers_by == \WPGMZA\MarkerListing::ORDER_BY_DISTANCE && !empty($input_params['filteringParams']['center']))
				$last--;
			
			if(!empty($records->meta[$index]) && !empty($records->meta[$index]->id)){
				$records->meta[$index]->id = $records->data[$index][$last];
			}
			
			unset($records->data[$index][$last]);
		}
		
		$columnKeys = array_keys($this->getColumns());
		
		if(($index = array_search('description', $columnKeys)) !== false)
		{
			for($i = 0; $i < count($records->data); $i++)
				$records->data[$i][$index] = do_shortcode($records->data[$i][$index]);
		}
		
		return $records;
	}
}
