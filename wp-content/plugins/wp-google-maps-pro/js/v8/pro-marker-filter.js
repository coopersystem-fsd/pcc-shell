/**
 * @namespace WPGMZA
 * @module ProMarkerFilter
 * @requires WPGMZA.MarkerFilter
 */
jQuery(function($) {
	
	WPGMZA.ProMarkerFilter = function(map)
	{
		var self = this;
		
		WPGMZA.MarkerFilter.call(this, map);
	}
	
	WPGMZA.ProMarkerFilter.prototype = Object.create(WPGMZA.MarkerFilter.prototype);
	WPGMZA.ProMarkerFilter.prototype.constructor = WPGMZA.ProMarkerFilter;
	
	WPGMZA.MarkerFilter.createInstance = function(map)
	{
		return new WPGMZA.ProMarkerFilter(map);
	}
	
	WPGMZA.ProMarkerFilter.prototype.getFilteringParameters = function()
	{
		var params = WPGMZA.MarkerFilter.prototype.getFilteringParameters.call(this);
		var mashupIDs = this.map.mashupIDs;
		
		if(mashupIDs)
			params.mashupIDs = mashupIDs;
		
		if(this.map.markerListing)
			params = $.extend(params, this.map.markerListing.getFilteringParameters());
		
		if(this.map.customFieldFilterController)
		{
			var customFieldFilterAjaxParams = this.map.customFieldFilterController.getAjaxRequestData();
			var customFieldFilterFilteringParams = customFieldFilterAjaxParams.data.widgetData;
			params.customFields = customFieldFilterFilteringParams;
		}
		
		if(this.map.settings.only_load_markers_within_viewport)
		{
			var bounds = this.map.getBounds();
			params.bounds = bounds;
		}
		
		return params;
	}
	
	WPGMZA.ProMarkerFilter.prototype.update = function(params, source)
	{
		var self = this;
		
		if(this.updateTimeoutID)
			return;
		
		if(!params)
			params = {};
		
		if(this.xhr)
		{
			this.xhr.abort();
			delete this.xhr;
		}
		
		function dispatchEvent(result)
		{
			var event = new WPGMZA.Event("filteringcomplete");
			
			event.map = self.map;
			event.source = source;
			
			event.filteredMarkers = result;
			event.filteringParams = params;
			
			self.onFilteringComplete(event);
			
			self.trigger(event);
			self.map.trigger(event);
		}
		
		this.updateTimeoutID = setTimeout(function() {
			
			params = $.extend(self.getFilteringParameters(), params);
			
			if(params.center instanceof WPGMZA.LatLng)
				params.center = params.center.toLatLngLiteral();
			
			if(params.hideAll)
			{
				// Hide all markers before a store locator search is done
				dispatchEvent([]);
				delete self.updateTimeoutID;
				return;
			}
			
			self.map.showPreloader(true);
			
			self.xhr = WPGMZA.restAPI.call("/markers", {
				data: {
					fields: ["id"],
					filter: JSON.stringify(params)
				},
				success: function(result, status, xhr) {
					
					self.map.showPreloader(false);
					
					dispatchEvent(result);
					
				},
				useCompressedPathVariable: true
			});
			
			delete self.updateTimeoutID;
			
		}, 0);
	}
	
	WPGMZA.ProMarkerFilter.prototype.onFilteringComplete = function(event)
	{
		var self = this;
		var map = [];
		
		event.filteredMarkers.forEach(function(data) {
			map[data.id] = true;
		});
		
		this.map.markers.forEach(function(marker) {
			
			if(!marker.isFilterable)
				return;
				
			var allowByFilter = map[marker.id] ? true : false;
			
			marker.isFiltered = !allowByFilter;
			marker.setVisible(allowByFilter);
			
		});
	}
	
});