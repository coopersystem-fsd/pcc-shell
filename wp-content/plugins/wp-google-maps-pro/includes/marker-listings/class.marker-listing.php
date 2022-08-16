<?php

namespace WPGMZA;

class MarkerListing extends AjaxTable
{
	const STYLE_NONE					= 0;
	const STYLE_BASIC_TABLE				= 1;
	const STYLE_BASIC_LIST 				= 4;
	const STYLE_ADVANCED_TABLE			= 2;
	const STYLE_CAROUSEL				= 3;
	const STYLE_MODERN					= 6;
	const STYLE_GRID					= 7;
	
	const ORDER_BY_ID					= 1;
	const ORDER_BY_TITLE				= 2;
	const ORDER_BY_ADDRESS				= 3;
	const ORDER_BY_DESCRIPTION			= 4;
	const ORDER_BY_CATEGORY				= 5;
	const ORDER_BY_CATEGORY_PRIORITY	= 6;
	const ORDER_BY_DISTANCE				= 7;
	const ORDER_BY_RATING				= 8;
	
	const ORDER_ASC						= 1;
	const ORDER_DESC					= 2;
	
	private static $_cachedColumnNames;
	protected $map;
	
	// TODO: Pass the map object rather than ID
	public function __construct($map_id)
	{
		global $wpdb;
		global $wpgmza;
		
		AjaxTable::__construct("{$wpdb->prefix}wpgmza", '/marker-listing/');
		
		$this->map = Map::createInstance($map_id);
		
		// HTML attributes
		$this->element->setAttribute('data-wpgmza-marker-listing', "true");
		
		// Legacy HTML
		if($wpgmza->settings->useLegacyHTML)
		{
			$this->element->addClass('wpgmza_marker_list_class');
			$this->element->setInlineStyle('width', '100%');
		}
		
		// Pagination
		$proDir = plugin_dir_url(dirname(__DIR__));
		wp_enqueue_style('wpgmza_pagination', $proDir . 'lib/pagination.css');
		wp_enqueue_script('wpgmza_pagination', $proDir . 'lib/pagination.min.js');
	}
	
	public function __get($name)
	{
		global $wpgmza;
		
		switch($name)
		{
			case 'hideIcon':
				return !empty($wpgmza->settings->wpgmza_settings_markerlist_icon);
				break;
			
			case 'hideLink':
				return !empty($wpgmza->settings->wpgmza_settings_markerlist_link);
				break;
			
			case 'hideTitle':
				return !empty($wpgmza->settings->wpgmza_settings_markerlist_title);
				break;
			
			case 'hideAddress':
				return !empty($wpgmza->settings->wpgmza_settings_markerlist_address);
				break;
			
			case 'hideCategories':
				// Only applicable for AdvancedTable
				break;
				
			case 'hideDescription':
				return !empty($wpgmza->settings->wpgmza_settings_markerlist_description);
				break;
		}
		
		return AjaxTable::__get($name);
	}
	
	protected function getItemHTMLPath()
	{
		global $wpgmza;
		
		$path = plugin_dir_path(dirname(__DIR__)) . 'html/marker-listings/';
		
		if($wpgmza->settings->useLegacyHTML)
			$path .= 'legacy/';
		
		return $path;
	}
	
	public function getColumns()
	{
		global $wpdb;
		
		if(empty(MarkerListing::$_cachedColumnNames))
		{
			$cols = $wpdb->get_col("SHOW COLUMNS FROM {$this->table_name}");
			MarkerListing::$_cachedColumnNames = array_combine($cols, $cols);
		}
		
		$result = (array)MarkerListing::$_cachedColumnNames;
		
		return $result;
	}
	
