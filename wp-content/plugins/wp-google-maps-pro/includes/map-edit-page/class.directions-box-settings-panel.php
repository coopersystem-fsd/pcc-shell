<?php

namespace WPGMZA;

class DirectionsBoxSettingsPanel extends DOMDocument
{
	public function __construct($data)
	{
		global $wpgmza;
		
		DOMDocument::__construct();
		
		$this->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/directions-box-settings.html.php');
		
 		$originIconPicker = new MarkerIconPicker(array(
			'name'	=> 'directions_route_origin_icon',
			'value'	=> empty($data->directions_route_origin_icon) ? Marker::DEFAULT_ICON : $data->directions_route_origin_icon,
			'retina_name' => 'directions_origin_retina'
       	));
        $destinationIconPicker = new MarkerIconPicker(array(
			'name'	=> 'directions_route_destination_icon',
			'value'	=> empty($data->directions_route_destination_icon) ? Marker::DEFAULT_ICON : $data->directions_route_destination_icon,
			'retina_name' => 'directions_destination_retina'
        ));

		$this->querySelector('#directions_origin_icon_picker_container')->import( $originIconPicker );
		$this->querySelector('#directions_destination_icon_picker_container')->import( $destinationIconPicker );

		if($wpgmza->settings->user_interface_style != 'legacy')
			$this->querySelector('fieldset#wpgmza-directions-box-style')->remove();

		@$this->populate($data);

		if($wpgmza->settings->engine != "open-layers" || !empty($wpgmza->settings->open_route_service_key))
			$this->querySelector('#open-route-service-key-notice')->remove();
	}
	
	public function onMapSaved($map)
	{
		$data = $this->serializeFormData();
		
		foreach($data as $key => $value)
			$map->{$key} = $value;
	}
}

add_action('wpgmza_map_saved', function($map) {
	
	$panel = new DirectionsBoxSettingsPanel((object)$_POST);
	$panel->onMapSaved($map);
	
}, 10, 1);
