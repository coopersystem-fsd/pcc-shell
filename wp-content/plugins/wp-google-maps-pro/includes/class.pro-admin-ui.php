<?php
namespace WPGMZA\UI;


class ProAdmin extends Admin
{
	public function __construct()
	{
		Admin::__construct();
	}
	
	public function onAdminMenu()
	{
		global $wpgmza;
		
		Admin::onAdminMenu();
		
		$access_level = $wpgmza->getAccessCapability();
		
		add_submenu_page(
			'wp-google-maps-menu', 
			'WP Google Maps - Advanced', 
			__('Advanced', 'wp-google-maps'), 
			$access_level,
			'wp-google-maps-menu-advanced',
			'wpgmaps_menu_advanced_layout',
			2
		);
		
		add_submenu_page(
			'wp-google-maps-menu', 
			'WP Google Maps - Categories', 
			__('Categories', 'wp-google-maps'), 
			$access_level,
			'wp-google-maps-menu-categories',
			'wpgmaps_menu_category_layout',
			3
		);
		
		add_submenu_page(
			'wp-google-maps-menu', 
			'WP Google Maps - Custom Fields', 
			__('Custom Fields', 'wp-google-maps'), 
			$access_level,
			'wp-google-maps-menu-custom-fields',
			function() {
				$page = new \WPGMZA\CustomFieldsPage();
				$page->html();
			},
			4
		);
	}
	
	public function onMainMenu()
	{
		global $wpgmza;
		
		$action = (isset($_GET['action']) ? $_GET['action'] : null);
		
		switch($action)
		{
			case "wizard":
				wpgmaps_wizard_layout();
				return;
				break;
			
			default:
				break;
		}
		
		return Admin::onMainMenu();
	}
	
	public function onSubMenu()
	{
		switch($_GET['page'])
		{
			case "wp-google-maps-menu-advanced":
				break;
			
			case "wp-google-maps-menu-categories":
				break;
			
			case "wp-google-maps-menu-custom-fields":
				break;
			
			default:
				return Admin::onSubMenu();
				break;
		}
	}
}

add_filter('wpgmza_create_WPGMZA\\UI\\Admin', function() {
	
	return new ProAdmin();
	
});
