/**
 * @namespace WPGMZA
 * @module OLDirectionsRenderer
 * @requires WPGMZA.DirectionsRenderer
 */
jQuery(function($) {
	
	WPGMZA.OLDirectionsRenderer = function(map)
	{
		var self = this;
		
		WPGMZA.DirectionsRenderer.apply(this, arguments);
		
		this.panel = $("#directions_panel_" + map.id);
		this.panel.on("click", ".wpgmza-directions-step", function(event) {
			self.onStepClicked(event);
		});
	}
	
	WPGMZA.extend(WPGMZA.OLDirectionsRenderer, WPGMZA.DirectionsRenderer);
	
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_LEFT				= 0;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_RIGHT				= 1;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SHARP_LEFT			= 2;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SHARP_RIGHT		= 3;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SLIGHT_LEFT		= 4;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SLIGHT_RIGHT		= 5;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_STRAIGHT			= 6;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_ENTER_ROUNDABOUT	= 7;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_EXIT_ROUNDABOUT	= 8;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_U_TURN				= 9;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_GOAL				= 10;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_DEPART				= 11;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_KEEP_LEFT			= 12;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_KEEP_RIGHT			= 13;
	
	WPGMZA.OLDirectionsRenderer.instructionTypeToClassName = function(type)
	{
		for(var name in WPGMZA.OLDirectionsRenderer)
		{
			if(!name.match(/^INSTRUCTION_TYPE_/))
				continue;
			
			if(WPGMZA.OLDirectionsRenderer[name] == type)
				return "wpgmza-" + name.replace(/_/g, "-").toLowerCase();
		}
	}
	
	WPGMZA.OLDirectionsRenderer.prototype.clear = function()
	{
		if(this.polyline)
		{
			this.map.removePolyline(this.polyline);
			delete this.polyline;
		}
		
		if(this.stepHighlightPolyline)
		{
			this.map.removePolyline(this.stepHighlightPolyline);
			delete this.stepHighlightPolyline;
		}
		
		this.panel.html("");
	}
	
	WPGMZA.OLDirectionsRenderer.prototype.setDirections = function(directions)
	{
		var self = this;
		
		// Polyline route
		var route = directions.routes[0];
		var source = window.polyline.decode(route.geometry);
		var points = [];
		
		this.clear();
		
		source.forEach(function(arr) {
			
			points.push({
				lat: arr[0],
				lng: arr[1]
			});
			
		});
		
		var settings = {
			linecolor: "#4285F4",
			linethickness: 4,
			opacity: 0.8
		}

		if(this.map.settings.directions_route_stroke_color){
			settings.linecolor = this.map.settings.directions_route_stroke_color;
		}

		 if(this.map.settings.directions_route_stroke_weight){
		 	settings.linethickness = this.map.settings.directions_route_stroke_weight;
		 }

		 if(this.map.settings.directions_route_stroke_opacity){
		 	settings.opacity = this.map.settings.directions_route_stroke_opacity;
		 }


		this.polyline = WPGMZA.Polyline.createInstance({
			polydata: points,
			strokeWeight : settings.linethickness,
			strokeOpacity : settings.opacity,
			strokeColor : settings.linecolor
		});
		
		this.polyline.map = this.map;
		
		this.map.addPolyline(this.polyline);
		
		// Adds markers to origin and destination and removes if directions are searched once more
		if (this.directionStartMarker) {
			this.map.removeMarker(this.directionStartMarker);
		}

		if (this.directionEndMarker) {
			this.map.removeMarker(this.directionEndMarker);
		}

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

		// Panel
		var steps = [];
		
		if(route.segments)
			route.segments.forEach(function(segment) {
				steps = steps.concat(segment.steps);
			});
		
		steps.forEach(function(step) {
			
			var div = $("<div class='wpgmza-directions-step'></div>");
			
			div[0].wpgmzaDirectionsStep = step;
			
			div.html(step.instruction);
			div.addClass(WPGMZA.OLDirectionsRenderer.instructionTypeToClassName(step.type));
			
			self.panel.append(div);
			
		});

		if(this.map.settings.directions_fit_bounds_to_route){
			this.fitBoundsToRoute(points[0], points[points.length - 1]);
		}
	}
	
	WPGMZA.OLDirectionsRenderer.prototype.onStepClicked = function(event)
	{
		var step = event.currentTarget.wpgmzaDirectionsStep;
		var bounds = new WPGMZA.LatLngBounds();
		var startIndex = step.way_points[0];
		var endIndex = step.way_points[step.way_points.length - 1];
		
		if(this.stepHighlightPolyline)
		{
			this.map.removePolyline(this.stepHighlightPolyline);
			delete this.stepHighlightPolyline;
		}
		
		if(startIndex == endIndex)
			return;
		
		var points = [];
		
		for(var i = startIndex; i <= endIndex; i++)
		{
			var vertex = this.polyline.polydata[i];
			
			points.push(vertex);
			bounds.extend(vertex);
		}

		var settings = {
			strokeColor: "#ff0000",
			strokeWeight: 4,
			strokeOpacity: 0.8
		};

		if(this.map.settings.directions_route_stroke_weight){
		 	settings.linethickness = this.map.settings.directions_route_stroke_weight;
		}

		if(this.map.settings.directions_route_stroke_opacity){
		 	settings.opacity = this.map.settings.directions_route_stroke_opacity;
		}
		
		var polyline = WPGMZA.Polyline.createInstance({
			polydata: points,
			settings: settings
		});

		this.stepHighlightPolyline = polyline;
		
		this.map.addPolyline(this.stepHighlightPolyline);
		this.map.fitBounds(bounds);
		
		WPGMZA.animateScroll(this.map.element);
	}
	
});