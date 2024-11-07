<?php
/*
 * Plugin Name: Wordpress Vehicle Inventory Plugin
 * Description: This will include customization related to develop features for vehicle inventory web app in WP.
 * Author: Ravi S Vyas
 * Version: 1.0.0
 * Author URI: https://fusionwebexperts.tech
 * License: GPL2
 * Text Domain: https://fusionwebexperts.tech
 */
defined( 'ABSPATH' ) || exit;
/**
 * @class VehicleInventory
 */
if (!class_exists('VehicleInventory', false)) {

    class VehicleInventory {

        /**
         * Initialize required action and filters
         * @return void accommodation_supplement
         */
        public function __construct() {

            //include Backend all 
            include_once self::get_plugin_dir_path() . '/general-functions.php';
            include_once self::get_plugin_dir_path() . '/includes/class-vehicle-post-type.php';
            include_once self::get_plugin_dir_path() . '/includes/class-vehicle-fields.php';
            include_once self::get_plugin_dir_path() . '/includes/class-vehicle-filters.php';
            include_once self::get_plugin_dir_path() . '/includes/class-vehicle-shortcodes.php';

            // enqueue js and css files
            add_action('wp_enqueue_scripts', array( __CLASS__, 'vip_enqueue_assets' ) );
            add_action('admin_enqueue_scripts', array( __CLASS__, 'vip_enqueue_assets' ) );
            add_filter('template_include', array( __CLASS__, 'vip_template_include' ) );

            register_activation_hook(__FILE__, array( __CLASS__,'vip_flush_rewrite_rules' ) );
            add_action('init', array( __CLASS__,'create_vehicle_custom_fields_table' ) );

        }

        public static function create_vehicle_custom_fields_table() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'vehicle_custom_fields';
        
            // Check if the table already exists
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $charset_collate = $wpdb->get_charset_collate();
        
                // SQL to create the table
                $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    field_label varchar(255) NOT NULL,
                    slug VARCHAR(255) NOT NULL,
                    placeholder varchar(255),
                    icon varchar(255),
                    field_type varchar(50) NOT NULL,
                    options text DEFAULT NULL,
                    is_active TINYINT(1) DEFAULT 1,
                    search_bar_active TINYINT(1) DEFAULT 0,
                    created_at datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id)
                ) $charset_collate;";
        
                // Include the upgrade script and run dbDelta to create the table
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

            $table_name = $wpdb->prefix . 'vehicle_posts_meta'; 
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $charset_collate = $wpdb->get_charset_collate();
        
                // SQL to create the table
                $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    post_id BIGINT(20) UNSIGNED NOT NULL,
                    post_title VARCHAR(255) NOT NULL,
                    post_category VARCHAR(255),
                    post_status VARCHAR(20),
                    post_date DATETIME,
                    post_content LONGTEXT,
                    feature_image VARCHAR(255), /* Column to store the URL or ID of the feature image */
                    images LONGTEXT, /* Column to store multiple images (serialized array of URLs or IDs) */
                    PRIMARY KEY (post_id)
                ) $charset_collate;";
        
                // Include the upgrade script and run dbDelta to create the table
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }
        
        public static function vip_flush_rewrite_rules() {
            Vehicle_Post_Type::register_vehicle_post_type();
            flush_rewrite_rules();
        }

        // Enqueue CSS and JS files
        public static function vip_enqueue_assets() {
            wp_enqueue_style('vehicle-inventory-css', plugin_dir_url(__FILE__) . 'assets/css/vehicle-inventory.css', array(), '1.0.0', 'all');
            wp_enqueue_script('vehicle-inventory-js', plugin_dir_url(__FILE__) . 'assets/js/vehicle-inventory.js', array('jquery'), '1.0.0', true);
            // Enqueue Bootstrap CSS (CDN or Local)
            wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
             // Font Awesome CSS (CDN)
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

            // Enqueue Bootstrap JS and Popper.js for dropdowns (CDN or Local)
            wp_enqueue_script('jquery'); // Ensure jQuery is loaded
            wp_enqueue_script('popper-js', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js', array(), null, true);
            wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery', 'popper-js'), null, true);
    
        }
        
        // single and archive files
        public static function vip_template_include($template) {
            if (is_singular('vehicle')) {
                // Load single vehicle template from plugin.
                $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-vehicle.php';
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            } elseif (is_post_type_archive('vehicle')) {
                // Load archive vehicle template from plugin.
                $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-vehicle.php';
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
        
            return $template;
        }

        /**
         * Get plugin file path
         * @return system
         */
        public static function get_plugin_file_path() {
            return __FILE__;
        }

        /**
         * Get plugin dir path.
         * @return type
         */
        public static function get_plugin_dir_path() {
            return dirname(__FILE__);
        }

        /**
         * Get plugin url.
         * @return type
         */
        public static function get_plugin_url() {
            return plugin_dir_url(__FILE__);
        }
        
    }//class end

}//class condition end  

/*
 * Initialize class init method for load its functionality.
 */
function wpvi_load_plugin()
{
    // Initialize dependency injection.
    $GLOBALS['wpvi'] = new VehicleInventory();
}
add_action('plugins_loaded', 'wpvi_load_plugin');
?>