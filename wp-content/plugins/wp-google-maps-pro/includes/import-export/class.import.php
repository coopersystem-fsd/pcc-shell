<?php
/**
 * WP Google Maps Pro Import / Export API: Import abstract class
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;

/**
 * Importer for WP Google Maps Pro
 *
 * This handles importing of files.
 *
 * @since 7.0.0
 */
abstract class Import { 
	/**
	 * Absolute path to file.
	 *
	 * @var string $file Absolute path to file.
	 */
	protected $file = '';

	/**
	 * URL to file.
	 *
	 * @var string $file URL to file.
	 */
	protected $file_url = '';

	/**
	 * Decoded file data.
	 *
	 * @var mixed $file_data Decoded file data.
	 */
	protected $file_data = null;

	/**
	 * Import options.
	 *
	 * @var array $options Import options.
	 */
	protected $options = array();

	/**
	 * Database insertion defaults.
	 *
	 * @var array $db_defaults Array of object types with key value pairs of default data.
	 */
	protected $db_defaults = array();
	
	protected $logEntries = array();
	protected $loggedResponse;	// For URL imports
	
	private $recorded_start_time;
	
	/**
	 * Import constructor.
	 *
	 * @throws \Exception If unable to load file.
	 *
	 * @param string $file     Optional. Absolute path to file.
	 * @param string $file_url Optional. URL to file.
	 * @param array  $options  Optional. Import options.
	 */
	public function __construct( $file = '', $file_url = '', $options = array() )
	{
		$this->log("Base class constructor called");

		$this->options = $options;
		if ( file_exists( $file ) ) {

			$this->file = $file;

		}

		if ( strpos( $file_url, 'http' ) === 0 ) {

			$this->file_url = $file_url;

		}

		$this->options = $options;
		$this->check_options();
		
		$this->attempt_set_time_limit();

	}

	public function prepare(){

		$this->automaticBackup();
		$this->record_start_time();
		
		$this->load_file();
		$this->parse_file();
		
		if ( empty( $this->file_data ) && !($this instanceof ImportIntegration) ) {
			throw new \Exception( __( 'Error: Unable to load file.', 'wp-google-maps' ) );
		}
	}

	public function automaticBackup(){
		global $wpgmza;

		if(empty($wpgmza->settings->disable_automatic_backups)){
			$backup = new Backup();
			$backup->createBackup(false, Backup::FLAG_TYPE_PRE_IMPORT);
		}
	}

	/**
	 * Sanitize options.
	 */
	abstract protected function check_options();

	/**
	 * Check ids.
	 *
	 * @throws \Exception On bad id.
	 *
	 * @param array $ids Integer array of ids.
	 * @return array Integer array of ids.
	 */
	protected function check_ids( $ids ) {

		$id_count = count( $ids );

		for ( $i = 0; $i < $id_count; $i++ ) {

			if ( ! is_numeric( $ids[ $i ] ) ) {

				throw new \Exception( __( 'Error: Malformed options. Bad id.', 'wp-google-maps' ) );

			}

			$ids[ $i ] = absint( $ids[ $i ] );

			if ( $ids[ $i ] < 1 ) {

				throw new \Exception( __( 'Error: Malformed options. Bad id.', 'wp-google-maps' ) );

			}
		}

		return $ids;

	}

	/**
	 * Load file data from file.
	 */
	protected function load_file() {
		
		if ( ! empty( $this->file ) ) {
			
			$this->log("Loading file {$this->file}");

			$file_contents = file_get_contents( $this->file );
			
			if ( ! empty( $file_contents ) ) {

				$this->file_data = $file_contents;

			}
			else
				$this->log("File is empty");
			
		}

		if ( empty( $file_contents ) && ! empty( $this->file_url ) ) {
			
			$this->log("Loading URL {$this->file_url}");

			$file_contents = wp_remote_get( $this->file_url );
			
			if ( ! is_wp_error( $file_contents ) ) {
				
				$body = $response = $file_contents;
				
				if(is_array($response))
					$body = $response['body'];
				
				$this->log("Received " . strlen($body) . " characters");
				$this->loggedResponse = $body;
				
				$this->file_data = wp_remote_retrieve_body( $file_contents );

			}
			else
			{
				$this->log("Failed to retrieve data from URL: " . $file_contents->get_error_message());
			}
			
		}
		
	}
	
