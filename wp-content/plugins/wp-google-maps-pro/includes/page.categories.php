<?php

require_once(plugin_dir_path(__FILE__) . 'class.marker-library-dialog.php');

/*
Marker Category functionality for WP Google Maps Pro


*/

function wpgmaps_menu_category_layout() {


    if (!isset($_GET['action'])) {

        if (function_exists('wpgmza_register_pro_version')) {
            echo"<div class=\"wrap\"><h1>".__("Marker Categories","wp-google-maps")." <a href=\"admin.php?page=wp-google-maps-menu-categories&action=new\" class=\"add-new-h2\">".__("Add New Category","wp-google-maps")."</a></h1>";
            wpgmaps_list_categories();
        } else {
            echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>".__("Marker Categories","wp-google-maps")."</h2>";
            echo"<p><i><a href='http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=category' title='".__("Pro Version","wp-google-maps")."'>".__("Create marker categories","wp-google-maps")."</a> ".__("with the","wp-google-maps")." <a href='http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=category' title='Pro Version'>".__("Pro Version","wp-google-maps")."</a> ".__("of WP Google Maps for only","wp-google-maps")." <strong>$14.99!</strong></i></p>";


        }
        echo "</div>";
        echo"<br /><div style='float:right;'><a href='http://www.wpgmaps.com/documentation/troubleshooting/' title='WP Google Maps Troubleshooting Section'>".__("Problems with the plugin? See the troubleshooting manual.","wp-google-maps")."</a></div>";
    } else {

        if ($_GET['action'] == "trash" && isset($_GET['cat_id'])) {
            if (isset($_GET['s']) && $_GET['s'] == "1") {
                if (wpgmaps_trash_cat($_GET['cat_id'])) {
                    echo "<script>window.location = \"".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu-categories\"</script>";
                } else {
                    _e("There was a problem deleting the category.");;
                }
            } else {
                echo "<h2>".__("Delete your Category","wp-google-maps")."</h2><p>".__("Are you sure you want to delete the category","wp-google-maps")."? <br /><a href='?page=wp-google-maps-menu-categories&action=trash&cat_id=".$_GET['cat_id']."&s=1'>".__("Yes","wp-google-maps")."</a> | <a href='?page=wp-google-maps-menu-categories'>".__("No","wp-google-maps")."</a></p>";
            }


        }
        
        if ($_GET['action'] == "new") {
            wpgmza_pro_category_new_layout();
        }
        if ($_GET['action'] == "edit") {
            wpgmza_pro_category_edit_layout($_GET['cat_id']);
        }

    }

}

if (isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu-categories') {
    add_action('admin_print_scripts', 'wpgmaps_admin_category_scripts');
    add_action('admin_print_styles', 'wpgmaps_admin_category_styles');
}
function wpgmaps_admin_category_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('jquery-ui-core');

    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
        //wp_register_script('my-wpgmaps-upload', plugins_url('js/category_media.js', __FILE__), array('jquery'), '1.0', true);
		wp_register_script('my-wpgmaps-upload', plugin_dir_url(__DIR__) . 'js/category_media.js');
        wp_enqueue_script('my-wpgmaps-upload');
    } else {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('my-wpgmaps-upload', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/js/admin_category.js', array('jquery','media-upload','thickbox'));
        wp_enqueue_script('my-wpgmaps-upload');
    }

	wp_localize_script(
		'my-wpgmaps-upload',
		'wpgmza_legacy_map_edit_page_vars',
		array(
			'ajax_nonce' => wp_create_nonce('wpgmza')
		)
	);

}
function wpgmaps_admin_category_styles() {
    
}

