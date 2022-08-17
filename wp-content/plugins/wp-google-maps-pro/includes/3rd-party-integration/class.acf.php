<?php

namespace WPGMZA\Integration;

// See https://www.grzegorowski.com/how-to-get-list-of-all-acf-fields/

require_once(plugin_dir_path(__FILE__) . 'class.marker-source.php');

class ACF extends MarkerSource
{
	private $_fields;
	
	public function __construct()
	{
		MarkerSource::__construct();
		
		add_filter('acf/fields/google_map/api', array($this, 'onACFGoogleMapAPI'));
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case 'fields':
				if(!$this->_fields)
					$this->loadFields();
				
				return array_replace_recursive([], $this->_fields);
				break;
		}
	}
	
	public function isEnabled($map)
	{
		if(empty($map))
			return false;
		
		return !empty($map->enable_advanced_custom_fields_integration);
	}
	
	public function getSettingName()
	{
		return "enable_advanced_custom_fields_integration";
	}
	
	public function onACFGoogleMapAPI($api)
	{
		global $wpgmza;
		
		if(empty($api['key']) && !empty($wpgmza->settings->wpgmza_google_maps_api_key))
			$api['key'] = $wpgmza->settings->wpgmza_google_maps_api_key;
		
		return $api;
	}
	
	private function loadFields()
	{
		global $wpdb;
		
		$this->_fields = array();
		
		$qstr = "SELECT ID, post_excerpt AS name, post_title AS label, post_content
			FROM {$wpdb->prefix}posts
			WHERE post_type = 'acf-field'
			AND post_status = 'publish'";
			
		$results = $wpdb->get_results($qstr);
		
		foreach($results as $data)
		{
			$post_content = unserialize($data->post_content);
			
			unset($data->post_content);
			
			if($post_content['type'] != 'google_map')
				continue;
			
			$this->_fields[$data->name] = $data;
		}
	}
	
	protected function getIntegrationControl($document, $name, $type='radio', $class=null, $label=null)
	{
		$label = MarkerSource::getIntegrationControl(
			$document,
			$name,
			$type,
			'WPGMZA\Integration\ACF',
			__('Advanced Custom Fields', 'wp-google-maps')
		);
		
		if(!class_exists('acf'))
		{
			$a = $document->createElement('a');
			$a->setAttribute('target', '_BLANK');
			$a->setAttribute('href', 'https://wordpress.org/plugins/advanced-custom-fields/');
			$a->appendText(__('ACF plugin','wp-google-maps'));
			$label->appendText(" (");
			$label->appendChild($a);
			$label->appendText(__(' missing)', 'wp-google-maps'));
			$label->querySelector('input')->setAttribute('disabled', 'disabled');
			$label->querySelector('input')->setAttribute('readonly', 'readonly');
			
		}
		
		return $label;
	}
	
	public function onImportExportOptions()
	{
		$locations = $this->getIntegratedMarkers();
		
		$document = MarkerSource::onImportExportOptions();
		
		$p = $document->createElement('p');
		$p->appendText( sprintf(__( '%d marker(s) found.', 'wp-google-maps'), count($locations)) );
		
		$body = $document->querySelector('body');
		$body->insertAfter($p, $document->querySelector('h2'));
		
		return $document;
	}
	
	protected function getExtractSerializedStringSQL($key, $type='s')
	{
		switch($type)
		{
			case "s":
				$offset = strlen($key) + 5;
				
				return 'SUBSTRING(
					meta_value,
					
					# Locate the first quote after the key, value starts one character after that
					LOCATE(
					\'"\', 
					meta_value,
					LOCATE(\'"' . $key . '";s:\', meta_value) + ' . $offset . '
					) + 1,
					
					# Find the length by finding the next quote
					(
						LOCATE(
						\'"\',
						meta_value,
							# Use the first quote as the offset to start looking from
							LOCATE(
							\'"\', 
							meta_value,
							LOCATE(\'"' . $key . '";s:\', meta_value) + ' . $offset . '
							) + 1
						)
					)
					
					-
					
					# Now subtract the position of the first quote to give the length
					(
						LOCATE(
						\'"\', 
						meta_value,
						LOCATE(\'"' . $key . '";s:\', meta_value) + ' . $offset . '
						) + 1
					)
					
				)';
			
			case "d":
				
				$offset = strlen($key) + 4;
				
				return 'SUBSTRING(
					meta_value,
					
					# Locate the colon after the key, value starts one character after that
					LOCATE(
					\':\', 
					meta_value,
					LOCATE(\'"' . $key . '";d:\', meta_value) + ' . $offset . '
					) + 1,
					
					# Find the length by finding the following semicolon
					(
						LOCATE(
						\';\',
						meta_value,
							# Use the first quote as the offset to start looking from
							LOCATE(
							\':\', 
							meta_value,
							LOCATE(\'"' . $key . '";d:\', meta_value) + ' . $offset . '
							) + 1
						)
					)
					
					-
					
					# Now subtract the position of the colon to give the length
					(
						LOCATE(
						\':\', 
						meta_value,
						LOCATE(\'"' . $key . '";d:\', meta_value) + ' . $offset . '
						) + 1
					)
					
				)';
				
				break;
		}
		
		throw new \Exception('Unknown type');
	}
	
	protected function cachePostIDs($postmeta_query)
	{
		global $wpdb;
		
		$query = new \WPGMZA\Query();
		
		$query->type			= 'SELECT';
		$query->fields[]		= 'meta_id';
		$query->fields[]		= 'post_id';
		$query->table			= "{$wpdb->prefix}postmeta";
		
		$query->join["posts"]	= "{$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = post_id";
		
		foreach($postmeta_query->where as $key => $value)
			$query->where[$key] = $value;
		
		$stmt					= $query->build();
		$sql					= $wpdb->prepare($stmt, $postmeta_query->params->toArray());
		$results				= $wpdb->get_results($sql);
			
		$cache					= array();
		
		foreach($results as $obj)
			$cache[$obj->meta_id] = $obj->post_id;
		
		MarkerSource::addPostIDFromMetaIDToCache($cache);
	}
	
	public function getCategoryFilteringClauseMarkerIDFieldName()
	{
		global $wpdb;
		return "{$wpdb->prefix}posts.ID";
	}
	
	public function getQuery($fields=null, $markerFilter=null, $inputParams=null)
	{
		global $wpdb;
		global $wpgmza;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		$query = new \WPGMZA\Query();
		
		$query->type = 'SELECT';
		$query->table = "{$wpdb->prefix}postmeta";
		
		foreach($fields as $field)
		{
			if(preg_match('/^COUNT\([\w*]+\)$/', $field))
			{
				$query->fields[$field] = $field;
				continue;
			}
			
			switch($field)
			{
				case 'id':
					$query->fields[$field] = 'CONCAT("acf_", meta_id) AS id';
					break;
				
				case 'title':
					$query->fields[$field] = "(
						SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = post_id AND post_status = 'publish'
					) AS $field";
					break;
				
				case 'map_id':
					
					if(!empty($markerFilter->map->id))
						$query->fields[$field] = (int)$markerFilter->map->id . " AS map_id";
					else
						$query->fields[$field] = "'' AS $field";
					
					break;
					
				case 'address':
					$query->fields[$field] = $this->getExtractSerializedStringSQL($field) . " AS $field";
					break;
					
				case 'lat':
				case 'lng':
					$query->fields[$field] = "(
						CASE 
						WHEN meta_value LIKE '%\"$field\";d%'
						THEN " . $this->getExtractSerializedStringSQL($field, "d") . "
						ELSE " . $this->getExtractSerializedStringSQL($field, "s") . "
						END
					) AS $field";
					break;
				
				case 'link':
					$query->fields[$field] = 'guid AS link';
					break;
				
				case 'approved':
					$query->fields[$field] = '1 AS approved';
					break;
				
				case 'sticky':
					$query->fields[$field] = '0 AS sticky';
					break;
				
				case 'latlng':
					$query->fields[$field] = "(
						CASE
						WHEN meta_value LIKE '%\"lat\";d%'
						THEN POINT(
							" . $this->getExtractSerializedStringSQL('lat', 'd') . "
							,
							" . $this->getExtractSerializedStringSQL('lng', 'd') . "
						)
						ELSE POINT(
							" . $this->getExtractSerializedStringSQL('lat', 's') . "
							,
							" . $this->getExtractSerializedStringSQL('lng', 's') . "
						)
						END
					) AS $field";
					break;
				
				default:
					$query->fields[$field] = "'' AS $field";
					break;
			}
		}
		
		if($markerFilter)
		{
			if(@$markerFilter->acf_post_id) // NB: Can't use empty() on __get
			{
				$query->where['acf_post_id'] = "{$wpdb->prefix}posts.ID = " . (int)$markerFilter->acf_post_id;
			}
			
			// TODO: Merge markerIDs and overrideMarkerIDs
			if(isset($markerFilter->markerIDs))
				$query->in('CONCAT("acf_", meta_id)', $markerFilter->markerIDs, '%s');
			
			if(isset($inputParams['overrideMarkerIDs']))
			{
				$ids = $inputParams['overrideMarkerIDs'];
				
				if(is_string($ids))
					$ids = explode(',', $ids);
				
				$query->in('CONCAT("acf_", meta_id)', $ids, '%s');
			}
			
			if(!empty($inputParams['filteringParams']['center']) && $markerFilter->map->order_markers_by == \WPGMZA\MarkerListing::ORDER_BY_DISTANCE)
			{
				$lat1 = floatval($inputParams['filteringParams']['center']['lat']) / 180 * 3.1415926;
				$lng1 = floatval($inputParams['filteringParams']['center']['lng']) / 180 * 3.1415926;
				
				$lat2 = "(
					CASE 
					WHEN meta_value LIKE '%\"lat\";d%'
					THEN " . $this->getExtractSerializedStringSQL("lat", "d") . "
					ELSE " . $this->getExtractSerializedStringSQL("lat", "s") . "
					END
				)";
				
				$lng2 = "(
					CASE 
					WHEN meta_value LIKE '%\"lng\";d%'
					THEN " . $this->getExtractSerializedStringSQL("lng", "d") . "
					ELSE " . $this->getExtractSerializedStringSQL("lng", "s") . "
					END
				)";
				
				$query->fields['distance'] = "
					(
						6371 *
					
						2 *
					
						ATAN2(
							SQRT(
								POW( SIN( ( (($lat2) / 180 * 3.1415926) - $lat1 ) / 2 ), 2 ) +
								COS( ($lat2) / 180 * 3.1415926 ) * COS( $lat1 ) *
								POW( SIN( ( (($lng2) / 180 * 3.1415926) - $lng1 ) / 2 ), 2 )
							),
							
							SQRT(1 - 
								(
									POW( SIN( ( (($lat2) / 180 * 3.1415926) - $lat1 ) / 2 ), 2 ) +
									COS( ($lat2) / 180 * 3.1415926 ) * COS( $lat1 ) *
									POW( SIN( ( (($lng2) / 180 * 3.1415926) - $lng1 ) / 2 ), 2 )
								)
							)
						)
					) AS distance
				";
			}
		}
		
		$acfFields = $this->fields;
		$in = array();
		
		foreach($acfFields as $key => $value)
		{
			$in[] = "%s";
			$query->params[] = $value->name;
		}
		
		$query->where["has_lat"]		= "meta_value LIKE '%\"lat\";%'";
		$query->where["has_lng"]		= "meta_value LIKE '%\"lng\";%'";
		
		if(!empty($in))
			$query->where["meta_key"]		= "meta_key IN (" . implode(', ', $in) . ")";
		
		$query->where["meta_value"]		= "LENGTH(meta_value) > 0";
		
		$query->join["posts"] 			= "{$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = post_id";
		$query->where["post_status"] 	= "post_status = 'publish'";
		
		$keys = array_keys($this->fields);
		$query->in('meta_key', $keys, '%s');
		
		// NB: Cache post_id here for ProMarker, this is done here for performance reasons
		$this->cachePostIDs($query);
		
		return $query;
	}
	
}
