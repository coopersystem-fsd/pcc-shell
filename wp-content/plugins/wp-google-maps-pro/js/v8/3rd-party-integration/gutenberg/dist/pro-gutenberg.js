"use strict";

/**
 * @namespace WPGMZA.Integration
 * @module ProGutenberg
 * @requires WPGMZA.Gutenberg
 */

/**
 * Internal block libraries
 */
jQuery(function ($) {

	if (!window.wp || !wp.i18n || !wp.blocks || !wp.editor || !wp.components) return;

	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var _wp$editor = wp.editor,
	    InspectorControls = _wp$editor.InspectorControls,
	    BlockControls = _wp$editor.BlockControls;
	var _wp$components = wp.components,
	    Dashicon = _wp$components.Dashicon,
	    Toolbar = _wp$components.Toolbar,
	    Button = _wp$components.Button,
	    Tooltip = _wp$components.Tooltip,
	    PanelBody = _wp$components.PanelBody,
	    TextareaControl = _wp$components.TextareaControl,
	    TextControl = _wp$components.TextControl,
	    RichText = _wp$components.RichText,
	    SelectControl = _wp$components.SelectControl,
	    RangeControl = _wp$components.RangeControl;


	WPGMZA.Integration.ProGutenberg = function () {
		WPGMZA.Integration.Gutenberg.call(this);
	};

	WPGMZA.Integration.ProGutenberg.prototype = Object.create(WPGMZA.Integration.Gutenberg.prototype);
	WPGMZA.Integration.ProGutenberg.prototype.constructor = WPGMZA.Integration.ProGutenberg;

	WPGMZA.Integration.Gutenberg.getConstructor = function () {
		return WPGMZA.Integration.ProGutenberg;
	};

	WPGMZA.Integration.ProGutenberg.prototype.getMapSelectOptions = function () {
		var result = [];

		WPGMZA.gutenbergData.maps.forEach(function (el) {

			result.push({
				key: el.id,
				value: el.id,
				label: el.map_title + " (" + el.id + ")"
			});
		});

		return result;
	};

	WPGMZA.Integration.ProGutenberg.prototype.updateMarkerSelectOptions = function (props) {
		var select = $("select[name='marker']");
		var mashup_ids = $("select[name='mashup_ids']").val();
		var none = $("<option value='none'></option>");
		var request = {
			fields: ["id", "address", "title"],
			filter: {
				map_id: $("select[name='map_id']").val()
			}
		};

		none.text(__("None"));

		if (mashup_ids) request.filter.mashup_ids = mashup_ids;

		select.prop("disabled", true);

		WPGMZA.restAPI.call("/markers/", {
			success: function success(response, status, xhr) {

				select.html("");
				select.append(none);

				response.forEach(function (data) {

					var option = $("<option/>");

					option.val(data.id);
					option.prop("value", data.id);
					option.text((data.title.length ? data.title : data.address) + " (" + data.id + ")");

					select.append(option);
				});

				select.prop("disabled", false);

				if (props.attributes.marker) select.val(props.attributes.marker);
			},
			data: request
		});
	};

	WPGMZA.Integration.ProGutenberg.prototype.updateCategorySelectOptions = function (props) {
		var select = $("select[name='cat']");
		var none = $("<option value='none'></option>");
		var request = {
			filter: {
				map_id: $("select[name='map_id']").val()
			}
		};

		none.text(__("None"));

		select.prop("disabled", true);

		function addNodeChildren(node, depth) {
			if (!depth) depth = 0;

			if (!node.children) return;

			node.children.forEach(function (child) {

				var prefix = "";
				var option = $("<option/>");

				for (var i = 0; i < depth; i++) {
					prefix += "&nbsp;&nbsp;&nbsp;&nbsp;";
				}option.val(child.id);
				option.prop(child.id);
				option.html(prefix + child.name + " (" + child.id + ")");

				select.append(option);

				addNodeChildren(child, depth + 1);
			});
		}

		WPGMZA.restAPI.call("/categories/", {
			success: function success(response, status, xhr) {

				select.html("");
				select.append(none);

				addNodeChildren(response);

				select.prop("disabled", false);

				if (props.attributes.cat) select.val(props.attributes.cat);
			},
			data: request
		});
	};

	WPGMZA.Integration.ProGutenberg.prototype.getBlockInspectorControls = function (props) {
		var self = this;

		var onChangeMap = function onChangeMap(value) {
			props.setAttributes({ id: value });
		};

		var onChangeMashupIDs = function onChangeMashupIDs(value) {
			props.setAttributes({ mashup_ids: value });
		};

		var onResetMashupIDs = function onResetMashupIDs(value) {
			$("select[name='mashup_ids']").val(null);
			props.setAttributes({ mashup_ids: [] });
		};

		var onEditMap = function onEditMap(event) {

			var select = $("select[name='map_id']");
			var map_id = select.val();

			window.open(WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu&action=edit&map_id=" + map_id);

			event.preventDefault();
			return false;
		};

		var onChangeFocusedMarker = function onChangeFocusedMarker(value) {
			props.setAttributes({ marker: value });
		};

		var onChangeOverrideZoom = function onChangeOverrideZoom(value) {
			props.setAttributes({ zoom: value });
		};

		var onResetOverrideZoom = function onResetOverrideZoom(event) {
			props.setAttributes({ zoom: "" });
		};

		var onChangeInitialCategory = function onChangeInitialCategory(value) {
			props.setAttributes({ cat: value });
		};

		var selectedMapID = "1";

		if (props.attributes.id) selectedMapID = props.attributes.id;else if (WPGMZA.gutenbergData.maps.length) selectedMapID = WPGMZA.gutenbergData.maps[0].id;

		setTimeout(function () {
			self.updateMarkerSelectOptions(props);
			self.updateCategorySelectOptions(props);
		}, 100);

		return React.createElement(
			InspectorControls,
			{ key: "inspector" },
			React.createElement(
				PanelBody,
				{ title: __('Map Settings') },
				React.createElement(SelectControl, {
					name: "map_id",
					label: __("Map"),
					value: selectedMapID,
					options: this.getMapSelectOptions(),
					onChange: onChangeMap
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"a",
						{ href: WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu",
							onClick: onEditMap,
							target: "_blank",
							className: "button button-primary" },
						React.createElement("i", { className: "fa fa-pencil-square-o", "aria-hidden": "true" }),
						__('Go to Map Editor')
					)
				),
				React.createElement(SelectControl, {
					name: "mashup_ids",
					label: __("Mashup IDs"),
					value: props.attributes.mashup_ids || [],
					options: this.getMapSelectOptions(),
					multiple: true,
					onChange: onChangeMashupIDs
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"button",
						{ className: "button button-primary", onClick: onResetMashupIDs },
						React.createElement("i", { className: "fa fa-times", "aria-hidden": "true" }),
						__('Reset Mashup IDs')
					)
				),
				React.createElement(SelectControl, {
					name: "marker",
					label: __("Focused Marker"),
					value: "none",
					options: [{
						key: "none",
						value: "none",
						label: __("None")
					}],
					onChange: onChangeFocusedMarker
				}),
				React.createElement(RangeControl, {
					name: "zoom",
					label: __("Override Zoom"),
					onChange: onChangeOverrideZoom,
					min: 1,
					max: 21,
					step: 1,
					value: parseInt(props.attributes.zoom)
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"button",
						{ className: "button button-primary", onClick: onResetOverrideZoom },
						React.createElement("i", { className: "fa fa-times", "aria-hidden": "true" }),
						__('Reset Override Zoom')
					)
				),
				React.createElement(SelectControl, {
					name: "cat",
					label: __("Initial Category"),
					value: "none",
					options: [{
						key: "none",
						value: "none",
						label: __("None")
					}],
					onChange: onChangeInitialCategory
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"a",
						{ href: "https://www.wpgmaps.com/documentation/creating-your-first-map/",
							target: "_blank",
							className: "button button-primary" },
						React.createElement("i", { className: "fa fa-book", "aria-hidden": "true" }),
						__('View Documentation')
					)
				)
			)
		);
	};

	WPGMZA.Integration.ProGutenberg.prototype.getBlockAttributes = function (props) {
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
		};
	};

	WPGMZA.Integration.ProGutenberg.prototype.getBlockDefinition = function (props) {
		var definition = WPGMZA.Integration.Gutenberg.prototype.getBlockDefinition.call(this, props);

		return definition;
	};

	WPGMZA.integrationModules.gutenberg = WPGMZA.Integration.Gutenberg.createInstance();
});