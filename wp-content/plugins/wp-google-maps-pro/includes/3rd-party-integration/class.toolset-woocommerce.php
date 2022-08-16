<?php

namespace WPGMZA\Integration;

require_once(plugin_dir_path(__FILE__) . 'class.marker-source.php');

class ToolsetWooCommerce extends MarkerSource
{
	public function __construct()
	{
		MarkerSource::__construct();
	}
	
	public function getSettingName()
	{
		return "enable_toolset_woocommerce_integration";
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
		
		$postmeta_table = $wpdb->prefix . 'postmeta';
		
		$query = new \WPGMZA\Query();
		$query->type 	= 'SELECT';
		$query->table 	= "{$wpdb->prefix}posts";
		
		foreach($fields as $name)
		{
			if(preg_match('/^COUNT\([\w*]+\)$/', $name))
			{
				$query->fields[$name] = $name;
				continue;
			}
			
			switch($name)
			{
				case 'id':
					$query->fields['id'] = "CONCAT('toolset_', ID) AS id";
					break;
					
				case 'map_id':
				
					if($markerFilter && !empty($markerFilter->map))
						$query->fields[$name] = $markerFilter->map->id . ' AS map_id';
					else
						$query->fields[$name] = '"" AS map_id';
					
					break;
					
				case 'address':
					$query->fields[$name] = "(
						SELECT meta_value 
						FROM $postmeta_table 
						WHERE post_id=ID AND
						meta_key='wpcf-closest-community'
					) AS address";
					break;
					
				case 'description':
					$query->fields[$name] = "post_excerpt AS description";
					break;
					
				case 'link':
					$query->fields[$name] = "guid AS link";
					break;
					
				case 'lat':
					$query->fields[$name] = "(
						SELECT CAST( meta_value AS DECIMAL(11,8) )
						FROM $postmeta_table
						WHERE post_id=ID AND
						meta_key='wpcf-latitude'
					) AS lat";
					break;
				
				case 'lng':
					$query->fields[$name] = "(
						SELECT CAST( meta_value AS DECIMAL(11,8) )
						FROM $postmeta_table
						WHERE post_id=ID AND
						meta_key='wpcf-longitude'
					) AS lng";
					break;
					
				case 'title':
					$query->fields[$name] = "post_title AS title";
					break;
				
				case 'pic':
					$query->fields[$name] = "(
						SELECT guid FROM {$query->table} AS attachment
						WHERE attachment.ID = (
							SELECT meta_value 
							FROM $postmeta_table
							WHERE post_id={$query->table}.ID
							AND meta_key='_thumbnail_id'
						)
					) AS pic";
					break;
					
				case 'approved':
					$query->fields[$name] = '1 AS approved';
					break;
				
				case 'sticky':
					$query->fields[$name] = '0 AS sticky';
					break;
				
				case 'latlng':
					$query->fields[$name] = "{$wpgmza->spatialFunctionPrefix}PointFromText(
						CONCAT(
							'POINT(', 
							(
								SELECT CAST( meta_value AS DECIMAL(11,8) )
								FROM $postmeta_table
								WHERE post_id=ID AND
								meta_key='wpcf-latitude'
							),
							' ',
							(
								SELECT CAST( meta_value AS DECIMAL(11,8) )
								FROM $postmeta_table
								WHERE post_id=ID AND
								meta_key='wpcf-longitude'
							),
							')'
						)
					) AS latlng";
					break;
					
				default:
					$query->fields[$name] = "'' AS $name";
					break;
			}
		}
		
		if($markerFilter)
		{
			// TODO: Merge markerIDs and overrideMarkerIDs
			if(!empty($markerFilter->markerIDs))
				$query->in("CONCAT('toolset_', ID)", $markerFilter->markerIDs, '%s');
			
			if(isset($inputParams['overrideMarkerIDs']))
				$query->in('CONCAT("toolset_", ID)', explode(',', $inputParams['overrideMarkerIDs']), '%s');
			
			if(!empty($inputParams['filteringParams']['center']) && $markerFilter->map->order_markers_by == \WPGMZA\MarkerListing::ORDER_BY_DISTANCE)
			{
				$lat1 = floatval($inputParams['filteringParams']['center']['lat']) / 180 * 3.1415926;
				$lng1 = floatval($inputParams['filteringParams']['center']['lng']) / 180 * 3.1415926;
				
				$lat2 = "(
					SELECT CAST( meta_value AS DECIMAL(11,8) )
					FROM $postmeta_table
					WHERE post_id=ID AND
					meta_key='wpcf-latitude'
				)";
				$lng2 = "(
					SELECT CAST( meta_value AS DECIMAL(11,8) )
					FROM $postmeta_table
					WHERE post_id=ID AND
					meta_key='wpcf-longitude'
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
		
		$query->where["post_status"] 	= "post_status = 'publish'";
		$query->where["post-type"]		= "post_type = 'product'";
		
		return $query;
	}
	
	public function onGetIntegratedMarkers($input, $markerFilter) {
		if(!empty($markerFilter->map) && !$this->isEnabled($markerFilter->map))
			return $input;
		
		$results = $this->getIntegratedMarkers($markerFilter);
		
		return array_merge($input, $results);
	}
	
	protected function getIntegrationControl($document, $name, $type='radio', $class=null, $label=null) {
		$label = MarkerSource::getIntegrationControl(
			$document,
			$name,
			$type,
			'WPGMZA\Integration\ToolsetWooCommerce',
			__('Toolset', 'wp-google-maps')
		);
		
		if(!defined('TYPES_VERSION')) {

			$a = $document->createElement('a');
			$a->setAttribute('target', '_BLANK');
			$a->setAttribute('href', 'https://toolset.com/buy/');
			$a->appendText(__('Toolset plugin','wp-google-maps'));
			$label->appendText(" (");
			$label->appendChild($a);
			$label->appendText(__(' missing)', 'wp-google-maps'));
			$label->querySelector('input')->setAttribute('disabled', 'disabled');
			$label->querySelector('input')->setAttribute('readonly', 'readonly');

			
		}
		
		return $label;
	}
	
	
	
}