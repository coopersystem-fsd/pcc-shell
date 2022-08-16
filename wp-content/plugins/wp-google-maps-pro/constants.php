<?php

global $wpdb;

global $WPGMZA_TABLE_NAME_CATEGORIES;
global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
global $WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS;

define('WPGMZA_PRO_DIR_PATH', plugin_dir_path(__FILE__));
define('WPGMZA_PRO_DIR_URL', plugin_dir_url(__FILE__));

$WPGMZA_TABLE_NAME_CATEGORIES				= $wpdb->prefix . 'wpgmza_categories';
$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES	= $wpdb->prefix . 'wpgmza_markers_has_categories';
$WPGMZA_TABLE_NAME_CATEGORY_MAPS			= $wpdb->prefix . 'wpgmza_category_maps';
$WPGMZA_TABLE_NAME_BATCHED_IMPORTS			= $wpdb->prefix . 'wpgmza_batched_imports';
$WPGMZA_TABLE_NAME_CUSTOM_FIELDS 			= $wpdb->prefix . 'wpgmza_custom_fields';
$WPGMZA_TABLE_NAME_MAPS_HAS_CUSTOM_FIELDS_FILTERS = $wpdb->prefix . 'wpgmza_maps_has_custom_fields_filters';