/**
 * @namespace WPGMZA.Integration
 * @module ProGutenberg
 * @requires WPGMZA.Gutenberg
 */

/**
 * Internal block libraries
 */
jQuery(function($) {
	
	if(!window.wp || !wp.i18n || !wp.blocks || !wp.editor || !wp.components)
		return;
	
	const { __ } = wp.i18n;

	const { registerBlockType } = wp.blocks;

	const {
		InspectorControls,
		BlockControls
	} = wp.editor;

	const {
		Dashicon,
		Toolbar,
		Button,
		Tooltip,
		PanelBody,
		TextareaControl,
		TextControl,
		RichText,
		SelectControl,
		RangeControl
	} = wp.components;
	
	WPGMZA.Integration.ProGutenberg = function()
	{
		WPGMZA.Integration.Gutenberg.call(this);
	}
	
	WPGMZA.Integration.ProGutenberg.prototype = Object.create(WPGMZA.Integration.Gutenberg.prototype);
	WPGMZA.Integration.ProGutenberg.prototype.constructor = WPGMZA.Integration.ProGutenberg;
	
	WPGMZA.Integration.Gutenberg.getConstructor = function()
	{
		return WPGMZA.Integration.ProGutenberg;
	}
	
	WPGMZA.Integration.ProGutenberg.prototype.getMapSelectOptions = function()
	{
		var result = [];
		
		WPGMZA.gutenbergData.maps.forEach(function(el) {
			
			result.push({
				key: el.id,
				value: el.id,
				label: el.map_title + " (" + el.id + ")"
			});
			
		});
		
		return result;
	}
	
	WPGMZA.Integration.ProGutenberg.prototype.updateMarkerSelectOptions = function(props)
	{
		var select 		= $("select[name='marker']");
		var mashup_ids 	= $("select[name='mashup_ids']").val();
		var none 		= $("<option value='none'></option>");
		var request 	= {
			fields: ["id", "address", "title"],
			filter: {
				map_id: $("select[name='map_id']").val()
			}
		};
		
		none.text(__("None"));
		
		if(mashup_ids)
			request.filter.mashup_ids = mashup_ids;
		
		select.prop("disabled", true);
		
		WPGMZA.restAPI.call("/markers/", {
			success: function(response, status, xhr) {
				
				select.html("");
				select.append(none);
				
				response.forEach(function(data) {
					
					var option = $("<option/>");
					
					option.val(data.id);
					option.prop("value", data.id);
					option.text((data.title.length ? data.title : data.address) + " (" + data.id + ")");
					
					select.append(option);
					
				});
				
				select.prop("disabled", false);
				
				if(props.attributes.marker)
					select.val(props.attributes.marker);
				
			},
			data: request
		});
	}
	
	WPGMZA.Integration.ProGutenberg.prototype.updateCategorySelectOptions = function(props)
	{
		var select		= $("select[name='cat']");
		var none 		= $("<option value='none'></option>");
		var request		= {
			filter: {
				map_id: $("select[name='map_id']").val()
			}
		};
		
		none.text(__("None"));
		
		select.prop("disabled", true);
		
		function addNodeChildren(node, depth)
		{
			if(!depth)
				depth = 0;
			
			if(!node.children)
				return;
			
			node.children.forEach(function(child) {
				
				var prefix = "";
				var option = $("<option/>")
				
				for(var i = 0; i < depth; i++)
					prefix += "&nbsp;&nbsp;&nbsp;&nbsp;";
				
				option.val(child.id);
				option.prop(child.id);
				option.html(prefix + child.name + " (" + child.id + ")");
				
				select.append(option);
				
				addNodeChildren(child, depth + 1);
				
			});
		}
		
		WPGMZA.restAPI.call("/categories/", {
			success: function(response, status, xhr) {
				
				select.html("");
				select.append(none);
				
				addNodeChildren(response);
				
				select.prop("disabled", false);
				
				if(props.attributes.cat)
					select.val(props.attributes.cat);
				
			},
			data: request
		});
	}
	
	WPGMZA.Integration.ProGutenberg.prototype.getBlockInspectorControls = function(props)
	{
		var self = this;
		
		const onChangeMap = value => {
			props.setAttributes({id: value});
		};
		
		const onChangeMashupIDs = value => {
			props.setAttributes({mashup_ids: value});
		};
		
		const onResetMashupIDs = value => {
			$("select[name='mashup_ids']").val(null);
			props.setAttributes({mashup_ids: []});
		};
		
		const onEditMap = event => {
			
			var select = $("select[name='map_id']");
			var map_id = select.val();
			
			window.open(WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu&action=edit&map_id=" + map_id);
			
			event.preventDefault();
			return false;
			
		};
		
		const onChangeFocusedMarker = value => {
			props.setAttributes({marker: value});
		};
		
		const onChangeOverrideZoom = value => {
			props.setAttributes({zoom: value});
		};
		
		const onResetOverrideZoom = event => {
			props.setAttributes({zoom: ""});
		};
		
		const onChangeInitialCategory = value => {
			props.setAttributes({cat: value});
		};
		
		let selectedMapID = "1";
		
		if(props.attributes.id)
			selectedMapID = props.attributes.id;
		else if(WPGMZA.gutenbergData.maps.length)
			selectedMapID = WPGMZA.gutenbergData.maps[0].id;
		
		setTimeout(function() {
			self.updateMarkerSelectOptions(props);
			self.updateCategorySelectOptions(props);
		}, 100);
		
		return (
			<InspectorControls key="inspector">
				<PanelBody title={ __( 'Map Settings' ) } >
				
					<SelectControl
						name="map_id"
						label={__("Map")}
						value={selectedMapID}
						options={this.getMapSelectOptions()}
						onChange={onChangeMap}
						/>
						
					<p className="map-block-gutenberg-button-container">
						<a href={WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu"} 
							onClick={onEditMap}
							target="_blank" 
							className="button button-primary">
							<i className="fa fa-pencil-square-o" aria-hidden="true"></i>
							{__('Go to Map Editor')}
						</a>
					</p>
						
					<SelectControl
						name="mashup_ids"
						label={__("Mashup IDs")}
						value={props.attributes.mashup_ids || []}
						options={this.getMapSelectOptions()}
						multiple
						onChange={onChangeMashupIDs}
						/>
					
					<p className="map-block-gutenberg-button-container">
						<button className="button button-primary" onClick={onResetMashupIDs}>
							<i className="fa fa-times" aria-hidden="true"></i>
							{__('Reset Mashup IDs')}
						</button>
					</p>
					
					<SelectControl
						name="marker"
						label={__("Focused Marker")}
						value="none"
						options={[
							{
								key: "none",
								value: "none",
								label: __("None")
							}
						]}
						onChange={onChangeFocusedMarker}
						/>
					
					<RangeControl
						name="zoom"
						label={__("Override Zoom")}
						onChange={onChangeOverrideZoom}
						min={1}
						max={21}
						step={1}
						value={parseInt(props.attributes.zoom)}
						/>
						
					<p className="map-block-gutenberg-button-container">
						<button className="button button-primary" onClick={onResetOverrideZoom}>
							<i className="fa fa-times" aria-hidden="true"></i>
							{__('Reset Override Zoom')}
						</button>
					</p>
					
					<SelectControl
						name="cat"
						label={__("Initial Category")}
						value="none"
						options={[
							{
								key: "none",
								value: "none",
								label: __("None")
							}
						]}
						onChange={onChangeInitialCategory}
						/>
					
					<p className="map-block-gutenberg-button-container">
						<a href="https://www.wpgmaps.com/documentation/creating-your-first-map/"
							target="_blank"
							className="button button-primary">
							<i className="fa fa-book" aria-hidden="true"></i>
							{__('View Documentation')}
						</a>
					</p>
					
				</PanelBody>
			</InspectorControls>
		);
	}
	
	WPGMZA.Integration.ProGutenberg.prototype.getBlockAttributes = function(props)
	{
		return {
			"id": {
				type: "string"
			},
			"mashup_ids": {
				type: "array"
			},
			"marker": {
				type: "string"
			},
			"zoom": {
				type: "string"
			},
			"cat": {
				type: "string"
			}
		}
	}
	
	WPGMZA.Integration.ProGutenberg.prototype.getBlockDefinition = function(props)
	{
		var definition = WPGMZA.Integration.Gutenberg.prototype.getBlockDefinition.call(this, props);
		
		return definition;
	}
	
	WPGMZA.integrationModules.gutenberg = WPGMZA.Integration.Gutenberg.createInstance();
	
});