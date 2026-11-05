<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_action('wp_enqueue_scripts', function () {
    global $wp_query;

    foreach ($wp_query->posts ?? [] as $post) {
        if ($post instanceof \WP_Post && has_blocks($post->post_content)) {
            return;
        }
    }

    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
}, 100);

add_filter('wpcf7_load_js', function () {
    return is_page(['contact', 'section-library']);
});

add_filter('wpcf7_load_css', function () {
    return is_page(['contact', 'section-library']);
});

add_filter('wp_get_attachment_image_attributes', function ($attr) {
    if (! is_array($attr)) {
        return $attr;
    }

    if (($attr['data-hk-logo'] ?? '') === '1') {
        unset($attr['fetchpriority'], $attr['data-hk-logo']);
        $attr['loading'] = 'eager';
    }

    if (($attr['loading'] ?? '') === 'lazy') {
        unset($attr['fetchpriority']);
    }

    return $attr;
});

add_filter('excerpt_more', function () {
    return sprintf(
        ' &hellip; <a class="%s" href="%s">%s</a>',
        esc_attr(Design::classes('link', 'inline')),
        get_permalink(),
        __('Continued', 'sage')
    );
});

add_action('pre_get_posts', function ($query) {
    if (is_admin() || ! $query instanceof \WP_Query || ! $query->is_main_query() || ! $query->is_home()) {
        return;
    }

    $page = (int) get_option('page_for_posts');
    $featured = function_exists('get_field') ? get_field('featured_posts', $page) : null;
    $ids = array_map(function ($post) {
        return $post->ID;
    }, \App\Acf\Content::posts($featured));

    if ($ids !== []) {
        $query->set('post__not_in', $ids);
    }
});

add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
    $location = (string) ($args->theme_location ?? '');
    $variant = (string) ($args->hk_variant ?? '');
    $current = array_intersect(
        ['current-menu-item', 'current-menu-parent', 'current-menu-ancestor'],
        array_map('strval', (array) ($item->classes ?? []))
    ) !== [];

    $choice = match (true) {
        $location === 'primary_navigation' && $variant === 'mobile' && $current => 'navMobileCurrent',
        $location === 'primary_navigation' && $variant === 'mobile' => 'navMobile',
        $location === 'primary_navigation' && $current => 'navCurrent',
        $location === 'primary_navigation' => 'nav',
        $location === 'footer_navigation' && $current => 'navCurrent',
        $location === 'footer_navigation' => 'nav',
        default => '',
    };

    if ($choice !== '') {
        $atts['class'] = Design::classes('link', $choice);
    }

    return $atts;
}, 10, 3);
