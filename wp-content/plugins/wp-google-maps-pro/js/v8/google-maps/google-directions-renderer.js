/**
 * @namespace WPGMZA
 * @module GoogleDirectionsRenderer
 * @requires WPGMZA.DirectionsRenderer
 */
jQuery(function($) {
	
	WPGMZA.GoogleDirectionsRenderer = function(map)
	{
		WPGMZA.DirectionsRenderer.apply(this, arguments);
		
		this.map = map;
		
		this.googleDirectionsDisplay = new google.maps.DirectionsRenderer({
			map: map.googleMap,
			preserveViewport: true,
			draggable: true,
			suppressMarkers: true,
		});
		
		this.googleDirectionsDisplay.setPanel($("#directions_panel_" + map.id)[0]);
	}


	WPGMZA.extend(WPGMZA.GoogleDirectionsRenderer, WPGMZA.DirectionsRenderer);
	
	WPGMZA.GoogleDirectionsRenderer.prototype.setDirections = function(directions)
	{
		this.googleDirectionsDisplay.setDirections(directions.originalResponse);

		if(directions.routes && directions.routes[0] && directions.routes[0].legs && directions.routes[0].legs[0])
    {

			this.directionLeg = directions.routes[0].legs[0];

			this.directionStartMarker = WPGMZA.Marker.createInstance({
				position: new WPGMZA.LatLng( this.directionLeg.start_location.lat(), this.directionLeg.start_location.lng() ),
				icon: this.map.settings.directions_route_origin_icon ? this.map.settings.directions_route_origin_icon : "",
				retina: this.map.settings.directions_origin_retina,
				disableInfoWindow: true
			});

			this.directionStartMarker._icon.retina = this.directionStartMarker.retina;

			this.map.addMarker(this.directionStartMarker);

			this.directionEndMarker = WPGMZA.Marker.createInstance({
				position: new WPGMZA.LatLng( this.directionLeg.end_location.lat(), this.directionLeg.end_location.lng() ),
				icon: this.map.settings.directions_route_destination_icon ? this.map.settings.directions_route_destination_icon : "",
				retina: this.map.settings.directions_destination_retina,
				disableInfoWindow: true
			});

			this.directionEndMarker._icon.retina = this.directionEndMarker.retina;

			this.map.addMarker(this.directionEndMarker);
		}
		
		var options = {
			polylineOptions: {
			   strokeColor: "#4285F4"
			}
		};

		if(this.map.settings.directions_route_stroke_color)
			options.polylineOptions.strokeColor = this.map.settings.directions_route_stroke_color;

		if(this.map.settings.directions_route_stroke_weight)
			options.polylineOptions.strokeWeight = parseFloat(this.map.settings.directions_route_stroke_weight);
   
		if(this.map.settings.directions_route_stroke_opacity)
			options.polylineOptions.strokeOpacity = parseFloat(this.map.settings.directions_route_stroke_opacity);
	   
		this.googleDirectionsDisplay.setMap(this.map.googleMap);
		this.googleDirectionsDisplay.setOptions(options);

		if(this.map.settings.directions_fit_bounds_to_route){
			if(this.directionStartMarker && this.directionEndMarker){
				this.fitBoundsToRoute(this.directionStartMarker.getPosition(), this.directionEndMarker.getPosition());
			}
		}
	}

	WPGMZA.GoogleDirectionsRenderer.prototype.clear = function()
    {	

		this.googleDirectionsDisplay.setMap(null);
    
        if (this.directionStartMarker)
          this.map.removeMarker(this.directionStartMarker);

        if (this.directionEndMarker)
          this.map.removeMarker(this.directionEndMarker);

    }
	
});

