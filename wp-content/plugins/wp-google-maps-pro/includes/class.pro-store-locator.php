<?php

namespace WPGMZA;

if(!ProPlugin::assertClassExists("WPGMZA\\StoreLocator"))
	return;

class ProStoreLocator extends StoreLocator
{
	const SEARCH_AREA_RADIAL	= "radial";
	const SEARCH_AREA_AUTO		= "auto";
	
	public function __construct(Map $map)
	{
		global $wpgmza;
		
		StoreLocator::__construct($map);
		
		$fragment = new DOMDocument();
		$fragment->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/pro-store-locator.html.php');
		
		$document = $this->document;
		
		$useModernStyle = 
			(!empty($map->store_locator_style) && 
				$map->store_locator_style == 'modern' && 
				$wpgmza->isModernComponentStyleAllowed())
			||
			(!empty($wpgmza->settings->user_interface_style) && 
				$wpgmza->settings->user_interface_style == 'modern')
			;
		
		// Reset button
		$container = $fragment->querySelector("div.wpgmza-reset");
		
		if($container)
		{
			$before = $document->querySelector(".wpgmza-no-results");
			$container = $document->importNode($container, true);
			
			$before->parentNode->insertBefore($container, $before);
		}
		
		// Keyword search
		if($this->keywordSearchEnabled)
		{
			$container = $fragment->querySelector("div.wpgmza-keywords");
			$before = $document->querySelector('.wpgmza-radius-container');
			
			if($container && $before)
			{
				$container = $document->importNode($container, true);
				$container->populate($this);
				
				$before->parentNode->insertBefore($container, $before);
			}
			else
			{
				if(!$container)
					trigger_error("No keyword HTML fragment found", E_USER_WARNING);
				
				if(!$before)
					trigger_error("Failed to find insertion point for keyword filter container");
			}
		}
		
		// Category filter widget
		if($this->categorySearchEnabled)
		{
			// Grab the widget first
			if($useModernStyle)
			{
				// The modern store locator category filter needs checkboxes to function, presently, so let's use that here
				$categoryFilterWidget = new \WPGMZA\CategoryFilterWidget\Checkboxes($map);
			}
			else if($map->categoryFilterWidget instanceof \WPGMZA\CategoryFilterWidget\Dropdown)
			{
				$select = $map->categoryFilterWidget->document->querySelector("select");
				
				// This class specifically needs to be added to the store locator
				if($wpgmza->settings->useLegacyHTML)
					$select->addClass("wpgmza-form-field__input wpgmza_filter_select_{$map->id}");
				
				$categoryFilterWidget = $map->categoryFilterWidget;
			}
			else
				$categoryFilterWidget = $map->categoryFilterWidget;
			
			// Get the container
			$container = $fragment->querySelector(".wpgmza-category-filter-container");
			$before = $document->querySelector(".wpgmza-search");
			
			if($container && $before)
			{
				$container = $document->importNode($container, true);
				$container->import($categoryFilterWidget->document);
				$container->populate($this);
				
				$before->parentNode->insertBefore($container, $before);
			}
			else
			{
				if(!$container)
					trigger_error("No container found for category filter widget", E_USER_WARNING);
				
				if(!$before)
					trigger_error("Couldn't find insertion point for category filter container", E_USER_WARNING);
			}
		}
		
		/*if(!$this->keywordSearchEnabled && $element = $this->document->querySelector('div.wpgmza-keywords'))
			$element->remove();
		
		if($this->searchArea == ProStoreLocator::SEARCH_AREA_AUTO && $element = $this->document->querySelector("div.wpgmza-search-area"))
			$element->remove();*/
		
		$this->applyLegacyHTML();
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case "keywordsLabel":
			
				if(!empty($this->map->store_locator_name_string))
					return __($this->map->store_locator_name_string, 'wp-google-maps');
				
				return __("Title / Description:", "wp-google-maps");
			
				break;
			
			case "allowUserLocation":
			
				return !empty($this->map->store_locator_use_their_location) && $this->map->store_locator_use_their_location == "1";
			
				break;
			
			case "keywordSearchEnabled":
			
				return $this->map->store_locator_name_search == "1";
			
				break;
			
			case "categorySearchEnabled":
			
				return $this->map->store_locator_category == "1";
			
				break;
			
			case "searchArea":
				
				return $this->map->store_locator_search_area;
				
				break;
		}
		
