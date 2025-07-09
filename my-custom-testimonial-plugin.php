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
