<?php

/**
 * Advanced Custom Fields setup.
 *
 * Page sections are registered in app/fields/Sections.php.
 * Require it by path so the class loads on a case-sensitive filesystem.
 * Field groups edited in WordPress are saved to acf-json.
 */

namespace App;

require_once __DIR__.'/fields/Builder.php';
require_once __DIR__.'/fields/Sections.php';
require_once __DIR__.'/fields/page.php';
require_once __DIR__.'/fields/site-settings.php';
require_once __DIR__.'/fields/entries.php';
require_once __DIR__.'/fields/SectionEditor.php';

\App\Fields\SectionEditor::register();

add_action('acf/init', function () {
    if (! function_exists('acf_get_field_type') || acf_get_field_type('flexible_content')) {
        return;
    }

    add_filter('acf/format_value/type=flexible_content', [\App\Acf\Values::class, 'format'], 10, 3);

    add_filter('acf/pre_update_value', function ($check, $value, $postId, $field) {
        unset($value, $postId);

        if ($check !== null || ! is_array($field)) {
            return $check;
        }

        $type = (string) ($field['type'] ?? '');

        if (in_array($type, ['flexible_content', 'repeater', 'gallery'], true) && ! acf_get_field_type($type)) {
            return true;
        }

        return null;
    }, 10, 4);
});

add_action('admin_notices', function () {
    if (! function_exists('acf_is_pro') || acf_is_pro() || ! function_exists('get_current_screen')) {
        return;
    }

    $screen = get_current_screen();

    $postId = isset($_GET['post']) ? (int) $_GET['post'] : 0;

    if (! $screen || $screen->base !== 'post' || ! in_array($screen->post_type, ['page', 'project'], true)) {
        return;
    }

    if ($postId < 1 || ! current_user_can('edit_post', $postId)) {
        return;
    }

    if ($screen->post_type === 'page' && get_page_template_slug($postId) === 'template-site-settings.blade.php') {
        return;
    }

    echo '<div class="notice notice-info"><p>'.esc_html__('Page content edits the text, images, and buttons already on this page. Adding, removing, or reordering sections needs the licensed ACF PRO editor.', 'sage').'</p></div>';
});

add_filter('acf/settings/save_json', function () {
    return get_theme_file_path('acf-json');
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = get_theme_file_path('acf-json');

    return $paths;
});

add_action('init', function () {
    $stored = (int) get_option('hk_site_settings_page');

    if ($stored > 0 && get_post_status($stored)) {
        return;
    }

    $existing = get_posts([
        'name' => 'site-settings',
        'post_type' => 'page',
        'post_status' => ['private', 'publish', 'draft'],
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);

    if ($existing) {
        update_option('hk_site_settings_page', (int) $existing[0]);

        return;
    }

    $id = wp_insert_post([
        'post_title' => __('Site Settings', 'sage'),
        'post_name' => 'site-settings',
        'post_status' => 'private',
        'post_type' => 'page',
        'post_content' => __('Header, footer, contact, and design fields for the website.', 'sage'),
    ]);

    if (! is_int($id) || $id < 1) {
        return;
    }

    update_post_meta($id, '_wp_page_template', 'template-site-settings.blade.php');
    update_option('hk_site_settings_page', $id);
});

add_action('admin_menu', function () {
    add_menu_page(
        __('Site Settings', 'sage'),
        __('Site Settings', 'sage'),
        'edit_pages',
        'hk-site-settings',
        static function () {
            echo '';
        },
        'dashicons-admin-generic',
        59
    );
});

add_action('admin_init', function () {
    if (! is_admin() || ! isset($_GET['page']) || $_GET['page'] !== 'hk-site-settings') {
        return;
    }

    if (! current_user_can('edit_pages')) {
        return;
    }

    $id = (int) get_option('hk_site_settings_page');

    if ($id < 1) {
        return;
    }

    wp_safe_redirect(admin_url('post.php?post='.$id.'&action=edit'));
    exit;
});
