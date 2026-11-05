<?php

/**
 * Plugin Name: Heartwell Content
 * Description: Projects, services, team members, and project categories for the MAISON & FORM site.
 * Version: 1.0.0
 * Author: MAISON & FORM
 */

add_action('init', function () {
    register_post_type('project', [
        'labels' => [
            'name' => __('Projects', 'heartwell-content'),
            'singular_name' => __('Project', 'heartwell-content'),
            'add_new_item' => __('Add project', 'heartwell-content'),
            'edit_item' => __('Edit project', 'heartwell-content'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-portfolio',
        'menu_position' => 20,
        'rewrite' => ['slug' => 'project'],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
    ]);

    register_taxonomy('project_category', ['project'], [
        'labels' => [
            'name' => __('Project categories', 'heartwell-content'),
            'singular_name' => __('Project category', 'heartwell-content'),
            'add_new_item' => __('Add project category', 'heartwell-content'),
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'project-category'],
    ]);

    register_post_type('service', [
        'labels' => [
            'name' => __('Services', 'heartwell-content'),
            'singular_name' => __('Service', 'heartwell-content'),
            'add_new_item' => __('Add service', 'heartwell-content'),
            'edit_item' => __('Edit service', 'heartwell-content'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-hammer',
        'menu_position' => 21,
        'rewrite' => ['slug' => 'service'],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes'],
    ]);

    register_post_type('team_member', [
        'labels' => [
            'name' => __('Team', 'heartwell-content'),
            'singular_name' => __('Team member', 'heartwell-content'),
            'add_new_item' => __('Add team member', 'heartwell-content'),
            'edit_item' => __('Edit team member', 'heartwell-content'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-groups',
        'menu_position' => 22,
        'rewrite' => ['slug' => 'team'],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes'],
    ]);
});
