(function($) {
	
jQuery(document).ready(function() {
	
	$("button[data-fit-bounds-to-shape]").each(function(index, el) {
		
		$(el).on("click", function(event) {
			
			var name = $(el).attr("data-fit-bounds-to-shape");
			var shape = window[name];
			var bounds;
			
			if(shape instanceof google.maps.Polygon || shape instanceof google.maps.Polyline)
			{
				bounds = new google.maps.LatLngBounds();
				shape.getPath().forEach(function(element, index) {
					bounds.extend(element);
				});
			}
			else
				bounds = shape.getBounds();
		
			MYMAP.map.fitBounds(bounds);
		});
		
	});
    
	$(document.body).on("click", "#wpgmza-gdpr-privacy-policy-notice .notice-dismiss", function(event) {
		
		$.ajax(WPGMZA.ajaxurl, {
			data: {
				action: "wpgmza_gdpr_privacy_policy_notice_dismissed"
			}
		})
		
	});
	
    if($("input[name='wpgmza_directions_box_style'][value='modern']").length > 0){
        if($("input[name='wpgmza_directions_box_style'][value='modern']").is(':checked')){
           wpgmza_toggle_dbox_width_setting_area(true);
           wpgmza_toggle_dbox_position_setting_area(true);
        }

        if($("input[name='wpgmza_store_locator_radius_style'][value='modern']").is(':checked')){
           wpgmza_toggle_legacy_sl_style_setting_area(true);

        }
    }
    
    var tgm_media_frame_custom_1;
    jQuery(document.body).on('click.tgmOpenMediaManager', '#upload_custom_marker_click_button', function(e){
        e.preventDefault();

        if ( tgm_media_frame_custom_1 ) {
            tgm_media_frame_custom_1.open();
            return;
        }

        tgm_media_frame_custom_1 = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Upload Custom Marker Icon',
            library: {
                type: 'image'
            },

            button: {
                text:  'Use as Custom Marker'
            }
        });

        tgm_media_frame_custom_1.on('select', function(){
            var media_attachment = tgm_media_frame_custom_1.state().get('selection').first().toJSON();
            jQuery('#wpgmza_add_custom_marker_on_click').val(media_attachment.url);
            jQuery("#wpgmza_cmm_custom").html("<img src=\""+media_attachment.url+"\" />");
        });
        tgm_media_frame_custom_1.open();
    });

    var tgm_media_frame_default;
    jQuery(document.body).on('click.tgmOpenMediaManager', '#upload_default_ul_marker_btn', function(e){
        e.preventDefault();

        if ( tgm_media_frame_default ) {
            tgm_media_frame_default.open();
            return;
        }

        tgm_media_frame_default = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Default Marker Icon',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as Default Marker'
            }
        });

        tgm_media_frame_default.on('select', function(){
            var media_attachment = tgm_media_frame_default.state().get('selection').first().toJSON();
            jQuery('#upload_default_ul_marker').val(media_attachment.url);
            jQuery("#wpgmza_mm_ul").html("<img src=\""+media_attachment.url+"\" />");
        });
        tgm_media_frame_default.open();
    });
    jQuery(document.body).on('click.tgmOpenMediaManager', '#upload_default_sl_marker_btn', function(e){
        e.preventDefault();

        if ( tgm_media_frame_default ) {
            tgm_media_frame_default.open();
            return;
        }

        tgm_media_frame_default = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Default Marker Icon',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as Default Marker'
            }
        });

        tgm_media_frame_default.on('select', function(){
            var media_attachment = tgm_media_frame_default.state().get('selection').first().toJSON();
            jQuery('#upload_default_sl_marker').val(media_attachment.url);
            jQuery("#wpgmza_mm_sl").html("<img src=\""+media_attachment.url+"\" />");
        });
        tgm_media_frame_default.open();
    });    


    jQuery("body").on("click",".wpgmza_copy_shortcode", function() {
        var $temp = jQuery('<input>');
        var $tmp2 = jQuery('<span id="wpgmza_tmp" style="display:none; width:100%; text-align:center;">');
        jQuery("body").append($temp);
        $temp.val(jQuery(this).val()).select();
        document.execCommand("copy");
        $temp.remove();
        //jQuery(this).after($tmp2);
        //jQuery($tmp2).html(wpgmaps_localize_strings["wpgm_copy_string"]);
        //jQuery($tmp2).fadeIn();
        //setTimeout(function(){ jQuery($tmp2).fadeOut(); }, 1000);
        //setTimeout(function(){ jQuery($tmp2).remove(); }, 1500);
        WPGMZA.notification(wpgmaps_localize_strings["wpgm_copy_string"]);
    });

    jQuery("body").on("click",".wpgmza-marker-listing-picker__item", function() {
        jQuery('body').find('.wpgmza-marker-listing-picker__item').removeClass('selected');
        jQuery('body').find(this).addClass('selected');
    });

    if(jQuery('#wpgmza_store_locator_bounce').attr('checked')){
        jQuery('#wpgmza_store_locator_bounce_conditional').fadeIn();
    }else{
        jQuery('#wpgmza_store_locator_bounce_conditional').fadeOut();
    }

    jQuery('#wpgmza_store_locator_bounce').on('change', function(){
        if(jQuery(this).attr('checked')){
            jQuery('#wpgmza_store_locator_bounce_conditional').fadeIn();
        }else{
            jQuery('#wpgmza_store_locator_bounce_conditional').fadeOut();
        }
    });


    if(jQuery('#wpgmza_show_user_location').attr('checked')){
        jQuery('#wpgmza_show_user_location_conditional').fadeIn();
    }else{
        jQuery('#wpgmza_show_user_location_conditional').fadeOut();
    }

    jQuery('#wpgmza_show_user_location').on('change', function(){
        if(jQuery(this).attr('checked')){
            jQuery('#wpgmza_show_user_location_conditional').fadeIn();
        }else{
            jQuery('#wpgmza_show_user_location_conditional').fadeOut();
        }
    });

    if(jQuery('#wpgmza_automatically_pan_to_users_location').attr('checked')){
        jQuery('#wpgmza_override_user_location_setting').fadeIn();
        jQuery('#wpgmza_override_user_location_zoom_value_option').fadeIn();
    }else{
        jQuery('#wpgmza_override_user_location_setting').fadeOut();
        jQuery('#wpgmza_override_user_location_zoom_value_option').fadeOut();
    }

    jQuery('#wpgmza_automatically_pan_to_users_location').on('change', function(){
        if(jQuery(this).attr('checked')){
            jQuery('#wpgmza_override_user_location_setting').fadeIn();
            jQuery('#wpgmza_override_user_location_zoom_value_option').fadeIn();
        }else{
            jQuery('#wpgmza_override_user_location_setting').fadeOut();
            jQuery('#wpgmza_override_user_location_zoom_value_option').fadeOut();
        }
    });

    if(jQuery('#wpgmza_override_users_location_zoom_level').attr('checked')){ 
        jQuery('select[name="wpgmza_override_users_location_zoom_levels"]').removeAttr("disabled");
    }else{
        jQuery('select[name="wpgmza_override_users_location_zoom_levels"]').attr('disabled', 'disabled');
    }

    jQuery('#wpgmza_override_users_location_zoom_level').on('change', function(){
       
        if(jQuery(this).attr('checked')){
            jQuery('select[name="wpgmza_override_users_location_zoom_levels"]').removeAttr("disabled");
        }else{
            jQuery('select[name="wpgmza_override_users_location_zoom_levels"]').attr('disabled', 'disabled');
        }
    });

    jQuery("body").on("click","#wpgmza_gradient_show", function(e) {
        e.preventDefault();
        var gtype = jQuery(this).attr("gtype");
        if (gtype == "default") {
              var gradient = '1';
            jQuery("#heatmap_gradient").html(JSON.stringify(gradient));
            jQuery('#heatmap_gradient').keyup();
        }
        if (gtype == "blue") {
              var gradient = [
                'rgba(0, 255, 255, 0)',
                'rgba(0, 255, 255, 1)',
                'rgba(0, 191, 255, 1)',
                'rgba(0, 127, 255, 1)',
                'rgba(0, 63, 255, 1)',
                'rgba(0, 0, 255, 1)',
                'rgba(0, 0, 223, 1)',
                'rgba(0, 0, 191, 1)',
                'rgba(0, 0, 159, 1)',
                'rgba(0, 0, 127, 1)',
                'rgba(63, 0, 91, 1)',
                'rgba(127, 0, 63, 1)',
                'rgba(191, 0, 31, 1)',
                'rgba(255, 0, 0, 1)'
              ]
            jQuery("#heatmap_gradient").html(JSON.stringify(gradient));
            jQuery('#heatmap_gradient').keyup();
        }

    });
	
	$(".wpgmza-marker-listing-style-menu input").on("change", function(event) {
		var title = $(event.target).next("img").attr("title");
		$(".wpgmza_mlist_sel_text").html(title);
		if ('6' == $(this).val()) {
		    $('.wpgmza_modern_marker_hide').hide();
        } else {
			$('.wpgmza_modern_marker_hide').show();
        }
	});

	$(".wpgmza-enable-custom-field-filter[readonly]").closest("li").on("click", function(event) {
		var warning = $(event.currentTarget).find(".notice-warning");
		
		warning.show();
		warning.delay(2000).fadeOut();
		
		event.preventDefault();
		return false;
	});
	
	var title = $(".wpgmza-marker-listing-style-menu input:checked").next("img").attr("title");
	$(".wpgmza_mlist_sel_text").html(title);

	$(".wpgmza-infowindow-picker__item").on("click", function(event) {
		var item = jQuery(this);
		var selection = $(this).find('[id^="wpgmza_iw_selection_"]').attr("id").match(/\d+$/);
		
		$("[name='wpgmza_iw_type']:checked").prop("checked", false);
		$("[name='wpgmza_iw_type'][id$=" + selection + "]").prop("checked", true);
		
		$(".wpgmza-infowindow-picker__item").removeClass("wpgmza_mlist_selection_activate");
		$("#wpgmza_iw_selection_" + selection).addClass("wpgmza_mlist_selection_activate");
        item.addClass('wpgmza_mlist_selection_activate');

        
		
		jQuery(".wpgmza_iw_sel_text").text(wpgmaps_localize_strings["wpgm_iw_sel_" + selection]);
		
	});

    jQuery('.add-new-editor').hover(function(){
        jQuery('#wpmgza_unsave_notice').toggleClass('visible');
    });

	$("[name='wpgmza_store_locator_radius_style']").on("change", function(event) {
		
		var disableFillControl = $(event.target).val() == "modern";
		$("#sl_fill_color, #sl_fill_opacity").prop("disabled", disableFillControl);
		
	}).change();
	
	$("#wpgmza_store_locator").on("change", function(event) {
		
		var disableStoreLocatorControls = !($("#wpgmza_store_locator").prop("checked"));
		$("#tabs-3 input:not(#wpgmza_store_locator), #tabs-3 select").prop("disabled", disableStoreLocatorControls);
		
	}).change();
	
	$("[name='wpgmza_listmarkers_by']").on("change", function(event) {
		
		var images = {
			0: "marker_list_0.png",
			1: "marker_list_1.png",
			4: "marker_list_2.png",
			2: "marker_list_3.png",
			3: "marker_list_4.png",
			6: "marker_list_modern.png",
            7: "marker_list_grid.png"
		};
		
		$("#wpgmza-marker-listing-preview").attr("src", wpgmza_plugin_dir_url + "images/" + images[this.value])
		
	});

    $("input[name='wpgmza_directions_box_style'][value='modern']").on("change", function(){
        var checked = this.checked;
        if(checked){
           wpgmza_toggle_dbox_width_setting_area(true);
           wpgmza_toggle_dbox_position_setting_area(true);
        }
    });

    $("input[name='wpgmza_directions_box_style'][value='default']").on("change", function(){
        var checked = this.checked;
        if(checked){
           wpgmza_toggle_dbox_width_setting_area(false);
           wpgmza_toggle_dbox_position_setting_area(false);
        }
    });
	
	$("#wpgmza_store_locator_distance").on("change", function(event) {
		
		var units = $(this).prop("checked") ? "mi" : "km";
		
		$(".wpgmza-store-locator-default-radius option").each(function(index, el) {
			
			$(el).html(
				$(el).html().match(/\d+/) + units
			);
			
		});
		
	});

    $("input[name='wpgmza_store_locator_radius_style'][value='modern']").on("change", function(){
        var checked = this.checked;
        if(checked){
           wpgmza_toggle_legacy_sl_style_setting_area(true);
        }
    });

    $("input[name='wpgmza_store_locator_radius_style'][value='legacy']").on("change", function(){
        var checked = this.checked;
        if(checked){
           wpgmza_toggle_legacy_sl_style_setting_area(false);
        }
    });

    $("body").on('click', '.bulk_select', function(){
        $(".bulk_selection").toggleClass("active");
        $(".bulk_delete").toggle();
    });

    $(".bulk_delete").click(function(){
        if(confirm("Are you sure you want to delete this?")){

            var maps_to_delete = "";

            jQuery('.bulk_selection_checkbox').each(function(){
                if(jQuery(this).is(':checked')){
                    maps_to_delete += jQuery(this).attr('data-map-id') + ",";
                }
            });
            var trash_nonce = $(this).attr('data-nonce'); 
            window.location = "?page=wp-google-maps-menu&wpgmza_nonce=" + trash_nonce + "&trash_bulk=" + maps_to_delete;
        } else {
            return false;
        }
    });

    /** Moving all functions here for easy editing moving forward */

    function wpgmza_toggle_dbox_width_setting_area(toggle){
        if(toggle){
            $(".wpgmza_dbox_width_settings_area").hide();
        } else {
            $(".wpgmza_dbox_width_settings_area").show();

        }
    }
    function wpgmza_toggle_dbox_position_setting_area(toggle){
        if(toggle){
            $(".wpgmza_dbox_width_position_area").hide();
        } else {
            $(".wpgmza_dbox_width_position_area").show();

        }
    }
    function wpgmza_toggle_legacy_sl_style_setting_area(toggle){
        if(toggle){
            $(".wpgmza_legacy_sl_style_option_area").hide();
        } else {
            $(".wpgmza_legacy_sl_style_option_area").show();

        }
    }
		
});

	$(window).on("load", function(event) {
		
		if(WPGMZA.settings.engine != "open-layers")
			return;
		
		$(".wpgmza-open-layers-feature-unavailable:not(.notice)").each(function(index, el) {
			
			var warning = $($(".notice.wpgmza-open-layers-feature-unavailable")[0]).clone();
			$(warning).show();
			$(el).prepend(warning);
			
			$.merge(
				$(el).find("input, select, textarea, button, .button-primary"),
				$(el).siblings("input, select, textarea, button, .button-primary")
			).each(function(index, el) {
				if($(el).hasClass("button-primary"))
					$(el).attr("href", "javascript: ;");
				else
					$(el).prop("disabled", true);
			});
			
		});
		
		$(".wpgmza-open-layers-feature-coming-soon:not(.notice)").each(function(index, el) {
			
			var warning = $($(".notice.wpgmza-open-layers-feature-coming-soon")[0]).clone();
			$(warning).show();
			$(el).prepend(warning);
			
			$.merge(
				$(el).find("input, select, textarea, button, .button-primary"),
				$(el).siblings("input, select, textarea, button, .button-primary")
			).each(function(index, el) {
				if($(el).hasClass("button-primary"))
					$(el).attr("href", "javascript: ;");
				else
					$(el).prop("disabled", true);
			});
			
		});
		
	});

})(jQuery);