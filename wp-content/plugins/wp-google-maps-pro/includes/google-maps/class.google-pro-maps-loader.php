<?php

namespace WPGMZA;

require_once(WPGMZA_DIR_PATH. 'includes/google-maps/class.google-maps-loader.php');

class GoogleProMapsLoader extends GoogleMapsLoader
{
	public static function _createInstance()
	{
		return new GoogleProMapsLoader();
	}
	
	protected function getGoogleMapsAPIParams()
	{
		$params = GoogleMapsLoader::getGoogleMapsAPIParams();
		
		$libraries = array();
		if(!empty($params['libraries']))
			$libraries = explode(',', $params['libraries']);
		$libraries[] = 'places';
		$libraries[] = 'visualization';
		$params['libraries'] = implode(',', $libraries);
		
		return $params;
	}
}