	protected function attempt_set_time_limit()
	{
		$desired_time_limit = 60 * 15;
		
		if(function_exists('set_time_limit'))
			set_time_limit($desired_time_limit);
		
		if(function_exists('get_time_limit'))
			ini_set('max_execution_time', $desired_time_limit);
	}
	
	protected function record_start_time()
	{
		$this->recorded_start_time = time();
	}
	
	protected function get_time_limit()
	{
		if(function_exists('ini_get'))
			return (int)ini_get('max_execution_time');
		
		return 30; // PHP default
	}
	
	protected function get_remaining_time()
	{
		$now = time();
		$elapsed = (int)$now - (int)$this->recorded_start_time;
		$limit = $this->get_time_limit();
		
		return $limit - $elapsed;
	}
	
	protected function bail_if_near_time_limit()
	{
		$remaining = $this->get_remaining_time();
		$threshold = 5;
		
		if($remaining < $threshold)
			throw new \Exception(__('Time limit threshold reached. Please speak to your host to increase your PHP execution time limit, or break your data into smaller parts', 'wp-google-maps'));
	}

	/**
	 * Parse file data.
	 */
	abstract protected function parse_file();

	/**
	 * Output admin import options.
	 *
	 * @return string Options html.
	 */
	abstract public function admin_options();

	/**
	 * Import the file.
	 */
	abstract public function import();
	
	public function onImportComplete()
	{
		global $wpdb;
		global $wpgmza_tblname_maps;
		
		// NB: Check encoding here. mb_detect_encoding doesn't seem reliable
		
		// TODO: Use global settings module
		$settings = get_option('WPGMZA_OTHER_SETTINGS');
		
		if(!empty($settings['wpgmza_settings_marker_pull']) && $settings['wpgmza_settings_marker_pull'] == '1') // TODO: Replace with constant
		{
			$map_ids = $wpdb->get_col("SELECT id FROM $wpgmza_tblname_maps");
			
			foreach($map_ids as $map_id)
				wpgmaps_update_xml_file($map_id);
		}
		
		do_action('wpgmza_import_complete');
		
		$this->log("Import completed");
	}

	/**
	 * Sets the import progress for this session.
	 *
	 * @param float $value A number between 0 and 1 representing the progress.
	 */
	public function set_progress( $value ) {
		
		if(!isset($_POST['wpgmaps_security']))
			return;

		@session_start();

		$_SESSION['wpgmza_import_progress_' . $_POST['wpgmaps_security']] = $value;

		session_write_close();

	}

	/**
	 * Returns HTML for the admin notices.
	 *
	 * @return string
	 */
	public function get_admin_notices() {

		return '';

	}
	
	protected function log($str)
	{
		$this->logEntries[] = date('Y-m-d H:i:s :- ') . htmlentities($str);
		
		file_put_contents(plugin_dir_path(__FILE__) . 'import.log', date('Y-m-d H:i:s :- ') . $str . "\r\n", FILE_APPEND);
	}
	
	public function getLogText()
	{
		return implode('<br/>', $this->logEntries);
	}
	
	public function getLoggedResponse()
	{
		return $this->loggedResponse;
	}

