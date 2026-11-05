<?php

/**
 * Sample content for Home, Work, Studio, Services, Journal, and Contact.
 *
 * Run from the project root:
 * lando wp eval-file scripts/seed-pages.php
 *
 * This replaces the flexible sections on those six pages. It leaves the
 * section-library page alone. The copy is editable development content.
 */

use App\Acf\Content;
use App\Fields\Sections;

if (! function_exists('wp_insert_post')) {
    fwrite(STDERR, "Load this file with WP-CLI eval-file.\n");
    exit(1);
}

function hk_page(string $slug, string $title): int
{
    $existing = get_page_by_path($slug, OBJECT, 'page');
    $data = [
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => '',
    ];

    if ($existing instanceof WP_Post) {
        $data['ID'] = $existing->ID;
        $id = (int) wp_update_post($data);
    } else {
        $id = (int) wp_insert_post($data);
    }

    if ($id < 1) {
        fwrite(STDERR, "Could not save the {$title} page.\n");
        exit(1);
    }

    return $id;
}

function hk_entry(string $type, string $slug, string $title, string $excerpt, string $content, int $image = 0): int
{
    $existing = get_page_by_path($slug, OBJECT, $type);
    $data = [
        'post_type' => $type,
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => $excerpt,
        'post_content' => $content,
        'post_author' => 1,
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

function hk_image(string $key): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_hk_demo_image',
        'meta_value' => $key,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);

    return $existing ? (int) $existing[0] : 0;
}

function hk_link(string $path, string $title): array
{
    return [
        'url' => home_url($path),
        'title' => $title,
        'target' => '',
    ];
}

function hk_term(string $taxonomy, string $name, string $slug): int
{
    $existing = term_exists($slug, $taxonomy);

    if (! $existing) {
        $existing = wp_insert_term($name, $taxonomy, ['slug' => $slug]);
    }

    if (is_wp_error($existing)) {
        fwrite(STDERR, $existing->get_error_message()."\n");
        exit(1);
    }

    return (int) (is_array($existing) ? $existing['term_id'] : $existing);
}

function hk_contact_form(): int
{
    if (! class_exists('WPCF7_ContactForm')) {
        fwrite(STDERR, "Activate Contact Form 7 before seeding the contact page.\n");
        exit(1);
    }

    $forms = get_posts([
        'post_type' => 'wpcf7_contact_form',
        'post_status' => 'any',
        'numberposts' => 20,
    ]);

    foreach ($forms as $formPost) {
        if ($formPost->post_title === 'Contact') {
            return (int) $formPost->ID;
        }
    }

    $host = (string) wp_parse_url(home_url(), PHP_URL_HOST);
    $form = WPCF7_ContactForm::get_template(['title' => 'Contact']);
    $properties = $form->get_properties();
    $properties['form'] = implode("\n", [
        '<label> Name',
        '    [text* your-name autocomplete:name] </label>',
        '',
        '<label> Email',
        '    [email* your-email autocomplete:email] </label>',
        '',
        '<label> Message',
        '    [textarea* your-message] </label>',
        '',
        '[submit "Send"]',
    ]);
    $properties['mail'] = array_merge($properties['mail'], [
        'active' => true,
        'recipient' => '[_site_admin_email]',
        'sender' => '[_site_title] <wordpress@'.$host.'>',
        'subject' => '[_site_title] "[your-name]"',
        'additional_headers' => 'Reply-To: [your-email]',
        'body' => "From: [your-name] <[your-email]>\n\n[your-message]",
        'attachments' => '',
        'use_html' => false,
        'exclude_blank' => false,
    ]);
    $form->set_properties($properties);
    $form->save();

    return (int) $form->id();
}

$field = hk_image('hk-field');
$stone = hk_image('hk-stone');
$clay = hk_image('hk-clay');
$mist = hk_image('hk-mist');
$paper = hk_image('hk-paper');
$ink = hk_image('hk-ink');

