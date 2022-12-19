/**
 * @namespace WPGMZA
 * @module GoogleProMap
 * @requires WPGMZA.GoogleMap
 */
jQuery(function($) {
	WPGMZA.GoogleProMap = function(element, options)
	{
		WPGMZA.GoogleMap.call(this, element, options);
		
		// Load KML layers
		this.loadKMLLayers();
		
		// Dispatch event
		this.trigger("init");
		
		this.dispatchEvent("created");
		WPGMZA.events.dispatchEvent({type: "mapcreated", map: this});
		
		// Legacy event
		$(this.element).trigger("wpgooglemaps_loaded");
	}
	
	WPGMZA.GoogleProMap.prototype = Object.create(WPGMZA.GoogleMap.prototype);
	WPGMZA.GoogleProMap.prototype.constructor = WPGMZA.GoogleProMap.prototype;
	
	WPGMZA.GoogleProMap.prototype.addHeatmap = function(heatmap)
	{
		heatmap.googleHeatmap.setMap(this.googleMap);
		
		WPGMZA.ProMap.prototype.addHeatmap.call(this, heatmap);
	}
	
	/**
	 * Loads KML/GeoRSS layers
	 * @return void
	 */
	WPGMZA.GoogleProMap.prototype.loadKMLLayers = function() {
		

		// Remove old layers
		if(this.kmlLayers) {
			for(var i = 0; i < this.kmlLayers.length; i++)
				this.kmlLayers[i].setMap(null);
		}
		
		this.kmlLayers = [];
		
		if(!this.settings.kml)
			return;
		


		// Add layers
		var urls = this.settings.kml.split(",");
		var cachebuster = new Date().getTime();
		
		for(var i = 0; i < urls.length; i++)
		{
			this.kmlLayers.push(
				new google.maps.KmlLayer(urls[i] + "?cachebuster=" + cachebuster,
					{
						map: this.googleMap,
						preserveViewport: true
					}
				)
			);
		}
	}
	
	WPGMZA.GoogleProMap.prototype.loadFusionTableLayer = function() 
	{
		if(!this.settings.fusion)
			return;
		
		console.warn("Fusion Table Layers are deprecated and will cease functioning from 2019/12/03");
		
		this.fusionLayer = new google.maps.FusionTablesLayer(this.settings.fusion, {
			map: this.googleMap,
			surpressInfoWindows: true
		});
	}
	
	WPGMZA.GoogleProMap.prototype.setStreetView = function(options)
	{
		var latLng = this.getCenter();
		
		if(!options)
			options = {
				bearing: 0,
				pitch: 10
			};
		
		if("marker" in options && (marker = this.getMarkerByID(options.marker)))
		{
			latLng = marker.getPosition().toLatLngLiteral();
		}
		else if(("lat" in options) && ("lng" in options))
		{
			latLng = {
				lat: parseFloat(options.lat),
				lng: parseFloat(options.lng)
			};
		}
		
		if("bearing" in options)
		{
			options.bearing = parseInt(options.bearing);
			
			if(isNaN(options.bearing))
				console.warn("Invalid bearing");
		}
		
		if("pitch" in options)
		{
			options.pitch = parseInt(options.pitch);
			
			if(isNaN(options.pitch))
				console.warn("Invalid pitch");
		}
		
		this.panorama = new google.maps.StreetViewPanorama(
			this.element,
			{
				position: latLng,
				pov: {
					heading: parseInt(options.bearing),
					pitch: parseInt(options.pitch)
				}
			}
		);
	}
	
	WPGMZA.GoogleProMap.prototype.onInit = function(event)
	{
		WPGMZA.GoogleMap.prototype.onInit.call(this, event);
		
		if(this.shortcodeAttributes.streetview && !this.shortcodeAttributes.marker)
			this.setStreetView(this.shortcodeAttributes);
	}
	
	WPGMZA.GoogleProMap.prototype.onMarkersPlaced = function(event) 
	{
		WPGMZA.GoogleMap.prototype.onMarkersPlaced.call(this, event);
		
		if(this.shortcodeAttributes.streetview && this.shortcodeAttributes.marker)
			this.setStreetView(this.shortcodeAttributes);
	}
	
});