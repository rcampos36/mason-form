<?php

/**
 * Creates the Section library demonstration page and the records it displays.
 *
 * Run from the project root:
 * lando wp eval-file scripts/seed-section-library.php
 */

use App\Acf\Content;
use App\Fields\Sections;

if (! function_exists('wp_insert_post')) {
    fwrite(STDERR, "Load this file with WP-CLI eval-file.\n");
    exit(1);
}

require_once ABSPATH.'wp-admin/includes/image.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/media.php';

function hk_demo_image(string $key, int $red, int $green, int $blue, string $alt, string $caption): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_hk_demo_image',
        'meta_value' => $key,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);

    if ($existing) {
        return (int) $existing[0];
    }

    if (! function_exists('imagecreatetruecolor')) {
        fwrite(STDERR, "PHP GD is required to create demonstration images.\n");
        exit(1);
    }

    $image = imagecreatetruecolor(1600, 1000);
    imagefilledrectangle($image, 0, 0, 1600, 1000, imagecolorallocate($image, $red, $green, $blue));
    ob_start();
    imagepng($image);
    $bytes = ob_get_clean();
    imagedestroy($image);

    $upload = wp_upload_bits($key.'.png', null, $bytes);

    if (! empty($upload['error'])) {
        fwrite(STDERR, $upload['error']."\n");
        exit(1);
    }

    $id = wp_insert_attachment([
        'post_mime_type' => 'image/png',
        'post_title' => $alt,
        'post_excerpt' => $caption,
        'post_status' => 'inherit',
    ], $upload['file']);

    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    update_post_meta($id, '_hk_demo_image', $key);

    return (int) $id;
}

function hk_demo_entry(string $type, string $slug, string $title, string $excerpt, string $content, int $image = 0): int
{
    $existing = get_page_by_path($slug, OBJECT, $type);
    $data = [
        'post_type' => $type,
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => $excerpt,
        'post_content' => $content,
    ];

    if ($existing instanceof WP_Post) {
        $data['ID'] = $existing->ID;
        $id = (int) wp_update_post($data);
    } else {
        $id = (int) wp_insert_post($data);
    }

    if ($image > 0) {
        set_post_thumbnail($id, $image);
    }

    return $id;
}

function hk_link(string $path, string $title): array
{
    return [
        'url' => home_url($path),
        'title' => $title,
        'target' => '',
    ];
}

$field = hk_demo_image('hk-field', 32, 32, 32, 'Dark studio field', 'Studio interior');
$stone = hk_demo_image('hk-stone', 72, 64, 56, 'Stone study', 'Material study');
$clay = hk_demo_image('hk-clay', 120, 96, 78, 'Clay sample', 'Archive table');
$mist = hk_demo_image('hk-mist', 176, 176, 176, 'North light', 'North light');
$paper = hk_demo_image('hk-paper', 214, 206, 196, 'Paper sample', 'Paper sample');
$ink = hk_demo_image('hk-ink', 48, 48, 48, 'Model photograph', 'Model photograph');

$northline = hk_demo_entry('project', 'northline', 'Northline', 'Identity and site for a research studio.', 'A research studio identity, from the name to the site.', $stone);
$atelier = hk_demo_entry('project', 'atelier-house', 'Atelier House', 'A house and the publications around it.', 'Editorial and spatial work for a private house.', $clay);
$harbor = hk_demo_entry('project', 'harbor-index', 'Harbor Index', 'An index of waterfront places.', 'A digital index with a quiet visual system.', $mist);

update_field('project_client', 'Northline', $northline);
update_field('project_year', '2026', $northline);
update_field('project_client', 'Atelier', $atelier);
update_field('project_year', '2025', $atelier);
update_field('project_client', 'Harbor', $harbor);
update_field('project_year', '2024', $harbor);

hk_demo_entry('service', 'strategy', 'Strategy', 'Positioning, narrative, and the sequence of a project.', 'Strategy sets the order of the work.');
hk_demo_entry('service', 'identity', 'Identity', 'Names, marks, and the rules that keep them whole.', 'Identity covers the name, the mark, and its use.');
hk_demo_entry('service', 'digital', 'Digital', 'Sites and tools with the same visual standard.', 'Digital work carries the identity into use.');

$avery = hk_demo_entry('team_member', 'avery-cole', 'Avery Cole', 'Design director. Leads identity and digital programs.', 'Avery leads identity and digital programs.', $ink);
update_field('team_role', 'Design Director', $avery);

