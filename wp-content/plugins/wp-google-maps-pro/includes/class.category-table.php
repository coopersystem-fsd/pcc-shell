<?php

namespace WPGMZA;

class CategoryTable extends DOMDocument
{
    public function __construct()
    {

        DOMDocument::__construct();

        $this->loadPHPFile(plugin_dir_path(WPGMZA_PRO_FILE) . 'html/category-table.html.php');

        $this->tree         = \WPGMZA\CategoryTree::createInstance();

        $this->template     = $this->querySelector("tbody > tr");

        $this->container    = $this->template->parentNode;

        $this->template->remove();

        $this->build($this->tree);

    }

	protected function getMapNames($node)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
		
		$qstr = "
			SELECT map_title 
			FROM $WPGMZA_TABLE_NAME_MAPS
			WHERE id IN
			(
				SELECT map_id
				FROM $WPGMZA_TABLE_NAME_CATEGORY_MAPS
				WHERE cat_id = %d
			)
			ORDER BY map_title
		";
		
		$stmt = $wpdb->prepare($qstr, array($node->id));
		
		return implode(", ", $wpdb->get_col($stmt));
	}

    protected function getMapLinks($node)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MAPS;
		global $WPGMZA_TABLE_NAME_CATEGORY_MAPS;
        
        $qstr = " SELECT id, map_title
        FROM $WPGMZA_TABLE_NAME_MAPS
        WHERE id IN (
            SELECT map_id
            FROM $WPGMZA_TABLE_NAME_CATEGORY_MAPS
            WHERE cat_id = %d )
        ";
		
        $stmt = $wpdb->prepare($qstr, array($node->id));
		
        $results = $wpdb->get_results($stmt);
        if (empty($results) || $results == "")
        {
            return "<a href='" . esc_attr(admin_url('?page=wp-google-maps-menu')) . "'>" . esc_html(__("All maps","wp-google-maps")) . "</a>";
        }

        $str = "";
        
        foreach($results as $obj)
        {       
            $link = admin_url("admin.php?page=wp-google-maps-menu&action=edit&map_id={$obj->id}");     
            $title = $obj->map_title;

            $str .= "<a href='" . esc_attr($link) . "'>" . esc_html($title) . "</a>";
        }

        return $str;
    }

    protected function build($node)
    {
        foreach($node->children as $child)
        {
            // Do the work here
            $row = $this->template->cloneNode(true);
            $row->populate($child);
			
			$row->querySelector("[data-name='category_icon']")->setAttribute("src", $child->category_icon->url);

            // Attributes
            $row->setAttribute('id', "record_($child->id)");

            $link = "?page=wp-google-maps-menu-categories&action=edit&cat_id={$child->id}";
            foreach($row->querySelectorAll('a.wpgmza-edit-category') as $a)
                $a->setAttribute('href', $link);

            $link = "?page=wp-google-maps-menu-categories&action=trash&cat_id={$child->id}";
            $row->querySelector('a.wpgmza-trash-category')->setAttribute('href', $link);

			// Map names
            $map_names = $this->getMapLinks($child);
            $row->querySelectorAll(".wpgmza-category-map-names")->import($map_names);
          

            // Dashes
            $depth = $child->getDepth() - 1;

            $dashes = str_repeat('—', $depth) . " ";

            $text = $this->createTextNode($dashes);
            
            $row->querySelector('.column-map_title')->prepend($text);

            $this->container->appendChild($row);

            // Now recurse into the child node
            $this->build($child);

        }



    }
}
