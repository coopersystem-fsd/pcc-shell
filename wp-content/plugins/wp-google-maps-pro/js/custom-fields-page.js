jQuery(function($) {
	
	if(!window.WPGMZA)
		window.WPGMZA = {};
	
	WPGMZA.CustomFieldsPage = function() {
		var self = this;
		
		this.newRow = $("tr#wpgmza-new-custom-field");
		this.attributeTableInitialState = this.newRow.find("table.attributes").html();
		
		// Event listeners
		$("form#wpgmza-custom-fields").on("submit", function(event) {
			self.onSubmit(event);
		});
		
		$("form#wpgmza-custom-fields").on("click", ".wpgmza-delete-custom-field", function(event) {
			self.onDeleteRow(event);
		});

		$("form#wpgmza-custom-fields").on("click", ".wpgmza-display-in-infowindows", function(event) {
			self.onToggleInfoWindowDisplay(event);
		});

		$("form#wpgmza-custom-fields").on("click", ".wpgmza-display-in-marker-listings", function(event) {
			self.onToggleMarkerListingDisplay(event);
		});
		
		$("form#wpgmza-custom-fields input[type='submit']").on("click", function(event) {
			self.onSave(event);
		});
		
		$("form#wpgmza-custom-fields").on("keydown", "table.attributes", function(event) {
			self.onAttributesKeyDown(event);
		});
		
		$("form#wpgmza-custom-fields").on("input", "table.attributes", function(event) {
			self.onAttributesInput(event);
		});
		
		$("form#wpgmza-custom-fields").on("change input", function(event) {
			self.onChange(event);
		});
		
		$("#wpgmza-new-custom-field input[name='ids[]']").hide();
		
		$(".wpgmza-fontawesome-iconpicker").iconpicker();
		
		if(WPGMZA.isSafari())
		{
			// Safari workaround for activeElement not matching clicked submit button.
			// Please note this workaround will prevent the "required" attribute from issuing a message where it normally would.
			$(".wpgmza-add-custom-field").attr("type", "button");
			
			$(".wpgmza-add-custom-field").on("click", function(event) {
				
				self.addRow();
				event.preventDefault();
				return false;
				
			});
		}
	}
	
	/**
	 * Add a custom field table row
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.addRow = function() {
		var row = this.newRow.clone();
		
		row.removeAttr("id");
		row.find(".wpgmza-add-custom-field").remove();
		row.children("td:last-child").append(
			$("<button type='button' class='button wpgmza-delete-custom-field'><i class='fa fa-trash-o' aria-hidden='true'></i></button>")
		);
		
		try{
			row.find(".wpgmza-fontawesome-iconpicker").iconpicker();
		}catch(e) {
			console.warn("Error initializing FontAwesome icon picker");
		}
		
		var newWidgetType = this.newRow.find("[name='widget_types[]']");
		row.find("[name='widget_types[]']").val(newWidgetType.val());
		newWidgetType.val("none");
		this.newRow.before(row);
		this.newRow.find("input").val("");
		this.newRow.find("input[name='names[]']").focus();
		
		this.newRow.find("table.attributes").html(this.attributeTableInitialState);
		
		this.onChange();
	}
	
	/**
	 * Delete a custom field table row
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onDeleteRow = function(event) {
		$(event.currentTarget).closest("tr").remove();
		
		this.onChange();
	}
	
	/**
	 * Add an attribute table row
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.addAttributeRow = function(event) {
		var row = $(event.target).closest("tr");
		var name = row.find("input.attribute-name");
		var val = row.find("input.attribute-value");
		
		if(!$(name).val().length)
		{
			$(name).focus();
			return;
		}
		
		var newRow = row.clone();
		newRow.find("input").val("");
		row.parent().append(newRow);
		newRow.find("input.attribute-name").focus();
		
		this.onChange();
	}
	
	/**
	 * Called upon keydown on an attribute table input
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onAttributesKeyDown = function(event) {
		var row = $(event.target).closest("tr");
		var name = row.find("input.attribute-name");
		var val = row.find("input.attribute-value");
		
		switch(event.keyCode)
		{
			// Stop enter submitting the form
			case 13:	
				this.addAttributeRow(event);
				
				event.preventDefault();
				return false;
				break;
				
			// Stop backspace from navigating back
			case 8:
				if(name.val().length == 0 && val.val().length == 0)
				{
					this.onDeleteAttributeRow(event);
					event.preventDefault();
					return false;
				}
				
				if(val.val().length == 0 && event.target == val[0])
					name.focus();
				
				break;
		}
	}
	
	/**
	 * Called upon input on an attribute table input
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onAttributesInput = function(event) {
		var row = $(event.target).closest("tr");
		var name = row.find("input.attribute-name");
		var val = row.find("input.attribute-value");
		
		if(name.val().length == 0 && val.val().length == 0)
			this.onDeleteAttributeRow(event);
	}
	
	/**
	 * Deletes an attribute row based on event
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onDeleteAttributeRow = function(event) {
		var numRows = $(event.target).closest("tbody").children("tr").length;
		
		if(numRows == 1)
			return;	// Don't delete when only one row is present
		
		var row = $(event.target).closest("tr");
		var prevValueInput = row.prev("tr").find("input.attribute-value");
		
		row.remove();
		prevValueInput.focus();
		
	};
	
	/**
	 * Binds the unload listener when the form changes
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onChange = function(event) {
		var self = this;
		
		if(!event)
			event = {};
		
		if(event.type == "input" && 
			$(event.target).closest("#wpgmza-new-custom-field").length &&
			!$(event.target).is("input.iconpicker-search-input"))
		{
			var name = $(event.target).attr("name");
			
			this.addRow();
			
			var target = $(event.target).closest("#wpgmza-new-custom-field").prev("tr").find("[name='" + name + "']");
			
			target.focus();
			if(target[0].setSelectionRange)
				target[0].setSelectionRange(target.val().length, target.val().length);
			
			$(event.target).val("");
		}
		
		if(this.unloadListenerBound)
			return;
		
		window.addEventListener("beforeunload", this.onBeforeUnload);
		
		this.unloadListenerBound = true;
	}

	/**
	 * Called upon toggling the visiblity of a custom field for InfoWindows
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onToggleInfoWindowDisplay = function(event) {
		$(event.currentTarget).toggleClass('fa-eye');
		$(event.currentTarget).toggleClass('fa-eye-slash');

		this.onChange();
	}

	/**
	 * Called upon toggling the visiblity of a custom field for Marker Listings
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onToggleMarkerListingDisplay = function(event) {
		$(event.currentTarget).toggleClass('fa-eye');
		$(event.currentTarget).toggleClass('fa-eye-slash');

		this.onChange();
	}
	
	/** 
	 * Prompt the user to leave or not after changes have been made
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onBeforeUnload = function(event) {
		var confirmationMessage = "Are you sure you want to leave without saving your changes?";
		
		event.preventDefault();
		event.returnValue = confirmationMessage;
		
		return confirmationMessage;
	};
	
	/**
	 * Called when the user clicks the save button
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onSave = function(event) {
		var newRowNameInput = $("#wpgmza-new-custom-field input[name='names[]']")
		
		window.removeEventListener("beforeunload", this.onBeforeUnload);
		this.unloadListenerBound = false;
		
		newRowNameInput.removeAttr("required");
		
		setTimeout(function(event) {
			newRowNameInput.attr("required", "required");
		}, 5);
	}
	
	/**
	 * Called when the forum subits, removes the required attribute on new row
	 * @return void
	 */
	WPGMZA.CustomFieldsPage.prototype.onSubmit = function(event) {
		
		if($(document.activeElement).hasClass("wpgmza-add-custom-field"))
		{
			this.addRow();
			event.preventDefault();
			
			return false;
		}
		
		var name_arr = new Array();

		$('#wpgmza-custom-fields-table').children().find("input[name='names[]']").each(function(){
			name_arr.push($(this).val());
		});
    
		for(var i=0; i<name_arr.length;i++){
			for(var j=i+1;j<name_arr.length;j++){
				if(name_arr[i]==name_arr[j]){
					alert(WPGMZA.localized_strings.duplicate_custom_field_name);
					event.preventDefault();
					return false;         
				}
			}
		}
		
		$("#wpgmza-custom-fields>table>tbody>tr:last-child").remove();
		
		$("#wpgmza-custom-fields table.attributes").each(function(index, el) {
			var json = {};
			
			$(el).find("input.attribute-name").each(function(j, input) {
				var name = $(input).val();
				var val = $(input).closest("tr").find("input.attribute-value").val();
				json[name] = val;
			});
			
			$(el).closest("tr").find("input[name='attributes[]']").val(JSON.stringify(json));
		});
		
	}
	
	$(document).ready(function(event) {
		WPGMZA.customFieldsPage = new WPGMZA.CustomFieldsPage();
	});

	jQuery( function() {
	    jQuery( "tbody" ).sortable({
	        items: "tr:not(#wpgmza-new-custom-field)",
	        handle: ".handle"      
	    });
	    jQuery( "tbody" ).disableSelection();
    });
	
});