function wpgmza_pro_category_new_layout() {
    
	// DB Fix
	global $wpdb;
	if(!$wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}wpgmza_categories LIKE 'parent'"))
		$wpdb->query("ALTER TABLE {$wpdb->prefix}wpgmza_categories ADD COLUMN parent int(11)");
	
	$markerLibraryDialog = new WPGMZA\MarkerLibraryDialog();
	$markerLibHtml = $markerLibraryDialog->html();
	
    $display_marker = "<img src=\"".wpgmaps_get_plugin_url()."/images/marker.png\" />";
    $map_ids = wpgmza_return_all_map_ids();
    
    echo $markerLibHtml;
    echo "<div class='wrap'>";
    echo "  <h1>".__("Add a Marker Category","wp-google-maps")."</h1>";
    echo "  <div class='wide'>";
    echo "      <form action='admin.php?page=wp-google-maps-menu-categories' method='post' id='wpgmaps_add_marker_category' name='wpgmaps_add_marker_category_form'>";
	
	echo "<input name='real_post_nonce' value='" . wp_create_nonce('wpgmza') . "' type='hidden'/>";
	
    echo "      <table class='wpgmza-listing-comp wpgmza-panel'>";
    echo "          <tr>";
    echo "              <td><strong>".__("Category Name","wp-google-maps")."</strong>:</td>";
    echo "              <td><input type='text' name='wpgmaps_marker_category_name' id='wpgmaps_marker_category_name' value='' /></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr style='height:20px;'>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr valign='top'>";
    
	echo "              <td><strong>".__("Category Marker","wp-google-maps")."</strong>:</td>";
	
	echo "<td align='left'><span id=\"wpgmza_mm\">";
	
	$options = array(
		'name' => 'upload_default_category_marker'
	);
	$markerIconPicker = new WPGMZA\MarkerIconPicker($options);
	echo $markerIconPicker->html;
	
    echo "</td></tr>";
	
	?>
	
	<tr class="wpgmza-hidden">
		<td>
			<strong>
				<?php _e('Category Image:', 'wp-google-maps'); ?>
			</strong>
		</td>
		<td>
			<div class='wpgmza-flex wpgmza-category-icon__btns'>
                <div class="wpgmza-flex__item">
                    <input type="text" name="category_image" placeholder="<?php _e('Enter URL', 'wp-google-maps'); ?>" />
                </div>
                <div id="wpgmza-category__upload-image-button" class="wpgmza-flex__item">
                    <button class="wpgmza_general_btn button button-secondary" type="button" data-media-dialog-target="input[name='category_image']">
                    <?php _e('Upload Image', 'wp-google-maps'); ?>
                    </button>
                </div>
            </div>
		</td>
	</tr>
	
	<?php
    echo "          <tr>";
    echo "              <td valign='top'><strong>".__("Parent Category","wp-google-maps")." (" . __("Optional", 'wp-google-maps') . "):</strong></td>";
    echo "              <td>";
    echo "                  <select name='parent_category' id='parent_category'>";
    echo "                      <option value='0'>".__( "None", "wp-google-maps" )."</option>";
    
    $cats = wpgmza_return_all_categories();
    if ($cats) {
        foreach ($cats as $cat) {
            
            $cat_id = $cat->id;
            if (isset($cat->category_name)) { $cat_name = $cat->category_name; } else { $cat_name = ""; }

            $display_cat_name = $cat_name . " (#" . $cat_id . ")";
            echo "                      <option value='".$cat_id."'>". $display_cat_name ."</option>";
        }
    }
    echo "                  </select>";
    echo "              </td>";
    echo "          </tr>";
    echo "          <tr style='height:20px;'>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr>";
    echo "              <td><strong>".__("Priority","wp-google-maps")."</strong>:</td>";
    echo "              <td><input type='number' name='wpgmaps_marker_category_priority' id='wpgmaps_marker_category_priority' value='0'  step='1' /></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr style='height:20px;'>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "          </tr>";

    echo "          <tr>";
    echo "              <td valign='top'><strong>".__("Assigned to ","wp-google-maps")."</strong>:</td>";
    echo "              <td>";
    echo "                  <div class='switch'><input class='cmn-toggle cmn-toggle-round-flat' id='map-cat-all' type='checkbox' name='assigned_to_map[]' value='ALL'><label for='map-cat-all'></label></div> All Maps <br /><br />";
    
    foreach ($map_ids as $map_id) {
        $map_data = wpgmza_get_map_data($map_id);
        echo "                  <div class='switch'><input class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='map-cat-".$map_id."' name='assigned_to_map[]' value='".$map_id."'> <label for='map-cat-".$map_id."'></label></div>".$map_data->map_title."  (#".$map_id.")<br />";
    }
    echo "              </td>";
    echo "          </tr>";
    
    echo "      </table>";
    
    echo "          <p class='submit'><input type='submit' name='wpgmza_save_marker_category' class='button-primary' value='".__("Save Category","wp-google-maps")." &raquo;' /></p>";
    echo "      </form>";
    echo "  </div>";
    echo "</div>";

}
function wpgmza_pro_category_edit_layout($cat_id) {

    global $wpdb;
    global $wpgmza_tblname_categories;
	
	$markerLibraryDialog = new WPGMZA\MarkerLibraryDialog();
	$markerLibHtml = $markerLibraryDialog->html();
	
    $map_ids = wpgmza_return_all_map_ids();

	$cat_id = (int)$cat_id;
    
    $results = $wpdb->get_results("
      SELECT *
      FROM $wpgmza_tblname_categories
      WHERE `id` = '$cat_id' LIMIT 1
    ");

	$category = $results[0];
    
    if (isset($results[0]->category_icon) && $results[0]->category_icon != '') {
        $display_marker = "<img src='".$results[0]->category_icon."' />";
        $display_url = $results[0]->category_icon;
    } else {
        $display_marker = "<img src=\"".wpgmaps_get_plugin_url()."/images/marker.png\" />";
        $display_url = "";

    }
    
    if (isset($results[0]->retina) && intval($results[0]->retina) == 1) {
        $retina_checked = "checked='checked'";
    } else {
        $retina_checked = "";
    }

    if (isset($results[0]->parent) && $results[0]->parent > 0) {
        $cat_parent_selected = $results[0]->parent;
    } else {
        $cat_parent_selected = 0;
    } 



    echo $markerLibHtml; 

    echo "<div class='wrap'>";
    echo "  <h1>".__("Edit a Marker Category","wp-google-maps")."</h1>";
    echo "  <div class='wide'>";
    echo "      <form action='admin.php?page=wp-google-maps-menu-categories' method='post' id='wpgmaps_add_marker_category' name='wpgmaps_edit_marker_category_form'>";

	echo "<input name='real_post_nonce' value='" . wp_create_nonce('wpgmza') . "' type='hidden'/>";


    if(intval($cat_parent_selected) == $cat_id){
        echo "<span style='padding: 10px;' class='notice notice-error'>Issue found: Your parent ID currently matches this category ID, this will cause errors with the category system. We have attempted to fix this, but recommend saving this category again</span><br><br>";    
    }

    echo "      <table class='wpgmza-listing-comp wpgmza-panel'>";
    echo "          <tr>";
    echo "              <td><strong>".__("Category Name","wp-google-maps")."</strong>:</td>";
    echo "              <td><input type='hidden' name='wpgmaps_marker_category_id' id='wpgmaps_marker_category_id' value='".$results[0]->id."' /><input type='text' name='wpgmaps_marker_category_name' id='wpgmaps_marker_category_name' value='".$results[0]->category_name."' /></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr style='height:20px;'>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "          </tr>";
    ?>

    <tr valign="top">
        <td><strong><?php _e("Category Marker","wp-google-maps"); ?></strong>:</td>
        <td>
		
	<?php
	
	$options = array(
		'name' => 'upload_default_category_marker'
	);
	
	if(!empty($display_url))
		$options['value'] = $display_url;
	
	$markerIconPicker = new WPGMZA\MarkerIconPicker($options);
	echo $markerIconPicker->html;
	
	?>
	
        </td>
    </tr>

	<tr class="wpgmza-hidden">
		<td>
			<strong>
				<?php _e('Category Image:', 'wp-google-maps'); ?>
			</strong>
		</td>
		<td>
            <div class='wpgmza-flex wpgmza-category-icon__btns'>
                <div class="wpgmza-flex__item">
                    <input type="text" name="category_image" value="<?php echo htmlspecialchars($category->image); ?>" placeholder="<?php _e('Enter URL', 'wp-google-maps'); ?>"/>
                </div>
                <div id="wpgmza-category__upload-image-button" class="wpgmza-flex__item">
                    <button class="wpgmza_general_btn button button-secondary" type="button" data-media-dialog-target="input[name='category_image']">
                        <?php _e('Upload Image', 'wp-google-maps'); ?>
                    </button>
                </div>
            </div>
		</td>
        <td></td>
	</tr>
	<?php
	
    echo "          <tr>";
    echo "              <td valign='top'><strong>".__("Parent Category:","wp-google-maps")."</strong></td>";
    echo "              <td>";
    echo "                  <select name='parent_category' id='parent_category'>";
    echo "                      <option value='0'>".__( "None", "wp-google-maps" )."</option>";
    
    $cats = wpgmza_return_all_categories();
    if ($cats) {
        foreach ($cats as $cat) {
            
            $current_cat_id = $cat->id;

            if(intval($current_cat_id) == $cat_id){
                continue;
            }

            if (isset($cat->category_name)) { $cat_name = $cat->category_name; } else { $cat_name = ""; }

            /* do not show if this is the same category id as the one we are editing - cannot be a parent of iteself... */
            if ($cat_id !== $current_cat_id) {
                $display_cat_name = $cat_name . " (#" . $current_cat_id . ")";
                if ($cat_parent_selected == $current_cat_id) {
                    echo "                      <option value='".$current_cat_id."' selected>". $display_cat_name ."</option>";
                } else {
                    echo "                      <option value='".$current_cat_id."'>". $display_cat_name ."</option>";

                }
            }
        }
    }
    echo "                  </select>";
    echo "                  <p class='description'>" . __( "Optional", "wp-google-maps" ) . "</p>";
    echo "              </td><td></td>";
    echo "          </tr>";
    echo "          <tr style='height:20px;'>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr>";
    echo "              <td><strong>".__("Priority","wp-google-maps")."</strong>:</td>";
    echo "              <td><input type='number' name='wpgmaps_marker_category_priority' id='wpgmaps_marker_category_priority' value='".$results[0]->priority."'  step='1' /></td>";
    echo "              <td></td>";
    echo "          </tr>";
    echo "          <tr style='height:20px;'>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "              <td></td>";
    echo "          </tr>";

    echo "          <tr>";
    echo "              <td valign='top'><strong>".__("Assigned to ","wp-google-maps")."</strong>:</td>";
    echo "              <td>";
    echo "                  <div class='switch'><input class='cmn-toggle cmn-toggle-round-flat' id='map-cat-all' type='checkbox' name='assigned_to_map[]' value='ALL' ".wpgmza_check_cat_map('ALL',$cat_id)."><label for='map-cat-all'></label></div> All Maps <br /><br />";
    
    foreach ($map_ids as $map_id) {
        $map_data = wpgmza_get_map_data($map_id);
        echo "                   <div class='switch'><input class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='map-cat-".$map_id."' name='assigned_to_map[]' value='".$map_id."' ".wpgmza_check_cat_map($map_id,$cat_id)."> <label for='map-cat-".$map_id."'></label></div>".$map_data->map_title."  (id ".$map_id.")<br />";
    }
    echo "              </td><td></td>";
    echo "          </tr>";
    
    echo "      </table>";    
    
    
    echo "          <p class='submit'><input type='submit' name='wpgmza_edit_marker_category' class='button-primary' value='".__("Save Category","wp-google-maps")." &raquo;' /></p>";
    echo "      </form>";
    echo "  </div>";
    echo "</div>";

}


function wpgmza_check_cat_map($map_id,$cat_id) {
    global $wpdb;
    global $wpgmza_tblname_category_maps;
	
	$map_id = (int)$map_id;
	$cat_id = (int)$cat_id;
	
    if ($map_id == "ALL") {
        $sql = "SELECT COUNT(*) FROM `".$wpgmza_tblname_category_maps."` WHERE `cat_id` = '$cat_id' AND `map_id` = '0' LIMIT 1";
    } else {
        $sql = "SELECT COUNT(*) FROM `".$wpgmza_tblname_category_maps."` WHERE `cat_id` = '$cat_id' AND `map_id` = '$map_id' LIMIT 1";
    }
    $results = $wpdb->get_var($sql);
    if ($results>0) { return "checked"; } else { return ""; }
}

add_action('admin_head', 'wpgmaps_category_head');
function wpgmaps_category_head() {

	global $wpdb;
	global $wpgmza_tblname_categories;
	global $wpgmza_tblname_category_maps;
	
	if(empty($wpgmza_tblname_categories))
		return;

	$columns = $wpdb->get_col("SHOW COLUMNS FROM $wpgmza_tblname_categories");
	
	if(array_search('image', $columns) === false)
		$wpdb->query("ALTER TABLE $wpgmza_tblname_categories ADD COLUMN image VARCHAR(512)");
	
	if(array_search('parent', $columns) === false)
		$wpdb->query("ALTER TABLE $wpgmza_tblname_categories ADD COLUMN parent INT(11) NOT NULL DEFAULT 0");
	
	if(array_search('priority', $columns) === false)
		$wpdb->query("ALTER TABLE $wpgmza_tblname_categories ADD COLUMN priority INT(11) NOT NULL DEFAULT 0");
	
    if (isset($_GET['page']) && $_GET['page'] == "wp-google-maps-menu-categories" && isset($_POST['wpgmza_save_marker_category'])) {
		
		check_ajax_referer( 'wpgmza', 'real_post_nonce' );
		
		if(!current_user_can('administrator'))
		{
			http_response_code(401);
			exit;
		}
		
        if (isset($_POST['wpgmza_save_marker_category'])){
            
            
            $wpgmaps_category_name = sanitize_text_field(stripslashes($_POST['wpgmaps_marker_category_name']));
            $wpgmaps_category_marker = sanitize_text_field(esc_attr($_POST['upload_default_category_marker']));
			$wpgmza_category_image = sanitize_text_field(esc_attr($_POST['category_image']));
			
			$wpgmaps_category_retina = (isset($_POST['retina']) ? 1 : 0);
			
			$icon = new \WPGMZA\MarkerIcon(array(
				"url"		=> $wpgmaps_category_marker,
				"retina"	=> $wpgmaps_category_retina
			));
			
			if($icon->isDefault)
				$icon = "";
			else
				$icon = json_encode($icon);

            if ( !isset( $_POST['assigned_to_map'] ) ) { $_POST['assigned_to_map'][0] = __( "All", "wp-google-maps" ); }
            
            if ( !isset( $_POST['parent_category'] ) ) { $cat_parent = 0; } else { $cat_parent = intval( sanitize_text_field( $_POST['parent_category'] ) );}

            $cat_priority = isset( $_POST['wpgmaps_marker_category_priority'] ) ? intval( $_POST['wpgmaps_marker_category_priority'] ) : 0;

            $rows_affected = $wpdb->query( $wpdb->prepare(
                    "INSERT INTO $wpgmza_tblname_categories SET
                        category_name = %s,
                        active = %d,
                        category_icon = %s,
						image = %s,
                        retina = %d,
                        parent = %d,
                        priority = %d
                    ",
                    $wpgmaps_category_name,
                    0,
                    $icon,
					$wpgmza_category_image,
                    intval($wpgmaps_category_retina),
                    $cat_parent,
                    $cat_priority
                )
            );
            
			
			
            $cat_id = $wpdb->insert_id;
            
            
            if ($_POST['assigned_to_map'][0] == "ALL") {
                    $rows_affected = $wpdb->query( $wpdb->prepare(
                        "INSERT INTO $wpgmza_tblname_category_maps SET
                            cat_id = %d,
                            map_id = %d
                        ",
                        $cat_id,
                        0
                    )
                    );
            } else {
                if( isset( $_POST['assigned_to_map'] ) ){ 
                    foreach ($_POST['assigned_to_map'] as $assigned_map) {

                        $rows_affected = $wpdb->query( $wpdb->prepare(
                            "INSERT INTO $wpgmza_tblname_category_maps SET
                                cat_id = %d,
                                map_id = %d
                            ",
                            $cat_id,
                            $assigned_map
                        )
                        );
                    }            
                }
            }
            echo "<div class='updated'>";
            _e("Your category has been created.","wp-google-maps");
            echo "</div>";

			do_action('wpgmza_categories_saved');

        }

    }
    if (isset($_GET['page']) && $_GET['page'] == "wp-google-maps-menu-categories" && isset($_POST['wpgmza_edit_marker_category'])) {
        
		check_ajax_referer( 'wpgmza', 'real_post_nonce' );
		
		if(!current_user_can('administrator'))
		{
			http_response_code(401);
			exit;
		}
		
            global $wpdb;
            global $wpgmza_tblname_categories;
            global $wpgmza_tblname_category_maps;
            $wpgmaps_cid = esc_attr($_POST['wpgmaps_marker_category_id']);
            if ( !isset($_POST['wpgmaps_marker_category_name'] ) ) { 
                $wpgmaps_category_name = "Unnamed category"; 
            } else { 
                $wpgmaps_category_name = sanitize_text_field(stripslashes($_POST['wpgmaps_marker_category_name'])); 
            }
            
            if ( !isset($_POST['assigned_to_map'] ) ) { $_POST['assigned_to_map'][0] = __( "All", "wp-google-maps" ); }

            if ( !isset( $_POST['parent_category'] ) ) { $cat_parent = 0; } else { $cat_parent = intval( sanitize_text_field( $_POST['parent_category'] ) ); }

            $cat_priority = isset( $_POST['wpgmaps_marker_category_priority'] ) ? intval( $_POST['wpgmaps_marker_category_priority'] ) : 0;

            $wpgmaps_category_marker = sanitize_text_field(esc_attr($_POST['upload_default_category_marker']));
			$wpgmza_category_image = sanitize_text_field(esc_attr($_POST['upload_default_category_marker']));
			
			$wpgmaps_category_retina = (isset($_POST['retina']) ? 1 : 0);
			
			$icon = new \WPGMZA\MarkerIcon(array(
				"url"		=> $wpgmaps_category_marker,
				"retina"	=> $wpgmaps_category_retina
			));
			
			if($icon->isDefault)
				$icon = "";
			else
				$icon = json_encode($icon);

            $rows_affected = $wpdb->query( $wpdb->prepare(
                "DELETE FROM $wpgmza_tblname_category_maps WHERE
                cat_id = %d"
                ,
                $wpgmaps_cid) 
            ); // remove all instances of this category in the category_maps table

            
            $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_categories SET

                category_name = %s,
                active = %d,
                category_icon = %s,
				image = %s,
                retina = %d,
                parent = %d,
                priority = %d

                WHERE
                id = %d",
                $wpgmaps_category_name,
                0,
                $icon,
				$_POST['category_image'],
                intval($wpgmaps_category_retina),
                $cat_parent,
                $cat_priority,
                $wpgmaps_cid) 
            );
            
            
            if ($_POST['assigned_to_map'][0] == "ALL") {
                    $rows_affected = $wpdb->query( $wpdb->prepare(
                        "INSERT INTO $wpgmza_tblname_category_maps SET
                            cat_id = %d,
                            map_id = %d
                        ",
                        $wpgmaps_cid,
                        0
                    )
                    );
            } else {
                
                
                foreach ($_POST['assigned_to_map'] as $assigned_map) {

                    $rows_affected = $wpdb->query( $wpdb->prepare(
                        "INSERT INTO $wpgmza_tblname_category_maps SET
                            cat_id = %d,
                            map_id = %d
                        ",
                        $wpgmaps_cid,
                        $assigned_map
                    )
                    );
                }            
            }            

            echo "<div class='updated'>";
            _e("Your category has been saved.","wp-google-maps");
            echo "</div>";
			
			do_action('wpgmza_categories_saved');
    }
}

