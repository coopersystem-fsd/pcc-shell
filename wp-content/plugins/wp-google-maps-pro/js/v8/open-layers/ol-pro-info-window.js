/**
 * @namespace WPGMZA
 * @module OLProInfoWindow
 * @requires WPGMZA.OLInfoWindow
 */
jQuery(function($) {
	
	WPGMZA.OLProInfoWindow = function(feature)
	{
		WPGMZA.OLInfoWindow.call(this, feature);

		var self = this;
		$(this.element).on('click', function(event){
			if(self.feature.map.settings.close_infowindow_on_map_click){
				event.stopPropagation();
				event.stopImmediatePropagation();
				return;
			}
		});
	}
	
	WPGMZA.OLProInfoWindow.prototype = Object.create(WPGMZA.OLInfoWindow.prototype);
	WPGMZA.OLProInfoWindow.prototype.constructor = WPGMZA.OLProInfoWindow;
	
	Object.defineProperty(WPGMZA.OLProInfoWindow.prototype, "panIntoViewOnOpen", {
		
		"get": function() {
			return this.style == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		}
		
	});
	
	WPGMZA.OLProInfoWindow.prototype.open = function(map, feature)
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
				return WPGMZA.OLInfoWindow.prototype.open.call(this, map, feature);
				break;
		}
	}

	WPGMZA.OLProInfoWindow.prototype.setPosition = function(position){
		var latLng = position.toLatLngLiteral();
		this.overlay.setPosition(ol.proj.fromLonLat([
			latLng.lng,
			latLng.lat
		]));
	}
	
});