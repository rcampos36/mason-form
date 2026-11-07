<?php

/**
 * Edits page-section and gallery values with the field types shipped in free ACF.
 *
 * Flexible Content, repeater, and gallery inputs stay in the field definitions for
 * ACF PRO. Until that editor is loaded, this screen reads and writes the same meta.
 */

namespace App\Fields;

use App\Acf\Content;

class SectionEditor
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private static array $bound = [];

    /**
     * @var array<int, list<array<string, mixed>>>
     */
    private static array $rows = [];

    public static function register(): void
    {
        add_action('acf/include_fields', [self::class, 'fields'], 20);
        add_filter('acf/load_value', [self::class, 'load'], 10, 3);
        add_action('acf/save_post', [self::class, 'save'], 20);
    }

    public static function fields(): void
    {
        if (! is_admin()) {
            return;
        }

        $postId = self::requestedPostId();

        if ($postId < 1) {
            return;
        }

        $type = get_post_type($postId);

        if (! is_string($type) || $type === '') {
            return;
        }

        if (post_type_exists($type) && ! current_user_can('edit_post', $postId)) {
            return;
        }

        if (! in_array($type, ['page', 'project'], true)) {
            return;
        }

        if ($type === 'page' && get_page_template_slug($postId) === 'template-site-settings.blade.php') {
            return;
        }

        self::defineFor($postId);
    }

    public static function defineFor(int $postId): void
    {
        self::$bound = [];

        if ($postId < 1 || ! function_exists('acf_add_local_field_group')) {
            return;
        }

        if (! function_exists('acf_get_field_type') || ! acf_get_field_type('flexible_content')) {
            $fields = self::sectionFields($postId);

            if ($fields !== []) {
                acf_add_local_field_group([
                    'key' => 'group_hk_page_content',
                    'title' => __('Page content', 'sage'),
                    'fields' => $fields,
                    'location' => Sections::group()['location'],
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'active' => true,
                    'show_in_rest' => 0,
                ]);
            }
        }

        if (get_post_type($postId) === 'project' && (! function_exists('acf_get_field_type') || ! acf_get_field_type('gallery'))) {
            $media = self::mediaFields($postId);

            if ($media !== []) {
                acf_add_local_field_group([
                    'key' => 'group_hk_project_media',
                    'title' => __('Project images', 'sage'),
                    'fields' => $media,
                    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'project']]],
                    'menu_order' => 5,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'active' => true,
                    'show_in_rest' => 0,
                ]);
            }
        }
    }

    /**
     * @param  mixed  $value
     * @param  mixed  $postId
     * @param  array<string, mixed>  $field
     */
    public static function load(mixed $value, mixed $postId, array $field): mixed
    {
        $key = (string) ($field['key'] ?? '');
        $bound = self::$bound[$key] ?? null;

        if (! is_array($bound)) {
            return $value;
        }

        $postId = (int) $postId;

        if (($bound['kind'] ?? '') === 'media') {
            $ids = self::storedIds($postId, (string) $bound['meta']);

            return $ids[(int) $bound['index']] ?? '';
        }

        $rows = self::rows($postId);
        $current = self::dig($rows[(int) $bound['section']] ?? [], (string) $bound['path']);

        return self::present($current, (string) $bound['type']);
    }

    public static function save(mixed $postId): void
    {
        if (! is_numeric($postId)) {
            return;
        }

        $postId = (int) $postId;

        if ($postId < 1 || self::$bound === [] || wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
            return;
        }

        if (! current_user_can('edit_post', $postId) || ! isset($_POST['acf']) || ! is_array($_POST['acf'])) {
            return;
        }

        $posted = wp_unslash($_POST['acf']);
        $touched = false;

        foreach (self::$bound as $key => $unused) {
            if (array_key_exists($key, $posted)) {
                $touched = true;
                break;
            }
        }

        if (! $touched) {
            return;
        }

        if (! function_exists('acf_get_field_type') || ! acf_get_field_type('flexible_content')) {
            self::saveSections($postId, $posted);
        }

        if (! function_exists('acf_get_field_type') || ! acf_get_field_type('gallery')) {
            self::saveMedia($postId, $posted);
        }

        self::cleanup($postId);
        unset(self::$rows[$postId]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function sectionFields(int $postId): array
    {
        $layouts = [];

        foreach (Sections::flexibleField()['layouts'] as $layout) {
            if (is_array($layout) && isset($layout['name'])) {
                $layouts[(string) $layout['name']] = $layout;
            }
        }

        $rows = self::rows($postId);

        if ($rows === []) {
            return [];
        }

        $fields = [
            Builder::message(
                'field_hk_page_content_note',
                __('How to edit', 'sage'),
                __('This panel edits the words, pictures, and buttons already on the page. Adding, removing, or reordering whole sections needs the licensed ACF PRO editor. The large homepage name is Company name under Site Settings.', 'sage')
            ),
        ];

        foreach ($rows as $index => $row) {
            $name = (string) ($row['acf_fc_layout'] ?? '');
            $layout = $layouts[$name] ?? null;

            if (! is_array($layout)) {
                continue;
            }

            $fields[] = Builder::tab(
                self::key($postId, $index, 'tab'),
                ($index + 1).'. '.(string) ($layout['label'] ?? $name)
            );

            if ($name === 'hero' && (int) get_option('page_on_front') === $postId) {
                $fields[] = Builder::message(
                    self::key($postId, $index, 'home_note'),
                    __('Homepage title', 'sage'),
                    __('The large title is Company name in Site Settings. An ampersand with spaces, such as Maison & Form, splits it onto two lines. A photograph caption filled in below replaces the caption saved on the image.', 'sage')
                );
            }

            foreach ($layout['sub_fields'] ?? [] as $subField) {
                if (is_array($subField)) {
                    $fields = array_merge($fields, self::expose($postId, $index, $subField, $row[$subField['name']] ?? null, (string) $subField['name']));
                }
            }
        }

        return $fields;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function mediaFields(int $postId): array
    {
        $sources = [
            'project_gallery' => [__('Gallery', 'sage'), 'field_hk_project_gallery', __('Shown after the project text. The main picture stays in Featured image.', 'sage')],
            'project_floor_plans' => [__('Floor plans', 'sage'), 'field_hk_project_floor_plans', __('Drawings shown in the floor-plan section.', 'sage')],
        ];
        $fields = [
            Builder::message(
                'field_hk_project_media_edit_note',
                __('Images', 'sage'),
                __('Replace a picture, clear one to remove it, or use the empty field to add one image. Update the project, then add another if you need more.', 'sage')
            ),
        ];

        foreach ($sources as $meta => [$label, $reference, $instructions]) {
            $ids = self::storedIds($postId, $meta);
            $slots = $ids;

            if (count($ids) < 12) {
                $slots[] = 0;
            }

            $fields[] = Builder::message('field_hk_m_'.$postId.'_'.$meta.'_note', $label, $instructions);

            foreach ($slots as $index => $unused) {
                $key = 'field_hk_m_'.$postId.'_'.$meta.'_'.$index;
                $fields[] = Builder::image($key, sprintf('%s %d', $label, $index + 1), 'hk_media_'.$meta.'_'.$index);
                self::$bound[$key] = [
                    'kind' => 'media',
                    'meta' => $meta,
                    'index' => $index,
                    'reference' => $reference,
                ];
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    private static function expose(int $postId, int $section, array $field, mixed $value, string $path, string $prefix = ''): array
    {
        $type = (string) ($field['type'] ?? '');
        $label = $prefix.(string) ($field['label'] ?? $path);

        if ($type === 'repeater') {
            $rows = array_values(array_filter(is_array($value) ? $value : [], 'is_array'));
            $max = (int) ($field['max'] ?? 0);
            $slots = $rows;

            if ($max < 1 || count($rows) < $max) {
                $slots[] = [];
            }

            $out = [
                Builder::message(
                    self::key($postId, $section, $path.'_note'),
                    $label,
                    __('Edit each item below. The last item is blank so one more can be added. Removing an item needs the licensed editor.', 'sage')
                ),
            ];

            foreach ($slots as $index => $row) {
                foreach ($field['sub_fields'] ?? [] as $subField) {
                    if (! is_array($subField) || empty($subField['name'])) {
                        continue;
                    }

                    $out = array_merge($out, self::expose(
                        $postId,
                        $section,
                        $subField,
                        $row[$subField['name']] ?? null,
                        $path.'__'.$index.'__'.$subField['name'],
                        $label.' '.($index + 1).' — '
                    ));
                }
            }

            return $out;
        }

        if ($type === 'gallery') {
            $ids = Content::ids(is_array($value) ? $value : []);
            $max = max(1, (int) ($field['max'] ?? 12));
            $slots = $ids;

            if (count($ids) < $max) {
                $slots[] = 0;
            }

            $out = [
                Builder::message(
                    self::key($postId, $section, $path.'_note'),
                    $label,
                    __('Replace a picture, clear one to remove it, or use the empty field to add one image.', 'sage')
                ),
            ];

            foreach ($slots as $index => $unused) {
                $slot = $path.'__'.$index;
                $copy = Builder::image(self::key($postId, $section, $slot), sprintf('%s %d', $label, $index + 1), self::inputName($section, $slot));
                self::$bound[$copy['key']] = [
                    'kind' => 'section',
                    'section' => $section,
                    'path' => $slot,
                    'type' => 'image',
                ];
                $out[] = $copy;
            }

            return $out;
        }

        if ($type === 'group') {
            $current = is_array($value) ? $value : [];
            $out = [];

            foreach ($field['sub_fields'] ?? [] as $subField) {
                if (! is_array($subField) || empty($subField['name'])) {
                    continue;
                }

                $out = array_merge($out, self::expose(
                    $postId,
                    $section,
                    $subField,
                    $current[$subField['name']] ?? null,
                    $path.'__'.$subField['name'],
                    $label.' — '
                ));
            }

            return $out;
        }

        if (in_array($type, ['tab', 'message', 'accordion', 'flexible_content'], true) || $type === '') {
            return [];
        }

        $copy = $field;
        $copy['key'] = self::key($postId, $section, $path);
        $copy['name'] = self::inputName($section, $path);
        $copy['label'] = $label;
        $copy['conditional_logic'] = 0;
        self::$bound[$copy['key']] = [
            'kind' => 'section',
            'section' => $section,
            'path' => $path,
            'type' => $type,
        ];

        return [$copy];
    }

    /**
     * @param  array<string, mixed>  $posted
     */
    private static function saveSections(int $postId, array $posted): void
    {
        $layouts = [];

        foreach (Sections::flexibleField()['layouts'] as $layout) {
            if (is_array($layout) && isset($layout['name'])) {
                $layouts[(string) $layout['name']] = $layout;
            }
        }

        $rows = self::rows($postId);
        $next = [];

        foreach ($rows as $index => $row) {
            $name = (string) ($row['acf_fc_layout'] ?? '');
            $layout = $layouts[$name] ?? null;

            if (! is_array($row) || ! is_array($layout)) {
                return;
            }

            $next[] = self::overlay($postId, $index, $layout['sub_fields'] ?? [], $row, $posted);
        }

        if (count($next) !== count($rows)) {
            return;
        }

        Sections::save($postId, $next);
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $posted
     * @return array<string, mixed>
     */
    private static function overlay(int $postId, int $section, array $fields, array $row, array $posted, string $prefix = ''): array
    {
        foreach ($fields as $field) {
            if (! is_array($field) || empty($field['name'])) {
                continue;
            }

            $name = (string) $field['name'];
            $path = $prefix === '' ? $name : $prefix.'__'.$name;
            $type = (string) ($field['type'] ?? '');

            if ($type === 'repeater') {
                $existing = array_values(array_filter(is_array($row[$name] ?? null) ? $row[$name] : [], 'is_array'));
                $max = (int) ($field['max'] ?? 0);
                $count = count($existing);

                if ($max < 1 || $count < $max) {
                    $count++;
                }

                $items = [];

                for ($index = 0; $index < $count; $index++) {
                    $item = self::overlay($postId, $section, $field['sub_fields'] ?? [], $existing[$index] ?? [], $posted, $path.'__'.$index);

                    if ($index >= count($existing) && ! self::hasContent($field['sub_fields'] ?? [], $item)) {
                        continue;
                    }

                    $items[] = $item;
                }

                $row[$name] = $items;

                continue;
            }

            if ($type === 'gallery') {
                $row[$name] = self::postedIds($postId, $section, $path, Content::ids(is_array($row[$name] ?? null) ? $row[$name] : []), (int) ($field['max'] ?? 12), $posted);

                continue;
            }

            if ($type === 'group') {
                $current = is_array($row[$name] ?? null) ? $row[$name] : [];
                $row[$name] = self::overlay($postId, $section, $field['sub_fields'] ?? [], $current, $posted, $path);

                continue;
            }

            $key = self::key($postId, $section, $path);

            if (! array_key_exists($key, $posted)) {
                continue;
            }

            $row[$name] = self::clean($field, $posted[$key], $row[$name] ?? null);
        }

        return $row;
    }

    /**
     * @param  list<int>  $current
     * @param  array<string, mixed>  $posted
     * @return list<int>
     */
    private static function postedIds(int $postId, int $section, string $path, array $current, int $max, array $posted): array
    {
        $max = max(1, $max);
        $count = count($current);

        if ($count < $max) {
            $count++;
        }

        $ids = [];
        $seen = false;

        for ($index = 0; $index < $count; $index++) {
            $key = self::key($postId, $section, $path.'__'.$index);

            if (! array_key_exists($key, $posted)) {
                if (isset($current[$index])) {
                    $ids[] = $current[$index];
                }

                continue;
            }

            $seen = true;
            $id = is_numeric($posted[$key]) ? (int) $posted[$key] : 0;

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return $seen ? $ids : $current;
    }

    /**
     * @param  array<string, mixed>  $posted
     */
    private static function saveMedia(int $postId, array $posted): void
    {
        $groups = [];

        foreach (self::$bound as $key => $bound) {
            if (($bound['kind'] ?? '') !== 'media') {
                continue;
            }

            $meta = (string) $bound['meta'];
            $groups[$meta]['reference'] = (string) $bound['reference'];
            $groups[$meta]['slots'][(int) $bound['index']] = $key;
        }

        foreach ($groups as $meta => $group) {
            $current = self::storedIds($postId, $meta);
            $ids = [];
            $seen = false;
            ksort($group['slots']);

            foreach ($group['slots'] as $index => $key) {
                if (! array_key_exists($key, $posted)) {
                    if (isset($current[$index])) {
                        $ids[] = $current[$index];
                    }

                    continue;
                }

                $seen = true;
                $id = is_numeric($posted[$key]) ? (int) $posted[$key] : 0;

                if ($id > 0) {
                    $ids[] = $id;
                }
            }

            if (! $seen) {
                continue;
            }

            update_post_meta($postId, $meta, $ids);
            update_post_meta($postId, '_'.$meta, $group['reference']);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @param  array<string, mixed>  $row
     */
    private static function hasContent(array $fields, array $row): bool
    {
        foreach ($fields as $field) {
            if (! is_array($field) || empty($field['name'])) {
                continue;
            }

            $type = (string) ($field['type'] ?? '');
            $value = $row[$field['name']] ?? null;

            if ($type === 'group' && is_array($value) && self::hasContent($field['sub_fields'] ?? [], $value)) {
                return true;
            }

            if ($type === 'repeater' && is_array($value)) {
                foreach ($value as $item) {
                    if (is_array($item) && self::hasContent($field['sub_fields'] ?? [], $item)) {
                        return true;
                    }
                }

                continue;
            }

            if ($type === 'group') {
                continue;
            }

            if (! in_array($type, ['text', 'textarea', 'wysiwyg', 'image', 'file', 'link', 'relationship', 'gallery', 'url', 'email', 'oembed', 'post_object', 'taxonomy'], true)) {
                continue;
            }

            if (is_array($value)) {
                if (trim((string) ($value['url'] ?? '')) !== '' || Content::ids($value) !== []) {
                    return true;
                }

                continue;
            }

            if (is_numeric($value) && (int) $value > 0) {
                return true;
            }

            if (is_string($value) && trim($value) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private static function clean(array $field, mixed $value, mixed $previous): mixed
    {
        $type = (string) ($field['type'] ?? 'text');

        if ($type === 'number') {
            return is_numeric($value) ? (int) $value : '';
        }

        if (in_array($type, ['image', 'file', 'post_object', 'taxonomy'], true)) {
            return is_numeric($value) && (int) $value > 0 ? (int) $value : '';
        }

        if ($type === 'true_false') {
            return in_array($value, [1, '1', true], true) ? 1 : 0;
        }

        if ($type === 'select') {
            $choices = is_array($field['choices'] ?? null) ? $field['choices'] : [];
            $choice = is_scalar($value) ? (string) $value : '';

            return array_key_exists($choice, $choices) ? $choice : (is_scalar($previous) ? (string) $previous : (string) ($field['default_value'] ?? ''));
        }

        if ($type === 'relationship') {
            return Content::ids(is_array($value) ? $value : []);
        }

        if ($type === 'link') {
            if (! is_array($value)) {
                return '';
            }

            $url = esc_url_raw(trim((string) ($value['url'] ?? '')));

            if ($url === '') {
                return '';
            }

            $target = ($value['target'] ?? '') === '_blank' ? '_blank' : '';

            return [
                'title' => sanitize_text_field((string) ($value['title'] ?? '')),
                'url' => $url,
                'target' => $target,
            ];
        }

        $text = is_scalar($value) ? (string) $value : '';

        if ($type === 'wysiwyg') {
            return wp_kses_post($text);
        }

        if (in_array($type, ['url', 'oembed'], true)) {
            $url = esc_url_raw(trim($text));

            if ($url === '' && trim($text) !== '' && is_string($previous)) {
                return $previous;
            }

            return $url;
        }

        if ($type === 'email') {
            return sanitize_email($text);
        }

        if ($type === 'textarea') {
            return sanitize_textarea_field($text);
        }

        return sanitize_text_field($text);
    }

    private static function present(mixed $value, string $type): mixed
    {
        if ($type === 'number') {
            return is_numeric($value) ? (int) $value : '';
        }

        if (in_array($type, ['image', 'file', 'post_object', 'taxonomy'], true)) {
            return is_numeric($value) && (int) $value > 0 ? (int) $value : '';
        }

        if ($type === 'true_false') {
            return in_array($value, [1, '1', true], true) ? 1 : 0;
        }

        if ($type === 'relationship') {
            return Content::ids(is_array($value) ? $value : []);
        }

        if ($type === 'link') {
            return is_array($value) && ! empty($value['url']) ? $value : '';
        }

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function dig(array $row, string $path): mixed
    {
        $cursor = $row;

        foreach (explode('__', $path) as $part) {
            if (! is_array($cursor) || ! array_key_exists($part, $cursor)) {
                return null;
            }

            $cursor = $cursor[$part];
        }

        return $cursor;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function rows(int $postId): array
    {
        if (! isset(self::$rows[$postId])) {
            self::$rows[$postId] = Content::sections($postId);
        }

        return self::$rows[$postId];
    }

    /**
     * @return list<int>
     */
    private static function storedIds(int $postId, string $meta): array
    {
        $stored = get_post_meta($postId, $meta, true);

        if (is_string($stored) && str_contains($stored, ',')) {
            $stored = array_map('trim', explode(',', $stored));
        }

        return Content::ids(is_array($stored) ? $stored : []);
    }

    private static function cleanup(int $postId): void
    {
        foreach (array_keys(get_post_meta($postId)) as $metaKey) {
            foreach (['hk_edit_', '_hk_edit_', 'hk_media_', '_hk_media_'] as $prefix) {
                if (str_starts_with((string) $metaKey, $prefix)) {
                    delete_post_meta($postId, (string) $metaKey);
                }
            }
        }
    }

    private static function requestedPostId(): int
    {
        foreach (['post_ID', 'post_id'] as $key) {
            if (isset($_POST[$key]) && is_numeric($_POST[$key])) {
                return (int) $_POST[$key];
            }
        }

        return isset($_GET['post']) && is_numeric($_GET['post']) ? (int) $_GET['post'] : 0;
    }

    private static function key(int $postId, int $section, string $path): string
    {
        return 'field_hk_e_'.$postId.'_'.$section.'_'.preg_replace('/[^A-Za-z0-9_]/', '_', $path);
    }

    private static function inputName(int $section, string $path): string
    {
        return 'hk_edit_'.$section.'_'.preg_replace('/[^A-Za-z0-9_]/', '_', $path);
    }
}
