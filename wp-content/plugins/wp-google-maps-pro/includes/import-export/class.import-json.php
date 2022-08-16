<?php
/**
 * WP Google Maps Pro Import / Export API: ImportJSON class
 *
 * @package WPGMapsPro\ImportExport
 * @since 7.0.0
 */

namespace WPGMZA;
 
/**
 * JSON importer for WP Google Maps Pro
 *
 * This handles importing of files.
 *
 * @since 7.0.0
 */
class ImportJSON extends Import {

	/**
	 * Map id map.
	 *
	 * Maps new imported ids to old importing ids.
	 *
	 * @var array $map_id_map Key is old importing id, value is new imported id.
	 */
	protected $map_id_map = array();

	/**
	 * Category id map.
	 *
	 * Maps new imported ids to old importing ids.
	 *
	 * @var array $cat_id_map Key is old importing id, value is new imported id.
	 */
	protected $cat_id_map = array();

	/**
	 * Categories mapped to all maps.
	 *
	 * @var array $cat_all_maps Key is old importing id, value is new imported id.
	 */
	protected $cat_all_maps = array();

	/**
	 * Custom field id map.
	 *
	 * Maps new imported ids to old importing ids.
	 *
	 * @var array $field_id_map Key is old importing id, value is new imported id.
	 */
	protected $field_id_map = array();

	/**
	 * Marker id map.
	 *
	 * Maps new imported ids to old importing ids.
	 *
	 * @var array $marker_id_map Key is old importing id, value is new imported id.
	 */
	protected $marker_id_map = array();

	/**
	 * Check options.
	 *
	 * @throws \Exception On malformed options.
	 */
	protected function check_options() {

		if ( ! is_array( $this->options ) ) {

			throw new \Exception( __( 'Error: Malformed options.', 'wp-google-maps' ) );

		}

		$this->options['maps']         = isset( $this->options['maps'] ) ? explode( ',', $this->options['maps'] ) : array();
		$this->options['maps']         = $this->check_ids( $this->options['maps'] );
		$this->options['categories']   = isset( $this->options['categories'] ) ? true : false;
		$this->options['customfields'] = isset( $this->options['customfields'] ) ? true : false;
		$this->options['markers']      = isset( $this->options['markers'] ) ? true : false;
		$this->options['circles']      = isset( $this->options['circles'] ) ? true : false;
		$this->options['polygons']     = isset( $this->options['polygons'] ) ? true : false;
		$this->options['polylines']    = isset( $this->options['polylines'] ) ? true : false;
		$this->options['rectangles']   = isset( $this->options['rectangles'] ) ? true : false;
		$this->options['datasets']     = isset( $this->options['datasets'] ) ? true : false;
		$this->options['apply']        = isset( $this->options['apply'] ) ? true : false;
		$this->options['replace']      = isset( $this->options['replace'] ) ? true : false;
		$this->options['applys']       = isset( $this->options['applys'] ) ? explode( ',', $this->options['applys'] ) : array();

		if ( $this->options['apply'] && empty( $this->options['applys'] ) ) {

			$this->options['applys'] = import_export_get_maps_list( 'ids' );

		}

		$this->options['applys'] = $this->check_ids( $this->options['applys'] );

	}

