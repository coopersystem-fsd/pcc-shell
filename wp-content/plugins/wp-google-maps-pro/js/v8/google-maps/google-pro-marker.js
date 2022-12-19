/**
 * @namespace WPGMZA
 * @module GoogleProMarker
 * @requires WPGMZA.GoogleMarker
 */
jQuery(function($) {
	
	WPGMZA.GoogleProMarker = function(row)
	{
		WPGMZA.GoogleMarker.call(this, row);
	}
	
	WPGMZA.GoogleProMarker.prototype = Object.create(WPGMZA.GoogleMarker.prototype);
	WPGMZA.GoogleProMarker.prototype.constructor = WPGMZA.GoogleProMarker;
	
	WPGMZA.GoogleProMarker.prototype.onAdded = function(event)
	{
		WPGMZA.GoogleMarker.prototype.onAdded.apply(this, arguments);
		
		if(this.map.settings.wpgmza_settings_disable_infowindows)
			this.googleMarker.setOptions({clickable: false});
	}
	
	WPGMZA.GoogleProMarker.prototype.updateIcon = function()
	{
		var self = this;
		var icon = this._icon;

		if(icon.retina) {
			var img = new Image();
			
			img.onload = function(event) {
				
				var autoDetect = false;
				
				//var isSVG = icon.match(/\.svg/i);
				
				var size;
				
				if(!autoDetect) {
					size = new google.maps.Size(
						WPGMZA.settings.retinaWidth ? parseInt(WPGMZA.settings.retinaWidth) : Math.round(img.width / 2),
						WPGMZA.settings.retinaHeight ? parseInt(WPGMZA.settings.retinaHeight) : Math.round(img.height / 2)
					);
				} else {
					size = new google.maps.Size(
						Math.round(img.width / 2),
						Math.round(img.height / 2)
					);
				}
					
				self.googleMarker.setIcon(
					new google.maps.MarkerImage(icon.url, null, null, null, size)
				);
				
			};
			
			img.src = (icon.isDefault ? WPGMZA.defaultMarkerIcon : icon.url);
		}
		else
			this.googleMarker.setIcon(icon.url);
	}
	
});