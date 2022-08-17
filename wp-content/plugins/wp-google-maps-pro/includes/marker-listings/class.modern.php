<?php

namespace WPGMZA\MarkerListing;

class Modern extends \WPGMZA\MarkerListing
{
	
	protected $listItemTemplate;
	protected $listItemContainer;
	
	public function __construct($map_id)
	{
		\WPGMZA\MarkerListing::__construct($map_id);
		
		$this->element->removeAttribute('style');
		
		$this->listItemTemplate = $this->document->querySelector('li');
		$this->listItemContainer = $this->listItemTemplate->parentNode;
		
		$this->listItemTemplate->remove();
	}
	
	protected function loadDocument()
	{
		$this->document->loadPHPFile($this->getItemHTMLPath() . 'modern.html.php');
	}
	
	protected function getElement()
	{
		return $this->document->querySelector('body>*');
	}
	
	public function getAjaxResponse($request)
	{
		global $wpgmza;
		
		$response = $this->getRecords($request);
		
		foreach($response->data as $marker)
		{
			$item = $this->listItemTemplate->cloneNode(true);
			
			if(isset($request['map_id']))
				$item->setAttribute('mapid', $request['map_id']);
			$item->setAttribute('mid', $marker->id);
			
			$item->populate($marker);
			
			$this->appendListingItem($this->document, $item, $marker);
		}
		
		$response->html = "";
		foreach($this->document->querySelectorAll("li") as $li)
			$response->html .= $this->document->saveHTML($li);
		
		return $response;
	}
}
