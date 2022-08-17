<div class="wpgmza-marker-icon-picker wpgmza-flex">

	<div class="wpgmza-marker-icon-preview"></div>
	<input class="wpgmza-marker-icon-url" type="hidden"/>
	
	<button type="button" class="wpgmza-retina">
		<label title="<?php esc_attr_e("This is a retina ready marker","wp-google-maps"); ?>">
			<input 
				type="checkbox" 
				name="retina"
				data-ajax-name="retina"
				/>
			<?php
			esc_html_e('Retina Ready', 'wp-google-maps');
			?>
		</label>
	</button>
	
	<button type="button" class="wpgmza-upload button button-primary">
		<?php
		esc_html_e('Upload Image', 'wp-google-maps');
		?>
	</button>
	<button type="button" class="wpgmza-marker-library button button-primary">
		<?php
		esc_html_e('Marker Library', 'wp-google-maps');
		?>
	</button>
	<button type="button" class="wpgmza-reset button button-primary">
		<?php
		esc_html_e('Reset', 'wp-google-maps');
		?>
	</button>
	
</div>