/**
 * @namespace WPGMZA
 * @module DirectionsRenderer
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.DirectionsRenderer = function(map)
	{
		WPGMZA.EventDispatcher.apply(this, arguments);
		
		this.map = map;
	}
	
	WPGMZA.extend(WPGMZA.DirectionsRenderer, WPGMZA.EventDispatcher);
	
	WPGMZA.DirectionsRenderer.createInstance = function(map)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLDirectionsRenderer(map);
				break;
			
			default:
			
				if(WPGMZA.CloudAPI.isBeingUsed)
					return new WPGMZA.CloudDirectionsRenderer(map);
				else
					return new WPGMZA.GoogleDirectionsRenderer(map);
				
				break;
		}
	}
	
	WPGMZA.DirectionsRenderer.prototype.getPolylineOptions = function()
	{
		var settings = {
			strokeColor: "#4285F4",
			strokeWeight: 4,
			strokeOpacity: 0.8
		}

		if(this.map.settings.directions_route_stroke_color){
			settings.strokeColor = this.map.settings.directions_route_stroke_color;
		}

		 if(this.map.settings.directions_route_stroke_weight){
		 	settings.strokeWeight = parseInt(this.map.settings.directions_route_stroke_weight);
		 }

		 if(this.map.settings.directions_route_stroke_opacity){
		 	settings.strokeOpacity = parseFloat(this.map.settings.directions_route_stroke_opacity);
		 }
		 
		 return settings;
	}
	
	WPGMZA.DirectionsRenderer.prototype.removeMarkers = function()
	{
		if (this.directionStartMarker)
			this.map.removeMarker(this.directionStartMarker);
		
		if (this.directionEndMarker)
			this.map.removeMarker(this.directionEndMarker);
	}
	
	WPGMZA.DirectionsRenderer.prototype.addMarkers = function(points)
	{
		this.directionStartMarker = WPGMZA.Marker.createInstance({
			position: points[0],
			icon: this.map.settings.directions_route_origin_icon,
			retina: this.map.settings.directions_origin_retina,
			disableInfoWindow: true
		});

		this.directionStartMarker._icon.retina = this.directionStartMarker.retina;
		
		this.map.addMarker(this.directionStartMarker);

		this.directionEndMarker = WPGMZA.Marker.createInstance({
			position: points[points.length - 1],
			icon: this.map.settings.directions_route_destination_icon,
			retina: this.map.settings.directions_destination_retina,
			disableInfoWindow: true
		});

		this.directionEndMarker._icon.retina = this.directionEndMarker.retina;

		this.map.addMarker(this.directionEndMarker);
	}
	
	WPGMZA.DirectionsRenderer.prototype.setDirections = function(directions){
		
	}

	WPGMZA.DirectionsRenderer.prototype.fitBoundsToRoute = function(pointA, pointB){
		var bounds = new WPGMZA.LatLngBounds();
		bounds.extend(pointA);
		bounds.extend(pointB);
		this.map.fitBounds(bounds);
	}
	
});