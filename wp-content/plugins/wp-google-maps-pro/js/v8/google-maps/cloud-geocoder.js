/**
 * @namespace WPGMZA
 * @module CloudGeocoder
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.CloudGeocoder = function()
	{
		
	}
	
	WPGMZA.CloudGeocoder.SUCCESS = "success";
	
	WPGMZA.CloudGeocoder.prototype.geocode = function(options, callback)
	{
		WPGMZA.cloudAPI.call("/geocode", {
			data: options,
			success: function(results, status) {
				
				if(!results)
				{
					callback(results, WPGMZA.GeocoderStatus.FAIL);
					return;
				}
				
				results.forEach(function(result) {
					
					result.geometry.location = new google.maps.LatLng(
						result.geometry.location.lat,
						result.geometry.location.lng
					);
					
				});
				
				if(results.length == 0)
					status = WPGMZA.Geocoder.ZERO_RESULTS;
				
				callback(results, status);
				
			}
		});
	}
	
});