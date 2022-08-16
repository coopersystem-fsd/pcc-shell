/**
 * @namespace WPGMZA
 * @module GoogleProDrawingManager
 * @requires WPGMZA.ProDrawingManager
 */
jQuery(function($) {
	
	WPGMZA.GoogleProDrawingManager = function(map)
	{
		var self = this;
		
		WPGMZA.ProDrawingManager.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.GoogleProDrawingManager, WPGMZA.ProDrawingManager);
	
});