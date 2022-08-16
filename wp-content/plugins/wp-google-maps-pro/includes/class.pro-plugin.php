<?php

namespace WPGMZA;

if(!defined('WPGMZA_PLUGIN_DIR_PATH'))
	return;

wpgmza_require_once(WPGMZA_PLUGIN_DIR_PATH . 'includes/class.plugin.php');

$dir = plugin_dir_path(__FILE__);

wpgmza_require_once($dir . 'class.category.php');
wpgmza_require_once($dir . 'class.pro-rest-api.php');
wpgmza_require_once($dir . 'class.pro-marker.php');
wpgmza_require_once($dir . 'class.pro-store-locator.php');
wpgmza_require_once($dir . 'class.pro-admin-ui.php');
wpgmza_require_once($dir . 'map-edit-page/class.directions-box-settings-panel.php');
wpgmza_require_once($dir . 'map-edit-page/class.pro-map-edit-page.php');
wpgmza_require_once($dir . 'tables/class.pro-admin-marker-datatable.php');
wpgmza_require_once($dir . '3rd-party-integration/class.pro-gutenberg.php');
wpgmza_require_once($dir . '3rd-party-integration/class.marker-source.php');
wpgmza_require_once($dir . 'class.pro-settings-page.php');
wpgmza_require_once($dir . 'class.pro-feature.php');

class ProPlugin extends Plugin
{
	private $cachedProVersion;
	
	public function __construct()
	{
		Plugin::__construct();
		
		$this->acfIntegration 					= new \WPGMZA\Integration\ACF();
		$this->toolsetWooCommerceIntegration 	= new \WPGMZA\Integration\ToolsetWooCommerce();
		
		
		$this->proDatabase = new ProDatabase();
	}
	
	public static function assertClassExists($class)
	{
		if(!class_exists($class))
		{
			if(wpgmza_preload_is_in_developer_mode())
				return false;
			
			add_action('admin_notices', function() {
				
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong>
						<?php
						_e('WP Google Maps', 'wp-google-maps');
						?></strong>:
						<?php
						_e("The Pro add-on failed to assert that the class dependency $class exists. This could be due to truncated or empty PHP scripts in the core plugin. We recommend re-installing WP Google Maps to attempt to solve this issue.", 'wp-google-maps');
						?>
					</p>
				</div>
				<?php
			
			});
			
			return false;
		}
		
		return true;
	}
	
	public static function onActivate()
	{
		require_once(plugin_dir_path(__FILE__) . 'class.pro-database.php');
		
		$db = new ProDatabase();
		$db->install();
	}
	
	public static function onDeactivate()
	{
		
	}
	
	public function onInit()
	{
		Plugin::onInit();
		
		$this->cloudAPI = new CloudAPI();
	}
	
	public function getLocalizedData()
	{
		$data = Plugin::getLocalizedData();
		
		$categoryTree = CategoryTree::createInstance();
		
		if(empty($data['ajaxnonce']))
			$data['ajaxnonce'] = wp_create_nonce('wpgmza_ajaxnonce');
		
		return array_merge($data, array(
			'mediaRestUrl'			=> rest_url('/wp/v2/media/'),
			'categoryTreeData'		=> $categoryTree,
			'defaultPreloaderImage'	=> plugin_dir_url(__DIR__) . 'images/AjaxLoader.gif',
			'pro_version' 			=> $this->getProVersion(),
			'heatmapIcon'			=> plugin_dir_url(__DIR__) . 'images/heatmap-point.png'
		));
	}
	
	public static function getDirectoryURL()
	{
		return plugin_dir_url(__DIR__);
	}
	
	public function isProVersion()
	{
		return true;
	}
	
	public function getProVersion()
	{
		if($this->cachedProVersion != null)
			return $this->cachedProVersion;
		
		$subject = file_get_contents(plugin_dir_path(__DIR__) . 'wp-google-maps-pro.php');
		if(preg_match('/Version:\s*(.+)/', $subject, $m))
			$this->cachedProVersion = trim($m[1]);
		
		return $this->cachedProVersion;
	}
}

add_filter('wpgmza_create_WPGMZA\\Plugin', function() {
	
	return new ProPlugin();
	
}, 10, 0);
