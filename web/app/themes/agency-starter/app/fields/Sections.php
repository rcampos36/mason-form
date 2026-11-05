<?php

/**
 * Page section library.
 *
 * The flexible field is the editor for adding, duplicating, removing, and
 * reordering sections. Repeaters hold the rows inside a section. Link fields
 * are buttons. Images are stored as attachment IDs.
 */

namespace App\Fields;

class Sections
{
    public static function register(): void
    {
        if (! function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(self::group());
    }

    /**
     * @return array<string, mixed>
     */
    public static function group(): array
    {
        $field = Builder::available(self::flexibleField());

        return [
            'key' => 'group_hk_page',
            'title' => __('Page sections', 'sage'),
            'fields' => [$field],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ],
                    [
                        'param' => 'page_template',
                        'operator' => '!=',
                        'value' => 'template-site-settings.blade.php',
                    ],
                ],
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'project',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'description' => __('Reusable page sections. The page title and featured image stay in the normal WordPress fields.', 'sage'),
            'show_in_rest' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function flexibleField(): array
    {
        return Builder::flexible(
            'field_hk_page_sections',
            __('Page sections', 'sage'),
            'page_sections',
            self::layouts(),
            __('Add a section, then drag to reorder it. Duplicate and remove controls are on each section. Choices such as background, alignment, and columns use the approved set.', 'sage')
        );
    }

    /**
     * Replace the page's section rows using the same meta shape ACF PRO saves.
     *
     * @param  list<array<string, mixed>>  $rows
     */
    public static function save(int $postId, array $rows): void
    {
        if ($postId < 1) {
            return;
        }

        $field = self::flexibleField();
        $layouts = [];

        foreach ($field['layouts'] as $layout) {
            if (is_array($layout) && isset($layout['name'])) {
                $layouts[$layout['name']] = $layout;
            }
        }

        foreach (get_post_meta($postId) as $key => $unused) {
            if ($key === 'page_sections' || $key === '_page_sections' || str_starts_with($key, 'page_sections_') || str_starts_with($key, '_page_sections_')) {
                delete_post_meta($postId, $key);
            }
        }

        $stored = [];

        foreach (array_values($rows) as $row) {
            $name = is_array($row) ? (string) ($row['acf_fc_layout'] ?? '') : '';

            if ($name === '' || ! isset($layouts[$name])) {
                continue;
            }

            $stored[] = [$layouts[$name], $row];
        }

        update_post_meta($postId, 'page_sections', (string) count($stored));
        update_post_meta($postId, '_page_sections', $field['key']);

        foreach ($stored as $index => [$layout, $row]) {
            update_post_meta($postId, 'page_sections_'.$index.'_acf_fc_layout', $layout['name']);
            update_post_meta($postId, '_page_sections_'.$index.'_acf_fc_layout', $field['key']);
            self::writeFields($postId, 'page_sections_'.$index, $layout['sub_fields'], $row);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @param  array<string, mixed>  $data
     */
    private static function writeFields(int $postId, string $prefix, array $fields, array $data): void
    {
        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');

            if ($name === '') {
                continue;
            }

            $key = $prefix.'_'.$name;
            $value = $data[$name] ?? null;
            update_post_meta($postId, '_'.$key, $field['key']);

            if (($field['type'] ?? '') === 'repeater') {
                $items = is_array($value) ? array_values(array_filter($value, 'is_array')) : [];
                update_post_meta($postId, $key, (string) count($items));

                foreach ($items as $index => $item) {
                    self::writeFields($postId, $key.'_'.$index, $field['sub_fields'] ?? [], $item);
                }

                continue;
            }

            update_post_meta($postId, $key, self::storeValue($field, $value));
        }
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private static function storeValue(array $field, mixed $value): mixed
    {
        $type = (string) ($field['type'] ?? 'text');

        if (in_array($type, ['image', 'file', 'taxonomy', 'post_object'], true)) {
            return is_numeric($value) && (int) $value > 0 ? (int) $value : '';
        }

        if ($type === 'true_false') {
            return in_array($value, [1, '1', true], true) ? 1 : 0;
        }

        if (in_array($type, ['gallery', 'relationship'], true)) {
            $ids = [];

            foreach (is_array($value) ? $value : [] as $item) {
                $id = is_numeric($item) ? (int) $item : (is_object($item) && isset($item->ID) ? (int) $item->ID : 0);

                if ($id > 0) {
                    $ids[] = $id;
                }
            }

            return $ids;
        }

        if ($type === 'link') {
            return is_array($value) && ! empty($value['url']) ? $value : '';
        }

        if ($type === 'number') {
            return is_numeric($value) ? (int) $value : (int) ($field['default_value'] ?? 0);
        }

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function layouts(): array
    {
        $layouts = [
            self::layout('layout_hk_hero', 'hero', __('Hero', 'sage'), [
                Builder::text('field_hk_hero_eyebrow', __('Eyebrow', 'sage'), 'eyebrow', __('Short line above the heading.', 'sage')),
                Builder::text('field_hk_hero_heading', __('Heading', 'sage'), 'heading', __('Leave blank to use the page title.', 'sage')),
                Builder::textarea('field_hk_hero_text', __('Description', 'sage'), 'text', __('Plain text under the heading.', 'sage'), 3),
                Builder::image('field_hk_hero_image', __('Background image', 'sage'), 'image', __('Wide image behind the text. Recommended 2400×1600 pixels, JPG or WebP. Captions are not shown here.', 'sage')),
                Builder::file('field_hk_hero_video', __('Background video', 'sage'), 'video', __('Optional. MP4 or WebM, muted behind the text. The image remains for visitors who prefer reduced motion.', 'sage')),
                Builder::link('field_hk_hero_primary', __('Primary button', 'sage'), 'primary_link', __('Optional. The link title is the button label.', 'sage')),
                Builder::link('field_hk_hero_secondary', __('Secondary button', 'sage'), 'secondary_link', __('Optional second button.', 'sage')),
                self::align('field_hk_hero_align'),
                self::background('field_hk_hero_background'),
                Builder::select('field_hk_hero_height', __('Minimum height', 'sage'), 'min_height', [
                    'compact' => __('Compact', 'sage'),
                    'standard' => __('Standard', 'sage'),
                    'tall' => __('Tall', 'sage'),
                    'screen' => __('Screen', 'sage'),
                ], 'standard', __('Approved height.', 'sage')),
            ]),
            self::layout('layout_hk_introduction', 'introduction', __('Introduction', 'sage'), [
                Builder::text('field_hk_intro_heading', __('Heading', 'sage'), 'heading'),
                Builder::wysiwyg('field_hk_intro_text', __('Text', 'sage'), 'text', __('Basic formatting is kept. Embedded media is not available in this field.', 'sage')),
                Builder::image('field_hk_intro_image', __('Image', 'sage'), 'image', __('Optional. Recommended 1600×1000 pixels, JPG or WebP.', 'sage')),
                Builder::select('field_hk_intro_layout', __('Layout', 'sage'), 'layout', [
                    'stacked' => __('Stacked', 'sage'),
                    'image-left' => __('Image left', 'sage'),
                    'image-right' => __('Image right', 'sage'),
                ], 'stacked', __('Approved arrangement.', 'sage')),
                self::background('field_hk_intro_background'),
                self::align('field_hk_intro_align'),
            ]),
            self::layout('layout_hk_image_text', 'image_text', __('Image and text', 'sage'), [
                Builder::text('field_hk_image_text_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_image_text_text', __('Description', 'sage'), 'text', '', 4),
                Builder::image('field_hk_image_text_image', __('Image', 'sage'), 'image'),
                Builder::link('field_hk_image_text_button', __('Button', 'sage'), 'button', __('Optional. The link title is the button label.', 'sage')),
                Builder::select('field_hk_image_text_position', __('Image position', 'sage'), 'image_position', [
                    'left' => __('Left', 'sage'),
                    'right' => __('Right', 'sage'),
                ], 'right', __('Approved position. On small screens the text stays first.', 'sage')),
                self::background('field_hk_image_text_background'),
            ]),
            self::layout('layout_hk_features', 'feature_grid', __('Feature grid', 'sage'), [
                Builder::text('field_hk_features_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_features_text', __('Description', 'sage'), 'text', '', 3),
                Builder::repeater('field_hk_features_rows', __('Features', 'sage'), 'features', [
                    Builder::image('field_hk_feature_image', __('Image or icon', 'sage'), 'image', __('Optional.', 'sage')),
                    Builder::text('field_hk_feature_heading', __('Heading', 'sage'), 'heading'),
                    Builder::textarea('field_hk_feature_text', __('Description', 'sage'), 'text', '', 3),
                    Builder::link('field_hk_feature_link', __('Link', 'sage'), 'link', __('Optional. The link title is shown on the card.', 'sage')),
                ], __('Each row is one card.', 'sage'), 'field_hk_feature_heading', 12),
                self::columns('field_hk_features_columns'),
                self::background('field_hk_features_background'),
                self::align('field_hk_features_align'),
            ]),
            self::layout('layout_hk_portfolio', 'portfolio', __('Portfolio grid', 'sage'), [
                Builder::text('field_hk_portfolio_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_portfolio_text', __('Description', 'sage'), 'text', '', 3),
                Builder::select('field_hk_portfolio_source', __('Projects', 'sage'), 'source', [
                    'selected' => __('Selected projects', 'sage'),
                    'latest' => __('Latest projects', 'sage'),
                    'archive' => __('All projects', 'sage'),
                ], 'selected', __('Selected projects keep the order you choose. Latest and all projects follow the publish date.', 'sage')),
                self::when(Builder::relationship('field_hk_portfolio_projects', __('Selected projects', 'sage'), 'projects', ['project'], __('Choose the projects to show, in the order they should appear.', 'sage')), 'field_hk_portfolio_source', 'selected'),
                self::whenAny(Builder::number('field_hk_portfolio_count', __('Number of projects', 'sage'), 'count', 6, __('Used for latest or all projects.', 'sage'), 1, 24), 'field_hk_portfolio_source', ['latest', 'archive']),
                self::when(Builder::boolean('field_hk_portfolio_filters', __('Category filters', 'sage'), 'show_filters', 0, __('Show', 'sage'), __('Hide', 'sage'), __('Shows project categories on the all-projects grid.', 'sage')), 'field_hk_portfolio_source', 'archive'),
                Builder::select('field_hk_portfolio_layout', __('Layout', 'sage'), 'layout', [
                    'grid' => __('Grid', 'sage'),
                    'list' => __('List', 'sage'),
                ], 'grid', __('Approved layout.', 'sage')),
                Builder::link('field_hk_portfolio_view_all', __('View-all button', 'sage'), 'view_all', __('Optional.', 'sage')),
                self::background('field_hk_portfolio_background'),
                self::align('field_hk_portfolio_align'),
            ]),
            self::layout('layout_hk_services', 'services', __('Services grid', 'sage'), [
                Builder::text('field_hk_services_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_services_text', __('Description', 'sage'), 'text', '', 3),
                Builder::select('field_hk_services_source', __('Services', 'sage'), 'source', [
                    'items' => __('Custom items', 'sage'),
                    'records' => __('Service records', 'sage'),
                ], 'records', __('Custom items are written here. Service records use the Services admin menu, including their titles, excerpts, and featured images.', 'sage')),
                self::when(Builder::repeater('field_hk_services_items', __('Service items', 'sage'), 'items', [
                    Builder::image('field_hk_service_item_image', __('Image or icon', 'sage'), 'image'),
                    Builder::text('field_hk_service_item_heading', __('Title', 'sage'), 'heading'),
                    Builder::textarea('field_hk_service_item_text', __('Description', 'sage'), 'text', '', 3),
                    Builder::link('field_hk_service_item_link', __('Link', 'sage'), 'link', __('Optional.', 'sage')),
                ], '', 'field_hk_service_item_heading', 12), 'field_hk_services_source', 'items'),
                self::when(Builder::relationship('field_hk_services_records', __('Service records', 'sage'), 'services', ['service'], __('Leave empty to show the latest services.', 'sage')), 'field_hk_services_source', 'records'),
                self::columns('field_hk_services_columns'),
                self::background('field_hk_services_background'),
                self::align('field_hk_services_align'),
            ]),
            self::layout('layout_hk_testimonials', 'testimonials', __('Testimonials', 'sage'), [
                Builder::text('field_hk_testimonials_heading', __('Heading', 'sage'), 'heading'),
                Builder::repeater('field_hk_testimonials_rows', __('Testimonials', 'sage'), 'testimonials', [
                    Builder::textarea('field_hk_testimonial_quote', __('Quote', 'sage'), 'quote', '', 4),
                    Builder::text('field_hk_testimonial_name', __('Name', 'sage'), 'name'),
                    Builder::text('field_hk_testimonial_role', __('Job title', 'sage'), 'role'),
                    Builder::text('field_hk_testimonial_company', __('Company', 'sage'), 'company'),
                    Builder::image('field_hk_testimonial_image', __('Photo', 'sage'), 'image', __('Optional.', 'sage')),
                ], '', 'field_hk_testimonial_name', 12),
                self::background('field_hk_testimonials_background'),
                self::align('field_hk_testimonials_align'),
            ]),
            self::layout('layout_hk_statistics', 'statistics', __('Statistics', 'sage'), [
                Builder::text('field_hk_statistics_heading', __('Heading', 'sage'), 'heading', __('Optional.', 'sage')),
                Builder::repeater('field_hk_statistics_rows', __('Statistics', 'sage'), 'statistics', [
                    Builder::text('field_hk_statistic_number', __('Number', 'sage'), 'number', __('Shown as entered, for example 48 or 12+.', 'sage')),
                    Builder::text('field_hk_statistic_label', __('Label', 'sage'), 'label'),
                    Builder::textarea('field_hk_statistic_text', __('Description', 'sage'), 'text', __('Optional.', 'sage'), 2),
                ], '', 'field_hk_statistic_label', 8),
                self::background('field_hk_statistics_background'),
            ]),
            self::layout('layout_hk_team', 'team', __('Team grid', 'sage'), [
                Builder::text('field_hk_team_heading', __('Heading', 'sage'), 'heading'),
                Builder::select('field_hk_team_source', __('People', 'sage'), 'source', [
                    'items' => __('Custom profiles', 'sage'),
                    'records' => __('Team records', 'sage'),
                ], 'records', __('Team records use the Team admin menu for the name, biography, and photo.', 'sage')),
                self::when(Builder::repeater('field_hk_team_items', __('Profiles', 'sage'), 'members', [
                    Builder::text('field_hk_team_item_name', __('Name', 'sage'), 'name'),
                    Builder::text('field_hk_team_item_role', __('Position', 'sage'), 'role'),
                    Builder::image('field_hk_team_item_image', __('Photo', 'sage'), 'image'),
                    Builder::textarea('field_hk_team_item_bio', __('Biography', 'sage'), 'biography', '', 4),
                    Builder::repeater('field_hk_team_item_links', __('Social links', 'sage'), 'links', [
                        Builder::text('field_hk_team_item_link_label', __('Label', 'sage'), 'label'),
                        Builder::url('field_hk_team_item_link_url', __('URL', 'sage'), 'url'),
                    ], __('Optional. Add one row per profile link.', 'sage'), 'field_hk_team_item_link_label', 4),
                ], '', 'field_hk_team_item_name', 12), 'field_hk_team_source', 'items'),
                self::when(Builder::relationship('field_hk_team_records', __('Team records', 'sage'), 'team', ['team_member'], __('Leave empty to show the latest team records. Role and profile link are edited on each person.', 'sage')), 'field_hk_team_source', 'records'),
                self::background('field_hk_team_background'),
                self::align('field_hk_team_align'),
            ]),
            self::layout('layout_hk_process', 'process', __('Process steps', 'sage'), [
                Builder::text('field_hk_process_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_process_text', __('Description', 'sage'), 'text', '', 3),
                Builder::repeater('field_hk_process_steps', __('Steps', 'sage'), 'steps', [
                    Builder::text('field_hk_process_step_heading', __('Title', 'sage'), 'heading'),
                    Builder::textarea('field_hk_process_step_text', __('Description', 'sage'), 'text', '', 3),
                    Builder::image('field_hk_process_step_image', __('Image', 'sage'), 'image', __('Optional.', 'sage')),
                ], __('Numbers are added automatically from the row order.', 'sage'), 'field_hk_process_step_heading', 8),
                self::background('field_hk_process_background'),
            ]),
            self::layout('layout_hk_gallery', 'gallery', __('Image gallery', 'sage'), [
                Builder::text('field_hk_gallery_heading', __('Heading', 'sage'), 'heading', __('Optional.', 'sage')),
                Builder::gallery('field_hk_gallery_images', __('Images', 'sage'), 'images', __('Recommended 1600×1000 pixels. The caption shown on the site is the caption saved with each image in the Media library.', 'sage')),
                Builder::select('field_hk_gallery_columns', __('Grid', 'sage'), 'columns', [
                    '2' => __('Two columns', 'sage'),
                    '3' => __('Three columns', 'sage'),
                    '4' => __('Four columns', 'sage'),
                ], '3', __('Approved grid.', 'sage')),
                Builder::boolean('field_hk_gallery_lightbox', __('Lightbox', 'sage'), 'lightbox', 0, __('Enabled', 'sage'), __('Disabled', 'sage'), __('Opens the full image in a dialog.', 'sage')),
                self::background('field_hk_gallery_background'),
            ]),
            self::layout('layout_hk_logos', 'logos', __('Logo grid', 'sage'), [
                Builder::text('field_hk_logos_heading', __('Heading', 'sage'), 'heading'),
                Builder::repeater('field_hk_logos_rows', __('Logos', 'sage'), 'logos', [
                    Builder::image('field_hk_logo_image', __('Logo', 'sage'), 'image', __('Use the image alt text as the name.', 'sage')),
                    Builder::link('field_hk_logo_link', __('Link', 'sage'), 'link', __('Optional.', 'sage')),
                ], '', 'field_hk_logo_image', 12),
                self::background('field_hk_logos_background'),
            ]),
            self::layout('layout_hk_faq', 'faq', __('FAQ', 'sage'), [
                Builder::text('field_hk_faq_heading', __('Heading', 'sage'), 'heading'),
                Builder::repeater('field_hk_faq_rows', __('Questions', 'sage'), 'questions', [
                    Builder::text('field_hk_faq_question', __('Question', 'sage'), 'question'),
                    Builder::wysiwyg('field_hk_faq_answer', __('Answer', 'sage'), 'answer'),
                ], __('Each row opens as an accordion panel.', 'sage'), 'field_hk_faq_question', 20),
                self::background('field_hk_faq_background'),
            ]),
            self::layout('layout_hk_cta', 'call_to_action', __('Call to action', 'sage'), [
                Builder::text('field_hk_cta_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_cta_text', __('Description', 'sage'), 'text', '', 3),
                self::background('field_hk_cta_background'),
                Builder::link('field_hk_cta_primary', __('Primary button', 'sage'), 'primary_link', __('Optional. The link title is the button label.', 'sage')),
                Builder::link('field_hk_cta_secondary', __('Secondary button', 'sage'), 'secondary_link', __('Optional second button.', 'sage')),
                self::align('field_hk_cta_align'),
            ]),
            self::layout('layout_hk_journal', 'journal', __('Latest journal posts', 'sage'), [
                Builder::text('field_hk_journal_heading', __('Heading', 'sage'), 'heading'),
                Builder::textarea('field_hk_journal_text', __('Description', 'sage'), 'text', '', 3),
                Builder::number('field_hk_journal_count', __('Number of posts', 'sage'), 'count', 3, __('Published posts only.', 'sage'), 1, 12),
                Builder::taxonomy('field_hk_journal_category', __('Category', 'sage'), 'category', 'category', __('Leave empty to show every category.', 'sage')),
                Builder::select('field_hk_journal_layout', __('Layout', 'sage'), 'layout', [
                    'grid' => __('Grid', 'sage'),
                    'list' => __('List', 'sage'),
                ], 'grid', __('Approved layout.', 'sage')),
                self::background('field_hk_journal_background'),
                self::align('field_hk_journal_align'),
            ]),
            self::layout('layout_hk_contact', 'contact', __('Contact information', 'sage'), [
                Builder::text('field_hk_contact_heading', __('Heading', 'sage'), 'heading'),
                Builder::wysiwyg('field_hk_contact_text', __('Description', 'sage'), 'text'),
                Builder::boolean('field_hk_contact_details', __('Contact details', 'sage'), 'show_details', 1, __('Show', 'sage'), __('Hide', 'sage'), __('Uses the email, phone, and address from Site Settings.', 'sage')),
                Builder::boolean('field_hk_contact_social', __('Social links', 'sage'), 'show_social', 1, __('Show', 'sage'), __('Hide', 'sage'), __('Uses the social links from Site Settings.', 'sage')),
                Builder::boolean('field_hk_contact_form', __('Contact form', 'sage'), 'show_form', 0, __('Show', 'sage'), __('Hide', 'sage'), __('Shows the Contact Form 7 form selected below.', 'sage')),
                self::when(Builder::postObject('field_hk_contact_form_id', __('Form', 'sage'), 'form', ['wpcf7_contact_form'], __('Edit the fields and recipient under Contact → Contact Forms.', 'sage')), 'field_hk_contact_form', '1'),
                Builder::oembed('field_hk_contact_map', __('Map', 'sage'), 'map', __('Optional. Paste a map link that WordPress can embed.', 'sage')),
                self::background('field_hk_contact_background'),
                self::align('field_hk_contact_align'),
            ]),
            self::layout('layout_hk_full_media', 'full_media', __('Full-width media', 'sage'), [
                Builder::select('field_hk_full_media_type', __('Media', 'sage'), 'media_type', [
                    'image' => __('Image', 'sage'),
                    'video' => __('Video', 'sage'),
                ], 'image'),
                self::when(Builder::image('field_hk_full_media_image', __('Image', 'sage'), 'image'), 'field_hk_full_media_type', 'image'),
                self::when(Builder::file('field_hk_full_media_video', __('Video', 'sage'), 'video', __('MP4 or WebM.', 'sage')), 'field_hk_full_media_type', 'video'),
                Builder::text('field_hk_full_media_caption', __('Caption', 'sage'), 'caption', __('Optional.', 'sage')),
                Builder::select('field_hk_full_media_aspect', __('Aspect ratio', 'sage'), 'aspect', [
                    'video' => __('16:9', 'sage'),
                    'square' => __('Square', 'sage'),
                    'wide' => __('Wide', 'sage'),
                    'portrait' => __('Portrait', 'sage'),
                ], 'video', __('Approved ratio.', 'sage')),
            ]),
            self::layout('layout_hk_spacer', 'spacer', __('Spacer', 'sage'), [
                Builder::select('field_hk_spacer_space', __('Spacing', 'sage'), 'spacing', [
                    'none' => __('None', 'sage'),
                    'small' => __('Small', 'sage'),
                    'medium' => __('Medium', 'sage'),
                    'large' => __('Large', 'sage'),
                ], 'medium', __('Approved space.', 'sage')),
                Builder::select('field_hk_spacer_divider', __('Divider', 'sage'), 'divider', [
                    'none' => __('None', 'sage'),
                    'line' => __('Line', 'sage'),
                ], 'none', __('Optional line.', 'sage')),
            ]),
        ];

        $keyed = [];

        foreach ($layouts as $layout) {
            $keyed[$layout['key']] = $layout;
        }

        return $keyed;
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @return array<string, mixed>
     */
    private static function layout(string $key, string $name, string $label, array $fields): array
    {
        return [
            'key' => $key,
            'name' => $name,
            'label' => $label,
            'display' => 'block',
            'sub_fields' => $fields,
            'min' => '',
            'max' => '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function background(string $key): array
    {
        return Builder::select($key, __('Background', 'sage'), 'section_background', [
            'default' => __('Default', 'sage'),
            'surface' => __('Surface', 'sage'),
        ], 'default', __('Approved background.', 'sage'));
    }

    /**
     * @return array<string, mixed>
     */
    private static function align(string $key): array
    {
        return Builder::select($key, __('Text alignment', 'sage'), 'section_align', [
            'left' => __('Left', 'sage'),
            'center' => __('Center', 'sage'),
        ], 'left', __('Approved alignment.', 'sage'));
    }

    /**
     * @return array<string, mixed>
     */
    private static function columns(string $key): array
    {
        return Builder::select($key, __('Columns', 'sage'), 'columns', [
            '2' => __('Two', 'sage'),
            '3' => __('Three', 'sage'),
            '4' => __('Four', 'sage'),
        ], '3', __('Approved column count.', 'sage'));
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    private static function when(array $field, string $fieldKey, string $value): array
    {
        $field['conditional_logic'] = Builder::showWhen($fieldKey, $value);

        return $field;
    }

    /**
     * @param  array<string, mixed>  $field
     * @param  list<string>  $values
     * @return array<string, mixed>
     */
    private static function whenAny(array $field, string $fieldKey, array $values): array
    {
        $field['conditional_logic'] = array_map(function (string $value) use ($fieldKey) {
            return [[
                'field' => $fieldKey,
                'operator' => '==',
                'value' => $value,
            ]];
        }, $values);

        return $field;
    }
}
