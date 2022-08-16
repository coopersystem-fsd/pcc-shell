/**
 * @namespace WPGMZA
 * @module GoogleProInfoWindow
 * @requires WPGMZA.GoogleInfoWindow
 */
jQuery(function($) {

	WPGMZA.GoogleProInfoWindow = function(feature)
	{
		WPGMZA.GoogleInfoWindow.call(this, feature);
	}
	
	WPGMZA.GoogleProInfoWindow.prototype = Object.create(WPGMZA.GoogleInfoWindow.prototype);
	WPGMZA.GoogleProInfoWindow.prototype.constructor = WPGMZA.GoogleProInfoWindow;

	WPGMZA.GoogleProInfoWindow.prototype.open = function(map, feature)
	{
		this.feature = feature;
		
		var style = (WPGMZA.currentPage == "map-edit" ? WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE : this.style);
		
		switch(style)
		{
			case WPGMZA.ProInfoWindow.STYLE_MODERN:
			case WPGMZA.ProInfoWindow.STYLE_MODERN_PLUS:
			case WPGMZA.ProInfoWindow.STYLE_MODERN_CIRCULAR:
			case WPGMZA.ProInfoWindow.STYLE_TEMPLATE:
				return WPGMZA.ProInfoWindow.prototype.open.call(this, map, feature);
				break;
			
			default:
				var result = WPGMZA.GoogleInfoWindow.prototype.open.call(this, map, feature);
				
				if(this.maxWidth && this.googleInfoWindow) // There will be no Google InfoWindow with Modern style marker listing selected
					this.googleInfoWindow.setOptions({maxWidth: this.maxWidth});
				
				return result;
				break;
		}
	}

	WPGMZA.GoogleProInfoWindow.prototype.setPosition = function(position){
		if(this.googleInfoWindow){
			this.googleInfoWindow.setPosition(position.toGoogleLatLng());
		}
	}
		
});