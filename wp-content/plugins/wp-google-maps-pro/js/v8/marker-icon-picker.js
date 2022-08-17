/**
 * @namespace WPGMZA
 * @module MarkerIconPicker
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.MarkerIconPicker = function(element) {
		var self = this;
		
		if(!element)
			throw new Error("Element cannot be undefined");
		
		if(!(element instanceof HTMLElement) && !(element instanceof jQuery && element.length == 1))
			throw new Error("Invalid element");
		
		this.element = element;
		
		var input = $(this.element).find("input.wpgmza-marker-icon-url");
		
		var name =  $(input).attr("name") || $(input).attr("data-ajax-name");
		
		if(input.length)
		{
			if(!name)
				throw new Error("Input must have a name for marker library to function");
			
			$(this.element).find("button.wpgmza-marker-library").attr("data-target-name", name);
			
			var icon = WPGMZA.MarkerIcon.createInstance(input.val());
			
			// NB: The above seems to be unfinished, or redundant
		}
		
		$(this.element).find("button.wpgmza-upload").on("click", function(event) {
			self.onUploadImage(event);
		});
		
		$(this.element).find("button.wpgmza-reset").on("click", function(event) {
			self.onReset(event);
		});
	}
	
	WPGMZA.MarkerIconPicker.prototype.setIcon = function(input) {

		var icon = WPGMZA.MarkerIcon.createInstance(input);
		var url = icon.url;
		
		var preview = url;
		
		if(url != WPGMZA.defaultMarkerIcon)
			$(this.element).find("input.wpgmza-marker-icon-url").val(url);
		else
			$(this.element).find("input.wpgmza-marker-icon-url").val("");
		
		if(url.length == 0)
			preview = WPGMZA.defaultMarkerIcon;
		
		$(this.element).find(".wpgmza-marker-icon-preview").css({
			"background-image": "url('" + preview + "')"
		});
	}
	
	WPGMZA.MarkerIconPicker.prototype.onUploadImage = function() {
		var self = this;
		
		WPGMZA.openMediaDialog(function(attachment_id, attachment_url) {
			self.setIcon(attachment_url);
			$(this.element).find("input.wpgmza-marker-icon-url").val("");
		});
	}
	
	WPGMZA.MarkerIconPicker.prototype.onReset = function() {
		this.reset();
	}
	
	WPGMZA.MarkerIconPicker.prototype.reset = function() {
		this.setIcon(WPGMZA.defaultMarkerIcon);
	}
	
});