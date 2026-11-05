<?php

/**
 * Dashboard organization for people editing the site.
 */

namespace App;

add_action('init', function () {
    $editor = get_role('editor');

    if ($editor && ! $editor->has_cap('edit_theme_options')) {
        $editor->add_cap('edit_theme_options');
    }
});

add_action('admin_menu', function () {
    $pages = [
        'home' => __('Home', 'sage'),
        'work' => __('Work', 'sage'),
        'studio' => __('Studio', 'sage'),
        'services' => __('Services', 'sage'),
        'journal' => __('Journal', 'sage'),
        'contact' => __('Contact', 'sage'),
    ];

    foreach ($pages as $slug => $label) {
        $page = get_page_by_path($slug, OBJECT, 'page');

        if (! $page instanceof \WP_Post) {
            continue;
        }

        add_submenu_page(
            'edit.php?post_type=page',
            $label,
            $label,
            'edit_pages',
            'hk-edit-'.$slug,
            '__return_empty_string'
        );
    }

    add_menu_page(
        __('Navigation', 'sage'),
        __('Navigation', 'sage'),
        'edit_theme_options',
        'nav-menus.php',
        '',
        'dashicons-menu',
        58
    );

    if (current_user_can('manage_options')) {
        return;
    }

    remove_menu_page('themes.php');
    remove_menu_page('plugins.php');
    remove_menu_page('tools.php');
    remove_menu_page('options-general.php');
    remove_menu_page('edit-comments.php');
    remove_submenu_page('index.php', 'update-core.php');
}, 99);

add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', function () {
    return [
        'index.php',
        'edit.php?post_type=page',
        'edit.php?post_type=project',
        'edit.php?post_type=service',
        'edit.php?post_type=team_member',
        'edit.php',
        'upload.php',
        'nav-menus.php',
        'hk-site-settings',
    ];
});

add_action('admin_bar_menu', function ($bar) {
    if (current_user_can('manage_options') || ! $bar instanceof \WP_Admin_Bar) {
        return;
    }

    foreach (['customize', 'themes', 'widgets', 'updates', 'comments'] as $node) {
        $bar->remove_node($node);
    }
}, 999);

add_action('admin_init', function () {
    $requested = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

    if (str_starts_with($requested, 'hk-edit-')) {
        $page = get_page_by_path(substr($requested, 8), OBJECT, 'page');

        if ($page instanceof \WP_Post && current_user_can('edit_post', $page->ID)) {
            wp_safe_redirect(admin_url('post.php?post='.$page->ID.'&action=edit'));
            exit;
        }
    }

    if (current_user_can('manage_options')) {
        return;
    }

    global $pagenow;

    if (in_array($pagenow, blockedAdminScreens(), true)) {
        wp_safe_redirect(admin_url());
        exit;
    }
});

add_action('admin_page_access_denied', function () {
    global $pagenow;

    if (in_array($pagenow, blockedAdminScreens(), true)) {
        wp_safe_redirect(admin_url());
        exit;
    }
});

add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'hk_edit_website',
        __('Edit the website', 'sage'),
        static function () {
            $links = [
                ['label' => __('Home', 'sage'), 'url' => admin_url('edit.php?post_type=page&page=hk-edit-home')],
                ['label' => __('Work', 'sage'), 'url' => admin_url('edit.php?post_type=page&page=hk-edit-work')],
                ['label' => __('Studio', 'sage'), 'url' => admin_url('edit.php?post_type=page&page=hk-edit-studio')],
                ['label' => __('Services page', 'sage'), 'url' => admin_url('edit.php?post_type=page&page=hk-edit-services')],
                ['label' => __('Journal page', 'sage'), 'url' => admin_url('edit.php?post_type=page&page=hk-edit-journal')],
                ['label' => __('Contact', 'sage'), 'url' => admin_url('edit.php?post_type=page&page=hk-edit-contact')],
                ['label' => __('Projects', 'sage'), 'url' => admin_url('edit.php?post_type=project')],
                ['label' => __('Service records', 'sage'), 'url' => admin_url('edit.php?post_type=service')],
                ['label' => __('Team', 'sage'), 'url' => admin_url('edit.php?post_type=team_member')],
                ['label' => __('Journal articles', 'sage'), 'url' => admin_url('edit.php')],
                ['label' => __('Media', 'sage'), 'url' => admin_url('upload.php')],
                ['label' => __('Navigation', 'sage'), 'url' => admin_url('nav-menus.php')],
                ['label' => __('Site Settings', 'sage'), 'url' => admin_url('admin.php?page=hk-site-settings')],
            ];

            echo '<ul>';

            foreach ($links as $link) {
                echo '<li><a href="'.esc_url($link['url']).'">'.esc_html($link['label']).'</a></li>';
            }

            echo '</ul>';
        }
    );

    if (current_user_can('manage_options')) {
        return;
    }

    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
});

