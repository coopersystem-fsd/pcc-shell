<div id="import-tab">
	<div id="import_files">
		<h2>
			<?php esc_html_e( 'Import Data', 'wp-google-maps' ); ?>
		</h2>
		<table style="width:100%;">
			<tbody>
				<tr>
					<td style="width:100px;vertical-align:top;">
						<p>
							<?php esc_html_e( 'Import via:', 'wp-google-maps' ); ?>
						</p>
					</td>
					<td style="vertical-align:top;">
						<p>
							<label>
								<input type="radio" name="import_data_type" class="import_data_type" value="URL" checked="checked"/>
								<?php esc_html_e( 'URL', 'wp-google-maps' ); ?>
							</label>
						</p>
						<br/>
						<label>
							<input type="radio" name="import_data_type" class="import_data_type" value="file"/>
							<?php esc_html_e( 'File', 'wp-google-maps' ); ?>
						</label>
					</p>
					<br/>
					<div id="import_from_url">
						<p>
							<input id="wpgmaps_import_url" placeholder="<?php esc_attr_e( 'Import URL', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
							<br/>
							<span class="description" style="display:inline-block;max-width:500px;">
								<?php esc_html_e( 'If using a Google Sheet URL, the sheet must be public or have link sharing turned on.', 'wp-google-maps' ); ?>
							</span>
							<br/>
							<br/>
							<button id="wpgmaps_import_url_button" class="wpgmza_general_btn">
								<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
							</button>
						</p>
					</div>
					<div id="import_from_file" style="display:none;">
						<p>
							<span name="import_accepts"/>
							<br/>
							<br/>
							<input name="wpgmaps_import_file" id="wpgmaps_import_file" type="file" style="display:none;"/>
							<label for="wpgmaps_import_file" class="wpgmza_file_select_btn">
								<i class="fa fa-download"/>
								<?php esc_html_e( 'Select File', 'wp-google-maps' ); ?>
							</label>
							<span id="wpgmaps_import_file_name" style="margin-left:10px;"/>
							<br/>
							<br/>
							<?php esc_html_e( 'Max upload size', 'wp-google-maps' ); ?>: 
							<span name="max_upload_size"/>
							<br/>
							<br/>
							<button id="wpgmaps_import_upload_button" class="wpgmza_general_btn">
								<?php esc_html_e( 'Upload', 'wp-google-maps' ); ?>
							</button>
							<span id="wpgmaps_import_upload_spinner" class="spinner" style="float:none;margin-bottom:8px;"/>
						</p>
						<?php
					$import_files = new \WP_Query( array( 
						'post_type'      => 'attachment',
						'meta_key'       => '_wp_attachment_context',
						'meta_value'     => 'wpgmaps-import',
						'posts_per_page' => - 1,
					) );
					?>
						<div id="wpgmaps_import_file_list" <?php echo $import_files->found_posts < 1 ? 'style="display:none;"' : ''; ?>>
								<br/>
								<table id="wpgmap_import_file_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;padding:0;border:0 !important;">
									<thead>
										<tr>
											<th style="font-weight:bold;">
												<?php esc_html_e( 'Import Uploads', 'wp-google-maps' ); ?>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $import_files->posts as $import_file ) { ?>
										<tr id="import-list-item-<?php echo esc_attr( $import_file->ID ); ?>">
											<td>
												<strong>
													<span class="import_file_title" style="font-size:larger;">
														<?php echo esc_html( $import_file->post_title ); ?></span>
												</strong>
												<br/>
												<a href="javascript:void(0);" class="import_import" data-import-id="<?php echo esc_attr( $import_file->ID ); ?>"><?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
												</a>
										|
												<a href="javascript:void(0);" class="import_delete" data-import-id="<?php echo esc_attr( $import_file->ID ); ?>"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?>
												</a>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<br/>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="import_loader" style="display:none;">
		<div style="text-align:center;padding:50px 0;">
			<div class="spinner is-active" style="float:none;"/>
			<div id="import_loader_text"/>
		</div>
	</div>
	<div id="import_options" style="display:none;"/>
</div>
<?php $import_schedule = import_get_schedule(); ?>
<div id="schedule-tab" style="display:none;">
	<h2>
		<?php esc_html_e( 'Schedule', 'wp-google-maps' ); ?>
	</h2>
	<p class="description" style="max-width:600px;">
		<?php esc_html_e( 'Imports can be scheduled by url or uploaded file. To schedule an import, import as normal and select the Schedule button. Scheduled imports will be listed on this page and can be edited or deleted from here.', 'wp-google-maps' ); ?>
	</p>
	<div id="wpgmaps_import_schedule_list"<?php if ( empty( $import_schedule ) ) { ?> style="display:none;"<?php } ?>>
			<br/>
			<table id="wpgmap_import_schedule_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
				<thead>
					<tr>
						<th>
							<?php esc_html_e( 'URL / Filename', 'wp-google-maps' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $import_schedule ) ) {
				foreach ( $import_schedule as $schedule_id => $schedule ) { ?>
					<tr id="import-schedule-list-item-<?php echo esc_attr( $schedule_id ); ?>">
						<td>
							<strong>
								<span class="import_schedule_title" style="font-size:larger;">
									<?php echo esc_html( $schedule['title'] ); ?>
								</span>
							</strong>
							<br/>
							<a href="javascript:void(0);" class="import_schedule_edit" data-schedule-id="<?php echo esc_attr( $schedule_id ); ?>"><?php esc_html_e( 'Edit', 'wp-google-maps' ); ?>
							</a>
							|
							<a href="javascript:void(0);" class="import_schedule_delete" data-schedule-id="<?php echo esc_attr( $schedule_id ); ?>"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?>
							</a>
							|
							<?php if ( empty( $schedule['next_run'] ) ) { ?>
							<?php esc_html_e( 'No schedule found', 'wp-google-maps' ); ?>
							<?php } else { ?>
							<?php esc_html_e( 'Next Scheduled Run', 'wp-google-maps' ); ?>: <?php echo esc_html( $schedule['next_run'] ); ?>
							<?php } ?>
							<?php if ( ! empty( $schedule['last_run_message'] ) ) { ?>
							<br/>
							<?php echo esc_html( $schedule['last_run_message'] ); ?>
							<?php } ?>
						</td>
					</tr>
					<?php }
			} ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php $maps = import_export_get_maps_list( 'export' ); ?>
	<div id="export-tab" style="display:none;">
		<h2>
			<?php esc_html_e( 'Export Data', 'wp-google-maps' ); ?>
		</h2>
		<p class="description" style="max-width:600px;">
			<?php esc_html_e( 'Select which maps and map data you\'d like to export. Click the Export button to download a JSON file of the exported maps and their data.', 'wp-google-maps' ); ?>
		</p>
		<div style="margin:0 0 1em 0;width:100%;">
			<?php if ( empty( $maps ) ) { ?>
			<br/>
			<?php esc_html_e( 'No maps available for export.', 'wp-google-maps' ); ?>
			<?php } else { ?>
			<table class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;">
				<thead style="display:block;border-bottom:1px solid #e1e1e1;">
					<tr style="display:block;width:100%;">
						<th style="width:2.2em;border:none;"/>
						<th style="width:80px;border:none;">
							<?php esc_html_e( 'ID', 'wp-google-maps' ); ?>
						</th>
						<th style="border:none;">
							<?php esc_html_e( 'Title', 'wp-google-maps' ); ?>
						</th>
					</tr>
				</thead>
				<tbody style="display:block;max-height:370px;overflow-y:scroll;">
					<?php echo $maps; ?>
				</tbody>
			</table>
			<button id="maps_export_select_all" class="wpgmza_general_btn">
				<?php esc_html_e( 'Select All', 'wp-google-maps' ); ?>
			</button>
			<button id='maps_export_select_none' class='wpgmza_general_btn'>
				<?php esc_html_e( 'Select None', 'wp-google-maps' ); ?>
			</button>
			<br/>
			<br/>
			<?php } ?>
		</div>
		<p>
			<h2>Map Data</h2>
		</p>
		<div class="switch">
			<input id="categories_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="categories_export"/>
		</div>
		<?php esc_html_e( 'Categories', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="customfields_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="customfields_export"/>
		</div>
		<?php esc_html_e( 'Custom Fields', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="markers_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="markers_export"/>
		</div>
		<?php esc_html_e( 'Markers', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="circles_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="circles_export"/>
		</div>
		<?php esc_html_e( 'Circles', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="polygons_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="polygons_export"/>
		</div>
		<?php esc_html_e( 'Polygons', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="polylines_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="polylines_export"/>
		</div>
		<?php esc_html_e( 'Polylines', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="rectangles_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="rectangles_export"/>
		</div>
		<?php esc_html_e( 'Rectangles', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="datasets_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="datasets_export"/>
		</div>
		<?php esc_html_e( 'Heatmap Datasets', 'wp-google-maps' ); ?>
		<br/>
		<br/>
		<p>
			<button id="export-json" class="wpgmza_general_btn">
				<?php esc_html_e( 'Export', 'wp-google-maps' ); ?>
			</button>
		</p>
	</div>