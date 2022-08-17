<?php 

namespace WPGMZA;

class ImportGeoJSON extends Import{

	const FEATURE_TYPE_POINT 		= "Point";
	const FEATURE_TYPE_LINE  		= "LineString";
	const FEATURE_TYPE_POLY  		= "Polygon";

	const FEATURE_TYPE_MULTI_POINT 	= "MultiPoint";
	const FEATURE_TYPE_MULTI_LINE 	= "MultiLineString";
	const FEATURE_TYPE_MULTI_POLY 	= "MultiPolygon";

	public function import() {

		$maps = array();

		if ($this->options['apply'] && is_array($this->options['applys'])){
			$maps = $this->options['applys'];

			if(!empty($this->options['replace'])){
				$this->clear_map_data();
			}
		} else {
			$maps[] = $this->createNewMap();
		}

		foreach ($maps as $mapId) {
			$this->activeMapId = intval($mapId);
			$this->importFeatures();
		}
		
		$this->onImportComplete();
	}

	protected function importFeatures(){
		if(!empty($this->file_data['features'])){
			$this->log("GeoJSON Import: Found " . count($this->file_data['features']) . " features");

			foreach ($this->file_data['features'] as $feature) {
				$type = $this->getTypeFromFeature($feature);

				switch ($type) {
					case self::FEATURE_TYPE_POINT:
					case self::FEATURE_TYPE_MULTI_POINT:
						$this->importMarker($feature, $type);
						break;
					case self::FEATURE_TYPE_LINE:
					case self::FEATURE_TYPE_MULTI_LINE:
						$this->importPolyine($feature, $type);
						break;
					case self::FEATURE_TYPE_POLY:
					case self::FEATURE_TYPE_MULTI_POLY:
						$this->importPolygon($feature, $type);
						break;
					default:
						$this->log("GeoJSON Import: Skip feature as type is unknown or not supported");
						break;
				}
			}
		}
	}

	protected function importMarker($feature, $type){
		$coordinates = !empty($feature['geometry']['coordinates']) ? $feature['geometry']['coordinates'] : false; 
		
		if(!empty($coordinates)){
			$fields = $this->hydrateFieldsFromFeature($feature, 
				array(
					'map_id'	  => $this->activeMapId,
					'address' 	  => '',
					'title'		  => '',
					'description' => '',
					'pic'		  => '',
					'link'		  => '',
					'icon' 		  => '',
					'approved'	  => 1,
				),
				'description'
			);

			if($type == self::FEATURE_TYPE_POINT){
				if($this->isValidLatLngPair($coordinates)){
					$this->insertMarker($fields, $coordinates);
				}
			} else if($type == self::FEATURE_TYPE_MULTI_POINT){
				foreach ($coordinates as $coords) {
					if($this->isValidLatLngPair($coords)){
						$this->insertMarker($fields, $coords);
					}
				}
			}
		}
	}

	protected function importPolyine($feature, $type){
		$coordinates = !empty($feature['geometry']['coordinates']) ? $feature['geometry']['coordinates'] : false; 
		
		if(!empty($coordinates)){
			$fields = $this->hydrateFieldsFromFeature($feature, 
				array(
					'map_id'	  => $this->activeMapId,
					'linecolor'		=> '000000',
					'linethickness'	=> 4,
					'opacity'		=> 0.8,
					'polyname'		=> 'Imported Line'
				),
				false
			);

			if($type === self::FEATURE_TYPE_LINE){
				$this->insertPolyline($fields, $coordinates);
			} else if($type === self::FEATURE_TYPE_MULTI_LINE){
				foreach ($coordinates as $coords) {
					$this->insertPolyline($fields, $coords);
				}
			}
		}
	}