	protected function filterColumns(&$columns, $input_params)
	{
		global $wpgmza;
		
		AjaxTable::filterColumns($columns, $input_params);
		
		foreach($columns as $key => $value)
		{
			$name = $this->getColumnNameByIndex($key);
			
			switch($name)
			{					
				case 'icon':
				
					$columns[$key] = \WPGMZA\ProMarker::getIconSQL($this->map->id);
					
					break;
			}
		}
		
		// TODO: See here for 3rd party sort by distance
		if($this->map->order_markers_by == MarkerListing::ORDER_BY_DISTANCE && !empty($input_params['filteringParams']['center']))
		{
			$lat = floatval($input_params['filteringParams']['center']['lat']) / 180 * 3.1415926;
			$lng = floatval($input_params['filteringParams']['center']['lng']) / 180 * 3.1415926;


			/**
			 * Temporary sort by distance patch
			 * 
			 * Some PHP environments will return floatval as 3,141 instead of 3.141
			 * 
			 * This causes issues with sort by distance. We aren't exaclty sure why this happens at the moment, but this
			 * temporary patch should hold while we look into a more robust solution in V9.0
			 *
			 * Confirmed patch on a few live installations which show the issue, meaning it's been field-tested
			 *  
			 * Date added: 2021-07-27
			 * Author: Dylan Auty
			*/
			if(strpos($lat . "", ",") !== FALSE){
				$lat = str_replace(",", ".", $lat . "");
			}

			if(strpos($lng . "", ",") !== FALSE){
				$lng = str_replace(",", ".", $lng . "");
			}
			
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
	
	public function filterOrderBy($orderBy, $keys)
	{
		global $wpgmza;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		
		$column = (empty($this->map->order_markers_by) ? MarkerListing::ORDER_BY_ID : $this->map->order_markers_by);
		
		switch($column)
		{
			case MarkerListing::ORDER_BY_TITLE:
				$orderBy = "title";
				break;
				
			case MarkerListing::ORDER_BY_ADDRESS:
				$orderBy = "address";
				break;
				
			case MarkerListing::ORDER_BY_DESCRIPTION:
				$orderBy = "description";
				break;
				
			case MarkerListing::ORDER_BY_CATEGORY:
				//$orderBy = "category";
				
				return "(
					SELECT category_name FROM $WPGMZA_TABLE_NAME_CATEGORIES
					WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
						SELECT category_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
						WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
					)
					ORDER BY priority
					LIMIT 1
				)";
				
				break;
				
			case MarkerListing::ORDER_BY_CATEGORY_PRIORITY:
				return "(
					SELECT MAX(priority) FROM $WPGMZA_TABLE_NAME_CATEGORIES
					WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
						SELECT category_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
						WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
					)
				)";
				break;
				
			case MarkerListing::ORDER_BY_DISTANCE:
				$params = $wpgmza->restAPI->getRequestParameters();
				
				if(!empty($params['filteringParams']['center']))
					$orderBy = "distance";
				
					break;
				
