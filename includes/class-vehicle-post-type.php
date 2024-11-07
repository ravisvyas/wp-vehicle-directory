<?php
/**
 * @class Vehicle_Post_Type
 */
if (!class_exists('Vehicle_Post_Type', false)) {

    class Vehicle_Post_Type {

        public static function init() {
            add_action('init', array(__CLASS__, 'register_vehicle_post_type'));
            add_action('init', array(__CLASS__, 'register_vehicle_taxonomy'), 0);
            add_action('admin_menu', array(__CLASS__, 'custom_fields_submenu') );
            add_action('wp_ajax_add_custom_vehicle_field', array(__CLASS__, 'add_custom_vehicle_field') );
            add_action('wp_ajax_get_custom_field', array(__CLASS__, 'get_custom_field') );
            add_action('wp_ajax_update_custom_field', array(__CLASS__, 'update_custom_field') );

        }

        public static function custom_fields_submenu() {
            add_submenu_page(
                'edit.php?post_type=vehicle', // Parent slug (replace 'vehicle' with your post type)
                'Manage Custom Fields', 
                'Custom Fields', 
                'manage_options', 
                'custom-fields', 
                array( __CLASS__, 'render_custom_fields_page')
            );
        }

        public static function register_vehicle_post_type() {
            $labels = array(
                'name'                  => _x('Vehicles', 'Post type general name', 'textdomain'),
                'singular_name'         => _x('Vehicle', 'Post type singular name', 'textdomain'),
                'menu_name'             => _x('Vehicles', 'Admin Menu text', 'textdomain'),
                'name_admin_bar'        => _x('Vehicle', 'Add New on Toolbar', 'textdomain'),
                'add_new'               => __('Add New', 'vehicle', 'textdomain'),
                'add_new_item'          => __('Add New Vehicle', 'textdomain'),
                'new_item'              => __('New Vehicle', 'textdomain'),
                'edit_item'             => __('Edit Vehicle', 'textdomain'),
                'view_item'             => __('View Vehicle', 'textdomain'),
                'all_items'             => __('All Vehicles', 'textdomain'),
                'search_items'          => __('Search Vehicles', 'textdomain'),
                'parent_item_colon'     => __('Parent Vehicles:', 'textdomain'),
                'not_found'             => __('No vehicles found.', 'textdomain'),
                'not_found_in_trash'    => __('No vehicles found in Trash.', 'textdomain'),
                'featured_image'        => _x('Vehicle Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
                'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'archives'              => _x('Vehicle archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
                'insert_into_item'      => _x('Insert into vehicle', 'Overrides the “Insert into post”/“Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
                'uploaded_to_this_item' => _x('Uploaded to this vehicle', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
                'filter_items_list'     => _x('Filter vehicles list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/“Filter pages list”. Added in 4.4', 'textdomain'),
                'items_list_navigation' => _x('Vehicles list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/“Pages list navigation”. Added in 4.4', 'textdomain'),
                'items_list'            => _x('Vehicles list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/“Pages list”. Added in 4.4', 'textdomain'),
            );
    
            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array('slug' => 'vehicle'),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'show_in_rest'       => false, // Disable Gutenberg editor
                'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            );
    
            register_post_type('vehicle', $args);
        }

        public static function register_vehicle_taxonomy() {
            $labels = array(
                'name'              => _x('Vehicle Categories', 'taxonomy general name', 'textdomain'),
                'singular_name'     => _x('Vehicle Category', 'taxonomy singular name', 'textdomain'),
                'search_items'      => __('Search Vehicle Categories', 'textdomain'),
                'all_items'         => __('All Vehicle Categories', 'textdomain'),
                'parent_item'       => __('Parent Vehicle Category', 'textdomain'),
                'parent_item_colon' => __('Parent Vehicle Category:', 'textdomain'),
                'edit_item'         => __('Edit Vehicle Category', 'textdomain'),
                'update_item'       => __('Update Vehicle Category', 'textdomain'),
                'add_new_item'      => __('Add New Vehicle Category', 'textdomain'),
                'new_item_name'     => __('New Vehicle Category Name', 'textdomain'),
                'menu_name'         => __('Vehicle Categories', 'textdomain'),
            );
    
            $args = array(
                'hierarchical'      => true, // True for category-like taxonomy
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'vehicle-category'),
            );
    
            register_taxonomy('vehicle_category', array('vehicle'), $args);
        }
        
        public static function render_custom_fields_page() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_custom_fields';

            // Fetch all the custom fields from the table
            $custom_fields = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
            ?>
            <div class="container-fluid custom-fields-container">
                <div class="row">
                    <!-- Left Side: Form for Custom Field Settings -->
                    <div class="col-md-6">
                        <h3>Add Custom Fields</h3>
                        <div class="custom-field-settings">
                            <div class="form-group">
                                <label for="field_label">Field Label</label>
                                <input type="text" id="field_label" name="field_label" class="form-control" required placeholder="Enter field label" onkeyup="generateSlug()">
                            </div>
                            <div class="form-group">
                                <label for="field_slug">Field Slug (Auto-generated)</label>
                                <input type="text" class="form-control" id="field_slug" name="field_slug" readonly>
                            </div>

                            <div class="form-group">
                                <label for="placeholder">Placeholder</label>
                                <input type="text" id="placeholder" name="placeholder" class="form-control" placeholder="Enter placeholder">
                            </div>

                            <div class="form-group">
                                <label for="icon">Icon</label>
                                <input type="text" id="icon" name="icon" class="form-control" placeholder="Enter icon">
                            </div>

                            <div class="form-group">
                                <label for="field_type">Field Type</label>
                                <select id="field_type" name="field_type" class="form-control">
                                    <option value="text">Text</option>
                                    <option value="number">Number</option>
                                    <option value="select">Select</option>
                                    <option value="multiselect">Multi-Select</option>
                                    <option value="file">File</option>
                                </select>
                            </div>

                            <div class="form-group" id="select-options-group" style="display:none;">
                                <label for="select_options">Select Options (comma-separated)</label>
                                <input type="text" id="select_options" name="select_options" class="form-control" placeholder="Option1, Option2">
                            </div>

                            <!-- Is Active Checkbox -->
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>

                            <!-- Search Bar Active Checkbox -->
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="search_bar_active" name="search_bar_active">
                                <label class="form-check-label" for="search_bar_active">Search Bar Active</label>
                            </div>

                            <div class="form-group">
                                <button id="add-custom-field" type="submit" class="btn btn-primary">Add Field</button>
                                <button id="edit-custom-field" type="submit" class="btn btn-primary" style="display:none">Edit Field</button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: List of Custom Fields -->
                    <div class="right-side col-md-6">
                        <!-- List of custom fields with Edit button -->
                        <h3>Custom Fields</h3>
                        <div id="custom-field-list">
                            <ul>
                                <?php if (!empty($custom_fields)) : ?>
                                    <?php foreach ($custom_fields as $field) : ?>
                                        <li class="custom-field-box" id="field_<?php echo $field['id']; ?>">
                                            <strong><?php echo esc_html($field['field_label']); ?></strong> 
                                            <span class="field-type">(<?php echo esc_html($field['field_type']); ?>)</span>
                                            <div class="field-details">
                                                <p><strong>Placeholder:</strong> <?php echo esc_html($field['placeholder']); ?></p>
                                                <p><strong>Icon:</strong> <span class="icon-placeholder"><?php echo esc_html($field['icon']); ?></span></p>
                                                <p><strong>Options:</strong> <?php echo !empty($field['options']) ? implode(', ', maybe_unserialize($field['options'])) : 'N/A'; ?></p>
                                            </div>
                                            
                                            <!-- Edit Button -->
                                            <button class="edit-field" data-id="<?php echo $field['id']; ?>">Edit</button>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <li>No custom fields found.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function generateSlug() {
                    let label = document.getElementById("field_label").value;
                    let slug = label.trim().toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '-');
                    document.getElementById("field_slug").value = slug;
                }
                document.getElementById("field_type").addEventListener("change", function() {
                    if (this.value === "select" || this.value === "multiselect") {
                        document.getElementById("select-options-group").style.display = "block";
                    } else {
                        document.getElementById("select-options-group").style.display = "none";
                    }
                });
                jQuery(document).ready(function($) {
                    
                    $('#add-custom-field').on('click', function(e) {
                        e.preventDefault();

                        var fieldLabel = $('#field_label').val();
                        var field_slug = $('#field_slug').val();
                        var placeholder = $('#placeholder').val();
                        var icon = $('#icon').val();
                        var fieldType = $('#field_type').val();
                        var selectOptions = $('#select_options').val();
                        var is_active = $('#is_active').val();
                        var search_bar_active = $('#search_bar_active').val();

                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>', // Using the localized value
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'add_custom_vehicle_field',
                                field_label: fieldLabel,
                                placeholder: placeholder,
                                icon: icon,
                                field_type: fieldType,
                                select_options: selectOptions,
                                field_slug: field_slug,
                                is_active: is_active,
                                search_bar_active: search_bar_active
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.location.reload();
                                    var fields = response.data;
                                    var fieldList = '';

                                    $.each(fields, function(index, field) {
                                        var options = field.options ? field.options.join(', ') : 'N/A';
                                        fieldList += '<li>' + field.field_label + ' (' + field.field_type + ') - Placeholder: ' + field.placeholder + ', Icon: ' + field.icon + ', Options: ' + options + '</li>';
                                    });

                                    $('#custom-field-list').html('<ul>' + fieldList + '</ul>');
                                } else {
                                    alert('Error adding field');
                                }
                            },
                            error: function() {
                                alert('AJAX request failed');
                            }
                        });
                    });

                    // When clicking "Edit", populate the form with field data
                    $('.edit-field').on('click', function(e) {
                        e.preventDefault();
                        var fieldId = $(this).data('id');

                        // Fetch field data via AJAX
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'get_custom_field',
                                field_id: fieldId
                            },
                            success: function(response) {
                                if (response.success) {
                                    var field = response.data;

                                    console.log(field);

                                    // Populate the form with field data
                                    $('#field_id').val(field.id);
                                    $('#field_label').val(field.field_label);
                                    $('#placeholder').val(field.placeholder);
                                    $('#icon').val(field.icon);
                                    $('#field_type').val(field.field_type);
                                    $('#select_options').val(field.select_option);

                                    if(field.field_type == 'select' || field.field_type == 'multiselect'){
                                        $('#select-options-group').show();
                                    }

                                    // Show the "Update" button and hide the "Add" button
                                    $('#add-custom-field').hide();
                                    $('#edit-custom-field').show();
                                }
                            }
                        });
                    });

                    // Handle form submission for updating fields
                    $('#edit-custom-field').on('click', function(e) {
                        e.preventDefault();
                        var formData = $('#custom-field-form').serialize();

                        // Update the field via AJAX
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'update_custom_field',
                                form_data: formData
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('Field updated successfully');
                                    location.reload(); // Reload the page to update the list
                                } else {
                                    alert('Failed to update field');
                                }
                            }
                        });
                    });
                });
            </script>
            <?php
        
        }

        // Add the AJAX action for logged-in users
        public static function add_custom_vehicle_field() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_custom_fields';

            // Sanitize and retrieve form data
            $field_label = sanitize_text_field($_POST['field_label']);
            $slug = sanitize_text_field($_POST['field_slug']);
            $placeholder = sanitize_text_field($_POST['placeholder']);
            $icon = sanitize_text_field($_POST['icon']);
            $field_type = sanitize_text_field($_POST['field_type']);
            $select_options = isset($_POST['select_options']) ? sanitize_text_field($_POST['select_options']) : '';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $search_bar_active = isset($_POST['search_bar_active']) ? 1 : 0;

            // Serialize select options if they exist
            $options = !empty($select_options) ? maybe_serialize(explode(',', $select_options)) : '';

            // Insert data into the custom table
            $wpdb->insert(
                $table_name,
                [
                    'field_label' => $field_label,
                    'slug' => $slug, // Save the auto-generated slug
                    'placeholder' => $placeholder,
                    'icon' => $icon,
                    'field_type' => $field_type,
                    'options' => $select_options,
                    'is_active' => $is_active,
                    'search_bar_active' => $search_bar_active, // Save search bar active status
                ]
            );

            // Call the function to add the new column to the vehicle post table
            add_custom_field_column($slug);
            // Retrieve all custom fields after insertion
            $custom_fields = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

            // Send response back to AJAX
            wp_send_json_success($custom_fields);
        }

        public static function add_custom_field_column($slug) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_posts_meta';
        
            // Check if the column exists, if not, add it
            $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE '$slug'");
            if (empty($column_exists)) {
                // Add the new column
                $sql = "ALTER TABLE $table_name ADD $slug TEXT;";
                $wpdb->query($sql);
            }
        }

        public static function get_custom_field() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_custom_fields';
            $field_id = intval($_POST['field_id']);

            // Fetch the field by ID
            $field = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $field_id), ARRAY_A);

            $field['select_option'] = !empty($field['options']) ? implode(', ', maybe_unserialize($field['options'])) : 'N/A';

            if ($field) {
                wp_send_json_success($field);
            } else {
                wp_send_json_error('Field not found');
            }
        }

        public static function update_custom_field() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_custom_fields';

            parse_str($_POST['form_data'], $form_data);
            $field_id = intval($form_data['field_id']);

            // Prepare the updated data
            $updated_field = [
                'field_label' => sanitize_text_field($form_data['field_label']),
                'placeholder' => sanitize_text_field($form_data['placeholder']),
                'icon' => sanitize_text_field($form_data['icon']),
                'field_type' => sanitize_text_field($form_data['field_type']),
                'options' => isset($form_data['select_options']) ? maybe_serialize(explode(',', sanitize_text_field($form_data['select_options']))) : []
            ];

            // Update the field in the database
            $result = $wpdb->update($table_name, $updated_field, ['id' => $field_id]);

            if ($result !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error('Failed to update field');
            }
        }
        
    }

    /*
     * Initialize class init method for load its functionality.
     */
    Vehicle_Post_Type::init();
}
?>