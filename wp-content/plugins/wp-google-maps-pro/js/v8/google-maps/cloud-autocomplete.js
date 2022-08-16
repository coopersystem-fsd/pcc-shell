/**
 * @namespace WPGMZA
 * @module CloudAutocomplete
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.CloudAutocomplete = function(element, options)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.apply(this, arguments);
		
		this.element = element;
		this.options = options;
		
		$(this.element).wrap("<div class='wpgmza-cloud-address-input-wrapper'></div>");
		this.wrapper = $(this.element).parent();
		
		this.preloader = $(WPGMZA.loadingHTML);
		$(this.element).after(this.preloader);
		$(this.preloader).hide();
		
		this.session = {
			guid: null,
			expires: 0
		};
		
		$(element).autocomplete({
			open: function(event, ui) {
				self.onOpen(event, ui);
			},
			
			select: function(event, ui) {
				self.onSelect(event, ui);
			},
			
			source: function( request, response ) {
				
				// Session management
				var now = new Date().getTime();
				
				if(self.session.expires < now)
					self.session.guid		= WPGMZA.guid();
					
				self.session.expires	= now + 30000;
				
				// Data
				var defaults = {
					input:			$(self.element).val(),
					sessiontoken:	self.session.guid
				};
				
				if(options.country)
					defaults.components = "country:" + options.country;
				
				var data = $.extend(defaults, self.options);
				
				// Pre-loader
				self.showPreloader(true);
				
				// Make the request
				WPGMZA.cloudAPI.call("/autocomplete", {
					
					data: data,
					
					success: function( data ) {
						
						var items = [];
						
						data.predictions.forEach(function(prediction) {
							items.push({
								id:		prediction.id,
								value:	prediction.description
							})
						});
						
						response( items );
						
						self.showPreloader(false);
						
					}
					
				});
			}
		});
		
		this.widget = $(element).autocomplete("widget");
		this.widget.addClass( "wpgmza-cloud-autocomplete" );
	}
	
	WPGMZA.extend(WPGMZA.CloudAutocomplete, WPGMZA.EventDispatcher);
	
	WPGMZA.CloudAutocomplete.prototype.onOpen = function(event, ui)
	{
		this.widget.css({
			width: $(this.element).outerWidth() + "px"
		});
	}
	
	WPGMZA.CloudAutocomplete.prototype.onSelect = function(event, ui)
	{
		this.session.expires = 0;
	}
	
	WPGMZA.CloudAutocomplete.prototype.showPreloader = function(show)
	{
		if(show)
			$(this.preloader).show();
		else
			$(this.preloader).hide();
	}
	
});