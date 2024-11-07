<?php
/**
 * @class Vehicle_Shortcodes
 */
if (!class_exists('Vehicle_Shortcodes', false)) {

    class Vehicle_Shortcodes {
    
        public static function init() {
            add_shortcode('vehicle_inventory', array(__CLASS__, 'render_vehicle_inventory'));
        }
    
        public static function render_vehicle_inventory() {
            ob_start();
    
            $args = array(
                'post_type' => 'vehicle',
                'posts_per_page' => -1,
            );
            $vehicles = new WP_Query($args);
    
            if ($vehicles->have_posts()) {
                echo '<div class="vehicle-gallery">';
                while ($vehicles->have_posts()) {
                    $vehicles->the_post();
                    $make = get_post_meta(get_the_ID(), '_vehicle_make', true);
                    $model = get_post_meta(get_the_ID(), '_vehicle_model', true);
                    $price = get_post_meta(get_the_ID(), '_vehicle_price', true);
                    $mileage = get_post_meta(get_the_ID(), '_vehicle_mileage', true);
                    ?>
                        <div id="card" class="card-body">
                            <div class="image-box">
                            <img src="https://images.pexels.com/photos/7343049/pexels-photo-7343049.jpeg" alt="" class="h-100 w-100">
                            </div>
                            <div class="content-box bg-primary d-flex justify-content-between px-4 py-2 align-items-center">
                            <div class="left-content">
                                <h5 class="heading fw-medium text-white">Lorem ipsum dolor sit.</h5>
                                <div class="short-text text-white"><?php echo esc_html($mileage); ?></div>
                            </div>
                            <h2 class="prices text-white fw-bold me-2">$<?php echo esc_html($price); ?></h2>
                            </div>
                        </div>
                    <?php
                    echo '<div class="vehicle-item">';
                    the_post_thumbnail('medium');
                    echo '<h3>' . esc_html($make) . ' ' . esc_html($model) . '</h3>';
                    echo '<p>Price: $' . esc_html($price) . '</p>';
                    echo '<p>Mileage: ' . esc_html($mileage) . ' miles</p>';
                    echo '<a href="' . get_permalink() . '">View Details</a>';
                    echo '</div>';
                }
                echo '</div>';
            }
    
            wp_reset_postdata();
            return ob_get_clean();
        }
    } 
    
    /*
     * Initialize class init method for load its functionality.
     */
    Vehicle_Shortcodes::init();
}
?>