/**
 * @namespace WPGMZA
 * @module ModernDirectionsBox
 * @requires WPGMZA.DirectionsBox
 */
jQuery(function($) {
	
	/**
	 * The new modern look directions box. It takes the elements
	 * from the default look and moves them into the map, wrapping
	 * in a new element so we can apply new styles.
	 * @return Object
	 */
	WPGMZA.ModernDirectionsBox = function(map) {
		
		WPGMZA.DirectionsBox.apply(this, arguments);
		
		var self = this;
		var original = this.element;
		
		if(!original.length)
			return;
		
		var container = $(map.element);
		
		this.map = map;
		
		// Build element
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-directions-box'></div>");
		this.panel = new WPGMZA.PopoutPanel(this.element);
		
		// Add to DOM tree
		this.element.append(original);
		container.append(this.element);
		
		// Add buttons
		$(this.element).find("h2").after($("\
			<div class='wpgmza-directions-buttons'>\
				<span class='wpgmza-close'><i class='fa fa-times' aria-hidden='true'></i></span>\
			</div>\
		"));
		
		// Remove labels
		$(this.element).find("td:first-child").remove();
		
		// Move show options and options box to after the type select
		var row = $(this.element).find("select[name^='wpgmza_dir_type']").closest("tr");
		$(this.element).find(".wpgmaps_to_row").after(row);
		
		// Options box
		$(this.element).find("#wpgmza_options_box_" + map.id).addClass("wpgmza-directions-options");
		
		// Fancy checkboxes (This would require adding admin styles)
		//$(this.element).find("input:checkbox").addClass("postform cmn-toggle cmn-toggle-round-flat");
		
		// NB: Via waypoints is handled below to be compatible with legacy systems. Search "Waypoint JS"
		
		// Result box
		this.resultBox = new WPGMZA.ModernDirectionsResultBox(map, this);
		
		var behaviour = map.settings.directions_behaviour;
			
		if(behaviour == "intelligent")
		{
			if(WPGMZA.isTouchDevice())
				behaviour = "external";
			else
				behaviour = "default";
		}
		
		if(behaviour == "default")
		{
			$(this.element).find(".wpgmaps_get_directions").on("click", function(event) {
				if(self.from.length == 0 || self.to.length == 0)
				return;
			
				self.resultBox.open();
		});
		}
		
		// Close button
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.panel.close();
		});
		
		$(this.element).on('click', '.wpgmza-travel-mode-option', function(){
		
		    var mode = jQuery(this).data('mode');
		    jQuery('body').find('.wpgmza-travel-mode-option').removeClass('wpgmza-travel-option__selected');
		    jQuery(this).addClass('wpgmza-travel-option__selected');
		    jQuery('body').find('.wpgmza-travel-mode').val(mode);
		});
	};
	
	WPGMZA.extend(WPGMZA.ModernDirectionsBox, WPGMZA.DirectionsBox);
	
	Object.defineProperty(WPGMZA.ModernDirectionsBox.prototype, "from", {
		get: function() {
			return $(this.element).find("input.wpgmza-directions-from").val();
		},
		set: function(value) {
			return $(this.element).find("input.wpgmza-directions-from").val(value);
		}
	});
	
	Object.defineProperty(WPGMZA.ModernDirectionsBox.prototype, "to", {
		get: function() {
			return $(this.element).find("input.wpgmza-directions-to").val();
		},
		set: function(value) {
			return $(this.element).find("input.wpgmza-directions-to").val(value);
		}
	});
	
	/**
	 * Opens the popup and closes the results box if it's open
	 * @return void
	 */
	WPGMZA.ModernDirectionsBox.prototype.open = function()
	{
		this.panel.open();
		
		if(this.resultBox)
			this.resultBox.close();
		
		$(this.element).children().show();
	};
	
	/**
	 * Fires when the "open native map" button is clicked
	 * @return void
	 */
	WPGMZA.ModernDirectionsBox.prototype.onNativeMapsApp = function()
	{
		var url = this.getExternalURL();
		window.open(url, "_blank");
	}
	
});