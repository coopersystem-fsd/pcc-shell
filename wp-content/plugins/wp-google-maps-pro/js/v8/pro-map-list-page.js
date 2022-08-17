/**
 * @namespace WPGMZA
 * @module ProMapListPage
 * @requires WPGMZA.MapListPage
 */
jQuery(function($) {
	
	WPGMZA.ProMapListPage = function()
	{
		var self = this;
		
		WPGMZA.MapListPage.apply(this, arguments);
		
		$("[data-action='new-map']").on("click", function(event) {
			self.onNewMap(event);
		});
		
		$("[data-action='wizard']").on("click", function(event) {
			self.onWizard(event);
		});
	}
	
	WPGMZA.extend(WPGMZA.ProMapListPage, WPGMZA.MapListPage);
	
	WPGMZA.MapListPage.createInstance = function()
	{
		return new WPGMZA.ProMapListPage();
	}
	
	WPGMZA.ProMapListPage.prototype.onNewMap = function(event)
	{
		$(event.target).prop("disabled", "true");
		
		WPGMZA.restAPI.call("/maps/", {
			method: "POST",
			data: {
				map_title:		WPGMZA.localized_strings.new_map,
				map_start_lat:	36.778261,
				map_start_lng:	-119.4179323999,
				map_start_zoom: 3
			},
			success: function(response, status, xhr) {
				
				window.location.href = window.location.href = "admin.php?page=wp-google-maps-menu&action=edit&map_id=" + response.id;
				
			}
		});
	}
	
	WPGMZA.ProMapListPage.prototype.onWizard = function(event)
	{
		window.location.href = "admin.php?page=wp-google-maps-menu&action=wizard";
	}
	
});