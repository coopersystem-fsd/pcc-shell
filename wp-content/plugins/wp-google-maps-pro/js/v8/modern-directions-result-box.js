/**
 * @namespace WPGMZA
 * @module ModernDirectionsResultBox
 * @requires WPGMZA.PopoutPanel
 */
jQuery(function($) {
	
	/**
	 * The second step of the directions box
	 * @return Object
	 */
	WPGMZA.ModernDirectionsResultBox = function(map, directionsBox)
	{
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var self = this;
		var container = $(map.element);
		
		this.map = map;
		
		this.directionsBox = directionsBox;
		
		// Build element
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-directions-box'>\
			<h2 class='wpgmza-directions-box__title'>" + $(directionsBox.element).find("h2").html() + "</h2>\
			<div class='wpgmza-directions-buttons'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
				<a class='wpgmza-print' style='display: none;'><i class='fa fa-print' aria-hidden='true'></i></a>\
			</div>\
			<div class='wpgmza-directions-results'>\
			</div>\
		</div>");
		
		this.element.addClass('wpgmza-modern-directions-box__results');
		
		var nativeIcon = new WPGMZA.NativeMapsAppIcon();
		this.nativeMapAppIcon = nativeIcon;
		$(this.element).find(".wpgmza-directions-buttons").append(nativeIcon.element);
		$(nativeIcon.element).on("click", function(event) {
			self.onNativeMapsApp(event);
		});
		
		// Add to DOM tree
		container.append(this.element);
		
		this.element.append($("#directions_panel_" + map.id));
		
		// Print directions link
		$(this.element).find(".wpgmza-print").attr("href", "data:text/html,<script>document.body.innerHTML += sessionStorage.wpgmzaPrintDirectionsHTML; window.print();</script>");
		
		// Event listeners
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
		
		$(this.element).find(".wpgmza-print").on("click", function(event) {
			self.onPrint(event);
		});
		
		this.map.on("directionsserviceresult", function(event) {
			self.onDirectionsChanged(event, event.response, event.status);
		});
		
		// Initial state
		this.clear();
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernDirectionsResultBox.prototype.constructor = WPGMZA.ModernDirectionsResultBox;
	
	WPGMZA.ModernDirectionsResultBox.prototype.clear = function()
	{
		$(this.element).find(".wpgmza-directions-results").html("");
		$(this.element).find("a.wpgmza-print").attr("href", "");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.open = function()
	{
		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		this.showPreloader();
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.showPreloader = function()
	{
		$(this.element).find(".wpgmza-directions-results").html("<img src='" + wpgmza_ajax_loader_gif.src + "'/>");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onDirectionsChanged = function(event, response, status)
	{
		this.clear();
		
		switch(status)
		{
			case WPGMZA.DirectionsService.SUCCESS:
				// NB: The new directions renderers take care of this themselves
				break;
				
			case WPGMZA.DirectionsService.NOT_FOUND:
			case WPGMZA.DirectionsService.ZERO_RESULTS:
			 
				var key = status.toLowerCase();
				var message = WPGMZA.localized_strings[key];
				
				$(this.element).find(".wpgmza-directions-results").html(
					'<i class="fa fa-times" aria-hidden="true"></i>' + message
				);
				
				break;
			
			default:
				
				var message = WPGMZA.localized_strings.unknown_error;
				
				$(this.element).find(".wpgmza-directions-results").html(
					'<i class="fa fa-times" aria-hidden="true"></i>' + message
				);
				
				break;
		}
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onNativeMapsApp = function(event)
	{
		var url = this.directionsBox.getExternalURL();
		window.open(url, "_blank");
	}
	
	WPGMZA.ModernDirectionsResultBox.prototype.onPrint = function(event)
	{
		var content = $(this.element).find(".wpgmza-directions-results").html();
		var doc = document.implementation.createHTMLDocument();
		var html;
		
		sessionStorage.wpgmzaPrintDirectionsHTML = content;
	};
	
	
});