$notes = term_exists('notes', 'category');

if (! $notes) {
    $notes = wp_insert_term('Notes', 'category', ['slug' => 'notes']);
}

$notesId = (int) (is_array($notes) ? $notes['term_id'] : $notes);

foreach ([
    ['first-note', 'A note on pace', 'The work moves at the speed of the decision.'],
    ['second-note', 'Material before mood', 'Samples come before the palette is named.'],
    ['third-note', 'What the grid holds', 'The grid is a constraint, not a style.'],
] as [$slug, $title, $excerpt]) {
    $postId = hk_demo_entry('post', $slug, $title, $excerpt, '<p>'.$excerpt.'</p>');
    wp_set_post_categories($postId, [$notesId]);
}

$settings = Content::settingsId();

$savedContact = $settings > 0 ? get_field('contact', $settings) : null;

if ($settings > 0 && (! is_array($savedContact) || ! is_email((string) ($savedContact['email'] ?? '')))) {
    update_field('contact', [
        'email' => 'studio@heartwell-kessler.test',
        'phone' => '212 555 0148',
        'address' => "120 Mercer Street\nNew York, NY",
    ], $settings);
    update_field('social', [
        'instagram' => 'https://instagram.com/',
        'linkedin' => 'https://www.linkedin.com/',
    ], $settings);
}

$pageId = hk_demo_entry(
    'page',
    'section-library',
    'Section library',
    '',
    ''
);