add_filter('manage_pages_columns', function ($columns) {
    $columns['hk_role'] = __('On the site', 'sage');

    return $columns;
});

add_action('manage_pages_custom_column', function ($column, $postId) {
    if ($column !== 'hk_role') {
        return;
    }

    if ((int) get_option('page_on_front') === (int) $postId) {
        esc_html_e('Front page', 'sage');

        return;
    }

    if ((int) get_option('page_for_posts') === (int) $postId) {
        esc_html_e('Journal listing', 'sage');

        return;
    }

    $template = get_page_template_slug($postId);
    $labels = [
        'template-work.blade.php' => __('Work', 'sage'),
        'template-studio.blade.php' => __('Studio', 'sage'),
        'template-services.blade.php' => __('Services', 'sage'),
        'template-contact.blade.php' => __('Contact', 'sage'),
        'template-site-settings.blade.php' => __('Site Settings', 'sage'),
    ];

    echo esc_html($labels[$template] ?? '—');
}, 10, 2);

add_action('pre_get_posts', function ($query) {
    if (! is_admin() || ! $query instanceof \WP_Query || ! $query->is_main_query()) {
        return;
    }

    global $pagenow;

    if ($pagenow !== 'edit.php' || $query->get('post_type') !== 'page') {
        return;
    }

    $hidden = array_filter([
        (int) get_option('hk_site_settings_page'),
        current_user_can('manage_options') ? 0 : (int) (get_page_by_path('section-library', OBJECT, 'page')->ID ?? 0),
    ]);

    if ($hidden === []) {
        return;
    }

    $query->set('post__not_in', array_values(array_unique(array_merge(
        array_map('intval', (array) $query->get('post__not_in')),
        $hidden
    ))));
});

add_action('load-post.php', function () {
    if (current_user_can('manage_options')) {
        return;
    }

    $postId = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    $demo = get_page_by_path('section-library', OBJECT, 'page');

    if ($demo instanceof \WP_Post && $postId === (int) $demo->ID) {
        wp_safe_redirect(admin_url('edit.php?post_type=page'));
        exit;
    }
});

add_filter('wp_insert_post_data', function ($data, $postarr) {
    $postId = (int) ($postarr['ID'] ?? 0);

    if ($postId < 1 || $postId !== (int) get_option('hk_site_settings_page')) {
        return $data;
    }

    $data['post_status'] = 'private';
    $data['post_name'] = 'site-settings';

    return $data;
}, 10, 2);

add_action('save_post_page', function ($postId) {
    if ((int) $postId !== (int) get_option('hk_site_settings_page')) {
        return;
    }

    if (get_page_template_slug($postId) !== 'template-site-settings.blade.php') {
        update_post_meta($postId, '_wp_page_template', 'template-site-settings.blade.php');
    }
});

/**
 * @return list<string>
 */
function blockedAdminScreens(): array
{
    return [
        'customize.php',
        'widgets.php',
        'themes.php',
        'theme-editor.php',
        'theme-install.php',
        'plugins.php',
        'plugin-editor.php',
        'plugin-install.php',
        'update-core.php',
        'tools.php',
        'import.php',
        'export.php',
        'site-health.php',
        'options.php',
        'options-general.php',
        'options-writing.php',
        'options-reading.php',
        'options-discussion.php',
        'options-media.php',
        'options-permalink.php',
        'options-privacy.php',
    ];
}

add_filter('map_meta_cap', function ($caps, $cap, $userId, $args) {
    unset($userId);

    if (! in_array($cap, ['delete_post', 'delete_page'], true)) {
        return $caps;
    }

    $postId = (int) ($args[0] ?? 0);

    if ($postId > 0 && $postId === (int) get_option('hk_site_settings_page')) {
        $caps[] = 'do_not_allow';
    }

    return $caps;
}, 10, 4);