function old_wpgmza_pro_return_category_select_list($map_id)
{
    trigger_error("Deprecated as of 8.0.19");

}
function wpgmza_pro_return_category_select_list($map_id)
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_pro_return_category_checkbox_list($map_id,$show_all = true,$array = false)
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_return_marker_count_by_category( $cat_id = false, $map_id = false )
{
	if($map_id)
	{
		$map = \WPGMZA\Map::createInstance($map_id);
		$categoryTree = $map->categoryTree;
	}
	else
	{
		$categoryTree = new \WPGMZA\CategoryTreeNative();
	}
	
	$node = $categoryTree->getChildByID($cat_id);
	
	return $node->marker_count;
}

function wpgmza_return_marker_count_category_via_elements( $elements, $wpgmza_settings )
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_tree_marker_counter( $elements, $wpgmza_settings )
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_consume_tree(array $array,$array_suffix,$map_id, $is_child = false)
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_consume_tree_dropdown(array $array,$array_suffix,$map_id, $ext_string = '')
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_build_tree(array $elements, $parentId = 0)
{
	$branch = array();

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = wpgmza_build_tree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
	
    return $branch;
}

function wpgmza_pro_return_category_dropdown_list($map_id, $show_all = true, $array = false)
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_pro_return_category_blocks($map_id,$show_all = true,$array = false)
{
	trigger_error("Deprecated as of 8.0.19");
}

