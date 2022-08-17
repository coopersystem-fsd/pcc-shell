/**
 * @namespace WPGMZA
 * @module AdvancedPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.AdvancedPage = function()
	{
		$("#wpgmaps_tabs").tabs();
		
		WPGMZA.restAPI.call("/markers?action=count-duplicates", {
			
			success: function(result) {
				
				// $("button#wpgmza-remove-duplicates").append(" (" + result.count + ")");
			
			}
				
		});
		
		$("button#wpgmza-remove-duplicates").on("click", function(event) {
			
			if(!confirm(WPGMZA.localized_strings.confirm_remove_duplicates))
				return;
			
			$(event.target).prop("disabled", true);
			
			WPGMZA.restAPI.call("/markers?action=remove-duplicates", {
				
				success: function(result) {
					
					alert(result.message);
					$(event.target).prop("disabled", false);
					
				}
				
			});
			
		});
	}
	
	$(document).ready(function(event) {
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_ADVANCED)
			WPGMZA.advancedPage = new WPGMZA.AdvancedPage();
		
	});
	
});