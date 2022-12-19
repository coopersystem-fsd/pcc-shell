<?php

require_once(plugin_dir_path(__DIR__) . 'includes/custom-fields/class.custom-fields.php');

global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;

?>

<div id="marker-filtering" class="wpgmza-no-flex">

	<h3><?php _e('Marker Filtering', 'wp-google-maps'); ?>:</h3>

	<fieldset>
		<label>
			<?php
			_e('Enable custom field filtering on', 'wp-google-maps');
			?>
		</label>
		<div>
			<p id="wpgmza-marker-filtering-tab-no-custom-fields-warning" class="notice notice-warning">
				<?php
				_e('You have no custom fields to filter on. Please add some in order to add custom field filters.', 'wp-google-maps');
				?>
			</p>
		
			<ul>
			</ul>
		</div>
	</fieldset>
	
</div>