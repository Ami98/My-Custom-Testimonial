<?php

/**
 * Plugin Name: My Custom Testimonial
 * Description:A testimonial management plugin with front-end submission support.Place [testimonial_form] where you want users to submit testimonials.Use [testimonials] to show all testimonials, or filter them using [testimonials category="category_name"] or [testimonials tag="tag_name"].


 * Version: 1.0
 * Author: Your Name
 */

defined('ABSPATH') || exit;

// Load form handler
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';

// Register CPT
function mct_register_testimonial_post_type()
{
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-testimonial',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'mct_register_testimonial_post_type');

// Register taxonomies
function mct_register_testimonial_taxonomies()
{
    register_taxonomy('testimonial_category', 'testimonial', [
        'label' => 'Categories',
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'testimonial-category'],
    ]);

    register_taxonomy('testimonial_tag', 'testimonial', [
        'label' => 'Tags',
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'testimonial-tag'],
    ]);
}
add_action('init', 'mct_register_testimonial_taxonomies');

// Shortcode to display testimonials
function mct_testimonial_shortcode($atts)
{
    $atts = shortcode_atts([
        'category' => '',
        'tag' => '',
        'posts' => -1,
    ], $atts);

    $tax_query = ['relation' => 'AND'];

    if (!empty($atts['category'])) {
        $tax_query[] = [
            'taxonomy' => 'testimonial_category',
            'field'    => 'slug',
            'terms'    => explode(',', $atts['category']),
        ];
    }

    if (!empty($atts['tag'])) {
        $tax_query[] = [
            'taxonomy' => 'testimonial_tag',
            'field'    => 'slug',
            'terms'    => explode(',', $atts['tag']),
        ];
    }

    $args = [
        'post_type' => 'testimonial',
        'posts_per_page' => $atts['posts'],
    ];

    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);
    $output = '<div class="mct-testimonials">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<div class="mct-testimonial">';
            if (has_post_thumbnail()) {
                $output .= get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            }
            $output .= '<h3>' . esc_html(get_the_title()) . '</h3>';
            $output .= '<div>' . wp_kses_post(get_the_content()) . '</div>';
            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No testimonials found.</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('testimonials', 'mct_testimonial_shortcode');

// Enqueue frontend assets
function mct_enqueue_assets()
{
    wp_enqueue_style('mct-frontend-css', plugin_dir_url(__FILE__) . 'assets/css/frontend.css');
    wp_enqueue_script('mct-frontend-js', plugin_dir_url(__FILE__) . 'assets/js/frontend.js', [], false, true);

    wp_localize_script('mct-frontend-js', 'mct_ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action('wp_enqueue_scripts', 'mct_enqueue_assets');


// Plugin deactivation hook
register_deactivation_hook(__FILE__, 'mct_deactivate_plugin');
function mct_deactivate_plugin()
{
    // Example cleanup (optional): delete_option('mct_custom_option');
}
