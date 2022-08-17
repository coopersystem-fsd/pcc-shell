<?php

namespace WPGMZA;

require_once(WPGMZA_PLUGIN_DIR_PATH . 'includes/map-edit-page/class.map-edit-page.php');
require_once(plugin_dir_path(WPGMZA_PRO_FILE) . 'includes/map-edit-page/class.pro-marker-panel.php');

class ProMapEditPage extends MapEditPage
{
	protected $form;
	
	public function __construct($map_id = null)
	{
		global $wpgmza;
		
		MapEditPage::__construct($map_id);
		
		$map = $this->map;

		
		
		// Add-on text
		if($element = $this->document->querySelector("#wpgmza-title-label > small"))
		{
			if (function_exists("wpgmza_register_gold_version"))
				$addon_text = __("including Pro & Gold add-ons","wp-google-maps");
			else
				$addon_text = __("including Pro add-on","wp-google-maps");
			
			$element->appendText($addon_text);
		}
		
		// GDPR privacy policy changes notice
		if($element = $this->document->querySelector("#wpgmza-gdpr-privacy-policy-notice") && property_exists($wpgmza, 'gdprCompliance'))
			$element->import( $wpgmza->gdprCompliance->getPrivacyPolicyNoticeHTML() );
		
		// Tabs
		if($element = $this->document->querySelector("#wpgmaps_tabs > ul"))
		{
			$html = apply_filters("wpgmaps_filter_pro_map_editor_tabs", "");
			$element->import( $html );
		}

		$addOnTabs = apply_filters("wpgmaps_filter_pro_map_editor_tab_content", "");
		if(!empty($addOnTabs)){
			$this->document->querySelector('#wpgmaps_tabs')->import($addOnTabs);
		}

		
		
		// Directions panel
		if($element = $this->document->querySelector('.wpgmza-directions-box-settings-panel'))
		{	
			$directionsBoxSettingsPanel = new DirectionsBoxSettingsPanel($map);
			$element->clear();
			$element->import($directionsBoxSettingsPanel);
		}
		
		// Country restriction
		if($element = $this->document->querySelector('#wpgmza-store-locator-country-restriction-container'))
		{
			$select = new CountrySelect(array(
				'name' => 'wpgmza_store_locator_restrict',
				'value' => !empty($map->wpgmza_store_locator_restrict) ? $map->wpgmza_store_locator_restrict : ''
			));
			
			$select->querySelector('select')->setAttribute('id', 'wpgmza_store_locator_restrict');

			$element->import($select);
		}
		
		// Store locator default address autocomplete
		if(($element = $this->document->querySelector('[name="wpgmza_store_locator_default_address"]')) && !empty($map->wpgmza_store_locator_restrict))
		{
			$options = array(
				'countryRestriction' => $map->wpgmza_store_locator_restrict
			);
			
			$element->setAttribute('data-autocomplete-options', json_encode($options));
		}
		
		// Pro Marker Panel
		$container	= $this->document->querySelector(".wpgmza-marker-panel")->parentNode;
		$panel		= new ProMarkerPanel($this->map->id);
		
		$container->clear();
		$container->import($panel);
		
		// Marker icon picker
		$markerIconPicker = new \WPGMZA\MarkerIconPicker(array(
			'ajaxName' => 'icon'
		));
		
		$this->document->querySelector(".wpgmza-marker-panel .wpgmza-marker-icon-picker-container")->import($markerIconPicker);
		
		// Other marker icon pickers
		$markerIconPickers = array(
			'upload_default_sl_marker'	=> '.wpgmza-store-locator-marker-icon-picker-container',
			'default_marker'		=> '#advanced-settings-marker-icon-picker-container',
			'upload_default_ul_marker'	=> '.wpgmza-user-location-marker-icon-picker-container'
		);
		

		foreach($markerIconPickers as $name => $selector)
		{
			if(!($element = $this->document->querySelector($selector)))
				continue;
			
			$options = array(
				'name' => $name
			);

			if(!empty($map->{$name})){
				$options['value'] = $map->{$name};
			}

			if($name !== 'default_marker'){
				$options['retina_name'] = $name . '_retina';

				/*
				 * This is not the solution I think we should be using. 
				 *
				 * Something is fundamentally wrong with the storage part of this, where it cannot parse out the value, but never the less,
				 * this will do for now
				*/
				$retina_setting = $name . '_retina';
				if(!empty($map->{$retina_setting})){
					$existingValue = $options['value'];
					$options['value'] = array(
						'url' => $existingValue,
						'retina' => true
					);
				}
			}

			

			$picker = new MarkerIconPicker($options);

			$element->setAttribute("value", "on");
			
			$element->import($picker);


		}
		
		// Marker library dialog
		$this->markerLibraryDialog = new MarkerLibraryDialog();
		@$this->document->querySelector("#wpgmza-map-edit-page")->import($this->markerLibraryDialog->html());
		
		// Integration panel
		if($element = $this->document->querySelector("#wpgmza-integration-panel-container"))
		{
			$integrationPanel = new DOMDocument();
			$integrationPanel->loadHTML('<div id="wpgmza-integration-panel"/>');
			$integrationPanel = apply_filters('wpgmza_map_integration_panel', $integrationPanel, $map);

			$element->import($integrationPanel);
		}
		
		// Ratings for Gold
		// TODO: Move this onto Gold using a filter
		if((!defined('WPGMZA_GOLD_VERSION') || version_compare(WPGMZA_GOLD_VERSION, '5.0.0', '<'))
			&&
			$element = $this->document->querySelector('fieldset#wpgmza-marker-ratings')
			)
		{
			$element->remove();
		}
		
		// Marker filtering tab
		$this->markerFilteringTab = new MarkerFilteringTab($map);
		$this->document->querySelector('#wpgmaps_tabs')->import($this->markerFilteringTab);
		
		// Remove basic only stuff
		ProPage::removeUpsells($this->document);
		ProPage::enableProFeatures($this->document);
		
		if($a = $this->document->querySelector("a[href='#advanced-markers']"))
			$a->parentNode->remove();



	}
	
