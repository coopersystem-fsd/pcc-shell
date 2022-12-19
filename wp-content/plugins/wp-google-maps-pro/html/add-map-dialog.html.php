<div class="remodal wpgmza-add-map-dialog" data-remodal-id="modal">
	<button data-remodal-action="close" class="remodal-close"></button>
	<h1><?php _e('Add Map', 'wp-google-maps'); ?></h1>
	
	<div class="wpgmza-add-map-dialog-inner-panels">
		<div>
			<div>
				<h2><?php _e('Existing Map', 'wp-google-maps'); ?></h2>
				<p>
					<?php
					
					$select = new WPGMZA\MapSelect();
					echo $select->html();
					
					?>
				</p>
			</div>
			<div class="wpgmza-center">
				<button data-remodal-action="confirm" class="remodal-confirm wpgmza-insert-map">
					<?php _e('Insert Map', 'wp-google-maps'); ?>
				</button>
			</div>
		</div>
		
		<div>
			<h2><?php _e('Quick Create', 'wp-google-maps'); ?></h2>
			<form>
				<fieldset>
					<input name="wpgmza-title" placeholder="<?php _e('Title', 'wp-google-maps'); ?>"/>
				</fieldset>
				<fieldset>
					<input name="wpgmza-address" placeholder="<?php _e('Address', 'wp-google-maps'); ?>"/>
				</fieldset>
				<!--<fieldset>
					<input name="wpgmza-icon" placeholder="<?php _e('Icon', 'wp-google-maps'); ?>"/>
					<br/>
					<small>
						<?php
						_e('Leave blank for the default', 'wp-google-maps');
						?>
					</small>
				</fieldset>-->
			</form>
			<div class="wpgmza-center">
				<button data-remodal-action="" class="remodal-confirm wpgmza-quick-create">
					<?php _e('Create Map', 'wp-google-maps'); ?>
				</button>
			</div>
		</div>
	</div>
</div>