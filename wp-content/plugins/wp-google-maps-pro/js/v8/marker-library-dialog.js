/**
 * @namespace WPGMZA
 * @module MarkerLibraryDialog
 * @requires WPGMZA
 */
jQuery(function($) {

	var searchTimeoutID, lazyLoaded, currentCallback;
	
	if(!window.WPGMZA)
		window.WPGMZA = {};
	
	WPGMZA.MarkerLibraryDialog = function(element)
	{
		var self = this;
		
		this.element = element;
		
		$(element).remodal();
		
		window.addEventListener("message", function(event) {
			
			if(event.data.action != "download_marker")
				return;
			
			$.ajax({
				
				url: ajaxurl,
				type: "POST",
				data: {
					action: "wpgmza_upload_base64_image",
					security: WPGMZA.legacyajaxnonce,
					data: event.data.data.replace(/^data:.+?base64,/, ''),
					mimeType: "image/png"
				},
				success: function(data, status, xhr) {
					var url = data.url;
					currentCallback(url);
					$(self.element).remodal().close();
				}
				
			});
			
		}, false);
	}
	
	WPGMZA.MarkerLibraryDialog.prototype.open = function(callback)
	{
		currentCallback = callback;
		
		$(this.element).remodal().open();
		
		$("iframe#mappity").attr("src", "https://www.mappity.org?wpgmza-embed=1");
	}
	
	WPGMZA.MarkerLibraryDialog.prototype.onSearch = function()
	{
		// Escape special regex characters and build regex
		var string = this.searchInput.val().replace(/[-\\^$*+?.()|[\]{}]/g, '\\$&');
		var regexp = new RegExp(string, "i");
		
		$(this.element).find("img").each(function(index, img) {
			var li = $(img).closest("li");
			var filename = $(img).attr("title").replace(/\.png$/, "");
			
			if(string.length && !filename.match(regexp))
				$(li).addClass("wpgmza-marker-library-no-result");
			else
				$(li).removeClass("wpgmza-marker-library-no-result");
		});
	}
	
	WPGMZA.MarkerLibraryDialog.prototype.onIconSelected = function(event)
	{
		currentCallback(event.target.src);
		$(this.element).remodal().close();
	}
	
	$(document).ready(function(event) {
		
		var el = $(".wpgmza-marker-library-dialog");
		
		if(!el.length)
			return;
		

		$(el).css('display','');

		WPGMZA.markerLibraryDialog = new WPGMZA.MarkerLibraryDialog(el);
		
		function bindButtonClickHandler(button)
		{
			// NB: This can be simplified once all areas use the new marker icon picker
			var target = $(button).closest(".wpgmza-marker-icon-picker").find(".wpgmza-marker-icon-url");
			var preview = $(button).closest(".wpgmza-marker-icon-picker").find("img, .wpgmza-marker-icon-preview");
			
			$(button).on("click", function() {
				WPGMZA.markerLibraryDialog.open(function(src) {
					target.val(src);
					target.change();
					
					if(preview.prop("tagName").match(/img/))
						preview.attr("src", src);
					else
						preview.css({"background-image": "url(" + src + ")"});
					
					if(!$(button).hasClass('wpgmza-marker-directions-library'))
						$("#wpgmza_cmm>img").attr("src", src);
				});
			});
		}
		
		$("input.wpgmza-marker-library, button.wpgmza-marker-library").each(function(index, el) {
			bindButtonClickHandler(el);
		});
		
	});
	
});