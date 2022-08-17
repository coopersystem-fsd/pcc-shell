/**
 * @namespace WPGMZA
 * @module MarkerGalleryInput
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.MarkerGalleryInput = function(input)
	{
		var self = this;
		var container = $(input).parent();
		
		container.append("<div class='wpgmza-gallery-input'><ul><li class='wpgmza-add-new-picture'><i class='fa fa-camera' aria-hidden='true'></i></li></ul></div>");
		
		this.input = input;
		this.element = container.find(".wpgmza-gallery-input");
		
		$(this.input).next("#upload_image_button").remove();
		$(this.input).hide();
		
		this.addNewPictureButton = $(this.element).find(".wpgmza-add-new-picture");
		this.addNewPictureButton.on("click", function(event) {
			self.onAddNewPictureClicked(event);
		});
		
		this.templateItem = $(this.addNewPictureButton).clone();
		this.templateItem.removeClass("wpgmza-add-new-picture");
		this.templateItem.find("i").remove();
		
		$(this.element).find("ul").sortable({
			items: "li:not(.wpgmza-add-new-picture)",
			stop: function() {
				self.onDragEnd();
			}
		});
		
		$(document.body).on("click", ".wpgmza-delete-gallery-item", function(event) {
			self.onDeleteItem(event);
		});
	}
	
	WPGMZA.MarkerGalleryInput.prototype.populate = function(arr)
	{
		this.clear();
		
		if(!arr || !arr.length)
			return;
		
		for(var i = 0; i < arr.length; i++)
			this.addPicture(arr[i]);
	}
	
	WPGMZA.MarkerGalleryInput.prototype.update = function()
	{
		var string = this.serialize();
		
		this.input.val(string);
		this.input.attr("value", string);
	}
	
	WPGMZA.MarkerGalleryInput.prototype.clear = function()
	{
		$(this.element).find("[data-picture-url]").remove();
	}
	
	WPGMZA.MarkerGalleryInput.prototype.addPicture = function(picture)
	{
		var item = this.templateItem.clone();
		var url = picture.url;
		
		item.css({
			"background-image": "url('" + url + "')"
		});
		item.attr("data-picture-url", url);
		item.attr("data-attachment-id", picture.attachment_id);
		item.insertBefore(this.addNewPictureButton);
		
		item.append($("<button type='button' class='wpgmza-delete-gallery-item'>✖</button>"));
		
		this.update();
	}
	
	WPGMZA.MarkerGalleryInput.prototype.serialize = function()
	{
		return JSON.stringify(this.toJSON());
	}
	
	WPGMZA.MarkerGalleryInput.prototype.toJSON = function()
	{
		var gallery = [];
		
		$(this.element).find("[data-picture-url]").each(function(index, el) {
			gallery.push({
				attachment_id:	$(el).attr("data-attachment-id"),
				url: 			$(el).attr("data-picture-url")
			});
		});
		
		return gallery;
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onDragEnd = function()
	{
		this.update();
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onAttachmentPicked = function(attachment_id, attachment_url)
	{
		this.addPicture({
			attachment_id: attachment_id,
			url: attachment_url
		});
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onAddNewPictureClicked = function(event)
	{
		var self = this;
		
		WPGMZA.openMediaDialog(function(attachment_id, attachment_url) {
			
			self.onAttachmentPicked(attachment_id, attachment_url);
			
		});
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onDeleteItem = function(event)
	{
		$(event.target).closest("[data-picture-url]").remove();
	}
	
});