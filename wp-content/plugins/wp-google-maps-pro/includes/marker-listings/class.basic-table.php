<?php

namespace WPGMZA\MarkerListing;

class BasicTable extends \WPGMZA\MarkerListing
{
	public function __construct($map_id)
	{
		\WPGMZA\MarkerListing::__construct($map_id);
	}
	
	public function getAjaxResponse($request)
	{
		global $wpgmza;
		
		$response = $this->getRecords($request);
		
		$document = new \WPGMZA\DOMDocument();
		$document->loadPHPFile($this->getItemHTMLPath() . 'basic-table-item.html.php');
		
		$template = $document->querySelector("body>*");
		$template->remove();
		
		if(!$this->map->isDirectionsEnabled())
		{
			foreach($template->querySelectorAll(".wpgmza_gd") as $el)
				$el->remove();
		}
		
		$imageDimensions = $this->getImageDimensions();
		
		foreach($response->data as $marker)
		{
			$item = $template->cloneNode(true);
			
			if($wpgmza->settings->useLegacyHTML)
			{
				if(isset($request['map_id']))
					$item->setAttribute('mapid',	$request['map_id']);
				
				// Attributes
				$item->setAttribute('id', "wpgmza_marker_{$marker->id}");
				$item->setAttribute('mid', $marker->id);
				
				// Image
				$img = $item->querySelector('.wpgmza_map_image');
				if(!empty($marker->pic))
					$img->setAttribute('src', $marker->pic);
				$img->setInlineStyle('width', "{$imageDimensions->width}px");
				$img->setInlineStyle('height', "{$imageDimensions->height}px");
				$img->setAttribute('alt', $marker->title);
				
				// Title
				$title = $item->querySelector('.wpgmza_marker_title a');
				$title->setAttribute('title', $marker->title);
				$title->appendText($marker->title);
				
				// Address
				$address = $item->querySelector(".wpgmza-address");
				$address->appendText($marker->address);
				
				// Description
				$item->querySelector(".wpgmza-desc>p")->import(do_shortcode($marker->description));
				
				// Icon
				$icon = new \WPGMZA\MarkerIcon($marker->icon);
				$item->querySelector('.wpgmza_marker_icon')->setAttribute('data-marker-icon-src', json_encode($icon));
				
				// Directions
				$directions = $item->querySelector('.wpgmza_gd');
				if($directions)
				{
					$directions->setAttribute('wpgm_addr_field', $marker->address);
					$directions->setAttribute('gps', "{$marker->lat},{$marker->lng}");
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
			}
			
			$this->appendListingItem($document, $item, $marker);
		}
		
		$response->html = $document->saveInnerBody();
		
		unset($response->data);
		
		return $response;
	}
}
