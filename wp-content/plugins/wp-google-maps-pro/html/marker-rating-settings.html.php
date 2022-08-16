<div id="marker-ratings">

	<h3>
		<?php
		esc_html_e('Marker Ratings', 'wp-google-maps');
		?>
	</h3>

	<fieldset>
		<label>
			<?php
			esc_html_e('Minimum Rating', 'wp-google-maps');
			?>
		</label>
		<input name="minimum_rating" type="number" min="0" value="1"/>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Maximum Rating', 'wp-google-maps');
			?>
		</label>
		<div>
			<input name="maximum_rating" type="number" min="1" value="5"/>
			<br/>
			<p>
				<strong>
					<?php
					esc_html_e('Please note that changing these settings will not scale ratings which have already been recorded.');
					?>
				</strong>
				<?php
				esc_html_e('We recommend that you do not modify the maximum and minimum settings after you begin accepting ratings.');
				?>
			</p>
		
			<p>
				<small>
					<?php
					esc_html_e('Example range settings:', 'wp-google-maps');
					?>
					<ul>
						<li>
							<?php
							esc_html_e('1 - 5 :- Traditional One to Five Stars', 'wp-google-maps');
							?>
						</li>
						<li>
							<?php
							esc_html_e('0 - 100 :- Percentage Rating', 'wp-google-maps');
							?>
						</li>
						<li>
							<?php
							esc_html_e('0 - 1 :- Like / Dislike, or Upvote / Downvote', 'wp-google-maps');
							?>
						</li>
					</ul>
				</small>
			</p>
		</div>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Rating Step', 'wp-google-maps');
			?>
		</label>
		<div>
			<input name="rating_step" type="number" step="any" min="0" value="1"/>
			<br/>
			<small>
				<strong>
					<?php
					esc_html_e('The "Stars" widget style does not currently support "Step"', 'wp-google-maps');
					?>
				</strong>
			</small>
			<br/>
			<small>
				<?php
				esc_html_e('Example step settings:', 'wp-google-maps');
				?>
				<ul>
					<li>
						<?php
						esc_html_e('0.01 :- Allow decimal / floating point ratings', 'wp-google-maps');
						?>
					</li>
					<li>
						<?php
						esc_html_e('1 :- Whole number ratings only', 'wp-google-maps');
						?>
					</li>
					<li>
						<?php
						esc_html_e('5 :- Round to nearest five (eg for percentage ratings)', 'wp-google-maps');
						?>
					</li>
				</ul>
			</small>
		</div>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Widget Style', 'wp-google-maps');
			?>
		</label>
		<div>
			<div>
				<label>
					<input name="marker_rating_widget_style" type="radio" value="radios" checked="checked"/>
				</label>
			</div>
			<div>
    			<label>
       				 <input name="marker_rating_widget_style" class="gradient_widget_style" type="radio" value="gradient"/>
		
					<!-- if you have selected the gradient style then this class will show -->
        			<div class="wpgmza-rating-widget-style-options">
					Bar rating gradient colours:
					<br>
					<!-- start color for radient bar -->
					Start - <input name="marker_rating_gradient_widget_start_color" type="color" value="#ff0000"/> 
					<!-- End color for radient bar -->
            		End - <input name="marker_rating_gradient_widget_end_color" type="color" value="#00ff00"/>
        			</div>
    			</label>
			</div>
			<div>
				<label>
					<input name="marker_rating_widget_style" type="radio" value="stars"/>
				</label>
			</div>
			<div>
				<label>
					<input name="marker_rating_widget_style" type="radio" value="thumbs"/>
					<!-- if you have selected the thumbs style then this class will show -->
					<div class="wpgmza-rating-widget-style-options">
						Background color:
						<br>
						<!-- Allows you to choose a background color -->
						 <input name="marker_rating_thumb_widget_average_rating_color" type="color" value="#4285F4"/>
        			</div>
				</label>
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<label>
			<?php
			esc_html_e('Tampering Countermeasures', 'wp-google-maps');
			?>
		</label>
		<div>
			<div>
				<label>
					<input name="marker_rating_tampering_countermeasures" type="radio" value="basic-only" checked="checked"/>
					<?php
					esc_html_e('Basic Only', 'wp-google-maps');
					?>
					<div>
						<small>
							<?php
							esc_html_e('Uses basic client side countermeasures. This is easily circumvented by anyone with technical knowledge, and is vulnerable to bots.', 'wp-google-maps');
							?>
							<strong>
								<?php
								esc_html_e('This method is not reliable for preventing tampering, and is not recommended for use cases where reliability is imperative.', 'wp-google-maps');
								?>
							</strong>
						</small>
					</div>
				</label>
			</div>
			<!--<div>
				<label>
					<input name="marker_rating_tampering_countermeasures" type="radio" value="anti-spam"/>
					<?php
					esc_html_e('Anti-Spam', 'wp-google-maps');
					?>
					<div>
						<small>
							<?php
							esc_html_e('Uses anti-spam countermeasures and basic server side countermeasures. This can be circumvented with human interaction, but is significantly less vulnerable to bots.', 'wp-google-maps');
							?>
							<strong>
								<?php
								esc_html_e('This method is not reliable for preventing tampering, and is not recommended for use cases where reliability is imperative.', 'wp-google-maps');
								?>
							</strong>
						</small>
					</div>
				</label>
			</div>-->
			<div>
				<label>
					<input name="marker_rating_tampering_countermeasures" type="radio" value="require-login"/>
					<?php
					esc_html_e('Require Account', 'wp-google-maps');
					?>
					<div>
						<small>
							<?php
							esc_html_e('This method uses the same countermeasures as Basic Only, however it does require that in order to leave a rating, the user must be logged in.', 'wp-google-maps');
							?>
							<strong>
								<?php
								esc_html_e('This method requires login and associates ratings with a users account. You can compliment this by using security methods (eg 3rd party plugins) to prevent fake accounts.', 'wp-google-maps');
								?>
							</strong>
						</small>
					</div>
				</label>
			</div>
		</div>
	</fieldset>
</div>