/**
 * @namespace WPGMZA
 * @module ModernMarkerListing
 * @requires WPGMZA.MarkerListing
 * @requires WPGMZA.PopoutPanel
 */
jQuery(function($) {
	
	/**
	 * The modern look and feel marker listing
	 * @return Object
	 */
	WPGMZA.ModernMarkerListing = function(map, element, options)
	{
		var self = this;
		var map_id = map.id;
		var container = $("#wpgmza_map_" + map_id);
		var mashup_ids = container.attr("data-mashup-ids");
		
		WPGMZA.MarkerListing.apply(this, arguments);
		
		this.map = map;
		
		this.element = element;
		this.openButton = $('<div class="wpgmza-modern-marker-open-button wpgmza-modern-shadow wpgmza-modern-hover-opaque"><i class="fa fa-map-marker"></i> <i class="fa fa-list"></i></div>');
		
		container.append(this.openButton);
		container.append(this.element);
		
		this.popoutPanel = new WPGMZA.PopoutPanel();
		this.popoutPanel.element = this.element;
		
		map.on("init", function(event) {
			
			container.append(self.element);
			container.append(self.openButton);
			
		});
		
		self.openButton.on("click", function(event) {
			
			self.open();
			$("#wpgmza_map_" + map_id + " .wpgmza-modern-store-locator").addClass("wpgmza_sl_offset");
			
		});
		
		// Marker view
		this.markerView = new WPGMZA.ModernMarkerListingMarkerView(map);
		this.markerView.parent = this;
		
		// Event listeners
		$(this.element).find(".wpgmza-close-container").on("click", function(event) {
			self.close();
            $("#wpgmza_map_" + self.map.id + " .wpgmza-modern-store-locator").removeClass("wpgmza_sl_offset");
		});
		
		$(this.element).on("click", "li", function(event) {
			self.markerView.open($(event.currentTarget).attr("mid"));
		});
		
		$(document.body).on("click", ".wpgmza_sl_reset_button_" + map_id, function(event) {
			$(self.element).find("li[mid]").show();
		});
		
		$(document.body).on("filteringcomplete.wpgmza", function(event) {
			
			if(event.map.id == self._mapID)
				self.onFilteringComplete(event);
			
		});
	};
	
	WPGMZA.ModernMarkerListing.prototype = Object.create(WPGMZA.MarkerListing.prototype);
	WPGMZA.ModernMarkerListing.prototype.constructor = WPGMZA.ModernMarkerListing;
	
	WPGMZA.ModernMarkerListing.prototype.initPagination = function()
	{
		WPGMZA.MarkerListing.prototype.initPagination.apply(this, arguments);
		
		if(this.pageSize)
			$(this.element).find("ul").after(this.paginationElement);
	}
	
	WPGMZA.ModernMarkerListing.prototype.onHTMLResponse = function(html)
	{
		$(this.element).find("ul.wpgmza-modern-marker-listing-list-item-container").html(html);
	}
	
	WPGMZA.ModernMarkerListing.prototype.open = function()
	{
		this.popoutPanel.open();
	}
	
	WPGMZA.ModernMarkerListing.prototype.close = function()
	{
		this.popoutPanel.close();
	}
	
});