<div id="import-tab">
	<div id="import_files">
		<h2>
			<?php esc_html_e( 'Import Data', 'wp-google-maps' ); ?>
		</h2>
		<table style="width:100%;">
			<tbody>
				<tr>
					<td style="width:100px;vertical-align:top;">
						<?php esc_html_e( 'Import via:', 'wp-google-maps' ); ?>
					</td>
					<td style="vertical-align:top;">
						<div id="import_via">
							<label>
								<input type="radio" name="import_data_type" class="import_data_type" value="URL" checked="checked"/>
								<?php esc_html_e( 'URL', 'wp-google-maps' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="import_data_type" class="import_data_type" value="file"/>
								<?php esc_html_e( 'File', 'wp-google-maps' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="import_data_type" class="import_data_type" value="bulk_jpeg"/>
								<?php esc_html_e("Bulk JPEG", "wp-google-maps"); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="import_data_type" class="import_data_type" value="airtable"/>
								<?php esc_html_e("Airtable", "wp-google-maps"); ?>
							</label>

						</div>
						
						<br/>
						<div id="import_from_url" class="wpgmza-import-upload-panel">
							<input id="wpgmaps_import_url" placeholder="<?php esc_attr_e( 'Import URL', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
							<br/>
							<span class="description" style="display:inline-block;max-width:500px;">
								<?php esc_html_e( 'If using a Google Sheet URL, the sheet must be public or have link sharing turned on.', 'wp-google-maps' ); ?>
							</span>
							<br/>
							<br/>
							<button id="wpgmaps_import_url_button" class="wpgmza_general_btn button button-primary">
								<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
							</button>
						</div>
						
						<div id="import_from_file" class="wpgmza-import-upload-panel" style="display:none;">
							<div>
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
								<button id="wpgmaps_import_upload_button" class="wpgmza_general_btn button button-primary">
									<?php esc_html_e( 'Upload', 'wp-google-maps' ); ?>
								</button>
								<span id="wpgmaps_import_upload_spinner" class="spinner" style="float:none;margin-bottom:8px;"/>
							</div>
							
							<div id="wpgmaps_import_file_list">
								<br/>
								<table id="wpgmap_import_file_list_table" class="wp-list-table widefat fixed striped wpgmza-listing" style="width:100%;padding:0;">
									<thead>
										<tr>
											<th style="font-weight:bold;">
												<?php esc_html_e( 'Import Uploads', 'wp-google-maps' ); ?>
											</th>
										</tr>
									</thead>
									<tbody>
										
										<tr>
											<td>
												<strong>
													<span 
														name="post_title"
														class="import_file_title" 
														style="font-size:larger;"></span>
												</strong>
												<br/>
												<a href="javascript:void(0);" class="import_import" data-import-id><?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
												</a>
												|
												<a href="javascript:void(0);" class="import_delete" data-import-id><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?>
												</a>
											</td>
										</tr>
										
									</tbody>
								</table>
							</div>
							<br/>
						</div>
						
						<div id="import_from_bulk_jpeg" class="wpgmza-import-upload-panel" style="display: none;">
							
							<p>
								<input name="bulk_jpeg_files" type="file" multiple accept="image/jpeg"/>
							</p>
							
							<p id="bulk_jpeg_status"></p>
							
							<button id="wpgmaps_import_bulk_jpeg_button" class="wpgmza_general_btn button button-primary">
								<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
							</button>
							
						</div>

						<!-- Airtables Integration -->
						<div id="import_from_airtable" class="wpgmza-import-upload-panel" style="display: none;">

							<input id="wpgmaps_import_airtable_url" placeholder="<?php esc_attr_e( 'Airtable URL', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
							<br/>
							<span class="description" style="display:inline-block;max-width:500px;">
								<?php esc_html_e( 'Link to Airtable', 'wp-google-maps' ); ?>
							</span>

							<br/>
							<br/>

							<input id="wpgmaps_import_airtable_api" placeholder="<?php esc_attr_e( 'Airtable API Key', 'wp-google-maps' ); ?>" type="text" style="max-width:500px;width:100%;"/>
							<br/>
							<span class="description" style="display:inline-block;max-width:500px;">
								<?php esc_html_e( 'Airtable API Key', 'wp-google-maps' ); ?>
							</span>
							<br/>
							<br/>

							<button id="wpgmaps_import_airtable_button" class="wpgmza_general_btn">
								<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
							</button>	
						</div>

						<!-- Airtables Integration -->
						
						<div id="import_from_integration" class="wpgmza-import-upload-panel" style="display: none;">
							<button id="wpgmaps_import_integration_button" class="wpgmza_general_btn button button-primary">
								<?php esc_html_e( 'Import', 'wp-google-maps' ); ?>
							</button>
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
<div id="schedule-tab" style="display:none;">
	<h2>
		<?php esc_html_e( 'Schedule', 'wp-google-maps' ); ?>
	</h2>
	<p class="description" style="max-width:600px;">
		<?php esc_html_e( 'Imports can be scheduled by url or uploaded file. To schedule an import, import as normal and select the Schedule button. Scheduled imports will be listed on this page and can be edited or deleted from here.', 'wp-google-maps' ); ?>
	</p>
	<div id="wpgmaps_import_schedule_list">
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
				<tr>
					<td>
						<strong>
							<span 
								class="import_schedule_title" 
								style="font-size:larger;"
								name="title">
							</span>
						</strong>
						
						<br/>
						
						<a href="javascript:void(0);" class="import_schedule_edit"><?php esc_html_e( 'Edit', 'wp-google-maps' ); ?>
						</a>
						|
						<a href="javascript:void(0);" class="import_schedule_delete"><?php esc_html_e( 'Delete', 'wp-google-maps' ); ?>
						</a>
						|
						<a href="javascript:void(0);" class="import_schedule_view_log"><?php esc_html_e( 'View Log', 'wp-google-maps' ); ?>
						</a>
						|
						<a href="javascript:void(0);" class="import_schedule_view_response"><?php esc_html_e( 'View Response', 'wp-google-maps' ); ?>
						</a>
						
						<span name="status"></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="export-tab" style="display:none;">
	<h2>
		<?php esc_html_e( 'Export Data', 'wp-google-maps' ); ?>
	</h2>
	<p class="description">
		<?php esc_html_e( 'Select which maps and map data you\'d like to export. Click the Export button to download a JSON file of the exported maps and their data.', 'wp-google-maps' ); ?>
	</p>
	<div id="wpgmza-import-target-map-panel" style="margin:0 0 1em 0;width:100%;">
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
			</tbody>
		</table>
		<button id="maps_export_select_all" class="wpgmza_general_btn button button-secondary">
			<?php esc_html_e( 'Select All', 'wp-google-maps' ); ?>
		</button>
		<button id='maps_export_select_none' class='wpgmza_general_btn button button-secondary'>
			<?php esc_html_e( 'Select None', 'wp-google-maps' ); ?>
		</button>
		<br/>
		<br/>
	</div>
	
	<h2>Map Data</h2>
	
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
	
	<?php
	// TODO: Move to Gold, use hooks
	if(defined('WPGMZA_GOLD_VERSION') && version_compare(WPGMZA_GOLD_VERSION, '5.0.0', '>='))
	{
		?>
		
		<?php esc_html_e( 'Ratings', 'wp-google-maps' ); ?>
		<br/>
		<div class="switch">
			<input id="ratings_export" class="map_data_export cmn-toggle cmn-toggle-round-flat" type="checkbox" checked/>
			<label for="ratings_export"/>
		</div>
		
		<?php
	}
	?>
	
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
		<button id="export-json" class="wpgmza_general_btn button button-primary">
			<?php esc_html_e( 'Export', 'wp-google-maps' ); ?>
		</button>
	</p>
</div>

<div id="backups-tab" style="display:none;">
	<h2>
		<?php esc_html_e( 'Backups (Beta)', 'wp-google-maps' ); ?>
	</h2>

	<div class="notice notice-success wpgmza_backup_notice">

	</div>

	<div class="wpgmza_backups_info">
		<p class="description"><?php _e("WP Google Maps will automatically backup your data before an update is installed and when an import is performed. Manual backups can also be generated below.", "wp-google-maps"); ?></p>

		<p><?php _e("Automated backups will be removed when new backups of the same type are generated. Manual backups will not be removed by the backup module.", "wp-google-maps"); ?></p>

		<p><strong><?php _e("Important Note", "wp-google-maps"); ?>:</strong> <?php _e("This feature is in early beta and data backups cannot be guaranteed as they are dependent on file write access on your server", "wp-google-maps"); ?></p>
	</div>

	<div id="wpgmza_backups_content">

	</div>

	<p>
		<a href="#" id="wpgmza_new_backup_btn" class="button button-primary"><?php _e("Create Backup", "wp-google-maps"); ?></a>
	</p>
</div>