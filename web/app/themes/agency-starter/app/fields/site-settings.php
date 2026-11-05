<?php

/**
 * Global site fields. Free ACF has no Options Page, so these are edited
 * on the private Site Settings page.
 */

namespace App\Fields;

add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_hk_site_settings',
        'title' => __('Site Settings', 'sage'),
        'fields' => [
            Builder::tab('field_hk_tab_general', __('General Information', 'sage')),
            Builder::text('field_hk_company_name', __('Company name', 'sage'), 'company_name', __('Shown in the header when no header logo is selected.', 'sage')),
            Builder::textarea('field_hk_company_description', __('Business description', 'sage'), 'company_description', __('Shown in the footer when the footer description is empty.', 'sage')),
            Builder::image('field_hk_primary_logo', __('Primary logo', 'sage'), 'primary_logo', __('PNG or WebP, at least 480 pixels wide. A preview appears after you select the file.', 'sage')),
            Builder::image('field_hk_alternate_logo', __('Alternate logo', 'sage'), 'alternate_logo', __('Used in the footer when a footer logo is not selected. PNG or WebP, at least 480 pixels wide.', 'sage')),
            Builder::group('field_hk_contact', __('Contact details', 'sage'), 'contact', [
                Builder::email('field_hk_contact_email', __('Email', 'sage'), 'email'),
                Builder::text('field_hk_contact_phone', __('Phone number', 'sage'), 'phone', __('Shown as typed, for example 212 555 0148.', 'sage')),
                Builder::textarea('field_hk_contact_address', __('Address', 'sage'), 'address', __('One line per line of the address.', 'sage'), 3),
            ], __('Used on the contact page and in the footer.', 'sage')),
            Builder::tab('field_hk_tab_header', __('Header', 'sage')),
            Builder::image('field_hk_header_logo', __('Header logo', 'sage'), 'header_logo', __('Falls back to the primary logo. PNG or WebP, at least 480 pixels wide.', 'sage')),
            Builder::boolean('field_hk_announcement_enabled', __('Announcement bar', 'sage'), 'announcement_enabled', 0, __('Enabled', 'sage'), __('Disabled', 'sage'), __('A short line above the header.', 'sage')),
            whenEnabled(Builder::text('field_hk_announcement_text', __('Announcement text', 'sage'), 'announcement_text', __('One sentence. Leave the link blank for text only.', 'sage')), 'field_hk_announcement_enabled'),
            whenEnabled(Builder::link('field_hk_announcement_link', __('Announcement link', 'sage'), 'announcement_link', __('Optional. The link title is the text visitors click.', 'sage')), 'field_hk_announcement_enabled'),
            Builder::link('field_hk_header_cta', __('Header button', 'sage'), 'header_cta', __('Optional. The link title is the button label.', 'sage')),
            Builder::boolean('field_hk_sticky_header', __('Sticky header', 'sage'), 'sticky_header', 0, __('Enabled', 'sage'), __('Disabled', 'sage'), __('Keep the header at the top while scrolling.', 'sage')),
            Builder::tab('field_hk_tab_footer', __('Footer', 'sage')),
            Builder::image('field_hk_footer_logo', __('Footer logo', 'sage'), 'footer_logo', __('Falls back to the alternate logo, then the primary logo. PNG or WebP, at least 480 pixels wide.', 'sage')),
            Builder::textarea('field_hk_footer_description', __('Footer description', 'sage'), 'footer_description', __('Falls back to the business description.', 'sage')),
            Builder::group('field_hk_footer_contact', __('Contact details', 'sage'), 'footer_contact', [
                Builder::email('field_hk_footer_email', __('Email', 'sage'), 'email'),
                Builder::text('field_hk_footer_phone', __('Phone number', 'sage'), 'phone'),
                Builder::textarea('field_hk_footer_address', __('Address', 'sage'), 'address', '', 3),
            ], __('Leave a field blank to use the matching general contact detail.', 'sage')),
            Builder::link('field_hk_footer_cta', __('Footer button', 'sage'), 'footer_cta', __('Optional. The link title is the button label.', 'sage')),
            Builder::text('field_hk_copyright_text', __('Copyright text', 'sage'), 'copyright_text', __('Leave blank to use the current year and company name.', 'sage')),
            Builder::tab('field_hk_tab_social', __('Social Media', 'sage')),
            Builder::group('field_hk_social', __('Social links', 'sage'), 'social', [
                Builder::url('field_hk_social_instagram', __('Instagram', 'sage'), 'instagram'),
                Builder::url('field_hk_social_facebook', __('Facebook', 'sage'), 'facebook'),
                Builder::url('field_hk_social_linkedin', __('LinkedIn', 'sage'), 'linkedin'),
                Builder::url('field_hk_social_youtube', __('YouTube', 'sage'), 'youtube'),
                Builder::text('field_hk_social_other_label_1', __('Other label', 'sage'), 'other_label_1'),
                Builder::url('field_hk_social_other_url_1', __('Other URL', 'sage'), 'other_url_1'),
                Builder::text('field_hk_social_other_label_2', __('Second other label', 'sage'), 'other_label_2'),
                Builder::url('field_hk_social_other_url_2', __('Second other URL', 'sage'), 'other_url_2'),
            ]),
            Builder::tab('field_hk_tab_design', __('Design Options', 'sage')),
            Builder::color('field_hk_accent_color', __('Accent color', 'sage'), 'accent_color', __('Hex color only. Custom CSS is not accepted.', 'sage')),
            Builder::select('field_hk_header_variation', __('Default header variation', 'sage'), 'default_header_variation', [
                'default' => __('Default', 'sage'),
                'minimal' => __('Minimal', 'sage'),
            ], 'default'),
            Builder::select('field_hk_footer_variation', __('Default footer variation', 'sage'), 'default_footer_variation', [
                'default' => __('Default', 'sage'),
                'stacked' => __('Stacked', 'sage'),
            ], 'default'),
            Builder::select('field_hk_default_background', __('Background', 'sage'), 'default_background', [
                'default' => __('Default', 'sage'),
                'surface' => __('Surface', 'sage'),
            ], 'default'),
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'template-site-settings.blade.php',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'show_in_rest' => 0,
    ]);
});

/**
 * @param  array<string, mixed>  $field
 * @return array<string, mixed>
 */
function whenEnabled(array $field, string $fieldKey): array
{
    $field['conditional_logic'] = Builder::showWhen($fieldKey, '1');

    return $field;
}
