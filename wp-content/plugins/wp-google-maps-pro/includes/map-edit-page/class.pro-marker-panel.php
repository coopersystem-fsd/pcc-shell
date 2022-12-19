<?php

namespace WPGMZA;

class ProMarkerPanel extends MarkerPanel
{
	public function __construct($map_id)
	{
		/* Added: 8.1.15 - Should be considered temporary */
		add_filter('user_can_richedit', array($this, 'enableRichTextEditing'), 99, 1);
		
		MarkerPanel::__construct($map_id);

		$this->initCategoryPicker($map_id);
		$this->initCustomFields();
	}
	
	protected function initCategoryPicker($map_id)
	{
		$categoryPicker = new CategoryPicker(array(
			'ajaxName' => 'category',
			'map_id' => $map_id
		));

		$this->querySelector(".wpgmza-category-picker-container")->import($categoryPicker);
	}
	
	protected function initCustomFields()
	{
		$panel = $this->querySelector('.wpgmza-marker-panel');
		
		// Add custom fields
		$customFieldsHTML = CustomFeatureFields::adminHtml();
		$panel->import($customFieldsHTML);
		
		// Move save button to back (after custom fields added)
		$fieldset = $this->querySelector(".wpgmza-save-feature-container");
		$panel->appendChild($fieldset);
	}

	/**
	 * Enabled Rich Text editing temporarily for the user
	 * 
	 * This allows users who have disabled Visual Editing in their profile (TinyMCE) to still use the tool in our editor. Our core relies on TinyMCE at the moment, so this force is necessary
	 * 
	 * This is filter based, to keep things lightweight and isolated. With that said, this is temporary until V9 is released which will do away with TinyMCE
	 * 
	 * @param bool $allowed Whether or not the user can edit currently
	 * 
	 * @return bool
	*/
	public function enableRichTextEditing($allowed){
		if(empty($allowed)){
			$allowed = true;
		}
		return $allowed;
	}
}

