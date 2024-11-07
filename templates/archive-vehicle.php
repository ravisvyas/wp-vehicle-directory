<?php 
get_header(); ?>
<div class="container">
    <div class="row justify-content-end mb-3">
        <!-- Filter Icon -->
        <div class="col-auto">
            <i class="fas fa-filter icon-btn" id="filterIcon" data-bs-toggle="modal" data-bs-target="#filterModal"></i>
        </div>
        <!-- Sort Icon -->
        <div class="col-auto">
            <i class="fas fa-sort icon-btn"></i>
        </div>
    </div>
    <div class="row">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="col-md-4 mb-4">
                    <div id="card" class="card-body">
                        <a href="<?php the_permalink(); ?>">
                          <div class="image-box">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('class' => 'image-box')); ?>
                            <?php endif; ?>
                            </div>
                        </a>
                        <div class="content-box bg-primary d-flex justify-content-between px-3 py-2 align-items-center position-relative">
                          <div class="left-content">
                              <h5 class="card-title"><?php the_title(); ?></h5>
                              <div class="short-text text-white"><?php echo vehicle_get_post_meta(get_the_ID(), 'mileage', true); ?></div>
                            </div>
                            <h2 class="prices text-white fw-bold  me-2">$<?php echo vehicle_get_post_meta(get_the_ID(), 'price', true); ?></h2>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php else : ?>
            <div class="col-12">
                <div class="no-results-card text-center p-5 bg-light shadow rounded">
                    <div class="icon-box mb-3">
                        <i class="fas fa-search-minus" style="font-size: 48px; color: #e74c3c;"></i>
                    </div>
                    <h3 class="mb-3">No Results Found</h3>
                    <p class="text-muted mb-4">Sorry, we couldn't find any vehicles that match your search criteria. Try adjusting your filters or search terms.</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
            <?php endif; ?>
    </div>
</div>



<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form method="GET" action="<?php echo esc_url(home_url('/vehicle/')); ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filters</h5>
          <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close"><span class="dashicons dashicons-no-alt"></span></button>
        </div>
        <div class="modal-body">
          <?php render_dynamic_vehicle_filters(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
jQuery(document).ready(function(){
            jQuery('#filterIcon').click(function() {
                jQuery('#filterModal').modal('show');
            });
        });
</script>

<?php

get_footer();