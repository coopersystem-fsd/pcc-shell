<?php

namespace WPGMZA;

abstract class ImportIntegration extends Import
{
	public function __construct()
	{
		Import::__construct();
	}
	
	//abstract protected function parse_file();
	//abstract protected function import();
	
	protected function load_file()
	{
		// Do nothing, integration is usually in the DB. This can be overriden as needed.
	}
	
	public function admin_options()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		
		$document = new DOMDocument();
		
		$document->loadHTML('<div class="wpgmza-import-admin-options">
			<select name="map_id"></select>
		</div>');
		$select = $document->querySelector('select');
		
		$results = $wpdb->get_results("SELECT id, map_title AS title FROM $WPGMZA_TABLE_NAME_MAPS");
		
		foreach($results as $data)
		{
			$option = $document->createElement('option');
			$option->setAttribute('value', $data->id);
			$option->appendText("{$data->title} ({$data->id})");
			$select->appendChild($option);
		}
		
		return $document->html;
	}
	
	protected function check_options()
	{
		return true;
	}
}
