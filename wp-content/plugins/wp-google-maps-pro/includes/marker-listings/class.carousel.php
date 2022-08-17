<?php

namespace WPGMZA\MarkerListing;

class Carousel extends \WPGMZA\MarkerListing {
	public function __construct($map_id) {
		global $wpgmza;

		\WPGMZA\MarkerListing::__construct($map_id);
		
		$this->setAjaxParameters(array());
		
		if($wpgmza->settings->useLegacyHTML) {
			$document = $this->element->ownerDocument;
			
			$container = $document->createElement('div');
			
			if($map_id !== null)
			{
				$container->setAttribute('id', "wpgmza_marker_list_container_$map_id");
				$this->element->setAttribute('id', "wpgmza_marker_list_$map_id");
			}
			
			$container->addClass('wpgmza_marker_carousel');
			$this->element->addClass('owl-carousel');
			$this->element->addClass('owl-theme');
			
			$this->element->parentNode->appendChild($container);
			$container->appendChild($this->element);
		}
		
		$this->element->setAttribute('data-wpgmza-carousel-marker-listing', null);
		$this->element->addClass('wpgmza_marker_carousel');
		$this->element->removeClass('wpgmza_marker_list_class');
	}
	
	public function __get($name) {
		global $wpgmza;
		
		switch($name) {
			case 'hideImage':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_image);
				break;
			
			case 'hideTitle':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_title);
				break;
			
			case 'hideIcon':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_icon);
				break;
			
			case 'hideAddress':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_address);
				break;
			
			case 'hideDescription':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_description);
				break;
			
			case 'hideLink':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_marker_link);
				break;
			
			case 'hideDirectionsLink':
				return !empty($wpgmza->settings->wpgmza_settings_carousel_markerlist_directions);
				break;
		}
		
		return \WPGMZA\MarkerListing::__get($name);
	}
	
	protected function removeHiddenFields($item, $marker)
	{
		global $wpgmza;
		
		\WPGMZA\MarkerListing::removeHiddenFields($item, $marker);
		
		if($this->hideDirectionsLink && $el = $item->querySelector('.wpgmza_marker_directions_link'))
			$el->remove();

		if($this->hideImage && $el = $item->querySelector('.wpgmza-gallery-container'))
			$el->remove();
	}
	
	public function getAjaxResponse($request)
	{
		global $wpgmza;
		
		$response = $this->getRecords($request);
		
		$document = new \WPGMZA\DOMDocument();
		$document->loadPHPFile($this->getItemHTMLPath() . 'carousel-item.html.php');
		
		$template = $document->querySelector("body>*");
		$template->remove();
		
		$imageDimensions = $this->getImageDimensions();
		
		// TODO: Odd / even classnames
		
		$index = 1;
		foreach($response->data as $marker)
		{
			$item = $template->cloneNode(true);
			
			// Classes
			if($index % 2 == 1)
				$item->addClass('wpgmza_carousel_odd');
			else
				$item->addClass('wpgmza_carousel_even');
			
			// NB: Removed owl-item - this breaks OwlCarousel >= 2.3.4
			$item->addClass('item');
			
			// Attributes
			$item->setAttribute('mid', $marker->id);
			$item->setAttribute('mapid', $request['map_id']);
			
			// Fields
			$icon = new \WPGMZA\MarkerIcon($marker->icon);
			
			$item->querySelector('.wpgmza_marker_icon')->setAttribute('data-marker-icon-src', json_encode($icon));
			$item->querySelector('.wpgmza_marker_title')->appendText($marker->title);
			$item->querySelector('.wpgmza_marker_address')->appendText($marker->address);
			$item->querySelector('.wpgmza_marker_description')->import(do_shortcode($marker->description));
			
			if($img = $item->querySelector('.wpgmza_map_image'))
			{
				if(empty($marker->pic))
					$img->remove();
				else
					$img->setAttribute('src', $marker->pic);
				$img->setAttribute('alt', $marker->title);
			}
			
			// Link
			$a = $item->querySelector('.wpgmza-link > a, .wpgmza_marker_link > a');
			$text = __('More Details', 'wp-google-maps');
			
			if(!empty($wpgmza->settings->wpgmza_settings_infowindow_link_text))
				$text = $wpgmza->settings->wpgmza_settings_infowindow_link_text;
			
			if($a && !empty($marker->link))
			{
				$a->setAttribute('href', $marker->link);
				$a->appendText($text);
			}
			
			$this->appendListingItem($document, $item, $marker);
			$index++;
		}
		
		$response->html = $document->saveInnerBody();
		
		unset($response->data);
		
		return $response;
	}
}
