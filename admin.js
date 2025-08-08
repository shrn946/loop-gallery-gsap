jQuery(document).ready(function($) {
    // Add images
    $('#add-loop-gallery-images').on('click', function(e) {
        e.preventDefault();
        var frame = wp.media({
            title: 'Select or Upload Images',
            button: { text: 'Use these images' },
            multiple: true
        }).open().on('select', function() {
            var selection = frame.state().get('selection');
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                $('#loop-gallery-images').append(
                    '<div class="loop-gallery-item">' +
                    '<img src="' + attachment.url + '">' +
                    '<input type="hidden" name="loop_gallery_images[]" value="' + attachment.url + '">' +
                    '<span class="close-icon" title="Remove">&times;</span>' +
                    '</div>'
                );
            });
        });
    });

    // Remove image
    $(document).on('click', '.loop-gallery-item .close-icon', function() {
        $(this).closest('.loop-gallery-item').remove();
    });

    // Drag & Drop sorting
    $('#loop-gallery-images').sortable({
        items: '.loop-gallery-item',
        cursor: 'move',
        placeholder: 'sortable-placeholder',
        stop: function(event, ui) {
            // Automatically updates order in form submission
        }
    });
});