function wpgmza_pro_return_maps_linked_to_cat($cat_id)
{
    global $wpdb;
    global $wpgmza_tblname_category_maps;
    $ret_msg = "";
    
    $sql = "SELECT * FROM `$wpgmza_tblname_category_maps` WHERE `cat_id` = '$cat_id'";
    $results = $wpdb->get_results($sql);
    $cnt = count($results);
    $cnt_i = 1;
    foreach ( $results as $result ) {
        
        $map_id = $result->map_id;
        if ($map_id == 0) {
            $ret_msg .= "<a href=\"?page=wp-google-maps-menu\">".__("All maps","wp-google-maps")."</option>";
            return $ret_msg;
        } else { 
            $map_data = wpgmza_get_map_data($map_id);
            if ($cnt_i == $cnt) { $wpgmza_com = ""; } else { $wpgmza_com = ","; }
            $ret_msg .= "<a href=\"?page=wp-google-maps-menu&action=edit&map_id=".$map_id."\">".$map_data->map_title."</a>$wpgmza_com ";
        }
        $cnt_i++;
        
    }
    

    return $ret_msg;

}

function wpgmaps_list_categories()
{
    //Use the category table class
    $categoryTable = new \WPGMZA\CategoryTable();
    echo $categoryTable->html;

}

