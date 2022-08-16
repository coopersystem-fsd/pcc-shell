<?php

namespace WPGMZA;

if(!defined('ABSPATH'))
	return;

class Heatmap extends Feature
{
	public function __construct($id_or_fields=-1, $read_mode=Crud::SINGLE_READ)
	{
		global $wpdb;
		
		Crud::__construct("{$wpdb->prefix}wpgmza_datasets", $id_or_fields, $read_mode);
	}
	
	protected function get_arbitrary_data_column_name()
	{
		return "options";
	}
}