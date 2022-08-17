<div id="wpgmza-directions-box-settings">
	<h3><?php _e("Directions:", "wp-google-maps"); ?></h3>
	<fieldset id="open-route-service-key-notice" class="wpgmza-no-flex">
		<?php
		_e('This feature requires an API key from OpenRouteService to function. Please obtain a key from <a href="https://openrouteservice.org/dev/#/home">the OpenRouteService Developer Console</a> and paste your key into Maps &rarr; Settings &rarr; Advanced in the "OpenRouteService API key" field.', 'wp-google-maps')
		?>		
	</fieldset>

	<fieldset>
		<legend>
			<?php
			esc_html_e("Enable Directions", "wp-google-maps");
			?>:
		</legend>
		<span class='switch'>
			<input type='checkbox' 
				id='directions_enabled' 
				name='directions_enabled'
				class="postform cmn-toggle cmn-toggle-yes-no"/>
			<label class='cmn-override-big-wide' 
				for='directions_enabled' 
				data-on='<?php esc_attr_e("Yes", "wp-google-maps"); ?>' 
				data-off='<?php esc_attr_e("No", "wp-google-maps"); ?>'>
			</label>
		</span>
	</fieldset>
	
	<fieldset id="wpgmza-directions-box-style">
		<legend>
			<?php
			esc_html_e("Directions Box Style", "wp-google-maps");
			?>
		</legend>
		<span>
			<ul>
				<li>
					<input name="directions_box_style"
						type="radio"
						value="default"
						checked/>
					<?php
					esc_html_e('Default', 'wp-google-maps');
					?>
				</li>
				<li>
					<input name="directions_box_style"
						type="radio"
						value="modern"/>
					<?php
					esc_html_e('Modern', 'wp-google-maps');
					?>
				</li>
			</ul>
		</span>
	</fieldset>
	
	<fieldset id="wpgmza-directions-box-open-by-default">
		<legend>
			<?php
			esc_html_e("Directions Box Open by Default?","wp-google-maps");
			?>
		</legend>
		<select id="wpgmza_dbox" name="dbox" class="postform">
			<option value="1">
				<?php
				esc_html_e("No", "wp-google-maps");
				?>
			</option>
			<option value="2">
				<?php
				esc_html_e("Yes, on the left", "wp-google-maps");
				?>
			</option>
			<option value="3">
				<?php
				esc_html_e("Yes, on the right", "wp-google-maps");
				?>
			</option>
			<option value="4">
				<?php
				esc_html_e("Yes, above", "wp-google-maps");
				?>
			</option>
			<option value="5">
				<?php
				esc_html_e("Yes, below", "wp-google-maps");
				?>
			</option>
		</select>
	</fieldset>

	<fieldset id="wpgmza-directions-box-width">
		<legend>
			<?php
			esc_html_e("Directions Box Width", "wp-google-maps");
			?>
		</legend>
		<span>
			<input id="dbox_width"
				name="dbox_width"
				size="4"
				maxlength="4"
				value="100"
				class="small-text"/>
			<select name="wpgmza_dbox_width_type">
				<option value="%">
					<?php
					esc_html_e('%', 'wp-google-maps');
					?>
				</option>
				<option value="px">
					<?php
					esc_html_e('px', 'wp-google-maps');
					?>
				</option>
			</select>
		</span>
	</fieldset>
	
	<fieldset>
		<legend>
			<?php
			_e("Default 'To' address", "wp-google-maps");
			?>
		</legend>
		<input name="default_to" class="wpgmza-address"/>
	</fieldset>
	
	<fieldset>
		<legend>
			<?php
			_e("Default 'From' address", "wp-google-maps");
			?>
		</legend>
		<input name="default_from" class="wpgmza-address"/>
	</fieldset>
	
	<fieldset>
		<legend>
			<?php
			_e('Directions behaviour', 'wp-google-maps');
			?>
		</legend>
		<ul>
			<li>
				<input name="directions_behaviour" type="radio" value="default" checked/>
				<?php
				esc_html_e('Default', 'wp-google-maps');
				?>
				<div>
					<small class="wpgmza-info__small">
						<?php
						esc_html_e('Display directions on the page', 'wp-google-maps');
						?>
					</small>
				</div>
			</li>
			<li>
				<input name="directions_behaviour" type="radio" value="external"/>
				<?php
				esc_html_e('External', 'wp-google-maps');
				?>
				<div>
					<small class="wpgmza-info__small">
						<?php
						esc_html_e('Open Google / Apple maps in a new tab', 'wp-google-maps');
						?>
					</small>
				</div>
			</li>
			<li>
				<input name="directions_behaviour" type="radio" value="intelligent"/>
				<?php
				esc_html_e('Intelligent', 'wp-google-maps');
				?>
				<div>
					<small class="wpgmza-info__small">
						<?php
						esc_html_e('Display directions on the page on desktop devices, open Google / Apple maps mobile app on mobile devices', 'wp-google-maps');
						?>
					</small>
				</div>
			</li>
			<li>
				<input name="force_google_directions_app" type="checkbox"/>
				<?php
				esc_html_e('Force Google Maps mobile app', 'wp-google-maps');
				?>
				<div>
					<small class="wpgmza-info__small">
						<?php
						esc_html_e('Force iOS devices to use the Google Maps mobile app for directions', 'wp-google-maps');
						?>
					</small>
				</div>
			</li>
		</ul>
	</fieldset>


	<fieldset>
		<legend>
			<?php
			esc_html_e("Origin icon picker", "wp-google-maps");
			?>
		</legend>
		<div id="directions_origin_icon_picker_container"></div>
   </fieldset>

   <fieldset>
		<legend>
			<?php

			esc_html_e("Destination Icon Picker", "wp-google-maps");
			?>
		</legend>
    <div id="directions_destination_icon_picker_container"></div>
   </fieldset>
	
	<fieldset>
		<legend>
			<?php
			esc_html_e("Directions route color", "wp-google-maps");
			?>
		</legend>
		<div>
			<input id="directions_route_stroke_color"
				type="color"
				value="#4F8DF5"
				name="directions_route_stroke_color"/>
		</div>
	</fieldset>

  <fieldset>
    <legend>
      <?php
			esc_html_e("Directions route weight", "wp-google-maps");
			?>
		</legend>
		<div>
			<input id="directions_route_stroke_weight"
				type="number"
				name="directions_route_stroke_weight"
				max="100"
				min="1"
				value="4"
				class="small-text"/>
		</div>
	</fieldset>

	<fieldset>
		<legend>
			<?php
			esc_html_e("Directions route opacity", "wp-google-maps");
			?>
		</legend>
		<div>
			<input id="directions_route_stroke_opacity"
				type="number"
				name="directions_route_stroke_opacity"
				max="1"
				min="0"
				step="0.01"
				value="0.8"
				class="number"/>
		</div>
	</fieldset>

	<fieldset>
		<legend>
			<?php
			_e("Fit map bounds to route", "wp-google-maps");
			?>
		</legend>
		
		<div class='switch'>
			<input type='checkbox' 
				id='directions_fit_bounds_to_route' 
				name='directions_fit_bounds_to_route' 
				class='postform cmn-toggle cmn-toggle-round-flat'/>
			<label for='directions_fit_bounds_to_route'>
			</label>
		</div>
	</fieldset>
</div>