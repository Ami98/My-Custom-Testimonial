<?php

/**
 * Plugin Name: My Custom Testimonial Plugin
 * Description: A lightweight and easy-to-use plugin for displaying testimonials via shortcode. Users can showcase testimonials on the frontend without requiring login access. The plugin also includes an admin interface for managing testimonials within the WordPress dashboard.
 * Version: 1.0
 * Author: Ami Dalwadi
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function tp_register_testimonial_post_type()
{
    $labels = array(
        'name' => 'Testimonials',
        'singular_name' => 'Testimonial',
        'menu_name' => 'Testimonials',
        'name_admin_bar' => 'Testimonial',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Testimonial',
        'edit_item' => 'Edit Testimonial',
        'new_item' => 'New Testimonial',
        'view_item' => 'View Testimonial',
        'all_items' => 'All Testimonials',
        'search_items' => 'Search Testimonials',
    );

    $args = array(
        'label' => 'Testimonial',
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-testimonial',
        'has_archive' => false,
        'supports' => array('title', 'editor', 'thumbnail'),
    );

    register_post_type('testimonial', $args);
}
add_action('init', 'tp_register_testimonial_post_type');

// Shortcode to display testimonials
function tp_testimonial_shortcode($atts)
{
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1
    );

    $query = new WP_Query($args);
    $output = '<div class="tp-testimonials">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<div class="tp-testimonial">';
            if (has_post_thumbnail()) {
                $output .= get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            }
            $output .= '<h3>' . get_the_title() . '</h3>';
            $output .= '<div>' . get_the_content() . '</div>';
            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No testimonials found.</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('testimonials', 'tp_testimonial_shortcode');


function tp_testimonial_styles()
{
    echo '<style>
        .tp-testimonials { display: flex; flex-wrap: wrap; gap: 20px; }
        .tp-testimonial { border: 1px solid #ddd; padding: 15px; width: 300px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .tp-testimonial img { max-width: 100%; height: auto; border-radius: 50%; }
        .tp-testimonial h3 { margin-top: 10px; font-size: 1.2em; }
    </style>';
}
add_action('wp_head', 'tp_testimonial_styles');
