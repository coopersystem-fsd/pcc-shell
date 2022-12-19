/**
 * @namespace WPGMZA
 * @module OLProDrawingManager
 * @requires WPGMZA.ProDrawingManager
 */
jQuery(function($) {
	
	WPGMZA.OLProDrawingManager = function()
	{
		WPGMZA.ProDrawingManager.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.OLProDrawingManager, WPGMZA.ProDrawingManager);
	
});