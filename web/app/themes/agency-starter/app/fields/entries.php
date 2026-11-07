<?php

/**
 * Fields for projects, services, and team members.
 * Titles, body content, excerpts, and featured images stay in WordPress.
 */

namespace App\Fields;

add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_hk_project',
        'title' => __('Project details', 'sage'),
        'fields' => [
            Builder::message('field_hk_project_media_note', __('Featured image', 'sage'), __('Use the Featured image panel for the main picture. Recommended size: 1600×1000 pixels, JPG or WebP.', 'sage')),
            Builder::text('field_hk_project_client', __('Client', 'sage'), 'project_client'),
            Builder::text('field_hk_project_year', __('Year', 'sage'), 'project_year', __('Shown on the homepage project row.', 'sage')),
            Builder::text('field_hk_project_location', __('Location', 'sage'), 'project_location', __('Shown on the homepage project row, for example a city.', 'sage')),
            Builder::text('field_hk_project_area', __('Area', 'sage'), 'project_area', __('Shown on the homepage project row, for example 260 m².', 'sage')),
            Builder::text('field_hk_project_type', __('Type', 'sage'), 'project_type', __('Shown on the homepage project row. Leave blank to use the project category.', 'sage')),
            Builder::link('field_hk_project_url', __('Website', 'sage'), 'project_url', __('Optional link to the live project.', 'sage')),
            function_exists('acf_get_field_type') && acf_get_field_type('gallery')
                ? Builder::gallery('field_hk_project_gallery', __('Gallery', 'sage'), 'project_gallery', __('Additional images, in the order they should appear. Recommended 1600×1000 pixels. The featured image stays in the WordPress panel.', 'sage'))
                : Builder::message('field_hk_project_gallery_notice', __('Gallery', 'sage'), __('Edit these pictures under Project images. Clear a picture to remove it, or use the empty field to add one. The featured image stays in the Featured image panel.', 'sage')),
            function_exists('acf_get_field_type') && acf_get_field_type('gallery')
                ? Builder::gallery('field_hk_project_floor_plans', __('Floor plans', 'sage'), 'project_floor_plans', __('Optional drawings. JPG, PNG, or WebP.', 'sage'))
                : Builder::message('field_hk_project_floor_plans_notice', __('Floor plans', 'sage'), __('Edit these drawings under Project images.', 'sage')),
            Builder::relationship('field_hk_project_services', __('Services', 'sage'), 'project_services', ['service'], __('Drag to reorder.', 'sage')),
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'project']]],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'active' => true,
        'show_in_rest' => 0,
    ]);

    acf_add_local_field_group([
        'key' => 'group_hk_service',
        'title' => __('Service details', 'sage'),
        'fields' => [
            Builder::text('field_hk_service_deliverables', __('Deliverables', 'sage'), 'service_deliverables', __('Shown in the homepage stage row.', 'sage')),
            Builder::text('field_hk_service_duration', __('Duration', 'sage'), 'service_duration', __('Shown in the homepage stage row.', 'sage')),
            Builder::text('field_hk_service_team', __('Team', 'sage'), 'service_team', __('Shown in the homepage stage row.', 'sage')),
            Builder::postObject('field_hk_service_page', __('Related page', 'sage'), 'service_page', ['page'], __('Optional page this service should link to.', 'sage')),
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'service']]],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'active' => true,
        'show_in_rest' => 0,
    ]);

    acf_add_local_field_group([
        'key' => 'group_hk_team',
        'title' => __('Team member details', 'sage'),
        'fields' => [
            Builder::text('field_hk_team_role', __('Role', 'sage'), 'team_role'),
            Builder::link('field_hk_team_link', __('Link', 'sage'), 'team_link', __('Optional profile or social link.', 'sage')),
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'team_member']]],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'active' => true,
        'show_in_rest' => 0,
    ]);

    acf_add_local_field_group([
        'key' => 'group_hk_journal_page',
        'title' => __('Journal listing', 'sage'),
        'fields' => [
            Builder::text('field_hk_featured_heading', __('Featured heading', 'sage'), 'featured_heading', __('Optional heading above the featured articles.', 'sage')),
            Builder::relationship('field_hk_featured_posts', __('Featured articles', 'sage'), 'featured_posts', ['post'], __('Shown above the article grid and left out of that grid.', 'sage')),
        ],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'posts_page']]],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'active' => true,
        'show_in_rest' => 0,
    ]);

    acf_add_local_field_group([
        'key' => 'group_hk_article',
        'title' => __('Related articles', 'sage'),
        'fields' => [
            Builder::relationship('field_hk_related_posts', __('Related articles', 'sage'), 'related_posts', ['post'], __('Optional. Leave empty to use other articles in the same category.', 'sage')),
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'post']]],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'active' => true,
        'show_in_rest' => 0,
    ]);
});
