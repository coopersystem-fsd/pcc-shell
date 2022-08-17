<?php
/**
 * WP Google Maps Pro Import / Export API: ImportGPX class
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;
 
/**
 * GPX importer for WP Google Maps Pro
 *
 * This handles importing of files.
 *
 * @since 7.0.0
 */
class ImportGPX extends Import {

	/**
	 * Check options.
	 *
	 * @throws \Exception On malformed options.
	 */
	protected function check_options() {

		if ( ! is_array( $this->options ) ) {

			throw new \Exception( __( 'Error: Malformed options.', 'wp-google-maps' ) );

		}

		$this->options['tracks']    = isset( $this->options['tracks'] ) ? true : false;
		$this->options['waypoints'] = isset( $this->options['waypoints'] ) ? true : false;
		$this->options['geocode']   = isset( $this->options['geocode'] ) ? true : false;
		$this->options['apply']     = isset( $this->options['apply'] ) ? true : false;
		$this->options['replace']   = isset( $this->options['replace'] ) ? true : false;
		$this->options['applys']    = isset( $this->options['applys'] ) ? explode( ',', $this->options['applys'] ) : array();

		if ( $this->options['apply'] && empty( $this->options['applys'] ) ) {

			$this->options['applys'] = import_export_get_maps_list( 'ids' );

		}

		$this->options['applys'] = $this->check_ids( $this->options['applys'] );

		$this->options['tracks_resolution'] = isset( $this->options['tracks_resolution'] ) ? absint( $this->options['tracks_resolution'] ) : 10;
		$this->options['tracks_resolution'] = max( 1, min( 10, $this->options['tracks_resolution'] ) );

		if ( ! isset( $_POST['schedule_id'] ) ) {

			$this->options['tracks_resolution'] = 0.01 / ( pow( 10, $this->options['tracks_resolution'] / 2 ) );

		}

	}

	/**
	 * Parse file data.
	 *
	 * @throws \Exception When no data found in file data or xml parse error.
	 */
	protected function parse_file() {

		if ( ! empty( $this->file_data ) ) {

			$this->file_data = simplexml_load_string( $this->file_data );

			if ( false === $this->file_data ) {

				$error_message = __( 'Error: Unable to parse file.', 'wp-google-maps' );
				foreach ( libxml_get_errors() as $error ) {

					$error_message .= ' ' . $error->message;

				}

				throw new \Exception( $error_message );

			}

			if ( empty( $this->file_data->trk ) && empty( $this->file_data->wpt ) ) {

				throw new \Exception( __( 'Error: No tracks or way points found in GPX file.', 'wp-google-maps' ) );

			}
		} else {

			throw new \Exception( __( 'Error: Empty file data.', 'wp-google-maps' ) );

		}
	}