function wpgmza_consume_tree_main_list(array $array,$array_suffix,$map_id, $ext_string = '') {

    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");


    global $wpmgza_cat_tree_html;

    foreach($array as $key => $value) {

		if ($value['parent_id'] == 0) {
            $ext_string = '';
        }

        $trashlink = "| <a href=\"?page=wp-google-maps-menu-categories&action=trash&cat_id=".$value['id']."\" title=\"Trash\">".__("Trash","wp-google-maps")."</a>";

		

        $wpmgza_cat_tree_html .=  "<tr id=\"record_".$value['id']."\">";
        $wpmgza_cat_tree_html .=  "  <td class='id column-id'>".$value['id']."</td>";
        $wpmgza_cat_tree_html .=  "  <td class='column-map_title'><strong><big><a href=\"?page=wp-google-maps-menu-categories&action=edit&cat_id=".$value['id']."\" title=\"".__("Edit","wp-google-maps")."\">".$ext_string.stripslashes($value['title'])."</a></big></strong><br /><a href=\"?page=wp-google-maps-menu-categories&action=edit&cat_id=".$value['id']."\" title=\"".__("Edit","wp-google-maps")."\">".__("Edit","wp-google-maps")."</a> $trashlink</td>";
        $wpmgza_cat_tree_html .=  "  <td class='column-map_width'><img src=\"".$value['category_icon']."\" style=\"max-width:100px; max-height:100px;\" /></td>";
        $wpmgza_cat_tree_html .=  "  <td class='column-map_width'>".wpgmza_pro_return_maps_linked_to_cat($value['id'])."</td>";
        $wpmgza_cat_tree_html .=  "  <td class='column-map_width'>" . (isset($value['priority']) ? $value['priority'] : "" ). "</td>";
        $wpmgza_cat_tree_html .=  "</tr>";

        //If $value is an array.

        

        
        if(is_array($value['children'])){
            $ext_string .= '— ';
            wpgmza_consume_tree_main_list($value['children'], $array_suffix, $map_id, $ext_string);
        }
    }
    $ext_string = '-';



    
}