			case MarkerListing::ORDER_BY_RATING:
			
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
				$orderBy = "id";
				break;
		}
		
		return "$orderBy";
	}
	
	protected function filterOrderClause($clause)
	{
		return "sticky DESC, $clause";
	}
	
	public function filterOrderDirection($input_params)
	{
		$dir = (empty($this->map->order_markers_choice) ? MarkerListing::ORDER_DESC : $this->map->order_markers_choice);
		
		return ($dir == MarkerListing::ORDER_DESC ? 'DESC' : 'ASC');
	}
	
	public function setAjaxParameters($params)
	{
		global $wpgmza;
		
		AjaxTable::setAjaxParameters($params);
		
		$obj = (object)$params;
		
		if($wpgmza->settings->useLegacyHTML && property_exists($obj, 'map_id'))
			$this->element->setAttribute('id', 'wpgmza_marker_list_' . $obj->map_id);
	}
	
	public function getImageDimensions()
	{
		global $wpgmza;
		
		$dimensions = (object)array(
			'width'		=> 100,
			'height'	=> 'auto'
		);
		
		if(!empty($wpgmza->settings->wpgmza_settings_image_width))
			$dimensions->width = $wpgmza->settings->wpgmza_settings_image_width;
		
		if(!empty($wpgmza->settings->wpgmza_settings_image_height))
			$dimensions->height = $wpgmza->settings->wpgmza_settings_image_height;
		
		return $dimensions;
	}
	
	protected function removeHiddenFields($item, $marker)
	{
		global $wpgmza;
		
		// Hide the icon if setting is selected
		if($this->hideIcon && $el = $item->querySelector('.wpgmza_marker_icon'))
			$el->remove();
		
		// Hide the link if selected
		if($link = $item->querySelector('.wpgmza-link, .wpgmza_marker_link'))
		{
			if(empty($marker->link) || $this->hideLink)
				$link->remove();
		}
		
		// Hide title if selected
		if($this->hideTitle && $el = $item->querySelector('.wpgmza_marker_title, .wpgmza_div_title'))
			$el->remove();
		
		// Hide address if selected
		if($this->hideAddress && $el = $item->querySelector('.wpgmza_div_address, .wpgmza-address, .wpgmza_marker_address'))
			$el->remove();
		
		// Hide description if selected
		if($this->hideDescription && $el = $item->querySelector('.wpgmza-desc, .wpgmza_marker_description'))
			$el->remove();

		// Hide custom fields if visiblity is hidden
		$customFields = $item->querySelectorAll('[data-hide-in-marker-listings]');
		
		foreach ($customFields as $field) {
			$field->parentNode->removeChild($field);
		}

		// Hide distance from location
		if(!$this->map->show_distance_from_location && $el = $item->querySelector('.wpgmza-distance-from-location')) {
			$el->remove();
		}
	}
	
	protected function getWhereClause($input_params, &$query_params, $clause_for_total=false)
	{
		global $wpgmza;
		
		$clause = AjaxTable::getWhereClause($input_params, $query_params, $clause_for_total);
		
		if(!(is_admin() || (isset($_SERVER['HTTP_REFERER']) && preg_match('/page=wp-google-maps-menu/', $_SERVER['HTTP_REFERER']) && $wpgmza->isUserAllowedToEdit())))
		{
			$clause .= ' AND approved=%d';
			$query_params[] = 1;
		}
		
		return $clause;
	}
	
	protected function getSQLAfterWhere($input_params, &$query_params)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		$fields = $wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS");
		$markerFilter = new \WPGMZA\MarkerFilter();
		$queryStrings = array();
		
		if(!empty($input_params['map_id']))
		{
			$markerFilter->map 		= \WPGMZA\Map::createInstance($input_params['map_id']);
			$markerFilter->map_id 	= $input_params['map_id'];
		}
		
		if(!empty($input_params['markerIDs']))
			$markerFilter->markerIDs = explode(',', $input_params['markerIDs']);
		
		$integrationQueries = apply_filters('wpgmza_get_integration_queries', array(), $fields, $markerFilter, $input_params);
		
		foreach($integrationQueries as $query)
		{
			$queryStrings[] = "UNION ALL (" . $query->build() . ")";
		}
		
		return implode(" ", $queryStrings);
	}
	
	public function getRecords($params)
	{
		global $wpdb;
		
		$result = Parent::getRecords($params);
		
		// TODO: Use MarkerFilter to populate marker listings, don't use query here. This is a hacky workaround, we should implement ORDER BY on MarkerFilter and use the marker filter to pull data for marker listings.
		foreach($result->data as $record)
		{
			if(!empty($record->icon))
				$record->icon = new MarkerIcon($record->icon);
			
			//$icon = new MarkerIcon($record->icon);
			//$record->icon = $icon->url;
			
			if(preg_match('/^\d+$/', $record->id))
				continue;
			
			preg_match('/\d+/', $record->id, $m);
			
			$qstr = "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_id = " . $m[0];
			$id = $wpdb->get_var($qstr);
			
			if(!$id)
				return;
			
			$record->link = get_permalink($id);
		}
		
		return apply_filters('wpgmza_marker_listing_markers', $result);
	}
	
	protected function appendListingItem($document, $item, $marker)
	{
		global $wpgmza;
		$container = $document->querySelector('body');
		
		$item->setAttribute('data-marker-id', $marker->id);
		$item->setAttribute('data-map-id', $marker->map_id);
		$item->setAttribute('data-latlng', $marker->lat . ", " . $marker->lng);
		$item->setAttribute('data-address', $marker->address);
		
		if($marker->sticky == 1)
			$item->addClass('wpgmza-sticky');
		
		if($wpgmza->settings->useLegacyHTML)
		{
			$even = ($container->childNodes->length % 2 == 1);
			$item->addClass(($even ? 'wpgmaps_even' : 'wpgmaps_odd'));
		}

		if($customFieldsContainer = $item->querySelector(".wpgmza_custom_fields"))
        {
            $customFields = new CustomMarkerFields($marker->id);
            $customFieldsContainer->import($customFields->html());
        }
		
        if(
			($distanceFromLocationContainer = $item->querySelector(".wpgmza-distance-from-location"))
			&& 
			!empty($marker->distance)
			&&
			isset($this->lastInputParams['filteringParams']['center']['source'])
			)
        {
			// Copy this value for processing (unit conversion)
			$distance = $marker->distance;
			
			// Distance units, we grab this from store locator, pending a global setting
			if($this->map->store_locator_distance == 1)
			{
				$units = __('mi', 'wp-google-maps');
				$distance /= \WPGMZA\Distance::KILOMETERS_PER_MILE;
			}
			else
			{
				$units = __('km', 'wp-google-maps');
			}
			
			// Rounding
			if($distance >= 100)
				$amount = round($distance);
			else
				$amount = round($distance, 1);
			
			// From where?
			switch($this->lastInputParams['filteringParams']['center']['source'])
			{
				case 'user':
					$from = __('from your location', 'wp-google-maps');
					break;
				
				case 'searched':
					$from = __('from searched location', 'wp-google-maps');
					break;
				
				default:
					$from = __('from unknown location', 'wp-google-maps');
					break;
			}
			
            $distanceFromLocationContainer->appendText("$amount $units $from");
        }

		if($wpgmza->settings->wpgmza_settings_infowindow_links !== "yes" && $a = $item->querySelector('.wpgmza-link > a') )
		{
			$a->setAttribute('target', '_self');
		}
		
		$this->removeHiddenFields($item, $marker);
		
		$container->appendChild($item);
	}
	
	public static function createInstanceFromStyle($style, $map_id)
	{
		$class = apply_filters('wpgmza_get_marker_listing_class_from_style', $style);
		
		if(!$class)
			return null;
		
		return $class::createInstance($map_id);
	}
}

// TODO: Don't use a closure, allow this filter to be removed
add_filter('wpgmza_get_marker_listing_class_from_style', function($style) {
	
	switch($style)
	{
		case MarkerListing::STYLE_BASIC_TABLE:
			return '\WPGMZA\MarkerListing\BasicTable';
			break;
			
		case MarkerListing::STYLE_BASIC_LIST:
			return '\WPGMZA\MarkerListing\BasicList';
			break;
			
		case MarkerListing::STYLE_ADVANCED_TABLE:
			return '\WPGMZA\MarkerListing\AdvancedTable';
			break;
			
		case MarkerListing::STYLE_CAROUSEL:
			return '\WPGMZA\MarkerListing\Carousel';
			break;
			
		case MarkerListing::STYLE_MODERN:
			return '\WPGMZA\MarkerListing\Modern';
			break;
		case MarkerListing::STYLE_GRID:
			return '\WPGMZA\MarkerListing\Grid';
			break;
	}
	
	return null;
	
}, 1, 1);
