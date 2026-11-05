<?php

/**
 * Safe reads for ACF values. Missing fields and a missing plugin return empties.
 */

namespace App\Acf;

use App\Design;
use WP_Post;

class Content
{
    /**
     * @var list<string>
     */
    public const LAYOUTS = [
        'hero',
        'introduction',
        'image_text',
        'feature_grid',
        'portfolio',
        'services',
        'testimonials',
        'statistics',
        'team',
        'process',
        'gallery',
        'logos',
        'faq',
        'call_to_action',
        'journal',
        'contact',
        'full_media',
        'spacer',
    ];

    public static function settingsId(): int
    {
        $id = (int) get_option('hk_site_settings_page');

        if ($id > 0 && get_post_status($id)) {
            return $id;
        }

        $pages = get_posts([
            'name' => 'site-settings',
            'post_type' => 'page',
            'post_status' => ['private', 'publish', 'draft'],
            'posts_per_page' => 1,
            'fields' => 'ids',
        ]);

        if (! $pages) {
            return 0;
        }

        $id = (int) $pages[0];
        update_option('hk_site_settings_page', $id);

        return $id;
    }

    public static function raw(string $name, mixed $source = null): mixed
    {
        if (! function_exists('get_field')) {
            return null;
        }

        if ($source === null) {
            $source = self::settingsId();

            if ($source < 1) {
                return null;
            }
        }

        $value = get_field($name, $source);

        return $value === false ? null : $value;
    }

    public static function enabled(string $name, mixed $source = null): bool
    {
        $value = self::raw($name, $source);

        return ! in_array($value, [null, false, 0, '0', ''], true);
    }

    public static function text(string $name, mixed $source = null, string $default = ''): string
    {
        if (is_array($source)) {
            $value = $source[$name] ?? null;
        } else {
            $value = self::raw($name, $source);
        }

        if (! is_string($value) || trim($value) === '') {
            return $default;
        }

        return trim($value);
    }

    public static function companyName(): string
    {
        $name = self::text('company_name');

        return $name !== '' ? $name : (string) get_bloginfo('name', 'display');
    }

    public static function copyright(): string
    {
        $custom = self::text('copyright_text');

        if ($custom !== '') {
            return $custom;
        }

        return '© '.gmdate('Y').' '.self::companyName();
    }