	/**
	 * Parse file data.
	 *
	 * @throws \Exception When no maps found in file data, or no data to parse.
	 */
	protected function parse_file() {
		
		$this->log("Attempting to parse JSON");

		if ( ! empty( $this->file_data ) ) {
			
			$this->file_data = json_decode( $this->file_data, true );
			
			if( $this->file_data === null )
			{
				$this->log("Failed to parse JSON");
				$this->log(json_last_error_msg());
			
				throw new \Exception( __('Error parsing JSON: ', 'wp-google-maps') . json_last_error_msg() );
			}

			if(!empty($this->file_data['type']) && $this->file_data['type'] === "FeatureCollection"){
				throw new \Exception( __('It looks like you may be trying to import a GeoJSON file, please change the file extension from .json to .geojson', 'wp-google-maps') );
			}

			if ( empty( $this->file_data['maps'] ) )
			{
				$this->log("No maps detected");
				
				throw new \Exception( __( 'Error: File contains no maps.', 'wp-google-maps' ) );
			}

		} else {
			
			$this->log("The file is empty");

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
		<div style="margin:0 0 1em 0;width:100%;">
			<?php if ( empty( $this->file_data['maps'] ) ) { ?>
				<br><?php esc_html_e( 'No maps available for import.', 'wp-google-maps' ); ?>
				<?php
				echo '</div>';
				return;
				?>
			<?php } else { ?>
				<table class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
					<thead style="display:block;border-bottom:1px solid #e1e1e1;">
					<tr style="display:block;width:100%;">
						<th style="width:2.2em;border:none;"></th>
						<th style="border:none;"><?php esc_html_e( 'Title', 'wp-google-maps' ); ?></th>
					</tr>
					</thead>
					<tbody style="display:block;max-height:370px;overflow-y:scroll;">
					<?php
					foreach ( $this->file_data['maps'] as $map ) {
						?><tr style="display:block;width:100%;"><td style="width:2.2em;"><div class="switch"><input id="maps_import_<?php echo esc_attr( $map['id'] ); ?>" type="checkbox" value="<?php echo esc_attr( $map['id'] ); ?>" class="maps_import cmn-toggle cmn-toggle-round-flat" <?php echo empty( $this->options['maps'] ) || in_array( $map['id'], $this->options['maps'] ) ? 'checked' : ''; ?>><label for="maps_import_<?php echo esc_attr( $map['id'] ); ?>"></label></div></td><td><?php echo esc_html( $map['map_title'] ); ?></td></tr><?php
					}
					?>
					</tbody>
				</table>
				<button id="maps_import_select_all" class="wpgmza_general_btn"><?php esc_html_e( 'Select All', 'wp-google-maps' ); ?></button> <button id='maps_import_select_none' class='wpgmza_general_btn'><?php esc_html_e( 'Select None', 'wp-google-maps' ); ?></button><br><br>
			<?php } ?>
		</div>
		<p>
		<h2><?php esc_html_e( 'Map Data', 'wp-google-maps' ); ?></h2>
		</p>
		<div class="switch"><input id="categories_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['categories'] ) ? 'disabled' : ( ! $doing_edit || $this->options['categories'] ? 'checked' : '' ); ?>><label for="categories_import"></label></div><?php esc_html_e( 'Categories', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="customfields_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo ( ! $doing_edit || $this->options['customfields'] ? 'checked' : '' ); ?>><label for="customfields_import"></label></div><?php esc_html_e( 'Custom Fields', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="markers_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['markers'] ) ? 'disabled' : ( ! $doing_edit || $this->options['markers'] ? 'checked' : '' ); ?>><label for="markers_import"></label></div><?php esc_html_e( 'Markers', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="circles_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['circles'] ) ? 'disabled' : ( ! $doing_edit || $this->options['circles'] ? 'checked' : '' ); ?>><label for="circles_import"></label></div><?php esc_html_e( 'Circles', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="polygons_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['polygons'] ) ? 'disabled' : ( ! $doing_edit || $this->options['polygons'] ? 'checked' : '' ); ?>><label for="polygons_import"></label></div><?php esc_html_e( 'Polygons', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="polylines_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['polylines'] ) ? 'disabled' : ( ! $doing_edit || $this->options['polylines'] ? 'checked' : '' ); ?>><label for="polylines_import"></label></div><?php esc_html_e( 'Polylines', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="rectangles_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['rectangles'] ) ? 'disabled' : ( ! $doing_edit || $this->options['rectangles'] ? 'checked' : '' ); ?>><label for="rectangles_import"></label></div><?php esc_html_e( 'Rectangles', 'wp-google-maps' ); ?><br>
		<div class="switch"><input id="datasets_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $this->file_data['datasets'] ) ? 'disabled' : ( ! $doing_edit || $this->options['datasets'] ? 'checked' : '' ); ?>><label for="datasets_import"></label></div><?php esc_html_e( 'Heatmap Datasets', 'wp-google-maps' ); ?><br>
		<br>
		
		<div class="switch">
			<input 
				id="geocode_import" 
				class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox"
				<?php
				echo ($doing_edit && isset($this->options['geocode']) ? 'checked' : '');
				?>
				/>
			<label for="geocode_import"></label>
		</div>
		<?php esc_html_e( 'Find Addresses or Latitude and Longitude when missing', 'wp-google-maps' ); ?>
		<br>
		<span style="font-style:italic;"><?php esc_html_e( 'Requires Google Maps Geocoding API to be enabled.', 'wp-google-maps' ); ?></span> <a href="https://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/" target="_blank">[?]</a><br>
		<br>
		
		<div class="switch"><input id="apply_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo empty( $maps ) ? 'disabled' : ( $doing_edit && $this->options['apply'] ? 'checked' : '' ); ?>><label for="apply_import"></label></div><?php esc_html_e( 'Apply import data to', 'wp-google-maps' ); ?>
		<br>
		<span style="font-style:italic;"><?php esc_html_e( 'No maps will be imported with this option, only map data.', 'wp-google-maps' ); ?></span>
		<br>
		<div id="maps_apply_import" style="<?php echo empty( $maps ) ? 'display:none;' : ( $doing_edit && $this->options['apply'] ? '' : 'display:none;' ); ?>width:100%;">
			<?php if ( empty( $maps ) ) { ?>
				<br><?php esc_html_e( 'No maps available for import to.', 'wp-google-maps' ); ?>
			<?php } else { ?>
				<br>
				<div class="switch"><input id="replace_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit && $this->options['replace'] ? 'checked' : ''; ?>><label for="replace_import"></label></div><?php esc_html_e( 'Replace map data', 'wp-google-maps' ); ?>
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
		<br>
		<div class="delete-after-import">
			<div class="switch"><input id="delete_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit ? 'disabled' : ''; ?>><label for="delete_import"></label></div><?php esc_html_e( 'Delete import file after import', 'wp-google-maps' ); ?>
		</div>
		<br><br>
		<div id="import-schedule-json-options" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>>
			<h2><?php esc_html_e( 'Scheduling Options', 'wp-google-maps' ); ?></h2>
			<?php esc_html_e( 'Start Date', 'wp-google-maps' ); ?>
			<br>
			<input type="date" id="import-schedule-json-start" class="import-schedule-json-options" <?php echo $doing_edit ? 'value="' . $this->options['start'] . '"' : ''; ?>>
			<br><br>
			<?php esc_html_e( 'Interval', 'wp-google-maps' ); ?>
			<br>
			<select id="import-schedule-json-interval" class="import-schedule-json-options">
				<?php
				$schedule_intervals = wp_get_schedules();
				foreach ( $schedule_intervals as $schedule_interval_key => $schedule_interval ) { ?>
					<option value="<?php echo esc_attr( $schedule_interval_key ); ?>" <?php echo $doing_edit && $schedule_interval_key === $this->options['interval'] ? 'selected' : ''; ?>><?php echo esc_html( $schedule_interval['display'] ); ?></option>
				<?php } ?>
			</select>
			<br><br>
		</div>
		<p>
			<button id="import-json" class="wpgmza_general_btn" <?php if ( $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
			<button id="import-schedule-json" class="wpgmza_general_btn"><?php echo $doing_edit ? esc_html__( 'Update Schedule', 'wp-google-maps' ) : esc_html__( 'Schedule', 'wp-google-maps' ); ?></button>
			<button id="import-schedule-json-cancel" class="wpgmza_general_btn" <?php if ( ! $doing_edit ) { ?>style="display:none;"<?php } ?>><?php esc_html_e( 'Cancel', 'wp-google-maps' ); ?></button>
		</p>
		<script>
			(function($){
				<?php if ( ! $doing_edit ) { ?>$('.maps_apply').prop('checked', false);<?php } ?>
				$('#maps_import_select_all').click(function(){
					$('.maps_import').prop('checked', true);
				});
				$('#maps_import_select_none').click(function(){
					$('.maps_import').prop('checked', false);
				});
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
				function json_get_import_options(){
					var import_options = {};
					var maps_check = $('.maps_import:checked');
					var map_ids = [];
					var apply_check = $('.maps_apply:checked');
					var apply_ids = [];
					if (maps_check.length < 1){
						alert('<?php echo wp_slash( __( 'Please select at least one map to import.', 'wp-google-maps' ) ); ?>');
						return {};
					}
					maps_check.each(function(){
						map_ids.push($(this).val());
					});
					if (map_ids.length < $('.maps_import').length){
						import_options['maps'] = map_ids.join(',');
					}
					$('.map_data_import').each(function(){
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
					return import_options;
				}
				$('#import-json').click(function(){
					var import_options = json_get_import_options();
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
				$('#import-schedule-json').click(function(){
					if ($('#import-json').is(':visible')) {
						$('#import-json,.delete-after-import').hide();
						$('#import-schedule-json-cancel').show();
						$('#import-schedule-json-options').slideDown(300);
					} else {
						var import_options = json_get_import_options();
						if (Object.keys(import_options).length < 1){
							return;
						}
						if ($('#import-schedule-json-start').val().length < 1){
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

								start: $('#import-schedule-json-start').val(),
								interval: $('#import-schedule-json-interval').val(),
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
				$('#import-schedule-json-cancel').click(function(){
					$('#import-json,.delete-after-import').show();
					$('#import-schedule-json-cancel').hide();
					$('#import-schedule-json-options').slideUp(300);
				});
			})(jQuery);
		</script>
		<?php

		return ob_get_clean();

	}

	/**
	 * Import the JSON file.
	 */
	public function import() {

		$iterations = 1;

		if ( $this->options['apply'] ) {

			$iterations = count( $this->options['applys'] );

		}

		for ( $i = 0; $i < $iterations; $i++ ) {

			if ( $this->options['apply'] ) {

				if ( $this->options['replace'] ) {

					$this->clear_map_data();
					$this->options['replace'] = false;

				}

				$this->map_id_map = array();

				foreach ( $this->file_data['maps'] as $map ) {

					if ( ! empty( $this->options['maps'] ) && ! in_array( absint( $map['id'] ), $this->options['maps'], true ) ) {

						continue;

					}

					$this->map_id_map[ absint( $map['id'] ) ] = $this->options['applys'][ $i ];

				}
			}

			// $this->import_custom_fields();
			$this->import_maps();
			$this->import_categories();
			$this->import_markers();
			$this->import_circles();
			$this->import_polygons();
			$this->import_polylines();
			$this->import_rectangles();
			$this->import_datasets();

		} // End for().
		
		$this->onImportComplete();
	}

	/**
	 * Clear map data from applys.
	 */
	function clear_map_data() {

		if ( ! $this->options['apply'] || empty( $this->options['applys'] ) ) {

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

		if ( $this->options['markers'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname` WHERE `map_id` IN ($applys_in)" );

		}

		if ( $this->options['circles'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname_circles` WHERE `map_id` IN ($applys_in)" );

		}

		if ( $this->options['polygons'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname_poly` WHERE `map_id` IN ($applys_in)" );

		}

		if ( $this->options['polylines'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname_polylines` WHERE `map_id` IN ($applys_in)" );

		}

		if ( $this->options['rectangles'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname_rectangles` WHERE `map_id` IN ($applys_in)" );

		}

		if ( $this->options['datasets'] ) {

			$wpdb->query( "DELETE FROM `$wpgmza_tblname_datasets` WHERE `map_id` IN ($applys_in)" );

		}
	}

	/**
	 * Import maps.
	 */
	protected function import_maps() {

		if ( $this->options['apply'] || empty( $this->file_data['maps'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_maps;

		foreach ( $this->file_data['maps'] as $map ) {

			if ( ! empty( $this->options['maps'] ) && ! in_array( absint( $map['id'] ), $this->options['maps'], true ) ) {

				continue;

			}

			$success = $wpdb->insert( $wpgmza_tblname_maps, array( 
				'map_title'            => isset( $map['map_title'] ) ? $map['map_title'] : __( 'New Imported Map', 'wp-google-maps' ),
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

				$map_id = empty( $map['id'] ) ? 0 : absint( $map['id'] );
				$this->map_id_map[ $map_id ] = $wpdb->insert_id;

			}
			
			if(!empty($map->filter_custom_field_names))
			{
				// TODO: Link
			}
			
		} // End foreach().
	}

	/**
	 * Import categories.
	 */
	protected function import_categories() {

		if ( ! $this->options['categories'] || empty( $this->file_data['categories'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_categories;
		global $wpgmza_tblname_category_maps;

		$parent_update = array();

		foreach ( $this->file_data['categories'] as $cat_key => $category ) {
			
			if ( ! isset( $category['map_id'] ) || ( absint( $category['map_id'] ) > 0 && ! isset( $this->map_id_map[ absint( $category['map_id'] ) ] ) ) ) {

				continue;

			}

			$cat_id = empty( $category['id'] ) ? 0 : absint( $category['id'] );
			
			if ( ! isset( $this->cat_id_map[ $cat_id ] ) ) {

				$cat_search = null;

				if ( ! empty( $category['category_name'] ) ) {

					$cat_search = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM `$wpgmza_tblname_categories` WHERE `category_name`=%s AND active=0", $category['category_name'] ) );

				}

				if ( null === $cat_search ) {
					
					$params = array(
						'active'        => isset( $category['active'] ) ? $category['active'] : 0,
						'category_name' => isset( $category['category_name'] ) ? $category['category_name'] : __( 'New Imported Category', 'wp-google-maps' ),
						'category_icon' => isset( $category['category_icon'] ) ? $category['category_icon'] : '',
						'retina'        => isset( $category['retina'] ) ? $category['retina'] : 0,
						'parent'        => isset( $category['parent'] ) ? $category['parent'] : 0,
						'priority'      => isset( $category['priority'] ) ? $category['priority'] : 0,
					);

					$success = $wpdb->insert( $wpgmza_tblname_categories, $params, array(
						'%d',
						'%s',
						'%s',
						'%d',
						'%d',
						'%d',
					) );
					
				} else {

					$success = 1;

				}

				if ( false !== $success ) {

					$this->cat_id_map[ $cat_id ] = null !== $cat_search ? $cat_search : $wpdb->insert_id;

					if ( isset( $category['parent'] ) && intval( $category['parent'] ) > 0 ) {

						$parent_update[] = $cat_key;

					}
				}
			} // End if().

			if ( isset( $this->cat_id_map[ $cat_id ], $category['map_id'] ) ) {

				$map_id = isset( $this->map_id_map[ absint( $category['map_id'] ) ] ) ? $this->map_id_map[ absint( $category['map_id'] ) ] : 0;

				if ( 0 === $map_id ) {

					if ( isset( $this->cat_all_maps[ $cat_id ] ) ) {

						continue;

					} else {

						$this->cat_all_maps[ $cat_id ] = $this->cat_id_map[ $cat_id ];

					}
				}

				$success = $wpdb->insert( $wpgmza_tblname_category_maps, array(
					'cat_id' => $this->cat_id_map[ $cat_id ],
					'map_id' => $map_id,
				), array(
					'%d',
					'%d',
				) );

			}
		} // End foreach().

		foreach ( $parent_update as $cat_key ) {

			$cat_id = absint( $this->file_data['categories'][ $cat_key ]['id'] );
			$parent_id = absint( $this->file_data['categories'][ $cat_key ]['parent'] );

			if ( isset( $this->cat_id_map[ $cat_id ], $this->cat_id_map[ $parent_id ] ) ) {

				$success = $wpdb->update( $wpgmza_tblname_categories, array(
					'parent' => $this->cat_id_map[ $parent_id ],
				), array(
					'id' => $this->cat_id_map[ $cat_id ],
				), '%d', '%d' );

			}
		}
	}

	/**
	 * Import markers.
	 */
	protected function import_markers() {
		
		if ( ! $this->options['markers'] || empty( $this->file_data['markers'] ) ) {
			
			$this->log("No marker data found");
			return;

		}
		
		CustomFields::install();

		global $wpdb;
		global $wpgmza_tblname;
		global $WPGMZA_TABLE_NAME_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;

		foreach ( $this->file_data['markers'] as $fields ) {
			
			$custom_fields_data = null;
			$ratings_data = null;

			if ( ! isset( $fields['map_id'] ) || absint( $fields['map_id'] ) < 1 || ! isset( $this->map_id_map[ absint( $fields['map_id'] ) ] ) )
			{
				//$this->log("Ignoring marker, no map found");
				//continue;
			}

			if(isset($this->options['geocode']))
			{
				if(!($result = $this->geocode($fields['address'])))
					echo "<div>" . sprintf( __('Failed to geocode "%s"'), $fields['address'] ) . '</div>';
				
				$fields['lat'] = $result[0];
				$fields['lng'] = $result[1];
			}
			
			$fields['map_id'] = $this->map_id_map[ absint( $fields['map_id'] ) ];
			
			if(!empty($fields['custom_fields_data']))
			{
				$custom_fields_data = $fields['custom_fields_data'];
				unset($fields['custom_fields_data']);
			}
			
			if(!empty($fields['ratings']))
			{
				$ratings_data = $fields['ratings'];
				unset($fields['ratings']);
			}
			
			// Discard ID
			unset($fields['id']);
			
			// Remap categories
			$oldCategories = null;
			$newCategories = array();
			
			if(!empty($fields['category']))
				$oldCategories = explode(',', $fields['category']);
			
			if(!empty($fields['categories']))
				$oldCategories = $fields['categories'];
			
			unset($fields['category']);
			unset($fields['categories']);
			
			if($oldCategories)
			{
				foreach($oldCategories as $old)
				{
					if(isset($this->cat_id_map[$old]))
						$newCategories[] = $this->cat_id_map[$old];
					else
						$newCategories[] = $old; // Nothing to re-map to, use raw ID
				}
			}
			
			// Create the marker
			$marker = Marker::createInstance($fields);
			$marker->categories = $newCategories;
			
			// Custom field data
			if(!empty($custom_fields_data))
			{
				$custom_fields = new CustomMarkerFields($marker->id);
				
				foreach($custom_fields_data as $key => $value)
				{
					if(isset($value['id']))
					{
						// NB: New format with ID as a field
						$custom_field_name = $value['name'];
						
						unset($value['id']);
						unset($value['name']);
						
						$custom_fields->{$custom_field_name} = $value['value'];
					}
					else
					{
						// NB: Old format with field names as keys
						$custom_fields->{$key} = $value;
					}
				}
			}
			
			// Ratings
			if($ratings_data)
				foreach($ratings_data as $arr)
				{
					// Discard the rating ID
					unset($arr['id']);
					
					$params = array(
						$arr['created'],
						$arr['amount'],
						$arr['ip_address'],
						$arr['user_agent'],
						$arr['user_guid']
					);
					
					$author = "";
					
					if($arr['author'] != null)
					{
						$author = "%d";
						$params[] = $arr['author'];
					}
					else
						$author = "NULL";
					
					$stmt = $wpdb->prepare("
						INSERT INTO $WPGMZA_TABLE_NAME_RATINGS
						(
							created,
							amount,
							ip_address,
							user_agent,
							user_guid,
							author
						)
						VALUES (
							%s,
							%f,
							%s,
							%s,
							%s,
							$author
						)
					", $params);
					
					$wpdb->query($stmt);
					
					// Now link the rating to the marker
					$params = array(
						'rating_id'		=> $wpdb->insert_id,
						'marker_id'		=> $marker->id
					);
					
					$stmt = $wpdb->prepare("
						INSERT INTO $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS
						(rating_id, marker_id)
						VALUES
						(%d, %d)
					", $params);
				}
			
		} // End foreach().
	}

	/**
	 * Import circles.
	 */
	protected function import_circles() {

		if ( ! $this->options['circles'] || empty( $this->file_data['circles'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_circles;

		foreach ( $this->file_data['circles'] as $circle ) {

			if ( ! isset( $circle['map_id'] ) || absint( $circle['map_id'] ) < 1 || ! isset( $this->map_id_map[ absint( $circle['map_id'] ) ] ) ) {

				continue;

			}

			$success = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpgmza_tblname_circles`
				(`map_id`,`name`,`center`,`radius`,`color`,`opacity`)
				VALUES (%d,%s,POINT(%f,%f),%f,%s,%f)",
				$this->map_id_map[ absint( $circle['map_id'] ) ],
				isset( $circle['name'] ) ? $circle['name'] : __( 'New Imported Circle', 'wp-google-maps' ),
				isset( $circle['centerX'] ) ? $circle['centerX'] : 0,
				isset( $circle['centerY'] ) ? $circle['centerY'] : 0,
				isset( $circle['radius'] ) ? $circle['radius'] : 20,
				isset( $circle['color'] ) ? $circle['color'] : '#ff0000',
				isset( $circle['opacity'] ) ? $circle['opacity'] : 0.6
			) );

		} // End foreach().
	}

	/**
	 * Import polygons.
	 */
	protected function import_polygons() {

		if ( ! $this->options['polygons'] || empty( $this->file_data['polygons'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_poly;

		foreach ( $this->file_data['polygons'] as $polygon ) {

			if ( ! isset( $polygon['map_id'] ) || absint( $polygon['map_id'] ) < 1 || ! isset( $this->map_id_map[ absint( $polygon['map_id'] ) ] ) ) {

				continue;

			}

			$success = $wpdb->insert( $wpgmza_tblname_poly, array(
				'map_id'        => $this->map_id_map[ absint( $polygon['map_id'] ) ],
				'polydata'      => isset( $polygon['polydata'] ) ? $polygon['polydata'] : '',
				'innerpolydata' => isset( $polygon['innerpolydata'] ) ? $polygon['innerpolydata'] : '',
				'linecolor'     => isset( $polygon['linecolor'] ) ? $polygon['linecolor'] : '000000',
				'lineopacity'   => isset( $polygon['lineopacity'] ) ? $polygon['lineopacity'] : 0.5,
				'fillcolor'     => isset( $polygon['fillcolor'] ) ? $polygon['fillcolor'] : '66FF00',
				'opacity'       => isset( $polygon['opacity'] ) ? $polygon['opacity'] : 0.5,
				'title'         => isset( $polygon['title'] ) ? $polygon['title'] : '',
				'description'   => isset( $polygon['description'] ) ? $polygon['description'] : '',
				'link'          => isset( $polygon['link'] ) ? $polygon['link'] : '',
				'ohfillcolor'   => isset( $polygon['ohfillcolor'] ) ? $polygon['ohfillcolor'] : '57FF78',
				'ohlinecolor'   => isset( $polygon['ohlinecolor'] ) ? $polygon['ohlinecolor'] : '737373',
				'ohopacity'     => isset( $polygon['opacity'] ) ? $polygon['opacity'] : 0.7,
				'polyname'      => isset( $polygon['polyname'] ) ? $polygon['polyname'] : __( 'New Imported Polygon', 'wp-google-maps' ),
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
				'%s',
				'%f',
				'%s',
			) );

		} // End foreach().
	}

	/**
	 * Import polylines.
	 */
	protected function import_polylines() {

		if ( ! $this->options['polylines'] || empty( $this->file_data['polylines'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_polylines;

		foreach ( $this->file_data['polylines'] as $polyline ) {

			if ( ! isset( $polyline['map_id'] ) || absint( $polyline['map_id'] ) < 1 || ! isset( $this->map_id_map[ absint( $polyline['map_id'] ) ] ) ) {

				continue;

			}

			$success = $wpdb->insert( $wpgmza_tblname_polylines, array(
				'map_id'        => $this->map_id_map[ absint( $polyline['map_id'] ) ],
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

		} // End foreach().
	}

	/**
	 * Import rectangles.
	 */
	protected function import_rectangles() {

		if ( ! $this->options['rectangles'] || empty( $this->file_data['rectangles'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_rectangles;

		foreach ( $this->file_data['rectangles'] as $rectangle ) {

			if ( ! isset( $rectangle['map_id'] ) || absint( $rectangle['map_id'] ) < 1 || ! isset( $this->map_id_map[ absint( $rectangle['map_id'] ) ] ) ) {

				continue;

			}

			$success = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpgmza_tblname_rectangles`
				(`map_id`,`name`,`cornerA`,`cornerB`,`color`,`opacity`)
				VALUES (%d,%s,POINT(%f,%f),POINT(%f,%f),%s,%f)",
				$this->map_id_map[ absint( $rectangle['map_id'] ) ],
				isset( $rectangle['name'] ) ? $rectangle['name'] : __( 'New Imported Rectangle', 'wp-google-maps' ),
				isset( $rectangle['cornerAX'] ) ? $rectangle['cornerAX'] : 0,
				isset( $rectangle['cornerAY'] ) ? $rectangle['cornerAY'] : 0,
				isset( $rectangle['cornerBX'] ) ? $rectangle['cornerBX'] : 0,
				isset( $rectangle['cornerBY'] ) ? $rectangle['cornerBY'] : 0,
				isset( $rectangle['color'] ) ? $rectangle['color'] : '#ff0000',
				isset( $rectangle['opacity'] ) ? $rectangle['opacity'] : 0.6
			) );

		} // End foreach().
	}

	/**
	 * Import datasets.
	 */
	protected function import_datasets() {

		if ( ! $this->options['datasets'] || empty( $this->file_data['datasets'] ) ) {

			return;

		}

		global $wpdb;
		global $wpgmza_tblname_datasets;

		foreach ( $this->file_data['datasets'] as $dataset ) {

			if ( ! isset( $dataset['map_id'] ) || absint( $dataset['map_id'] ) < 1 || ! isset( $this->map_id_map[ absint( $dataset['map_id'] ) ] ) ) {

				continue;

			}

			$success = $wpdb->insert( $wpgmza_tblname_datasets, array(
				'map_id'       => $this->map_id_map[ absint( $dataset['map_id'] ) ],
				'type'         => isset( $dataset['type'] ) ? $dataset['type'] : 0,
				'dataset_name' => isset( $dataset['dataset_name'] ) ? $dataset['dataset_name'] : __( 'New Imported Dataset', 'wp-google-maps' ),
				'dataset'      => isset( $dataset['dataset'] ) ? $dataset['dataset'] : '',
				'options'      => isset( $dataset['options'] ) ? $dataset['options'] : '',
			), array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
			) );

		} // End foreach().
	}
}

