<?php





// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Handle AJAX submission
add_action('wp_ajax_mct_submit_testimonial', 'mct_handle_testimonial_submission');
add_action('wp_ajax_nopriv_mct_submit_testimonial', 'mct_handle_testimonial_submission');

function mct_handle_testimonial_submission()
{
    // Check nonce
    if (
        !isset($_POST['mct_nonce_field']) ||
        !wp_verify_nonce($_POST['mct_nonce_field'], 'mct_submit_testimonial')
    ) {
        echo '<p class="mct-error">❌ Security check failed. Please refresh the page.</p>';
        wp_die();
    }

    $errors = [];

    // Validate name
    if (empty($_POST['mct_name'])) {
        $errors[] = 'Name is required.';
    }

    // Validate testimonial
    if (empty($_POST['mct_testimonial'])) {
        $errors[] = 'Testimonial is required.';
    }

    // Validate image (required)
    if (empty($_FILES['mct_image']['name'])) {
        $errors[] = 'Image is required.';
    } else {
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

    // Show validation errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="mct-error">❌ ' . esc_html($error) . '</p>';
        }
        wp_die();
    }

    // Sanitize and save
    $name = sanitize_text_field($_POST['mct_name']);
    $testimonial = sanitize_textarea_field($_POST['mct_testimonial']);

    $post_id = wp_insert_post([
        'post_type'    => 'testimonial',
        'post_title'   => $name,
        'post_content' => $testimonial,
        'post_status'  => 'pending',
    ]);

    if (!is_wp_error($post_id)) {
        // Upload and attach the image
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachment_id = media_handle_upload('mct_image', $post_id);
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }

        echo '<p class="mct-success">✅ Thank you! Your testimonial has been submitted for review.</p>';
    } else {
        echo '<p class="mct-error">❌ Failed to save testimonial. Please try again.</p>';
    }

    wp_die(); // always required for AJAX
}