		return StoreLocator::__get($name);
	}
	
	public function getIterator()
	{
		$iterator = StoreLocator::getIterator();

		$fields = array(
			'keywordsLabel'		=> $this->keywordsLabel
		);
		
		foreach($fields as $key => $value)
			$iterator->offsetSet($key, $value);
		
		return $iterator;
	}
	
	protected function applyLegacyHTML()
	{
		$document = $this->document;
		
		// Legacy map ID attributes
		$map = array(
			'label.wpgmza-address'			=> "for",
			'input.wpgmza-address'			=> "mid",
			'input.wpgmza-address'			=> "id",
			'label.wpgmza-keywords'			=> "for",
			'input.wpgmza-keywords'			=> "id",
			'label.wpgmza-search-area'		=> "for",
			'select.wpgmza-search-area'		=> "id",
			'input.wpgmza-search'			=> "mid"
		);
		
		$document->querySelectorAll(".wpgmza-keywords > label")->setAttribute("for", "nameInput_");
		$document->querySelectorAll(".wpgmza-keywords > input")->setAttribute("id", "nameInput_");
		
		$document->querySelectorAll("label.wpgmza-address")->setAttribute("for", "addressInput_");
		$document->querySelectorAll("input.wpgmza-address")->setAttribute("id", "addressInput_");
		
		$document
			->querySelectorAll("input.wpgmza-search")
			->removeClass("wpgmza_sl_search_button")
			->addClass("wpgmza_sl_search_button_" . $this->map->id);
		
		foreach($map as $selector => $attr)
		{
			if(!($element = $document->querySelector($selector)))
				continue;
			
			$value = $element->getAttribute($attr);
			$element->setAttribute($attr, $value . $this->map->id);
		}
		
		// Legacy classes
		$document
			->querySelectorAll(".wpgmza-category-filter-container")
			->addClass("wpgmza-form-field wpgmza_sl_category_div");
			
		$document
			->querySelectorAll(".wpgmza-category-filter-container > label")
			->setAttribute("for", "wpgmza_filter_select")
			->addClass("wpgmza-form-field__label wpgmza-form-field__label--float");
		
		$document
			->querySelectorAll("div.wpgmza-keywords")
			->addClass('wpgmza-form-field wpgmza_sl_query_div');
			
		$document
			->querySelectorAll(".wpgmza-keywords > label")
			->addClass('wpgmza-form-field__label wpgmza-form-field__label--float wpgmza_sl_query_innerdiv1 wpgmza_name_search_string');
		
		$document
			->querySelectorAll(".wpgmza-keywords > input")
			->addClass("wpgmza-text-search")
			->setAttribute("size", "20");
		
		$document
			->querySelectorAll(".wpgmza-address-container")
			->removeClass("wpgmza-clearfix");
			
		$document
			->querySelectorAll("input.wpgmza-address")
			->addClass("addressInput");
			
		$document
			->querySelectorAll("select.wpgmza-radius")
			->setAttribute("id", "radiusSelect_{$this->map->id}");

		$document
			->querySelectorAll("label.wpgmza-radius")
			->setAttribute("for", "radiusSelect_{$this->map->id}");
			
		// Wrap search button
		$div = $document->createElement("div");
		$div->addClass("wpgmza-search wpgmza_sl_search_button_div");
		
		$document
			->querySelectorAll("input.wpgmza-search")
			->wrap($div);
			
		// Reset button
		$document
			->querySelectorAll("div.wpgmza-reset")
			->addClass("wpgmza_sl_reset_button_div");
		
		$document
			->querySelectorAll("input.wpgmza-reset")
			->addClass("wpgmza_sl_reset_button_{$this->map->id}")
			->setAttribute("mid", $this->map->id)
			->setAttribute("onclick", "resetLocations({$this->map->id})");
	}
}

add_filter('wpgmza_create_WPGMZA\\StoreLocator', function($map) {
	
	return new ProStoreLocator($map);
	
});