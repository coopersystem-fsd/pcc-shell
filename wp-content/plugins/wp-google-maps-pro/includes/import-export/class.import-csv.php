<?php
/**
 * WP Google Maps Pro Import / Export API: ImportCSV class
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */
 
namespace WPGMZA;

/**
 * CSV importer for WP Google Maps Pro
 *
 * This handles importing of files.
 *
 * @since 7.0.0
 */
class ImportCSV extends Import {

	protected $notices;

	/**
	 * Import type.
	 *
	 * The type of import ie. map, marker, polyline etc.
	 *
	 * @var string Import type.
	 */
	protected $import_type = '';

	/**
	 * Header map.
	 *
	 * Maps header key string to array indexes.
	 *
	 * @var array Key is header string, value is array index.
	 */
	protected $header_map = array();

    /**
     * Custom field map.
     *
     * Maps custom field headers to custom field ids.
     *
     * @var array Key is header string, value is custom field id.
     */
    protected $custom_field_map = array();
	
	/**
	 * Error messages by handle
	 */
	protected $failure_message_by_handle;
	protected $failed_rows_by_handle;

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

	/**
	 * Appends a line to the log
	 * @return void
	 */
	protected function failure($handle, $row_index) {
		
		if(!isset($this->failed_rows_by_handle[$handle]))
			$this->failed_rows_by_handle[$handle] = array();
		
		$this->failed_rows_by_handle[$handle][] = $row_index;
		
		$this->log("$handle on $row_index");
	}
	
	/**
	 * Turns an array of numerical values, eg 2, 3, 4, 7, 8, 9, 10 and
	 * condenses them down insto a string eg 2 - 4, 7 - 10
	 * @return string
	 */
	protected function condense_row_ids($ids) {
		
		if(empty($ids))
			return "";
		
		$len = count($ids);
		$prev = null;
		$parts = array();
		$range = null;
		$result = '';
		
		for($i = 0; $i < $len; $i++)
		{
			$value = $ids[$i];
			
			if($prev === null || $value > $prev + 1)
			{
				// Start new range
				$range = (object)array(
					'start' => $value,
					'end' => $value
				);
				$parts[] = $range;
			}
			else
			{
				// Continue existing range
				$parts[count($parts) - 1]->end = $value;
			}
			
			$prev = $value;
		}
		
		foreach($parts as $range)
		{
			if($range->start == $range->end)
				$result .= $range->start;
			else
				$result .= $range->start . ' - ' . $range->end;
			$result .= ', ';
		}
		
		return rtrim($result, ', ');
		
	}
	
