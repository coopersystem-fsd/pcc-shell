/**
 * @namespace WPGMZA
 * @module Heatmap
 * @requires WPGMZA.Feature
 */
jQuery(function($) {
	
	WPGMZA.Heatmap = function(options)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "EventDispatcher");
		
		if(!options)
			options = {};
		
		this.name = "";
		this.radius = 20;
		this.opacity = 0.5;
		
		var gradient = null;
		
		if(options.gradient && options.gradient != "default")
		{
			if(typeof options.gradient == "string")
				options.gradient = JSON.parse(options.gradient);
			else if(typeof options.gradient != "array")
				console.warn("Ignoring invalid gradient");
		}
		
		if(options.gradient == "default")
			delete options.gradient; // NB: Remove this here so that we don't try to pass this in as a color array. Simply let the default be used without providing this as an option.
		
		WPGMZA.Feature.apply(this, arguments);
	}
	
	WPGMZA.Heatmap.prototype = Object.create(WPGMZA.Feature.prototype);
	WPGMZA.Heatmap.prototype.constructor = WPGMZA.Heatmap;
	
	WPGMZA.Heatmap.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return WPGMZA.OLHeatmap;
				break;
			
			default:
				return WPGMZA.GoogleHeatmap;
				break;
		}
	}
	
	WPGMZA.Heatmap.createInstance = function(row)
	{
		var constructor = WPGMZA.Heatmap.getConstructor();
		return new constructor(row);
	}
	
	WPGMZA.Heatmap.createEditableMarker = function(options)
	{
		var options = $.extend({
			draggable: true
		}, options);
		
		var marker = WPGMZA.Marker.createInstance(options);
		
		// NB: Hack for constructor not accepting icon prooperly. Once it does, this can be removed
		var callback = function()
		{
			marker.setIcon(WPGMZA.heatmapIcon);
			marker.off("added", callback);
		};
		marker.on("added", callback);
		
		if(options.heatmap)
			options.heatmap.markers.push(marker);
		
		return marker;
	}
	
	WPGMZA.Heatmap.prototype.setEditable = function(editable)
	{
		var self = this;
		
		if(this.markers)
		{
			this.markers.forEach(function(marker) {
				marker.map.removeMarker(marker);
			});
			
			delete this.markers;
		}
		
		if(this._prevMap)
		{
			
			
			delete this._prevMap;
		}
		
		if(editable)
		{
			this.markers = [];
			
			this.dataset.forEach(function(latLng) {
				
				var options = {
					lat: latLng.lat,
					lng: latLng.lng,
					heatmap: self
				};
				
				var marker = WPGMZA.Heatmap.createEditableMarker(options);
				
				self.map.addMarker(marker);
				
			});
			
			this._clickCallback = function(event) {
				self.onClick(event);
			};
			
			this._dragEndCallback = function(event) {
				self.onDragEnd(event);
			};
			
			this._mouseDownCallback = function(event) {
				self.onMapMouseDown(event);
			};
			
			this._mouseMoveCallback = function(event) {
				self.onMapMouseMove(event);
			};
			
			this._mouseUpCallback = function(event) {
				self.onWindowMouseUp(event);
			};
			
			var map = this.map;
			
			map.on("click", this._clickCallback);
			map.on("dragend", this._dragEndCallback);

			$(map.element).on("mousedown", this._mouseDownCallback);
			$(map.element).on("mousemove", this._mouseMoveCallback);
			
			$(window).on("mouseup", function(event) {
				self.onWindowMouseUp(event);
			});
			
			map.on("heatmapremoved", function(event) {
				
				if(event.heatmap !== self)
					return;
				
				map.off("click", self._clickCallback);
				map.off("dragend", self._dragEndCallback);
				
				$(map.element).off("mousedown", self._mouseDownCallback);
				$(map.element).off("mousemove", self._mouseMoveCallback);
				
				$(window).off("mouseup", this._mouseUpCallback);
				
			});
		}
	}
	
	WPGMZA.Heatmap.prototype.updateDatasetFromMarkers = function()
	{
		var dataset = [];
		
		this.markers.forEach(function(marker) {
			
			dataset.push(marker.getPosition());
			
		});
		
		this.dataset = dataset;
	}
	
	WPGMZA.Heatmap.prototype.onClick = function(event)
	{
		if(event.target instanceof WPGMZA.Marker && event.target.heatmap === this)
		{
			var index = this.markers.indexOf(event.target);
			this.markers.splice(index, 1);
			this.map.removeMarker(event.target);
			
			this.updateDatasetFromMarkers();
			this.trigger("change");
			
			return;
		}
		
		if(event.target instanceof WPGMZA.Map)
		{
			var options = {
				lat: event.latLng.lat,
				lng: event.latLng.lng,
				heatmap: this
			}
			
			var marker = WPGMZA.Heatmap.createEditableMarker(options);
			
			this.map.addMarker(marker);
			
			this.updateDatasetFromMarkers();
			this.trigger("change");
			
			return;
		}
	}
	
	WPGMZA.Heatmap.prototype.onDragEnd = function(event)
	{
		if(!(event.target instanceof WPGMZA.Marker))
			return;
		
		if(!this.markers)
			return;
		
		if(this.markers.indexOf(event.target) == -1)
			return;
		
		this.updateDatasetFromMarkers();
		this.trigger("change");
	}
	
	WPGMZA.Heatmap.prototype.getGeometry = function()
	{
		return this.dataset;
	}
	
	WPGMZA.Heatmap.prototype.onMapMouseDown = function(event)
	{
		if(event.button == 2)
		{
			this._rightMouseDown = true;
			event.preventDefault();
			return false;
		}
	}
	
	WPGMZA.Heatmap.prototype.onWindowMouseUp = function(event)
	{
		if(event.button == 2)
			this._rightMouseDown = false;
	}
	
	WPGMZA.Heatmap.prototype.onMapMouseMove = function(event)
	{
		if(!this._rightMouseDown)
			return;
		
		var pixels = {
			x: event.pageX - $(this.map.element).offset().left,
			y: event.pageY - $(this.map.element).offset().top
		}
		
		var latLng = this.map.pixelsToLatLng(pixels);
		
		var options = {
			lat: latLng.lat,
			lng: latLng.lng,
			heatmap: this
		};
		
		var marker = WPGMZA.Heatmap.createEditableMarker(options);
		
		this.map.addMarker(marker);
		
		this.updateDatasetFromMarkers();
		this.trigger("change");
	}
	
});