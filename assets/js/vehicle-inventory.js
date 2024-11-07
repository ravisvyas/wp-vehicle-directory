jQuery(document).ready(function($) {
    // Example functionality for filtering or sorting (extend this as per your needs)

    // When a filter option is changed
    $('#vehicle-filter').on('change', function() {
        var filterValue = $(this).val();

        // Perform AJAX or other actions to filter the vehicle listings
        console.log("Filter selected: " + filterValue);

        // Example: refresh or fetch updated vehicle list via AJAX
        // $.ajax({
        //     url: '/wp-admin/admin-ajax.php',
        //     type: 'POST',
        //     data: {
        //         action: 'filter_vehicles',
        //         filter: filterValue
        //     },
        //     success: function(response) {
        //         // Update the vehicle list on the page with the response
        //         $('.vehicle-gallery').html(response);
        //     }
        // });
    });
});

jQuery(document).ready(function($) {
    var frame;

    // Feature Image Uploader
    $('.upload-image-button').on('click', function(e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select or Upload Feature Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#feature_image').val(attachment.url); // You can save the attachment ID if preferred
        });

        frame.open();
    });

    // Multiple Images Uploader
    $('.upload-images-button').on('click', function(e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select or Upload Images',
            button: {
                text: 'Use these images'
            },
            multiple: true // Allow multiple image selection
        });

        frame.on('select', function() {
            var attachments = frame.state().get('selection').map(function(attachment) {
                return attachment.toJSON().url; // You can use attachment.id if needed
            });

            $('#images').val(attachments.join(',')); // Store as comma-separated URLs
        });

        frame.open();
    });
});