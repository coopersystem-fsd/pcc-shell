/**
 * @namespace WPGMZA
 * @module ProDrawingManager
 * @requires WPGMZA.GoogleDrawingManager
 * @requires WPGMZA.OLDrawingManager
 */
jQuery(function($) {
	
	var Parent = WPGMZA.settings.engine == "open-layers" ? WPGMZA.OLDrawingManager : WPGMZA.GoogleDrawingManager;
	
	WPGMZA.ProDrawingManager = function(map) {
		var self = this;
		
		Parent.apply(this, arguments);
		
		this.map.on("click rightclick", function(event) {
			self.onMapClick(event);
		});
	}
	
	WPGMZA.extend(WPGMZA.ProDrawingManager, Parent);
	
	WPGMZA.DrawingManager.getConstructor = function() {
		switch(WPGMZA.settings.engine)
		{
			case "google-maps":
				return WPGMZA.GoogleProDrawingManager;
				break;
				
			default:
				return WPGMZA.OLProDrawingManager;
				break;
		}
	}
	
	WPGMZA.ProDrawingManager.prototype.setDrawingMode = function(mode) {
		var self = this;
		
		if(mode != WPGMZA.DrawingManager.MODE_HEATMAP) {
			if(this.heatmap) {
				this.heatmap.markers.forEach(function(marker) {
					self.map.removeMarker(marker);
				});
				
				this.map.removeHeatmap(this.heatmap);
				delete this.heatmap;
			}
			
			Parent.prototype.setDrawingMode.apply(this, arguments);
			
			return;
		}
		
		// NB: Don't create the heatmap until we have at least one point
		
		Parent.prototype.setDrawingMode.apply(this, arguments);
	}
	
	WPGMZA.ProDrawingManager.prototype.getHeatmapParameters = function() {
		var params = {};
		
		// NB: Gather properties from the heatmap panel
		$(".wpgmza-feature-panel[data-wpgmza-feature-type='heatmap'] [data-ajax-name]").each(function(index, el) {
			
			var value;
			
			if($(el).attr("data-ajax-name") == "gradient")
				return;	// NB: Continue iterating
		
			switch($(el).attr("type"))
			{
				case "number":
					value = parseFloat($(el).val());
					break;
				
				default:
					value = $(el).val();
					break;
			}
		
			params[$(el).attr("data-ajax-name")] = value;
			
		});
		
		// NB: Handle gradient differently as it's a radio
		var str = $(".wpgmza-feature-panel[data-wpgmza-feature-type='heatmap'] [data-ajax-name='gradient']:checked").val();
		
		if(str != "default")
			params.gradient = JSON.parse(str);
		
		return params;
	}
	
	WPGMZA.ProDrawingManager.prototype.onMapClick = function(event) {
		var self = this;
		if(this.mode != WPGMZA.DrawingManager.MODE_HEATMAP) {
			return;
		}
		
		if(!(event.target instanceof WPGMZA.Map))
			return;
		
		if(!this.heatmap) {
			this.heatmap = WPGMZA.Heatmap.createInstance({
				dataset: []
			});
			
			this.map.addHeatmap(this.heatmap);
			this.heatmap.setEditable(true);
			
			this.heatmap.on("change", function(event) {
				self.onHeatmapGeometryChanged(event);
			});
		}
		
		if(event.button == 2) {
			event.preventDefault();
			return false;
		}
	}
	
	WPGMZA.ProDrawingManager.prototype.updateHeatmapGeometryField = function() {
		// NB: Normally, we'd listen for a "drawingcomplete" event before updating the field, this is how polygons etc. work. However, because a heatmap has no completion in the same sense asd a polygon, this needs to be called after any change
		
		var arr = [];
		
		this.heatmap.markers.forEach(function(marker) {
			
			var position = marker.getPosition().toLatLngLiteral();
			arr.push(position);
			
		});
		
		$("[data-wpgmza-feature-type='heatmap']").find("[data-ajax-name='dataset']").val( JSON.stringify(arr) );
	}
	
	WPGMZA.ProDrawingManager.prototype.updateHeatmap = function() {
		var params = this.getHeatmapParameters();
		
		for(var key in params)
			this.heatmap[key] = params[key];
		
		this.heatmap.update();
	}
	
	WPGMZA.ProDrawingManager.prototype.onHeatmapPropertyChanged = function(event) {
		if($(event.target).attr("data-ajax-name") == "dataset_name")
			return; // NB: Ignore name changes, they don't affect the appearance of the heatmap
		
		this.updateHeatmap();
	}
	
	WPGMZA.ProDrawingManager.prototype.onHeatmapGeometryChanged = function(event) {
		this.updateHeatmapGeometryField();
	}
	
});