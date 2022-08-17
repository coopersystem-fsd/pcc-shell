/**
 * @namespace WPGMZA
 * @module MarkerListing
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.MarkerListing = function(map, element, options)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.apply(this);
		
		this._paginationEnabled = true;
		
		this.map = this.parent = map;
		this.element = element;
		
		if(this.element)
		{
			this.element.wpgmzaMarkerListing = this;
		}
		else if(WPGMZA.isDeveloperMode())
		{
			console.warn("Marker listing initialised with null element. This is presently supported to allow the marker listing category filter to still function, however this will be removed in the future.");
		}
		
		if(options)
			for(var key in options)
				this[key] = options[key];
		
		this.categoryDropdown = $(".wpgmza-marker-listing-category-filter[data-map-id='" + this.map.id + "'] select");
		if(!$(this.categoryDropdown).closest(".wpgmza-store-locator").length)
			this.categoryDropdown.on("change", function(event) {
				var map = WPGMZA.getMapByID(self.map.id);
				map.markerFilter.update();
			});
		
		this.categoryCheckboxes = $(".wpgmza-marker-listing-category-filter[data-map-id='" + this.map.id + "'] input[type='checkbox']");
		this.categoryCheckboxes.on("change", function(event) {
			var map = WPGMZA.getMapByID(self.map.id);
			map.markerFilter.update();
		});
		
		if(map.settings.wpgmza_store_locator_hide_before_search == 1) {
			this.showOnFilteringComplete = true;
			$(this.element).hide();
		}

		
		//backwards compat
		if (typeof map.settings.push_in_map !== 'undefined' && typeof map.settings.wpgmza_push_in_map == 'undefined') {
			map.settings.wpgmza_push_in_map = map.settings.push_in_map;
			map.settings.wpgmza_push_in_map_placement = map.settings.push_in_map_placement;
		}	
		if(map.settings.wpgmza_push_in_map) {
			this.pushIntoMap();
		}
		
		$(this.element).on("click", ".wpgmaps_mlist_row, .wpgmaps_blist_row", function(event) {
			self.onItemClick(event);
		});
		
		$(document.body).on("filteringcomplete.wpgmza", function(event) {
			
			if(event.map.id == self.map.id)
				self.onFilteringComplete(event);
			
		});
		
		this.reload();
	}
	
	WPGMZA.extend(WPGMZA.MarkerListing, WPGMZA.EventDispatcher);
	
	WPGMZA.MarkerListing.createInstance = function(map, element, options) {

		// backwards compat
		if (typeof map.settings.list_markers_by != 'undefined' && typeof map.settings.wpgmza_listmarkers_by == 'undefined') {
			map.settings.wpgmza_listmarkers_by = parseInt(map.settings.list_markers_by);
		}

		switch(map.settings.wpgmza_listmarkers_by) {
			case WPGMZA.MarkerListing.STYLE_ADVANCED_TABLE:
				return new WPGMZA.AdvancedTableMarkerListing(map, element, options); 
				break;
			
			case WPGMZA.MarkerListing.STYLE_CAROUSEL:
				return new WPGMZA.CarouselMarkerListing(map, element, options);
				break;
			
			case WPGMZA.MarkerListing.STYLE_MODERN:
				return new WPGMZA.ModernMarkerListing(map, element, options);
				break;
			
			default:
				return new WPGMZA.MarkerListing(map, element, options);
				break;
		}
	}
	
	WPGMZA.MarkerListing.STYLE_NONE					= 0;
	WPGMZA.MarkerListing.STYLE_BASIC_TABLE			= 1;
	WPGMZA.MarkerListing.STYLE_BASIC_LIST 			= 4;
	WPGMZA.MarkerListing.STYLE_ADVANCED_TABLE		= 2;
	WPGMZA.MarkerListing.STYLE_CAROUSEL				= 3;
	WPGMZA.MarkerListing.STYLE_MODERN				= 6;
	
	WPGMZA.MarkerListing.ORDER_BY_ID				= 1;
	WPGMZA.MarkerListing.ORDER_BY_TITLE				= 2;
	WPGMZA.MarkerListing.ORDER_BY_ADDRESS			= 3;
	WPGMZA.MarkerListing.ORDER_BY_DESCRIPTION		= 4;
	WPGMZA.MarkerListing.ORDER_BY_CATEGORY			= 5;
	WPGMZA.MarkerListing.ORDER_BY_CATEGORY_PRIORITY	= 6;
	WPGMZA.MarkerListing.ORDER_BY_DISTANCE			= 7;
	WPGMZA.MarkerListing.ORDER_BY_RATING			= 8;
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "mapID", {
		
		"get": function() {
			return this.map.id;
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "paginationEnabled", {
		
		"get": function() {
			return this._paginationEnabled;
		},
		
		"set": function(value) {
			this._paginationEnabled = (value ? true : false);
		}
		
	});
	
	/**
	 * The page size, or the default of 10 if none is set
	 */
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "pageSize", {
		
		"get": function() {
			
			if(!WPGMZA.settings.wpgmza_default_items)
				return 10;
			
			var pageSize = parseInt( WPGMZA.settings.wpgmza_default_items );
			
			if(isNaN(pageSize))
			{
				//console.warn("Invalid page size");
				return null;
			}
			
			return pageSize;
			
		},
		
		"set": function(value) {
			this.pagination("pageSize", value);
		}
		
	});
	
	/**
	 * The current page number, zero based
	 */
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "currentPage", {
		
		"get": function() {
			if(!this.paginationElement)
				return 0;
			
			try{
				return $(this.paginationElement).pagination("getSelectedPageNum") - 1;
			}catch(e) {
				//console.warn("pagination.js getSelectedPageNum failed");
				return 0;
			}
		},
		
		"set": function(value) {
			throw new Error("Not yet implemented");
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "imageWidth", {
		
		get: function() {
			var width = WPGMZA.settings.wpgmza_settings_image_width;
			
			if(!width || !(/^\d+$/.test(width)))
				return false;
				
			return width;
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "imageHeight", {
		
		get: function() {
			var height = WPGMZA.settings.wpgmza_settings_image_height;
			
			if(!height || !(/^\d+$/.test(height)))
				return false;
				
			return height;
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "style", {
		
		"get": function() {
			return this.map.settings.list_markers_by;
		}
		
	});
	
	WPGMZA.MarkerListing.prototype.initPagination = function()
	{
		if(this.paginationElement)
		{
			try{
				$(this.paginationElement).pagination("destroy");
			}catch(e) {
				//console.warn(e);
			}
			$(this.paginationElement).remove();
		}
		
		if(!this.paginationEnabled || this.showOnFilteringComplete)
			return;
		
		if(this.pageSize)
		{
			var options = this.getPaginationOptions();
			
			if(this.lastAJAXResponse.recordsFiltered <= options.pageSize)
				return;
			
			this.paginationElement = $("<div class='wpgmza-pagination'/>");
			this.pagination = $(this.paginationElement).pagination(this.getPaginationOptions());
			
			$(this.element).after(this.paginationElement);

			if(this.map.settings.wpgmza_push_in_map){
				if(this.paginationElement && WPGMZA.settings.engine === "google-maps"){

					$(this.paginationElement).css({
						"zIndex" : "999"
					});

					var position = parseInt(this.map.settings.wpgmza_push_in_map_placement);

					if(this.paginationElement.style){
						this.map.googleMap.controls[position].push(this.paginationElement);
					} else {
						if(this.paginationElement[0]){
							this.map.googleMap.controls[position].push(this.paginationElement[0]);
						}
					}
				}
			}
		}
	}
	
	WPGMZA.MarkerListing.prototype.getPaginationOptions = function()
	{
		var self = this;
		
		var options = {
			
			triggerPagingOnInit: false,
			pageSize: this.pageSize,
			
			dataSource: function(done) {
				done( self.getPaginationDataSource() )
			},
			
			callback: function(data, pagination) {
				self.pageOnPaginationReinit = $(self.paginationElement).pagination("getSelectedPageNum");
				$(self.paginationElement).pagination("disable");
				self.reload();
			}
			
		};
		
		if(this.pageOnPaginationReinit)
			options.pageNumber = this.pageOnPaginationReinit;
		
		return options;
	}
	
	WPGMZA.MarkerListing.prototype.getPaginationDataSource = function()
	{
		var source = [];
		
		if(!this.lastAJAXResponse)
			return source;
		
		for(var i = 0; i < this.lastAJAXResponse.recordsFiltered; i++)
			source.push(i);
		
		return source;
	}
	
	WPGMZA.MarkerListing.prototype.getAJAXRequestParameters = function(params)
	{
		var self = this;
		
		// Create parameters object if it doesn't exist already
		if(!params)
			params = {};
		if(!params.data)
			params.data = {};
		
		// We use POST as the requests can become quite large with marker IDs, don't want to hit the GET limit
		params.method = "POST";
		params.useCompressedPathVariable = true;
		params.cache = true;
		
		// Parse parameters passed from the server
		var str = $(this.element).attr("data-wpgmza-ajax-parameters");
		if(!str || !str.length)
			throw new Error("No AJAX parameters specified on Marker Listing attribute");
		
		var attributeParameters = JSON.parse(str);
		
		// Put PHP class and attribute parameters in params.data
		$.extend(
			params.data, 
			{
				"phpClass": $(this.element).attr("data-wpgmza-php-class"),
				"start": this.currentPage * this.pageSize,
				"length": this.pageSize
			},
			attributeParameters
		);
		
		if(this.overrideMarkerIDs)
			params.data.overrideMarkerIDs = this.overrideMarkerIDs.join(",");
		
		if(this.lastFilteringParams)
			params.data.filteringParams = this.lastFilteringParams;

		if(this.map.showDistanceFromLocation)
		{
			if(!params.data.filteringParams)
				params.data.filteringParams = {};
			
			var location = this.map.showDistanceFromLocation;
			
			params.data.filteringParams.center = {
				lat:	location.lat,
				lng:	location.lng,
				source: location.source
			};
		}
		
		// Add success callback
		params.success = function(response, textStatus, xhr) {
			self.onAJAXResponse(response, textStatus, xhr);
		};
		
		return params;
	}
	
	WPGMZA.MarkerListing.prototype.onAJAXResponse = function(response, textStatus, xhr)
	{
		this.map.showPreloader(false);
		
		this.lastAJAXResponse = response;
		
		this.onHTMLResponse(response.html);
		this.initPagination();
		
		if (typeof this.map.settings.directions_enabled !== 'undefined' && parseInt(this.map.settings.directions_enabled) == 0) {
			$('.wpgmza_marker_directions_link').remove();
		}

		
		this.trigger("markerlistingupdated");
	}
	
	WPGMZA.MarkerListing.prototype.onHTMLResponse = function(html)
	{
		var self = this;
		
		$(this.element).html(html);
		
		$(this.element).find(".wpgmza-gallery-container").each(function(index, el) {
			
			var map = self.map;
			var marker_id = $(el).closest("[data-marker-id]").attr("data-marker-id");
			var marker = map.getMarkerByID(marker_id);
			
			if(!marker.gallery)
				return;
			
			var gallery = new WPGMZA.MarkerGallery(marker, self);
			
			$(el).html("");
			$(el).append(gallery.element);
			
			
		});
		
		$(this.element).find("[data-marker-icon-src]").each(function(index, el) {
			
			var data;
			var src = $(el).attr("data-marker-icon-src");
			
			try{
				data = JSON.parse( src );
			}catch(e) {
				data = src;
			}
			
			var icon = WPGMZA.MarkerIcon.createInstance(data);
			
			icon.applyToElement(el);
			
		});
	}
	
	WPGMZA.MarkerListing.prototype.getImageElementFromURL = function(url)
	{
		var img = $("<img class='wpgmza_map_image'/>");
		
		$(img).attr("src", url);
		
		if(this.imageWidth)
			$(img).css({"width": this.imageWidth + "px"});
		
		if(this.imageHeight)
			$(img).css({"height": this.imageHeight + "px"});
		
		return img;
	}
	
	WPGMZA.MarkerListing.prototype.getRatingWidget = function(marker)
	{
		var options = {
			type: "marker",
			id: marker.id
		};
		
		if(marker.rating)
		{
			options.averageRating = marker.rating.average;
			options.numRatings = marker.rating.count;
		}
		
		var widget = WPGMZA.RatingWidget.createInstance(options);
		
		return widget;
	}
	
	WPGMZA.MarkerListing.prototype.reload = function()
	{
		// NB: This allows for the marker category filter to work even if "No marker listing" is selected
		if(!this.element)
			return;
		
		if(this.prevXHRRequest)
			this.prevXHRRequest.abort();
		
		var route = $(this.element).attr("data-wpgmza-rest-api-route");
		var params = this.getAJAXRequestParameters();
		
		this.map.showPreloader(true);
		
		this.prevXHRRequest = WPGMZA.restAPI.call(route, params);
	}
	
	WPGMZA.MarkerListing.prototype.enable = function(value)
	{
		if(!value)
			this.pagination("disable");
		else
			this.pagination("enable");
	}
	
	WPGMZA.MarkerListing.prototype.getFilteringParameters = function()
	{
		var params = {};
		
		if(this.categoryDropdown.length && this.categoryDropdown.val() != "0")
			params.categories = [this.categoryDropdown.val()];
		
		if(this.categoryCheckboxes.length)
		{
			params.categories = [];
			
			this.categoryCheckboxes.each(function(index, el) {
				
				if($(el).prop("checked"))
					params.categories.push($(el).val());
				
			});
		}
		
		return params;
	}
	
	WPGMZA.MarkerListing.prototype.pushIntoMap = function(){
		var width	= "30%";
		var height	= "50%";
		var setting;

		if(!this.element){
			return false;
		}
		
		if(WPGMZA.settings.engine == "open-layers")
		{
			console.warn("Push into map is not yet supported when using OpenLayers engine");
			return false;
		}
		
		if(this.map.settings.list_markers_by == WPGMZA.MarkerListing.STYLE_MODERN)
		{
			console.warn("Push into map is not available with modern style marker listing");
			return false;
		}
		
		if((setting = this.map.settings.wpgmza_push_in_map_width) && setting.length)
			width = setting;
		
		if((setting = this.map.settings.wpgmza_push_in_map_height) && setting.length)
			height = setting;
		
		$(this.element).css({
			"margin": 	"15px",	// TODO: Move to .wpgmza_map [data-marker-listing]
			"overflow":	"auto",
			"zIndex" : "999",
			"width":	width,
			"height":	height
		});
		
		$(this.element).addClass("wpgmza-shadow-sm wpgmza_innermap_holder");
		
		// NB: This next bit needs to be offlaoded to GoogleProMap and OLProMap
	
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				// Not yet implemented
				break;
				
			default:
				
				var position = parseInt(this.map.settings.wpgmza_push_in_map_placement);
				if(this.element.style){
					this.map.googleMap.controls[position].push(this.element);
				} else {
					if(this.element[0]){
						this.map.googleMap.controls[position].push(this.element[0]);
					}
				}
				
				break;
		}
	
		return true;
	}
	
	WPGMZA.MarkerListing.prototype.onFilteringComplete = function(event)
	{
		var self = this;
		
		if(this.showOnFilteringComplete)
		{
			$(this.element).show();
			delete this.showOnFilteringComplete;
		}
		
		this.overrideMarkerIDs = [];
		
		event.filteredMarkers.forEach(function(data) {
			self.overrideMarkerIDs.push(data.id);
		});
		
		this.lastFilteringParams = event.filteringParams;
		
		// NB: Workaround for paginatejs not resetting, as it's not aware of what's going on with our data
		this.pageOnPaginationReinit = 1;
		
		this.reload();
	}
	
	WPGMZA.MarkerListing.prototype.onItemClick = function(event) {

		var marker_id = $(event.currentTarget).attr("mid");
		var marker = this.map.getMarkerByID(marker_id);
		var listingPushedInMap = WPGMZA.maps[0].settings.push_in_map && WPGMZA.maps[0].settings.push_in_map.length;
		var clickedGetDirections = $(event.target).hasClass("wpgmza_gd");
		var zoomLevelOnClick = 13;
		
		marker.trigger("select");
		
		if(this.style != WPGMZA.MarkerListing.STYLE_MODERN && 
			!WPGMZA.settings.disable_scroll_on_marker_listing_click &&
			!clickedGetDirections &&
			!listingPushedInMap)
		{
			var offset = 0;
			
			if(WPGMZA.settings.marker_listing_item_click_scroll_offset)
				offset = parseInt(WPGMZA.settings.marker_listing_item_click_scroll_offset);
			

			// only scroll to the top if we are NOT using a "inside map" marker listing.
			if (!this.map.settings.wpgmza_push_in_map) {
				$('html, body').animate({
					scrollTop: $(this.map.element).offset().top - offset
				}, 500);
			}
		}
				
		if(this.map.settings.zoom_level_on_marker_listing_override && this.map.settings.zoom_level_on_marker_listing_click){
			zoomLevelOnClick = this.map.settings.zoom_level_on_marker_listing_click;
		} else {
			// Check for clusters
			if(this.map.settings.mass_marker_support){
				if(WPGMZA.settings.clusterAdvancedEnabled && WPGMZA.settings.clusterMaxZoom){
					zoomLevelOnClick = parseInt(WPGMZA.settings.clusterMaxZoom);
				}
			}
		}
		
		if(this.map instanceof WPGMZA.GoogleMap)
		{
			this.map.panTo(marker.getPosition());
			this.map.setZoom(zoomLevelOnClick);
		}
		else
		{
			this.map.panTo(marker.getPosition(), zoomLevelOnClick);
		}
	}
	
});