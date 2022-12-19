<?php

namespace WPGMZA;

require_once(__DIR__ . '/class.custom-fields.php');
require_once(__DIR__ . '/class.custom-marker-fields.php');

class CustomFieldsPage
{
	public function __construct()
	{
		global $wpgmza;
		
		if(!CustomFields::installed())
			CustomFields::install();
		
		$wpgmza->loadScripts();
		
		$this->fontAwesomeIconPicker = new FontAwesomeIconPicker();
		
		wp_enqueue_script('wpgmza-custom-fields-page', plugin_dir_url(WPGMZA_PRO_FILE) . 'js/custom-fields-page.js');
		wp_enqueue_style('wpgmza-legacy-admin', plugin_dir_url(WPGMZA_PRO_FILE) . 'css/wpgmaps-admin.css');
	}
	
	/**
	 * Called when POSTing custom field data through WP admin post hook
	 * @return void
	 */
	public static function POST()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		check_ajax_referer('wpgmza', 'security');
		
		if(!current_user_can('administrator'))
		{
			http_response_code(401);
			exit;
		}
		
		$numFields = count($_POST['ids']);
		
		// Remove fields which aren't in POST from the DB
		$qstr = "DELETE FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS";
		if($numFields > 0)
			$qstr .= " WHERE id NOT IN (" . implode(',', array_map('intval', $_POST['ids'])) . ")";
		$wpdb->query($qstr);

		// Iterate over fields in POST
		for($i = 0; $i < $numFields; $i++)
		{
			$id 						= $_POST['ids'][$i];
			$stack_order 				= $i;
			$name 						= sanitize_text_field($_POST['names'][$i]);
			$icon						= sanitize_text_field($_POST['icons'][$i]);
			$attributes					= stripslashes($_POST['attributes'][$i]);
			$widget_type				= sanitize_text_field($_POST['widget_types'][$i]);
			$display_in_infowindows		= isset($_POST['display_in_infowindows'][$id]) ? 1 : 0;
			$display_in_marker_listings	= isset($_POST['display_in_marker_listings'][$id]) ? 1 : 0;

			/*
			 * Increases the complexity, but a needed step due to how we submit this data
			*/ 
			try{
				$attributes = json_decode($attributes, true);
				$cleanAttributes = array();
				foreach($attributes as $aKey => $aValue){
					$aKey = sanitize_text_field($aKey);
					$aValue = sanitize_text_field($aValue);

					$cleanAttributes[$aKey] = $aValue;
				}

				if(!empty($cleanAttributes)){
					$attributes = $cleanAttributes;
				} else {
					$attributes = array("" => "");
				}
			} catch (\Exception $ex){
				$attributes = array("" => "");
			} catch (\Error $er){
				$attributes = array("" => "");
			}

			$attributes = json_encode($attributes);

			if(!json_decode($attributes))
				throw new \Exception('Invalid attribute JSON');
			
			if($id == -1 || empty($id))
			{
				$display_in_infowindows	= isset( $_POST['display_in_infowindows']['-1'] );
				$display_in_marker_listings = isset( $_POST['display_in_marker_listings']['-1'] );

				$qstr = "INSERT INTO $WPGMZA_TABLE_NAME_CUSTOM_FIELDS (name, icon, attributes, widget_type, display_in_infowindows, display_in_marker_listings, stack_order) VALUES (%s, %s, %s, %s, %s, %s, %s)";
				$params = array($name, $icon, $attributes, $widget_type, $display_in_infowindows, $display_in_marker_listings, $stack_order);
			}
			else
			{
				$qstr = "UPDATE $WPGMZA_TABLE_NAME_CUSTOM_FIELDS SET name=%s, icon=%s, attributes=%s, widget_type=%s, display_in_infowindows=%s, display_in_marker_listings=%s, stack_order=%s WHERE id=%s";
				$params = array($name, $icon, $attributes, $widget_type, $display_in_infowindows, $display_in_marker_listings, $stack_order, $id);
			}
			
			$stmt = $wpdb->prepare($qstr, $params);
			$wpdb->query($stmt);
		}
		
