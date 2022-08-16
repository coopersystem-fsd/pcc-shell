/**
 * @namespace WPGMZA
 * @module ModernMarkerListingMarkerView
 * @requires WPGMZA.PopoutPanel
 */
jQuery(function($) {
	
	/**
	 * This is the 2nd step of the modern look and feel marker listing
	 * @return Object
	 */
	WPGMZA.ModernMarkerListingMarkerView = function(map)
	{
		var self = this;
		
		this.map = map;
		this.map_id = map.id;
		
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var container = $("#wpgmza_map_" + map.id);
		
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-marker-listing-marker-view'>\
			<div class='wpgmza-close-container'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
				<span class='wpgmza-close'><i class='fa fa-times' aria-hidden='true'></i></span>\
			</div>\
			<div data-name='title'></div>\
			<div data-name='address'></div>\
			<div data-name='category'></div>\
			<img data-name='pic'/>\
			<div data-name='description'></div>\
			<div class='wpgmza-modern-marker-listing-buttons'>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-link-button'>\
					<i class='fa fa-link' aria-hidden='true'></i>\
					<div>\
						" + WPGMZA.localized_strings.link + "\
					</div>\
				</div>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-directions-button'>\
					<i class='fa fa-road' aria-hidden='true'></i>\
					<div>\
						" + WPGMZA.localized_strings.directions + "\
					</div>\
				</div>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-zoom-button'>\
					<i class='fa fa-search-plus' aria-hidden='true'></i>\
					<div>\
						" + WPGMZA.localized_strings.zoom + "\
					</div>\
				</div>\
			</div>\
		</div>");
		
		map.on("init", function() {
			
			container.append(self.element);
			
		});
		
		map.on("click", function(event) {
			
			if(!(event.target instanceof WPGMZA.Marker))
				return;
			
			if(event.target == self.map.userLocationMarker || event.target == self.map.storeLocatorMarker)
				return;
			
			self.open(event.target.id);
			
		});
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
            $("#wpgmza_map_" + self.map_id + " .wpgmza-modern-store-locator").removeClass("wpgmza_sl_mv_offset");
		});
		
		$(this.element).find(".wpgmza-link-button").on("click", function(event) {
			self.onLink(event);
		});
		
		$(this.element).find(".wpgmza-directions-button").on("click", function(event) {
			self.onDirections(event);
		});
		
		$(this.element).find(".wpgmza-zoom-button").on("click", function(event) {
			self.onZoom(event);
		});
		
		$(container).append(this.element);
		
		// NB: Don't obscure the modern directions box
		if(container.children(".wpgmza-modern-directions-box").length)
			$(this.element).after(container.children(".wpgmza-modern-directions-box"));
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernMarkerListingMarkerView.prototype.constructor = WPGMZA.ModernMarkerListingMarkerView;
	
	/*WPGMZA.ModernMarkerListingMarkerView.prototype.getMarkerAndData = function(marker_id)
	{
		var result = {
			marker: null,
			data: null
		};
		
		var mashup_ids = this.parent.mashup_ids;
		var map_id = this.map_id;
		var map_ids = [map_id];
		
		if(mashup_ids && mashup_ids.length)
			map_ids = mashup_ids.split(",");
		
		map_ids.forEach(function(map_id) {
			
			
			
		});
		
		return result;
	}*/
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.open = function(marker_id)
	{
		var self = this;
		var marker = this.map.getMarkerByID(marker_id);
		
		if(marker.disableInfoWindow)
			return;
		
		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		
		this.marker = marker;
		
		$(this.element).find("[data-name]").each(function(index, el) {
			
			var name = $(el).attr("data-name");
			var value;
			
			if(!marker[name])
				value = "";
			else
				value = marker[name];

			if(name === "category"){
				if(marker.categories.length > 0){
					value = marker.categories.join(',');
				}
			}
			
			switch(name)
			{
				case "pic":
					$(el).attr("src", value);
					$(el).attr("alt", marker['title']);
					// $(el).css({visibility: (value == "" ? "hidden" : "visible")});
					
					if(marker['pic'].length)
						$(el).show();
					else
						$(el).hide();
					
					break;
				
				case "category":
					var ids = value.split(",");
					var names = [];
					
					for(var i = 0; i < ids.length; i++) {
						var id = ids[i];
						
						if(wpgmza_category_data[id])
							names.push(wpgmza_category_data[id].category_name);
					}
					
					$(el).html(names.join(", "));
					
					break;
				
				default:
					$(el).html(value);
					break;
			}
			
		});
		
		if(!marker["link"] || marker["link"].length == 0)
			$(this.element).find(".wpgmza-link-button").hide();
		else
			$(this.element).find(".wpgmza-link-button").show();

        $("#wpgmza_map_" + this.map_id + " .wpgmza-modern-store-locator").addClass("wpgmza_sl_mv_offset");
	 
		$(this.element).find("[data-custom-field-name]").remove();
		$(this.element).find(".wpgmza-modern-marker-listing-buttons").before(marker.custom_fields_html);
		
		$(this.element).find(".wpgmza-directions-button").attr("data-marker-id", marker_id);
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onLink = function(event)
	{
		window.open(this.marker.link, "_blank");
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onDirections = function(event)
	{
		this.map.directionsBox.to = this.marker.address;
		this.map.directionsBox.element.show();
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onZoom = function(event)
	{
		this.map.setCenter(this.marker.getPosition());
		this.map.setZoom(14);
	}
	
});