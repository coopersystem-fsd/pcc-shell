<?php

namespace WPGMZA;

/**
 * Airtables import integration module.
 */
class ImportAIRTABLES extends Import
{
	
	public function __construct($file='', $file_url='', $options=array())
	{
		Import::__construct($file, $file_url, $options);
		
		$this->notices = array();
		$this->failure_message_by_handle = array(
			'geocode_failed'				=> __('Failed to geocode address', 'wp-google-maps'),
			'no_address'					=> __('No address specified for geocoding', 'wp-google-maps'),
			'no_address_or_coordinates'		=> __('No address or coordinates specified', 'wp-google-maps')
		);
		$this->failed_rows_by_handle = array();
	}

	protected function parse_file() 
	{
		$this->import_type = 'marker';
		
		$this->log("Attempting to parse Airtable");

		if ( ! empty( $this->file_data ) ) {
			
			$this->file_data = json_decode( $this->file_data, true );
			
			if( $this->file_data === null )
			{
				$this->log("Failed to parse Airtable");
				$this->log(json_last_error_msg());
			
				throw new \Exception( __('Error parsing Airtable: ', 'wp-google-maps') . json_last_error_msg() );
			}

			if ( empty( $this->file_data['records'] ) )
			{
				$this->log("Invalid Airtable Structure");
				
				throw new \Exception( __( 'Error: Invalid Airtable structure.', 'wp-google-maps' ) );
			}

			$this->file_data['markers'] = array();

			foreach ($this->file_data['records'] as $field_data) {
				if(!empty($field_data['fields'])){
					$this->file_data['markers'][] = $field_data['fields'];	
						
				}
			} 

		} else {
			
			$this->log("The Airtable is empty");

			throw new \Exception( __( 'Error: No Airtable data.', 'wp-google-maps' ) );
		}
	}

	public function import() {


		//if($this->import_type != 'map')
		//	$this->create_map();

		$this->import_markers();

		$this->onImportComplete();
	}



