/**
 * @namespace WPGMZA
 * @module WPGMZA.CategoriesPage
 * @requires WPGMZA
 */
jQuery(function($){ 

	WPGMZA.CategoriesPage = function()
	{
		if($(".wpgmza-marker-icon-picker").length > 0)
			this.markerIconPicker = new WPGMZA.MarkerIconPicker($(".wpgmza-marker-icon-picker"));
	}
	
	$(document).ready(function(event) {
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_CATEGORIES)
			WPGMZA.categoriesPage = new WPGMZA.CategoriesPage();
		
	});

});