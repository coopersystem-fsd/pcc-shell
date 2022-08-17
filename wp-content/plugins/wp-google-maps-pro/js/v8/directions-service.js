/**
 * @namespace WPGMZA
 * @module DirectionsService
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.DirectionsService = function(map)
	{
		WPGMZA.EventDispatcher.apply(this, arguments);
		
		this.map = map;
	}
	
	WPGMZA.extend(WPGMZA.DirectionsService, WPGMZA.EventDispatcher);
	
	WPGMZA.DirectionsService.ZERO_RESULTS	= "zero-results";
	WPGMZA.DirectionsService.NOT_FOUND		= "not-found";
	WPGMZA.DirectionsService.SUCCESS		= "success";
	
	WPGMZA.DirectionsService.DRIVING		= "driving";
	WPGMZA.DirectionsService.WALKING		= "walking";
	WPGMZA.DirectionsService.TRANSIT		= "transit";
	WPGMZA.DirectionsService.BICYCLING		= "bicycling";
	
	WPGMZA.DirectionsService.createInstance = function(map)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLDirectionsService(map);
			
			default:
				return new WPGMZA.GoogleDirectionsService(map);
		}
	}
	
	WPGMZA.DirectionsService.route = function(params, callback)
	{
		
	}
	
});