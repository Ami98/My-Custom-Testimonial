<?php

// Shortcode: [testimonial_form]
function mct_testimonial_form_shortcode()
{
    ob_start(); ?>
    <form action="" method="post" enctype="multipart/form-data" class="mct-testimonial-form">
        <?php wp_nonce_field('mct_submit_testimonial', 'mct_nonce_field'); ?>
        <p>
            <label for="mct_name">Name *</label><br>
            <input type="text" name="mct_name" required />
        </p>
        <p>
            <label for="mct_testimonial">Your Testimonial *</label><br>
            <textarea name="mct_testimonial" rows="5" required></textarea>
        </p>
        <p>
            <label for="mct_image">Upload a Photo (optional)</label><br>
            <input type="file" name="mct_image" accept="image/*" />
        </p>
        <p>
            <input type="submit" name="mct_submit" value="Submit Testimonial" />
        </p>
    </form>
<?php

    if (isset($_POST['mct_submit'])) {
        mct_handle_testimonial_submission();
    }

    return ob_get_clean();
}
add_shortcode('testimonial_form', 'mct_testimonial_form_shortcode');

// Handle form submission
function mct_handle_testimonial_submission()
{
    if (
        !isset($_POST['mct_nonce_field']) ||
        !wp_verify_nonce($_POST['mct_nonce_field'], 'mct_submit_testimonial')
    ) {
        echo '<p>❌ Security check failed. Please refresh the page.</p>';
        return;
    }

    if (!isset($_POST['mct_name']) || !isset($_POST['mct_testimonial'])) return;

    $name = sanitize_text_field($_POST['mct_name']);
    $testimonial = sanitize_textarea_field($_POST['mct_testimonial']);

    $post_id = wp_insert_post([
        'post_type'    => 'testimonial',
        'post_title'   => $name,
        'post_content' => $testimonial,
        'post_status'  => 'pending',
    ]);

    if (!empty($_FILES['mct_image']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachment_id = media_handle_upload('mct_image', $post_id);
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    echo '<p>✅ Thank you! Your testimonial has been submitted for review.</p>';
}
