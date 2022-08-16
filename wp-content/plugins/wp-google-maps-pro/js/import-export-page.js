jQuery(function($) {
	
	var prevRawResponse, prevRawLog;
	
	jQuery(document).ready(function () {
		
		$(document.body).on("click", "#wpgmaps_import_bulk_jpeg_button", function(event) {
			
			event.preventDefault();
			$(event.currentTarget).prop("disabled", true);
			
			var files = $("input[name='bulk_jpeg_files']")[0].files;
			var i = 0;
			
			function finish()
			{
				$(event.currentTarget).prop("disabled", false);
			}
			
			function status(text)
			{
				$("#bulk_jpeg_status").html(text);
			}
			
			function uploadNextJpeg()
			{
				if(i >= files.length)
				{
					status(WPGMZA.localized_strings.upload_complete);
					finish();
					return;
				}
				
				status(WPGMZA.localized_strings.uploading_file + " " + (i+1) + " / " + files.length);
				
				var file = files[i];
				var url = WPGMZA.mediaRestUrl;
				var formData = new FormData();
				var now = new Date();
				var title = WPGMZA.localized_strings.bulk_jpeg_media_title + " (" + now.toString() + ")";
				
				formData.append("file", file);
				formData.append("title", title);
				formData.append("caption", title);
				
				// TODO: Add additional nonce
				$.ajax({
					url: 			WPGMZA.mediaRestUrl,
					method:			"POST",
					contentType:	false,
					processData:	false,
					beforeSend: function(xhr) {
						xhr.setRequestHeader("X-WP-Nonce", WPGMZA.restnonce);
					},
					data:			formData
				}).success(function(response) {
					
					uploadMarker(response);
					
				}).error(function(response) {
					
					alert(response);
					
					i++;
					uploadNextJpeg();
					
				});
			}
			
			function uploadMarker(media)
			{
				var url = media.guid.rendered;
				
				var gallery = [
					{
						attachment_id:	media.id,
						url:			url,
						thumbnail:		media.media_details.sizes.thumbnail.source_url
					}
				];
				
				var data = {
					map_id:		$("#import_from_bulk_jpeg select[name='map_id']").val(),
					gallery: 	gallery,
					approved:	1
				};
				
				WPGMZA.LatLng.fromJpeg(url, function(result) {
					
					// TOOD: Handle failure
					
					if(result)
					{
						data.lat = result.lat;
						data.lng = result.lng;
					}
					
					WPGMZA.restAPI.call("/markers/", {
						method: "POST",
						data: data,
						success: function(data, status, xhr) {
							
							i++;
							uploadNextJpeg();
							
						}
					})
					
				});
				
				
			}
			
			uploadNextJpeg();
			
		});
		
		$('.import_data_type').change(function(){
			
			var id = "import_from_" + $(this).val().toLowerCase();
			var el = $("#" + id);

			
			$(".wpgmza-import-upload-panel").hide();
			$(el).show();
			
			if($(this).attr("data-wpgmza-integration-class"))
				$("#import_from_integration").show();
			
		});
		$('#wpgmaps_import_file').change(function () {
			if ($(this)[0].files.length > 0) {
				$('#wpgmaps_import_file_name').text($(this)[0].files[0].name);
			} else {
				$('#wpgmaps_import_file_name').html('');
			}
		});

		$('#wpgmaps_import_upload_button').click(function (e) {
			if ($('#wpgmaps_import_file')[0].files.length < 1) {
				alert(WPGMZA.localized_strings.please_select_a_file_to_upload);
				return;
			}

			$('#wpgmaps_import_file,#wpgmaps_import_upload_button').prop('disabled', true);
			$('#wpgmaps_import_file + label,#wpgmaps_import_upload_button').css('opacity', '0.5');
			$('#wpgmaps_import_upload_spinner').addClass('is-active');

			var form_data = new FormData();
			form_data.append('action', 'wpgmza_import_upload');
			form_data.append('wpgmaps_security', WPGMZA.import_security_nonce);
			form_data.append('wpgmaps_import_file', $('#wpgmaps_import_file')[0].files[0]);

			wp.ajax.send({
				data: form_data,
				processData: false,
				contentType: false,
				cache: false,
				success: function (data) {
					if (typeof data !== 'undefined' && data.hasOwnProperty('id') && data.hasOwnProperty('title')) {
						$('#wpgmap_import_file_list_table tbody').prepend('<tr id="import-list-item-' + data.id + '"><td><strong><span class="import_file_title" style="font-size:larger;">' + data.title + '</span></strong><br>' +
							'<a href="javascript:void(0);" class="import_import" data-import-id="' + data.id + '">' + WPGMZA.localized_strings.import_reservedwordsfix + '</a>' +
							' | <a href="javascript:void(0);" class="import_delete" data-import-id="' + data.id + '">' + WPGMZA.localized_strings.delete_reservedwordsfix + '</a></td></tr>');
						wpgmaps_import_setup_file_links(data.id);
						$('#wpgmaps_import_file_list').show();
						$('#import-list-item-' + data.id + ' .import_import').click();
					}
				},
				error: function (data) {
					if (typeof data !== 'undefined') {
						wpgmaps_import_add_notice(data, 'error');
					}
				}
			}).always(function () {
				$('#wpgmaps_import_file_name').html('');
				$('#wpgmaps_import_file').replaceWith($('#wpgmaps_import_file').val('').clone(true));
				$('#wpgmaps_import_file,#wpgmaps_import_upload_button').prop('disabled', false);
				$('#wpgmaps_import_file + label,#wpgmaps_import_upload_button').css('opacity', '1.0');
				$('#wpgmaps_import_upload_spinner').removeClass('is-active');
			});
		});

		function wpgmaps_import_setup_file_links(id = '') {
			var del_select = '.import_delete';
			var imp_select = '.import_import';
			if (parseInt(id) > 1){
				del_select = '#import-list-item-' + id + ' ' + del_select;
				imp_select = '#import-list-item-' + id + ' ' + imp_select;
			}
			$(imp_select).click(function () {
				$('#import_files').hide();
				$('#import_loader_text').html('<br>Loading import options...');
				$('#import_loader').show();
				wp.ajax.send({
					data: {
						action: 'wpgmza_import_file_options',
						wpgmaps_security: WPGMZA.import_security_nonce,
						import_id: $(this).attr('data-import-id')
					},
					success: function (data) {
						if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
							$('#import_loader').hide();
							$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();">' + WPGMZA.localized_strings.back_to_import_data + '</a></div>' + data.options_html).show();
						}
					},
					error: function (data) {
						if (typeof data !== 'undefined') {
							wpgmaps_import_add_notice(data, 'error');
						}
						$('#import_loader').hide();
						$('#import_options').html('').hide();
						$('#import_files').show();
					}
				});
			});
			$(del_select).click(function () {
				if (confirm(WPGMZA.localized_strings.are_you_sure_you_wish_to_delete_this_file + $(this).parent().find('.import_file_title').text())) {
					wp.ajax.send({
						data: {
							action: 'wpgmza_import_delete',
							wpgmaps_security: WPGMZA.import_security_nonce,
							import_id: $(this).attr('data-import-id')
						},
						success: function (data) {
							if (typeof data !== 'undefined' && data.hasOwnProperty('id')) {
								$('#import-list-item-' + data.id).remove();
								wpgmaps_import_add_notice('<p>' + WPGMZA.localized_strings.file_deleted + '</p>');
							}
						},
						error: function (data) {
							if (typeof data !== 'undefined') {
								wpgmaps_import_add_notice(data, 'error');
							}
						}
					});
				}
			});
		}

		wpgmaps_import_setup_file_links();

		$('#wpgmaps_import_url_button').click(function () {
			var import_url = $('#wpgmaps_import_url').val();

			if (import_url.length < 1) {
				alert(WPGMZA.localized_strings.please_enter_a_url_to_import_from);
				return;
			}
			
			$('#import_files').hide();
			$('#import_options').html('<div style="text-align:center;"><div class="spinner is-active" style="float:none;"></div></div>').show();
			wp.ajax.send({
				data: {
					action: 'wpgmza_import_file_options',
					wpgmaps_security: WPGMZA.import_security_nonce,
					import_url: import_url
				},
				success: function (data) {
					if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
						$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();">' + WPGMZA.localized_strings.back_to_import_data + '</a></div>' + data.options_html);
					}
				},
				error: function (data) {
					if (typeof data !== 'undefined') {
						wpgmaps_import_add_notice(data, 'error');
					}
					$('#import_options').html('').hide();
					$('#import_files').show();
				}
			});
		});

		//Airtables Integration Snippet
		$('#wpgmaps_import_airtable_button').click(function () {
			var import_url = $('#wpgmaps_import_airtable_url').val();
			var api_key = $('#wpgmaps_import_airtable_api').val();

			if (import_url.length < 1) {
				alert(WPGMZA.localized_strings.please_enter_a_url_to_import_from);
				return;
			}

			if (api_key.length < 1) {
				alert(WPGMZA.localized_strings.please_enter_a_url_to_import_from);
				return;
			}

			$('#import_files').hide();
			$('#import_options').html('<div style="text-align:center;"><div class="spinner is-active" style="float:none;"></div></div>').show();
			wp.ajax.send({
				data: {
					action: 'wpgmza_import_file_options',
					wpgmaps_security: WPGMZA.import_security_nonce,
					import_url: import_url,
					import_airtable_api_key: api_key
				},
				success: function (data) {
					if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
						$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();">' + WPGMZA.localized_strings.back_to_import_data + '</a></div>' + data.options_html);
					}
				},
				error: function (data) {
					if (typeof data !== 'undefined') {
						wpgmaps_import_add_notice(data, 'error');
					}
					$('#import_options').html('').hide();
					$('#import_files').show();
				}
			});

		});
		
		$("#wpgmaps_import_integration_button").click(function() {
			
			$('#import_files').hide();
			$('#import_options').html('<div style="text-align:center;"><div class="spinner is-active" style="float:none;"></div></div>').show();
			
			wp.ajax.send({
				data: {
					action: 'wpgmza_import_integration_options',
					wpgmaps_security: WPGMZA.import_security_nonce,
					import_class: $("[data-wpgmza-integration-class]:checked").attr("data-wpgmza-integration-class")
				},
				success: function (data) {
					if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
						$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();">' + WPGMZA.localized_strings.back_to_import_data + '</a></div>' + data.options_html);
					}
				},
				error: function(data) {
					if (typeof data !== 'undefined') {
						wpgmaps_import_add_notice(data, 'error');
					}
					$('#import_options').html('').hide();
					$('#import_files').show();
				}
			})
			
		});
		
		$(document.body).on("click", "#import-integration", function(event) {
			
			$('#import_loader_text').html('<br/>' + WPGMZA.localized_strings.importing_please_wait + '<br/><progress id="wpgmza-import-csv-progress"/>');
			
			$('#import_loader').show();
			$('#import_options').hide();
			
			var importClass = 
			
			wp.ajax.send({
				data: {
					action: "wpgmza_import_integration",
					import_class: $("[data-wpgmza-integration-class]:checked").attr("data-wpgmza-integration-class"),
					map_id: $("#map-select-container select").val(),
					replace_map_data: $("#replace-map-data").prop("checked"),
					wpgmaps_security: WPGMZA.import_security_nonce
				},
				success: function(data) {
					
					$('#import_loader').hide();
					
					$('#import_options').html('');
					$('#import_files').show();
					
					wpgmaps_import_add_notice(WPGMZA.localized_strings.import_completed, 'success');
					
				},
				error: function (data) {
					
					var string = (typeof data == "string" ? data : data.statusText);
					
					if (typeof data !== 'undefined') {
						wpgmaps_import_add_notice(data, 'error');
					}
					
					$('#import_loader').hide();
					$('#import_options').show();
				}
			})
			
		});
		
		$(document.body).on("click", ".wpgmza-expand-import-log", function(event) {
			
			var a = event.currentTarget;
			var log = $(a).closest("p").next(".wpgmza-import-log");
			
			var chevron = $(a).find(".fa-chevron-down, .fa-chevron-up");
			
			if(chevron.hasClass("fa-chevron-down"))
			{
				log.show();
				chevron.removeClass("fa-chevron-down");
				chevron.addClass("fa-chevron-up");
			}
			else
			{
				log.hide();
				chevron.addClass("fa-chevron-down");
				chevron.removeClass("fa-chevron-up");
			}
			
		});
		
		$(document.body).on("click", ".wpgmza-copy-import-log", function(event) {
			
			var el = $(event.target).closest(".wpgmza-import-log");
			var text = el[0].rawLogData;
			
			var $temp = $("<textarea>");
			$("body").append($temp);
			$temp.val(text).select();
			document.execCommand("copy");
			$temp.remove();
			
			alert("Copied to clipboard");

		});
		
		$(document.body).on("click", ".wpgmza-download-import-log", function(event) {
			
			var el = $(event.target).closest(".wpgmza-import-log");
			var text = el[0].rawLogData;
			var a = document.createElement("a");
			var blob = new Blob([text], {type: "octet/stream"});
			var url = URL.createObjectURL(blob);
			
			document.body.appendChild(a);
			
			a.style = "display: none;";
			a.href = url;
			a.download = "import-" + WPGMZA.guid() + ".log";
			
			a.click();
			
			URL.revokeObjectURL(url);

		});
		
		function wpgmaps_import_add_notice( notice, type = 'success', noclear ) {
			if(!noclear)
				$('.notice').remove();
			
			var response = notice;
			
			if(typeof notice == "object")
			{
				if(notice.responseText)
					notice = "<p>" + notice.responseText + "</p>";
				else if(notice.statusText)
					notice = "<p>" + notice.statusText + "</p>";
				else if(notice.message)
				{
					var html = "<p>" + notice.message + "</p>";
					var logButtons = "<span class='wpgmza-import-log-buttons'>" + 
						"<button class='button button-secondary wpgmza-copy-import-log' title='Copy to Clipboard'><i class='fa fa-clone' aria-hidden='true'></i></button>" +
						"<button class='button button-secondary wpgmza-download-import-log' title='Download'><i class='fa fa-download' aria-hidden='true'></i></button>" +
					"</span>";
					
					if(notice.response)
					{
						html += "<p>";
						html += '<a class="wpgmza-expand-import-log" href="javascript: ;">View Response <i class="fa fa-chevron-down" aria-hidden="true"></i></a>';
						html += "</p>";
						
						html += "<p class='wpgmza-import-log wpgmza-import-response-log'>";
						html += logButtons;
						html += "<span class='wpgmza-log-contents'>" + notice.response + "</span>";
						html += "</p>";
						
						prevRawResponse = notice.response;
					}
					
					html += "<p>";
					html += '<a class="wpgmza-expand-import-log" href="javascript: ;">View Log <i class="fa fa-chevron-down" aria-hidden="true"></i></a>';
					html += "</p>";
					
					html += "<p class='wpgmza-import-log wpgmza-import-log-log'>";
					html += logButtons;
					html += "<span class='wpgmza-log-contents'>" + notice.log + "</span>";
					html += "</p>";
					
					prevRawLog = notice.log;
					
					notice = html;
				}
				else
				{
					notice = "<p>Unknown error - Status " + notice.status + "</p>";
				}
			}

			var notice = '<div class="notice notice-' + type + ' is-dismissible">' + notice + '</div>';
			
			$('#wpgmaps_tabs').before(notice);
			
			if(response.log)
				$(".wpgmza-import-log-log")[0].rawLogData = response.log.replace(/<br(\/?)>/g, "\r\n");
			
			if(response.response)
				$(".wpgmza-import-response-log")[0].rawLogData = response.rawResponse;
			
			$(notice).append('<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>');
			$(notice).find(".notice-dismiss").on("click", function() {
				$(notice).fadeTo(100, 0, function() {
					$(notice).slideUp(100, function() {
						$(notice).remove();
					});
				});
			});
		}
		window.wpgmaps_import_add_notice = wpgmaps_import_add_notice;
		
		function wpgmaps_import_setup_schedule_links(id = '') {
			var del_select = '.import_schedule_delete';
			var edt_select = '.import_schedule_edit';
			
			if (id.length > 1){
				del_select = '#import-schedule-list-item-' + id + ' ' + del_select;
				edt_select = '#import-schedule-list-item-' + id + ' ' + edt_select;
			}
			
			$(edt_select).click(function () {
				$('a[href="#import-tab"]').click();
				$('#import_files').hide();
				$('#import_loader_text').html(WPGMZA.localized_strings.loading_import_options);
				$('#import_loader').show();
				wp.ajax.send({
					data: {
						action: 'wpgmza_import_file_options',
						wpgmaps_security: WPGMZA.import_security_nonce,
						schedule_id: $(this).attr('data-schedule-id'),
					},
					success: function (data) {
						if (typeof data !== 'undefined' && data.hasOwnProperty('options_html')) {
							$('#import_loader').hide();
							$('#import_options').html('<div style="margin:5px 0;"><a href="javascript:void(0);" onclick="jQuery(\'#import_options\').html(\'\').hide();jQuery(\'#import_files\').show();">' + WPGMZA.localized_strings.back_to_import_data + '</a></div>' + data.options_html).show();
						}
					},
					error: function (data) {
						if (typeof data !== 'undefined') {
							wpgmaps_import_add_notice(data, 'error');
						}
						$('#import_loader').hide();
						$('#import_options').html('').hide();
						$('#import_files').show();
					}
				});
			});
			$(del_select).click(function () {
				if (confirm(WPGMZA.localized_strings.are_you_sure_you_wish_to_delete_this_scheduled_import + $(this).parent().find('.import_schedule_title').text())) {
					wp.ajax.send({
						data: {
							action: 'wpgmza_import_delete_schedule',
							wpgmaps_security: WPGMZA.import_security_nonce,
							schedule_id: $(this).attr('data-schedule-id')
						},
						success: function (data) {
							if (typeof data !== 'undefined' && data.hasOwnProperty('schedule_id')) {
								$('#import-schedule-list-item-' + data.schedule_id).remove();
								wpgmaps_import_add_notice('<p>Scheduled Import Deleted</p>');
							}
						},
						error: function (data) {
							if (typeof data !== 'undefined') {
								wpgmaps_import_add_notice(data, 'error');
							}
						}
					});
				}
			});
			
			$(".import_schedule_view_log").on("click", function(event) {
				
				var schedule_id = $(event.target).closest("tr").attr("id");
				var url = window.location.href + "&action=view-import-log&schedule_id=" + schedule_id;
				
				window.open(url);
				
			});
			
			$(".import_schedule_view_response").on("click", function(event) {
				
				var schedule_id = $(event.target).closest("tr").attr("id");
				var url = window.location.href + "&action=view-import-response&schedule_id=" + schedule_id;
				
				window.open(url);
				
			});
		}
		window.wpgmaps_import_setup_schedule_links = wpgmaps_import_setup_schedule_links;

		wpgmaps_import_setup_schedule_links();
		
		$('#maps_export_select_all').click(function(){
			$('.maps_export').prop('checked',true);
		});
		$('#maps_export_select_none').click(function(){
			$('.maps_export').prop('checked',false);
		});
		$('#export-json').click(function(){
			var download_url = '?page=wp-google-maps-menu-advanced&action=export_json';
			var maps_check = $('.maps_export:checked');
			var map_ids = [];
			if (maps_check.length < 1){
				alert(WPGMZA.localized_strings.please_select_at_least_one_map_to_export);
				return;
			}
			maps_check.each(function(){
				map_ids.push($(this).val());
			});
			if (map_ids.length < $('.maps_export').length){
				download_url += '&maps=' + map_ids.join(',');
			}
			$('.map_data_export').each(function(){
				if ($(this).prop('checked')){
					download_url += '&' + $(this).attr('id').replace('_export', '');
				}
			});
			window.open(download_url + '&export_nonce=' + WPGMZA.export_security_nonce, '_blank');
		});
		
		
	});
	
	$(document.body).on("change", "#keep_map_id", function(event) {
		if($(this).prop("checked"))
			$("#apply_import").prop("checked", false);
	});
	
	$(document.body).on("change", "#apply_import", function(event) {
		if($(this).prop("checked"))
			$("#keep_map_id").prop("checked", false);
	});
	
});