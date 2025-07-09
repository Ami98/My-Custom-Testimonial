<?php

// Shortcode: [testimonial_form]
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
function mct_handle_testimonial_submission()
{
    if (
        !isset($_POST['mct_nonce_field']) ||
        !wp_verify_nonce($_POST['mct_nonce_field'], 'mct_submit_testimonial')
    ) {
        echo '<p class="mct-error">❌ Security check failed.</p>';
        return;
    }

    $errors = [];

    if (empty($_POST['mct_name'])) {
        $errors[] = 'Name is required.';
    }

    if (empty($_POST['mct_testimonial'])) {
        $errors[] = 'Testimonial is required.';
    }

    // Validate image if uploaded
    if (!empty($_FILES['mct_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_type = $_FILES['mct_image']['type'];
        $file_size = $_FILES['mct_image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Invalid image type. Allowed: JPG, PNG, GIF, WebP.';
        }

        if ($file_size > $max_size) {
            $errors[] = 'Image is too large. Maximum size is 2MB.';
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="mct-error">❌ ' . esc_html($error) . '</p>';
        }
        return;
    }

    $name = sanitize_text_field($_POST['mct_name']);
    $testimonial = sanitize_textarea_field($_POST['mct_testimonial']);

    $post_id = wp_insert_post([
        'post_type'    => 'testimonial',
        'post_title'   => $name,
        'post_content' => $testimonial,
        'post_status'  => 'pending',
    ]);

    if (!is_wp_error($post_id) && !empty($_FILES['mct_image']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachment_id = media_handle_upload('mct_image', $post_id);
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    echo '<p class="mct-success">✅ Thank you! Your testimonial has been submitted for review.</p>';
}
