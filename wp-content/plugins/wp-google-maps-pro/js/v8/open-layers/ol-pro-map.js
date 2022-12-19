/**
 * @namespace WPGMZA
 * @module OLProMap
 * @requires WPGMZA.OLMap
 */
jQuery(function($) {
	
	WPGMZA.OLProMap = function(element, options)
	{
		var self = this;
		
		WPGMZA.OLMap.call(this, element, options);
		
		var prevHoveringFeatures = [];
		
		// Load KML layers
		this.loadKMLLayers();
		
		// Hover interaction
		// NB: Commented out, this appears to be implemented in OLMap. Not sure why there's a different, separate implementation here. The "hovering" property appears to be unused.
		/*this.olMap.on("pointermove", function(event) {
			if(event.dragging)
				return;
			
			var pixel = event.map.getEventPixel(event.originalEvent);
			var currentHoveringFeatures = [];
			
			var hit = event.map.forEachFeatureAtPixel(pixel, function(feature, layer) {
				
				if(layer && layer.wpgmzaObject)
				{
					if(!layer.wpgmzaObject.hovering)
					{
						layer.wpgmzaObject.hovering = true;
						layer.wpgmzaObject.dispatchEvent("mouseover");
					}
					currentHoveringFeatures.push(layer.wpgmzaObject);
				}
				
				return true;
			});
			
			for(var i = 0; i < prevHoveringFeatures.length; i++)
			{
				if(currentHoveringFeatures.indexOf(prevHoveringFeatures[i]) == -1)
				{
					prevHoveringFeatures[i].hovering = false;
					prevHoveringFeatures[i].dispatchEvent("mouseout");
				}
			}
			
			prevHoveringFeatures = currentHoveringFeatures;
		});*/
		
		this.trigger("init");
		
		this.dispatchEvent("created");
		WPGMZA.events.dispatchEvent({type: "mapcreated", map: this});
		
		// Legacy event
		$(this.element).trigger("wpgooglemaps_loaded");
	}
	
	WPGMZA.OLProMap.prototype = Object.create(WPGMZA.OLMap.prototype);
	WPGMZA.OLProMap.prototype.constructor = WPGMZA.OLMap.prototype;
	
	WPGMZA.OLMap.prototype.addHeatmap = function(heatmap)
	{
		heatmap.olHeatmap.setMap(this.olMap);
		
		WPGMZA.ProMap.prototype.addHeatmap.call(this, heatmap);
	}
	
	/**
	 * Loads KML/GeoRSS layers
	 * @return void
	 */
	WPGMZA.OLProMap.prototype.loadKMLLayers = function()
	{
		// Remove old layers
		if(this.kmlLayers)
		{
			for(var i = 0; i < this.kmlLayers.length; i++)
				this.olMap.removeLayer(this.kmlLayers[i]);
		}
		
		this.kmlLayers = [];
		
		if(!this.settings.kml)
			return;
		
		// Add layers
		var urls = this.settings.kml.split(",");
		var cachebuster = new Date().getTime();
		
		for(var i = 0; i < urls.length; i++)
		{
			var layer = new ol.layer.Vector({
				source: new ol.source.Vector({
					url: urls[i],
					format: new ol.format.KML({
						// extractStyle: true,
						extractAttributes: true
					})
				})
			});
			
			this.kmlLayers.push(layer);
			this.olMap.addLayer(layer);
		}
	}
	
});