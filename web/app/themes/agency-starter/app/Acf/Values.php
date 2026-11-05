<?php

/**
 * Reads flexible-content meta when the installed ACF edition has no Flexible
 * Content field class. ACF PRO formats these values itself, so this is not
 * attached in that case.
 */

namespace App\Acf;

class Values
{
    /**
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    public static function format(mixed $value, mixed $postId, array $field): array
    {
        if (self::isLayoutList($value)) {
            return $value;
        }

        return self::flexible($postId, $field);
    }

    /**
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    public static function flexible(mixed $postId, array $field): array
    {
        $postId = (int) $postId;

        if ($postId < 1) {
            return [];
        }

        $count = (int) get_post_meta($postId, (string) ($field['name'] ?? ''), true);
        $layouts = [];

        foreach ($field['layouts'] ?? [] as $layout) {
            if (is_array($layout) && isset($layout['name'])) {
                $layouts[$layout['name']] = $layout;
            }
        }

        $rows = [];
        $name = (string) ($field['name'] ?? '');

        for ($index = 0; $index < $count; $index++) {
            $layoutName = (string) get_post_meta($postId, $name.'_'.$index.'_acf_fc_layout', true);

            if ($layoutName === '' || ! isset($layouts[$layoutName])) {
                continue;
            }

            $row = ['acf_fc_layout' => $layoutName];

            foreach ($layouts[$layoutName]['sub_fields'] ?? [] as $subField) {
                if (! is_array($subField) || empty($subField['name'])) {
                    continue;
                }

                $row[$subField['name']] = self::read($postId, $name.'_'.$index.'_'.$subField['name'], $subField);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private static function read(int $postId, string $key, array $field): mixed
    {
        if (($field['type'] ?? '') === 'repeater') {
            $count = (int) get_post_meta($postId, $key, true);
            $rows = [];

            for ($index = 0; $index < $count; $index++) {
                $row = [];

                foreach ($field['sub_fields'] ?? [] as $subField) {
                    if (! is_array($subField) || empty($subField['name'])) {
                        continue;
                    }

                    $row[$subField['name']] = self::read($postId, $key.'_'.$index.'_'.$subField['name'], $subField);
                }

                $rows[] = $row;
            }

            return $rows;
        }

        $value = get_post_meta($postId, $key, true);
        $type = (string) ($field['type'] ?? 'text');

        if (in_array($type, ['image', 'file', 'taxonomy', 'number', 'post_object'], true)) {
            return is_numeric($value) ? (int) $value : 0;
        }

        if ($type === 'true_false') {
            return in_array($value, [1, '1', true], true) ? 1 : 0;
        }

        if (in_array($type, ['gallery', 'relationship'], true)) {
            return self::idList($value);
        }

        if ($type === 'link') {
            return is_array($value) ? $value : null;
        }

        return is_string($value) ? $value : '';
    }

    /**
     * @return list<int>
     */
    private static function idList(mixed $value): array
    {
        if (is_string($value) && str_contains($value, ',')) {
            $value = array_map('trim', explode(',', $value));
        }

        if (! is_array($value)) {
            return [];
        }

        $ids = [];

        foreach ($value as $item) {
            $id = is_numeric($item) ? (int) $item : (is_object($item) && isset($item->ID) ? (int) $item->ID : 0);

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    private static function isLayoutList(mixed $value): bool
    {
        return is_array($value) && isset($value[0]) && is_array($value[0]) && isset($value[0]['acf_fc_layout']);
    }
}
