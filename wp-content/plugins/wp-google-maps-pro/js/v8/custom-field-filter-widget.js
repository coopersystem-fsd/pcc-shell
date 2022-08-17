/**
 * @namespace WPGMZA
 * @module CustomFieldFilterWidget
 * @requires WPGMZA
 */
jQuery(function($) {

	/**
	 * This is the base module for custom field filter widgets
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget = function(element) {
		this.element = element;
	};
	
	WPGMZA.CustomFieldFilterWidget.createInstance = function(element) {
		var widgetPHPClass = $(element).attr("data-wpgmza-filter-widget-class");
		var constructor = null;
		
		switch(widgetPHPClass)
		{
			case "WPGMZA\\CustomFieldFilterWidget\\Text":
				constructor = WPGMZA.CustomFieldFilterWidget.Text;
				break;
				
			case "WPGMZA\\CustomFieldFilterWidget\\Dropdown":
				constructor = WPGMZA.CustomFieldFilterWidget.Dropdown;
				break;
			
			case "WPGMZA\\CustomFieldFilterWidget\\Checkboxes":
				constructor = WPGMZA.CustomFieldFilterWidget.Checkboxes;
				break;

			case "WPGMZA\\CustomFieldFilterWidget\\Time":
				constructor = WPGMZA.CustomFieldFilterWidget.Time;
				break;

			case "WPGMZA\\CustomFieldFilterWidget\\Date":
				constructor = WPGMZA.CustomFieldFilterWidget.Date;
				break;
				
			default:
				throw new Error("Unknown field type '" + widgetPHPClass + "'");
				break;
		}
		
		return new constructor(element);
	};
	
	WPGMZA.CustomFieldFilterWidget.prototype.getAjaxRequestData = function() {
		var data = {
			field_id: $(this.element).attr("data-field-id"),
			value: $(this.element).val()
		};
		
		return data;
	};
	
	/**
	 * Text field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Text = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Text.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Text.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Text;
	
	/**
	 * Dropdown field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Dropdown = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Dropdown.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Dropdown.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Dropdown;
	
	/**
	 * Checkboxes field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Checkboxes = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Checkboxes;
	
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype.getAjaxRequestData = function() {
		var checked = [];
		
		$(this.element).find(":checked").each(function(index, el) {
			checked.push($(el).val());
		});
		
		return {
			field_id: $(this.element).attr("data-field-id"),
			value: checked
		}
	};
	
	$(document.body).on("mouseover", ".wpgmza-placeholder-label", function(event) {
	
		$(event.currentTarget).children("ul.wpgmza-checkboxes").stop(true, false).fadeIn();
	
	});
	
	$(document.body).on("mouseleave", ".wpgmza-placeholder-label", function(event) {
	
		$(event.currentTarget).children("ul.wpgmza-checkboxes").stop(true, false).fadeOut();
	
	});

	/**
	 * Time field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Time = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};

	WPGMZA.CustomFieldFilterWidget.Time.prototype.getAjaxRequestData = function() {

		var field_id = $(this.element).attr("data-field-id");

		var data = {
			field_id: field_id,
			value_start: $('[data-field-id="' + field_id + '"][data-date-start="true"]').val(),
			value_end: $('[data-field-id="' + field_id + '"][data-date-end="true"]').val(),
			type: 'time'
		};

		return data;
	};

	/**
	 * Date field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Date = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};

	WPGMZA.CustomFieldFilterWidget.Date.prototype.getAjaxRequestData = function() {
		var field_id = $(this.element).attr("data-field-id");
		var data = {
			field_id: field_id,
			value_start: $('[data-field-id="' + field_id + '"][data-date-start="true"]').val(),
			value_end: $('[data-field-id="' + field_id + '"][data-date-end="true"]').val(),
			type: 'date'
		};
		
		return data;
	};
	
});