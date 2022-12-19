/**
 * @namespace WPGMZA
 * @module ProLatLng
 * @requires WPGMZA.LatLng
 */
jQuery(function($) {
	
	WPGMZA.LatLng.fromJpeg = function(src, callback)
	{
		var img = new Image();
		
		img.onload = function() {
			
			EXIF.getData(img, function() {
				
				var aLat = EXIF.getTag(img, "GPSLatitude");
				var aLng = EXIF.getTag(img, "GPSLongitude");
				
				if(!(aLat && aLng))
				{
					callback(null);
					return;
				}
				
				var latRef = EXIF.getTag(img, "GPSLatitudeRef") || "N";
				var lngRef = EXIF.getTag(img, "GPSLongitudeRef") || "W";
				
				var fLat = (aLat[0] + aLat[1] / 60 + aLat[2] / 3600) * (latRef == "N" ? 1 : -1);
				var fLng = (aLng[0] + aLng[1] / 60 + aLng[2] / 3600) * (lngRef == "W" ? -1 : 1);
				
				callback(new WPGMZA.LatLng({
					lat: fLat,
					lng: fLng
				}));
				
			});
			
		}
		
		img.src = src;
	}
	
	// When reverse geocoding JPEG EXIF GPS coordinates to an address, this is the threshold for accuracy
	WPGMZA.LatLng.EXIF_ADDRESS_GEOCODE_KM_THRESHOLD = 0.5;
	
	$(document.body).on("click", ".wpgmza-get-location-from-picture[data-source][data-destination]", function(event) {
		
		var style, m, url;
		var source = $(this).attr("data-source");
		var dest = $(this).attr("data-destination");
		
		var lat = $(this).attr("data-destination-lat");
		var lng = $(this).attr("data-destination-lng");
		
		if(!$(source).length){
			alert(WPGMZA.localized_strings.no_picture_found);
			throw new Error("Source element not found");
		}
		
		if(!$(dest).length){
			throw new Error("Destination element not found");
		}
		
		if($(source).is("img")){
			url = $(source).attr("src");
		} else {
			style = $(source).css("background-image");
			
			if(!(m = style.match(/url\(["'](.+)["'"]\)/))){
				throw new Error("No background image found");
			}
			
			url = m[1];
		}
		
		if(!url || url.length == 0)
			alert(WPGMZA.localized_strings.no_picture_found);
		
		WPGMZA.LatLng.fromJpeg(url, function(jpegLatLng) {
			
			if(!jpegLatLng)
			{
				// No coordinates found, inform the user and bail out
				alert(WPGMZA.localized_strings.no_gps_coordinates);
				return;
			}
			
			// Fill the destination with the coordinates
			$(dest).val(jpegLatLng.toString());
			
			// Fill the lat and lng fields if applicable
			if(lat && lng)
			{
				$(lat).val(jpegLatLng.lat);
				$(lng).val(jpegLatLng.lng);
			}
			
			if(WPGMZA.settings.useRawJpegCoordinates)
				return;
			
			// Attempt to get the address from these coordinates
			var geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.getAddressFromLatLng({
				latLng: jpegLatLng
			}, function(results, status) {
				
				// Failed to get the address, coordinates will be used
				if(status != WPGMZA.Geocoder.SUCCESS)
					return;
				
				// We have an address
				var address = results[0];
				
				// Let's geocode this address and see how close that address is to the raw GPS coordinates
				geocoder.getLatLngFromAddress({
					address: address
				}, function(results, status) {
					
					// Failed to geocode the found address (this should not happen)
					if(status != WPGMZA.Geocoder.SUCCESS)
						return;
					
					// Find the distance in KM between the raw GPS point and the geocoded address
					var addressLatLng = new WPGMZA.LatLng(results[0].latLng);
					var kmOffset = WPGMZA.Distance.between(addressLatLng, jpegLatLng);
					
					// If it's below the threshold, use the address instead of raw coordinates
					if(kmOffset <= WPGMZA.LatLng.EXIF_ADDRESS_GEOCODE_KM_THRESHOLD)
						$(dest).val(address);
					
				});
				
			});
			
		});
		
	});
	
});