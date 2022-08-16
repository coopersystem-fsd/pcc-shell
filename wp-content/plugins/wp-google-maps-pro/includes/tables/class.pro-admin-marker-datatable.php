<?php

namespace WPGMZA;

class ProAdminMarkerDataTable extends AdminMarkerDataTable
{
	public function __construct($ajax_parameters=null)
	{
		AdminMarkerDataTable::__construct($ajax_parameters);
	}
	
	protected function getActionButtons()
	{
		global $wpgmza_ugm_version;
		
		$string = AdminMarkerDataTable::getActionButtons();

		if(!empty($wpgmza_ugm_version))
		{
			if(!preg_match('/REPLACE\((.+\t\'),/msi', $string, $m, PREG_OFFSET_CAPTURE))
				return $string;

			$inside = $m[1][0];
			$inside_original_length = strlen($inside);
			$inside_position = $m[1][1];
			

			if(!preg_match("/<a.+?class=\\\'wpgmza_del_btn/", $inside, $m, PREG_OFFSET_CAPTURE))
				return $string;
			
			$button_insert_position = $m[0][1];
			$button_html = '<a href="javascript: ;" 
				title="' . esc_attr( __('Approve this marker', 'wp-google-maps') ) . '" 
				class="wpgmza_approve_btn button" 
				id="' . AdminMarkerDataTable::ID_PLACEHOLDER . '">
					<i class="fa fa-check"></i>
				</a>';
			
			$before = substr($inside, 0, $button_insert_position);
			$after = substr($inside, $button_insert_position);
			
			$replacement = "CONCAT($before', 
			
				CASE WHEN approved = '0' THEN '$button_html'
				ELSE '' END,
				
			'$after)";

			$result = substr_replace($string, $replacement, $inside_position, $inside_original_length);
			
			return $result;
		}
		
		return $string;
	}
	
	protected function filterColumns(&$columns, $input_params)
	{
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		
		AdminMarkerDataTable::filterColumns($columns, $input_params);
		
		// Temporary workaround for map ID not passed through datatables endpoint
		if(isset($_REQUEST['wpgmzaDataTableRequestData']))
			$map_id = (int)$_REQUEST['wpgmzaDataTableRequestData']['map_id'];
		else if(isset($_REQUEST['map_id']))
			$map_id = (int)$_REQUEST['map_id'];
		
		if(isset($input_params['map_id']))
			$map_id = $input_params['map_id'];
		
		foreach($columns as $key => $value)
		{
			$name = $this->getColumnNameByIndex($key);
			
			switch($name)
			{
				case 'icon':
					
					$columns[$key] = ProMarker::getIconSQL($map_id, true);
					
					break;
				
				case 'category':
					
					$columns[$key] = "(
						SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
						FROM $WPGMZA_TABLE_NAME_CATEGORIES
						WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
							SELECT category_id
							FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
							WHERE marker_id = $WPGMZA_TABLE_NAME_MARKERS.id
						)
					) AS category";
						
					break;
					
				case 'description':
				
					$columns[$key] = 'description';
				
					break;
					
				case 'pic':
				
					$columns[$key] = "(
						CASE WHEN LENGTH(pic)=0 THEN 
							''
						ELSE 
							CONCAT(
								'<img src=\"', 
								pic,
								'\" width=\"40\"/>'
							)
						END
					) AS pic";
				
					break;
					
				case 'link':
					
					$columns[$key] = "(
					
						CASE WHEN LENGTH(link)=0 THEN 
							''
						ELSE 
							CONCAT(
								'<a href=\"',
								link,
								'\" target=\"_blank\">&gt;&gt;</a>'
							)
						END
					
					) AS link";
				
					break;
			}
		}
		
		return $columns;
	}
	
	public static function appendCategoryAndCustomFieldSearchClauses($sql, $input_params, &$query_params)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		global $WPGMZA_TABLE_NAME_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;
		
		if(empty($input_params['search']['value']))
			return $sql;
			
		$clauses = explode(' OR ', trim($sql, '()'));
		$term = $input_params['search']['value'];
		
		// Categories
		$categories = "(
			
			SELECT GROUP_CONCAT(category_name SEPARATOR ', ')
			FROM $WPGMZA_TABLE_NAME_CATEGORIES
			WHERE $WPGMZA_TABLE_NAME_CATEGORIES.id IN (
				SELECT category_id
				FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES
				WHERE marker_id=$WPGMZA_TABLE_NAME_MARKERS.id
			)
			
		) LIKE %s";
		
		$clauses[] = $categories;
		$query_params[] = "%%" . $wpdb->esc_like($term) . "%%";
		
		// Custom fields
		$custom_fields = "(
			
			SELECT GROUP_CONCAT(value SEPARATOR ', ')
			FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS
			WHERE $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS.object_id = $WPGMZA_TABLE_NAME_MARKERS.id
			
		) LIKE %s";
		
		$clauses[] = $custom_fields;
		$query_params[] = "%%" . $wpdb->esc_like($term) . "%%";
		
		// Rebuild clause
		$sql = "(" . implode(' OR ', $clauses) . ")";
		
		return $sql;
	}
	
	// TODO: Implement this as a trait when we drop support for PHP 5.3
	public function getSearchClause($input_params, &$query_params, $exclude_columns=null)
	{
		$sql = AdminMarkerDataTable::getSearchClause($input_params, $query_params, $exclude_columns);
		
		if(empty($input_params['search']['value']))
			return $sql;
		
		return ProAdminMarkerDataTable::appendCategoryAndCustomFieldSearchClauses($sql, $input_params, $query_params);
	}
}

add_filter('wpgmza_create_WPGMZA\\AdminMarkerDataTable', function($ajax_parameters=null) {
	
	return new ProAdminMarkerDataTable($ajax_parameters);
	
});
