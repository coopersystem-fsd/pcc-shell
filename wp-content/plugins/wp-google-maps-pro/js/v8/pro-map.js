/**
 * @namespace WPGMZA
 * @module ProMap
 * @requires WPGMZA.Map
 */
jQuery(function($) {
	
	/**
	 * Base class for maps. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.ProMap
	 * @constructor WPGMZA.ProMap
	 * @memberof WPGMZA
	 * @param {HTMLElement} element to contain map
	 * @param {object} [options] Options to apply to this map
	 * @augments WPGMZA.Map
	 */
	WPGMZA.ProMap = function(element, options) {
		var self = this;
		
		this._markersPlaced = false;
		
		// Some objects created in the parent constructor use the category data, so load that first
		this.element = element;
		
		// Call the parent constructor
		WPGMZA.Map.call(this, element, options);
		
		// Default marker icon
		this.defaultMarkerIcon = null;
		
		if(this.settings.upload_default_marker)
			this.defaultMarkerIcon = WPGMZA.MarkerIcon.createInstance(this.settings.upload_default_marker)

		this.heatmaps = [];
		
		// Showing distance from this position
		this.showDistanceFromLocation = null;
		
		// Custom field filtering
		this.initCustomFieldFilterController();
		
		// User location
		this.initUserLocationMarker();
		
		// Update on filtering
		this.on("filteringcomplete", function() {
			//call onFilteringComplete function
			self.onFilteringComplete();
		
		});

		// Place markers
		this._onMarkersPlaced = function(event) {
			self.onMarkersPlaced(event);
		}
		this.on("markersplaced", this._onMarkersPlaced);
		
		// Cloud API
		if(WPGMZA.CloudAPI && WPGMZA.CloudAPI.isBeingUsed)
			WPGMZA.cloudAPI.call("/load");
	}
	
	WPGMZA.ProMap.prototype = Object.create(WPGMZA.Map.prototype);
	WPGMZA.ProMap.prototype.constructor = WPGMZA.ProMap;
	
	WPGMZA.ProMap.SHOW_DISTANCE_FROM_USER_LOCATION		= "user";
	WPGMZA.ProMap.SHOW_DISTANCE_FROM_SEARCHED_ADDRESS	= "searched";
	
	/*
	<select id="wpgmza_push_in_map_placement" name="wpgmza_push_in_map_placement" class="postform">
		<option value="1" selected="">Top Center</option>
		<option value="2">Top Left</option>
		<option value="3">Top Right</option>
		<option value="4">Left Top </option>
		<option value="5">Right Top</option>
		<option value="6">Left Center</option>
		<option value="7">Right Center</option>
		<option value="8">Left Bottom</option>
		<option value="9">Right Bottom</option>
		<option value="10">Bottom Center</option>
		<option value="11">Bottom Left</option>
		<option value="12">Bottom Right</option>
	</select>
	*/
	
	WPGMZA.ProMap.ControlPosition = {
		TOP_CENTER:		1,
		TOP_LEFT:		2,
		TOP_RIGHT:		3,
		LEFT_TOP:		4,
		RIGHT_TOP:		5,
		LEFT_CENTER:	6,
		RIGHT_CENTER:	7,
		LEFT_BOTTOM:	8,
		RIGHT_BOTTOM:	9,
		BOTTOM_CENTER:	10,
		BOTTOM_LEFT:	11,
		BOTTOM_RIGHT:	12
	};
	
	Object.defineProperty(WPGMZA.ProMap.prototype, "mashupIDs", {
		
		get: function() {
			
			var result = [];
			var attr = $(this.element).attr("data-mashup-ids");
			
			if(attr && attr.length)
				result = result = attr.split(",");
			
			return result;
			
		}
		
	});
	
	/**
	 * Whether directions are enabled or not
	 *  
	 * @name WPGMZA.ProMap#directionsEnabled
	 * @type Boolean
	 */
	Object.defineProperty(WPGMZA.ProMap.prototype, "directionsEnabled", {
		
		get: function() {
			return this.settings.directions_enabled == 1;
		}
		
	});
	
	/**
	 * Called by the engine specific map classes when the map has fully initialised
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.Event} The event
	 * @listens module:WPGMZA.Map~init
	 */
	WPGMZA.ProMap.prototype.onInit = function(event)
	{
		var self = this;
		
		WPGMZA.Map.prototype.onInit.apply(this, arguments);
		
		this.initDirectionsBox();
		
		if(this.shortcodeAttributes.lat && this.shortcodeAttributes.lng){
			var latLng = new WPGMZA.LatLng({
				lat: this.shortcodeAttributes.lat,
				lng: this.shortcodeAttributes.lng
			});
			
			this.setCenter(latLng);

			if(this.shortcodeAttributes.mark_center && this.shortcodeAttributes.mark_center === 'true'){
				var centerMarker = WPGMZA.Marker.createInstance({
					lat : this.shortcodeAttributes.lat,
					lng : this.shortcodeAttributes.lng,
					address : this.shortcodeAttributes.lat + ", " + this.shortcodeAttributes.lng
				});

				this.addMarker(centerMarker);
			}
		} else if(this.shortcodeAttributes.address){
			var geocoder = WPGMZA.Geocoder.createInstance(); // Will return a GoogleGeocoder or OLGeocoder depending on engine selection
			
			geocoder.geocode({address: this.shortcodeAttributes.address}, function(results, status) {
				
				if(status != WPGMZA.Geocoder.SUCCESS)
				{
					console.warn("Shortcode attribute address could not be geocoded");
					return;
				}
				
				self.setCenter(results[0].latLng); 	// I think - not sure about the format off the top of my head. May need to log results
				
			});
		}
		
		var zoom;
		if(zoom = WPGMZA.getQueryParamValue("mzoom")){
			this.setZoom(zoom);
		}
		
		if(WPGMZA.getCurrentPage() != WPGMZA.PAGE_MAP_EDIT && this.settings.automatically_pan_to_users_location == "1"){

			WPGMZA.getCurrentPosition(function(result) {
				if(!self.userLocationMarker){
					/* No user marker yet. Init function returns early if no marker should be shown */
					self.initUserLocationMarker(result);
				}

				self.setCenter(
					new WPGMZA.LatLng({
						lat: result.coords.latitude,
						lng: result.coords.longitude
					})
				);
					
				if(self.settings.override_users_location_zoom_level)
					self.setZoom(self.settings.override_users_location_zoom_levels);
					
			});
		}
	}
	
	/**
	 * Called when all the markers have been loaded and placed
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.Event} The event
	 * @listens module:WPGMZA.ProMap~markersplaced
	 */
	WPGMZA.ProMap.prototype.onMarkersPlaced = function(event)
	{
		var self = this;
		
		// NB: Marker listing. We delay this til here because the marker gallery will need to fetch marker data from here
		// A good alternative to this would be to transmit the marker data in a data- attribute
		
		var jumpToNearestMarker = (WPGMZA.is_admin == 0 && self.settings.jump_to_nearest_marker_on_initialization == 1);
		
		if(this.settings.order_markers_by == WPGMZA.MarkerListing.ORDER_BY_DISTANCE || this.settings.show_distance_from_location == 1 || jumpToNearestMarker)
		{
			WPGMZA.getCurrentPosition(function(result) {
				
				var location = new WPGMZA.LatLng({
					lat: result.coords.latitude,
					lng: result.coords.longitude
				});
				
				self.userLocation = location;
				self.userLocation.source = WPGMZA.ProMap.SHOW_DISTANCE_FROM_USER_LOCATION;
				
				self.showDistanceFromLocation = location;

				self.updateInfoWindowDistances();
				
				if(self.markerListing)
					if(self.markersPlaced)
						self.markerListing.reload();
					else
					{					
						self.on("markersplaced", function(event) {
							self.markerListing.reload();
						});
					}
				
				// Checks if jump_to_nearest_marker_on_initialization setting is enabled, only on the front end though
				if(jumpToNearestMarker)
					self.panToNearestMarker(location);
				
			}, function(error) {
				
				if(self.markerListing)
					self.markerListing.reload();
				
			});
		}

		if(self.settings.fit_maps_bounds_to_markers && self.markers.length > 0){
			self.fitBoundsToMarkers();
		}

		self.initMarkerListing();

		// Clustering
		// TODO: Move to Gold with a listener
		if(this.settings.mass_marker_support == 1 && WPGMZA.MarkerClusterer)
		{
			var options = {};
			
			if(WPGMZA.settings.wpgmza_cluster_advanced_enabled)
			{
				var styles = [];
				
				options.gridSize		= parseInt( WPGMZA.settings.wpgmza_cluster_grid_size );
				options.maxZoom			= parseInt( WPGMZA.settings.wpgmza_cluster_max_zoom );
				options.minClusterSize	= parseInt( WPGMZA.settings.wpgmza_cluster_min_cluster_size );
				options.zoomOnClick		= WPGMZA.settings.wpgmza_cluster_zoom_click ? true : false;
				
				for(var i = 1; i <= 5; i++) {
					level = {};
					level.url		= WPGMZA.settings["clusterLevel" + i].replace(/%2F/g, "/");
					level.width		= parseInt( WPGMZA.settings["clusterLevel" + i + "Width"] );
					level.height	= parseInt( WPGMZA.settings["clusterLevel" + i + "Height"] );
					
					level.textColor	= WPGMZA.settings.wpgmza_cluster_font_color;
					level.textSize	= parseInt( WPGMZA.settings.wpgmza_cluster_font_size );
					
					styles.push(level);
				}
				
				options.styles = styles;
			}
			
			
			this.markerClusterer = new WPGMZA.MarkerClusterer(this, null, options);
			this.markerClusterer.addMarkers(this.markers);
		}
	}
	
	WPGMZA.ProMap.prototype.getRESTParameters = function(options)
	{
		var params = WPGMZA.Map.prototype.getRESTParameters.apply(this, arguments);
		
		if(this.settings.only_load_markers_within_viewport && this.initialFetchCompleted)
		{
			// NB: We only want *markers* within the visible boundaries. We already have the other featuers, so make sure they're not fetched again.
			params.include = "markers";
		}
		
		return params;
	}
	
	WPGMZA.ProMap.prototype.fetchFeatures = function()
	{
		var self = this;
		
		if(this.settings.only_load_markers_within_viewport)
		{
			// NB: Force REST pull and wait for idle event, the bounds aren't available until the map has initialised. XML pull won't work with this feature.
			
			this.on("idle", function(event) {
				
				self.fetchFeaturesViaREST();
				self.initialFetchCompleted = true;
				
			});
			
			return;
		}
		
		WPGMZA.Map.prototype.fetchFeatures.apply(this, arguments);
	}
	
	WPGMZA.ProMap.prototype.onMarkersFetched = function(data, expectMoreBatches)
	{
		if(this.settings.only_load_markers_within_viewport)
		{
			// NB: Remove existing markers before adding
			this.removeAllMarkers();
		}
		
		WPGMZA.Map.prototype.onMarkersFetched.apply(this, arguments);
	}
	
	/**
	 * Pans to the nearest marker to the specified latlng, or the center of the map if no latlng is specified
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.LatLng} [latlng] Pan to the nearest marker to this latlng, optional. The center is used if no value is specified.
	 */
	WPGMZA.ProMap.prototype.panToNearestMarker = function(latlng)
	{
		var closestMarker;
		var distance = Infinity;

		if(!latlng)
			latlng = this.getCenter();

    	// Loop through each marker on this map
    	for (var i = 0; i < this.markers.length; i++) {

        	// Calculate the distance from the latlng passed in to marker[i]
        	var distanceToMarker = WPGMZA.Distance.between(latlng, this.markers[i].getPosition());
        
        	// Is this closer than our current recorded nearest marker?
        	if(distanceToMarker < distance)
        	{
            	// Yes it is, store marker[i] as the closest marker
            	closestMarker = this.markers[i];
            
            	// Store the distance as the new closest difference
            	distance = distanceToMarker;
        	}
		}

    	// Now that the loop has completed, marker will hold the nearest marker to latlng (or null if there are no markers on this map)
    	if(!closestMarker)
        	return;
    
   		 // Pan to it
    	this.panTo(closestMarker.getPosition(this.setZoom(7)));
	}
	
	/**
	 * Fits the map boundaries to any unfiltered (visible) markers in the specified array, or all markers on the map if no markers are specified.
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.Marker[]} [markers] Markers to fit the map boundaries to. If no markers are specified, all markers are used.
	 */
	WPGMZA.ProMap.prototype.fitBoundsToMarkers = function(markers)
	{
		var bounds = new WPGMZA.LatLngBounds();
		
		if(!markers)
			markers = this.markers;
		
		// Loop through the markers
		for (var i = 0; i < markers.length; i++)
		{
			if(!(markers[i] instanceof WPGMZA.Marker))
				throw new Error("Invalid input, not a WPGMZA.Marker");
			
			if (!markers[i].isFiltered)
			{
				// Set map bounds to these markers
				bounds.extend(markers[i]);
			}
		}
		
		this.fitBounds(bounds);
	}
	
	// NB: Legacy support
	WPGMZA.ProMap.prototype.fitMapBoundsToMarkers = WPGMZA.ProMap.prototype.fitBoundsToMarkers;

	/**
	 * Resets the map latitude, longitude and zoom to their starting values in the map settings.
	 * @method
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.resetBounds = function()
	{
		var latlng = new WPGMZA.LatLng(this.settings.map_start_lat, this.settings.map_start_lng);
		this.panTo(latlng);
		this.setZoom(this.settings.map_start_zoom);
	}

	/**
	 * Callback for when the marker filter has completed
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @listens module:WPGMZA.Map~onFilteringComplete
	 */
	WPGMZA.ProMap.prototype.onFilteringComplete = function()
	{
		// Check if Fit map bounds to markers after filtering setting is enabled
		if(this.settings.fit_maps_bounds_to_markers_after_filtering == '1')
		{
			var self = this;
			var areMarkersVisible = false;
			
			// Loop through the markers
			for (var i = 0; i < this.markers.length; i++) 
			{
				if(!this.markers[i].isFiltered){
					// Total markers filtered
					areMarkersVisible = true;
					break;
				}
			}		
			
			if(areMarkersVisible)
			{
				// If total markers filtered is more than 0, call fitMapBoundsToMarkers function
				self.fitBoundsToMarkers();
			}
		}
	}
	
	/**
	 * Initialises the marker listing
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initMarkerListing = function()
	{
		if(WPGMZA.is_admin == "1")
			return;	// NB: No marker listings on the back end
		
		/*if(this.markerListing)
		{
			console.warn("Marker listing already initialized. No action will be taken.");
			return;
		}*/
		
		var markerListingElement = $("[data-wpgmza-marker-listing][id$='_" + this.id + "']");
		
		// NB: This is commented out to allow the category filter to still function with "No marker listing". This will be rectified in the future with a unified filtering interface
		//if(markerListingElement.length)
		this.markerListing = WPGMZA.MarkerListing.createInstance(this, markerListingElement[0]);

		this.off("markersplaced", this._onMarkersPlaced);
		delete this._onMarkersPlaced;
	}
	
	/**
	 * Initialises the custom field filter controller
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initCustomFieldFilterController = function()
	{
		this.customFieldFilterController = WPGMZA.CustomFieldFilterController.createInstance(this.id);
		
		if(WPGMZA.useLegacyGlobals && wpgmzaLegacyGlobals.MYMAP[this.id])
			wpgmzaLegacyGlobals.MYMAP[this.id].customFieldFilterController = this.customFieldFilterController;
	}
	
	/**
	 * Initialises the user location marker, if the setting is enabled
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initUserLocationMarker = function(cachedPos) {
		var self = this;
		
		if(this.settings.show_user_location != 1 || parseInt(WPGMZA.is_admin) == 1)
			return;
		
		var icon = this.settings.upload_default_ul_marker;
		var options = {
			id: WPGMZA.guid(),
			animation: WPGMZA.Marker.ANIMATION_DROP,
			user_location : true
		};
		
		if(icon && icon.length)
			options.icon = icon;
		
		if(this.settings.upload_default_ul_marker_retina){
			options.retina = true;
		}

		var marker = WPGMZA.Marker.createInstance(options);

		marker.isFilterable = false;
		marker.setOptions({
			zIndex: 999999
		});

		marker._icon.retina = marker.retina;

		if(cachedPos && cachedPos.coords){
			/* This function received a cached version of the user position for the init */
			marker.setPosition({
				lat: cachedPos.coords.latitude,
				lng: cachedPos.coords.longitude
			});

			if(!marker.map)
				self.addMarker(marker);
			
			if(!self.userLocationMarker){
				self.userLocationMarker = marker;
				self.trigger("userlocationmarkerplaced");
			}
		}

		WPGMZA.watchPosition(function(position) {
			
			marker.setPosition({
				lat: position.coords.latitude,
				lng: position.coords.longitude
			});
			
			if(!marker.map)
				self.addMarker(marker);
			
			if(!self.userLocationMarker)
			{	
				self.userLocationMarker = marker;
				self.trigger("userlocationmarkerplaced");
			}
			
		});
	}
	
	/**
	 * Initialises the directions box on the front end, if the setting is enabled
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initDirectionsBox = function()
	{
		if(WPGMZA.is_admin == 1)
			return;
		
		if(!this.directionsEnabled)
			return;
		
		this.directionsBox = WPGMZA.DirectionsBox.createInstance(this);
	}
	
	/**
	 * Adds the specified heatmap to the map
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.addHeatmap = function(heatmap)
	{
		if(!(heatmap instanceof WPGMZA.Heatmap))
			throw new Error("Argument must be an instance of WPGMZA.Heatmap");
		
		heatmap.map = this;
		
		this.heatmaps.push(heatmap);
		this.dispatchEvent({type: "heatmapadded", heatmap: heatmap});
	}
	
	/**
	 * Gets a heatmap by ID
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.getHeatmapByID = function(id)
	{
		for(var i = 0; i < this.heatmaps.length; i++)
			if(this.heatmaps[i].id == id)
				return this.heatmaps[i];
			
		return null;
	}
	
	/**
	 * Removes the specified heatmap and fires an event
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.removeHeatmap = function(heatmap)
	{
		if(!(heatmap instanceof WPGMZA.Heatmap))
			throw new Error("Argument must be an instance of WPGMZA.Heatmap");
		
		if(heatmap.map != this)
			throw new Error("Wrong map error");
		
		heatmap.map = null;
		
		// TODO: This shoud not be here in the generic class
		if(heatmap instanceof WPGMZA.GoogleHeatmap)
			heatmap.googleHeatmap.setMap(null);
		
		this.heatmaps.splice(this.heatmaps.indexOf(heatmap), 1);
		this.dispatchEvent({type: "heatmapremoved", heatmap: heatmap});
	}
	
	/**
	 * Removes the specified heatmap and fires an event
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.removeHeatmapByID = function(id)
	{
		var heatmap = this.getHeatmapByID(id);
		
		if(!heatmap)
			return;
		
		this.removeHeatmap(heatmap);
	}
	
	/**
	 * Get's the selected infowindow style for this map, or the global style if "inherit" is selected.
	 * 
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return {mixed} The InfoWindow style, see WPGMZA.ProInfoWindow for possible values
	 */
	WPGMZA.ProMap.prototype.getInfoWindowStyle = function()
	{
		if(!this.settings.other_settings)
			return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		
		var local = this.settings.other_settings.wpgmza_iw_type;
		var global = WPGMZA.settings.wpgmza_iw_type;
		
		if(local == "-1" && global == "-1")
			return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		
		if(local == "-1")
			return global;
		
		if(local)
			return local;
		
		return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
	}
	
	WPGMZA.ProMap.prototype.getFilteringParameters = function()
	{
		
	}

	
	
	/**
	 * Called internally to update the infowindow distances, for example, when the users location has changed or a new search has been performed
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.updateInfoWindowDistances = function()
	{
		var location = this.showDistanceFromLocation;
		
		this.markers.forEach(function(marker) {
			
			if(!marker.infoWindow)
				return;
			
			marker.infoWindow.updateDistanceFromLocation();
			
		});
	}
	
	/**
	 * Find out if the map has visible markers. Only counts filterable markers (not the user location marker, store locator center point marker, etc.)
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @returns {Boolean} True if at least one marker is visible
	 */
	WPGMZA.ProMap.prototype.hasVisibleMarkers = function()
	{
		 // grab markers
		 var markers = this.markers;
		 
		 // loop through all the markers
		 for (var i = 0; i < markers.length; i++)
		 {
			 // Find only visible markers after filtering
			 if(markers[i].isFilterable && markers[i].getVisible())
				return true;
		 }
		 
		 return false;
	}
	
	WPGMZA.ProMap.prototype.pushElementIntoMapPanel = function(element, position)
	{
		
	}

	WPGMZA.ProMap.prototype.onClick = function(event)
	{
		var self = this;
		
		if(this.settings.close_infowindow_on_map_click)
		{	
			if(event.target instanceof WPGMZA.Map)
			{
				if(this.lastInteractedMarker !== undefined && this.lastInteractedMarker.infoWindow){
					this.lastInteractedMarker.infoWindow.close();	

					if($(this.lastInteractedMarker.infoWindow.element).hasClass('wpgmza_modern_infowindow')){
						$(this.lastInteractedMarker.infoWindow.element).remove();
					}
				}
			}
		}
	}
	
	jQuery(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function() {
        var isFullScreen = document.fullScreen ||
            document.mozFullScreen ||
            document.webkitIsFullScreen;
        var modernMarkerButton = jQuery('.wpgmza-modern-marker-open-button');
        var modernPopoutPanel = jQuery('.wpgmza-popout-panel');
        var modernStoreLocator = jQuery('.wpgmza-modern-store-locator');
        var fullScreenMap = undefined;
        if (modernMarkerButton.length) {
            fullScreenMap = modernMarkerButton.parent('.wpgmza_map').children('div').first();
        } else if (modernPopoutPanel.length) {
            fullScreenMap = modernPopoutPanel.parent('.wpgmza_map').children('div').first();
        } else {
            fullScreenMap = modernStoreLocator.parent('.wpgmza_map').children('div').first();
        }
        if (isFullScreen && typeof fullScreenMap !== "undefined") {
            fullScreenMap.append(modernMarkerButton, modernPopoutPanel, modernStoreLocator);
        }
    });
	
});