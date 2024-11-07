<?php
/**
 * @class Vehicle_Fields
 */
if (!class_exists('Vehicle_Fields', false)) {


    class Vehicle_Fields {
    
        public static function init() {
            add_action('admin_menu', array(__CLASS__, 'add_custom_field_menu'));
            add_action('save_post', array(__CLASS__, 'save_vehicle_meta_data'));
        }
    
        public static function add_custom_field_menu() {

            add_meta_box(
                'vehicle_images_box',
                __( 'Vehicle Images', 'textdomain' ),
                array(__CLASS__, 'render_vehicle_images_meta_box' ),
                'vehicle', // Custom post type slug
                'normal',
                'high'
            );

            add_meta_box(
                'vehicle_custom_fields',
                'Vehicle Details',
                array(__CLASS__, 'render_vehicle_custom_fields'),
                'vehicle',
                'normal',
                'core'
            );
        }

        public static 
        function render_vehicle_images_meta_box( $post ) {
            wp_nonce_field( 'save_vehicle_images', 'vehicle_images_nonce' );
        
            $vehicle_images = get_post_meta( $post->ID, '_vehicle_images', true );
            ?>
            <div>
                <a href="#" class="button" id="upload_vehicle_images"><?php _e( 'Add Images', 'textdomain' ); ?></a>
                <ul id="vehicle-images-list">
                    <?php
                    if ( ! empty( $vehicle_images ) ) {
                        foreach ( $vehicle_images as $image_id ) {
                            echo '<li><img src="' . wp_get_attachment_image_url( $image_id, 'thumbnail' ) . '" style="max-width: 100px;"><input type="hidden" name="vehicle_images[]" value="' . esc_attr( $image_id ) . '"><a href="#" class="remove-image">Remove</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <script>
            jQuery(document).ready(function($) {
                var frame;
                $('#upload_vehicle_images').on('click', function(e) {
                    e.preventDefault();
        
                    if ( frame ) {
                        frame.open();
                        return;
                    }
        
                    frame = wp.media({
                        title: 'Select or Upload Images',
                        button: { text: 'Use these images' },
                        multiple: true
                    });
        
                    frame.on('select', function() {
                        var attachments = frame.state().get('selection').map(function(attachment) {
                            attachment = attachment.toJSON();
                            return attachment.id;
                        });
        
                        attachments.forEach(function(attachment_id) {
                            var image_url = wp.media.attachment(attachment_id).get('url');
                            $('#vehicle-images-list').append('<li><img src="'+image_url+'" style="max-width: 100px;"><input type="hidden" name="vehicle_images[]" value="'+attachment_id+'"><a href="#" class="remove-image">Remove</a></li>');
                        });
                    });
        
                    frame.open();
                });
        
                $(document).on('click', '.remove-image', function(e) {
                    e.preventDefault();
                    $(this).parent().remove();
                });
            });
            </script>
            <?php
        }
    
        public static function render_vehicle_custom_fields($post) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_custom_fields'; // Custom table name

            $feature_image = get_post_meta($post->ID, 'feature_image', true);
            $images = get_post_meta($post->ID, 'images', true);

        
            // Fetch custom fields from the database
            $custom_fields = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $field) {
                    echo '<div class="form-group">'; // Bootstrap form group wrapper
        
                    // Display label
                    echo '<label for="vehicle_' . esc_attr($field['slug']) . '">' . esc_html($field['field_label']) . '</label>';
        
                    // Switch case to render different field types
                    switch ($field['field_type']) {
                        case 'text':
                        case 'number':
                            // Text or number input with Bootstrap styling
                            echo '<input type="' . esc_attr($field['field_type']) . '" class="form-control" id="vehicle_' . esc_attr($field['slug']) . '" name="vehicle_' . esc_attr($field['slug']) . '" placeholder="' . esc_attr($field['placeholder']) . '">';
                            break;
        
                        case 'select':
                            // Select dropdown with Bootstrap styling
                            $options = implode(', ', maybe_unserialize($field['options']));
                            $options = explode(', ', $options);
                            echo '<select class="form-control" id="vehicle_' . esc_attr($field['slug']) . '" name="vehicle_' . esc_attr($field['slug']) . '">';
                            // $options = !empty($field['options']) ? explode(', ', maybe_unserialize($field['options'])) : 'N/A'; // Convert options to array
                            echo '<option value="">' . esc_html(trim($field['placeholder'])) . '</option>';
                            foreach ($options as $option) {
                                echo '<option value="' . esc_attr(trim($option)) . '">' . esc_html(trim($option)) . '</option>';
                            }
                            echo '</select>';
                            break;
        
                        case 'multiselect':
                            // Multi-select dropdown with Bootstrap styling
                            echo '<select class="form-control" id="vehicle_' . esc_attr($field['slug']) . '" name="vehicle_' . esc_attr($field['slug']) . '[]" multiple>';
                            $options = explode(',', $field['options']); // Convert options to array
                            foreach ($options as $option) {
                                echo '<option value="' . esc_attr(trim($option)) . '">' . esc_html(trim($option)) . '</option>';
                            }
                            echo '</select>';
                            break;
        
                        case 'file':
                            // File input with Bootstrap styling
                            echo '<input type="file" class="form-control-file" id="vehicle_' . esc_attr($field['slug']) . '" name="vehicle_' . esc_attr($field['slug']) . '">';
                            break;
                    }
        
                    echo '</div>'; // Close form group
                }
            } else {
                echo '<p>No custom fields available.</p>';
            }
        }
    
        public static function save_vehicle_meta_data($post_id) {
            if (get_post_type($post_id) != 'vehicle') {
                return; // Only apply for vehicle post type
            }
        
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_posts_meta';
        
            // Fetch post data
            $post = get_post($post_id);
            $post_title = $post->post_title;
            $post_category = get_the_category($post_id); // You can modify this according to your needs
            $post_status = $post->post_status;
            $post_date = $post->post_date;
            $post_content = $post->post_content;
        
            // Prepare the data to insert/update
            $data = [
                'post_id' => $post_id,
                'post_title' => $post_title,
                'post_category' => isset($post_category[0]->name) ? $post_category[0]->name : '',
                'post_status' => $post_status,
                'post_date' => $post_date,
                'post_content' => $post_content,
                'feature_image' => isset($_POST['feature_image']) ? sanitize_text_field($_POST['feature_image']) : '',
                'images' => isset($_POST['images']) ? maybe_serialize($_POST['images']) : '' // Serialized array for multiple images
            ];
        
            // Fetch custom fields and append their values to the data array
            $field_table_name = $wpdb->prefix . 'vehicle_custom_fields'; 
            $custom_fields = $wpdb->get_results("SELECT * FROM $field_table_name", ARRAY_A);
        
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $field) {
                    $slug = $field['slug'];
                    $value = isset($_POST['vehicle_' . $slug]) ? sanitize_text_field($_POST['vehicle_' . $slug]) : '';

                    if($value){
                        update_post_meta($post_id, 'vehicle_' . $slug, $value);
                    }
                    $data[$slug] = $value;
                }
            }

            // echo '<pre>';
            // print_r($data);
            // echo '</pre>';die;
        
            // Check if the post already exists in the vehicle_posts_meta table
            $existing_post = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $table_name WHERE post_id = %d", $post_id));
        
            if ($existing_post) {
                // Update the existing post meta
                $wpdb->update(
                    $table_name,
                    $data,
                    ['post_id' => $post_id]
                );
            } else {
                // Insert new post meta
                $wpdb->insert(
                    $table_name,
                    $data
                );
            }

            // Save vehicle images.
            if ( isset( $_POST['vehicle_images'] ) ) {
                $vehicle_images = array_map( 'intval', $_POST['vehicle_images'] );
                update_post_meta( $post_id, '_vehicle_images', $vehicle_images );
            } else {
                delete_post_meta( $post_id, '_vehicle_images' );
            }
        }
    }    
    /*
     * Initialize class init method for load its functionality.
     */
    Vehicle_Fields::init();
}
?>