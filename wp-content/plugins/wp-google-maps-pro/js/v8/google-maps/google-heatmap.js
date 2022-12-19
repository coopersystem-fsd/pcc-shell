/**
 * @namespace WPGMZA
 * @module GoogleHeatmap
 * @requires WPGMZA.Heatmap
 */
jQuery(function($) {
	
	WPGMZA.GoogleHeatmap = function(options)
	{
		WPGMZA.Heatmap.call(this, options);
		
		if(!google.maps.visualization)
		{
			console.warn("Heatmaps disabled. You must include the visualization library in the Google Maps API");
			return;
		}
		
		this.googleHeatmap = new google.maps.visualization.HeatmapLayer();
		this.googleFeature = this.googleHeatmap;
		
		this.updateGoogleHeatmap();
	}
	
	WPGMZA.GoogleHeatmap.prototype = Object.create(WPGMZA.Heatmap.prototype);
	WPGMZA.GoogleHeatmap.prototype.constructor = WPGMZA.GoogleHeatmap;
	
	WPGMZA.GoogleHeatmap.prototype.updateGoogleHeatmap = function()
	{
		var points = this.parseGeometry(this.dataset);
		var len = points.length;
		var data = [];
		
		// TODO: There are optimizations that could be made here, instead of regenerating the entire array and calling new google.maps.LatLng for each point, it would be better to keep an array and splice it
		// NB: To further the above, and MVC array should do it
		
		for(var i = 0; i < len; i++)
			data.push(
				new google.maps.LatLng(
					parseFloat(points[i].lat), 
					parseFloat(points[i].lng)
				)
			);
		
		this.googleHeatmap.setData(data);
		
		if(this.gradient)
			this.googleHeatmap.set("gradient", this.gradient);
		
		if(this.radius)
			this.googleHeatmap.set("radius", parseFloat(this.radius));
		
		// NB: Legacy variable name support. "heatmap_" is redundant here
		if(this.heatmap_radius)
			this.googleHeatmap.set("radius", parseFloat(this.heatmap_radius));
		
		if(this.opacity)
			this.googleHeatmap.set("opacity", parseFloat(this.opacity));

		// NB: Legacy variable name support. "heatmap_" is redundant here
		if(this.heatmap_opacity)
			this.googleHeatmap.set("opacity", parseFloat(this.heatmap_opacity));
		
		if(this.map && !this.googleHeatmap.getMap())
			this.googleHeatmap.setMap(this.map.googleMap);
	}
	
	WPGMZA.GoogleHeatmap.prototype.update = function()
	{
		this.updateGoogleHeatmap();
	}
	
	WPGMZA.GoogleHeatmap.prototype.updateDatasetFromMarkers = function()
	{
		WPGMZA.Heatmap.prototype.updateDatasetFromMarkers.apply(this, arguments);
		
		this.updateGoogleHeatmap();
	}
	
	WPGMZA.GoogleHeatmap.prototype.onMapMouseDown = function(event)
	{
		if(event.button == 2)
		{
			// NB: Stop Google map from being dragged on right click, this creates issues with drawing heatmaps
			this.map.googleMap.setOptions({
				draggable: false
			});
		}
		
		WPGMZA.Heatmap.prototype.onMapMouseDown.apply(this, arguments);
	}
	
	WPGMZA.GoogleHeatmap.prototype.onWindowMouseUp = function(event)
	{
		if(event.button == 2)
		{
			// NB: Restore draggability. Freehand mode would trigger dragging if we didn't manually stop this in onMapMouseDown
			this.map.googleMap.setOptions({
				draggable: true
			});
		}
		
		WPGMZA.Heatmap.prototype.onWindowMouseUp.apply(this, arguments);
	}
	
});