<?php

if (!defined('ABSPATH')) {
    exit; // Don't access directly
};

use const LEXO\AcfSeo\{
    PATH,
    URL
};

return [
    'priority'  => 90,
    'dist_path' => PATH . 'dist',
    'dist_uri'  => URL . 'dist',
    'assets'    => [
        'front' => [
            'styles'    => [],
            'scripts'   => []
        ],
        'admin' => [
            'styles'    => [],
            'scripts'   => []
        ],
        'editor' => [
            'styles'    => []
        ],
    ]
];