	/**
	 * Output admin import options.
	 *
	 * @return string Options html.
	 */
	public function admin_options() {

		$doing_edit = ! empty( $_POST['schedule_id'] ) ? true : false;

		$maps = import_export_get_maps_list( 'apply', $doing_edit ? $this->options['applys'] : false );

		ob_start();
		?>
		<h2><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></h2>
		<h4><?php echo ! empty( $this->file ) ? esc_html( basename( $this->file ) ) : ( ! empty( $this->file_url ) ? esc_html( $this->file_url ) : '' ); ?></h4>
		<p>
		<h2><?php esc_html_e( 'GPX Data', 'wp-google-maps' ); ?></h2>
		</p>
		<div class="switch"><input id="tracks_import" class="gpx_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data->trk ) ? 'disabled' : ( ! $doing_edit || $this->options['tracks'] ? 'checked' : '' ); ?>><label for="tracks_import"></label></div><?php esc_html_e( 'Tracks', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="waypoints_import" class="gpx_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data->wpt ) ? 'disabled' : ( ! $doing_edit || $this->options['waypoints'] ? 'checked' : '' ); ?>><label for="waypoints_import"></label></div><?php esc_html_e( 'Way Points', 'wp-google-maps' ); ?><br>
		<br>
		<?php esc_html_e( 'Track Resolution', 'wp-google-maps' ); ?><br>
		<input type="text" id="tracks_resolution" style="display:none;" value="<?php echo $doing_edit ? $this->options['tracks_resolution'] : '10'; ?>">
		<div id="slider-tracks-resolution" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" style="width:100%;max-width:250px;">
			<div class="ui-slider-range ui-widget-header ui-corner-all ui-slider-range-max" style="width:35%;"></div>
			<span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left:65%;"></span>
		</div>
		<br>
		<div class="switch"><input id="geocode_import" class="gpx_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data->wpt ) ? 'disabled' : ( $doing_edit && $this->options['geocode'] ? 'checked' : '' ); ?>><label for="geocode_import"></label></div><?php esc_html_e( 'Find Addresses for Way Points', 'wp-google-maps' ); ?><br>
		<span style="font-style:italic;"><?php esc_html_e( 'Requires Google Maps Geocoding API to be enabled.', 'wp-google-maps' ); ?></span> <a href="https://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/" target="_blank">[?]</a><br>
		<br>
		<div class="switch"><input id="apply_import" class="gpx_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $maps ) ? 'disabled' : ( $doing_edit && $this->options['apply'] ? 'checked' : '' ); ?>><label for="apply_import"></label></div><?php esc_html_e( 'Apply import data to', 'wp-google-maps' ); ?>
		<br>
		<div id="maps_apply_import" style="<?php echo empty( $maps ) ? 'display:none;' : ( $doing_edit && $this->options['apply'] ? '' : 'display:none;' ); ?>width:100%;">
			<?php if ( empty( $maps ) ) { ?>
				<br><?php esc_html_e( 'No maps available for import to.', 'wp-google-maps' ); ?>
			<?php } else { ?>
				<br>
				<div class="switch"><input id="replace_import" class="gpx_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['replace'] ? 'checked' : ''; ?>><label for="replace_import"></label></div><?php esc_html_e( 'Replace map data', 'wp-google-maps' ); ?>
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
					<?php echo $maps ?>
					</tbody>
				</table>
				<button id="maps_apply_select_all" class="wpgmza_general_btn"><?php esc_html_e( 'Select All', 'wp-google-maps' ); ?></button> <button id='maps_apply_select_none' class='wpgmza_general_btn'><?php esc_html_e( 'Select None', 'wp-google-maps' ); ?></button><br><br>
			<?php } ?>
		</div>
		<br>
		<div class="delete-after-import">
			<div class="switch"><input id="delete_import" class="gpx_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit ? 'disabled' : ''; ?>><label for="delete_import"></label></div><?php esc_html_e( 'Delete import file after import', 'wp-google-maps' ); ?>
		</div>
		<br><br>
		<div id="import-schedule-gpx-options" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>>
			<h2><?php esc_html_e( 'Scheduling Options', 'wp-google-maps' ); ?></h2>
			<?php esc_html_e( 'Start Date', 'wp-google-maps' ); ?>
			<br>
			<input type="date" id="import-schedule-gpx-start" class="import-schedule-gpx-options" <?php echo $doing_edit ? 'value="' . $this->options['start'] . '"' : ''; ?>>
			<br><br>
			<?php esc_html_e( 'Interval', 'wp-google-maps' ); ?>
			<br>
			<select id="import-schedule-gpx-interval" class="import-schedule-gpx-options">
				<?php
				$schedule_intervals = wp_get_schedules();
				foreach ( $schedule_intervals as $schedule_interval_key => $schedule_interval ) { ?>
					<option value="<?php echo esc_attr( $schedule_interval_key ); ?>" <?php echo $doing_edit && $schedule_interval_key === $this->options['interval'] ? 'selected' : ''; ?>><?php echo esc_html( $schedule_interval['display'] ); ?></option>
				<?php } ?>
			</select>
			<br><br>
		</div>
		<p>
			<button id="import-gpx" class="wpgmza_general_btn" <?php if ( $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
			<button id="import-schedule-gpx" class="wpgmza_general_btn"><?php echo $doing_edit ? esc_html__( 'Update Schedule', 'wp-google-maps' ) : esc_html__( 'Schedule', 'wp-google-maps' ); ?></button>
			<button id="import-schedule-gpx-cancel" class="wpgmza_general_btn" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Cancel', 'wp-google-maps' ); ?></button>
		</p>
		<script>
			(function($) {
				$('#slider-tracks-resolution').slider({
					range: "max",
					min: 1,
					max: 10,
					value: $('#tracks_resolution').val(),
					slide: function (event, ui) {
						$('#tracks_resolution').val(ui.value);
					}
				});
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
				function gpx_get_import_options(){
					var import_options = {};
					var apply_check = $('.maps_apply:checked');
					var apply_ids = [];
					$('.gpx_data_import').each(function(){
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
					import_options['tracks_resolution'] = $('#tracks_resolution').val();
					return import_options;
				}
				$('#import-gpx').click(function(){
					var import_options = gpx_get_import_options();
					if (Object.keys(import_options).length < 1){
						return;
					}
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
				$('#import-schedule-gpx').click(function(){
					if ($('#import-gpx').is(':visible')) {
						$('#import-gpx,.delete-after-import').hide();
						$('#import-schedule-gpx-cancel').show();
						$('#import-schedule-gpx-options').slideDown(300);
					} else {
						var import_options = gpx_get_import_options();
						if (Object.keys(import_options).length < 1){
							return;
						}
						if ($('#import-schedule-gpx-start').val().length < 1){
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

								start: $('#import-schedule-gpx-start').val(),
								interval: $('#import-schedule-gpx-interval').val(),
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
				$('#import-schedule-gpx-cancel').click(function(){
					$('#import-gpx,.delete-after-import').show();
					$('#import-schedule-gpx-cancel').hide();
					$('#import-schedule-gpx-options').slideUp(300);
				});
			})(jQuery);
		<?php

		return ob_get_clean();

	}

	/**
	 * Import the GPX file.
	 */
	public function import() {

		if ( $this->options['replace'] ) {

			$this->clear_map_data();

		}

		$this->create_map();
		$this->import_tracks();
		$this->import_waypoints();

	}

	/**
	 * Clear map data from applys.
	 */
	function clear_map_data() {

		if ( empty( $this->options['applys'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname;
		global $wpgmza_tblname_polylines;

		$applys_in = implode( ',', $this->options['applys'] );

		if ( $this->options['waypoints'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname` WHERE `map_id` IN ($applys_in)" );

		}

		if ( $this->options['tracks'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname_polylines` WHERE `map_id` IN ($applys_in)" );

		}
	}

	/**
	 * Create map.
	 */
	protected function create_map() {

		if ( $this->options['apply'] ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_maps;

		$map = array();
		$map['map_title'] = isset( $this->file ) ? basename( $this->file ) : ( isset( $this->file_url ) ? basename( $this->file_url ) : __( 'New GPX Map Import', 'wp-google-maps' ) );
		$map['map_start_lat'] = 0;
		$map['map_start_lng'] = 0;

		if ( isset( $this->file_data->trk[0]->trkseg[0]->trkpt[0]['lat'], $this->file_data->trk[0]->trkseg[0]->trkpt[0]['lon'] ) ) {

			$map['map_start_lat'] = (string) $this->file_data->trk[0]->trkseg[0]->trkpt[0]['lat'];
			$map['map_start_lng'] = (string) $this->file_data->trk[0]->trkseg[0]->trkpt[0]['lon'];

		} elseif ( isset( $this->file_data->wpt[0]['lat'], $this->file_data->wpt[0]['lon'] ) ) {

			$map['map_start_lat'] = (string) $this->file_data->wpt[0]['lat'];
			$map['map_start_lng'] = (string) $this->file_data->wpt[0]['lon'];

		}

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
				$wpdb->insert_id,
			);

		}
	}

	/**
	 * Import tracks.
	 */
	protected function import_tracks() {

		if ( ! $this->options['tracks'] || ! isset( $this->file_data->trk ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_polylines;

		foreach ( $this->file_data->trk as $track ) {

			$segment_count = count( $track->trkseg );
			$cur_segment = 1;

			foreach ( $track->trkseg as $segment ) {

				$polyline = array();
				$polyline['polyname'] = ( isset( $track->name ) ? (string) $track->name : __( 'New Imported Polyline', 'wp-google-maps' ) ) .
				                        ( $segment_count > 1 ? ' ' . __( 'Segment', 'wp-google-maps' ) . " $cur_segment" : '' );
				$polyline['polydata'] = '';

				$trk_points = array();

				foreach ( $segment->trkpt as $point ) {

					if ( 0.0000001 >= $this->options['tracks_resolution'] ) {

						$polyline['polydata'] .= "({$point['lat']},{$point['lon']}),";

					} else {

						$trk_points[] = array( (float) $point['lat'], (float) $point['lon'] );

					}
				}

				if ( empty( $polyline['polydata'] ) && ! empty( $trk_points ) ) {

					$trk_points = $this->simplify_polyline( $trk_points, $this->options['tracks_resolution'] );

					foreach ( $trk_points as $point ) {

						$polyline['polydata'] .= "({$point[0]},{$point[1]}),";

					}
				}

				foreach ( $this->options['applys'] as $map_id ) {

					$success = $wpdb->insert( $wpgmza_tblname_polylines, array(
						'map_id'        => $map_id,
						'polydata'      => isset( $polyline['polydata'] ) ? $polyline['polydata'] : '',
						'linecolor'     => isset( $polyline['linecolor'] ) ? $polyline['linecolor'] : '000000',
						'linethickness' => isset( $polyline['linethickness'] ) ? $polyline['linethickness'] : 4,
						'opacity'       => isset( $polyline['opacity'] ) ? $polyline['opacity'] : 0.8,
						'polyname'      => isset( $polyline['polyname'] ) ? $polyline['polyname'] : __( 'New Imported Polyline', 'wp-google-maps' ),
					), array(
						'%d',
						'%s',
						'%s',
						'%f',
						'%f',
						'%s',
					) );

				}

				$cur_segment++;

			} // End foreach().
		} // End foreach().
	}

	/**
	 * Takes a polyline and finds a similar shape with fewer points.
	 *
	 * Uses Ramer-Douglas-Peucker algorithm.
	 *
	 * @param array $points  Point array.
	 * @param float $epsilon Distance threshold.
	 * @return array Point array.
	 */
	protected function simplify_polyline( $points, $epsilon ) {

		if ( $epsilon <= 0 || count( $points ) < 2 ) {

			return $points;

		}

		// Find the point with the maximum distance from line.
		$max_distance = 0;
		$index = 0;
		$total_points = count( $points );

		for ( $i = 1; $i < ( $total_points - 1 ); $i++ ) {

			if ( $points[ $total_points - 1 ][0] === $points[0][0] ) {

				$d = abs( $points[ $i ][0] - $points[ $total_points - 1 ][0] );

			} else {

				$slope = ( ( $points[ $total_points - 1 ][1] - $points[0][1] ) / ( $points[ $total_points - 1 ][0] - $points[0][0] ) );
				$pass_through_y = ( 0 - $points[0][0] ) * $slope + $points[0][1];
				$d = ( abs( ( $slope * $points[ $i ][0] ) - $points[ $i ][1] + $pass_through_y ) ) / ( sqrt( $slope * $slope + 1 ) );

			}

			if ( $d > $max_distance ) {

				$index = $i;
				$max_distance = $d;

			}
		}

		// If max distance is greater than epsilon, recursively simplify.
		if ( $max_distance >= $epsilon ) {

			// Recursive call.
			$rec_results_1 = $this->simplify_polyline( array_slice( $points, 0, $index + 1 ), $epsilon );
			$rec_results_2 = $this->simplify_polyline( array_slice( $points, $index, $total_points - $index ), $epsilon );

			// Build the result list.
			return array_merge( array_slice( $rec_results_1, 0, count( $rec_results_1 ) - 1 ),
				array_slice( $rec_results_2, 0, count( $rec_results_2 ) ) );

		}

		return array( $points[0], $points[ $total_points - 1 ] );

	}

	/**
	 * Import way points.
	 */
	protected function import_waypoints() {

		if ( ! $this->options['waypoints'] || ! isset( $this->file_data->wpt ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname;

		foreach ( $this->file_data->wpt as $way_point ) {

			$marker                = array();
			$marker['lat']         = (float) $way_point['lat'];
			$marker['lng']         = (float) $way_point['lon'];
			$marker['title']       = isset( $way_point->name ) ? (string) $way_point->name : __( 'New Imported Marker', 'wp-google-maps' );
			$marker['description'] = isset( $way_point->desc ) ? (string) $way_point->desc : '';
			$marker['link']        = isset( $way_point->link ) ? (string) $way_point->link : '';

			if ( $this->options['geocode'] ) {

				$marker['address'] = $this->geocode( "{$marker['lat']},{$marker['lng']}", 'latlng' );

			}

			$marker['address'] = empty( $marker['address'] ) ? "{$marker['lat']},{$marker['lng']}" : $marker['address'];

			foreach ( $this->options['applys'] as $map_id ) {

				$success = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpgmza_tblname`
				(`map_id`,`address`,`description`,`pic`,`link`,`icon`,`lat`,`lng`,`anim`,`title`,`infoopen`,`category`,`approved`,`retina`,`type`,`did`,`other_data`,`latlng`)
				VALUES (%d,%s,%s,%s,%s,%s,%f,%f,%d,%s,%d,%d,%d,%d,%d,%s,%s,POINT(%f,%f))",
					$map_id,
					isset( $marker['address'] ) ? $marker['address'] : '',
					isset( $marker['description'] ) ? $marker['description'] : '',
					isset( $marker['pic'] ) ? $marker['pic'] : '',
					isset( $marker['link'] ) ? $marker['link'] : '',
					isset( $marker['icon'] ) ? $marker['icon'] : '',
					isset( $marker['lat'] ) ? $marker['lat'] : 0,
					isset( $marker['lng'] ) ? $marker['lng'] : 0,
					isset( $marker['anim'] ) ? $marker['anim'] : 0,
					isset( $marker['title'] ) ? $marker['title'] : __( 'New Imported Marker', 'wp-google-maps' ),
					isset( $marker['infoopen'] ) ? $marker['infoopen'] : 0,
					isset( $marker['category'] ) ? ( $this->options['categories'] && isset( $this->cat_id_map[ absint( $marker['category'] ) ] ) ? $this->cat_id_map[ absint( $marker['category'] ) ] : 0 ) : 0,
					isset( $marker['approved'] ) ? $marker['approved'] : 1,
					isset( $marker['retina'] ) ? $marker['retina'] : 0,
					isset( $marker['type'] ) ? $marker['type'] : 0,
					isset( $marker['did'] ) ? $marker['did'] : '',
					isset( $marker['other_data'] ) ? $marker['other_data'] : '',
					isset( $marker['lat'] ) ? $marker['lat'] : 0,
					isset( $marker['lng'] ) ? $marker['lng'] : 0
				) );

			}
		} // End foreach().
	}
}
