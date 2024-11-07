<?php
/**
 * @class Vehicle_Filters
 */
if (!class_exists('Vehicle_Filters', false)) {

    class Vehicle_Filters {
    
        public static function init() {
            // add_action('pre_get_posts', array(__CLASS__, 'modify_vehicle_query'));
            add_filter('posts_join', array(__CLASS__, 'join_vehicle_meta_table'));
            add_filter('posts_where', array(__CLASS__, 'filter_vehicle_meta'));

            // Add this filter to print the query
            // add_filter('posts_request', array(__CLASS__, 'print_query'));
        }

        // Function to print the SQL query
        public static function print_query($query) {
            if (!is_admin() && is_post_type_archive('vehicle')) {
                echo '<pre>';
                echo esc_html($query); // Escape the query for security
                echo '</pre>';
            }

            return $query; // Return the query unchanged
        }

        public static function modify_vehicle_query($query) {

            // echo '<pre>';
            // print_r($query);
            // echo '</pre>';
            if (!is_admin() && $query->is_main_query() && is_post_type_archive('vehicle')) {
        
                // Sorting by price, mileage, or year
                if (isset($_GET['sort'])) {
                    switch ($_GET['sort']) {
                        case 'price':
                            $query->set('meta_key', 'vehicle_price');
                            $query->set('orderby', 'meta_value_num');
                            break;
                        case 'mileage':
                            $query->set('meta_key', 'vehicle_mileage');
                            $query->set('orderby', 'meta_value_num');
                            break;
                        case 'year':
                            $query->set('orderby', 'date');
                            break;
                    }
                }
            }
        }
    
        // Join custom table with main posts table
        public static function join_vehicle_meta_table($join) {
            global $wpdb;

            if (is_post_type_archive('vehicle')) {
                $join .= " LEFT JOIN {$wpdb->prefix}vehicle_posts_meta ON {$wpdb->posts}.ID = {$wpdb->prefix}vehicle_posts_meta.post_id ";
            }

            // echo $join;

            return $join;
        }

        // Add filtering logic for custom fields
        public static function filter_vehicle_meta($where) {
            global $wpdb;

            // print_r($_GET);
            if (is_post_type_archive('vehicle')) {
                // Fetch active custom fields for the search bar
                $custom_fields = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}vehicle_custom_fields WHERE search_bar_active = 1 AND is_active = 1");

                // Loop through the custom fields and check for corresponding GET parameters
                foreach ($custom_fields as $field) {
                    $slug = 'vehicle_'.$field->slug;
                    $slug_main = $field->slug;
                    $field_type = $field->field_type;

                    // Check if the field is set in the URL (GET request)
                    if (isset($_GET[$slug]) && !empty($_GET[$slug])) {
                        $value = esc_sql( $_GET[$slug] );

                        // Apply conditions based on field type
                        switch ($field_type) {
                            case 'select': // For dropdown fields, use IN clause
                                $where .= " AND {$wpdb->prefix}vehicle_posts_meta.{$slug_main} IN ('" . implode("', '", (array)$value) . "')";
                                break;

                            case 'number': // For numeric fields, use BETWEEN if a range is provided
                                if (is_array($value) && count($value) == 2) {
                                    $where .= " AND {$wpdb->prefix}vehicle_posts_meta.{$slug_main} BETWEEN {$value[0]} AND {$value[1]}";
                                } else {
                                    $where .= " AND {$wpdb->prefix}vehicle_posts_meta.{$slug_main} = {$value}";
                                }
                                break;

                            case 'text': // For text fields, use LIKE for partial matches
                                $where .= " AND {$wpdb->prefix}vehicle_posts_meta.{$slug_main} LIKE '%{$value}%'";
                                break;
                        }
                    }
                }
            }

            // echo $where;

            return $where;
        }
    }   
    /*
     * Initialize class init method for load its functionality.
     */
    Vehicle_Filters::init();
}
?>