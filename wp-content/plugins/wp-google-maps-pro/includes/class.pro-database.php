<?php

namespace WPGMZA;

class ProDatabase
{
	public function __construct()
	{
		global $wpgmza;
		global $wpgmza_pro_version;
		
		$this->version = get_option('wpgmza_pro_db_version');
		$this->charset_collate = (method_exists('WPGMZA\\Database', 'getCharsetAndCollate') ? Database::getCharsetAndCollate() : '');
		
		if(version_compare($this->version, $wpgmza_pro_version, '<'))
			$this->install();
	}
	
	public function install()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		global $wpgmza;
		global $wpgmza_pro_version;
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		if(version_compare($this->version, '8.0.11', '<'))
		{
			// Migrate directions enabled setting value
			// Legacy versions used 1 to represent ON, and 2 to represent OFF
			// 8.0.11 and forward use 1 to represent ON and 0 to represent OFF
			$wpdb->query("UPDATE $WPGMZA_TABLE_NAME_MAPS SET directions_enabled = 0 WHERE directions_enabled = 2");
		}
		
		$this->installCategoryMapsTable();
		$this->installCategoryTable();
		$this->installHeatmapTable();
		// $this->installBatchedImportTable();
		
		$this->fixGalleryBottleneck();
		
		CustomFields::install();
		
		update_option('wpgmza_pro_db_version', $wpgmza_pro_version);
	}
	
	protected function installCategoryMapsTable()
	{
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
		
		$sql = "CREATE TABLE `$WPGMZA_TABLE_NAME_CATEGORY_MAPS` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`cat_id` int(11) NOT NULL,
			`map_id` int(11) NOT NULL,
			PRIMARY KEY (`id`)
		) AUTO_INCREMENT=1 {$this->charset_collate}";

		dbDelta($sql);
	}
	
	protected function installCategoryTable()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		
		$sql = "
			CREATE TABLE `".$WPGMZA_TABLE_NAME_CATEGORIES."` (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  active TINYINT(1) NOT NULL,
			  category_name VARCHAR(50) NOT NULL,
			  category_icon VARCHAR(700) NOT NULL,
			  retina TINYINT(1) DEFAULT '0',
			  parent INT(11) DEFAULT '0',
			  priority INT(11) NOT NULL,
			  PRIMARY KEY  (id)
			) AUTO_INCREMENT=1 {$this->charset_collate}";

		dbDelta($sql);
		
		// Update to 8.0.26 marker icon format
		$categories = $wpdb->get_results("SELECT id, category_icon, retina FROM $WPGMZA_TABLE_NAME_CATEGORIES");
				
		foreach($categories as $category)
		{
			if(json_decode($category->category_icon))
				continue;	// Category already has new format of marker icon!
			
			if(class_exists('MarkerIcon')){
				if(empty($category->category_icon))
					$icon = new MarkerIcon();
				else
					$icon = new MarkerIcon($category->category_icon);
				
				$icon->retina = ($category->retina == 1);
				
				$json = json_encode($icon);
				$stmt = $wpdb->prepare("UPDATE $WPGMZA_TABLE_NAME_CATEGORIES SET category_icon = %s WHERE id = %d", array(
					json_encode($icon),#
					$category->id
				));
				$wpdb->query($stmt);
			}
		}
	}
	
	protected function installHeatmapTable()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_HEATMAPS;
		
		$sql = "
			CREATE TABLE `".$WPGMZA_TABLE_NAME_HEATMAPS."` (
			id int(11) NOT NULL AUTO_INCREMENT,
			map_id int(11) NOT NULL,
			type INT(3) NOT NULL,
			dataset_name VARCHAR(100) NOT NULL,
			dataset LONGTEXT NOT NULL,
			options LONGTEXT NOT NULL,
			PRIMARY KEY  (id)
			) AUTO_INCREMENT=1 {$this->charset_collate}";
		
		dbDelta($sql);
	}
	
	protected function installBatchedImportTable()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_BATCHED_IMPORTS;
		
		$sql = "
			CREATE TABLE `".$WPGMZA_TABLE_NAME_BATCHED_IMPORTS."` (
			id			INT(11) NOT NULL AUTO_INCREMENT,
			state		ENUM('initial', 'running', 'working', 'halted', 'complete') NOT NULL DEFAULT 'initial',
			playhead	INT(11) NOT NULL DEFAULT 0,
			steps		INT(11) NOT NULL DEFAULT 0,
			seconds		INT(11) NOT NULL DEFAULT 1,
			iterations	INT(11) NOT NULL DEFAULT 1,
			next_run	DATETIME,
			class		VARCHAR(512),
			data		LONGTEXT,
			output		LONGTEXT,
			PRIMARY KEY  (id)
			) AUTO_INCREMENT=1 {$this->charset_collate}";
		
		dbDelta($sql);
	}
	
	protected function fixGalleryBottleneck()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_BATCHED_IMPORTS;
		
		$wpdb->query('
			UPDATE ' . $wpdb->prefix . 'wpgmza SET other_data="" WHERE other_data=\'a:1:{s:7:"gallery";O:20:"WPGMZA\MarkerGallery":1:{s:8:"*items";a:0:{}}}\'
		');
	}
	
}