	protected function disableProFeatures()
	{
		// Do nothing!
	}
	
	protected function removeProMarkerFeatures()
	{
		// Do nothing!
	}
	
	protected function populateAdvancedMarkersPanel()
	{
		// Do nothing!
	}
	
	public function onSubmit()
	{
		
		$this->markerFilteringTab->onSubmit();


		if(empty($_POST['directions_enabled'])){
			$_POST['directions_enabled'] = 0;
		}

		if(empty($_POST['directions_fit_bounds_to_route'])){
			$_POST['directions_fit_bounds_to_route'] = 0;
		}

		if(empty($_POST['enable_advanced_custom_fields_integration'])){
			$_POST['enable_advanced_custom_fields_integration'] = 0;
		}

		/* 
		 * Temp retina uncheck patch
		 *
		 * Fields:
		 * - Store locator marker
		 * - User location marker
		 * - Directions markers
		 *
		 * We really need to standardize the marker icon storage, to avoid this in the future, something is just fundamentally wrong here
		*/
		if(empty($_POST['upload_default_sl_marker_retina'])){
			$_POST['upload_default_sl_marker_retina'] = 0;
		}

		if(empty($_POST['upload_default_ul_marker_retina'])){
			$_POST['upload_default_ul_marker_retina'] = 0;
		}

		if(empty($_POST['directions_origin_retina'])){
			$_POST['directions_origin_retina'] = 0;
		}

		if(empty($_POST['directions_destination_retina'])){
			$_POST['directions_destination_retina'] = 0;
		}

		// default icon retina support
		// addeed by Nick - Jan 2021
		
		if (isset($_POST['retina']) && $_POST['retina'] == 'on') {
			
			$_POST['default_marker'] = json_encode(array(
				'url' => $_POST['default_marker'],
				'retina' => true
			));
			unset($_POST['retina']);
		}




		MapEditPage::onSubmit();

		do_action("wpgooglemaps_hook_save_map", $this->map->id);
	}
}

add_filter('wpgmza_create_WPGMZA\\MapEditPage', function($map_id = null) {
	
	return new ProMapEditPage($map_id);
	
});

add_filter('wpgmza_create_WPGMZA\\MarkerPanel', function() {
	
	return new ProMarkerPanel();
	

});