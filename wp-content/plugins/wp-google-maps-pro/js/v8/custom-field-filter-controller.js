/**
 * @namespace WPGMZA
 * @module CustomFieldFilterController
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * This module handles the custom field filtering logic
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterController = function(map_id)
	{
		var self = this;
		
		this.map_id = map_id;
		this.widgets = [];
		this.ajaxTimeoutID = null;
		this.ajaxRequest = null;
		
		// TODO: This will break pagination (page count mismatch) when we integrate pagination for basic styles. I suggest we unify the filtering before doing so
		this.markerListingCSS = $("<style type='text/css'/>");
		$(document.body).append(this.markerListingCSS);
		
		WPGMZA.CustomFieldFilterController.controllersByMapID[map_id] = this;
		
		$("[data-wpgmza-filter-widget-class][data-map-id=" + map_id + "]").each(function(index, el) {
			self.widgets.push( WPGMZA.CustomFieldFilterWidget.createInstance(el) );
			
			$(el).on("input change", function(event) {
				self.onWidgetChanged(event);
			});
			
			if($(el).is(":checkbox"))
				$(el).on("click", function(event) {
					self.onWidgetChanged(event);
				});
		});
		
		var container = $(".wpgmza-filter-widgets[data-map-id='" + map_id + "']");
		$(container).find("button.wpgmza-reset-custom-fields").on("click", function(event) {
			$(container).find("input:not([type='checkbox']):not([type='radio']), textarea").val("");
			$(container).find("input[type='checkbox']").prop("checked", false);
			//$(container).find("option:selected").prop("selected", false);
			//$(container).find("option[value='*']").prop("selected", true);
			$(container).find("select").val("");
			self.onWidgetChanged();
		});
	};
	
	WPGMZA.CustomFieldFilterController.AJAX_DELAY = 500;
	WPGMZA.CustomFieldFilterController.controllersByMapID = {};
	WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID = {};
	
	WPGMZA.CustomFieldFilterController.createInstance = function(map_id)
	{
		return new WPGMZA.CustomFieldFilterController(map_id);
	};
	
	WPGMZA.CustomFieldFilterController.prototype.getAjaxRequestData = function() {
		var self = this;
		
		var result = {
			url: WPGMZA.ajaxurl,
			method: "POST",
			data: {
				action: "wpgmza_custom_field_filter_get_filtered_marker_ids",
				map_id: this.map_id,
				widgetData: []
			},
			success: function(response, status, xhr) {
				self.onAjaxResponse(response, status, xhr);
			}
		};
		
		this.widgets.forEach(function(widget) {
			result.data.widgetData.push(widget.getAjaxRequestData());
		});
		
		return result;
	};
	
	WPGMZA.CustomFieldFilterController.prototype.onWidgetChanged = function(event) {
		var self = this;
		
		var map = WPGMZA.getMapByID(this.map_id);

		/*
		 * Temporary system to move DataTables back to page 1 before filter application
		 *
		 * We really should rework this into the core classes which manage filtering
		 * 
  		 * For now, this should hold up most of the time, but because it does not address the root cause, we can't rely on it permanently
  		 *
  		 * Added:2021-07-27
  		*/
  		if(map.markerListing && map.markerListing.dataTable && map.markerListing.dataTable.dataTable){
  			map.markerListing.dataTable.dataTable.page(1).draw();
  		}
  		
		map.markerFilter.update({}, this);
	};
	
	WPGMZA.CustomFieldFilterController.prototype.onAjaxResponse = function(response, status, xhr) {
		this.lastResponse = response;
		
		var selectors = [];
		
		for(var marker_id in marker_array[this.map_id])
		{
			var visible = (response.marker_ids.length == 0 || response.marker_ids.indexOf(marker_id) > -1);
			marker_array[this.map_id][marker_id].setVisible(visible);
			
			if(!visible)
				selectors.push(".wpgmaps_mlist_row[mid='" + marker_id + "']");
		}
		
		if(wpgmaps_localize[this.map_id].order_markers_by && wpgmaps_localize[this.map_id].order_markers_by == 2)
		{
			wpgmza_update_data_table(
				WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID[this.map_id],
				this.map_id
			);
		}
		else
		{
			this.markerListingCSS.html( selectors.join(", ") + "{ display: none; }" );
			
			var container;
			if(this.currAdvancedTableHTML)
				container = $("#wpgmza_marker_holder_" + this.map_id);
			else
				container = $(this.currAdvancedTableHTML);
			
			this.applyToAdvancedTable(container);
		}
	};
	
	/**
	 * This function is a quick hack to re-apply the last response after the store locator
	 * has been used or marker listing filtering changes. This should be deprecated and
	 * the filtering system unified at some point.
	 * @return void
	 */
	WPGMZA.CustomFieldFilterController.prototype.reapplyLastResponse = function() {
		if(!this.lastResponse)
			return;
		
		var response = this.lastResponse;
		
		for(var marker_id in marker_array[this.map_id])
		{
			var visible = (response.marker_ids.indexOf(marker_id) > -1);
			marker_array[this.map_id][marker_id].setVisible(visible);
		}
	};
	
	WPGMZA.CustomFieldFilterController.prototype.applyToAdvancedTable = function() {
		if(!this.lastResponse)
			return;
		
		var response = this.lastResponse;
		var container = $("#wpgmza_marker_holder_" + this.map_id);
		
		$(container).find("[mid]").each(function(index, el) {
			var marker_id = $(el).attr("mid");
			if(response.marker_ids.indexOf(marker_id) == -1)
				$(el).remove();
		});
	};
	
	$(window).on("load", function(event) {
		
		if(WPGMZA.is_admin == 1)
			return;
		
		$(".wpgmza_map").each(function(index, el) {
			var map_id = parseInt( $(el).attr("id").match(/\d+/)[0] );
			
			/*MYMAP[map_id].customFieldFilterController 
				= MYMAP[map_id].map.customFieldFilterController 
				= WPGMZA.CustomFieldFilterController.createInstance(map_id);*/

            setTimeout(function () {
                $(el).children('div').first().after($('.wpgmza-modern-marker-open-button'));
            }, 500);
		});
		
		
	});
	
});