<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Delete all testimonial posts
$testimonials = get_posts(['post_type' => 'testimonial', 'numberposts' => -1]);
foreach ($testimonials as $post) {
    wp_delete_post($post->ID, true);
}

// Delete all testimonial taxonomies
$terms = get_terms(['taxonomy' => ['testimonial_category', 'testimonial_tag'], 'hide_empty' => false]);
foreach ($terms as $term) {
    wp_delete_term($term->term_id, $term->taxonomy);
}
