/**
 * @namespace WPGMZA
 * @module OLProPolygon
 * @requires WPGMZA.OLPolygon
 */
jQuery(function($) {
	
	WPGMZA.OLProPolygon = function(row, olFeature)
	{
		var self = this;
		
		WPGMZA.OLPolygon.call(this, row, olFeature);
	}
	
	WPGMZA.OLProPolygon.prototype = Object.create(WPGMZA.OLPolygon.prototype);
	WPGMZA.OLProPolygon.prototype.constructor = WPGMZA.OLProPolygon;
	
});