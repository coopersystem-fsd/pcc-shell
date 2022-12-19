/**
 * @namespace WPGMZA
 * @module AdvancedTableDataTable
 * @requires WPGMZA.DataTable
 */
jQuery(function($) {
	
	WPGMZA.AdvancedTableDataTable = function(element, listing) {

		var self = this;
		
		this.element = element;
		this.listing = listing;
		
		WPGMZA.DataTable.apply(this, arguments);
		
		this.overrideListingOrderSettings = false;
		
		$(this.dataTableElement).on("click", "th", function(event) {
			
			self.onUserChangedOrder(event);
			
		});
	}
	
	WPGMZA.AdvancedTableDataTable.prototype = Object.create(WPGMZA.DataTable.prototype);
	WPGMZA.AdvancedTableDataTable.prototype.constructor = WPGMZA.AdvancedTableDataTable;
	
	WPGMZA.AdvancedTableDataTable.prototype.getDataTableSettings = function() {
		var self = this;
		var options = WPGMZA.DataTable.prototype.getDataTableSettings.apply(this, arguments);
		var json;
		
		if(json = $(this.element).attr("data-order-json"))
			options.order = JSON.parse(json);
		
		options.drawCallback = function(settings) {
			
			var ths = $(self.element).find(".wpgmza_table > thead th");
			
			if(!self.lastResponse || !self.lastResponse.meta)
				return; // Not ready yet
			
			if(self.lastResponse.meta.length == 0)
			{
				self.map.markerListing.trigger("markerlistingupdated");
				return; // No results
			}
			
			$(self.element).find(".wpgmza_table > tbody > tr").each(function(index, tr) {
				
				var meta = self.lastResponse.meta[index];
				
				$(tr).addClass("wpgmaps_mlist_row");
				$(tr).attr("mid", meta.id);
				$(tr).attr("mapid", self.map.id);
				
				$(tr).children("td").each(function(col, td) {
					
					var wpgmza_class = ths[col].className.match(/wpgmza_\w+/)[0];
					$(td).addClass(wpgmza_class);
					
				});
				
			});
			
			$(self.element).find("[data-marker-icon-src]").each(function(index, el) {
				
				var data;
				var src = $(el).attr("data-marker-icon-src");
				
				try{
					data = JSON.parse( src );
				}catch(e) {
					data = src;
				}
				
				var icon = WPGMZA.MarkerIcon.createInstance(data);
				
				icon.applyToElement(el);
				
			});
			
			
			self.map.markerListing.trigger("markerlistingupdated");
		};

		options.language = {};

		var languageURL = this.getLanguageURL();
		if(languageURL){
			options.language = {
				"url": languageURL
			};
		}

		//change no results string
		if(this.listing.map.settings.datatable_no_result_message != '')
		{
			var No_result = this.listing.map.settings.datatable_no_result_message;
			options.language.zeroRecords = No_result;	
		}

		//remove search option
		var remove_search = this.listing.map.settings.remove_search_box_datables;
		if(remove_search == true)
		{
			options.searching = false;
		}
		

		//pagination style
		var pagination_style_option = this.listing.map.settings.dataTable_pagination_style;
		switch (pagination_style_option) {
			case "page-number-buttons-only":
				options.pagingType = "numbers";
				break;
			case "prev-and-next-buttons-only":
				options.pagingType = "simple";
				break;
			case "prev-and-next-buttons-plus-page-numbers":
				options.pagingType = "simple_numbers";
				break;
			case "first-prev-next-and-last-buttons":
				options.pagingType = "full";
				break;
			case "first-prev-next-and-last-buttons-plus-page-numbers":
				options.pagingType = "full_numbers";
				break;
			case "first-and-last-buttons-plus-page-numbers":
				options.pagingType = "fist_last_numbers";
				break;
			}

		//change search string
		if(this.listing.map.settings.datatable_search_string != '') {
			var search_string = this.listing.map.settings.datatable_search_string;
			options.language.search = search_string;
				
		}

		if(this.listing.map.settings.datatable_result) {

			if(this.listing.map.settings.datatable_result_start != '')
				var start = this.listing.map.settings.datatable_result_start;

			if(this.listing.map.settings.datatable_result_of != '')
				var string_of = this.listing.map.settings.datatable_result_of;

			if(this.listing.map.settings.datatable_result_to != '')
				var string_to = this.listing.map.settings.datatable_result_to;

			if(this.listing.map.settings.datatable_result_total != '')
				var total = this.listing.map.settings.datatable_result_total;

			options.language.sInfo =  start + " _START_ " + string_of + " _END_ " + string_to + " _TOTAL_ "  + total;

		}

		if(this.listing.map.settings.datatable_result_page) {
			if(this.listing.map.settings.datatable_result_show != '')
				var show = this.listing.map.settings.datatable_result_show;

			if(this.listing.map.settings.datatable_result_to != '')
				var entries = this.listing.map.settings.datatable_result_entries;

			options.language.sLengthMenu = show + " _MENU_ " + entries;

		}


		return options;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onAJAXRequest = function(data, settings) {
		var request;
		var listingParams			= this.listing.getAJAXRequestParameters().data;
		var listingFilteringParams	= listingParams.filteringParams;
		var overrideMarkerIDs		= listingParams.overrideMarkerIDs;
		
		delete listingParams.filteringParams;
		delete listingParams.overrideMarkerIDs;
		
		request = $.extend(
			{},
			listingParams,
			WPGMZA.DataTable.prototype.onAJAXRequest.apply(this, arguments)
		);
		
		request.filteringParams = $.extend(
			{},
			listingFilteringParams,
			this.filteringParams
		);
		
		if(this.filteredMarkerIDs)
			request.markerIDs = this.filteredMarkerIDs.join(",");
		
		//if(this.filteringParams)
			//request.filteringParams = this.filteringParams;
		
		if(this.overrideListingOrderSettings !== undefined)
			request.overrideListingOrderSettings = this.overrideListingOrderSettings;
		
		return request;
	}

	WPGMZA.AdvancedTableDataTable.prototype.getLanguageURL = function(){
		return WPGMZA.DataTable.prototype.getLanguageURL.apply(this, arguments);
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onMarkerFilterFilteringComplete = function(event) {
		var self = this;
		
		this.filteredMarkerIDs = [];
		
		event.filteredMarkers.forEach(function(data) {
			self.filteredMarkerIDs.push(data.id);
		});
		
		self.filteringParams = event.filteringParams;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onUserChangedOrder = function(event) {
		this.overrideListingOrderSettings = true;
	}
	
});