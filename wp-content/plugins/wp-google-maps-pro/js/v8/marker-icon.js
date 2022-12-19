/**
 * @namespace WPGMZA
 * @module MarkerIcon
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.MarkerIcon = function(options) {
		var self = this;
		
		WPGMZA.EventDispatcher.apply(this, arguments);

		this.isLoaded	= false;
		
		this.url		= "";
		this.retina		= false;
		
		if(typeof options == "object") {
			for(var key in options)
				this[key] = options[key];
		}
		else if(typeof options == "string") {
			try{
				var json = JSON.parse(options);
				
				for(var key in json)
					this[key] = json[key];
			}catch(e) {
				this.url = options;
			}
		}
		else if(options)
			throw new Error("Argument must be an object");
		
		this.url = this.url.replace(/^http(s?):/, "");
		
		this.dimensions = {
			width: null,
			height: null
		};
		var url = (this.isDefault ? WPGMZA.defaultMarkerIcon : this.url);
		WPGMZA.getImageDimensions(url, function(dimensions) {
			
			self.dimensions = dimensions;
			
			self.isLoaded = true;
			self.trigger("load");
			
		});
	}
	
	WPGMZA.extend(WPGMZA.MarkerIcon, WPGMZA.EventDispatcher);
	
	WPGMZA.MarkerIcon.createInstance = function(options) {
		return new WPGMZA.MarkerIcon(options);
	}
	
	Object.defineProperty(WPGMZA.MarkerIcon.prototype, "width", {
		
		get: function() {
			if(this.retina)
				return parseInt(WPGMZA.settings.retinaWidth);
				
			return parseInt(this.dimensions.width);
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerIcon.prototype, "height", {
		
		get: function() {
			if(this.retina)
				return parseInt(WPGMZA.settings.retinaHeight);
				
			return parseInt(this.dimensions.height);
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerIcon.prototype, "isDefault", {	
		
		"get": function() {
			return this.url.length == 0 || this.url == WPGMZA.defaultMarkerIcon.replace(/^http(s?):/, "");
		}
	});
	
	WPGMZA.MarkerIcon.prototype.applyToElement = function(element) {
		if(this.isDefault)
			$(element).attr("src", WPGMZA.defaultMarkerIcon);
		else
			$(element).attr("src", this.url);
		
		if(this.retina) {
			$(element).css({
				"width":	this.width + "px",
				"height":	this.height + "px"
			});
		}
	}
	
});
