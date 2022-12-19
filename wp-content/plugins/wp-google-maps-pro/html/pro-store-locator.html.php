<!-- This HTML fragment contains elements to add to the basic store locator -->
<div class="wpgmza-keywords">
	<label data-name="keywordsLabel" class="wpgmza-keywords"></label>
	<input type="text" class="wpgmza-keywords"/>
</div>

<div class="wpgmza-category-filter-container">
	<label class="wpgmza-category">
		<?php
		esc_html_e("Category", "wp-google-maps");
		?>:
	</label>
</div>

<div class="wpgmza-reset">
	<input 
		class="wpgmza-reset" 
		type="button" 
		value="<?php esc_attr_e("Reset", "wp-google-maps") ?>"/>
</div>