		wp_redirect( admin_url('admin.php') . '?page=wp-google-maps-menu-custom-fields' );
		exit;
	}
	
	/**
	 * Echos attribute table HTML for the given field
	 * @return void
	 */
	protected function attributeTableHTML($field)
	{
		$attributes = json_decode($field->attributes);
		
		if(empty($attributes))
			$attributes = array("" => "");
		
		?>
		<input name="attributes[]" type="hidden"/>
		<table class="attributes">
			<tbody>
				<?php
				foreach($attributes as $key => $value)
				{
				?>
					<tr>
						<td>
							<input
								placeholder="<?php _e('Name', 'wp-google-maps'); ?>"
								class="attribute-name"
								value="<?php echo $key; ?>"
								/>
						</td>
						<td>
							<input 
								placeholder="<?php _e('Value', 'wp-google-maps'); ?>"
								class="attribute-value"
								value="<?php echo $value; ?>"
								/>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Echos the custom field page table
	 * @return void
	 */
	protected function tableBodyHTML()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		$query = current_user_can( 'administrator' ) ? "SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS ORDER BY stack_order ASC" : "SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS WHERE display_in_infowindows = 1 OR display_in_marker_listings = 1 ORDER BY stack_order ASC;";
		$fields = $wpdb->get_results($query);
		

		foreach($fields as $index => $obj)
		{
			?>
			<tr>
				<td>
					<input type="hidden" name="stack_order[]" value="<?php echo !isset($obj->stack_order) ? $index : $obj->stack_order ?>">
					<i class="fa fa-bars handle"></i>
				</td>
				<td>
					<input readonly name="ids[]" value="<?php echo $obj->id; ?>"/>
				</td>
				<td>
					<input name="names[]" value="<?php echo addslashes($obj->name); ?>"/>
				</td>
				<td>
					<div class="wpgmza-custom-fields__iconpicker-wrap">
						<input class="wpgmza-fontawesome-iconpicker" name="icons[]" value="<?php echo $obj->icon; ?>"/>
					</div>
				</td>
				<td>
					<?php
					
					$this->attributeTableHTML($obj);
					
					?>
				</td>
				<td>
					<?php
					$options = array(
						'none'			=> 'None',
						'text'			=> 'Text',
						'dropdown'		=> 'Dropdown',
						'checkboxes'	=> 'Checkboxes',
						'time'			=> 'Time Range',
						'date'			=> 'Date Range'
					);
					?>
				
					<select name="widget_types[]">
						<?php
						foreach($options as $value => $text)
						{
							?>
							<option value="<?php echo $value; ?>"
							<?php
							if($obj->widget_type == $value)
								echo ' selected="selected"';
							?>
								>
								<?php echo __($text, 'wp-google-maps'); ?>
							</option>
							<?php
						}
						
						// Use this filter to add options to the dropdown
						$custom_options = apply_filters('wpgmza_custom_fields_widget_type_options', $obj);
						
						if(is_string($custom_options))
							echo $custom_options;
						
						?>
					</select>
				</td>
				<td>
					<label class="wpgmza-display-in-infowindows fa <?php echo $obj->display_in_infowindows ? 'fa-eye' : 'fa-eye-slash'; ?>" for="wpgmza-display-in-infowindows-<?php echo $obj->id; ?>"></label>
					<input type="checkbox" name="display_in_infowindows[<?php echo $obj->id; ?>]" id="wpgmza-display-in-infowindows-<?php echo $obj->id; ?>" class="wpgmza-toggle-infowindow-display-input" <?php echo $obj->display_in_infowindows ? 'checked' : ''; ?> value="<?php echo $obj->id; ?>" />
				</td>
				<td>
					<label class="wpgmza-display-in-marker-listings fa <?php echo $obj->display_in_marker_listings ? 'fa-eye' : 'fa-eye-slash'; ?>" for="wpgmza-display-in-marker-listings<?php echo $obj->id; ?>"></label>
					<input type="checkbox" name="display_in_marker_listings[<?php echo $obj->id; ?>]" id="wpgmza-display-in-marker-listings<?php echo $obj->id; ?>" class="wpgmza-toggle-marker-listing-display-input" <?php echo $obj->display_in_marker_listings ? 'checked' : ''; ?> value="<?php echo $obj->id; ?>" />
				</td>
				<td>
					<button type='button' class='button wpgmza-delete-custom-field'><i class='fa fa-trash-o' aria-hidden='true'></i></button>
				</td>
			</tr>
			<?php
		}
	}
	
	/**
	 * Echos the custom fields page
	 * @return void
	 */
	public function html()
	{
		$nonce = wp_create_nonce('wpgmza');
		
		?>
		
		<form id="wpgmza-custom-fields" 
			action="<?php echo admin_url('admin-post.php'); ?>" 
			method="POST"
			class="wrap">
			
			<input name="action" value="wpgmza_save_custom_fields" type="hidden"/>
			<input name="security" value="<?php echo $nonce; ?>" type="hidden"/>
			
			<h1>
				<?php
				_e('WP Google Maps - Custom Fields', 'wp-google-maps');
				?>
			</h1>
			
			<table id="wpgmza-custom-fields-table" class="wp-list-table widefat fixed striped pages">
				<thead>
					<tr>
						<th id="custom-order">
							<?php
							_e('Order', 'wp-google-maps');
							?>
						</th>
						<th scope="col" id="id" class ="manage-column column-id">
							<?php
							_e('ID', 'wp-google-maps');
							?>
						</th>
						<th scope="col" id="id" class ="manage-column column-id">
							<?php
							_e('Name', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Icon', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Attributes', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Filter Type', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('InfoWindows', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Marker Listings', 'wp-google-maps');
							?>
						</th>
						<th>
							<?php
							_e('Actions', 'wp-google-maps');
							?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$this->tableBodyHTML();
					?>
					
					<tr id="wpgmza-new-custom-field">
						<td>
							
						</td>
						<td>
							<input 
								name="ids[]"
								value="-1"
								readonly
								/>
						</td>
						<td>
							<input
								required
								name="names[]"
								/>
						</td>
						<td>
							<div class="wpgmza-custom-fields__iconpicker-wrap">
								<input name="icons[]" class="wpgmza-fontawesome-iconpicker"/>
							</div>
						</td>
						<td>
							<input name="attributes[]" type="hidden"/>
							<table class="attributes">
								<tbody>
									<tr>
										<td>
											<input
												placeholder="<?php _e('Name', 'wp-google-maps'); ?>"
												class="attribute-name"
												/>
										</td>
										<td>
											<input 
												placeholder="<?php _e('Value', 'wp-google-maps'); ?>"
												class="attribute-value"
												/>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td>
							<select name="widget_types[]">
								<option value="none">
									<?php
									_e('None', 'wp-google-maps');
									?>
								</option>
								<option value="text">
									<?php
									_e('Text', 'wp-google-maps');
									?>
								</option>
								<option value="dropdown">
									<?php
									_e('Dropdown', 'wp-google-maps');
									?>
								</option>
								<option value="checkboxes">
									<?php
									_e('Checkboxes', 'wp-google-maps');
									?>
								</option>
								<option value="time">
									<?php
									_e('Time Range', 'wp-google-maps');
									?>
								</option>
								<option value="date">
									<?php
									_e('Date Range', 'wp-google-maps');
									?>
								</option>
								<?php
								// Use this filter to add options to the dropdown
								echo apply_filters('wpgmza_custom_fields_widget_type_options', '');
								?>
							</select>
						</td>
						<td>
							<label class="wpgmza-display-in-infowindows fa fa-eye" for="wpgmza-display-in-infowindows--1"></label>
							<input type="checkbox" name="display_in_infowindows[-1]" id="wpgmza-display-in-infowindows--1" class="wpgmza-toggle-infowindow-display-input" checked value="1"/>
						</td>
						<td>
							<label class="wpgmza-display-in-marker-listings fa fa-eye" for="wpgmza-display-in-marker-listings--1"></label>
							<input type="checkbox" name="display_in_marker_listings[-1]" id="wpgmza-display-in-marker-listings--1" class="wpgmza-toggle-marker-listing-display-input" checked value="1"/>
						</td>
						<td>
							<button type="submit" class="button button-primary wpgmza-add-custom-field">
								<?php
								_e('Add', 'wp-google-maps');
								?>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="wpgmza-buttons__right">
				<input 
					type="submit" 
					id="wpgmza-custom-fields__save-btn"
					class="button button-primary" 
					value="<?php _e('Save', 'wp-google-maps'); ?>"
					/>
			</div>
		</form>
		
		<?php
	}
}

// Bind post listener
add_action('admin_post_wpgmza_save_custom_fields', array('WPGMZA\\CustomFieldsPage', 'POST'));

// Display function for menu hook
function show_custom_fields_page()
{
	$page = new CustomFieldsPage();
	$page->html();
}

