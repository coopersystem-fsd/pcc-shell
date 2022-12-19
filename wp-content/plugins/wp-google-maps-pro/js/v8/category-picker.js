/**
 * @namespace WPGMZA
 * @module WPGMZA.CategoryPicker
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.CategoryPicker = function(element)
	{
		var self = this;
		var data = JSON.parse( $(element).attr("data-js-tree-data") );
		
		this.element = element;
		this.input = $(this.element).find("input.wpgmza-category-picker-input");
		
		$(this.element).jstree({
			"core": {
				"data": data
			},
			"plugins": [
				"checkbox"
			]
		}).on("loaded.jstree", function() {
			$(self.element).jstree("open_all");
		});
		
		$(this.element).after(this.input);
		
		$(this.element).on("changed.jstree", function(e, data) {
			
			self.input.val(self.getSelection().join(","));
			
		});
	}
	
	WPGMZA.CategoryPicker.prototype.getSelection = function()
	{
		return $(this.element).jstree("get_selected");
	}
	
	WPGMZA.CategoryPicker.prototype.setSelection = function(arr)
	{
		$(this.element).jstree("deselect_all");
		
		if(!arr)
			return;
		
		$(this.element).jstree("select_node", arr);
	}
	
});