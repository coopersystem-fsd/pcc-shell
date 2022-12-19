<?php

namespace WPGMZA\Integration;

abstract class MarkerSource
{
	private static $cachedPostIDsByMetaID;
	
	public function __construct()
	{
		add_filter('wpgmza_get_integration_queries', array($this, 'onGetIntegrationQueries'), 10, 4);
		
		add_filter('wpgmza_import_export_document', array($this, 'onImportExportDocument'));
		add_filter('wpgmza_map_integration_panel', array($this, 'onMapIntegrationPanel'), 10, 2);
		add_action('wpgmza_map_saved', array($this, 'onMapSaved'));
		
		//add_action('wpgmza_get_integrated_markers_for_query_select', array($this, 'onGetIntegratedMarkersForQuerySelect'), 10, 3);
	}
	
	public static function createInstanceFromPOST()
	{
		$class = stripslashes($_POST['import_class']);
		
		if(!wpgmza_user_can_edit_maps() ||
			!isset( $_POST['wpgmaps_security'] ) ||
			!wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ))
		{
			wp_send_json_error( __( 'Security check failed.', 'wp-google-maps' ) );
		}
		
		if(!class_exists($class))
			wp_send_json_error(__('Specified import class does not exist', 'wp-google-maps'));
		
		$instance = new $class();
		
		if(!($instance instanceof MarkerSource))
			wp_send_json_error(__('Class must be an instance of \WPGMZA\Integration\MarkerSource', 'wp-google-maps'));
		
		return $instance;
	}
	
	/*
	 * This function will get a post ID for the given postmeta ID for a post.
	 * This is used to speed the process up rather than hitting the DB in ProMarker's constructor
	 */
	public static function getPostIDFromMetaID($meta_id)
	{
		global $wpdb;
		
		if(!MarkerSource::$cachedPostIDsByMetaID)
			MarkerSource::$cachedPostIDsByMetaID = array();
		
		if(!isset(MarkerSource::$cachedPostIDsByMetaID[$meta_id]))
			return $wpdb->get_var("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_id=" . (int)$meta_id);
		
		return MarkerSource::$cachedPostIDsByMetaID[$meta_id];
	}
	
	/*
	 * Adds an array of post IDs, indexed by postmeta ID
	 */
	public static function addPostIDFromMetaIDToCache($post_ids_by_meta_id)
	{
		if(!MarkerSource::$cachedPostIDsByMetaID)
			MarkerSource::$cachedPostIDsByMetaID = array();
		
		if(!is_array($post_ids_by_meta_id))
			throw new \Exception("Input must be an array");
		
		MarkerSource::$cachedPostIDsByMetaID = array_merge(MarkerSource::$cachedPostIDsByMetaID, $post_ids_by_meta_id);
	}
	
	abstract public function getSettingName();
	abstract public function getQuery($fields=null, $markerFilter=null, $inputParams=null);
	
	abstract public function getCategoryFilteringClauseMarkerIDFieldName();
	
	public function isEnabled($map)
	{
		return !empty($map->{$this->getSettingName()});
	}
	
	protected function getIntegrationControl($document, $name, $type='radio', $class=null, $caption=null)
	{
		if(!class_exists($class))
			throw new \Exception("Specified class does not exist");
		
		if(empty($caption))
			throw new \Exception("Caption cannot be empty");
		
		$label = $document->createElement('label');
		$input = $document->createElement('input');
		
		$input->addClass($name);
		$input->setAttribute('type', $type);
		$input->setAttribute('name', $name);
		$input->setAttribute('value', $class);
		$input->setAttribute('data-wpgmza-integration-class', $class);
		
		$label->appendChild($input);
		$label->appendText(' ' . $caption);
		
		return $label;
	}
	
	public function getIntegratedMarkers()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		$query = $this->getQuery(
			$wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS")
		);
		
		$stmt = $query->build();
		$results = $wpdb->get_results($stmt);

		return $this->setPermalinks($results);
	}
	
	public function onGetIntegrationQueries($input, $fields, $markerFilter, $inputParams)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		if(empty($markerFilter->map) || !$this->isEnabled($markerFilter->map))
			return $input;
		
		if(empty($fields))
			$fields = $wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS");
		
		$query = $this->getQuery($fields, $markerFilter, $inputParams);
		$query->integrationSource = $this;
		
		$input[] = $query;
		
		return $input;
	}
	
	public function importMarkers($map_id, $replace_map_data=false)
	{
		$data = $this->getIntegratedMarkers();

		if($replace_map_data)
		{
			$map = \WPGMZA\Map::createInstance($map_id);
			$markers = $map->getMarkers();
			
			foreach($markers as $marker)
				$marker->trash();
		}
		
		foreach($data as $obj)
		{
			unset($obj->id);
			$obj->map_id = $map_id;
			
			$marker = \WPGMZA\Marker::createInstance($obj);
		}
	}
	
	public function setPermalinks($markers)
	{
		foreach($markers as $key => $data)
		{
			if(!preg_match('/\d+$/', $data->id, $m))
				throw new \Exception('Cannot determine post ID from integrated marker data');
			
			$postId = MarkerSource::$cachedPostIDsByMetaID[$key];
			$markers[$key]->link = get_permalink($postId);
		}
		
		return $markers;
	}
	
	public function onMapIntegrationPanel($document, $map)
	{
		$container = $document->querySelector('#wpgmza-integration-panel');
		$settingName = $this->getSettingName();
		
		$checkbox = $this->getIntegrationControl($document, $settingName, 'checkbox');
		
		if(!empty($map->{$settingName})){
			$checkbox->querySelector('input')->setAttribute('checked', 'checked');
		}

		$container->appendChild($document->createElement('br'));
		$container->appendChild($checkbox);
		
		return $document;
	}
	
	public function onImportExportDocument($document)
	{
		$import_via = $document->querySelector("#import_via");
		
		$import_via->appendChild($document->createElement('br'));
		$import_via->appendChild($this->getIntegrationControl($document, 'import_data_type', 'radio'));
		
		return $document;
	}
	
	public function onImportExportOptions()
	{
		$document = new \WPGMZA\DOMDocument();
		$document->loadPHPFile(WPGMZA_PRO_DIR_PATH . 'html/import-export/integration-import-options-panel.html.php');
		
		$select = new \WPGMZA\MapSelect();
		$document->querySelector('#map-select-container')->import($select->html());
				
		return $document;
	}
	
	public function onMapSaved($map)
	{
		$settingName = $this->getSettingName();
		$enabled = !empty($_POST[$settingName]);
		$map->{$settingName} = $enabled;
	}
}

add_action('wp_ajax_wpgmza_import_integration_options', function() {

	$instance = MarkerSource::createInstanceFromPOST();
	
	wp_send_json_success(array(
		'options_html'	=> $instance->onImportExportOptions()->html
	));
	
	exit;
	
});

add_action('wp_ajax_wpgmza_import_integration', function() {
	
	if(!isset($_POST['map_id']))
	{
		wp_send_json_error('No map ID specified');
		exit;
	}
	
	$instance = MarkerSource::createInstanceFromPOST();
	
	if(is_string($_POST['replace_map_data']))
		$replace_map_data = $_POST['replace_map_data'] == 'true';
	else
		$replace_map_data = !empty($_POST['replace_map_data']);
	
	$instance->importMarkers($_POST['map_id'], $replace_map_data);
	
	wp_send_json_success(array(
		'success' => 1
	));
	
});