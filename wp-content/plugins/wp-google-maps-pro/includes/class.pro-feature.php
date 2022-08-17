<?php

namespace WPGMZA;

add_action('wpgmza_require_feature_classes', function() {
	
	$dir = plugin_dir_path(__FILE__);
	
	require_once($dir . 'class.heatmap.php');
	
});
