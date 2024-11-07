<?php
function display_vehicle_images( $post_id ) {
    $vehicle_images = get_post_meta( $post_id, '_vehicle_images', true );

    if ( ! empty( $vehicle_images ) ) {
        echo '<div class="vehicle-gallery">';
        foreach ( $vehicle_images as $image_id ) {
            $image_url = wp_get_attachment_image_url( $image_id, 'large' );
            echo '<img src="' . esc_url( $image_url ) . '" alt="">';
        }
        echo '</div>';
    }
}

function render_dynamic_vehicle_filters() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'vehicle_custom_fields';

    // Fetch custom fields from the custom table
    $custom_fields = $wpdb->get_results("SELECT * FROM $table_name");

    if (!$custom_fields) {
        return; // Exit if no custom fields are available
    }

    // print_r($custom_fields);die;
    echo '<div class="container filter-container">';
    echo '<div class="row">';

    foreach ($custom_fields as $field) {
        $field_type = esc_attr($field->field_type);
        $field_slug = esc_attr($field->slug);
        $field_label = esc_html($field->field_label);
        $field_options = '';
        if($field->options){
            $options = implode(', ', maybe_unserialize($field->options));
            $field_options = explode(', ', $options);
        }
        // $field_options = !empty($field->options) ? explode(',', $field->options) : [];
        $min = isset($field->min) ? intval($field->min) : 0;
        $max = isset($field->max) ? intval($field->max) : 100;
        $step = isset($field->step) ? intval($field->step) : 1;

        $current_value = isset($_GET['vehicle_' . $field_slug]) ? $_GET['vehicle_' . $field_slug] : '';

        echo '<div class="col-md-3 filter-section">';
        echo '<div class="filter-title">' . $field_label . '</div>';

        // Generate inputs based on field type
        switch ($field_type) {
            case 'select':
            case 'multiselect':
                echo '<div class="checkbox-list">';
                foreach ($field_options as $option) {
                    $option_value = esc_attr(trim($option));
                    $checked = (is_array($current_value) && in_array($option_value, $current_value)) ? 'checked' : '';
                    echo '<label class="checkbox-label">';
                    echo '<input type="checkbox" name="vehicle_' . $field_slug . '[]" value="' . $option_value . '"' . $checked . '> ' . esc_html($option);
                    echo '</label>';
                }
                echo '</div>';
                break;

            case 'number':
                echo '<div class="slider-wrapper">';
                echo '<div class="min-max text-uppercase">Min: <div class="text-white">' . $min . '</div></div>';
                echo '<div class="min-max text-uppercase">Max: <div class="text-white">' . $max . '</div></div>';
                echo '</div>';
                echo '<input type="range" class="range-slider" name="vehicle_' . $field_slug . '" min="' . $min . '" max="' . $max . '" step="' . $step . '" value="' . $min . '">';
                break;

            case 'text':
                echo '<input type="text" class="form-control" name="vehicle_' . $field_slug . '" placeholder="' . $field_label . '">';
                break;
        }

        echo '</div>'; // Close filter-section
    }

    echo '</div>'; // Close row
    echo '</div>'; // Close container
}
if ( ! function_exists( 'vehicle_get_post_meta' ) ) {
    function vehicle_get_post_meta( $post_id, $meta_key, $single = false ) {
        if ( ! $post_id ) {
            return false;
        }
        global $wpdb;
    
        $post_type = get_post_type( $post_id );
    
        if ( $post_type != 'vehicle' ) {
            return false;
        }
    
        $table = $wpdb->prefix .'vehicle_posts_meta';
    
        if ( $table && $meta_key ) {
            //if ( $wpdb->get_var( "SHOW COLUMNS FROM " . $table . " WHERE field = '" . $meta_key . "'" ) != '' ) {
            $meta_value = $wpdb->get_var( $wpdb->prepare( "SELECT `" . $meta_key . "` from " . $table . " where post_id = %d", array( $post_id ) ) );
    
            if ( ($meta_value || $meta_value==='0') && $meta_value !== '' ) {
                $meta_value = maybe_serialize( $meta_value );
            }else{
                $meta_value = false;
            }
        } else {
            $meta_value = false;
        }
    
        return $meta_value;
    }
}