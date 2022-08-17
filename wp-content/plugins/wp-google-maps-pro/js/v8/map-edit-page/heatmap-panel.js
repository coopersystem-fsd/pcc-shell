/**
 * @namespace WPGMZA
 * @module HeatmapPanel
 * @requires WPGMZA.FeaturePanel
 */
jQuery(function($) {
	
	WPGMZA.HeatmapPanel = function(element, mapEditPage)
	{
		WPGMZA.FeaturePanel.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.HeatmapPanel, WPGMZA.FeaturePanel);
	
	WPGMZA.HeatmapPanel.createInstance = function(element, mapEditPage)
	{
		return new WPGMZA.HeatmapPanel(element, mapEditPage);
	}
	
	WPGMZA.HeatmapPanel.prototype.reset = function(event)
	{
		WPGMZA.FeaturePanel.prototype.reset.apply(this, arguments);
		
		$(this.element).find("[data-ajax-name='gradient']").prop("checked", false);
		
		$( $(this.element).find("[data-ajax-name='gradient']")[0] ).prop("checked", true);
	}
	
	WPGMZA.HeatmapPanel.prototype.populate = function(data)
	{
		WPGMZA.FeaturePanel.prototype.populate.apply(this, arguments);
		
		if(data.gradient)
		{
			// NB: We parse and re-stringify the gradient here, this ensures that we're comparing that the JSON is equivalent, regardless of differences in formatting (eg single quotes vs double quotes, spacing, etc.)
			var str = JSON.stringify(JSON.parse(data.gradient));
			
			$(this.element).find("input[data-ajax-name='gradient']").each(function(index, el) {
				
				var compare = JSON.stringify(JSON.parse($(el).val()));
				
				if(str == compare)
				{
					$(el).prop("checked", true);
					return false;
				}
				
			});
		}
	}
	
	WPGMZA.HeatmapPanel.prototype.onPropertyChanged = function(event)
	{
		// NB: Normally, the panel wouldn't send property changes to the drawing manager. Polygons, for example, don't have any fill until the shape is closed. Therefore code to send fill color changes to the polygon is not required. However, heatmaps are an exception to this rule and are visible during drawing new heatmaps, as well as when editing existing heatmaps. For this reason we need this override to pass the parameters to the drawing manager
		
		if(this.drawingManager.mode == WPGMZA.DrawingManager.MODE_HEATMAP)
			this.drawingManager.onHeatmapPropertyChanged(event);
		else if(this.feature)
		{
			var name	= $(event.target).attr("data-ajax-name");
			var value	= $(event.target).val();
			
			switch(name)
			{
				case "gradient":
					value = JSON.parse(value);
				
				default:
					this.feature[name] = value;
					break;
			}
			
			this.feature.update();
		}
	}
	
	WPGMZA.HeatmapPanel.prototype.onFeatureChanged = function(event)
	{
		var geometryField = $(this.element).find("[data-ajax-name='dataset']");
		
		if(!geometryField.length)
			return;
		
		geometryField.val(JSON.stringify(this.feature.getGeometry()));
	}
	
});