function old_wpgmaps_list_categories() {
    global $wpdb;
    global $wpgmza_tblname_categories;

    $results = $wpdb->get_results("SELECT * FROM `$wpgmza_tblname_categories` WHERE `active` = 0 ORDER BY " . \WPGMZA\Category::getOrderBy());
    echo "<table class=\"wp-list-table widefat fixed striped pages\">";
    echo "  <thead>";
    echo "      <tr>";
    echo "          <th scope='col' width='100px' id='id' class='manage-column column-id' style=''>".__("ID","wp-google-maps")."</th>";
    echo "          <th scope='col' id='cat_cat' class='manage-column column-map_title'  style=''>".__("Category","wp-google-maps")."</th>";
    echo "          <th scope='col' id='cat_icon' class='manage-column column-map_width' style=\"\">".__("Icon","wp-google-maps")."</th>";
    echo "          <th scope='col' id='cat_parent' class='manage-column column-map_width' style=\"\">".__("Parent","wp-google-maps")."</th>";
    echo "          <th scope='col' id='cat_linked' class='manage-column column-map_width' style=\"\">".__("Linked maps","wp-google-maps")."</th>";
    echo "      </tr>";
    echo "  </thead>";
    echo "<tbody id=\"the-list\" class='list:wp_list_text_link'>";

    foreach ( $results as $result ) {
        $trashlink = "| <a href=\"?page=wp-google-maps-menu-categories&action=trash&cat_id=".$result->id."\" title=\"Trash\">".__("Trash","wp-google-maps")."</a>";

        if ($result->parent > 0) {
            $parent_data = wpgmza_return_category_data( $result->parent );
            $parent_link = admin_url('admin.php?page=wp-google-maps-menu-categories&action=edit&cat_id='.$result->parent);
            $parent_title = "<a href='".$parent_link."'>".stripslashes( $parent_data->category_name )."</a>";
        } else {
            $parent_title = '';
        }

        echo "<tr id=\"record_".$result->id."\">";
        echo "  <td class='id column-id'>".$result->id."</td>";
        echo "  <td class='column-map_title'><strong><big><a href=\"?page=wp-google-maps-menu-categories&action=edit&cat_id=".$result->id."\" title=\"".__("Edit","wp-google-maps")."\">".stripslashes( $result->category_name )."</a></big></strong><br /><a href=\"?page=wp-google-maps-menu-categories&action=edit&cat_id=".$result->id."\" title=\"".__("Edit","wp-google-maps")."\">".__("Edit","wp-google-maps")."</a> $trashlink</td>";
        echo "  <td class='column-map_width'><img src=\"".$result->category_icon."\" style=\"max-width:100px; max-height:100px;\" /></td>";
        echo "  <td class='column-map_width'>".$parent_title."</td>";
        echo "  <td class='column-map_width'>".wpgmza_pro_return_maps_linked_to_cat($result->id)."</td>";
        echo "</tr>";
    }
    echo "</table>";
}


