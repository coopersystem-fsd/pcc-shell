<?php

namespace WPGMZA;

class ProMarker extends Marker implements \JsonSerializable
{
	// NB: Properties must be explicitly declared on the class to stop them being written into other_data as arbitrary data
	private $_useRawData = false;
	
	protected $_icon;
	
	protected $categories;
	
	protected $customFields;
	protected $custom_fields;
	protected $rating;
	
	public function __construct($id_or_fields=-1, $read_mode=Crud::SINGLE_READ, $raw_data=false)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		$this->_useRawData = $raw_data;
		
		$this->_icon = new MarkerIcon();
		
		Marker::__construct($id_or_fields, $read_mode);
		
		$this->customFields = new CustomMarkerFields($this->id);
		
		// Legacy support
		$this->custom_fields = $this->customFields;
		
		// TODO: Optimize by not doing this if ratings aren't enabled, also, do it on Gold
		if(class_exists('WPGMZA\\MarkerRating'))
			$this->rating = new MarkerRating($this);
		
		if($read_mode == Crud::SINGLE_READ && is_numeric($id_or_fields)){
			/* 
			 * Modified: 2021-01-20
			 * 
			 * This condition should only be met if the icon column in the DB has a value
			 * Otherwise, this will lead to a category icon being set as the override next time around
			*/
			if(!$this->useRawData && !empty($this->icon)){
				$sql = "SELECT " . ProMarker::getIconSQL($this->map_id) . " FROM $WPGMZA_TABLE_NAME_MARKERS WHERE id=" . (int)$this->id;
				
				$this->_icon			= new MarkerIcon( $wpdb->get_var($sql) );
				$this->_icon->retina	= ($this->retina == 1);
			}
			
			// Categories
			$this->categories = $wpdb->get_col("SELECT category_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES WHERE marker_id=" . (int)$this->id);
		} else {
			$this->categories = $wpdb->get_col("SELECT category_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES WHERE marker_id=" . (int)$this->id);
		}
		
		// Gallery
		if(!empty($this->gallery) && !($this->gallery instanceof MarkerGallery))
			$this->gallery = new MarkerGallery($this->gallery);
		else if(empty($this->gallery) && !empty($this->pic))
		{
			// Backwards compatibliity for thumbnails / pic
			$attachment_id = attachment_url_to_postid($this->pic);
			if($attachment_id)
			{
				$this->gallery = new MarkerGallery(
					array(
						array(
							'attachment_id'	=> $attachment_id,
							'url'			=> $this->pic
						)
					)
				);
			}
		}
		
		if(!empty($this->icon)){
			$this->_icon = new MarkerIcon($this->icon);
			
			if($this->retina == "1")
				$this->_icon->retina = true;

			$this->updateIcon();
		}
		