	/**
	 * Geocode.
	 *
	 * @param string $location Either an address or latitude, longitude coordinates.
	 * @param string $type     Optional. 'address' to geocode, 'latlng' to reverse geocode. Default 'address'.
	 * @return string|array|bool Address or array of latitude and longitude, false on failure or no results.
	 */
	protected function geocode( $location, $type = 'address' )
	{
		global $wpgmza;

		$this->log("Attempting to geocode $location ($type)");
		
		if(empty($wpgmza->settings->wpgmza_google_maps_api_key))
			throw new \Exception("Geocode failed, no Google API key present");
		
		$api_key = $wpgmza->settings->wpgmza_google_maps_api_key;
		
		if(!empty($wpgmza->settings->importer_google_maps_api_key))
			$api_key = $wpgmza->settings->importer_google_maps_api_key;

		if(empty($api_key))
		{
			$this->log("Geocode failed (No API key)");
			return false;
		}
			
		if(empty($location))
		{
			$this->log("Geocode failed (Location empty)");
			return false;
		}
		
		if('address' != $type && 'latlng' != $type)
		{
			$this->log("Geocode failed (Invalid type)");
			return false;
		}
		
		$args = array();
		$options = array();
		
		if(CloudAPI::isCloudKey($api_key))
		{
			$endpoint = CloudAPI::URL . '/geocode';	
			
			$options['headers'] = array(
				'X-WPGMZA-CLOUD-API-KEY' => $api_key
			);
			
			if($type == 'latlng')
				$args['location'] = rawurldecode($location);
			else
				$args['address'] = rawurldecode($location);
		}
		else
		{
			$endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';
			
			$args['key'] = $api_key;
			$args[$type] = rawurldecode($location);
		}

		$this->log("Request Token: " . $api_key);
		
		$url = add_query_arg($args, $endpoint);
		
		$start_time = microtime( true );

		$response = wp_remote_get( $url, $options );

		if(is_wp_error($response))
		{
			$msg = $response->get_error_message();
			
			if(array_key_exists('SERVER_ADDR', $_SERVER))
				$ip = $_SERVER['SERVER_ADDR'];
			else if(array_key_exists('LOCAL_ADDR', $_SERVER))
				$ip = $_SERVER['LOCAL_ADDR'];
			else if(array_key_exists('SERVER_NAME', $_SERVER))
				$ip = gethostbyname($_SERVER['SERVER_NAME']);
			else
				$ip = 'unknown';
			
			if(preg_match('/refer(r?)er/i', $msg))
				$msg = sprintf(
					__("HTTP referrer restrictions on your API key forbid geocoding from this server. This can happen when your server is behind a proxy, or does not set the HTTP referrer header correctly. We recommend temporarily de-restricting your key, or generating a second key with an IP restriction to switch to temporarily. We detected this servers IP as %s.", 'wp-google-maps'),
					$ip
				);
			
			$this->log("Geocode failed ($msg)");
			return false;
		}

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response );
		
		if(isset($response->status))
		{
			switch($response->status)
			{
				case "OK":
					break;
					
				case "OVER_DAILY_LIMIT":
					$this->log("Over daily query limit");
					throw new \Exception("Over daily query limit");
					return false;
					break;
				
				case "OVER_QUERY_LIMIT":
					$this->log("Over query limit");
					throw new \Exception("Over query limit");
					return false;
					break;
					
				case "REQUEST_DENIED":
					$logMessage = "Request denied";
					if(!empty($response->error_message)){
						$logMessage .= " - " . $response->error_message;
					} else {
						$logMessage .= " - IP Restricted key may be required in the 'Alternative Import API Key' field found within the global settings";
					}

					$this->log($logMessage);
					throw new \Exception($logMessage);
					return false;
					break;
					
				case "INVALID_REQUEST":
					$this->log("Invalid request");
					return false;
					break;
					
				case "ZERO_RESULTS":
					$this->log("No results found");
					return false;
					break;
				
				default:
					$this->log("Unknown geocode response status");
					return false;
					break;
			}
		}
		
		$this->geocode_response = $response;

		// NB: For cloud API responses
		if(is_array($response) && !isset($response->results))
			$response = (object)array(
				'results' => $response
			);

		$result = false;
	
		switch ( $type ) {

			case 'address':

				if ( isset( $response->results[0]->geometry->location->lat, $response->results[0]->geometry->location->lng ) ) {

					$result = array( 
						$response->results[0]->geometry->location->lat, 
						$response->results[0]->geometry->location->lng
					);

					$this->log("Geocode successful (" . $result[0] . ", " . $result[1] . ")");
				}
				
				break;

			case 'latlng':
			case 'location':

				if ( isset( $response->results[0]->formatted_address ) ) {

					$result = $response->results[0]->formatted_address;
					
					$this->log("Geocode successful ($result)");

				}
				break;

		}
		
		

		$end_time = microtime( true );
		$delta_time = $end_time - $start_time;
		$min_time_between_requests = 1000000 / 10;

		if ( $delta_time < $min_time_between_requests ) {

			$delay = $min_time_between_requests - $delta_time;
			usleep( $delay );

		}

		return $result;

	}
}