	protected function import_markers() {
		if (empty($this->file_data['markers'])){
			$this->log("No marker data found");
			return;
		}
		
		global $wpdb;
		global $wpgmza_tblname;
		global $wpgmza_tblname_maps;
		
		// Delete all markers that are listed in the Airtable for specific map ids
		if(!empty($this->options['keep_map_id'])){
			
			$mapIDs = array();
			
			foreach($this->file_data['markers'] as $fields){
				if(empty($fields['map_id']))
					continue;
				
				$mapIDs[$fields['map_id']] = true;
			}
			
			$mapIDs = array_keys($mapIDs);
			
			if(!empty($mapIDs)){
				$imploded = implode(',', array_map("intval", $mapIDs));
				$qstr = "DELETE FROM $wpgmza_tblname WHERE map_id IN ($imploded)";
				$wpdb->query($qstr);
			}
		} else {
			if (!empty($this->options['applys'])) {
				$mapIDs = implode(',', array_map("intval", $this->options['applys']));

				if(!empty($mapIDs)){
					$qstr = "DELETE FROM $wpgmza_tblname WHERE map_id IN ($mapIDs)";
					$wpdb->query($qstr);
				}
			}
		}

		$total_markers = count($this->file_data['markers']);
		$current_marker_index = 1;

		//Loop through the markers and peform the import
		foreach($this->file_data['markers'] as $fields){

			$this->set_progress( $current_marker_index / $total_markers );
			$current_marker_index++;

			if($this->options['geocode']){
				
				if(empty($fields['address']) && empty($fields['lat']) && empty($fields['lng'])){
					$this->failure( 'no_address_or_coordinates', $current_marker_index );
					continue;
				}
				
				if(!empty($fields['address']) && (empty($fields['lat']) || empty($fields['lng']))){

					$address = $fields['address'];
					$latlng = $this->geocode($address);
					if($latlng == false){
						if (!empty( $this->geocode_response->status) && !empty($this->geocode_response->error_message)){

							$status = $this->geocode_response->status;
							$error_message = $this->geocode_response->error_message;

							if(!isset($this->failure_message_by_handle[$status])){
								$this->failure_message_by_handle[$status] = rtrim($error_message, ' .' );
							}

							$this->failure($status, $current_marker_index);

						} else {
							$this->failure('geocode_failed', $current_marker_index);
						}

						continue;
					}

					$fields['lat'] = isset($latlng[0]) ? $latlng[0] : 0;
					$fields['lng'] = isset($latlng[1]) ? $latlng[1] : 0;

				}
				
				if(empty($fields['address']) && !empty($fields['lat']) && !empty($fields['lng'])){
					$fields['address'] = $this->geocode( "{$fields['lat']},{$fields['lng']}", 'latlng' );
				}
			}

			$lat = $fields['lat'];
			$lng = $fields['lng'];

			if(!is_numeric($lat)){
				if(!$this->options['geocode'] && empty($lat)){
					$status = 'empty_latitude';
					
					$this->failure_message_by_handle[$status] = __('No latitude supplied, "Find latitude" not selected. Marker will have zero latitude', 'wp-google-maps');
					$this->failure($status, $current_marker_index);

				} else {

					$status = 'invalid_latitude';
					
					$this->failure_message_by_handle[ $status ] = __('Invalid latitude, supplied value is not numeric', 'wp-google-maps');
					$this->failure($status, $current_marker_index);
				}
			}

			if(!is_numeric($lng)){
				if(!$this->options['geocode'] && empty($lng)){
					$status = 'empty_longitude';
					
					$this->failure_message_by_handle[$status] = __('No longitude supplied, "Find longitude" not selected. Marker will have zero longitude', 'wp-google-maps');
					$this->failure($status, $current_marker_index);
				} else {					
					$status = 'invalid_longitude';
					
					$this->failure_message_by_handle[$status] = __('Invalid longitude, supplied value is not numeric', 'wp-google-maps');
					$this->failure($status, $current_marker_index);
				}
			}

			if(empty($fields['lat'])){
				$fields['lat'] = 0;
			}

			if(empty($fields['lng'])){
				$fields['lng'] = 0;
			}

			if(empty($fields['address'])){
				$fields['address']= "{$fields['lat']},{$fields['lng']}";
			}

			if(!empty($this->options['keep_map_id'])){
				$this->options['applys'] = array($fields['map_id']);
			}

			// Loop through each map this marker should be added to.
			foreach ( $this->options['applys'] as $map_id ) {
				
				$required_fields = array(
					'map_id',
					'address',
					'description',
					'pic',
					'link',
					'icon',
					'lat',
					'lng',
					'anim',
					'title',
					'infoopen',
					'category',
					'approved',
					'retina',
					'type',
					'did',
					'other_data'
				);
								
				foreach($required_fields as $field_name){

					$field_value = (isset($fields[$field_name]) ? $fields[$field_name] : null);
					
					if(function_exists('iconv') && function_exists('mb_detect_encoding') && function_exists('mb_detect_order')){
						$field_value = iconv(mb_detect_encoding($field_value, mb_detect_order(), true), "UTF-8", $field_value);
					}

					$fields[$field_name] = $field_value;
				}
				
				if(empty($this->options['keep_map_id'])){
					$fields['map_id'] = $map_id;
				}
				
				// Remove ID field
				if (isset($fields['id'])) {
					unset($fields['id']);
				}

				$instance = Marker::createInstance();
				$instance->set($fields);
			
			}

			$this->bail_if_near_time_limit();
		}
	}