Sections::save($pageId, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'MAISON & FORM',
        'heading' => 'A studio for considered work',
        'text' => 'Identity, space, and digital work for clients who want a clear point of view.',
        'image' => $field,
        'primary_link' => hk_link('/contact/', 'Start a project'),
        'secondary_link' => hk_link('/work/', 'View work'),
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'tall',
    ],
    [
        'acf_fc_layout' => 'introduction',
        'heading' => 'Introduction',
        'text' => '<p>The studio is small on purpose. Each project has a lead, a written point of view, and a pace that leaves room for revision.</p><p>Selected work lives on the <a href="'.esc_url(home_url('/work/')).'">work</a> page.</p>',
        'image' => $stone,
        'layout' => 'image-right',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'image_text',
        'heading' => 'Image and text',
        'text' => 'A two-column section for a single idea, with the image on the left and one action.',
        'image' => $clay,
        'button' => hk_link('/services/', 'See services'),
        'image_position' => 'left',
        'section_background' => 'default',
    ],
    [
        'acf_fc_layout' => 'feature_grid',
        'heading' => 'Feature grid',
        'text' => 'Three ways the studio usually begins.',
        'features' => [
            ['image' => $ink, 'heading' => 'Research', 'text' => 'Interviews, samples, and the constraints already in the room.', 'link' => hk_link('/studio/', 'Studio')],
            ['image' => $paper, 'heading' => 'Direction', 'text' => 'A short recommendation before production starts.', 'link' => hk_link('/services/', 'Services')],
            ['image' => $mist, 'heading' => 'Making', 'text' => 'Design and build against the same standard.', 'link' => ''],
        ],
        'columns' => '3',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'portfolio',
        'heading' => 'Portfolio grid',
        'text' => 'Three selected projects, in the order they were chosen.',
        'source' => 'selected',
        'projects' => [$northline, $atelier, $harbor],
        'count' => 3,
        'layout' => 'grid',
        'view_all' => hk_link('/work/', 'All work'),
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'services',
        'heading' => 'Services grid',
        'text' => 'Custom service items, each with an image, a title, a description, and a link.',
        'source' => 'items',
        'items' => [
            ['image' => $stone, 'heading' => 'Strategy', 'text' => 'Positioning and the sequence of the work.', 'link' => hk_link('/services/', 'Strategy')],
            ['image' => $clay, 'heading' => 'Identity', 'text' => 'Name, mark, and the rules around them.', 'link' => hk_link('/services/', 'Identity')],
            ['image' => $mist, 'heading' => 'Digital', 'text' => 'Sites and tools held to the same standard.', 'link' => hk_link('/services/', 'Digital')],
        ],
        'columns' => '3',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'testimonials',
        'heading' => 'Testimonials',
        'testimonials' => [
            ['quote' => 'They made the work feel inevitable.', 'name' => 'Mina Shah', 'role' => 'Director', 'company' => 'Northline', 'image' => $paper],
            ['quote' => 'Clear at the start, and still clear at the end.', 'name' => 'Eli Navarro', 'role' => 'Founder', 'company' => 'Harbor', 'image' => $ink],
        ],
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'statistics',
        'heading' => 'Statistics',
        'statistics' => [
            ['number' => '48', 'label' => 'Projects', 'text' => 'Completed with the current team.'],
            ['number' => '12', 'label' => 'Years', 'text' => 'Of independent practice.'],
            ['number' => '6', 'label' => 'Studios', 'text' => 'Partners we return to.'],
            ['number' => '3', 'label' => 'Disciplines', 'text' => 'Identity, space, and digital.'],
        ],
        'section_background' => 'surface',
    ],
    [
        'acf_fc_layout' => 'team',
        'heading' => 'Team',
        'source' => 'items',
        'members' => [
            [
                'name' => 'Avery Cole',
                'role' => 'Design Director',
                'image' => $ink,
                'biography' => 'Leads identity and digital programs. Previously with independent studios in New York and London.',
                'links' => [
                    ['label' => 'Instagram', 'url' => 'https://instagram.com/'],
                    ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/'],
                ],
            ],
            [
                'name' => 'Jordan Ellis',
                'role' => 'Strategist',
                'image' => $stone,
                'biography' => 'Writes the point of view and keeps the project sequence honest.',
                'links' => [
                    ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/'],
                ],
            ],
        ],
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'process',
        'heading' => 'Process',
        'text' => 'Three steps. The number follows the order of the rows.',
        'steps' => [
            ['heading' => 'Listen', 'text' => 'Interviews, constraints, and the material already in use.', 'image' => $paper],
            ['heading' => 'Shape', 'text' => 'A recommendation, then the first form of the work.', 'image' => 0],
            ['heading' => 'Deliver', 'text' => 'Files, guidelines, and the people who will use them.', 'image' => $mist],
        ],
        'section_background' => 'surface',
    ],
    [
        'acf_fc_layout' => 'gallery',
        'heading' => 'Image gallery',
        'images' => [$stone, $clay, $mist, $paper],
        'columns' => '2',
        'lightbox' => 1,
        'section_background' => 'default',
    ],
    [
        'acf_fc_layout' => 'logos',
        'heading' => 'Logo grid',
        'logos' => [
            ['image' => $paper, 'link' => hk_link('/', 'Paper')],
            ['image' => $mist, 'link' => hk_link('/work/', 'Mist')],
            ['image' => $clay, 'link' => ''],
            ['image' => $ink, 'link' => hk_link('/studio/', 'Ink')],
        ],
        'section_background' => 'surface',
    ],
    [
        'acf_fc_layout' => 'faq',
        'heading' => 'Questions',
        'questions' => [
            ['question' => 'How does a project start?', 'answer' => '<p>With a conversation and a written brief.</p><p>The <strong>schedule</strong> follows the scope, not the other way around.</p>'],
            ['question' => 'Where do you work?', 'answer' => '<p>The studio is in New York. Projects are not limited to the city.</p>'],
            ['question' => 'Can a section be removed?', 'answer' => '<p>Yes. In the page editor, remove or hide a section without deleting the others.</p>'],
        ],
        'section_background' => 'default',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'Call to action',
        'text' => 'A closing section with two buttons and a surface background.',
        'section_background' => 'surface',
        'primary_link' => hk_link('/contact/', 'Contact the studio'),
        'secondary_link' => hk_link('/work/', 'See the work'),
        'section_align' => 'center',
    ],
    [
        'acf_fc_layout' => 'journal',
        'heading' => 'Journal',
        'text' => 'The latest notes from one category.',
        'count' => 3,
        'category' => $notesId,
        'layout' => 'grid',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'full_media',
        'media_type' => 'image',
        'image' => $field,
        'caption' => 'Full-width media',
        'aspect' => 'wide',
    ],
    [
        'acf_fc_layout' => 'spacer',
        'spacing' => 'large',
        'divider' => 'line',
    ],
    [
        'acf_fc_layout' => 'contact',
        'heading' => 'Contact',
        'text' => '<p>Write with a timeline, a location, and what you want the work to do.</p>',
        'show_details' => 1,
        'show_social' => 1,
        'show_form' => 1,
        'section_background' => 'default',
        'section_align' => 'left',
    ],
]);

echo get_permalink($pageId)."\n";
