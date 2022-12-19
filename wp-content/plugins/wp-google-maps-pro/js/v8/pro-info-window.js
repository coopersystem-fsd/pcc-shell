/**
 * @namespace WPGMZA
 * @module ProInfoWindow
 * @requires WPGMZA.InfoWindow
 */
jQuery(function($) {
	
	WPGMZA.ProInfoWindow = function(feature)
	{
		var self = this;
		
		WPGMZA.InfoWindow.call(this, feature);
		
		this.on("infowindowopen", function(event) {
			self.updateDistanceFromLocation();
			self.showDistanceAwayFromStoreLocatorCenter();
		});
	}
	
	WPGMZA.ProInfoWindow.prototype = Object.create(WPGMZA.InfoWindow.prototype);
	WPGMZA.ProInfoWindow.prototype.constructor = WPGMZA.ProInfoWindow;
	
	WPGMZA.ProInfoWindow.STYLE_INHERIT			= "-1";
	WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE	= "0";
	WPGMZA.ProInfoWindow.STYLE_MODERN			= "1";
	WPGMZA.ProInfoWindow.STYLE_MODERN_PLUS		= "2";
	WPGMZA.ProInfoWindow.STYLE_MODERN_CIRCULAR	= "3";
	WPGMZA.ProInfoWindow.STYLE_TEMPLATE			= "template";
	
	WPGMZA.ProInfoWindow.OPEN_BY_CLICK			= 1;
	WPGMZA.ProInfoWindow.OPEN_BY_HOVER			= 2;
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "maxWidth", {
		
		get: function() {
			var width = WPGMZA.settings.wpgmza_settings_infowindow_width;
			
			if(!width || !(/^\d+$/.test(width)))
				return false;
			
			return width;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "imageWidth", {
		
		get: function() {
			var width = WPGMZA.settings.wpgmza_settings_image_width;
			
			if(!width || !(/^\d+$/.test(width)))
				return false;
				
			return width;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "imageHeight", {
		
		get: function() {
			var height = WPGMZA.settings.wpgmza_settings_image_height;
			
			if(!height || !(/^\d+$/.test(height)))
				return false;
				
			return height;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "enableImageResizing", {
		
		get: function() {
			return WPGMZA.settings.wpgmza_settings_image_resizing == true;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "linkTarget", {
		
		get: function() {
			return WPGMZA.settings.infoWindowLinks == true ? "_BLANK" : "";
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "linkText", {
		
		get: function() {
			return WPGMZA.localized_strings.more_info;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "directionsText", {
		
		get: function() {
			return WPGMZA.localized_strings.get_directions;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "distanceUnits", {
		
		get: function() {
			return this.feature.map.settings.store_locator_distance == 1 ? WPGMZA.Distance.MILES : WPGMZA.Distance.KILOMETERS;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "showAddress", {
		
		get: function() {
			return (WPGMZA.settings.infoWindowAddress != true || WPGMZA.currentPage == 'map-edit' ? true : false);
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "style", {
		
		get: function() {
			
			if(this.map && this.map.userLocationMarker == this)
				return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
			
			return this.getSelectedStyle();
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "isPanIntoViewAllowed", {
		
		"get": function()
		{
			return (this.style == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE);
		}
		
	});
	
	WPGMZA.ProInfoWindow.prototype.getSelectedStyle = function()
	{
		var globalTypeSetting = WPGMZA.settings.wpgmza_iw_type;
		var localTypeSetting = this.feature.map.settings.wpgmza_iw_type;
		var type = localTypeSetting;
		
		if(localTypeSetting == WPGMZA.ProInfoWindow.STYLE_INHERIT ||
			typeof localTypeSetting == "undefined")
		{
			type = globalTypeSetting;
			
			if(type == WPGMZA.ProInfoWindow.STYLE_INHERIT)
				return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		}
			
		if(!type)
			return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
			
		return String(type);
	}
	
	WPGMZA.ProInfoWindow.prototype.getImageElementFromURL = function(url)
	{
		var img = $("<img/>");
			
		img.addClass("wpgmza_infowindow_image");
		img.attr("src", url);
		img.css({"float": "right"});
		
		if(this.maxWidth)
			img.css({"max-width": this.maxWidth});
		
		if(this.enableImageResizing && this.imageWidth)
		{
			img.css({"width": this.imageWidth});
			img.css({"height": this.imageHeight});
		}
		
		if(!this.enableImageResizing)
			img.css({"margin": "5px"});
		
		return img;
	}
	
	WPGMZA.ProInfoWindow.prototype.showDistanceAwayFromStoreLocatorCenter = function() {
		if (this.feature instanceof WPGMZA.Marker) {
			// Store locator distance away
			// added by Nick 2020-01-12
			if (this.feature.map.settings.store_locator_show_distance && this.feature.map.storeLocator && (this.feature.map.storeLocator.state == WPGMZA.StoreLocator.STATE_APPLIED)) {
				if(this.feature.map.settings.show_distance_from_location){
					// Allow the updateDistanceFromLocation method handle everything
					return;
				}
				var currentLatLng = this.feature.getPosition();
				var distance = this.workOutDistanceBetweenTwoMarkers(this.feature.map.storeLocator.center, currentLatLng);

				$(this.element).append("<p>"+(this.feature.map.settings.store_locator_distance == WPGMZA.Distance.KILOMETERS ? distance + WPGMZA.localized_strings.kilometers_away : distance + " " +WPGMZA.localized_strings.miles_away)+"</p>");	
			} 

		}
	}

	WPGMZA.ProInfoWindow.prototype.updateDistanceFromLocation = function() {
		var marker = this.feature;
		
		if(!(marker instanceof WPGMZA.Marker)) {
			console.warn("This function is only intended for use with markers and should not have been called in this manner");
			return;
		}
		
		var location = marker.map.showDistanceFromLocation;
		
		if(!location)
			return; // No location (no search performed, user location unavailable)
		
		var distanceInKM = WPGMZA.Distance.between(location, marker.getPosition());
		var distanceToDisplay = distanceInKM;
			
		if(this.distanceUnits == WPGMZA.Distance.MILES)
			distanceToDisplay /= WPGMZA.Distance.KILOMETERS_PER_MILE;
		
		var text = Math.round(distanceToDisplay, 2);
		var source = location.source == WPGMZA.ProMap.SHOW_DISTANCE_FROM_USER_LOCATION ? WPGMZA.localized_strings.from_your_location : WPGMZA.localized_strings.from_searched_location;
		
		$(this.element).find(".wpgmza-distance-from-location .wpgmza-amount").text(text);
		$(this.element).find(".wpgmza-distance-from-location .wpgmza-source").text(source);
	}
	
	WPGMZA.ProInfoWindow.prototype.legacyCreateDefaultInfoWindow = function(map) {
		var marker = this.feature;
		var map = marker.map;
		
		function empty(field) {
			return !(field && field.length && field.length > 0);
		}
		
		var container = $("<div class='wpgmza_markerbox scrollFix'></div>");
		
		if(this.maxWidth)
			container.css({"max-width": this.maxWidth});
		
		if(!empty(marker.gallery)) {
			var gallery = new WPGMZA.MarkerGallery(marker, this);
			container.append(gallery.element);
		}
		else if(!empty(marker.pic)) {
			// Fallback for legacy picture, which was before the marker gallery was implemented in v8. This SHOULD have been taken care of on the server by ProMarker, but this fallback is provided just in case. This can be deprecated in the future
			var img = this.getImageElementFromURL(marker.pic);
			container.append(img);
		}

		if(!empty(marker.title)) {
			var p = $("<p class='wpgmza_infowindow_title'></p>");
			
			p.html(marker.title);
			
			container.append(p);
		}
		
		if(!empty(marker.address) && this.showAddress) {
		
			var p = $("<p class='wpgmza_infowindow_address'></p>");
			p.html(marker.address);
			container.append(p);
		
		}
		
		if(!empty(marker.desc) || !empty(marker.description)) {
			var description = empty(marker.desc) ? marker.description : marker.desc;
			var div = $("<div class='wpgmza_infowindow_description'></div>");
			
			div.html(description);
			
			container.append(div);
		}
		
		if(map.settings.show_distance_from_location == 1) {
			var p = $("<p class='wpgmza-distance-from-location'><span class='wpgmza-amount'></span> <span class='wpgmza-units'></span> <span class='wpgmza-source'></span></p>");
			
			var units = this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.localized_strings.miles_away : WPGMZA.localized_strings.kilometers_away;
			
			p.find(".wpgmza-units").text(units);
			
			container.append(p);
		}
		
		if(!empty(marker.linkd) || !empty(marker.link)) {
			var link = empty(marker.link) ? marker.linkd : marker.link;
			var p = $("<p class='wpgmza_infowindow_link'></p>");
			var a = $("<a class='wpgmza_infowindow_link'></a>");
			
			a.attr("href", WPGMZA.decodeEntities(link));
			a.attr("target", this.linkTarget);
			a.text(this.linkText);
			
			p.append(a);
			container.append(p);
		}
		
		if(map.directionsEnabled && !(parseInt(WPGMZA.is_admin) === 1) && marker.getPosition) {
			var p = $("<p></p>");
			var a = $("<a class='wpgmza_gd'></a>");
			
			a.attr("href", "javascript: ;");
			a.attr("id", map.id);
			
			a.attr("data-address", marker.address);
			a.attr("data-latlng", marker.getPosition().toString());
			a.attr("data-marker-id", marker.id);
			
			// Legacy fields
			a.attr("wpgm_addr_field", marker.address);
			a.attr("gps", marker.lat+","+marker.lng);
			
			a.text(this.directionsText);
			
			p.append(a);
			container.append(p);
		}
		
		if(marker.custom_fields_html)
			container.append(marker.custom_fields_html);


		container.append(this.addEditButton());

		this.setContent(container.html());
	}
	
	WPGMZA.ProInfoWindow.prototype.legacyCreateModernInfoWindow = function(map)
	{
		// Legacy code
		var mapid = map.id;
		var self = this;
		
		if($("#wpgmza_iw_holder_" + map.id).length == 0)
			$(document.body).append("<div id='wpgmza_iw_holder_" + map.id + "'></div>");
		else
			return;
		
		var legend = document.getElementById('wpgmza_iw_holder_' + mapid);
		if (legend !== null)
			$(legend).remove();
		
		if(!window.wpgmza_iw_Div)
			window.wpgmza_iw_Div = [];

		wpgmza_iw_Div[mapid] = document.createElement('div');
		wpgmza_iw_Div[mapid].id = 'wpgmza_iw_holder_' + mapid;
		wpgmza_iw_Div[mapid].style = 'display:block;';
		document.getElementsByTagName('body')[0].appendChild(wpgmza_iw_Div[mapid]);

		wpgmza_iw_Div_inner = document.createElement('div');
		wpgmza_iw_Div_inner.className = 'wpgmza_modern_infowindow_inner wpgmza_modern_infowindow_inner_' + mapid;
		wpgmza_iw_Div[mapid].appendChild(wpgmza_iw_Div_inner);

		wpgmza_iw_Div_close = document.createElement('div');
		wpgmza_iw_Div_close.className = 'wpgmza_modern_infowindow_close';
		wpgmza_iw_Div_close.setAttribute('mid', mapid);
		
		$(wpgmza_iw_Div_close).on("click", function(event) {
			$(wpgmza_iw_Div[mapid]).remove();
		});

		var t = document.createTextNode("x");
		wpgmza_iw_Div_close.appendChild(t);
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_Div_close);

		wpgmza_iw_Div_img = document.createElement('div');
		wpgmza_iw_Div_img.className = 'wpgmza_iw_image';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_Div_img);

		wpgmza_iw_img = document.createElement('img');
		wpgmza_iw_img.className = 'wpgmza_iw_marker_image';
		wpgmza_iw_img.src = '';
		wpgmza_iw_img.style = 'max-width:100%;';
		wpgmza_iw_Div_img.appendChild(wpgmza_iw_img);

		wpgmza_iw_img_div = document.createElement('div');
		wpgmza_iw_img_div.className = 'wpgmza_iw_title';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_img_div);

		wpgmza_iw_img_div_p = document.createElement('p');
		wpgmza_iw_img_div_p.className = 'wpgmza_iw_title_p';
		wpgmza_iw_img_div.appendChild(wpgmza_iw_img_div_p);


		if(!WPGMZA.settings.wpgmza_settings_infowindow_address){
			wpgmza_iw_address_div = document.createElement('div');
			wpgmza_iw_address_div.className = 'wpgmza_iw_address';
			wpgmza_iw_Div_inner.appendChild(wpgmza_iw_address_div);
			
			wpgmza_iw_address_p = document.createElement('p');
			wpgmza_iw_address_p.className = 'wpgmza_iw_address_p';
			wpgmza_iw_address_div.appendChild(wpgmza_iw_address_p);
		}


		wpgmza_iw_description = document.createElement('div');
		wpgmza_iw_description.className = 'wpgmza_iw_description';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_description);

		wpgmza_iw_description_p = document.createElement('p');
		wpgmza_iw_description_p.className = 'wpgmza_iw_description_p';
		wpgmza_iw_description.appendChild(wpgmza_iw_description_p);
		
		if(map.settings.show_distance_from_location == 1) {
			var p = $("<p class='wpgmza-distance-from-location'><span class='wpgmza-amount'></span> <span class='wpgmza-units'></span> <span class='wpgmza-source'></span></p>");
			
			var units = this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.localized_strings.miles_away : WPGMZA.localized_strings.kilometers_away;
			
			p.find(".wpgmza-units").text(units);
			
			$(wpgmza_iw_Div_inner).append(p);
		}
		
		var ratingContainer = $("<div class='wpgmza-rating-container'></div>")[0];
		wpgmza_iw_Div_inner.appendChild(ratingContainer);

		// Custom fields
		$(wpgmza_iw_Div_inner).append("<div class='wpgmza_iw_custom_fields'/>");
		
		wpgmza_iw_buttons = document.createElement('div');
		wpgmza_iw_buttons.className = 'wpgmza_iw_buttons';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_buttons);

		wpgmza_directions_button = document.createElement('a');
		wpgmza_directions_button.className = 'wpgmza_button wpgmza_left wpgmza_directions_button';
		wpgmza_directions_button.src = '#';
		
		var t = document.createTextNode(WPGMZA.localized_strings.directions);
		wpgmza_directions_button.appendChild(t);
		wpgmza_iw_buttons.appendChild(wpgmza_directions_button);

		wpgmza_more_info_button = document.createElement('a');
		wpgmza_more_info_button.className = 'wpgmza_button wpgmza_right wpgmza_more_info_button';
		wpgmza_more_info_button.src = '#';
		
		var t = document.createTextNode(WPGMZA.localized_strings.more_info);
		wpgmza_more_info_button.appendChild(t);
		wpgmza_iw_buttons.appendChild(wpgmza_more_info_button);

		var legend = document.getElementById('wpgmza_iw_holder_' + mapid);
		$(legend).css('display', 'block');
		$(legend).addClass('wpgmza_modern_infowindow');
		$(legend).addClass('wpgmza-shadow');
		
		
		
		if (WPGMZA.settings.engine == "google-maps")
		{
			var map = this.feature.map;
			map.googleMap.controls[google.maps.ControlPosition.RIGHT_TOP].push(legend);
		}
		else {
			var container = $(".wpgmza-ol-modern-infowindow-container[data-map-id='" + mapid + "']");
			if (!container.length) {
				container = $("<div class='wpgmza-ol-modern-infowindow-container' data-map-id='" + mapid + "'></div>");
				$(".wpgmza_map[data-map-id='" + mapid + "']").append(container);
			}

			container.append(legend);
		}

	}
	
	WPGMZA.ProInfoWindow.prototype.open = function(map, feature)
	{
		var self = this;
		
		// Legacy support
		if(window.infoWindow)
			infoWindow[feature.map.id] = this;
		
		if(!WPGMZA.InfoWindow.prototype.open.call(this, map, feature))
			return false;	// Parent class has detected that the window shouldn't open
		
		if(this.feature == map.userLocationMarker)
			return true;	// Allow the default style window to open for user location markers
		
		if(map.settings.wpgmza_list_markers_by == WPGMZA.MarkerListing.STYLE_MODERN)
			return false;	// Don't show if modern style marker listing is selected
		
		if(WPGMZA.settings.wpgmza_settings_disable_infowindows)
			return false;	// Global setting "disable infowindows" is set
		
		// Legacy support
		if(this.style == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE || WPGMZA.currentPage == "map-edit")
		{
			this.legacyCreateDefaultInfoWindow();
			return true;	// Always show default style when on map edit page
		}
		
		var marker_data;
		var data = wpgmaps_localize_marker_data[map.id];
		var marker = feature;
		
		if(typeof data == "array")
			for(var i = 0; i < data.length; i++)
			{
				if(data[i].marker_id == feature.id)
				{
					marker_data = data[i];
					
					break;
				}
			}
		else if(typeof data == "object")
			for(var key in data)
			{
				if(data[key].marker_id == feature.id)
				{
					marker_data = data[key];
					
					break;
				}
			}
		
		/** Deprecated this failure block as we reference features directly now */
		/*if(!marker_data){
			console.warn("Failed to find marker data for marker " + feature.id);
			return false;
		}*/
		
		this.legacyCreateModernInfoWindow(map);
		
		if(window.modern_iw_open)
			modern_iw_open[map.id] = true;

		var element = this.element = jQuery("#wpgmza_iw_holder_" + map.id);

		// Reset the contents first
		element.find(".wpgmza_iw_marker_image").attr("src",""); 
		element.find(".wpgmza_iw_title").html(""); 
		element.find(".wpgmza_iw_description").html(""); 
		element.find(".wpgmza_iw_address_p").html(""); 


		element.find(".wpgmza_more_info_button").attr("href","#"); 
		element.find(".wpgmza_more_info_button").attr("target",""); 
		element.find(".wpgmza_directions_button").attr("gps",""); 
		element.find(".wpgmza_directions_button").attr("href","#"); 
		element.find(".wpgmza_directions_button").attr("id",""); 
		element.find(".wpgmza_directions_button").attr("data-marker-id",""); 
		element.find(".wpgmza_directions_button").attr("wpgm_addr_field",""); 

		
		
		if (marker.image === "" && marker.title === "") {  
			element.find(".wpgmza_iw_image").css("display","none"); 
		} else {
			element.find(".wpgmza_iw_image").css("display","block"); 
		}

		var container = $("#wpgmza_iw_holder_" + map.id + " .wpgmza_iw_image");
		container.html("");
		
		if(marker.gallery)
		{
			var gallery = new WPGMZA.MarkerGallery(marker, this);
			gallery.element.css({
				"float": "none"
			});
			container.append(gallery.element);
		}
		else if(marker.pic.length)
		{
			var image = $("<img class='wpgmza_infowindow_image'/>");
			image.attr("src", marker.pic);
			image.css({"display": "block"});
			container.append(image);
		}
		else
		{
			element.find(".wpgmza_iw_marker_image").css("display","none"); 
			element.find(".wpgmza_iw_title").attr("style","position: relative !important"); 
			element.find(".wpgmza_iw_title").addClass('wpgmze_iw_title_no_image');
		}
		
		if (marker.title !== "") { element.find(".wpgmza_iw_title").html(marker.title); }

		var description = "";

		if(marker.desc)
			description = marker.desc;
		else if(marker.description)
			description = marker.description;

		if (description && description.length)
		{ 
			element.find(".wpgmza_iw_description").css("display","block"); 
			element.find(".wpgmza_iw_description").html(description); 
		}
		else
			element.find(".wpgmza_iw_description").css("display","none");

		// Custom fields
		var container = element.find(".wpgmza_iw_description");
		if(marker.custom_fields_html)
		{
			container.append(marker.custom_fields_html);
			container.css("display","block");
		}
		
		/*if (typeof wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] !== 'undefined' && wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] === "yes") {
		} else {*/
			if (typeof marker.address !== "undefined" && marker.address !== "") { element.find(".wpgmza_iw_address_p").html(marker.address); }
		/*}*/
		

		if (typeof marker.link !== "undefined" && marker.link !== "") { 
			element.find(".wpgmza_more_info_button").show();
			element.find(".wpgmza_more_info_button").attr("href",marker.link);
			
			element.find(".wpgmza_more_info_button").attr("target",this.linkTarget); 
		} else {
			element.find(".wpgmza_more_info_button").hide();
		}
		if (map.directionsEnabled) { 
			element.find(".wpgmza_directions_button").show();
			element.find(".wpgmza_directions_button").attr("href","javascript:void(0);"); 
			element.find(".wpgmza_directions_button").attr("gps",marker.lat + "," + marker.lng); 
			element.find(".wpgmza_directions_button").attr("wpgm_addr_field",marker.address); 
			element.find(".wpgmza_directions_button").attr("id",map.id); 
			element.find(".wpgmza_directions_button").attr("data-marker-id",marker.id); 
			element.find(".wpgmza_directions_button").addClass("wpgmza_gd"); 

		} else {
			element.find(".wpgmza_directions_button").hide();
		}

		element.show();

		this.trigger("domready");
		this.trigger("infowindowopen");

		return true;
	}

	// TODO: This doesn't appear to do anything, nor does it call the parent method
	WPGMZA.ProInfoWindow.prototype.close = function()
	{
		$(this.feature.map.element).find(".wpgmza-pro-info-window-container").html();
	}
	
	WPGMZA.ProInfoWindow.prototype.setPosition = function(position){
		
	}
	
	// TODO: This should be taken care of already in core.js
	$(document).ready(function(event) {
		$(document.body).on("click", ".wpgmza-close-info-window", function(event) {
			$(event.target).closest(".wpgmza-info-window").remove();
		});
	});
	
});