if ($field < 1 || $stone < 1) {
    fwrite(STDERR, "Run scripts/seed-section-library.php first so the sample images exist.\n");
    exit(1);
}

$northline = hk_entry('project', 'northline', 'Northline', 'Identity and site for a research studio.', 'A research studio identity, from the name to the site.', $stone);
$atelier = hk_entry('project', 'atelier-house', 'Atelier House', 'A house and the publications around it.', 'Editorial and spatial work for a private house.', $clay);
$harbor = hk_entry('project', 'harbor-index', 'Harbor Index', 'An index of waterfront places.', 'A digital index with a quiet visual system.', $mist);

update_field('project_client', 'Northline', $northline);
update_field('project_year', '2026', $northline);
update_field('project_url', ['url' => 'https://example.com', 'title' => 'Visit the site', 'target' => '_blank'], $northline);
update_post_meta($northline, 'project_gallery', [$stone, $clay, $paper]);
update_post_meta($northline, '_project_gallery', 'field_hk_project_gallery');

update_field('project_client', 'Atelier', $atelier);
update_field('project_year', '2025', $atelier);
update_field('project_client', 'Harbor', $harbor);
update_field('project_year', '2024', $harbor);

$strategy = hk_entry('service', 'strategy', 'Strategy', 'Positioning, narrative, and the sequence of a project.', 'Strategy sets the order of the work.', $mist);
$identity = hk_entry('service', 'identity', 'Identity', 'Names, marks, and the rules that keep them whole.', 'Identity covers the name, the mark, and its use.', $stone);
$digital = hk_entry('service', 'digital', 'Digital', 'Sites and tools with the same visual standard.', 'Digital work carries the identity into use.', $clay);

wp_update_post(['ID' => $digital, 'menu_order' => 1]);
wp_update_post(['ID' => $strategy, 'menu_order' => 2]);
wp_update_post(['ID' => $identity, 'menu_order' => 3]);
update_field('project_services', [$identity, $digital], $northline);

$avery = hk_entry('team_member', 'avery-cole', 'Avery Cole', 'Design director. Leads identity and digital programs.', 'Avery leads identity and digital programs.', $ink);
$jordan = hk_entry('team_member', 'jordan-ellis', 'Jordan Ellis', 'Leads spatial and editorial work.', 'Jordan leads spatial and editorial work.', $paper);
update_field('team_role', 'Design Director', $avery);
update_field('team_role', 'Spatial Lead', $jordan);

$identityTerm = hk_term('project_category', 'Identity', 'identity');
$spaceTerm = hk_term('project_category', 'Space', 'space');
$digitalTerm = hk_term('project_category', 'Digital', 'digital');
wp_set_object_terms($northline, [$identityTerm], 'project_category');
wp_set_object_terms($atelier, [$spaceTerm], 'project_category');
wp_set_object_terms($harbor, [$digitalTerm], 'project_category');

