<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all testimonials
$testimonials = get_posts(array('post_type' => 'testimonial', 'numberposts' => -1));
foreach ($testimonials as $testimonial) {
    wp_delete_post($testimonial->ID, true);
}

// Delete testimonial taxonomies
$terms = get_terms(array('taxonomy' => ['testimonial_category', 'testimonial_tag'], 'hide_empty' => false));
foreach ($terms as $term) {
    wp_delete_term($term->term_id, $term->taxonomy);
}