    public static function contact(string $field): string
    {
        foreach (['footer_contact', 'contact'] as $group) {
            $values = self::raw($group);

            if (! is_array($values)) {
                continue;
            }

            $value = trim((string) ($values[$field] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    public static function description(string $preferred, string $fallback): string
    {
        $value = self::text($preferred);

        return $value !== '' ? $value : self::text($fallback);
    }

    public static function accent(): string
    {
        $value = self::raw('accent_color');

        if (! is_string($value)) {
            return '';
        }

        return sanitize_hex_color($value) ?: '';
    }

    public static function choice(string $name, string $group): string
    {
        $value = self::raw($name);

        return Design::classes($group, is_string($value) ? $value : null);
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public static function socialLinks(): array
    {
        $social = self::raw('social');
        $social = is_array($social) ? $social : [];
        $links = [];

        foreach ([
            'instagram' => __('Instagram', 'sage'),
            'facebook' => __('Facebook', 'sage'),
            'linkedin' => __('LinkedIn', 'sage'),
            'youtube' => __('YouTube', 'sage'),
        ] as $field => $label) {
            $url = self::url($social[$field] ?? null);

            if ($url !== '') {
                $links[] = ['label' => $label, 'url' => $url];
            }
        }

        foreach ([1, 2] as $index) {
            $url = self::url($social['other_url_'.$index] ?? null);
            $label = trim((string) ($social['other_label_'.$index] ?? ''));

            if ($url === '' || $label === '') {
                continue;
            }

            $links[] = ['label' => $label, 'url' => $url];
        }

        return $links;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function sections(?int $postId = null): array
    {
        $postId = $postId ?: (int) get_the_ID();

        if ($postId < 1) {
            return [];
        }

        $rows = function_exists('get_field') ? get_field('page_sections', $postId) : null;

        if (! self::isLayoutList($rows)) {
            $field = function_exists('acf_get_field') ? acf_get_field('page_sections') : null;

            if (! is_array($field) || empty($field['layouts'])) {
                $field = \App\Fields\Sections::flexibleField();
            }

            $rows = Values::flexible($postId, $field);
        }

        if (! is_array($rows)) {
            return [];
        }

        return array_values(array_filter($rows, function ($row) {
            return is_array($row) && in_array($row['acf_fc_layout'] ?? '', self::LAYOUTS, true);
        }));
    }

    /**
     * @return list<int>
     */
    public static function imageSlots(mixed $source, string $prefix, int $count): array
    {
        $ids = [];

        for ($index = 1; $index <= $count; $index++) {
            $value = is_array($source)
                ? ($source[$prefix.$index] ?? null)
                : self::raw($prefix.$index, $source);
            $id = self::attachmentId($value);

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * @param  list<array<string, mixed>>  $sections
     */
    public static function startsWithHero(array $sections): bool
    {
        return ($sections[0]['acf_fc_layout'] ?? '') === 'hero';
    }

    public static function variant(mixed $value, string $group): string
    {
        return Design::classes($group, is_string($value) ? $value : null);
    }

    public static function flag(array $section, string $name, bool $default = false): bool
    {
        if (! array_key_exists($name, $section)) {
            return $default;
        }

        return ! in_array($section[$name], [null, false, 0, '0', ''], true);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function rows(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, 'is_array'));
    }

    public static function limit(mixed $value, int $fallback, int $min, int $max): int
    {
        $number = is_numeric($value) ? (int) $value : $fallback;

        return max($min, min($max, $number));
    }

    /**
     * @param  array<string, mixed>  $section
     * @return list<array{url: string, title: string, target: string, style: string}>
     */
    public static function pairedLinks(array $section, string $primary = 'primary_link', string $secondary = 'secondary_link'): array
    {
        $links = [];
        $first = self::link($section[$primary] ?? null);
        $second = self::link($section[$secondary] ?? null);

        if ($first !== null) {
            $first['style'] = 'primary';
            $links[] = $first;
        }

        if ($second !== null) {
            $second['style'] = 'secondary';
            $links[] = $second;
        }

        return $links;
    }

    /**
     * @param  array<string, mixed>  $section
     * @return list<WP_Post>
     */
    public static function projects(array $section): array
    {
        $source = (string) ($section['source'] ?? 'selected');

        if ($source === 'latest' || $source === 'archive') {
            $args = [
                'post_type' => 'project',
                'post_status' => 'publish',
                'numberposts' => self::limit($section['count'] ?? null, $source === 'archive' ? 12 : 3, 1, 24),
                'orderby' => 'date',
                'order' => 'DESC',
            ];
            $category = $source === 'archive' && self::flag($section, 'show_filters')
                ? self::requestedTerm('project_category')
                : '';

            if ($category !== '') {
                $args['tax_query'] = [[
                    'taxonomy' => 'project_category',
                    'field' => 'slug',
                    'terms' => $category,
                ]];
            }

            return get_posts($args);
        }

        $selected = self::posts($section['projects'] ?? null);

        return $selected !== [] ? $selected : get_posts([
            'post_type' => 'project',
            'post_status' => 'publish',
            'numberposts' => 6,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
    }

    public static function requestedTerm(string $taxonomy): string
    {
        if (! isset($_GET[$taxonomy])) {
            return '';
        }

        $slug = sanitize_title((string) wp_unslash($_GET[$taxonomy]));

        return ($slug !== '' && term_exists($slug, $taxonomy)) ? $slug : '';
    }

    /**
     * @return list<string>
     */
    public static function termNames(int $postId, string $taxonomy): array
    {
        $terms = get_the_terms($postId, $taxonomy);

        if (! is_array($terms)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($term) {
            return $term instanceof \WP_Term ? $term->name : '';
        }, $terms)));
    }

    public static function embed(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            return '';
        }

        $allowed = [
            'iframe' => [
                'src' => true,
                'width' => true,
                'height' => true,
                'title' => true,
                'loading' => true,
                'allow' => true,
                'allowfullscreen' => true,
                'frameborder' => true,
                'referrerpolicy' => true,
            ],
        ];

        if (str_contains($value, '<iframe')) {
            return self::safeEmbed(wp_kses($value, $allowed));
        }

        $url = esc_url_raw($value);
        $html = $url !== '' ? wp_oembed_get($url) : false;

        return is_string($html) ? self::safeEmbed(wp_kses($html, $allowed)) : '';
    }

    public static function preparePost(WP_Post $post): WP_Post
    {
        $GLOBALS['post'] = $post;
        setup_postdata($post);

        return $post;
    }

    private static function safeEmbed(string $html): string
    {
        if ($html === '' || ! str_contains($html, '<iframe')) {
            return '';
        }

        return preg_match('/\ssrc=(["\'])https:\/\/[^"\']+\1/i', $html) === 1 ? $html : '';
    }

    public static function formHtml(mixed $value): string
    {
        $id = is_object($value) && isset($value->ID) ? (int) $value->ID : (is_numeric($value) ? (int) $value : 0);

        if ($id < 1 || ! function_exists('wpcf7_contact_form')) {
            return '';
        }

        $form = wpcf7_contact_form($id);

        return $form ? (string) $form->form_html(['html_id' => 'contact-form-'.$id]) : '';
    }

    /**
     * @return list<WP_Post>
     */
    public static function relatedPosts(int $postId): array
    {
        $selected = array_values(array_filter(
            self::posts(function_exists('get_field') ? get_field('related_posts', $postId) : null),
            function (WP_Post $post) use ($postId) {
                return $post->ID !== $postId;
            }
        ));

        if ($selected !== []) {
            return array_slice($selected, 0, 3);
        }

        $categories = wp_get_post_categories($postId);

        if ($categories === []) {
            return [];
        }

        return get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'category__in' => $categories,
            'post__not_in' => [$postId],
            'numberposts' => 3,
        ]);
    }

    /**
     * @param  array<string, mixed>  $section
     * @return list<array{title: string, text: string, image: int, url: string, target: string}>
     */
    public static function serviceCards(array $section): array
    {
        if (($section['source'] ?? '') === 'items') {
            $cards = [];

            foreach (self::rows($section['items'] ?? null) as $item) {
                $link = self::link($item['link'] ?? null);
                $cards[] = [
                    'title' => self::text('heading', $item),
                    'text' => self::text('text', $item),
                    'image' => self::attachmentId($item['image'] ?? null),
                    'url' => $link['url'] ?? '',
                    'target' => $link['target'] ?? '',
                ];
            }

            return array_values(array_filter($cards, function ($card) {
                return $card['title'] !== '' || $card['text'] !== '' || $card['image'] > 0;
            }));
        }

        $posts = self::posts($section['services'] ?? null);

        if ($posts === []) {
            $posts = get_posts([
                'post_type' => 'service',
                'post_status' => 'publish',
                'numberposts' => 12,
                'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            ]);
        }

        return array_map(function (WP_Post $post) {
            $page = function_exists('get_field') ? get_field('service_page', $post->ID) : null;
            $linked = self::post($page);

            return [
                'title' => $post->post_title,
                'text' => trim(get_the_excerpt($post)),
                'image' => (int) get_post_thumbnail_id($post),
                'url' => get_permalink($linked ?: $post) ?: '',
                'target' => '',
            ];
        }, $posts);
    }

    /**
     * @param  array<string, mixed>  $section
     * @return list<array{name: string, role: string, image: int, biography: string, links: list<array{label: string, url: string}>}>
     */
    public static function people(array $section): array
    {
        if (($section['source'] ?? '') === 'items') {
            $people = [];

            foreach (self::rows($section['members'] ?? null) as $member) {
                $links = [];

                foreach (self::rows($member['links'] ?? null) as $link) {
                    $url = self::url($link['url'] ?? null);
                    $label = trim((string) ($link['label'] ?? ''));

                    if ($url !== '' && $label !== '') {
                        $links[] = ['label' => $label, 'url' => $url];
                    }
                }

                $people[] = [
                    'name' => self::text('name', $member),
                    'role' => self::text('role', $member),
                    'image' => self::attachmentId($member['image'] ?? null),
                    'biography' => self::text('biography', $member),
                    'links' => $links,
                ];
            }

            return array_values(array_filter($people, function ($person) {
                return $person['name'] !== '' || $person['biography'] !== '';
            }));
        }

        $posts = self::posts($section['team'] ?? null);

        if ($posts === []) {
            $posts = get_posts([
                'post_type' => 'team_member',
                'post_status' => 'publish',
                'numberposts' => 6,
                'orderby' => 'menu_order date',
                'order' => 'DESC',
            ]);
        }

        return array_map(function (WP_Post $post) {
            $link = function_exists('get_field') ? self::link(get_field('team_link', $post->ID)) : null;

            return [
                'name' => $post->post_title,
                'role' => self::text('team_role', $post->ID),
                'image' => (int) get_post_thumbnail_id($post),
                'biography' => trim(wp_strip_all_tags(get_the_excerpt($post))),
                'links' => $link ? [['label' => $link['title'], 'url' => $link['url']]] : [],
            ];
        }, $posts);
    }

    /**
     * @param  array<string, mixed>  $section
     * @return list<WP_Post>
     */
    public static function journal(array $section): array
    {
        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => self::limit($section['count'] ?? null, 3, 1, 12),
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        $category = self::termId($section['category'] ?? null);

        if ($category > 0) {
            $args['cat'] = $category;
        }

        return get_posts($args);
    }

    public static function fileUrl(mixed $value): string
    {
        $id = self::attachmentId($value);

        if ($id < 1) {
            return '';
        }

        $mime = (string) get_post_mime_type($id);

        if (! in_array($mime, ['video/mp4', 'video/webm'], true)) {
            return '';
        }

        $url = wp_get_attachment_url($id);

        return is_string($url) ? $url : '';
    }

    public static function fileMime(mixed $value): string
    {
        $id = self::attachmentId($value);
        $mime = $id > 0 ? (string) get_post_mime_type($id) : '';

        return in_array($mime, ['video/mp4', 'video/webm'], true) ? $mime : '';
    }

    public static function caption(mixed $value): string
    {
        $id = self::attachmentId($value);

        if ($id < 1) {
            return '';
        }

        return trim((string) wp_get_attachment_caption($id));
    }

    public static function imageUrl(mixed $value, string $size = 'large'): string
    {
        $id = self::attachmentId($value);

        if ($id < 1) {
            return '';
        }

        $url = wp_get_attachment_image_url($id, $size);

        return is_string($url) ? $url : '';
    }

    public static function rich(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            return '';
        }

        return (string) apply_filters('the_content', $value);
    }

    public static function addressHtml(string $address): string
    {
        if ($address === '') {
            return '';
        }

        return nl2br(esc_html($address), false);
    }

    public static function phoneHref(string $phone): string
    {
        $digits = preg_replace('/[^0-9+]/', '', $phone) ?? '';

        return $digits === '' ? '' : 'tel:'.$digits;
    }

    /**
     * @return array{url: string, title: string, target: string}|null
     */
    public static function link(mixed $link): ?array
    {
        if (! is_array($link)) {
            return null;
        }

        $url = self::url($link['url'] ?? null);

        if ($url === '') {
            return null;
        }

        $title = trim((string) ($link['title'] ?? ''));

        return [
            'url' => $url,
            'title' => $title !== '' ? $title : $url,
            'target' => ($link['target'] ?? '') === '_blank' ? '_blank' : '',
        ];
    }

    /**
     * @return list<array{url: string, title: string, target: string, style: string}>
     */
    public static function buttons(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $buttons = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $link = self::link($row['button_link'] ?? null);

            if ($link === null) {
                continue;
            }

            $style = $row['button_style'] ?? 'primary';
            $link['style'] = in_array($style, ['primary', 'secondary', 'ghost'], true) ? $style : 'primary';
            $buttons[] = $link;
        }

        return $buttons;
    }

    public static function buttonStyle(mixed $value): string
    {
        return in_array($value, ['primary', 'secondary', 'ghost'], true) ? $value : 'primary';
    }

    /**
     * @return list<WP_Post>
     */
    public static function posts(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $posts = [];

        foreach ($value as $item) {
            $post = $item instanceof WP_Post ? $item : (is_numeric($item) ? get_post((int) $item) : null);

            if ($post instanceof WP_Post && $post->post_status === 'publish') {
                $posts[] = $post;
            }
        }

        return $posts;
    }

    public static function post(mixed $value): ?WP_Post
    {
        $post = $value instanceof WP_Post ? $value : (is_numeric($value) ? get_post((int) $value) : null);

        return $post instanceof WP_Post && $post->post_status === 'publish' ? $post : null;
    }

    /**
     * @return list<int>
     */
    public static function ids(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $ids = [];

        foreach ($value as $item) {
            $id = self::attachmentId($item);

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public static function image(mixed $image, string $size = 'large', string $variant = 'content', string $sizes = '(min-width: 1024px) 50vw, 100vw', string $loading = 'lazy'): string
    {
        $id = self::attachmentId($image);

        if ($id < 1) {
            return '';
        }

        $attributes = [
            'class' => Design::classes('image', $variant),
            'sizes' => $sizes,
            'loading' => $loading === 'eager' ? 'eager' : 'lazy',
            'decoding' => 'async',
        ];

        if ($loading === 'eager') {
            $attributes['fetchpriority'] = 'high';
        }

        return wp_get_attachment_image($id, $size, false, $attributes) ?: '';
    }

    public static function featuredImage(int $postId, string $variant = 'content', string $loading = 'lazy'): string
    {
        return self::image((int) get_post_thumbnail_id($postId), 'large', $variant, '(min-width: 1024px) 30vw, 100vw', $loading);
    }

    public static function logoHtml(string $field, bool $useAlternate = false): string
    {
        $id = self::attachmentId(self::raw($field));

        if ($id < 1 && $useAlternate) {
            $id = self::attachmentId(self::raw('alternate_logo'));
        }

        if ($id < 1) {
            $id = self::attachmentId(self::raw('primary_logo'));
        }

        if ($id < 1) {
            return '';
        }

        $alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true);

        return wp_get_attachment_image($id, 'medium', false, [
            'class' => Design::classes('image', 'logo'),
            'loading' => 'eager',
            'decoding' => 'async',
            'data-hk-logo' => '1',
            'alt' => $alt !== '' ? $alt : self::companyName(),
        ]) ?: '';
    }

    private static function isLayoutList(mixed $value): bool
    {
        return is_array($value) && isset($value[0]) && is_array($value[0]) && isset($value[0]['acf_fc_layout']);
    }

    private static function termId(mixed $value): int
    {
        if ($value instanceof \WP_Term) {
            return (int) $value->term_id;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_array($value)) {
            $id = $value['term_id'] ?? $value['ID'] ?? 0;

            return is_numeric($id) ? (int) $id : 0;
        }

        return 0;
    }

    public static function attachmentId(mixed $image): int
    {
        if (is_numeric($image)) {
            return (int) $image;
        }

        if (is_array($image) && isset($image['ID']) && is_numeric($image['ID'])) {
            return (int) $image['ID'];
        }

        return 0;
    }

    private static function url(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            return '';
        }

        return esc_url_raw(trim($value)) ?: '';
    }
}
