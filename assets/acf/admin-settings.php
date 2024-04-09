<?php

if (!defined('ABSPATH')) {
    exit; // Don't access directly
};

use const LEXO\AcfSeo\{
    SETTINGS_PAGE_SLUG,
    PLUGIN_NAME
};

return [
    'key' => 'group_6152d9f341901',
    'title' => sprintf(
        __('%s Settings', 'acfseo'),
        PLUGIN_NAME
    ),
    'fields' => [
        [
            'key' => 'field_6152da7a29e01',
            'label' => _x('Mobile properties', 'Tab in Admin Settings', 'acfseo'),
            'name' => '',
            'type' => 'tab',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'placement' => 'left',
            'endpoint' => 0,
        ],
        [
            'key' => 'field_654cf1fa2d002',
            'label' => __('Global Theme color', 'acfseo'),
            'name' => 'seo_theme_color_global',
            'aria-label' => '',
            'type' => 'color_picker',
            'instructions' => __('Set a global Theme color here, which is set as the background for the respective tab on Android on Chrome. This setting only affects <b>Chrome on Android.</b>', 'acfseo'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'default_value' => '#ffffff',
            'enable_opacity' => 0,
            'return_format' => 'string',
        ]
    ],
    'location' => [
        [
            [
                'param' => 'options_page',
                'operator' => '==',
                'value' => SETTINGS_PAGE_SLUG,
            ],
        ],
    ],
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'field',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
];
