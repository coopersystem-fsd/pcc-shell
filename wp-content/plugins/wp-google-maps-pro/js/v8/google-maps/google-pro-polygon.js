/**
 * @namespace WPGMZA
 * @module GoogleProPolygon
 * @requires WPGMZA.GooglePolygon
 */
jQuery(function($) {
	
	WPGMZA.GoogleProPolygon = function(row, googlePolygon)
	{
		var self = this;
		
		WPGMZA.GooglePolygon.call(this, row, googlePolygon);
		
		google.maps.event.addListener(this.googlePolygon, "mouseover", function(event) {
			self.trigger("mouseover");
		});
		
		google.maps.event.addListener(this.googlePolygon, "mouseout", function(event) {
			self.trigger("mouseout");
		});
	}
	
	WPGMZA.GoogleProPolygon.prototype = Object.create(WPGMZA.GooglePolygon.prototype);
	WPGMZA.GoogleProPolygon.prototype.constructor = WPGMZA.GoogleProPolygon;
	
});