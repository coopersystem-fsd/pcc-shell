/**
 * @namespace WPGMZA
 * @module OLHeatmap
 * @requires WPGMZA.Heatmap
 */
jQuery(function($) {
	
	WPGMZA.OLHeatmap = function(row)
	{
		var self = this;
		
		WPGMZA.Heatmap.call(this, row);
		
		this._removeListenerBound = false;
		
		var settings = this.getOLHeatmapSettings();
		this.olHeatmap = new ol.layer.Heatmap(settings);
	}
	
	WPGMZA.OLHeatmap.prototype = Object.create(WPGMZA.Heatmap.prototype);
	WPGMZA.OLHeatmap.prototype.constructor = WPGMZA.OLHeatmap;
	
	WPGMZA.OLHeatmap.prototype.getOLHeatmapSettings = function()
	{
		var settings = {
			source: this.getSource()
		};
		
		if(this.opacity)
			settings.opacity = parseFloat(this.opacity);
		
		if(this.radius)
			settings.radius = parseFloat(this.radius);
		
		if(this.heatmap_opacity)
			settings.opacity = parseFloat(this.heatmap_opacity);
		
		if(this.heatmap_radius)
			settings.radius = parseFloat(this.heatmap_radius);
		
		if(this.gradient)
			settings.gradient = this.gradient;
		
		return settings;
	}
	
	WPGMZA.OLHeatmap.prototype.removeLayer = function()
	{
		if(!this.olHeatmap)
			return;
		
		this.olHeatmap.getSource().dispose();
		
		// NB: Hacktastic.. See https://github.com/openlayers/openlayers/issues/10320. For some reason this has to be done manually
		if(this.olHeatmap.renderer_)
			this.olHeatmap.renderer_.dispose();
		
		this.olHeatmap.setMap(null);
		this.olHeatmap.dispose();
		
		delete this.olHeatmap;
	}
	
	/**
	 * Updates the OL heatmap layer
	 * @return void
	 */
	WPGMZA.OLHeatmap.prototype.updateOLHeatmap = function()
	{
		// NB: This should work, but it has no effect.
		//this.olHeatmap.setSource(this.getSource());
		
		var self = this;
		
		if(this.olHeatmap)
			this.removeLayer();
		
		var settings = this.getOLHeatmapSettings();
		
		this.olHeatmap = new ol.layer.Heatmap(settings);
		
		if(this.map)
		{
			this.olHeatmap.setMap(this.map.olMap);
			
			if(!this._removeListenerBound)
			{
				this.map.on("heatmapremoved", function(event) {
					// NB: Workaround for layer persisting after removal
					if(event.heatmap === self)
						self.removeLayer();
				});
			}
		}
	}
	
	WPGMZA.OLHeatmap.prototype.getSource = function()
	{
		var points = this.parseGeometry(this.dataset);
		var len = points.length;
		var features = [];
		
		for(var i = 0; i < len; i++)
			features.push(
				new ol.Feature({
					geometry: new ol.geom.Point(ol.proj.fromLonLat([
						parseFloat(points[i].lng),
						parseFloat(points[i].lat)
					]))
				})
			);
		
		return new ol.source.Vector({
			features: features
		});
	}
	
	WPGMZA.OLHeatmap.prototype.setDraggable = function()
	{
		// NB: Do nothing. This will cause issues because we have no layer in OpenLayers.
	}
	
	WPGMZA.OLHeatmap.prototype.update = function()
	{
		this.updateOLHeatmap();
	}
	
	WPGMZA.OLHeatmap.prototype.updateDatasetFromMarkers = function()
	{
		WPGMZA.Heatmap.prototype.updateDatasetFromMarkers.apply(this, arguments);
		
		this.updateOLHeatmap();
	}
	
});