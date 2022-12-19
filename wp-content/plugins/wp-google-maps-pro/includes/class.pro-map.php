<?php

namespace WPGMZA;

$dir = wpgmza_get_basic_dir();

wpgmza_require_once($dir . 'includes/class.factory.php');
wpgmza_require_once($dir . 'includes/class.crud.php');
wpgmza_require_once($dir . 'includes/class.map.php');

class ProMap extends Map
{
	protected $_proSettingsMigrator;
	protected $_directionsBox;
	protected $_storeLocator;
	protected $_categoryTree;
	protected $_categoryFilterWidget;
	
	public function __construct($id_or_fields=-1, $overrides=null)
	{
		global $wpgmza;
		
		Map::__construct($id_or_fields, $overrides);
		
		if(is_null($this->element)){
			// Return as the Map core contructor failed to find the map
			return;
		}

		$this->element->setAttribute("data-map-id", $this->id);
		
		$this->_proSettingsMigrator = new ProSettingsMigrator();
		$this->_proSettingsMigrator->migrateMapSettings($this);
		
		$base = plugin_dir_url( wpgmza_get_basic_dir() . 'wp-google-maps.php' );
		

		if(empty($wpgmza->settings['wpgmza_do_not_enqueue_owl_carousel'])){
			wp_enqueue_script('owl-carousel', 						$base . 'lib/owl.carousel.js', array('jquery'), $wpgmza->getProVersion());
			wp_enqueue_style('owl-carousel_style',					$base . 'lib/owl.carousel.min.css', array(), $wpgmza->getProVersion());
			// wp_enqueue_style('owl-carousel_style_theme',			$base . 'lib/owl.theme.css', array(), $wpgmza->getProVersion());
			wp_enqueue_style('owl-carousel_style__default_theme',	$base . 'lib/owl.theme.default.min.css', array(), $wpgmza->getProVersion());
		}

		// Port the carousel style */
		if(!empty($wpgmza->settings['wpgmza_settings_carousel_markerlist_theme']) && empty($wpgmza->settings['wpgmza_do_not_enqueue_owl_carousel_themes'])){
			switch ($wpgmza->settings['wpgmza_settings_carousel_markerlist_theme']) {
				case 'sun':
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_sun.css', array(),  $wpgmza->getProVersion());
					break;
				case 'earth':
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_earth.css', array(),  $wpgmza->getProVersion());
					break;
				case 'monotone':
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_monotone.css', array(),  $wpgmza->getProVersion());
					break;
				case 'pinkpurple':
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_pinkpurple.css', array(),  $wpgmza->getProVersion());
					break;
				case 'white':
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_white.css', array(),  $wpgmza->getProVersion());
					break;
				case 'black':
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_black.css', array(),  $wpgmza->getProVersion());
					break;				
				case 'sky':
				default:
					wp_enqueue_style('owl_carousel_style_theme_select', WPGMZA_PRO_DIR_URL .'css/carousel_sky.css', array(),  $wpgmza->getProVersion());
					break;
			}
		}
		
		$base = plugin_dir_url(__DIR__);
		
		wp_enqueue_script('featherlight',				$base . 'lib/featherlight.min.js', array('jquery'), $wpgmza->getProVersion());
		wp_enqueue_style('featherlight',				$base . 'lib/featherlight.min.css', array(), $wpgmza->getProVersion());

		wp_enqueue_style('wpgmaps_datatables_responsive-style',	$base . 'lib/dataTables.responsive.css', array(), $wpgmza->getProVersion());

		
		// wp_enqueue_script('polylabel',					$base . 'lib/polylabel.js', array(), $wpgmza->getProVersion());
		wp_enqueue_script('polyline',					$base . 'lib/polyline.js', array(), $wpgmza->getProVersion());
		
		if($this->isDirectionsEnabled()){
			$this->_directionsBox = new DirectionsBox($this);
		}


		$this->checkLegacySettings();
		
		if(is_admin() && !empty($this->fusion))
		{
			add_action('admin_notices', function() {
				
				?>
				
				<div class="notice notice-error is-dismissible">
					<p>
						<?php
						_e('<strong>WP Google Maps:</strong> Fusion Tables are deprecated and will be turned off as of December the 3rd, 2019. Google Maps will no longer support Fusion Tables from this date forward.', 'wp-google-maps');
						?>
					</p>
				</div>
				
				<?php
				
			});
		}
		
		$this->_categoryTree = CategoryTree::createInstance($this);
		$this->_categoryFilterWidget = CategoryFilterWidget::createInstance($this);
		
		$this->onInit();
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case "directionsBox":
			case "storeLocator":
			case "categoryTree":
			case "categoryFilterWidget":
				return $this->{"_$name"};
				break;
				
			case "mashupIDs":
				
				if(empty($this->shortcodeAttributes['mashup_ids']))
					return array();
				
				$ids = explode(",", $this->shortcodeAttributes['mashup_ids']);
				
				return array_map('intval', $ids);
			
				break;
		}
		
