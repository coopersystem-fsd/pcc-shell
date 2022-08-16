<?php

namespace WPGMZA;

class FontAwesomeIconPicker
{
	private $fontAwesomeVersion;
	
	public function __construct()
	{
		global $wpgmza;
		
		$this->fontAwesomeVersion = $wpgmza->settings->use_fontawesome;
		
		add_action('wp_enqueue_scripts', array($this, 'onEnqueueScripts'));
		$this->onEnqueueScripts();
	}
	
	public function onEnqueueScripts()
	{
		switch($this->fontAwesomeVersion)
		{
			case '5.*':
			case 'none':
				wp_enqueue_script('wpgmza-fontawesome-iconpicker', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js');
				wp_enqueue_style('wpgmza-fontawesome-iconpicker', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css');
				break;
				
			default:
				wp_enqueue_script('wpgmza-awesome4-iconpicker', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/fontawesome4-iconpicker/awesome4-iconpicker.min.js');
				wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
				wp_enqueue_style('wpgmza-awesome4-iconpicker', plugin_dir_url(WPGMZA_PRO_FILE) . 'lib/fontawesome4-iconpicker/awesome4-iconpicker.min.css');
				break;
		}
	}
}
