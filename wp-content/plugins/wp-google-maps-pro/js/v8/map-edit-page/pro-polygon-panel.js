/**
 * @namespace WPGMZA
 * @module ProPolygonPanel
 * @requires WPGMZA.PolygonPanel
 */
jQuery(function($) {
	
	WPGMZA.ProPolygonPanel = function(element)
	{
		WPGMZA.PolygonPanel.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.ProPolygonPanel, WPGMZA.PolygonPanel);
	
});