function wpgmaps_trash_cat( $cat_id ) {
    global $wpdb;
    global $wpgmza_tblname_categories;
    global $wpgmza_tblname_category_maps;
	
	$cat_id = (int)$cat_id;
	
    if ( isset( $cat_id ) ) {
        $rows_affected = $wpdb->query( $wpdb->prepare( "UPDATE $wpgmza_tblname_categories SET active = %d WHERE id = %d", 1, $cat_id) );
        $rows_affected = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpgmza_tblname_category_maps WHERE cat_id = %d", $cat_id) );
		
		do_action('wpgmza_category_deleted');
		
        return true;
    } else {
        return false;
    }
}

/**
 * Return all category data from the table row that matches
 * 
 * @param  intval $cat_id   Category ID
 * @return array|boolean    Array if there is data or FALSE if not
 */
function wpgmza_return_category_data( $cat_id ) {
    global $wpgmza_tblname_categories;
    global $wpdb;
    $result = $wpdb->get_row( "SELECT * FROM `".$wpgmza_tblname_categories."` WHERE `id` = '".intval( $cat_id )."' AND `active` = 0 LIMIT 1" );
    return $result;
}


/**
 * Return all active categories
 *
 * @param  intval           $map_id    Map ID (optional)
 * @param  intval           $active    0 = active, 1 = deleted 
 * @return array|boolean               Array if there is data or FALSE if not
 */
