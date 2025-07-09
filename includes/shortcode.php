<?php

function mct_testimonial_form_shortcode()
{
    ob_start(); ?>

    <form class="mct-testimonial-form" enctype="multipart/form-data">
        <input type="text" name="mct_name" placeholder="Your Name" required />
        <textarea name="mct_testimonial" placeholder="Your Testimonial" required></textarea>
        <input type="file" name="mct_image" accept="image/*" required />

        <!-- ✅ Hidden field to trigger the correct WP AJAX handler -->
        <input type="hidden" name="action" value="mct_submit_testimonial">

        <!-- ✅ Security nonce to verify request -->
        <?php wp_nonce_field('mct_submit_testimonial', 'mct_nonce_field'); ?>

        <button type="submit">Submit Testimonial</button>

        <!-- ✅ Message box for AJAX response -->
        <div class="mct-message" style="display:none;"></div>
    </form>

<?php return ob_get_clean();
}
add_shortcode('testimonial_form', 'mct_testimonial_form_shortcode');
