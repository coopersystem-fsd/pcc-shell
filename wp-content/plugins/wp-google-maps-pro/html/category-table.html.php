<table id="wpgmza-category-table" class="wp-list-table widefat fixed striped pages">  
    <thead>      
        <tr>          
            <th scope="col" width="100px" id="id" class="manage-column column-id">
                <?php
                    esc_html_e("ID", "wp-google-maps");
                ?>
            </th>          
            <th scope="col" id="cat_cat" class="manage-column column-map_title">
                <?php
                    esc_html_e("Category", "wp-google-maps");
                ?>
            </th>          
            <th scope="col" id="cat_icon" class="manage-column column-map_width">
                <?php
                    esc_html_e("Icon", "wp-google-maps");
                ?>
            </th>          
            <th scope="col" id="cat_linked" class="manage-column column-map_width">
                <?php
                    esc_html_e("Linked Maps", "wp-google-maps");
                ?>
            </th>          
            <th scope="col" id="cat_priority" class="manage-column column-map_width">
                <?php
                    esc_html_e("Priority", "wp-google-maps");
                ?>
            </th>      
        </tr>  
    </thead>
    
    <tbody id="the-list" class="list:wp_list_text_link">
        <tr >  
            <td class="id column-id" data-name="id"></td>  
            <td class="column-map_title">
                <strong>
                    <big>
                        <a title="Edit" data-name="category_name"></a>
                    </big>
                </strong>
                
                <br>
                
                <a title="Edit" class="wpgmza-edit-category">
                    <?php
                        esc_html_e("Edit", "wp-google-maps");
                    ?>
                </a>
                
                | <a title="Trash" class="wpgmza-trash-category">
                    <?php
                        esc_html_e("Trash", "wp-google-maps");
                    ?>
                </a>
                
            </td>  

            <td class="column-map_width">
                <img style="max-width:100px; max-height:100px;" data-name="category_icon">
            </td>  

            <td class="column-map_width wpgmza-category-map-names"></td>

            <td class="column-map_width" data-name="priority">
            </td>   

        </tr>
    </tbody>
</table>

