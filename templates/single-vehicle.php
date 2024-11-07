<?php 
get_header(); ?>
<!-- <div class="vehicle-single">
    <h1><?php the_title(); ?></h1>
    <?php
    $make = get_post_meta(get_the_ID(), 'vehicle_make', true);
    $model = get_post_meta(get_the_ID(), 'vehicle_model', true);
    $price = get_post_meta(get_the_ID(), 'vehicle_price', true);
    $mileage = get_post_meta(get_the_ID(), 'vehicle_mileage', true);
    
    the_post_thumbnail('large');
    ?>
    <p><strong>Make:</strong> <?php echo esc_html($make); ?></p>
    <p><strong>Model:</strong> <?php echo esc_html($model); ?></p>
    <p><strong>Price:</strong> $<?php echo esc_html($price); ?></p>
    <p><strong>Mileage:</strong> <?php echo esc_html($mileage); ?> miles</p>
    <div class="vehicle-description">
        <?php the_content(); ?>
    </div>
</div> -->
<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
<section id="Details-page">
        <div class="container">
            <div class="row">
                <div class="col-md-6 position-relative">
                    <figure class="mb-0 h-100"><img src="<?php echo $image[0]; ?>" alt="" class="h-100 w-100"></figure>
                    <button class="btn text-white fw-medium bg-blur opecity-0 gallery-btn"><i class="fa fa-image me-2"></i>Go to gallery</button>
                </div>
                <div class="col-md-6">
                    <div class="border-success bg-light top-heading"><?php the_title(); ?></div>
                    <div class="details-content-box px-4 bg-light border border-secondary">
                    <div class="row py-4 border-bottom border-secondry">
                        <div class="col-6">
                            <div class="seller-asking" >Make:</div>
                            <h5 class="price fw-bold"><?php echo esc_html($make); ?></h5>
                        </div>
                        <div class="col-6">
                            <div class="seller-asking" >Model:</div>
                            <h5 class="price fw-bold"><?php echo esc_html($model); ?></h5>
                        </div>
                        <div class="col-6">
                            <div class="seller-asking" >Mileage:</div>
                            <h5 class="price fw-bold"><?php echo esc_html($mileage); ?></h5>
                        </div>
                        <div class="col-6">
                            <div class="seller-asking" >price:</div>
                            <h5 class="price fw-bold"><?php echo esc_html($price); ?></h5>
                        </div>
                    </div>
                    <div class="mt-4 mb-2">
                        <a href="#" class="btn btn-success text-white rounded-5 d-block mb-3 fw-semibold">Send Enquiry</a>
                        <!-- <a href="#" class="btn btn-outline-success rounded-5 d-block fw-semibold">Buy It Now</a> -->
                </div>
                <div class="row">
                    <!-- <div class="col-6">
                        <div class="btn-group btn-group-toggle" data-bs-toggle="buttons">
                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off" checked>
                            <label class="btn border-0  active" for="option1">USD</label>
                            <input type="radio" class="btn-check" name="options" id="option2" autocomplete="off">
                            <label class="btn border-0" for="option2">EUR</label>
                          </div>
                    </div>
                    <div class="col-6">
                        <p class="offer text-end fw-medium text-secondary" ><span>~</span> 1 Offer</p>
                    </div> -->
                </div>
                </div>
            </div>
        </div>
        </div>
        </div>


        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light border-bottom border-light pb-0">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                  <div class="navbar-nav">
                    <a class="nav-item nav-link fw-medium active" href="#">Offer history<span class="sr-only">(current)</span></a>
                    <a class="nav-item nav-link fw-medium" href="#">Details</a>
                    <a class="nav-item nav-link fw-medium" href="#">Photos</a>
                  </div>
                </div>
              </nav>

              <div class="offer-history py-5">
                <div class="offer-history-box mb-4">
                <h3 class="fw-bold">Offer History</h3>

                <div class="d-flex justify-content-end">
                    
                  </div>
            </div>
                <div class="table-content">
                <?php the_content(); ?>
                </div>
            </div>
            <div class="details py-5">
                <div class="row align-items-center">
                    <div class="col-md-7">
                <h3 class=" fw-bold">Details</h3>
            </div>
            
            </div>
            <div class="table-deatils responsive table py-5">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table border-bottom">
                            <tbody>
                                <tr class="py-2">
                                  <td class="h-44">Make</td>
                                  <td> <strong><?php echo esc_html($make); ?></strong></td>
                                </tr>
                                <tr class="h-44">
                                  <td>Mileage</td>
                                  <td><strong><?php echo esc_html($mileage); ?></strong></td>
                                </tr>
                                
                              </tbody>
                            </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table border-bottom">
                            <tbody>
                                <tr class="h-44">
                                  <td>Price</td>
                                  <td><strong><?php echo esc_html($price); ?></strong></td>
                                </tr>
                                <tr class="h-44">
                                  <td>Model</td>
                                  <td><strong><?php echo esc_html($model); ?></strong></td>
                                </tr>
                              </tbody>
                            </table>
                    </div>
                </div>

            </div>
            
            <div class="significanse about-this-vehicle">
                <h6 class="mb-4"><strong>About this Vehicle</strong></h6>
                <p class=""><?php the_content(); ?></p>
                
            </div>
            </div>
        </div>

        <div class="py-5" id="photo-video">
            <div class="container">
            <h3 class="fw-bold">Photo & Video</h3>
            <div class="mt-5">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-bs-toggle="tab" href="#all">All</a>
                  </li>
                </ul>
            </div>
       </div>

                <!-- Tab Content -->
              <div class="tab-content mt-3">
                  <!-- All Images -->
                  <div class="tab-pane fade show active" id="all">
                      <div class="row image-grid">
                         <?php
                            // Get vehicle images meta
                            $vehicle_images = get_post_meta( get_the_ID(), '_vehicle_images', true );

                            if ( ! empty( $vehicle_images ) ) :
                                foreach ( $vehicle_images as $image_id ) :
                                    $image_url = wp_get_attachment_image_url( $image_id, 'medium' ); // Get medium size image
                                    ?>
                                    <div class="col-md-4 mb-4">
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title(); ?>">
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                          ?>
                      </div>
                  </div>
              </div>
        </div>
    </section>
<?php
get_footer();
?>