	protected function importPolygon($feature, $type){
		$coordinates = !empty($feature['geometry']['coordinates']) ? $feature['geometry']['coordinates'] : false; 
		
		if(!empty($coordinates)){
			$fields = $this->hydrateFieldsFromFeature($feature, 
				array(
					'map_id'	    => $this->activeMapId,
					'polydata'		=> '',
					'linecolor'		=> '000000',
					'fillcolor'		=> '66F00',
					'ohfillcolor'   => '57FF78',
					'ohlinecolor'   => '737373',
					'link'			=> '',
					'polyname'		=> 'Imported Line',
					'title'			=> 'Imported Line',
					'lineopacity'	=> 0.5,
					'opacity'		=> 0.5,
					'ohopacity' 	=> 0.7
				),
				false
			);
		
			if($type === self::FEATURE_TYPE_POLY){
				$this->insertPolygon($fields, $coordinates);
			} else if($type === self::FEATURE_TYPE_MULTI_POLY){
				foreach ($coordinates as $coords) {
					$this->insertPolygon($fields, $coords);
				}
			}
		}
	}

	protected function insertMarker($fields, $coordinates){
		$latLng = $this->formatLatLng($coordinates);

		$fields['lat'] = $latLng['lat'];
		$fields['lng'] = $latLng['lng'];

		if(empty($fields['address'])){
			$fields['address'] = $fields['lat'] . ', ' . $fields['lng'];
		}

		if(empty($fields['title'])){
			$fields['title'] = $fields['address'];
		}

		$instance = Marker::createInstance();
		$instance->set($fields);
	}

	protected function insertPolyline($fields, $coordinates){
		global $wpdb;
		global $wpgmza_tblname_polylines;

		$polydata = "";
		foreach ($coordinates as $coords) {
			if($this->isValidLatLngPair($coords)){
				$latLng = $this->formatLatLng($coords);
				$polydata .= "(" . $latLng['lat'] . "," . $latLng['lng'] . "),";
			}
		}

		if(!empty($polydata)){
			$fields['polydata'] = $polydata;
			$success = $wpdb->insert( $wpgmza_tblname_polylines, $fields, 
				array(
					'%d',
					'%s',
					'%s',
					'%f',
					'%f',
					'%s',
				) 
			);
		}
	}

