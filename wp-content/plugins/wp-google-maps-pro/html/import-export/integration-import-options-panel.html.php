<h2>
	<?php esc_html_e('Import', 'wp-google-maps'); ?>
</h2>
<fieldset id="map-select-container"></fieldset>
<fieldset>
	<p>
		<input id="replace-map-data" name="replace-map-data" type="checkbox"/>
		<label for="replace-map-data">
			<?php
			esc_html_e('Replace map data', 'wp-google-maps');
			?>
		</label>
	</p>
</fieldset>
<fieldset>
	<p>
		<button id="import-integration" class="wpgmza_general_btn">
			<?php
			esc_html_e('Import', 'wp-google-maps');
			?>
		</button>
	</p>
</fieldset>