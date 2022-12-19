<div class="wpgmza-directions-box">
	<h2 class="wpgmza-directions-box__title"><?php esc_html_e("Get Directions", "wp-google-maps"); ?></h2>
	
	<div class="wpgmza-directions-box-inner">
		<div class="wpgmza-directions-actions wpgmza-form-field">
			<div class="wpgmza-directions__travel-mode">
				<span class="wpgmza-travel-mode-option wpgmza-travel-option__selected" data-mode="driving">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_car.png'); ?>">
				</span>
				<span class="wpgmza-travel-mode-option" data-mode="walking">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_walking.png'); ?>">
				</span>
				<span class="wpgmza-travel-mode-option" data-mode="transit">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_transit.png'); ?>">
				</span>
				<span class="wpgmza-travel-mode-option" data-mode="bicycling">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_bike.png'); ?>">
				</span>
			</div>

			<div class="wpgmza-directions-locations">
				<div class="wpgmza-directions-from">

					<i class="wpgmza-directions-from__icon fa fa-circle"></i>
			
					<label class="wpgmza-form-field__label">
						<?php
						esc_html_e('From', 'wp-google-maps');
						?>
					</label>
					
					<input class="wpgmza-address wpgmza-directions-from wpgmza-form-field__input" type="text" placeholder="<?php esc_html_e('From', 'wp-google-maps'); ?>"/>
				
				</div>

				<div class="wpgmza-directions-to">

					<i class="wpgmza-directions-to__icon fa fas fa-map-marker"></i>

					<label class="wpgmza-form-field__label">
						<?php
						esc_html_e('To', 'wp-google-maps');
						?>
					</label>
					<input class="wpgmza-address wpgmza-form-field__input wpgmza-directions-to" type="text" placeholder="<?php esc_html_e('To', 'wp-google-maps'); ?>"/>
				</div>
				
				<div class='wpgmza-waypoint-via'>

					<i class="wpgmza-directions-from__icon fa fa-circle"></i>
				
					<input class="wpgmza-waypoint-via" type="text" placeholder="<?php esc_html_e('Via', 'wp-google-maps'); ?>"/>
					
					<button href="javascript: ;" class="wpgmza_remove_via">
						<i class="fa fa-times"></i>
					</button>
					
				</div>
				
				<div class='wpgmza-add-waypoint'>
					<div class='wpgmaps_add_waypoint'>
						<a href='javascript: ;' class='wpgmaps_add_waypoint'>
							<i class='fa fa-plus-circle' aria-hidden='true'></i>
							<?php 
							esc_html_e('Add Waypoint', 'wp-google-maps');
							?>
						</a>
					</div>
				</div>
			</div>

			<div class="wpgmza-hidden">
			<label class="wpgmza-travel-mode wpgmza-form-field__label">
				<?php
				esc_html_e('For', 'wp-google-maps');
				?>
			</label>
			<select class="wpgmza-travel-mode wpgmza-form-field__input">
				<option value="driving">
					<?php
					esc_html_e("Driving", "wp-google-maps");
					?>
				</option>
				<option value="walking">
					<?php
					esc_html_e("Walking", "wp-google-maps");
					?>
				</option>
				<option value="transit">
					<?php
					esc_html_e("Transit", "wp-google-maps");
					?>
				</option>
				<option value="bicycling">
					<?php
					esc_html_e("Bicycling", "wp-google-maps");
					?>
				</option>
			</select>
			</div>
		</div>
			
		<div class="wpgmza-directions-options__section">
			<a href="javascript:;" class="wpgmza-show-directions-options">
				<?php esc_html_e("show options","wp-google-maps"); ?>
			</a>
			
			<a href="javascript:;" class="wpgmza-hide-directions-options">
				<?php esc_html_e("hide options","wp-google-maps"); ?>
			</a>
			
			<div class="wpgmza-directions-options">
				<label>
					<input type="checkbox" class="wpgmza-avoid-tolls" value="tolls"/>
					<?php
					esc_html_e('Avoid Tolls', 'wp-google-maps');
					?>
				</label>
				<label>
					<input type="checkbox" class="wpgmza-avoid-highways" value="highways"/>
					<?php
					esc_html_e('Avoid Highways', 'wp-google-maps');
					?>
				</label>
				<label>
					<input type="checkbox" class="wpgmza-avoid-ferries" value="ferries"/>
					<?php
					esc_html_e('Avoid Ferries', 'wp-google-maps');
					?>
				</label>
			</div>
		</div>
		
	</div>
	
	<div class="wpgmza-directions-buttons">
		<input 
			class="wpgmza-get-directions"
			onclick="javascript: ;" 
			type="button" 
			value="<?php esc_html_e('Go', 'wp-google-maps') ?>"/>
		
		<span class="wpgmza-directions-result__buttons">
			<a class="wpgmza-print-directions"
				style="display: none;"
				onclick="javascript: ;">
				<?php 
				esc_html_e('Print directions', 'wp-google-maps') 
				?>
			</a>
			
			<a class="wpgmza-reset-directions"
				style="display: none;"
				onclick="javascript: ;">
				<?php 
				esc_html_e('Reset directions', 'wp-google-maps') 
				?>
			</a>
		</span>
	</div>
</div>