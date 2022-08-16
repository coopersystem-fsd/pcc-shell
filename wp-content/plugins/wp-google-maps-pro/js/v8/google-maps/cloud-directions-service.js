/**
 * @namespace WPGMZA
 * @module CloudDirectionsService
 * @requires WPGMZA.DirectionsService
 */
jQuery(function($) {
	
	WPGMZA.CloudDirectionsService = function(map)
	{
		WPGMZA.DirectionsService.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.CloudDirectionsService, WPGMZA.DirectionsService);
	
	WPGMZA.CloudDirectionsService.prototype.route = function(request, callback)
	{
		WPGMZA.cloudAPI.call("/directions", {
			
			data: request,
			success: function(response, status, xhr) {
				
				for(var key in request)
					response[key] = request[key];
				
				callback(response);
				
			}
			
		});
	}
	
});