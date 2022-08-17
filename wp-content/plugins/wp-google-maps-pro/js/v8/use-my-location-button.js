/**
 * @namespace WPGMZA
 * @module UseMyLocationButton
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.UseMyLocationButton = function(target, options)
	{
		var self = this;
		
		this.options = {};
		if(options)
			this.options = options;
		
		this.target = $(target);
		
		this.element = $("<button class='wpgmza-use-my-location button-secondary' type='button' title='" + WPGMZA.localized_strings.use_my_location + "'><i class='fa fa-crosshairs' aria-hidden='true'></i></button>");
		this.element.on("click", function(event) {
			self.onClick(event);
		});
	}
	
	WPGMZA.UseMyLocationButton.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.UseMyLocationButton.prototype.constructor = WPGMZA.UseMyLocationButton;
	
	WPGMZA.UseMyLocationButton.prototype.onClick = function(event)
	{
		var self = this;
		
		WPGMZA.getCurrentPosition(function(position) {
			
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			
			self.target.val(lat + ", " + lng);
			self.target.trigger("change");
			
			var geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.geocode({latLng: {lat: lat, lng: lng}}, function(results) {
				
				if(results && results.length)
					self.target.val(results[0]);
				
			});
			
		});
	}
	
});