	/**
	 * Output admin import options.
	 *
	 * @return string Options html.
	 */
	public function admin_options()
	{
		$doing_edit = ! empty( $_POST['schedule_id'] ) ? true : false;

		$source = !empty( $this->file ) ? esc_html( basename( $this->file ) ) : ( ! empty( $this->file_url ) ? esc_html( $this->file_url ) : '' );
		
		$maps = import_export_get_maps_list( 'apply', $doing_edit ? $this->options['applys'] : false );

		ob_start();
		?>
		<h2><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></h2>
		<h4 data-wpgmza-import-source='<?php echo $source; ?>'><?php echo $source; ?></h4>
		<p>
		<h2><?php esc_html_e( 'Import Airtables', 'wp-google-maps' ); ?></h2>
		</p>
		<p>
		<?php
		switch ( $this->import_type ) {
			case 'marker':
				esc_html_e( 'Marker data found.', 'wp-google-maps' );
				break;
		} ?>
		</p>
		<?php
		if($this->import_type == 'marker')
		{
			?>
			<div class="switch"><input id="geocode_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['geocode'] ? 'checked' : ''; ?>><label for="geocode_import"></label></div><?php esc_html_e( 'Find Addresses or Latitude and Longitude when missing', 'wp-google-maps' ); ?><br>
			<span style="font-style:italic;"><?php esc_html_e( 'Requires Google Maps Geocoding API to be enabled.', 'wp-google-maps' ); ?></span> <a href="https://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/" target="_blank">[?]</a><br>
			<br>
			
			<div class="switch">
				<input id="keep_map_id" name="keep_map_id" class="cmn-toggle cmn-toggle-round-flat" type="checkbox"/>
				<label for="keep_map_id"></label></div><?php esc_html_e("Use map ID's specified in file", "wp-google-maps"); ?>
			<br/>
			
			<div class="switch"><input id="apply_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $maps ) ? 'disabled' : ( $doing_edit && $this->options['apply'] ? 'checked' : '' ); ?>><label for="apply_import"></label></div><?php esc_html_e( 'Apply import data to', 'wp-google-maps' ); ?>
			<br>
			<div id="maps_apply_import" style="<?php echo empty( $maps ) ? 'display:none;' : ( $doing_edit && $this->options['apply'] ? '' : 'display:none;' ); ?>width:100%;">
				<?php if ( empty( $maps ) ) { ?>
					<br><?php esc_html_e( 'No maps available for import to.', 'wp-google-maps' ); ?>
				<?php } else { ?>
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
					<button id="maps_apply_select_all" class="wpgmza_general_btn"><?php esc_html_e( 'Select All', 'wp-google-maps' ); ?></button> <button id='maps_apply_select_none' class='wpgmza_general_btn'><?php esc_html_e( 'Select None', 'wp-google-maps' ); ?></button><br><br>
				<?php } ?>
			</div>
			<?php
		}

		?>
		
		<br><br>
		<div id="import-schedule-csv-options" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>>
			<h2><?php esc_html_e( 'Scheduling Options', 'wp-google-maps' ); ?></h2>
			<?php esc_html_e( 'Start Date', 'wp-google-maps' ); ?>
			<br>
			<input type="date" id="import-schedule-csv-start" class="import-schedule-csv-options" <?php echo $doing_edit ? 'value="' . $this->options['start'] . '"' : ''; ?>>
			<br><br>
			<?php esc_html_e( 'Interval', 'wp-google-maps' ); ?>
			<br>
			<select id="import-schedule-csv-interval" class="import-schedule-csv-options">
				<?php
				$schedule_intervals = wp_get_schedules();
				foreach ( $schedule_intervals as $schedule_interval_key => $schedule_interval ) { ?>
					<option value="<?php echo esc_attr( $schedule_interval_key ); ?>" <?php echo $doing_edit && $schedule_interval_key === $this->options['interval'] ? 'selected' : ''; ?>><?php echo esc_html( $schedule_interval['display'] ); ?></option>
				<?php } ?>
			</select>
			<br><br>
		</div>
		<p>
			<button id="import-csv" class="wpgmza_general_btn" <?php if ( $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
			<button id="import-schedule-csv" class="wpgmza_general_btn"><?php echo $doing_edit ? esc_html__( 'Update Schedule', 'wp-google-maps' ) : esc_html__( 'Schedule', 'wp-google-maps' ); ?></button>
			<button id="import-schedule-csv-cancel" class="wpgmza_general_btn" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Cancel', 'wp-google-maps' ); ?></button>
		</p>
		<script>
			// TODO: Put this in a separate JS file and localize all the data that's using in inline PHP here
			
			(function($) {
				<?php if ( ! $doing_edit ) { ?>$('.maps_apply').prop('checked', false);<?php } ?>
				$('#maps_apply_select_all').click(function () {
					$('.maps_apply').prop('checked', true);
				});
				$('#maps_apply_select_none').click(function () {
					$('.maps_apply').prop('checked', false);
				});
				$('#apply_import').click(function () {
					if ($(this).prop('checked')) {
						$('#maps_apply_import').slideDown(300);
					} else {
						$('#maps_apply_import').slideUp(300);
					}
				});
				function csv_get_import_options(){
					var import_options = {};
					var apply_check = $('.maps_apply:checked');
					var apply_ids = [];
					$('.csv_data_import').each(function(){
						if ($(this).prop('checked')){
							import_options[ $(this).attr('id').replace('_import', '') ] = '';
						}
					});
					if ($('#apply_import').prop('checked')){
						if (apply_check.length < 1){
							alert('<?php echo wp_slash( __( 'Please select at least one map to import to, or deselect the "Apply import data to" option.', 'wp-google-maps' ) ); ?>');
							return {};
						}
						apply_check.each(function(){
							apply_ids.push($(this).val());
						});
						if (apply_ids.length < $('.maps_apply').length){
							import_options['applys'] = apply_ids.join(',');
						}
					}
					
					if($("#keep_map_id").prop("checked"))
						import_options['keep_map_id'] = true;
					
					return import_options;
				}
				$('#import-csv').click(function(){
					var import_options = csv_get_import_options();
					
					// NB: Commented out, this prevents map data import if no boxes are checked
					/*if (Object.keys(import_options).length < 1){
						return;
					}*/
					
					$('#import_loader_text').html('<br/>\
						<?php 
						echo wp_slash( __( 'Importing, this may take a moment...', 'wp-google-maps' ) ); 
						?> \
						<br/>\
						<progress id="wpgmza-import-csv-progress"/>\
						');
					
					$('#import_loader').show();
					$('#import_options').hide();
					
					var source = $("[data-wpgmza-import-source]").attr("data-wpgmza-import-source");
					var progressIntervalID = setInterval(function() {
						
						wp.ajax.send({
							data: {
								action: 'wpgmaps_get_import_progress',
								source: source,
								wpgmaps_security: WPGMZA.import_security_nonce
							},
							success: function(data) {
								$("#wpgmza-import-csv-progress").val(data);
							}
						})
						
					}, 5000);
					
					wp.ajax.send({
						data: {
							action: 'wpgmza_import',
							<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $source . "'," : '' ); ?>

							options: import_options,
							wpgmaps_security: WPGMZA.import_security_nonce
						},
						success: function (data) {
							
							clearInterval(progressIntervalID);
							
							$('#import_loader').hide();
							
							if (typeof data !== 'undefined' && data.hasOwnProperty('id')) {
								
								var type = "success";
								if(data.notices.length > 0)
									type = "warning";
								
								wpgmaps_import_add_notice('<p><?php 
									echo wp_slash( __( 'Import completed.', 'wp-google-maps' ) ); 
								?></p>', type);
								
								for(var i = 0; i < data.notices.length; i++) {
									wpgmaps_import_add_notice('<p>' + data.notices[i] + '</p>', 'error', true);
								}
								
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
							
							var string = (typeof data == "string" ? data : data.statusText);
							
							clearInterval(progressIntervalID);
							
							if (typeof data !== 'undefined') {
								wpgmaps_import_add_notice(data, 'error');
							}
							$('#import_loader').hide();
							$('#import_options').show();
						}
					});
				});
				$('#import-schedule-csv').click(function(){
					if ($('#import-csv').is(':visible')) {
						$('#import-csv,.delete-after-import').hide();
						$('#import-schedule-csv-cancel').show();
						$('#import-schedule-csv-options').slideDown(300);
					} else {
						var import_options = csv_get_import_options();
						if (Object.keys(import_options).length < 1){
							alert('<?php echo wp_slash( __( 'The schedule must target an existing map, or use map ID\'s specified in the file.', 'wp-google-maps' ) ); ?>');
							return;
						}
						if ($('#import-schedule-csv-start').val().length < 1){
							alert('<?php echo wp_slash( __( 'Please enter a start date.', 'wp-google-maps' ) ); ?>');
							return;
						}
						$('#import_loader_text').html('<br><?php echo wp_slash( __( 'Scheduling, this may take a moment...', 'wp-google-maps' ) ); ?>');
						$('#import_loader').show();
						$('#import_options').hide();
						wp.ajax.send({
							data: {
								action: 'wpgmza_import_schedule',
								<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $source . "'," : '' ); ?>

								options: import_options,
								<?php echo isset( $_POST['schedule_id'] ) ? "schedule_id: '" . $_POST['schedule_id'] . "'," : ''; ?>

								start: $('#import-schedule-csv-start').val(),
								interval: $('#import-schedule-csv-interval').val(),
								wpgmaps_security: WPGMZA.import_security_nonce
							},
							success: function (data) {
								if (typeof data !== 'undefined' && data.hasOwnProperty('schedule_id') && data.hasOwnProperty('next_run')) {
									wpgmaps_import_add_notice('<p><?php echo wp_slash( __( 'Scheduling completed.', 'wp-google-maps' ) ); ?></p>');
									$('#import_loader').hide();
									$('#import_options').html('').hide();
									$('#import_files').show();
									$('a[href="#schedule-tab"').click();
									var schedule_listing = '<tr id="import-schedule-list-item-' + data.schedule_id + '"><td><strong><span class="import_schedule_title" style="font-size:larger;">' + data.title + '</span></strong><br>' +
										'<a href="javascript:void(0);" class="import_schedule_edit" data-schedule-id="' + data.schedule_id + '"><?php esc_html_e( 'Edit', 'wp-google-maps' ); ?></a>' +
										' | <a href="javascript:void(0);" class="import_schedule_delete" data-schedule-id="' + data.schedule_id + '"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?></a>' +
										' | ' + ((data.next_run.length < 1 || !data.next_run) ? '<?php esc_html_e( 'No schedule found', 'wp-google-maps' ); ?>' :
											'<?php esc_html_e( 'Next Scheduled Run', 'wp-google-maps' ); ?>: ' + data.next_run) + '</td></tr>';
									if ($('#import-schedule-list-item-' + data.schedule_id).length > 0){
										$('#import-schedule-list-item-' + data.schedule_id).replaceWith(schedule_listing);
									} else {
										$('#wpgmap_import_schedule_list_table tbody').prepend(schedule_listing);
									}
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
				$('#import-schedule-csv-cancel').click(function(){
					$('#import-csv,.delete-after-import').show();
					$('#import-schedule-csv-cancel').hide();
					$('#import-schedule-csv-options').slideUp(300);
				});
			})(jQuery);
		<?php

		return ob_get_clean();

	}


	/**
	 * Check options.
	 *
	 * @throws \Exception On malformed options.
	 */
	protected function check_options() {
		
		if ( ! is_array( $this->options ) ) {

			if(empty($this->options))
				$this->options = array();
			else
				throw new \Exception( __( 'Error: Malformed options.', 'wp-google-maps' ) );

		}

		$this->options['geocode'] = isset( $this->options['geocode'] ) ? true : false;
		$this->options['apply']   = isset( $this->options['apply'] ) ? true : false;
		$this->options['replace'] = isset( $this->options['replace'] ) ? true : false;
		$this->options['applys']  = isset( $this->options['applys'] ) ? explode( ',', $this->options['applys'] ) : array();

		if ( $this->options['apply'] && empty( $this->options['applys'] ) ) {

			$this->options['applys'] = import_export_get_maps_list( 'ids' );

		}

		$this->options['applys'] = $this->check_ids( $this->options['applys'] );

	}
	
	protected function failure($handle, $row_index) {
		
		if(!isset($this->failed_rows_by_handle[$handle]))
			$this->failed_rows_by_handle[$handle] = array();
		
		$this->failed_rows_by_handle[$handle][] = $row_index;
	}

}