	protected function insertPolygon($fields, $coordinates){
		global $wpdb;
		global $wpgmza_tblname_poly;

		/*
		 * Before we loop through these, let's check for sub polygons, in some cases this may be present
		*/
		if(!$this->isValidLatLngPair($coordinates[0])){
			//The first point was not a lat/lng pair, it's safe to assume this needs to be refunelled
			foreach ($coordinates as $coords) {
				$this->insertPolygon($fields, $coords);
			}
		} else {
			$polydata = "";
			foreach ($coordinates as $coords) {
				if($this->isValidLatLngPair($coords)){
					$latLng = $this->formatLatLng($coords);
					$polydata .= '{"lat:"' . $latLng['lat'] . ',"lng:"' . $latLng['lng'] . "},";
				} 
			}

			if(!empty($polydata)){
				$fields['polydata'] = "[" . substr($polydata, 0, strlen($polydata) - 1) . "]";
				$success = $wpdb->insert($wpgmza_tblname_poly, $fields, 
					array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%f',
						'%f',
						'%f',
					) 
				);
			}
		}
	}

	protected function formatLatLng($coords){
		return array(
			'lat' => $coords[1],
			'lng' => $coords[0],
		);
	}

	protected function isValidLatLngPair($coords){
		if(!empty($coords[0]) && !empty($coords[1])){
			if(!is_array($coords[0]) && !is_array($coords[1])){
				return true;
			}
		}
		return false;
	}

	protected function hydrateFieldsFromFeature($feature, $fields, $concatKey, $concatGlue = "<br>"){
		if(!empty($feature['properties'])){
			$concat = "";
			foreach ($feature['properties'] as $key => $value) {
				if(is_array($value)){
					continue;
				}

				if(isset($fields[$key])){
					$fields[$key] = $value;
				} else if(isset($fields[strtolower($key)])){
					$fields[strtolower($key)] = $value;
				} else {
					$label = ucwords(strtolower($key)) . ":";
					$concat .= "{$label} {$value} {$concatGlue}";
				}
			}

			if(!empty($concatKey) && isset($fields[$concatKey])){
				$fields[$concatKey] .= $concat;
			}
		}
		return $fields;
	}

	protected function getTypeFromFeature($feature){
		if(!empty($feature['geometry']) && !empty($feature['geometry']['type'])){
			return $feature['geometry']['type'];
		}
		return false;
	}

	protected function createNewMap(){
		global $wpdb;
		global $wpgmza_tblname_maps;

		$this->log("GeoJSON Import: Creating new map");

		$success = $wpdb->insert( $wpgmza_tblname_maps, array( 
				'map_title'            => __( 'New Imported Map', 'wp-google-maps' ),
				'map_width'            => 100,
				'map_height'           => 400,
				'map_start_lat'        => '',
				'map_start_lng'        => '',
				'map_start_location'   => '',
				'map_start_zoom'       => 15,
				'default_marker'       => 0,
				'type'                 => 3,
				'alignment'            => 1,
				'directions_enabled'   => 1,
				'styling_enabled'      => 0,
				'styling_json'         => '',
				'active'               => 0,
				'kml'                  => '',
				'bicycle'              => 2,
				'traffic'              => 2,
				'dbox'                 => 4,
				'dbox_width'           => 100,
				'listmarkers'          => 0,
				'listmarkers_advanced' => 0,
				'filterbycat'          => 0,
				'ugm_enabled'          => 0,
				'ugm_category_enabled' => 0,
				'fusion'               => '',
				'map_width_type'       => '\%',
				'map_height_type'      => 'px',
				'mass_marker_support'  => 0,
				'ugm_access'           => 0,
				'order_markers_by'     => 2,
				'order_markers_choice' => 1,
				'show_user_location'   => 1,
				'default_to'           => '',
				'other_settings'       => '',
			), array(
				'%s',
				'%d',
				'%d',
				'%f',
				'%f',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
			) 
		);
		return $success ? $wpdb->insert_id : 1;
	}

	/**
	 * Clear map data from applys.
	 */
	protected function clear_map_data() {
		if (!$this->options['apply'] || empty($this->options['applys'])){
			return;
		}

		global $wpdb;
		global $wpgmza_tblname;
		global $wpgmza_tblname_circles;
		global $wpgmza_tblname_poly;
		global $wpgmza_tblname_polylines;
		global $wpgmza_tblname_rectangles;
		global $wpgmza_tblname_datasets;

		$applys_in = implode( ',', $this->options['applys'] );
		
		$wpdb->query( "DELETE FROM `$wpgmza_tblname` WHERE `map_id` IN ($applys_in)" );
		$wpdb->query( "DELETE FROM `$wpgmza_tblname_poly` WHERE `map_id` IN ($applys_in)" );
		$wpdb->query( "DELETE FROM `$wpgmza_tblname_polylines` WHERE `map_id` IN ($applys_in)" );

	}

	protected function check_options() {
		
		if ( ! is_array( $this->options ) ) {
			if(empty($this->options)){
				$this->options = array();
			} else {
				throw new \Exception( __( 'Error: Malformed options.', 'wp-google-maps' ) );
			}

		}

		$this->options['apply']   = isset( $this->options['apply'] ) ? true : false;
		$this->options['replace'] = isset( $this->options['replace'] ) ? true : false;
		$this->options['applys']  = isset( $this->options['applys'] ) ? explode( ',', $this->options['applys'] ) : array();

		if ( $this->options['apply'] && empty( $this->options['applys'] ) ) {
			$this->options['applys'] = import_export_get_maps_list( 'ids' );
		}

		$this->options['applys'] = $this->check_ids( $this->options['applys'] );
	}

	protected function parse_file() {
		$this->log("Attempting to parse GeoJSON");

		if(!empty($this->file_data)){
			$this->file_data = json_decode($this->file_data, true);
			
			if($this->file_data === null){
				$this->log("Failed to parse GeoJSON");
				$this->log(json_last_error_msg());
			
				throw new \Exception( __('Error parsing GeoJSON: ', 'wp-google-maps') . json_last_error_msg() );
			}

			if(empty($this->file_data['type']) || $this->file_data['type'] !== "FeatureCollection"){
				throw new \Exception( __('GeoJSON does not appear to be valid', 'wp-google-maps') );
			}

			if(empty($this->file_data['features'])){
				throw new \Exception( __('GeoJSON has no features', 'wp-google-maps') );
			}
		} else {
			$this->log("The file is empty");
			throw new \Exception( __( 'Error: Empty file data.', 'wp-google-maps' ) );
		}
	}

	public function admin_options(){
		$doing_edit = ! empty( $_POST['schedule_id'] ) ? true : false;

		$maps = import_export_get_maps_list( 'apply', $doing_edit ? $this->options['applys'] : false );

		ob_start();
		?>
			<h2><?php esc_html_e( 'Import GeoJSON', 'wp-google-maps' ); ?></h2>
			
			<h4><?php echo ! empty( $this->file ) ? esc_html( basename( $this->file ) ) : ( ! empty( $this->file_url ) ? esc_html( $this->file_url ) : '' ); ?></h4>
			
			<div class="switch">
				<input id="apply_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $maps ) ? 'disabled' : ( $doing_edit && $this->options['apply'] ? 'checked' : '' ); ?>>
				<label for="apply_import"></label>
			</div>

			<?php esc_html_e( 'Apply import data to', 'wp-google-maps' ); ?>
			<br>
			
			<span style="font-style:italic;"><?php esc_html_e( 'Leave this disabled to create a new map with the import', 'wp-google-maps' ); ?></span>
			<br>
		
			<div id="maps_apply_import" style="<?php echo empty( $maps ) ? 'display:none;' : ( $doing_edit && $this->options['apply'] ? '' : 'display:none;' ); ?>width:100%;">
				<?php if ( empty( $maps ) ) { ?>
					<br><?php esc_html_e( 'No maps available for import to.', 'wp-google-maps' ); ?>
				<?php } else { ?>
					<br>
					
					<div class="switch">
						<input id="replace_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['replace'] ? 'checked' : ''; ?>>
						<label for="replace_import"></label>
					</div>

					<?php esc_html_e( 'Replace map data', 'wp-google-maps' ); ?>
					<br>
					
					<table class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
						<thead style="display:block;border-bottom:1px solid #e1e1e1;">
							<tr style="display:block;width:100%;">
								<th style="width:2.2em;border:none;"></th>
								<th style="width:80px;border:none;"><?php esc_html_e( 'ID', 'wp-google-maps' ); ?></th>
								<th style="border:none;"><?php esc_html_e( 'Title', 'wp-google-maps' ); ?></th>
							</tr>
						</thead>
						
						<tbody style="display:block;max-height:370px;overflow-y:scroll;">
							<?php echo $maps; ?>
						</tbody>
					</table>

					<button id="maps_apply_select_all" class="wpgmza_general_btn"><?php esc_html_e( 'Select All', 'wp-google-maps' ); ?></button> 
					<button id='maps_apply_select_none' class='wpgmza_general_btn'><?php esc_html_e( 'Select None', 'wp-google-maps' ); ?></button>

					<br><br>
				<?php } ?>
			</div>
			
			<br>
			
			<div class="delete-after-import">
				<div class="switch">
					<input id="delete_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit ? 'disabled' : ''; ?>>
					<label for="delete_import"></label>
				</div>
				<?php esc_html_e( 'Delete import file after import', 'wp-google-maps' ); ?>
			</div>
		
			<br><br>
		
			<div id="import-schedule-geojson-options" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>>
				<h2><?php esc_html_e( 'Scheduling Options', 'wp-google-maps' ); ?></h2>
				
				<?php esc_html_e( 'Start Date', 'wp-google-maps' ); ?>
				<br>
				<input type="date" id="import-schedule-geojson-start" class="import-schedule-geojson-options" <?php echo $doing_edit ? 'value="' . $this->options['start'] . '"' : ''; ?>>
			
				<br><br>
			
				<?php esc_html_e( 'Interval', 'wp-google-maps' ); ?>
				<br>
				<select id="import-schedule-geojson-interval" class="import-schedule-geojson-options">
					<?php
						$schedule_intervals = wp_get_schedules();
						foreach ( $schedule_intervals as $schedule_interval_key => $schedule_interval ) { 
							?>
							<option value="<?php echo esc_attr( $schedule_interval_key ); ?>" <?php echo $doing_edit && $schedule_interval_key === $this->options['interval'] ? 'selected' : ''; ?>><?php echo esc_html( $schedule_interval['display'] ); ?></option>
							<?php 
						} 
					?>
				</select>
				
				<br><br>
			</div>
		
			<p>
				<button id="import-geojson" class="wpgmza_general_btn" <?php if ( $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
				<button id="import-schedule-geojson" class="wpgmza_general_btn"><?php echo $doing_edit ? esc_html__( 'Update Schedule', 'wp-google-maps' ) : esc_html__( 'Schedule', 'wp-google-maps' ); ?></button>
				<button id="import-schedule-geojson-cancel" class="wpgmza_general_btn" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Cancel', 'wp-google-maps' ); ?></button>
			</p>
			
			<script>
				(function($){
					<?php if ( ! $doing_edit ) { ?>$('.maps_apply').prop('checked', false);<?php } ?>
					
					$('#maps_apply_select_all').click(function(){
						$('.maps_apply').prop('checked', true);
					});
					
					$('#maps_apply_select_none').click(function(){
						$('.maps_apply').prop('checked', false);
					});
					
					$('#apply_import').click(function(){
						if ($(this).prop('checked')){
							$('#maps_apply_import').slideDown(300);
						} else {
							$('#maps_apply_import').slideUp(300);
						}
					});

					function geojson_get_import_options(){
						var import_options = {};
						var apply_check = $('.maps_apply:checked');
						var apply_ids = [];
					
						if ($('#apply_import').prop('checked')){
							if (apply_check.length < 1){
								alert('<?php echo wp_slash( __( 'Please select at least one map to import to, or deselect the "Apply import data to" option.', 'wp-google-maps' ) ); ?>');
								return {};
							}

							apply_check.each(function(){
								apply_ids.push($(this).val());
							});
							
							if (apply_ids.length > 0){
								import_options['applys'] = apply_ids.join(',');
							}

							if ($('#replace_import').prop('checked')){
								import_options['replace'] = true;
							}

							if ($('#apply_import').prop('checked')){
								import_options['apply'] = true;
							}
						}
						
						return import_options;
					}
				
					$('#import-geojson').click(function(){
						var import_options = geojson_get_import_options();

						$('#import_loader_text').html('<br><?php echo wp_slash( __( 'Importing, this may take a moment...', 'wp-google-maps' ) ); ?>');
						$('#import_loader').show();
						$('#import_options').hide();
					
						wp.ajax.send({
							data: {
								action: 'wpgmza_import',
								<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $_POST['import_url'] . "'," : '' ); ?>

								options: import_options,
								wpgmaps_security: WPGMZA.import_security_nonce
							},
							success: function (data) {
								$('#import_loader').hide();
								
								if (typeof data !== 'undefined' && data.hasOwnProperty('id')) {
									wpgmaps_import_add_notice('<p><?php echo wp_slash( __( 'Import completed.', 'wp-google-maps' ) ); ?></p>');
									if (data.hasOwnProperty('del') && 1 === data.del){
										$('#import_options').html('');
										$('#import-list-item-' + data.id).remove();
										$('#import_files').show();
										return;
									}
								}
								
								$('#import_options').show();
							},
							error: function (data) {
								if (typeof data !== 'undefined') {
									wpgmaps_import_add_notice(data, 'error');
								}
								
								$('#import_loader').hide();
								$('#import_options').show();
							}
						});
					});
				
				$('#import-schedule-geojson').click(function(){
					if ($('#import-geojson').is(':visible')) {
						$('#import-geojson,.delete-after-import').hide();
						$('#import-schedule-geojson-cancel').show();
						$('#import-schedule-geojson-options').slideDown(300);
					} else {
						var import_options = json_get_import_options();
						if (Object.keys(import_options).length < 1){
							return;
						}
						if ($('#import-schedule-geojson-start').val().length < 1){
							alert('<?php echo wp_slash( __( 'Please enter a start date.', 'wp-google-maps' ) ); ?>');
							return;
						}
						$('#import_loader_text').html('<br><?php echo wp_slash( __( 'Scheduling, this may take a moment...', 'wp-google-maps' ) ); ?>');
						$('#import_loader').show();
						$('#import_options').hide();
						wp.ajax.send({
							data: {
								action: 'wpgmza_import_schedule',
								<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $_POST['import_url'] . "'," : '' ); ?>

								options: import_options,
								<?php echo isset( $_POST['schedule_id'] ) ? "schedule_id: '" . $_POST['schedule_id'] . "'," : ''; ?>

								start: $('#import-schedule-geojson-start').val(),
								interval: $('#import-schedule-geojson-interval').val(),
								wpgmaps_security: WPGMZA.import_security_nonce
							},
							success: function (data) {
								if (typeof data !== 'undefined' && data.hasOwnProperty('schedule_id') && data.hasOwnProperty('next_run')) {
									wpgmaps_import_add_notice('<p><?php echo wp_slash( __( 'Scheduling completed.', 'wp-google-maps' ) ); ?></p>');
									$('#import_loader').hide();
									$('#import_options').html('').hide();
									$('#import_files').show();
									$('a[href="#schedule-tab"').click();
									$('#wpgmap_import_schedule_list_table tbody').prepend('<tr id="import-schedule-list-item-' + data.schedule_id + '"><td><strong><span class="import_schedule_title" style="font-size:larger;">' + data.title + '</span></strong><br>' +
										'<a href="javascript:void(0);" class="import_schedule_edit" data-schedule-id="' + data.schedule_id + '"><?php esc_html_e( 'Edit', 'wp-google-maps' ); ?></a>' +
										' | <a href="javascript:void(0);" class="import_schedule_delete" data-schedule-id="' + data.schedule_id + '"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?></a>' +
										' | ' + ( ( data.next_run.length < 1 || ! data.next_run ) ? '<?php esc_html_e( 'No schedule found', 'wp-google-maps' ); ?>' :
										'<?php esc_html_e( 'Next Scheduled Run', 'wp-google-maps' ); ?>: ' + data.next_run ) + '</td></tr>' );
									wpgmaps_import_setup_schedule_links(data.schedule_id);
									$('#wpgmaps_import_schedule_list').show();
								}
							},
							error: function (data) {
								if (typeof data !== 'undefined') {
									wpgmaps_import_add_notice(data, 'error');
									$('#import_loader').hide();
									$('#import_options').show();
								}
							}
						});
					}
				});
				$('#import-schedule-geojson-cancel').click(function(){
					$('#import-geojson,.delete-after-import').show();
					$('#import-schedule-geojson-cancel').hide();
					$('#import-schedule-geojson-options').slideUp(300);
				});
			})(jQuery);
		</script>
		<?php

		return ob_get_clean();

	}
}