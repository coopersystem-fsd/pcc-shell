/**
 * @namespace WPGMZA
 * @module ProMapEditPage
 * @pro-requires WPGMZA.MapEditPage
 */
jQuery(function($) {
	
	if(WPGMZA.currentPage != "map-edit")
		return;
	
	WPGMZA.ProMapEditPage = function()
	{
		var self = this;
		
		WPGMZA.MapEditPage.apply(this, arguments);
		
		this.directionsOriginIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_origin_icon_picker_container > .wpgmza-marker-icon-picker") );
		this.directionsDestinationIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_destination_icon_picker_container > .wpgmza-marker-icon-picker") );
		
		this.advancedSettingsMarkerIconPicker = new WPGMZA.MarkerIconPicker( $("#advanced-settings-marker-icon-picker-container .wpgmza-marker-icon-picker") );

		this.userIconPicker = new WPGMZA.MarkerIconPicker( $("#wpgmza_show_user_location_conditional .wpgmza-marker-icon-picker") );

		this.storeLocatorIconPicker = new WPGMZA.MarkerIconPicker( $("#wpgmza_store_locator_bounce_conditional .wpgmza-marker-icon-picker") );


		$("input[name='store_locator_search_area']").on("input", function(event) {
			self.onStoreLocatorSearchAreaChanged(event);
		});
		self.onStoreLocatorSearchAreaChanged();

		// InfoWindow colours
		if($('input[name="wpgmza_iw_type"][value="1"]').prop('checked') || 
			$('input[name="wpgmza_iw_type"][value="2"]').prop('checked') || 
			$('input[name="wpgmza_iw_type"][value="3"]').prop('checked'))
			$('#iw_custom_colors_row').fadeIn();
		else
			$('#iw_custom_colors_row').fadeOut();

		$('.iw_custom_click_show').on("click", function(){
			$('#iw_custom_colors_row').fadeIn();
		});

		$('.iw_custom_click_hide').on("click", function(){
			$('#iw_custom_colors_row').fadeOut();
		});
		
		// Marker listing push-in-map
		if($('#wpgmza_push_in_map').prop('checked'))
			$('#wpgmza_marker_list_conditional').fadeIn();
		else
			$('#wpgmza_marker_list_conditional').fadeOut();

		$('#wpgmza_push_in_map').on('change', function() {
			if($(this).prop('checked'))
				$('#wpgmza_marker_list_conditional').fadeIn();
			else
				$('#wpgmza_marker_list_conditional').fadeOut();
		});

		if($('#wpgmza_show_user_location').prop('checked')){
	        $('#wpgmza_show_user_location_conditional').fadeIn();
	    }else{
	        $('#wpgmza_show_user_location_conditional').fadeOut();
	    }

	    $('#wpgmza_show_user_location').on('change', function(){
	        if($(this).prop('checked')){
	            $('#wpgmza_show_user_location_conditional').fadeIn();
	        }else{
	            $('#wpgmza_show_user_location_conditional').fadeOut();
	        }
	    });

	    if($('#wpgmza_store_locator_bounce').prop('checked')){
	        $('#wpgmza_store_locator_bounce_conditional').fadeIn();
	    }else{
	        $('#wpgmza_store_locator_bounce_conditional').fadeOut();
	    }

	    $('#wpgmza_store_locator_bounce').on('change', function(){
	        if($(this).prop('checked')){
	            $('#wpgmza_store_locator_bounce_conditional').fadeIn();
	        }else{
	            $('#wpgmza_store_locator_bounce_conditional').fadeOut();
	        }
	    });

	    if($('#zoom_level_on_marker_listing_override').prop('checked')){
	        $('#zoom_level_on_marker_listing_click_level').fadeIn();
	    }else{
	        $('#zoom_level_on_marker_listing_click_level').fadeOut();
	    }

	    $('#zoom_level_on_marker_listing_override').on('change', function(){
	        if($(this).prop('checked')){
	            $('#zoom_level_on_marker_listing_click_level').fadeIn();
	        }else{
	            $('#zoom_level_on_marker_listing_click_level').fadeOut();
	        }
	    });

	    $('#zoom-on-marker-listing-click-slider').slider({
			range: "max",
			min: 1,
			max: 21,
			value: $("input[name='zoom_level_on_marker_listing_click']").val(),
			slide: function(event, ui){
				$("input[name='zoom_level_on_marker_listing_click']").val(ui.value);
			}
		});
	    

	    if($('#wpgmza_override_users_location_zoom_level').prop('checked')){
	        $('#wpgmza_override_users_location_zoom_levels_slider').fadeIn();
	    }else{
	        $('#wpgmza_override_users_location_zoom_levels_slider').fadeOut();
	    }

	    $('#wpgmza_override_users_location_zoom_level').on('change', function(){
	        if($(this).prop('checked')){
	            $('#wpgmza_override_users_location_zoom_levels_slider').fadeIn();
	        }else{
	            $('#wpgmza_override_users_location_zoom_levels_slider').fadeOut();
	        }
	    });

	    $('#override-users-location-zoom-levels-slider').slider({
			range: "max",
			min: 1,
			max: 21,
			value: $("input[name='override_users_location_zoom_levels']").val(),
			slide: function(event, ui){
				$("input[name='override_users_location_zoom_levels']").val(ui.value);
			}
		});
	      

		
		// NB: Workaround for bad DOM
		$("#open-route-service-key-notice").wrapInner("<div class='notice notice-error'><p></p></div>");

		$('#zoom-on-marker-click-slider').slider({
			range: "max",
			min: 1,
			max: 21,
			value: $("input[name='wpgmza_zoom_on_marker_click_slider']").val(),
			slide: function(event, ui){
				$("input[name='wpgmza_zoom_on_marker_click_slider']").val(ui.value);
			}
		});
		
		if($('#wpgmza_zoom_on_marker_click').prop('checked'))
			$('#wpgmza_zoom_on_marker_click_zoom_level').fadeIn();
		else
			$('#wpgmza_zoom_on_marker_click_zoom_level').fadeOut();

		$('#wpgmza_zoom_on_marker_click').on('change', function() {
			if($(this).prop('checked'))
				$('#wpgmza_zoom_on_marker_click_zoom_level').fadeIn();
			else
				$('#wpgmza_zoom_on_marker_click_zoom_level').fadeOut();
		});

		if($('#datatable_result').prop('checked'))
			$('#datable_strings').fadeIn();
		else
			$('#datable_strings').fadeOut();

		$('#datatable_result').on('change', function() {
			if($(this).prop('checked'))
				$('#datable_strings').fadeIn();
			else
				$('#datable_strings').fadeOut();
		});

		if($('#datatable_result_page').prop('checked'))
			$('#datable_strings_entries').fadeIn();
		else
			$('#datable_strings_entries').fadeOut();
		
		$('#datatable_result_page').on('change', function() {
			if($(this).prop('checked'))
				$('#datable_strings_entries').fadeIn();
			else
				$('#datable_strings_entries').fadeOut();
		});
	}
	
	WPGMZA.extend(WPGMZA.ProMapEditPage, WPGMZA.MapEditPage);
	
	WPGMZA.ProMapEditPage.prototype.onStoreLocatorSearchAreaChanged = function(event)
	{
		var value = $("input[name='store_locator_search_area']:checked").val();
		
		$("[data-search-area='" + value + "']").show();
		$("[data-search-area][data-search-area!='" + value + "']").hide();
	}
	
});