Sections::save($northline, [
    [
        'acf_fc_layout' => 'introduction',
        'heading' => 'The name came first',
        'text' => '<p>The identity started with a short written position, then the site followed the same measure.</p>',
        'image' => $mist,
        'layout' => 'image-right',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
]);

$notes = hk_term('category', 'Notes', 'notes');
$practice = hk_term('category', 'Practice', 'practice');

$articles = [
    ['first-note', 'A note on pace', 'The work moves at the speed of the decision.', $stone, $notes],
    ['second-note', 'Material before mood', 'Samples come before the palette is named.', $clay, $notes],
    ['third-note', 'What the grid holds', 'The grid is a constraint, not a style.', $mist, $notes],
    ['fourth-note', 'A margin for revision', 'The schedule keeps a week with nothing new in it.', $paper, $notes],
    ['studio-practice', 'How a week is built', 'Two reviews, one written note, and the rest is making.', $ink, $practice],
];

$hello = get_page_by_path('hello-world', OBJECT, 'post');

if ($hello instanceof WP_Post && $hello->post_status === 'publish') {
    wp_update_post(['ID' => $hello->ID, 'post_status' => 'draft']);
}

$articleIds = [];

foreach ($articles as [$slug, $title, $excerpt, $image, $term]) {
    $articleIds[$slug] = hk_entry('post', $slug, $title, $excerpt, '<p>'.$excerpt.'</p>', $image);
    wp_set_post_categories($articleIds[$slug], [$term]);
}

$home = hk_page('home', 'Home');
$work = hk_page('work', 'Work');
$studio = hk_page('studio', 'Studio');
$services = hk_page('services', 'Services');
$journal = hk_page('journal', 'Journal');
$contact = hk_page('contact', 'Contact');

update_post_meta($home, '_wp_page_template', 'default');
update_post_meta($journal, '_wp_page_template', 'default');
update_post_meta($work, '_wp_page_template', 'template-work.blade.php');
update_post_meta($studio, '_wp_page_template', 'template-studio.blade.php');
update_post_meta($services, '_wp_page_template', 'template-services.blade.php');
update_post_meta($contact, '_wp_page_template', 'template-contact.blade.php');

update_option('show_on_front', 'page');
update_option('page_on_front', $home);
update_option('page_for_posts', $journal);
update_option('posts_per_page', 3);

update_field('featured_heading', 'Featured', $journal);
update_field('featured_posts', [$articleIds['first-note']], $journal);

$formId = hk_contact_form();

Sections::save($home, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'MAISON & FORM',
        'heading' => 'Work with a clear point of view',
        'text' => 'Identity, space, and digital work for clients who want fewer things, made carefully.',
        'image' => $field,
        'primary_link' => hk_link('/contact/', 'Start a project'),
        'secondary_link' => hk_link('/work/', 'View work'),
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'screen',
    ],
    [
        'acf_fc_layout' => 'introduction',
        'heading' => 'A small studio, on purpose',
        'text' => '<p>Each project has a lead, a written position, and time left for revision. The work stays with the people who start it.</p>',
        'image' => $stone,
        'layout' => 'image-right',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'portfolio',
        'heading' => 'Selected work',
        'text' => 'Three recent projects, in the order we want them read.',
        'source' => 'selected',
        'projects' => [$harbor, $northline, $atelier],
        'layout' => 'grid',
        'view_all' => hk_link('/work/', 'All work'),
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'services',
        'heading' => 'What we do',
        'text' => 'Three practices, arranged for this page.',
        'source' => 'records',
        'services' => [$digital, $strategy, $identity],
        'columns' => '3',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'image_text',
        'heading' => 'The studio',
        'text' => 'A room on Mercer Street, a short list of people, and a way of working that stays the same from project to project.',
        'image' => $ink,
        'button' => hk_link('/studio/', 'Meet the studio'),
        'image_position' => 'left',
        'section_background' => 'surface',
    ],
    [
        'acf_fc_layout' => 'testimonials',
        'heading' => 'In their words',
        'testimonials' => [
            [
                'quote' => 'They kept the work smaller than we asked, and the result was clearer.',
                'name' => 'Mara Ellison',
                'role' => 'Director',
                'company' => 'Northline',
                'image' => $paper,
            ],
            [
                'quote' => 'The site and the printed piece finally use the same measure.',
                'name' => 'Owen Park',
                'role' => 'Editor',
                'company' => 'Atelier House',
                'image' => $mist,
            ],
        ],
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'Begin with a conversation',
        'text' => 'Tell us what the work needs to hold. We will reply with a way to start.',
        'section_background' => 'surface',
        'primary_link' => hk_link('/contact/', 'Contact the studio'),
        'secondary_link' => hk_link('/journal/', 'Read the journal'),
        'section_align' => 'left',
    ],
]);

Sections::save($work, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'Work',
        'heading' => 'Selected and recent projects',
        'text' => 'Filter by the kind of work, or read the full list.',
        'image' => $clay,
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'standard',
    ],
    [
        'acf_fc_layout' => 'portfolio',
        'heading' => 'Projects',
        'text' => 'Every published project. Categories come from the project record.',
        'source' => 'archive',
        'count' => 12,
        'show_filters' => 1,
        'layout' => 'grid',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'Have a project in mind?',
        'text' => 'We take a small number of new projects each year.',
        'section_background' => 'surface',
        'primary_link' => hk_link('/contact/', 'Start a project'),
        'section_align' => 'left',
    ],
]);

