<?php

/**
 * Field-array helpers shared by the theme's ACF groups.
 */

namespace App\Fields;

class Builder
{
    public static function text(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'text', $instructions);
    }

    public static function textarea(string $key, string $label, string $name, string $instructions = '', int $rows = 4): array
    {
        return self::base($key, $label, $name, 'textarea', $instructions, [
            'rows' => $rows,
            'new_lines' => '',
        ]);
    }

    public static function email(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'email', $instructions);
    }

    public static function url(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'url', $instructions);
    }

    public static function number(string $key, string $label, string $name, int $default, string $instructions = '', int $min = 0, int $max = 0): array
    {
        $settings = [
            'default_value' => $default,
            'min' => $min,
            'step' => 1,
        ];

        if ($max > 0) {
            $settings['max'] = $max;
        }

        return self::base($key, $label, $name, 'number', $instructions, $settings);
    }

    public static function image(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'image', $instructions, [
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
            'mime_types' => 'jpg,jpeg,png,webp',
        ]);
    }

    public static function link(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'link', $instructions, [
            'return_format' => 'array',
        ]);
    }

    public static function wysiwyg(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'wysiwyg', $instructions, [
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
        ]);
    }

    public static function color(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'color_picker', $instructions, [
            'default_value' => '#E4D5C3',
            'enable_opacity' => 0,
            'return_format' => 'string',
        ]);
    }

    public static function boolean(string $key, string $label, string $name, int $default = 0, string $on = 'Show', string $off = 'Hide', string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'true_false', $instructions, [
            'default_value' => $default,
            'ui' => 1,
            'ui_on_text' => $on,
            'ui_off_text' => $off,
        ]);
    }

    /**
     * @param  array<string, string>  $choices
     */
    public static function select(string $key, string $label, string $name, array $choices, string $default, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'select', $instructions, [
            'choices' => $choices,
            'default_value' => $default,
            'return_format' => 'value',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
        ]);
    }

    /**
     * @param  list<string>  $postTypes
     */
    public static function relationship(string $key, string $label, string $name, array $postTypes, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'relationship', $instructions, [
            'post_type' => $postTypes,
            'filters' => ['search'],
            'return_format' => 'object',
            'elements' => ['featured_image'],
        ]);
    }

    /**
     * @param  list<string>  $postTypes
     */
    public static function postObject(string $key, string $label, string $name, array $postTypes, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'post_object', $instructions, [
            'post_type' => $postTypes,
            'return_format' => 'object',
            'multiple' => 0,
            'allow_null' => 1,
            'ui' => 1,
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     */
    public static function group(string $key, string $label, string $name, array $fields, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'group', $instructions, [
            'layout' => 'block',
            'sub_fields' => $fields,
        ]);
    }

    public static function file(string $key, string $label, string $name, string $instructions = '', string $mime = 'mp4,webm'): array
    {
        return self::base($key, $label, $name, 'file', $instructions, [
            'return_format' => 'id',
            'library' => 'all',
            'mime_types' => $mime,
        ]);
    }

    public static function gallery(string $key, string $label, string $name, string $instructions = '', int $max = 12): array
    {
        return self::base($key, $label, $name, 'gallery', $instructions, [
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
            'mime_types' => 'jpg,jpeg,png,webp',
            'min' => 0,
            'max' => $max,
            'insert' => 'append',
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     */
    public static function repeater(string $key, string $label, string $name, array $fields, string $instructions = '', string $collapsed = '', int $max = 0): array
    {
        return self::base($key, $label, $name, 'repeater', $instructions, [
            'layout' => 'block',
            'button_label' => __('Add row', 'sage'),
            'collapsed' => $collapsed,
            'min' => 0,
            'max' => $max,
            'sub_fields' => $fields,
        ]);
    }

    /**
     * @param  array<string, array<string, mixed>>  $layouts
     */
    public static function flexible(string $key, string $label, string $name, array $layouts, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'flexible_content', $instructions, [
            'layouts' => $layouts,
            'button_label' => __('Add section', 'sage'),
            'min' => 0,
            'max' => 0,
        ]);
    }

    public static function taxonomy(string $key, string $label, string $name, string $taxonomy, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'taxonomy', $instructions, [
            'taxonomy' => $taxonomy,
            'field_type' => 'select',
            'return_format' => 'id',
            'add_term' => 0,
            'save_terms' => 0,
            'load_terms' => 0,
            'allow_null' => 1,
            'multiple' => 0,
        ]);
    }

    public static function oembed(string $key, string $label, string $name, string $instructions = ''): array
    {
        return self::base($key, $label, $name, 'oembed', $instructions);
    }

    /**
     * Keep PRO-only fields out of the editor until that field type is loaded.
     *
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    public static function available(array $field): array
    {
        $type = (string) ($field['type'] ?? '');

        if ($type === '' || ! function_exists('acf_get_field_type') || acf_get_field_type($type)) {
            return $field;
        }

        return self::message(
            (string) $field['key'].'_notice',
            (string) $field['label'],
            __('This control needs the licensed section editor. Content already on the website stays in place.', 'sage')
        );
    }

    public static function message(string $key, string $label, string $message): array
    {
        return self::base($key, $label, '', 'message', '', [
            'message' => $message,
            'new_lines' => 'wpautop',
            'esc_html' => 1,
        ]);
    }

    /**
     * @return array<int, array<int, array<string, string>>>
     */
    public static function showWhen(string $fieldKey, string $value): array
    {
        return [[
            [
                'field' => $fieldKey,
                'operator' => '==',
                'value' => $value,
            ],
        ]];
    }

    public static function tab(string $key, string $label): array
    {
        return self::base($key, $label, '', 'tab', '', [
            'placement' => 'top',
            'endpoint' => 0,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private static function base(string $key, string $label, string $name, string $type, string $instructions, array $extra = []): array
    {
        return array_merge([
            'key' => $key,
            'label' => $label,
            'name' => $name,
            'type' => $type,
            'instructions' => $instructions,
            'required' => 0,
        ], $extra);
    }
}
