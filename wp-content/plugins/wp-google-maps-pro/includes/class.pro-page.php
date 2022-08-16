<?php

namespace WPGMZA;

// NB: This should be implemented as a Trait when we move up to PHP 5.4
class ProPage extends Page
{
	public static function removeUpsells($document)
	{
		$document->querySelectorAll(".wpgmza-upsell")->remove();
	}
	
	public static function enableProFeatures($document)
	{
		$document->querySelectorAll('
			input.wpgmza-pro-feature, 
			select.wpgmza-pro-feature, 
			textarea.wpgmza-pro-feature, 
			.wpgmza-pro-feature input, 
			.wpgmza-pro-feature select, 
			.wpgmza-pro-feature textarea')
			->removeAttribute('disabled')
			->removeAttribute('title')
			->removeClass('wpgmza-pro-feature
		');
		
		$document->querySelectorAll('.wpgmza-pro-feature')->removeClass('wpgmza-pro-feature');
		
		$document->querySelectorAll('.wpgmza-pro-feature-upsell,.wpgmza_upgrade_nag')->setAttribute('style', 'display:none;');

		/* Now disabled integrations that should have been left as such */
		$document->querySelectorAll('#wpgmza-integration-panel input[readonly]')->setAttribute("disabled", "disabled");
	}
}