Sections::save($studio, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'Studio',
        'heading' => 'MAISON & FORM',
        'text' => 'A design studio in New York, working across identity, space, and digital.',
        'image' => $field,
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'tall',
    ],
    [
        'acf_fc_layout' => 'introduction',
        'heading' => 'How the studio works',
        'text' => '<p>The studio stays small so the people in the room are the people doing the work. Projects begin with a written position and end when that position is visible in the finished piece.</p>',
        'image' => $stone,
        'layout' => 'image-right',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'introduction',
        'heading' => 'Our story',
        'text' => '<p>MAISON &amp; FORM started as a shared table for identity and spatial work. The digital practice grew from the same drawings, not from a separate team.</p>',
        'image' => $clay,
        'layout' => 'image-left',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'feature_grid',
        'heading' => 'Mission and values',
        'text' => 'Three commitments that stay on the wall.',
        'features' => [
            ['heading' => 'Fewer things', 'text' => 'We would rather finish one piece well than start three.'],
            ['heading' => 'Written first', 'text' => 'A project has a sentence before it has a visual.'],
            ['heading' => 'Same people', 'text' => 'The lead at the start is the lead at the end.'],
        ],
        'columns' => '3',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'team',
        'heading' => 'People',
        'source' => 'records',
        'team' => [$avery, $jordan],
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'gallery',
        'heading' => 'The studio',
        'images' => [$field, $ink, $paper, $mist],
        'columns' => '2',
        'lightbox' => 1,
        'section_background' => 'default',
    ],
    [
        'acf_fc_layout' => 'feature_grid',
        'heading' => 'Recognition',
        'features' => [
            ['heading' => 'Type Directors Club', 'text' => 'Northline identity, 2026.'],
            ['heading' => 'ADC Awards', 'text' => 'Atelier House publications, 2025.'],
        ],
        'columns' => '2',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'Work with the studio',
        'text' => 'New projects start with a short written brief.',
        'section_background' => 'default',
        'primary_link' => hk_link('/contact/', 'Write to us'),
        'section_align' => 'left',
    ],
]);

Sections::save($services, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'Services',
        'heading' => 'Three practices',
        'text' => 'Strategy, identity, and digital work, taken together or one at a time.',
        'image' => $mist,
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'standard',
    ],
    [
        'acf_fc_layout' => 'introduction',
        'heading' => 'How the practices fit',
        'text' => '<p>A project can use one practice or all three. The order below is the order for this page, and it can be changed in the editor.</p>',
        'section_background' => 'default',
        'section_align' => 'left',
        'layout' => 'stacked',
    ],
    [
        'acf_fc_layout' => 'services',
        'heading' => 'Services',
        'text' => 'Drag the records in this section to change the order.',
        'source' => 'records',
        'services' => [$digital, $strategy, $identity],
        'columns' => '3',
        'section_background' => 'surface',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'feature_grid',
        'heading' => 'Capabilities',
        'features' => [
            ['heading' => 'Naming', 'text' => 'A name that can live on a door and in a sentence.'],
            ['heading' => 'Identity systems', 'text' => 'Marks, type, and the rules for using them.'],
            ['heading' => 'Sites', 'text' => 'Editorial websites with the same visual standard.'],
            ['heading' => 'Publications', 'text' => 'Books and papers that share the identity.'],
        ],
        'columns' => '2',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'process',
        'heading' => 'Working process',
        'text' => 'The same sequence, whatever the practice.',
        'steps' => [
            ['heading' => 'Listen', 'text' => 'A written brief and one conversation before any visual work.'],
            ['heading' => 'Frame', 'text' => 'A position, a schedule, and the things we will not do.'],
            ['heading' => 'Make', 'text' => 'Samples first, then the finished piece, with a week left to revise.'],
        ],
        'section_background' => 'surface',
    ],
    [
        'acf_fc_layout' => 'portfolio',
        'heading' => 'Selected projects',
        'text' => 'Work that shows more than one practice.',
        'source' => 'selected',
        'projects' => [$northline, $atelier],
        'layout' => 'grid',
        'view_all' => hk_link('/work/', 'All work'),
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'faq',
        'heading' => 'Questions',
        'questions' => [
            ['question' => 'Can we hire one practice?', 'answer' => '<p>Yes. Identity, strategy, or digital can start on its own.</p>'],
            ['question' => 'Where does a project begin?', 'answer' => '<p>With a short note about what the work needs to hold, sent through the contact page.</p>'],
        ],
        'section_background' => 'surface',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'Tell us which practice you need',
        'text' => 'We will answer with a way to begin.',
        'section_background' => 'default',
        'primary_link' => hk_link('/contact/', 'Contact the studio'),
        'section_align' => 'left',
    ],
]);

