<?php

namespace WPGMZA;

class ProSettingsPage extends SettingsPage
{
	public function __construct()
	{
		SettingsPage::__construct();
		
		// Enable Pro controls
		// $this->querySelectorAll('.wpgmza-basic-only');
		
		// Remove basic only elements
		$this->document->querySelectorAll('.wpgmza-upsell')->remove();
		$this->document->querySelectorAll('.wpgmza-pro-feature')->removeClass('wpgmza-pro-feature');

		$this->markerLibraryDialog = new MarkerLibraryDialog();
		@$this->document->querySelector("#wpgmza-global-settings")->import($this->markerLibraryDialog->html());

	}
}

add_filter('wpgmza_create_WPGMZA\\SettingsPage', function() {	
	return new ProSettingsPage();
}, 10, 0);
