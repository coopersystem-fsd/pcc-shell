/**
 * @namespace WPGMZA
 * @module ProStoreLocator
 * @requires WPGMZA.StoreLocator
 */
jQuery(function($) {
	
	WPGMZA.ProStoreLocator = function(map, element)
	{
		var self = this;
		
		WPGMZA.StoreLocator.call(this, map, element);
		
		// Initially disable buttons
		var buttons = $(element).find("input[type='button'], button:not(.wpgmza-use-my-location)");
		buttons.prop("disabled", true);
		map.on("markersplaced", function(event) {
			buttons.prop("disabled", false);
		});

		if(!map.settings.wpgmza_store_locator_use_their_location){
			$(this.element).find(".wpgmza-use-my-location").remove();
		}
		
		if(map.settings.store_locator_search_area == WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO)
		{
			$(this.element).find(".wpgmza_sl_radius_select").remove();
		}
		
		this.map.on("init", function(event) {
			
			/*self.map.markerFilter.on("filteringcomplete", function(event) {
				self.onFilteringComplete(event);
			});*/
			
		});
	}
	
	WPGMZA.ProStoreLocator.prototype = Object.create(WPGMZA.StoreLocator.prototype);
	WPGMZA.ProStoreLocator.prototype.constructor = WPGMZA.ProStoreLocator;
	
	WPGMZA.ProStoreLocator.SEARCH_AREA_RADIAL		= "radial";
	WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO			= "auto";
	
	WPGMZA.StoreLocator.createInstance = function(map, element)
	{
		return new WPGMZA.ProStoreLocator(map, element);
	}
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "keywords", {
		
		"get": function() {
			
			var legacy = $(".wpgmza_name_search_string + input").val();
			
			if(legacy)
				return legacy;
			
			var modern = $(this.map.element).find(".wpgmza-text-search").val();
			
			return modern;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "categories", {
		
		"configurable": true,
		
		"get": function() {
			var dropdown, checkboxes, value, results;
			
			var isModernStyle = $(this.map.element).find(".wpgmza-modern-store-locator").length > 0;
			
			
			if(isModernStyle)
			{
				$(this.map.element).find(".wpgmza-modern-store-locator [name='wpgmza_cat_checkbox']:checked").each(function(index, el) {
					
					if(!results)
						results = [];
					
					results.push( $(el).val() );
					
				});
			}
			else
			{
				if((dropdown = $(this.element).find(".wpgmza_sl_category_div > select")).length)
				{
					value = dropdown.val();
					
					if(value == "0")
						return null;
					
					return [value];
				}
				
				$(this.element).find(".wpgmza_sl_category_div :checked").each(function(index, el) {
					
					if(!results)
						results = [];
					
					results.push( $(el).val() );
					
				});
			}
			
			return results;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "hideMarkersInInitialState", {
		
		"get": function() {
			
			return this.map.settings.wpgmza_store_locator_hide_before_search == 1;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleStrokeColor", {
		
		"get": function() {
			
			if(this.map.settings.sl_stroke_color){
				return "#" + this.map.settings.sl_stroke_color.replace(/^#/, "");
			}
			
			return "#ff0000";
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleFillColor", {
		
		"get": function() {
			
			if(this.map.settings.sl_fill_color){
				return "#" + this.map.settings.sl_fill_color.replace(/^#/, "");
			}

			
			return "#ff0000";
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleStrokeOpacity", {
		
		"get": function() {
			
			if(this.map.settings.sl_stroke_opacity !== undefined && this.map.settings.sl_stroke_opacity !== "")
				return parseFloat(this.map.settings.sl_stroke_opacity);
			
			return 0.25;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleFillOpacity", {
		
		"get": function() {
			
			if(this.map.settings.sl_fill_opacity !== undefined && this.map.settings.sl_fill_opacity !== "")
				return parseFloat(this.map.settings.sl_fill_opacity);
			
			return 0.15;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circle", {
		
		"get": function() {
			
			if(this.map.settings.store_locator_search_area == WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO)
				return null;
			
			if(this._circle)
				return this._circle;
			
			if(!WPGMZA.isDeviceiOS() && this.map.settings.wpgmza_store_locator_radius_style == "modern")
			{
				this._circle = WPGMZA.ModernStoreLocatorCircle.createInstance(this.map.id);
				this._circle.settings.color = this.circleStrokeColor;
			} else {
				this._circle = WPGMZA.Circle.createInstance({
					strokeColor:	this.circleStrokeColor,
					strokeOpacity:	this.circleStrokeOpacity,
					strokeWeight:	2,
					fillColor:		this.circleFillColor,
					fillOpacity:	this.circleFillOpacity,
					visible:		false,
					clickable:      false,
					center: new WPGMZA.LatLng()
				});
			}
			
			return this._circle;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "marker", {
		
		"get": function() {
			
			if(this.map.settings.wpgmza_store_locator_bounce != 1)
				return null;
			
			if(this._marker)
				return this._marker;
			
			var options = {
				visible: false
			};
			
			if(this.map.settings.upload_default_sl_marker && this.map.settings.upload_default_sl_marker.length){
				options.icon = this.map.settings.upload_default_sl_marker;

				if(this.map.settings.upload_default_sl_marker_retina){
					options.retina = true;
				}
			}
			
			this._marker = WPGMZA.Marker.createInstance(options);
			this._marker.disableInfoWindow = true;
			this._marker.isFilterable = false;

			this._marker._icon.retina = this._marker.retina;
			
			if(this.map.settings.wpgmza_sl_animation == 1)
				this._marker.setAnimation(WPGMZA.Marker.ANIMATION_BOUNCE);
			else if(this.map.settings.wpgmza_sl_animation == 2)
				this._marker.setAnimation(WPGMZA.Marker.ANIMATION_DROP);
			
			return this._marker;
			
		}
		
	});
	
	WPGMZA.ProStoreLocator.prototype.getZoomFromRadius = function(radius)
	{
		if(this.distanceUnits == WPGMZA.Distance.MILES)
			radius *= WPGMZA.Distance.KILOMETERS_PER_MILE;
		
		return Math.round(14 - Math.log(radius) / Math.LN2);
	}
	
	WPGMZA.ProStoreLocator.prototype.getFilteringParameters = function()
	{
		if(this.state == WPGMZA.StoreLocator.STATE_INITIAL)
		{
			if(this.hideMarkersInInitialState)
			{
				return {
					hideAll: true
				};
			}
			
			return {};	// No search has been performed yet
		}
		
		var params = WPGMZA.StoreLocator.prototype.getFilteringParameters.call(this);
		var proParams = {};
		
		if(this.map.settings.store_locator_search_area == WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO)
		{
			delete params.center;
			delete params.radius;
		}
		
		if(this.keywords)
			proParams.keywords = this.keywords;
		
		if(this.categories)
			proParams.categories = this.categories;
		
		return $.extend(params, proParams);
	}
	
	WPGMZA.ProStoreLocator.prototype.onFilteringComplete = function(event)
	{
		var params = event.filteringParams;
		var circle = this.circle;
		var marker = this.marker;

		var factor = (this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.Distance.KILOMETERS_PER_MILE : 1.0);
		
		if(!(event.source instanceof WPGMZA.StoreLocator))
			return;
		
		WPGMZA.StoreLocator.prototype.onFilteringComplete.apply(this, arguments);
		
		switch(this.map.settings.store_locator_search_area)
		{
			case WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO:
			
				if(!this.bounds || this.bounds.isInInitialState())
				{
					this.map.setZoom(this.map.settings.map_start_zoom);
					/*this.map.setCenter(new WPGMZA.LatLng(
						this.map.settings.map_start_lat,
						this.map.settings.map_start_lng
					));*/
					
					break;
				}
			
				this.map.fitBounds(this.bounds);
				
				var maxZoom = this.map.settings.store_locator_auto_area_max_zoom;
				
				if(maxZoom && this.map.getZoom() >= maxZoom)
					this.map.setZoom(maxZoom);
			
				break;
			
			default:
			
				if(circle)
					circle.setVisible(false);
				
				if(params.center && params.radius)
				{
					// Focus on center and zoom
					this.map.setCenter(params.center);
					this.map.setZoom(this.getZoomFromRadius(params.radius));
					
					if(circle)
					{
						if(circle instanceof WPGMZA.ModernStoreLocatorCircle)
							circle.settings.radiusString = Math.round(params.radius);
						
						circle.setRadius(params.radius * factor);
						circle.setCenter(params.center);
						circle.setVisible(true);
						
						if(circle.map != this.map)
							this.map.addCircle(circle);
					}
					
					break;
				
			}
		}
		
		var storeLocatorResultEvent = {type: "storelocatorresult"};
		
		if(event.center)
			storeLocatorResultEvent.center = event.center;
		
		this.map.trigger(storeLocatorResultEvent);
	}
	
	WPGMZA.ProStoreLocator.prototype.onGeocodeComplete = function(event)
	{
		if(event.results && event.results.length)
		{
			var location = new WPGMZA.LatLng({
				lat: event.results[0].lat,
				lng: event.results[0].lng
			});
			
			location.source = WPGMZA.ProMap.SHOW_DISTANCE_FROM_SEARCHED_ADDRESS;
			
			this.map.showDistanceFromLocation = location;
		}
		
		WPGMZA.StoreLocator.prototype.onGeocodeComplete.apply(this, arguments);
	}
	
	WPGMZA.ProStoreLocator.prototype.onReset = function(event)
	{
		this.map.showDistanceFromLocation = this.map.userLocation;
		this.map.updateInfoWindowDistances();
		
		WPGMZA.StoreLocator.prototype.onReset.apply(this, arguments);
	}
	
	
});