Sections::save($journal, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'Journal',
        'heading' => 'Notes from the studio',
        'text' => 'Short writing on pace, materials, and the way a project is held.',
        'image' => $paper,
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'standard',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'Bring a project',
        'text' => 'The journal is how we think in public. The work itself starts with a note.',
        'section_background' => 'surface',
        'primary_link' => hk_link('/contact/', 'Start a project'),
        'section_align' => 'left',
    ],
]);

Sections::save($contact, [
    [
        'acf_fc_layout' => 'hero',
        'eyebrow' => 'Contact',
        'heading' => 'Write to the studio',
        'text' => 'A note is enough. We read everything that arrives.',
        'image' => $ink,
        'section_align' => 'left',
        'section_background' => 'default',
        'min_height' => 'standard',
    ],
    [
        'acf_fc_layout' => 'contact',
        'heading' => 'Studio',
        'text' => '<p>Email, phone, and the Mercer Street address are kept in Site Settings so the footer and this page stay the same.</p>',
        'show_details' => 1,
        'show_social' => 1,
        'show_form' => 1,
        'form' => $formId,
        'map' => '',
        'section_background' => 'default',
        'section_align' => 'left',
    ],
    [
        'acf_fc_layout' => 'call_to_action',
        'heading' => 'See the work first',
        'text' => 'The project list is a faster way to tell whether we are the right studio.',
        'section_background' => 'surface',
        'primary_link' => hk_link('/work/', 'View work'),
        'section_align' => 'left',
    ],
]);

$menuName = 'Primary';
$menu = wp_get_nav_menu_object($menuName);

if (! $menu) {
    $menuId = (int) wp_create_nav_menu($menuName);
} else {
    $menuId = (int) $menu->term_id;
}

$locations = get_theme_mod('nav_menu_locations', []);
$locations['primary_navigation'] = $menuId;
set_theme_mod('nav_menu_locations', $locations);

$order = [
    'home' => $home,
    'work' => $work,
    'studio' => $studio,
    'services' => $services,
    'journal' => $journal,
    'contact' => $contact,
];
$existingItems = wp_get_nav_menu_items($menuId) ?: [];
$byObject = [];

foreach ($existingItems as $item) {
    if ($item->type === 'post_type' && $item->object === 'page') {
        $byObject[(int) $item->object_id] = (int) $item->ID;
    }
}

$position = 1;

foreach ($order as $pageId) {
    wp_update_nav_menu_item($menuId, $byObject[$pageId] ?? 0, [
        'menu-item-title' => get_the_title($pageId),
        'menu-item-object' => 'page',
        'menu-item-object-id' => $pageId,
        'menu-item-type' => 'post_type',
        'menu-item-status' => 'publish',
        'menu-item-position' => $position,
    ]);
    $position++;
}

flush_rewrite_rules(false);

echo "Pages seeded. Contact form {$formId}. Journal posts page {$journal}.\n";