	/**
	 * Get the import failure notices
	 * @return string The HTML
	 */
	public function get_admin_notices() {
		
		$notices = array_merge(array(), $this->notices);
		
		foreach($this->failed_rows_by_handle as $handle => $row_indicies)
		{
			$message = $this->failure_message_by_handle[$handle];
			
			if(!empty($row_indicies)) {
				$message .= __(' on row(s) ', 'wp-google-maps');
				$message .= $this->condense_row_ids($row_indicies);
			}
			
			$notices[] = $message;
		}
		
		return $notices;
		
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

	/**
	 * Parse file data.
	 *
	 * @throws \Exception When no header data found in file.
	 */
	protected function parse_file() {

        global $wpdb;
        global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;

		// Convert newlines to <br/> and remove newlines, this ensures proper newline support
		$lines = array();
		
		$original_data = $this->file_data;
		
		// Convert \r to \r\n
		$converted = preg_replace('/\r(\n?)/u', "\r\n", $this->file_data);
		
		if(!$converted)
		{
			$error = preg_last_error();
			$message = 'Error when converting line endings (' . $error . ')';
			
			switch($error)
			{
				case PREG_INTERNAL_ERROR:
					$message = 'Internal error when converting line endings';
					break;
				
				case PREG_BACKTRACK_LIMIT_ERROR:
					$message = 'Backtrack limit error when converting line endings';
					break;
				
				case PREG_RECURSION_LIMIT_ERROR:
					$message = 'Recursion limit error when converting line endings';
					break;
				
				case PREG_BAD_UTF8_ERROR:
					$message = 'Bad UTF-8 error when converting line endings';
					break;
					
				case PREG_BAD_UTF8_OFFSET_ERROR:
					$message = 'Bad UTF-8 offset error when converting line endings';
					break;
				
				default:
					break;
			}
			
			$this->failure_message_by_handle['converting_line_endings'] = $message;
			$this->failure('converting_line_endings', 0);
		}
		else
			$this->file_data = $converted;
		
		$fp = fopen('php://memory', 'r+');
		fwrite($fp, $this->file_data);
		rewind($fp);
		
		while($cells = fgetcsv($fp))
		{
			foreach($cells as $key => $value)
				$cells[$key] = preg_replace('/[\r\n]/', '', nl2br($value));
			
			$lines[] = $cells;
		}
		
		fclose($fp);
		
		$fp = fopen('php://memory', 'r+');
		$this->file_data = array();
		
		foreach($lines as $line)
		{
			fputcsv($fp, $line);
			rewind($fp);
			
			$string = fread($fp, 65536);
			$this->file_data[] = $string;
			
			ftruncate($fp, 0);
		}
		
		$headers = str_getcsv( $this->file_data[0] );
		unset( $this->file_data[0] );

        CustomFields::install();
        $existing_custom_fields = $wpdb->get_results("SELECT `id`, `name` FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");

        foreach ( $headers as $index => $header ) {

			if ( strlen( $header ) > 0 ) {

			    if ( false !== stristr( $header, 'Custom Field:' ) ) {

                    $custom_field_name = trim( str_ireplace( 'Custom Field:', '', $header ) );
                    $custom_field_found = false;
                    if ( is_array( $existing_custom_fields ) ) {

                        foreach ( $existing_custom_fields as $custom_fields ) {

                            if ( strtolower( $custom_fields->name ) == strtolower( $custom_field_name ) ) {

                                $this->custom_field_map[$index] = $custom_fields->id;
                                $custom_field_found = true;
                                break;

                            }
                        }
                    }

                    if ( !$custom_field_found ) {

                        $insert_success = $wpdb->insert(
                            $WPGMZA_TABLE_NAME_CUSTOM_FIELDS,
                            array(
                                'name' => $custom_field_name,
                                'icon' => '',
                                'attributes' => '{"":""}',
                                'widget_type' => 'none',
                            ),
                            array(
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                            )
                        );

                        if ( $insert_success ) {

                            $this->custom_field_map[$index] = $wpdb->insert_id;

                        }
                    }
                } else {

                    $header = strtolower( str_replace( ' ', '_', $header ) );
                    $this->header_map[$header] = $index;

                }
			}
		}
		
		if( in_array('map_title', $headers, true) ) {
			
			$this->import_type = 'map';
			return;
			
		}

		if ( in_array( 'address', $headers, true ) ||
		     ( in_array( 'lat', $headers, true ) && ( in_array( 'lng', $headers, true ) ) ) ) {

			$this->import_type = 'marker';
			return;

		}

		if ( in_array( 'center_x', $headers, true ) && in_array( 'center_y', $headers, true ) &&
		     in_array( 'radius', $headers, true ) ) {

			$this->import_type = 'circle';
			return;

		}

		if ( in_array( 'polydata', $headers, true ) && in_array( 'innerpolydata', $headers, true ) ) {

			$this->import_type = 'polygon';
			return;

		}

		if ( in_array( 'polydata', $headers, true ) ) {

			$this->import_type = 'polyline';
			return;

		}

		if ( in_array( 'corner_ax', $headers, true ) && in_array( 'corner_ay', $headers, true ) &&
		     in_array( 'corner_bx', $headers, true ) && in_array( 'corner_by', $headers, true ) ) {

			$this->import_type = 'rectangle';
			return;

		}

		if ( in_array( 'dataset', $headers, true ) ) {

			$this->import_type = 'dataset';
			return;

		}

		throw new \Exception( __( 'We couldn\'t establish what kind of data you are trying to import (is the header row missing?)', 'wp-google-maps' ) );

	}

	/**
	 * Output admin import options.
	 *
	 * @return string Options html.
	 */
	public function admin_options() {
		
		$doing_edit = ! empty( $_POST['schedule_id'] ) ? true : false;

		$source = !empty( $this->file ) ? esc_html( basename( $this->file ) ) : ( ! empty( $this->file_url ) ? esc_html( $this->file_url ) : '' );
		
		$maps = import_export_get_maps_list( 'apply', $doing_edit ? $this->options['applys'] : false );

		ob_start();
		?>
		<h2><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></h2>
		<h4 data-wpgmza-import-source='<?php echo $source; ?>'><?php echo $source; ?></h4>
		<p>
		<h2><?php esc_html_e( 'CSV Data', 'wp-google-maps' ); ?></h2>
		</p>
		<p>
		<?php
		switch ( $this->import_type ) {
			case 'map':
				esc_html_e( 'Map data found.', 'wp-google-maps' );
				break;
			case 'marker':
				esc_html_e( 'Marker data found.', 'wp-google-maps' );
				break;
			case 'circle':
				esc_html_e( 'Circle data found.', 'wp-google-maps' );
				break;
			case 'polygon':
				esc_html_e( 'Polygon data found.', 'wp-google-maps' );
				break;
			case 'polyline':
				esc_html_e( 'Polyline data found.', 'wp-google-maps' );
				break;
			case 'rectangle':
				esc_html_e( 'Rectangle data found.', 'wp-google-maps' );
				break;
			case 'dataset':
				esc_html_e( 'Heatmap data found.', 'wp-google-maps' );
				break;
		} ?>
		</p>
		<?php
		if($this->import_type != 'map')
		{
			?>
			<div class="switch"><input id="geocode_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['geocode'] ? 'checked' : ''; ?>><label for="geocode_import"></label></div><?php esc_html_e( 'Find Addresses or Latitude and Longitude when missing', 'wp-google-maps' ); ?><br>
			<span style="font-style:italic;"><?php esc_html_e( 'Requires Google Maps Geocoding API to be enabled.', 'wp-google-maps' ); ?></span> <a href="https://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/" target="_blank">[?]</a><br>
			<br>
			
			<div class="switch">
				<input id="keep_map_id" name="keep_map_id" class="cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['keep_map_id'] ? 'checked' : ''; ?>/>
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
		else
		{
			?>
			<div class="switch"><input id="replace_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['replace'] ? 'checked' : ''; ?>><label for="replace_import"></label></div><?php esc_html_e( 'Replace map data', 'wp-google-maps' ); ?>
			<?php
		}
		?>
		
		<br>
		
		<div class="switch"><input id="replace_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['replace'] ? 'checked' : ''; ?>><label for="replace_import"></label></div><?php esc_html_e( 'Replace map data', 'wp-google-maps' ); ?>
		
		<div class="delete-after-import">
			<div class="switch"><input id="delete_import" class="csv_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit ? 'disabled' : ''; ?>><label for="delete_import"></label></div><?php esc_html_e( 'Delete import file after import', 'wp-google-maps' ); ?>
		</div>
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
							<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $_POST['import_url'] . "'," : '' ); ?>

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
								<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $_POST['import_url'] . "'," : '' ); ?>

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
	 * Import the CSV file.
	 */
	public function import() {

		
	
		if ( !empty($this->options['replace']) ) {

			$this->clear_map_data();

		}

		if($this->import_type != 'map')
			$this->create_map();

		$import_method = "import_{$this->import_type}s";
		$this->{$import_method}();

		$this->onImportComplete();
	}

	/**
	 * Clear map data from applys.
	 */
	protected function clear_map_data() {

		if ( empty( $this->options['applys'] ) && $this->import_type != "map" ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		global $wpgmza_tblname_maps;
		global $wpgmza_tblname_circles;
		global $wpgmza_tblname_datasets;
		global $wpgmza_tblname_polylines;
		global $wpgmza_tblname_poly;
		global $wpgmza_tblname_rectangles;

		$applys_in = implode( ',', $this->options['applys'] );

		switch ( $this->import_type ) {

			case 'marker':
				$wpdb->query( "DELETE a, b FROM `$wpgmza_tblname` a LEFT JOIN `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS` b ON b.object_id = a.id WHERE a.map_id IN ($applys_in)" );
				break;

			case 'circle':
				$wpdb->query( "DELETE FROM `$wpgmza_tblname_circles` WHERE `map_id` IN ($applys_in)" );
				break;

			case 'polygon':
				$wpdb->query( "DELETE FROM `$wpgmza_tblname_poly` WHERE `map_id` IN ($applys_in)" );
				break;

			case 'polyline':
				$wpdb->query( "DELETE FROM `$wpgmza_tblname_polylines` WHERE `map_id` IN ($applys_in)" );
				break;

			case 'rectangle':
				$wpdb->query( "DELETE FROM `$wpgmza_tblname_rectangles` WHERE `map_id` IN ($applys_in)" );
				break;

			case 'dataset':
				$wpdb->query( "DELETE FROM `$wpgmza_tblname_datasets` WHERE `map_id` IN ($applys_in)" );
				break;
				
			case "map":
				$wpdb->query("DELETE FROM `$wpgmza_tblname_maps`");
				break;

		}
	}

	/**
	 * Create map.
	 */
	protected function create_map() {
		
		if ( !empty($this->options['applys']) ) {

			return;

		}
		
		
		if( !empty($this->options['keep_map_id']) )
			return;

		global $wpdb;
		global $wpgmza_tblname_maps;

		$map = array();
		$map['map_title'] = isset( $this->file ) ? basename( $this->file ) : ( isset( $this->file_url ) ? basename( $this->file_url ) : __( 'New CSV Map Import', 'wp-google-maps' ) );
		$map['map_start_lat'] = 0;
		$map['map_start_lng'] = 0;
		
		$success = $wpdb->insert( $wpgmza_tblname_maps, array(
			'map_title'            => isset( $map['map_title'] ) ? $map['map_title'] : 'New Imported Map',
			'map_width'            => isset( $map['map_width'] ) ? $map['map_width'] : 100,
			'map_height'           => isset( $map['map_height'] ) ? $map['map_height'] : 400,
			'map_start_lat'        => isset( $map['map_start_lat'] ) ? $map['map_start_lat'] : '',
			'map_start_lng'        => isset( $map['map_start_lng'] ) ? $map['map_start_lng'] : '',
			'map_start_location'   => isset( $map['map_start_location'] ) ? $map['map_start_location'] : '',
			'map_start_zoom'       => isset( $map['map_start_zoom'] ) ? $map['map_start_zoom'] : 15,
			'default_marker'       => isset( $map['default_marker'] ) ? $map['default_marker'] : 0,
			'type'                 => isset( $map['type'] ) ? $map['type'] : 3,
			'alignment'            => isset( $map['alignment'] ) ? $map['alignment'] : 1,
			'directions_enabled'   => isset( $map['directions_enabled'] ) ? $map['directions_enabled'] : 1,
			'styling_enabled'      => isset( $map['styling_enabled'] ) ? $map['styling_enabled'] : 0,
			'styling_json'         => isset( $map['styling_json'] ) ? $map['styling_json'] : '',
			'active'               => isset( $map['active'] ) ? $map['active'] : 0,
			'kml'                  => isset( $map['kml'] ) ? $map['kml'] : '',
			'bicycle'              => isset( $map['bicycle'] ) ? $map['bicycle'] : 2,
			'traffic'              => isset( $map['traffic'] ) ? $map['traffic'] : 2,
			'dbox'                 => isset( $map['dbox'] ) ? $map['dbox'] : 4,
			'dbox_width'           => isset( $map['dbox_width'] ) ? $map['dbox_width'] : 100,
			'listmarkers'          => isset( $map['listmarkers'] ) ? $map['listmarkers'] : 0,
			'listmarkers_advanced' => isset( $map['listmarkers_advanced'] ) ? $map['listmarkers_advanced'] : 0,
			'filterbycat'          => isset( $map['filterbycat'] ) ? $map['filterbycat'] : 0,
			'ugm_enabled'          => isset( $map['ugm_enabled'] ) ? $map['ugm_enabled'] : 0,
			'ugm_category_enabled' => isset( $map['ugm_category_enabled'] ) ? $map['ugm_category_enabled'] : 0,
			'fusion'               => isset( $map['fusion'] ) ? $map['fusion'] : '',
			'map_width_type'       => isset( $map['map_width_type'] ) ? $map['map_width_type'] : '\%',
			'map_height_type'      => isset( $map['map_height_type'] ) ? $map['map_height_type'] : 'px',
			'mass_marker_support'  => isset( $map['mass_marker_support'] ) ? $map['mass_marker_support'] : 0,
			'ugm_access'           => isset( $map['ugm_access'] ) ? $map['ugm_access'] : 0,
			'order_markers_by'     => isset( $map['order_markers_by'] ) ? $map['order_markers_by'] : 2,
			'order_markers_choice' => isset( $map['order_markers_choice'] ) ? $map['order_markers_choice'] : 1,
			'show_user_location'   => isset( $map['show_user_location'] ) ? $map['show_user_location'] : 1,
			'default_to'           => isset( $map['default_to'] ) ? $map['default_to'] : '',
			'other_settings'       => isset( $map['other_settings'] ) ? $map['other_settings'] : '',
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
		) );

		if ( false !== $success ) {

			$this->options['applys'] = array(
				$wpdb->insert_id
			);

		}
		else
		{
			wp_send_json_error("Failed to create new map");
			exit;
		}
	}
	
	protected function import_maps() {
		
		global $wpdb;
		global $wpgmza_tblname_maps;
		
		if('map' !== $this->import_type)
			return;
		
		$header_count = count( $this->header_map );
		$header_map = $this->header_map;
		
		$columns = array_keys($header_map);
		$columns_imploded = implode(', ', $columns);
		
		$placeholders = array_fill(0, $header_count, '%s');
		$placeholders_imploded = implode(', ', $placeholders);
		
		foreach ( $this->file_data as $row_string ) {
			$row = str_getcsv($row_string);
			
			$qstr = "INSERT INTO $wpgmza_tblname_maps ($columns_imploded) VALUES ($placeholders_imploded)";
			$stmt = $wpdb->prepare($qstr, $row);
			$wpdb->query($stmt);
			
			$this->bail_if_near_time_limit();
		}
	}
	
	/**
	 * Import markers.
	 */
	protected function import_markers() {
		
		if ( 'marker' !== $this->import_type ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname;
		global $wpgmza_tblname_maps;

		$header_count = count( $this->header_map );
		
		$this->header_map['address'] = isset( $this->header_map['address'] ) ? $this->header_map['address'] : $header_count;
		$this->header_map['lat']     = isset( $this->header_map['lat'] ) ? $this->header_map['lat'] : $header_count + 1;
		$this->header_map['lng']     = isset( $this->header_map['lng'] ) ? $this->header_map['lng'] : $header_count + 2;

		$row_index = 1;
		$total_rows = count( $this->file_data );

		if($this->options['replace'] && !empty($this->options['keep_map_id']))
		{
			// Gather map IDs
			$mapIDs = array();
			
			foreach ( $this->file_data as $marker_csv_string )
			{
				$marker = str_getcsv( $marker_csv_string );
				
				if(empty($marker[ $this->header_map['map_id'] ]))
					continue;
				
				$mapIDs[$marker[ $this->header_map['map_id'] ]] = true;
			}
			
			$mapIDs = array_keys($mapIDs);
			
			if(!empty($mapIDs))
			{
				$imploded = implode(',', array_map("intval", $mapIDs));
				$qstr = "DELETE FROM $wpgmza_tblname WHERE map_id IN ($imploded)";
				$wpdb->query($qstr);
			}
		}
		
		foreach ( $this->file_data as $marker_csv_string ) {
			
			$this->set_progress( $row_index / $total_rows );
			$row_index++;

			$marker = str_getcsv( $marker_csv_string );

			if ( $this->options['geocode'] ) {
				
				if(empty( $marker[ $this->header_map['address'] ] ) &&
					empty( $marker[ $this->header_map['lat'] ] ) &&
					empty( $marker[ $this->header_map['lng'] ] ))
				{
					$this->failure( 'no_address_or_coordinates', $row_index );
					
					continue;
				}
				
				if ( ! empty( $marker[ $this->header_map['address'] ] ) &&
				     ( empty( $marker[ $this->header_map['lat'] ] ) || empty( $marker[ $this->header_map['lng'] ] ) ) ) {

					$address = $marker[ $this->header_map['address'] ];

					if(empty($address))
					{
						$this->failure('no_address', $row_index);
						continue;
					}

					$latlng = $this->geocode($address);

					if ( $latlng == false ) {

						if ( ! empty( $this->geocode_response->status ) && ! empty( $this->geocode_response->error_message ) ) {

							$status = $this->geocode_response->status;
							$error_message = $this->geocode_response->error_message;

							if ( ! isset( $this->failure_message_by_handle[ $status ] ) ) {

								$this->failure_message_by_handle[ $status ] = rtrim( $error_message, ' .' );

							}

							$this->failure( $status, $row_index );

						} else {

							$this->failure( 'geocode_failed', $row_index );

						}

						continue;

					}

					$marker[ $this->header_map['lat'] ] = isset( $latlng[0] ) ? $latlng[0] : 0;
					$marker[ $this->header_map['lng'] ] = isset( $latlng[1] ) ? $latlng[1] : 0;

				}
				
				if ( empty( $marker[ $this->header_map['address'] ] ) &&
				     ! empty( $marker[ $this->header_map['lat'] ] ) && ! empty( $marker[ $this->header_map['lng'] ] ) ) {

					$marker[ $this->header_map['address'] ] = $this->geocode( "{$marker[ $this->header_map['lat'] ]},{$marker[ $this->header_map['lng'] ]}", 'latlng' );

				}
			}
			
			// Check validity
			$lat = $marker[ $this->header_map['lat'] ];
			$lng = $marker[ $this->header_map['lng'] ];
			
			if(!is_numeric($lat))
			{
				if(!$this->options['geocode'] && empty($lat))
				{
					$status = 'empty_latitude';
					
					$this->failure_message_by_handle[ $status ] = __('No latitude supplied, "Find latitude" not selected. Marker will have zero latitude', 'wp-google-maps');
					$this->failure($status, $row_index);
				}
				else
				{
					$status = 'invalid_latitude';
					
					$this->failure_message_by_handle[ $status ] = __('Invalid latitude, supplied value is not numeric', 'wp-google-maps');
					$this->failure($status, $row_index);
				}
			}
			
			if(!is_numeric($lng))
			{
				if(!$this->options['geocode'] && empty($lng))
				{
					$status = 'empty_longitude';
					
					$this->failure_message_by_handle[ $status ] = __('No longitude supplied, "Find longitude" not selected. Marker will have zero longitude', 'wp-google-maps');
					$this->failure($status, $row_index);
				}
				else
				{					
					$status = 'invalid_longitude';
					
					$this->failure_message_by_handle[ $status ] = __('Invalid longitude, supplied value is not numeric', 'wp-google-maps');
					$this->failure($status, $row_index);
				}
			}
			
			if(!is_numeric($lat) || !is_numeric($lng))
				continue;

			if ( empty( $marker[ $this->header_map['lat'] ] ) ) {

				$marker[ $this->header_map['lat'] ] = 0;

			}

			if ( empty( $marker[ $this->header_map['lng'] ] ) ) {

				$marker[ $this->header_map['lng'] ] = 0;

			}

			if ( empty( $marker[ $this->header_map['address'] ] ) ) {

				$marker[ $this->header_map['address'] ] = "{$marker[ $this->header_map['lat'] ]},{$marker[ $this->header_map['lng'] ]}";

			}
			
			if(!empty($this->options['keep_map_id']))
				$this->options['applys'] = array( $marker[$this->header_map['map_id']] );
		
			foreach ( $this->options['applys'] as $map_id ) {
				
				$columns = array(
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
					'sticky',
					'other_data'
				);
				
				$fields = array();
				
				foreach($columns as $column)
				{
					$mapped = (isset($this->header_map[$column]) ? $this->header_map[$column] : null);
					$param = (empty($this->header_map[$mapped]) && isset($marker[$mapped]) ? $marker[$mapped] : $default);
					
					if(function_exists('iconv') && function_exists('mb_detect_encoding') && function_exists('mb_detect_order'))
						$param = iconv(mb_detect_encoding($param, mb_detect_order(), true), "UTF-8", $param);
					
					// NB: Removed as raw HTML entites were causing confusion with users, perhaps we should add an option for this
					// $param = DOMDocument::convertUTF8ToHTMLEntities($param);
					
					$fields[$column] = $param;
				}
				
				if(empty($this->options['keep_map_id']))
					$fields['map_id'] = $map_id;
				
				$instance = Marker::createInstance();
				$instance->set($fields);
				
				foreach ($marker as $index => $marker_data)
				{
					if(!empty($this->custom_field_map[$index]))
						$instance->customFields->{$this->custom_field_map[$index]} = $marker_data;
				}
				
			}
			
			$this->bail_if_near_time_limit();
			
		} // End foreach().
	}

	/**
	 * Import circles.
	 */
	protected function import_circles() {

		if ( 'circle' !== $this->import_type ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_circles;

		foreach ( $this->file_data as $circle_csv_string ) {

			$circle = str_getcsv( $circle_csv_string );

			foreach ( $this->options['applys'] as $map_id ) {

				$success = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpgmza_tblname_circles`
				(`map_id`,`name`,`center`,`radius`,`color`,`opacity`)
				VALUES (%d,%s,POINT(%f,%f),%f,%s,%f)",
					$map_id,
					isset( $this->header_map['name'], $circle[ $this->header_map['name'] ] ) ? $circle[ $this->header_map['name'] ] : __( 'New Imported Circle', 'wp-google-maps' ),
					isset( $this->header_map['center_x'], $circle[ $this->header_map['center_x'] ] ) ? $circle[ $this->header_map['center_x'] ] : 0,
					isset( $this->header_map['center_y'], $circle[ $this->header_map['center_y'] ] ) ? $circle[ $this->header_map['center_y'] ] : 0,
					isset( $this->header_map['radius'], $circle[ $this->header_map['radius'] ] ) ? $circle[ $this->header_map['radius'] ] : 20,
					isset( $this->header_map['color'], $circle[ $this->header_map['color'] ] ) ? $circle[ $this->header_map['color'] ] : '#ff0000',
					isset( $this->header_map['opacity'], $circle[ $this->header_map['opacity'] ] ) ? $circle[ $this->header_map['opacity'] ] : 0.6
				) );

			}
		}
	}

	/**
	 * Import polygons.
	 */
	protected function import_polygons() {

		if ( 'polygon' !== $this->import_type ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_poly;

		foreach ( $this->file_data as $polygon_csv_string ) {

			$polygon = str_getcsv( $polygon_csv_string );

			foreach ( $this->options['applys'] as $map_id ) {

				$success = $wpdb->insert( $wpgmza_tblname_poly, array(
					'map_id'		=> $map_id,
					'polydata'      => isset( $this->header_map['polydata'], $polygon[ $this->header_map['polydata'] ] ) ? $polygon[ $this->header_map['polydata'] ] : '',
					'innerpolydata' => isset( $this->header_map['innerpolydata'], $polygon[ $this->header_map['innerpolydata'] ] ) ? $polygon[ $this->header_map['innerpolydata'] ] : '',
					'linecolor'     => isset( $this->header_map['linecolor'], $polygon[ $this->header_map['linecolor'] ] ) ? $polygon[ $this->header_map['linecolor'] ] : '000000',
					'lineopacity'   => isset( $this->header_map['lineopacity'], $polygon[ $this->header_map['lineopacity'] ] ) ? $polygon[ $this->header_map['lineopacity'] ] : 0.5,
					'fillcolor'     => isset( $this->header_map['fillcolor'], $polygon[ $this->header_map['fillcolor'] ] ) ? $polygon[ $this->header_map['fillcolor'] ] : '66FF00',
					'opacity'       => isset( $this->header_map['opacity'], $polygon[ $this->header_map['opacity'] ] ) ? $polygon[ $this->header_map['opacity'] ] : 0.5,
					'title'         => isset( $this->header_map['title'], $polygon[ $this->header_map['title'] ] ) ? $polygon[ $this->header_map['title'] ] : '',
					'link'          => isset( $this->header_map['link'], $polygon[ $this->header_map['link'] ] ) ? $polygon[ $this->header_map['link'] ] : '',
					'ohfillcolor'   => isset( $this->header_map['ohfillcolor'], $polygon[ $this->header_map['ohfillcolor'] ] ) ? $polygon[ $this->header_map['ohfillcolor'] ] : '57FF78',
					'ohlinecolor'   => isset( $this->header_map['ohlinecolor'], $polygon[ $this->header_map['ohlinecolor'] ] ) ? $polygon[ $this->header_map['ohlinecolor'] ] : '737373',
					'ohopacity'     => isset( $this->header_map['ohopacity'], $polygon[ $this->header_map['ohopacity'] ] ) ? $polygon[ $this->header_map['ohopacity'] ] : (isset( $this->header_map['opacity'], $polygon[ $this->header_map['opacity'] ] ) ? $polygon[ $this->header_map['opacity'] ] : 0.7),
					'polyname'      => isset( $this->header_map['polyname'], $polygon[ $this->header_map['polyname'] ] ) ? $polygon[ $this->header_map['polyname'] ] : __( 'New Imported Polygon', 'wp-google-maps' ),
				), array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%f',
					'%s',
					'%f',
					'%s',
					'%s',
					'%s',
					'%s',
					'%f',
					'%s',
				) );

			}
		} // End foreach().
	}

	/**
	 * Import polylines.
	 */
	protected function import_polylines() {

		if ( 'polyline' !== $this->import_type ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_polylines;

		foreach ( $this->file_data as $polyline_csv_string ) {

			$polyline = str_getcsv( $polyline_csv_string );

			foreach ( $this->options['applys'] as $map_id ) {

				$success = $wpdb->insert( $wpgmza_tblname_polylines, array(
					'map_id'		=> $map_id,
					'polydata'      => isset( $this->header_map['polydata'], $polyline[ $this->header_map['polydata'] ] ) ? $polyline[ $this->header_map['polydata'] ] : '',
					'linecolor'     => isset( $this->header_map['linecolor'], $polyline[ $this->header_map['linecolor'] ] ) ? $polyline[ $this->header_map['linecolor'] ] : '000000',
					'linethickness' => isset( $this->header_map['linethickness'], $polyline[ $this->header_map['linethickness'] ] ) ? $polyline[ $this->header_map['linethickness'] ] : 4,
					'opacity'       => isset( $this->header_map['opacity'], $polyline[ $this->header_map['opacity'] ] ) ? $polyline[ $this->header_map['opacity'] ] : 0.8,
					'polyname'      => isset( $this->header_map['polyname'], $polyline[ $this->header_map['polyname'] ] ) ? $polyline[ $this->header_map['polyname'] ] : __( 'New Imported Polyline', 'wp-google-maps' ),
				), array(
					'%d',
					'%s',
					'%s',
					'%f',
					'%f',
					'%s',
				) );

			}
		}
	}

	/**
	 * Import rectangles.
	 */
	protected function import_rectangles() {

		if ( 'rectangle' !== $this->import_type ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_rectangles;

		foreach ( $this->file_data as $rectangle_csv_string ) {

			$rectangle = str_getcsv( $rectangle_csv_string );

			foreach ( $this->options['applys'] as $map_id ) {

				$success = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpgmza_tblname_rectangles`
				(`map_id`,`name`,`cornerA`,`cornerB`,`color`,`opacity`)
				VALUES (%d,%s,POINT(%f,%f),POINT(%f,%f),%s,%f)",
					$map_id,
					isset( $this->header_map['name'], $rectangle[ $this->header_map['name'] ] ) ? $rectangle[ $this->header_map['name'] ] : __( 'New Imported Rectangle', 'wp-google-maps' ),
					isset( $this->header_map['corner_ax'], $rectangle[ $this->header_map['corner_ax'] ] ) ? $rectangle[ $this->header_map['corner_ax'] ] : 0,
					isset( $this->header_map['corner_ay'], $rectangle[ $this->header_map['corner_ay'] ] ) ? $rectangle[ $this->header_map['corner_ay'] ] : 0,
					isset( $this->header_map['corner_bx'], $rectangle[ $this->header_map['corner_bx'] ] ) ? $rectangle[ $this->header_map['corner_bx'] ] : 0,
					isset( $this->header_map['corner_by'], $rectangle[ $this->header_map['corner_by'] ] ) ? $rectangle[ $this->header_map['corner_by'] ] : 0,
					isset( $this->header_map['color'], $rectangle[ $this->header_map['color'] ] ) ? $rectangle[ $this->header_map['color'] ] : '#ff0000',
					isset( $this->header_map['opacity'], $rectangle[ $this->header_map['opacity'] ] ) ? $rectangle[ $this->header_map['opacity'] ] : 0.6
				) );

			}
		}
	}

	/**
	 * Import datasets.
	 */
	protected function import_datasets() {

		if ( 'dataset' !== $this->import_type ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_datasets;

		foreach ( $this->file_data as $dataset_csv_string ) {

			$dataset = str_getcsv( $dataset_csv_string );

			foreach ( $this->options['applys'] as $map_id ) {

				$success = $wpdb->insert( $wpgmza_tblname_datasets, array(
					'map_id'		=> $map_id,
					'type'         => isset( $this->header_map['type'], $dataset[ $this->header_map['type'] ] ) ? $dataset[ $this->header_map['type'] ] : 0,
					'dataset_name' => isset( $this->header_map['dataset_name'], $dataset[ $this->header_map['dataset_name'] ] ) ? $dataset[ $this->header_map['dataset_name'] ] : __( 'New Imported Dataset', 'wp-google-maps' ),
					'dataset'      => isset( $this->header_map['dataset'], $dataset[ $this->header_map['dataset'] ] ) ? $dataset[ $this->header_map['dataset'] ] : '',
					'options'      => isset( $this->header_map['options'], $dataset[ $this->header_map['options'] ] ) ? $dataset[ $this->header_map['options'] ] : '',
				), array(
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
				) );

			}
		}
	}
}
