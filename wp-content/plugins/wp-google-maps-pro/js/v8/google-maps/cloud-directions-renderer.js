/**
 * @namespace WPGMZA
 * @module CloudDirectionsRenderer
 * @requires WPGMZA.DirectionsRenderer
 */
jQuery(function($) {
	
	WPGMZA.CloudDirectionsRenderer = function(map)
	{
		WPGMZA.DirectionsRenderer.apply(this, arguments);
		
		this.panel = $("#directions_panel_" + map.id);
	}
	
	WPGMZA.extend(WPGMZA.CloudDirectionsRenderer, WPGMZA.DirectionsRenderer);
	
	WPGMZA.CloudDirectionsRenderer.maneuverToClassName = function(maneuver)
	{
		var map = {
			"turn-slight-left":		"slight-left",
			"turn-sharp-left":		"sharp-left",
			"uturn-left":			"sharp-left",
			"turn-left":			"left",
			"turn-slight-right":	"slight-right",
			"turn-sharp-right":		"sharp-right",
			"uturn-right":			"sharp-right",
			"turn-right":			"right",
			"straight":				"straight",
			"ramp-left":			"keep-left",
			"ramp-right":			"keep-right",
			// "merge":				"",
			"fork-left":			"keep-left",
			"fork-right":			"keep-right",
			// "ferry":				"",
			// "ferry-train":		"",
			"roundabout-left":		"enter-roundabout",
			"roundabout-right":		"enter-roundabout"
		};
		
		if(!map[maneuver])
			return "";
		
		return "wpgmza-instruction-type-" + map[maneuver];
	}
	
	WPGMZA.CloudDirectionsRenderer.prototype.clear = function()
	{
		this.removeMarkers();
		
		if(this.polyline)
		{
			this.map.removePolyline(this.polyline);
			delete this.polyline;
		}
		
		this.panel.html("");
	}
	
	WPGMZA.CloudDirectionsRenderer.prototype.setDirections = function(directions)
	{
		var self = this;
		var route = directions.routes[0];
		
		this.clear();
		
		if(!route)
			return;
		
		var path = [], points = [];
		var source = window.polyline.decode(route.overview_polyline.points);
		
		source.forEach(function(arr) {
			
			path.push(new google.maps.LatLng({
				lat: arr[0],
				lng: arr[1]
			}));
			
			points.push(new WPGMZA.LatLng({
				lat: arr[0],
				lng: arr[1]
			}));
			
		});
		
		var settings = this.getPolylineOptions();
		
		this.polyline = WPGMZA.Polyline.createInstance({
			settings: settings
		});
		
		this.polyline.googlePolyline.setOptions({
			path: path
		});
		
		this.map.addPolyline(this.polyline);
		
		this.addMarkers(points);
		
		// Panel
		var steps = [];
		
		if(route.legs)
			route.legs.forEach(function(leg) {
				steps = steps.concat(leg.steps);
			});
		
		steps.forEach(function(step) {
			
			var div = $("<div class='wpgmza-directions-step'></div>");
			
			div[0].wpgmzaDirectionsStep = step;
			
			div.html(step.html_instructions);
			div.addClass(WPGMZA.CloudDirectionsRenderer.maneuverToClassName(step.maneuver));
		
			self.panel.append(div);
			
		});
	}
	
	
	
});