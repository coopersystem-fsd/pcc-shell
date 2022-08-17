<?php

namespace WPGMZA\MarkerListing;

class BasicList extends \WPGMZA\MarkerListing
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
		$document->loadPHPFile($this->getItemHTMLPath() . 'basic-list-item.html.php');
		
		$template = $document->querySelector("body>*");
		$template->remove();
		
		$imageDimensions = $this->getImageDimensions();
		
		foreach($response->data as $marker)
		{
			$item = $template->cloneNode(true);
			
			if($wpgmza->settings->useLegacyHTML)
			{
				// Attributes
				$item->setAttribute('id', 		"wpgmza_marker_{$marker->id}");
				$item->setAttribute('mid',		$marker->id);
				$item->setAttribute('mapid',	$request['map_id']);
				
				// Image
				// TODO: Base class should set marker icon. Ideally the DB would fetch WPGMZA\Markers in a PDO like fashion
				$icon = new \WPGMZA\MarkerIcon($marker->icon);
				$item->querySelector('.wpgmza_marker_icon')->setAttribute('data-marker-icon-src', json_encode($icon));
				
				// Title
				$item->querySelector('.wpgmza_div_title')->appendText($marker->title);
				
				// Address
				$item->querySelector('.wpgmza_div_address')->appendText($marker->address);
			}
			
			$this->appendListingItem($document, $item, $marker);
		}
		
		$response->html = $document->saveInnerBody();
		
		unset($response->data);
		
		return $response;
	}
}
