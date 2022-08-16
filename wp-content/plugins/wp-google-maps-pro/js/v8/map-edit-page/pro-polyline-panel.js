/**
 * @namespace WPGMZA
 * @module ProPolylinePanel
 * @requires WPGMZA.PolylinePanel
 */
jQuery(function($) {
	
	WPGMZA.ProPolylinePanel = function(element)
	{
		WPGMZA.PolylinePanel.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.ProPolylinePanel, WPGMZA.PolylinePanel);
	
});