		return Map::__get($name);
	}
	
	public function isStoreLocatorEnabled()
	{
		return $this->store_locator_enabled == "1";
	}
	
	public function isDirectionsEnabled()
	{
		global $wpgmza;
		
		if($this->directions_enabled == "1")
			return true;
		
		if(!empty($this->overrides['enable_directions']))
			return true;
		
		return false;
	}

	public function checkLegacySettings(){
		/*
		 * Interim patch for game-breaking settings that did not migrate properly
		 *
		 * There are better ways to achieve this, this is purely to reduce frustration
		 *
		 * Added: 2021-01-22
		*/
		$legacyCheckboxes = array('fit_maps_bounds_to_markers', 'fit_maps_bounds_to_markers_after_filtering', 'hide_point_of_interest');
		foreach ($legacyCheckboxes as $propKey) {
			if(!empty($this->{$propKey}) && intval($this->{$propKey}) === 2){
				$this->{$propKey} = false;
			}
		}
	}

	protected function getMarkersQuery()
	{
		global $wpdb, $WPGMZA_TABLE_NAME_MARKERS;
		
		$columns = array();
		
		foreach($wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS") as $name)
		{
			switch($name)
			{
				case "icon":
					$columns[] = ProMarker::getIconSQL($this->id);
					break;
				
				default:
					$columns[] = $name;
					break;
			}
		}
		
		$stmt = $wpdb->prepare("SELECT " . implode(", ", $columns) . " FROM $WPGMZA_TABLE_NAME_MARKERS WHERE approved=1 AND map_id=%d", array($this->id));
		
		return $stmt;
	}
	
	public function create()
	{
		Parent::create();
		
		// NB: Legacy workaround, the map has a lot of columns in the database with incorrect defaults. This code will set those defaults so that the HTML controls aren't initialised with blank zero values from the DB overriding the defaults in the HTML
		
		$files			= array(
			plugin_dir_path(WPGMZA_FILE) . 'html/map-edit-page/map-edit-page.html.php',
			plugin_dir_path(WPGMZA_PRO_FILE) . 'html/directions-box-settings.html.php'
		);
		
		$columns		= $this->get_column_names();
		$fields			= array();
		
		foreach($files as $filename)
		{
		
			$document		= new DOMDocument();
			$document->loadPHPFile($filename);
			
			foreach($columns as $name)
			{
				$element	= $document->querySelector("[name='$name']");
				
				if(!$element)
					continue;
				
				$value		= $element->getValue();
				
				if(empty($value))
					continue;
				
				// Get the default value from the element to insert into the database
				$fields[$name] = $value;
			}
			
		}
		
		$this->set($fields);
	}
	
	public function trash()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_POLYGONS;
		global $WPGMZA_TABLE_NAME_POLYLINES;
		global $WPGMZA_TABLE_NAME_HEATMAPS;
		global $WPGMZA_TABLE_NAME_CIRCLES;
		global $WPGMZA_TABLE_NAME_RECTANGLES;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
		
		$types = array(
			$WPGMZA_TABLE_NAME_MARKERS							=> 'WPGMZA\\Marker',
			$WPGMZA_TABLE_NAME_POLYGONS							=> null,
			$WPGMZA_TABLE_NAME_POLYLINES						=> null,
			$WPGMZA_TABLE_NAME_HEATMAPS							=> null,
			$WPGMZA_TABLE_NAME_CIRCLES							=> null,
			$WPGMZA_TABLE_NAME_RECTANGLES						=> null,
			$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS	=> null,	
			$WPGMZA_TABLE_NAME_CATEGORY_MAPS					=> null
		);
		
		foreach($types as $table => $class)
		{
			if($class && class_exists($class))
			{
				$stmt = $wpdb->prepare("SELECT id FROM $table WHERE map_id=%d", $this->id);
				$ids = $wpdb->get_col($stmt);
				
				foreach($ids as $id)
				{
					$instance = $class::createInstance($id);
					$instance->trash();
				}
			}
			else
			{
				$stmt = $wpdb->prepare("DELETE FROM $table WHERE map_id=%d", array($this->id));
				$wpdb->query($stmt);
			}
		}
		
		Map::trash();
	}
	
	public function duplicate()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_POLYGONS;
		global $WPGMZA_TABLE_NAME_POLYLINES;
		global $WPGMZA_TABLE_NAME_HEATMAPS;
		global $WPGMZA_TABLE_NAME_CIRCLES;
		global $WPGMZA_TABLE_NAME_RECTANGLES;
		global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
		
		$newMap = Map::duplicate();
		
		// TODO: 8.1.0 - Make this function dynamically detect feature types and iterate over them
		$types = array(
			$WPGMZA_TABLE_NAME_MARKERS							=> 'WPGMZA\\Marker',
			$WPGMZA_TABLE_NAME_POLYGONS							=> null,
			$WPGMZA_TABLE_NAME_POLYLINES						=> null,
			$WPGMZA_TABLE_NAME_HEATMAPS							=> null,
			$WPGMZA_TABLE_NAME_CIRCLES							=> null,
			$WPGMZA_TABLE_NAME_RECTANGLES						=> null,
			$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS	=> null,	
			$WPGMZA_TABLE_NAME_CATEGORY_MAPS					=> null
		);
		
		foreach($types as $table => $class)
		{
			if($class && class_exists($class))
			{
				$stmt = $wpdb->prepare("SELECT id FROM $table WHERE map_id=%d", $this->id);
				$ids = $wpdb->get_col($stmt);
				
				foreach($ids as $id)
				{
					$instance = $class::createInstance($id);
					
					$newFeature = $instance->duplicate();
					$newFeature->map_id = $newMap->id;
				}
			}
			else
			{
				$stmt = $wpdb->prepare("SELECT * FROM $table WHERE map_id=%d", array($this->id));
				$data = $wpdb->get_results($stmt);
				
				foreach($data as $obj)
				{
					$arr = (array)$obj;
					
					$src_id = $arr['id'];
					unset($arr['id']);
					
					$columns = array_keys($arr);
					$imploded = implode(',', $columns);
					
					$qstr = "INSERT INTO $table ($imploded) SELECT $imploded FROM $table WHERE id = %d";
					$stmt = $wpdb->prepare($qstr, array($src_id));
					$wpdb->query($stmt);
					
					$qstr = "UPDATE $table SET map_id = %d WHERE id = %d";
					$stmt = $wpdb->prepare($qstr, array($newMap->id, $wpdb->insert_id));
					$wpdb->query($stmt);
				}
			}
		}
		
		return $newMap;
	}
}

add_filter('wpgmza_create_WPGMZA\\Map', function($id_or_fields, $overrides=null) {
	
	return new ProMap($id_or_fields, $overrides);
	
}, 10, 2);
