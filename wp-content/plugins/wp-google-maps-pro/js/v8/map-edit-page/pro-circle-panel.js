/**
 * @namespace WPGMZA
 * @module ProCirclePanel
 * @requires WPGMZA.CirclePanel
 */
jQuery(function($) {
	
	WPGMZA.ProCirclePanel = function(element)
	{
		WPGMZA.CirclePanel.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.ProCirclePanel, WPGMZA.CirclePanel);
	
});