function wpgmza_return_all_categories( $map_id = false, $active = 0 ) {
    global $wpdb;
    global $wpgmza_tblname_categories;
    global $wpgmza_tblname_category_maps;    

	$active = (int)$active;
	
    if ( !$map_id ) {
        /* get all category data for all maps */
        $results = $wpdb->get_results("SELECT * FROM `".$wpgmza_tblname_categories."` WHERE `active` = ".$active." ORDER BY " . \WPGMZA\Category::getOrderBy());
    } else {
        /* get all category data for a specific map */

        $sql = "

        SELECT $wpgmza_tblname_category_maps.* , $wpgmza_tblname_categories.*
        FROM $wpgmza_tblname_category_maps LEFT JOIN $wpgmza_tblname_categories

        ON $wpgmza_tblname_category_maps.cat_id = $wpgmza_tblname_categories.id  
        WHERE  ($wpgmza_tblname_category_maps.map_id = 0 OR $wpgmza_tblname_category_maps.map_id = $map_id) 
        AND $wpgmza_tblname_categories.`active` = $active
       ";
        
        $results = $wpdb->get_results($sql);
    }
    return $results;
}


/**
 * Returns an array of category data for the specific map (parents and children)
 * @param  intval $map_id Map ID
 * @return array
 */
function wpgmza_get_category_localized_data( $map_id ) {
    if ( !$map_id )
        return;

    
    $cat_data = wpgmza_return_all_categories( $map_id, 0 );
    return $cat_data;

}