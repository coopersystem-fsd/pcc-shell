/**
 * @namespace WPGMZA
 * @module OLDirectionsService
 * @requires WPGMZA.DirectionsService
 */
jQuery(function($) {
	
	WPGMZA.OLDirectionsService = function(map)
	{
		WPGMZA.DirectionsService.apply(this, arguments);
		
		this.apiKey = WPGMZA.settings.open_route_service_key;
	}
	
	WPGMZA.extend(WPGMZA.OLDirectionsService, WPGMZA.DirectionsService);
	
	WPGMZA.OLDirectionsService.prototype.geocodeWaypoints = function(waypoints, callback)
	{
		var geocoder = WPGMZA.Geocoder.createInstance();
		var index = 0;
		var coordinates = [];
		
		function geocodeNextWaypoint()
		{
			geocoder.geocode({address: waypoints[index]}, function(results) {
				
				if(!results.length)
					coordinates.push(WPGMZA.DirectionsService.NOT_FOUND);
				else
					coordinates.push(
						[
							results[0].latLng.lng,
							results[0].latLng.lat
						]
					);
				
				if(++index == waypoints.length)
					callback(coordinates);
				else
					geocodeNextWaypoint();
				
			});
		}
		
		geocodeNextWaypoint();
	}
	
	WPGMZA.OLDirectionsService.prototype.route = function(request, callback)
	{
		var self = this;
		var profile, url;
		var translated = {};
		
		// URL and Travel mode
		switch(request.travelMode)
		{
			case WPGMZA.DirectionsService.WALKING:
				profile = "foot-walking";
				break;
			
			case WPGMZA.DirectionsService.BICYCLING:
				profile = "cycling-regular";
				break;
			
			case WPGMZA.DirectionsService.TRANSIT:
				console.warn("Public transport profile is not supported by OpenRouteService");
			
			default:
				profile = "driving-car";
				break;
		}

		/*
		 * Cast local distance to Google Unit System
		*/
		if(request.unitSystem === WPGMZA.Distance.KILOMETERS){
			translated.units = "km";
		} else {
			translated.units = "mi";
		}
		
		url = "https://api.openrouteservice.org/v2/directions/" + profile;
		
		// Coordinates
		var waypoints = [request.origin];
		
		if(request.waypoints)
			request.waypoints.forEach(function(obj) {
				waypoints.push(obj.location);
			});
		
		waypoints.push(request.destination);
		
		this.geocodeWaypoints(waypoints, function(coordinates) {
			
			for(var i = 0; i < coordinates.length; i++)
			{
				if(coordinates[i] == WPGMZA.DirectionsService.NOT_FOUND)
				{
					var response = {
						geocoded_waypoints: []
					};
					
					for(var i = 0; i < waypoints.length; i++)
					{
						response.geocoded_waypoints.push({
							geocoder_status: coordinates[i]
						});
					}
					
					callback(response, WPGMZA.DirectionsService.NOT_FOUND);
					return;
				}
			}
			
			translated.coordinates = coordinates;
			switch(WPGMZA.locale.substr(0, 2))
			{
				case "de":
				case "en":
				case "pt":
				case "ru":
				case "hu":
				case "fr":
				case "it":
				case "cn":
				case "dk":
				case "de":
					translated.language = WPGMZA.locale.substr(0, 2);
					break;
				default:
					break;
			}
			
			$.ajax(url, {
				method: "POST",
				dataType: "json",
				contentType: "application/json; charset=utf-8",
				data: JSON.stringify(translated),
				beforeSend: function(xhr) {
					xhr.setRequestHeader('Authorization', self.apiKey);
				},
				success: function(response, status, xhr) {
					
					var status;
					var data = {
						originalResponse: response
					};
					
					if(response.routes && response.routes.length > 0)
						status = WPGMZA.DirectionsService.SUCCESS;
					else
						status = WPGMZA.DirectionsService.ZERO_RESULTS;
					
					callback(response, status);
					
					var event = new WPGMZA.Event({
						type: "directionsserviceresult",
						response: response,
						status: status
					});
					
					self.map.trigger(event);
					
				}
			});
			
		});
	}
	
});