		// Permalinks for integrated markers
		if($this->isIntegrated){
			$this->getIntegratedPermalink();
			$this->updateIcon();
		}

	}
	
	public static function getIconSQL($map_id=null, $as_html_tag=false)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_MAPS;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		$default = Marker::DEFAULT_ICON;
		
		$map_id = intval($map_id);
		
		$concat_prefix = ($as_html_tag ? "<img src=\"" : '');
		$concat_suffix = ($as_html_tag ? "\">" : '');
		
		$result = "(
			CASE 
			WHEN LENGTH(icon) > 0
			AND icon NOT LIKE '%\"url\":\"\"%'
			THEN 
				CONCAT(
					'" . ($as_html_tag ? "<img class=\"wpgmza-custom-marker-icon\" data-marker-icon-src=\"" : '') . "', 
					" . ($as_html_tag ? "REPLACE(icon, '\"', '&quot;')" : "icon") . ",
					'$concat_suffix'
				)
			WHEN (
				SELECT COUNT(*) FROM $WPGMZA_TABLE_NAME_CATEGORIES
				WHERE LENGTH(category_icon) > 0
				AND $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
					SELECT category_id
					FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
					WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
				)
				LIMIT 1
			) THEN CONCAT(
				'" . ($as_html_tag ? "<img class=\"wpgmza-category-marker-icon\" data-marker-icon-src=\"" : '') . "',
				(
					SELECT " . ($as_html_tag ? "REPLACE(category_icon, '\"', '&quot;')" : "category_icon") . "
					FROM $WPGMZA_TABLE_NAME_CATEGORIES
					WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
						SELECT category_id
						FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
						WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
					)
					ORDER BY priority DESC
					LIMIT 1
				),
				'$concat_suffix'
			)
			WHEN (
				SELECT LENGTH(default_marker) FROM $WPGMZA_TABLE_NAME_MAPS WHERE $WPGMZA_TABLE_NAME_MAPS.id = $map_id
			) > 0
			AND (
				SELECT default_marker FROM $WPGMZA_TABLE_NAME_MAPS WHERE $WPGMZA_TABLE_NAME_MAPS.id = $map_id
			) <> '0'
			THEN
				CONCAT(
					'" . ($as_html_tag ? "<img class=\"wpgmza-map-marker-icon\" data-marker-icon-src=\"" : '') . "',
					(
						SELECT " . ($as_html_tag ? "REPLACE(default_marker, '\"', '&quot;')" : "default_marker") . "
						FROM $WPGMZA_TABLE_NAME_MAPS
						WHERE $WPGMZA_TABLE_NAME_MAPS.id = map_id
					),
					'$concat_suffix'
				)
			ELSE
				CONCAT(
					'" . ($as_html_tag ? "<img class=\"wpgmza-default-marker-icon\" src=\"" : '') . "', 
					'$default',
					'$concat_suffix'
				)
			END
		) AS icon";
		
		return $result;
	}
	
	public function jsonSerialize()
	{
		$json = Marker::jsonSerialize();
		
		$json['categories']		= $this->categories ? $this->categories : array();
		
		if(!$this->useRawData)
			$json['description']	= do_shortcode($this->description);
		
		// Gallery
		if(!empty($this->gallery))
			$json['gallery'] = $this->gallery;
		else
			unset($json['gallery']);

		// Custom fields
		$json['custom_field_data'] = $this->custom_fields;
		$html = $this->custom_fields->html();
		
		// Rating
		if(isset($this->rating))
			$json['rating']		= $this->rating;

		$json['custom_fields_html'] = $html;
		$json['icon'] = $this->_icon;
		
		return $json;
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case "rating":
			case "customFields":
				return $this->{$name};
				break;
				
			case "isIntegrated":
				return preg_match('/[^\d]/', $this->id);
				break;
			
			case "useRawData":
				return $this->_useRawData;
				break;
		}
		
		return Marker::__get($name);
	}
	
	public function set($arg, $val=null)
	{
		Marker::set($arg, $val);
		
		if(is_array($arg) || is_object($arg))
		{
			$obj = (object)$arg;
			
			if(isset($obj->{"category"}))
				$this->__set("category", $obj->{"category"});
			
			if(isset($obj->{"categories"}))
				$this->__set("categories", $obj->{"categories"});
			
			if(isset($obj->{"icon"}))
				$this->__set("icon", $obj->{"icon"});
		}
	}
	
	public function __set($name, $value)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		if(preg_match('/^custom_field_(.+)/', $name, $m))
		{
			$customFieldName = $m[1];
			
			$this->custom_fields->{$m[1]} = $value;
			
			return;
		}
		
		switch($name)
		{
			case "gallery":
				// When the gallery is updated, update the featured pic field / legacy pic field
				if(!($value instanceof MarkerGallery))
					$this->gallery = new MarkerGallery($value);
				else
					$this->gallery = $value;
				
				if($this->gallery->numItems)
					$this->pic = $this->gallery->item(0)->url;
				else
				{
					$this->pic = "";
					$this->__unset('gallery');	// NB: Workaround for unset not working as expected
					return;
				}
				
				break;
			
			case "category":
			case "categories":
				// TODO: Don't allow branch nodes to be set if any of their ancestors are set. We should only be setting the child-most nodes
			
				// Update the category table
				$stmt = $wpdb->prepare("DELETE FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES WHERE marker_id = %d", array($this->id));
				$wpdb->query($stmt);
			
				$categories = array();
				
				if(!empty($value))
				{
					if(is_string($value))
						$categories = explode(',', $value);
					else if(is_array($value))
						$categories = $value;
					else
						throw new \Exception('Invalid category data');
				}
				
				foreach($categories as $category_id)
				{
					if($category_id == "0")
						continue;
					
					$stmt = $wpdb->prepare("INSERT INTO $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
						(marker_id, category_id) 
						VALUES 
						(%d, %d)", array($this->id, $category_id));
						
					$wpdb->query($stmt);
				}
				
				if($name == "categories")
				{
					// NB: Legacy support
					Marker::__set("category", implode(',', $categories));
				}
				
				$this->updateIcon();
				
				return;
				break;
			
			case "icon":
			
				if($value instanceof \WPGMZA\MarkerIcon)
					$this->_icon = $value;
				else if(is_string($value))
					$this->_icon->url = $value;
				
				if($this->_icon->isDefault)
					$value = "";
				else
					$value = json_encode($this->_icon);
				
				$this->updateIcon();
				
				break;
			
			case "retina":
				$this->_icon->retina = ($value == 1);
				
				if($this->_icon->isDefault)
					$value = "";
				if($this->_icon->retina)
					$value = 1;
				else
					$value = 0;
				
				$this->updateIcon();
				
				break;
		}
		
		Marker::__set($name, $value);
	}
	
	public function duplicate()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		$newMarker = Marker::duplicate();
		
		$tables = array(
			$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES,
			$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS
		);
		
		foreach($tables as $table)
		{
			if($table == $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS)
				$field = 'object_id';
			else
				$field = 'marker_id';
			
			$columns = $wpdb->get_col("SHOW COLUMNS FROM `$table`");
			
			$src = $columns;
			$dst = array_merge($columns, array());
			
			foreach($dst as $index => $name)
			{
				if($dst[$index] == $field)
					$dst[$index] = "$newMarker->id AS marker_id";
			}
			
			$src = implode(',', $src);
			$dst = implode(',', $dst);
			
			$qstr = "INSERT INTO $table ($src) SELECT $dst FROM $table WHERE $field = " . (int)$this->id;
			
			$wpdb->query($qstr);
		}
		
		return $newMarker;
	}
	
	public function trash()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		$tables = array(
			$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES,
			$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS
		);
		
		foreach($tables as $table)
		{
			if($table == $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS)
				$field = 'object_id';
			else
				$field = 'marker_id';
			
			$qstr = "DELETE FROM $table WHERE $field = %d";
			$stmt = $wpdb->prepare($qstr, array($this->id));
			
			$wpdb->query($stmt);
		}
		
		Marker::trash();
	}
	
	public function getIntegratedPermalink()
	{
		if(!preg_match('/\d+$/', $this->id, $m))
			throw new \Exception('Cannot determine post ID on integrated marker');
		
		// Get post ID from postmeta ID
		$post_id	= \WPGMZA\Integration\MarkerSource::getPostIDFromMetaID($m[0]);
		$this->link = get_permalink($post_id);
		
		return $this->link;
	}
	
	/*
	 * This will update the marker icon from the database, for example, after changing categories
	 */
	protected function updateIcon()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_MAPS;
		
		$sql = ProMarker::getIconSQL($this->map_id);
		
		$qstr = "SELECT $sql FROM $WPGMZA_TABLE_NAME_MARKERS WHERE id = %d";
		$stmt = $wpdb->prepare($qstr, $this->id);
		
		if($this->isIntegrated){
			$qstr = "SELECT default_marker FROM $WPGMZA_TABLE_NAME_MAPS WHERE id = %d";
			$stmt = $wpdb->prepare($qstr, $this->map_id);

		}
		
		$data = $wpdb->get_var($stmt);
		$this->_icon = new MarkerIcon($data);

	}
}

add_filter('wpgmza_create_WPGMZA\\Marker', function($id_or_fields=-1, $read_mode=Crud::SINGLE_READ, $raw_data=false) {
	
	return new ProMarker($id_or_fields, $read_mode, $raw_data);
	
}, 10, 3);