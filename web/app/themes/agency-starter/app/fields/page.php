<?php

/**
 * Page sections. The field definitions live in Sections.php.
 */

namespace App\Fields;

add_action('acf/include_fields', function () {
    Sections::register();
});
