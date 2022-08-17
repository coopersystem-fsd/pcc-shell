/**
 * @namespace WPGMZA
 * @module ProRectanglePanel
 * @requires WPGMZA.RectanglePanel
 */
jQuery(function($) {
	
	WPGMZA.ProRectanglePanel = function(element)
	{
		WPGMZA.RectanglePanel.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.ProRectanglePanel, WPGMZA.RectanglePanel);
	
});