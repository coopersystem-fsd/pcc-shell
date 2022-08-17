/**
 * @namespace WPGMZA
 * @module AdvancedTableMarkerListing
 * @requires WPGMZA.MarkerListing
 */
jQuery(function($) {
	
	WPGMZA.AdvancedTableMarkerListing = function(map, element, options) {
		var self = this;
		
		// NB: Legacy compatibility
		this.element = element = $("#wpgmza_marker_holder_" + map.id + ", #wpgmza_marker_list_" + map.id);
		
		WPGMZA.MarkerListing.apply(this, arguments);
		
		this.dataTable = new WPGMZA.AdvancedTableDataTable(element, this);
		this.dataTable.map = map;
	}
	
	WPGMZA.AdvancedTableMarkerListing.prototype = Object.create(WPGMZA.MarkerListing.prototype);
	WPGMZA.AdvancedTableMarkerListing.prototype.constructor = WPGMZA.AdvancedTableMarkerListing;
	
	WPGMZA.AdvancedTableMarkerListing.prototype.reload = function() {
		if(!this.dataTable)
			return; // NB: Still construction. We return, as the dataTable will load itself on init.
		
		this.dataTable.reload();
	}
	
	WPGMZA.AdvancedTableMarkerListing.prototype.onFilteringComplete = function(event) {
		this.dataTable.onMarkerFilterFilteringComplete(event);
		
		WPGMZA.MarkerListing.prototype.onFilteringComplete.apply(this, arguments);
	}
	
	WPGMZA.AdvancedTableMarkerListing.prototype.onItemClick = function(event) {
		var isFirstCell = $(event.target).is(":first-child");
		var isCollapsed	= $(event.target).closest(".dataTable").is(".collapsed");
		
		if(isCollapsed && isFirstCell)
			return;	// NB: Do nothing. ALlow dataTables responsive module to expand and collapse the row
		
		// NB: Call the parent function
		WPGMZA.MarkerListing.prototype.onItemClick.call(this, event);
	}
	
});