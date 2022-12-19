/**
 * @namespace WPGMZA
 * @module DirectionsBox
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.DirectionsBox = function(map)
	{
		var self = this;
		
		this.map = map;
		this.element = $("#wpgmaps_directions_edit_" + map.id);
		
		this.element[0].wpgmzaMap = map;
		
		$(this.element).find("input.wpgmza-address").each(function(index, el) {
			el.wpgmzaAddressInput = WPGMZA.AddressInput.createInstance(el, map);
		});
		
		this.optionsElement = this.element.find(".wpgmza-directions-options");
		this.optionsElement.hide();
		
		this.showOptionsElement = this.element.find("#wpgmza_show_options_" + map.id);
		this.showOptionsElement.on("click", function(event) {
			self.onShowOptions(event);
		});
		
		this.hideOptionsElement = this.element.find("#wpgmza_hide_options_" + map.id);
		this.hideOptionsElement.on("click", function(event) {
			self.onHideOptions(event);
		});
		this.hideOptionsElement.hide();
		
		this.waypointTemplateItem = $(this.element).find(".wpgmaps_via.wpgmaps_template");
		this.waypointTemplateItem.removeClass("wpgmaps_template");
		this.waypointTemplateItem.remove();
		
		this.element.find(".wpgmaps_add_waypoint a").on("click", function(event) {
			self.onAddWaypoint(event);
		});
		
		this.element.on("click", ".wpgmza_remove_via", function(event) {
			self.onRemoveWaypoint(event);
		});

		this.element.on('click', '.wpgmza-travel-mode-option', function(){
			
			var mode = jQuery(this).data('mode')
		    self.travelMode = mode;
		    
		    jQuery('body').find('.wpgmza-travel-mode-option').removeClass('wpgmza-travel-option__selected');
		    jQuery(this).addClass('wpgmza-travel-option__selected');
		    jQuery('body').find('.wpgmza-travel-mode').val(mode);
		});
		
		if($("body").sortable)
			$(this.element).find(".wpgmaps_directions_outer_div [data-map-id]").sortable({
				items: ".wpgmza-form-field.wpgmaps_via"
			});
		
		this.getDirectionsButton = this.element.find(".wpgmaps_get_directions");
		this.getDirectionsButton.on("click", function(event) {
			self.onGetDirections();
		});


		
		$(this.element).find(".wpgmza-reset-directions").on("click", function(event) {
			self.onResetDirections(event);
		});
		
		$(this.element).find(".wpgmza-print-directions").on("click", function(event) {
			self.onPrintDirections(event);
		});
		
		this.service = WPGMZA.DirectionsService.createInstance(map);
		this.renderer = WPGMZA.DirectionsRenderer.createInstance(map);
		
		if(this.map.shortcodeAttributes.directions_from)
			$("#wpgmza_input_from_" + this.map.id).val(this.map.shortcodeAttributes.directions_from);
		
		if(this.map.shortcodeAttributes.directions_to)
			$("#wpgmza_input_to_" + this.map.id).val(this.map.shortcodeAttributes.directions_to);
		
		if(this.map.shortcodeAttributes.directions_waypoints)
		{
			var addresses = this.map.shortcodeAttributes.directions_waypoints.split("|");
			
			for(var i = 0; i < addresses.length; i++)
				this.addWaypoint(addresses[i]);
		}
		
		if(this.map.shortcodeAttributes.directions_auto == "true")
			this.route();
		
		if(this.openExternal && this.isUsingAppleMaps)
			$(".wpgmza-add-waypoint").hide();
	}
	
	WPGMZA.DirectionsBox.prototype = Object.create(WPGMZA.EventDispatcher);
	WPGMZA.DirectionsBox.prototype.constructor = WPGMZA.DirectionsBox;
	
	WPGMZA.DirectionsBox.STYLE_DEFAULT			= "default";
	WPGMZA.DirectionsBox.STYLE_MODERN			= "modern";
	
	WPGMZA.DirectionsBox.STATE_INPUT			= "input";
	WPGMZA.DirectionsBox.STATE_DISPLAY			= "display";
	
	WPGMZA.DirectionsBox.forceGoogleMaps = false;
	
	WPGMZA.DirectionsBox.createInstance = function(map) {
		if(WPGMZA.isModernComponentStyleAllowed() && (
				map.settings.directions_box_style == "modern" || WPGMZA.settings.user_interface_style == "modern"
			))
			return new WPGMZA.ModernDirectionsBox(map);
		else
			return new WPGMZA.DirectionsBox(map);
	}
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "style", {
		
		get: function()
		{
			return this.map.settings.directions_box_style;
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "state", {
		
		set: function(value)
		{
			$(".wpgmza-directions-box[data-map-id='" + this.map.id + "']").show();
			
			switch(value)
			{
				case WPGMZA.DirectionsBox.STATE_INPUT:
				
					$("#wpgmaps_directions_editbox_" + this.map.id).show("slow");
					$("#wpgmaps_directions_notification_" + this.map.id).hide("slow");
					
					$(this.element).find("input.wpgmza-get-directions").show();
					$(this.element).find("a.wpgmza-reset-directions").hide();
					$(this.element).find("a.wpgmza-print-directions").hide();
					
					break;
				
				case WPGMZA.DirectionsBox.STATE_DISPLAY:
				
					$("#wpgmaps_directions_editbox_" + this.map.id).hide("slow");
					$("#wpgmaps_directions_notification_" + this.map.id).show("slow");
					
					$(this.element).find("input.wpgmza-get-directions").hide();
					$(this.element).find("a.wpgmza-reset-directions").show();
					$(this.element).find("a.wpgmza-print-directions").show();
					
					break;
				
				default:
				
					throw new Error("Unknown state");
					
					break;
			}
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "start", {
		
		get: function()
		{
			return this.from;
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "end", {
		
		get: function()
		{
			return this.to;
		}
		
	});
	
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "from", {
		
		get: function()
		{
			return $("#wpgmza_input_from_" + this.map.id).val();
		},
		
		set: function(value)
		{
			$("#wpgmza_input_from_" + this.map.id).val(value);
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "to", {
		
		get: function()
		{
			return $("#wpgmza_input_to_" + this.map.id).val();
		},
		
		set: function(value)
		{
			$("#wpgmza_input_to_" + this.map.id).val(value);
		}
		
	});
	
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "avoidTolls", {
		
		get: function()
		{
			return $("#wpgmza_tolls_" + this.map.id).is(":checked");
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "avoidHighways", {
		
		get: function()
		{
			return $("#wpgmza_highways_" + this.map.id).is(":checked");
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "avoidFerries", {
		
		get: function()
		{
			return $("#wpgmza_ferries_" + this.map.id).is(":checked");
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "travelMode", {
		
		get: function()
		{
			return $("#wpgmza_dir_type_" + this.map.id).val();
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "travelModeShort", {
		
		get: function()
		{
			return this.travelMode.substr(0, 1).toLowerCase();
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "openExternal", {
		
		get: function()
		{
			if(this.map.settings.directions_behaviour == "external")
				return true;
			
			if(this.map.settings.directions_behaviour == "intelligent")
				return WPGMZA.isTouchDevice();
			
			return false;
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "isUsingAppleMaps", {
		
		get: function()
		{
			return navigator.platform.match(/iPhone|iPod|iPad/) && !this.map.settings.force_google_directions_app;
		}
		
	});
	
	WPGMZA.DirectionsBox.prototype.getAjaxParameters = function()
	{
		/*
		 * Unit system should be a new setting
		 *
		 * This will act as a typecast for now, but we should move away from this (Unit system not ported to OL)
		*/
		var request = {
			origin: 					this.from,
			destination:				this.to,
			provideRouteAlternatives: 	true,
			avoidHighways:				this.avoidHighways,
			avoidTolls:					this.avoidTolls,
			avoidFerries:				this.avoidFerries,
			travelMode:					this.travelMode,
			unitSystem:  				this.map.settings.store_locator_distance
		};
		
		var addresses = this.getWaypointAddresses();
		var waypoints = [];
		
		if(addresses.length)
		{
			for(var i in addresses)
			{
				var location = addresses[i];
				
				waypoints[i] = {
					location: location,
					stopover: false
				};
			}
			
			request.waypoints = waypoints;
		}
		
		return request;
	}
	
	WPGMZA.DirectionsBox.prototype.getWaypointAddresses = function()
	{
		var waypoints = $("#wpgmza_input_waypoints_" + this.map.id).val();
		var elements = $("#wpgmaps_directions_edit_" + this.map.id + " input.wpgmaps_via");
		var values = [];
		
		if(elements.length)
		{
			elements.each(function(index, el) {
				values.push($(el).val());
			});
		}
		
		return values;
	}
	
	WPGMZA.DirectionsBox.prototype.getExternalURLParameters = function(options)
	{
		var pararms, waypoints;
		
		if(!options)
			options = {};
		
		if(options.scheme == "apple")
		{
			params = {
				saddr:			this.from,
				daddr:			this.to
			};
			
			if(options.marker)
				params.daddr = options.marker.address;
		}
		else
		{
			params = {
				api:			1,
				origin:			this.from,
				destination:	this.to,
				travelmode:		this.travelMode
			};
		
			waypoints = this.getWaypointAddresses();
			
			if(waypoints.length)
				params.waypoints = waypoints.join("|");
			
			if(options.marker)
				params.destination = options.marker.address;
		}
		
		if(options.format == "string")
		{
			var components = [];
			
			for(var name in params)
				components.push(name + "=" + encodeURIComponent(params[name]));
			
			return "?" + components.join("&");
		}
		
		return params;
	}
	
	WPGMZA.DirectionsBox.prototype.getExternalURL = function(options)
	{
		if(!options)
			options = {};
		
		options = $.extend(options, {
			format: "string"
		});
		
		if(this.isUsingAppleMaps)
		{
			options.scheme = "apple";
			return "https://maps.apple.com/maps" + this.getExternalURLParameters(options);
		}
		
		return "https://www.google.com/maps/dir/" + this.getExternalURLParameters(options);
	}
	
	WPGMZA.DirectionsBox.prototype.route = function()
	{
		var self = this;
		
		if(this.from == "" && this.to == "")
		{
			alert(WPGMZA.localized_strings.please_fill_out_both_from_and_to_fields);
			return;
		}
		
		var params = this.getAjaxParameters();
		var usingModernStyleDirectionsBox =
			(
				WPGMZA.settings.user_interface_style == "legacy" 
				&&
				self.map.settings.directions_box_style == "modern"
			)
			|| 
			WPGMZA.settings.user_interface_style == "modern";

		this.state = WPGMZA.DirectionsBox.STATE_DISPLAY;
		
		if(this.map.modernDirectionsBox)
			this.map.modernDirectionsBox.open();
		
		this.service.route(params, function(response, status) {
			
			switch(status)
			{
				case WPGMZA.DirectionsService.SUCCESS:
				
					$("#wpgmaps_directions_notification_" + self.map.id).html("");
					$("#directions_panel_" + self.map.id).show();
					
					self.renderer.setDirections(response);
					
					break;
				
				case WPGMZA.DirectionsService.ZERO_RESULTS:
				
					self.state = WPGMZA.DirectionsBox.STATE_INPUT;
					
					$("#wpgmaps_directions_notification_" + self.map.id).html(WPGMZA.localized_strings.zero_results);
					
					self.reset();
					
					break;
				
				case WPGMZA.DirectionsService.NOT_FOUND:
				
					self.state = WPGMZA.DirectionsBox.STATE_INPUT;
					
					$("#wpgmaps_directions_notification_" + self.map.id).html(WPGMZA.localized_strings.zero_results);
					
					self.reset();
					
					if(response.geocoded_waypoints && response.geocoded_waypoints.length)
					{
						for(var i = 0; i < response.geocoded_waypoints.length; i++)
						{
							var waypoint = response.geocoded_waypoints[i];
							var status = waypoint.geocoder_status;
							
							if(status == WPGMZA.DirectionsService.NOT_FOUND)
							{
								if(i == 0)
								{
									$(self.element).find(".wpgmza-directions-from").addClass("wpgmza-not-found");
								}
								else if(i == response.geocoded_waypoints.length - 1)
								{
									$(self.element).find(".wpgmza-directions-to").addClass("wpgmza-not-found");
								}
								else
								{
									$($(self.element).find("div.wpgmza-waypoint-via")[i-1]).addClass("wpgmza-not-found");
								}
							}
						}
					}
					
					break;
				
				default:
				
					alert(WPGMZA.localized_strings.unknown_directions_service_status);
					
					this.state = WPGMZA.DirectionsBox.STATE_INPUT;
					
					break;
			}
			
		});
		
		
	}
	
	WPGMZA.DirectionsBox.prototype.reset = function()
	{
		$("#wpgmaps_directions_editbox_" + this.map.id).show();
		$("#directions_panel_" + this.map.id).hide();
		$("#directions_panel_" + this.map.id).html('');
		$("#wpgmaps_directions_notification_" + this.map.id).hide();
		$("#wpgmaps_directions_reset_" + this.map.id).hide();
		$("#wpgmaps_directions_notification_" + this.map.id).html(WPGMZA.localized_strings.fetching_directions);
		$(".wpgmza-not-found").removeClass("wpgmza-not-found");
		
		this.state = WPGMZA.DirectionsBox.STATE_INPUT;
		
		this.renderer.clear();
	}
	
	WPGMZA.DirectionsBox.prototype.showOptions = function(show)
	{
		if(show || arguments.length == 0)
		{
			this.optionsElement.show();
			this.showOptionsElement.hide();
			this.hideOptionsElement.show();
		}
		else
		{
			this.optionsElement.hide();
			this.showOptionsElement.show();
			this.hideOptionsElement.hide();
		}
	}
	
	WPGMZA.DirectionsBox.prototype.hideOptions = function()
	{
		this.showOptions(false);
	}
	
	WPGMZA.DirectionsBox.prototype.addWaypoint = function(address)
	{
		var row = this.waypointTemplateItem.clone();
		
		$(this.element).find("div.wpgmza-directions-to").before(row);
		
		if(address)
			$(row).find("input").val(address);
		
		WPGMZA.AddressInput.createInstance($(row).find("input")[0], this.map);
		
		return row;
	}
	
	WPGMZA.DirectionsBox.prototype.onAddWaypoint = function()
	{
		var row = this.addWaypoint();
		
		row.find("input").focus();
	}
	
	WPGMZA.DirectionsBox.prototype.onShowOptions = function(event)
	{
		$(this.element).find(".wpgmza-directions-options").show();
		$(this.element).find(".wpgmza-hide-directions-options").show();
		$(this.element).find(".wpgmza-show-directions-options").hide();
	}
	
	WPGMZA.DirectionsBox.prototype.onHideOptions = function(event)
	{
		$(this.element).find(".wpgmza-directions-options").hide();
		$(this.element).find(".wpgmza-hide-directions-options").hide();
		$(this.element).find(".wpgmza-show-directions-options").show();
	}
	
	WPGMZA.DirectionsBox.prototype.onRemoveWaypoint = function()
	{
		$(event.target).closest(".wpgmza-form-field").remove();
	}
	
	WPGMZA.DirectionsBox.prototype.onGetDirections = function(event)
	{
		if(this.openExternal)
		{
			window.open(this.getExternalURL(), "_blank");
			return;
		}
		
		this.route();
	}
	
	WPGMZA.DirectionsBox.prototype.onPrintDirections = function(event)
	{
		try{
		   var routeHtml = document.getElementById("directions_panel_" + this.map.id).innerHTML;  
	       var printWindow = window.open('', '', 'height=600,width=800');  
	       printWindow.document.write('<html><head><title>Get Directions</title>');  
	       printWindow.document.write('</head><body >');  
	       printWindow.document.write(routeHtml);  
	       printWindow.document.write('</body></html>');  
	       printWindow.document.close();  
	       printWindow.print(); 
		} catch(ex){
			var url = this.getExternalURL() + "&om=1";
			window.open(url, "_blank");
		}
	}
	
	WPGMZA.DirectionsBox.prototype.onResetDirections = function(event)
	{
		this.reset();
	}

	$(document.body).on("click", ".wpgmza_gd, .wpgmza-directions-button", function(event) {
		
		var component;
		var marker, address, coords, map;
		
		component = $(event.currentTarget).closest("[data-wpgmza-marker-listing]");
		
		if(!component.length) {
			component = $(event.currentTarget).closest(".wpgmza_modern_infowindow, [data-map-id]");
		}

		// added by Nick 04 Jan 2020 - Modern Infowindow Plus directions buttons causes JS error
		if (!component[0].wpgmzaMarkerListing && !component[0].wpgmzaInfoWindow && !component[0].wpgmzaMap) {
			component = $(event.currentTarget).closest(".wpgmza_map[data-map-id]");
		}

		if(!component.length) {
			return; // NB: ProInfoWindow handles this
		}
		
		if(component.length) {
			var element = component[0];
			
			if(element.wpgmzaMarkerListing) {
				map = element.wpgmzaMarkerListing.map;
				marker = map.getMarkerByID($(event.currentTarget).closest("[data-marker-id]").attr("data-marker-id"));
				
			}
			else if(element.wpgmzaInfoWindow) {
				marker = element.wpgmzaInfoWindow.mapObject;
				map = marker.map;
			}
			else if(element.wpgmzaMap) {
				map = element.wpgmzaMap;
				marker = element.wpgmzaMap.getMarkerByID($(event.currentTarget).attr("data-marker-id"));
			} else {
				// added by Nick 04 Jan 2020 - Modern Infowindow Plus directions buttons causes JS error
				map = element.wpgmzaMap;
				marker = map.getMarkerByID($(event.currentTarget).closest("[data-marker-id]").attr("data-marker-id"));
			}
		}
		
		if(marker){
			address = marker.address;
			coords = marker.getPosition().toString();
		} else {
			//Add support for non marker features that have direction links in the info-window
			var arbLatLng = $(event.currentTarget).data('latlng');
			if(arbLatLng){
				coords = arbLatLng;
				marker = {
					address : coords
				};
			}
		}
		
		if(map.directionsBox.openExternal)
			window.open(map.directionsBox.getExternalURL({marker: marker}));
		else
		{
			map.directionsBox.state = WPGMZA.DirectionsBox.STATE_INPUT;				
			map.directionsBox.to = (address && address.length ? address : coords);
			$("#wpgmza_input_from_" + map.id).focus().select();
			
			if(map.directionsBox instanceof WPGMZA.ModernDirectionsBox)
				map.directionsBox.open();
			else
				WPGMZA.animateScroll( map.directionsBox.element );
		}
		
	});
	
});