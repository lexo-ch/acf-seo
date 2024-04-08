<?php

namespace LEXO\AcfSeo\Core\Plugin;

use LEXO\AcfSeo\Core\Abstracts\Singleton;

use const LEXO\AcfSeo\{
    DOMAIN
};

class Fields extends Singleton
{
    protected static $instance = null;

    public function run()
    {
        $this->importFields();
    }

    public function importFields()
    {
        if (!function_exists('acf_add_local_field_group') || !function_exists('acf_add_local_field')) {
            return;
        }

        $location = [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ]
            ]
        ];
        $heading_1_type                 = 'text';
        $show_heading_2                 = true;
        $seo_title_recommended_length   = 60;
        $description_recommended_length = 160;
        $seo_title_maxlength            = 120;
        $description_maxlength          = 250;

        $location               = apply_filters(DOMAIN . '/location', $location);
        $heading_1_type         = apply_filters(DOMAIN . '/heading-1-type', $heading_1_type);
        $show_heading_2         = apply_filters(DOMAIN . '/show-heading-2', $show_heading_2);
        $seo_title_maxlength    = apply_filters(DOMAIN . '/seo-title/maxlength', $seo_title_maxlength);
        $description_maxlength  = apply_filters(DOMAIN . '/description/maxlength', $description_maxlength);

        $fields = [
            [
                'key' => 'field_654ce3e2d9808',
                'label' => __('Page title properties', 'acfseo'),
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'placement' => 'top',
                'endpoint' => 0,
            ]
        ];

        switch ($heading_1_type) {
            case 'editor':
                $fields[] = [
                    'key' => 'field_654ce426d9809',
                    'label' => __('Page heading 1', 'acfseo'),
                    'name' => 'seo_h1_title',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => __('Put the page title here. If this field is left blank, the respective page will have no title!', 'acfseo'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'tabs' => 'all',
                    'toolbar' => 'acf_seo_h1_editor',
                    'media_upload' => 0,
                    'delay' => 0,
                ];
                break;

            default:
                $fields[] = [
                    'key' => 'field_654ce426d9809',
                    'label' => __('Page heading 1', 'acfseo'),
                    'name' => 'seo_h1_title',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => __('Put the page title here. If this field is left blank, the respective page will have no title!', 'acfseo'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ];
                break;
        }

        if ($show_heading_2) {
            $fields[] = [
                'key' => 'field_654ce445d980a',
                'label' => __('Page heading 2', 'acfseo'),
                'name' => 'seo_h2_title',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => __('Put the page heading 2 here', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ];
        }

        $fields = array_merge($fields, [
            [
                'key' => 'field_654ce465d980b',
                'label' => __('Display in search engines', 'acfseo'),
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'placement' => 'top',
                'endpoint' => 0,
            ],
            [
                'key' => 'field_654ce477d980c',
                'label' => __('Search Engine Title', 'acfseo'),
                'name' => 'seo_title',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => sprintf(
                    __('Recommended Length: %s characters or less<br>Maximum Length: %s characters<br><br>If this field is left blank, the post\'s title will be used automatically. It\'s important to create a title that not only succinctly summarizes your article but also includes your key keywords. Strive for clarity and appeal, as this title is the first thing people will see in search engine results. Adhering to these recommendations can significantly enhance your post\'s visibility and attractiveness in search results.', 'acfseo'),
                    $seo_title_recommended_length,
                    $seo_title_maxlength
                ),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'maxlength' => $seo_title_maxlength,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ],
            [
                'key' => 'field_654ce49bd980d',
                'label' => __('Search Engine Description', 'acfseo'),
                'name' => 'seo_description',
                'aria-label' => '',
                'type' => 'textarea',
                'instructions' => sprintf(
                    __('Recommended Length: %s characters or less<br>Maximum Length: %s characters<br><br>The description appearing beneath the title in search engine results plays a crucial role in conveying the essence of the article. It should be concise yet comprehensive, promptly informing potential readers about the content\'s focus. Additionally, it\'s essential to integrate all relevant keywords seamlessly to enhance search relevance and visibility.', 'acfseo'),
                    $description_recommended_length,
                    $description_maxlength
                ),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'maxlength' => $description_maxlength,
                'rows' => '',
                'placeholder' => '',
                'new_lines' => '',
            ],
            [
                'key' => 'field_654ce4d2d980e',
                'label' => __('Search engine indexing', 'acfseo'),
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'placement' => 'top',
                'endpoint' => 0,
            ],
            [
                'key' => 'field_6613dc5ffc874',
                'label' => '',
                'name' => '',
                'aria-label' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'message' => sprintf(
                    __('Additional documentation about valid indexing and serving rules can be found <a href="%s" target="_blank">here</a>.', 'acfseo'),
                    'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag#directives'
                ),
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ],
            [
                'key' => 'field_654ce4ded980f',
                'label' => __('META Robot indexing settings', 'acfseo'),
                'name' => 'seo_meta_robots_index_setting',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => __('With the <b>"No indexing"</b> setting you can prevent Google robots from indexing your website.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'index' => __('Global default setup (index)', 'acfseo'),
                    'noindex' => __('No indexing'),
                ],
                'default_value' => false,
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ],
            [
                'key' => 'field_654ce511d9810',
                'label' => __('META Robot Follow Settings', 'acfseo'),
                'name' => 'seo_meta_robots_follow_setting',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => __('If the setting <b>"META robots do not follow"</b> is set, the Google robots will not follow the links on the page.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'follow' => __('Follow META robots', 'acfseo'),
                    'nofollow' => __('Don\'t follow META robots', 'acfseo'),
                ],
                'default_value' => false,
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ],
            [
                'key' => 'field_654ce511d9811',
                'label' => __('META Robot <code>max-image-preview</code> Settings', 'acfseo'),
                'name' => 'seo_meta_robots_max_image_preview_setting',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => __('Set the maximum size of an image preview for this page in a search results.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654ce4ded980f',
                            'operator' => '==',
                            'value' => 'index',
                        ],
                        [
                            'field' => 'field_654ce511d9810',
                            'operator' => '==',
                            'value' => 'follow',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '33.33',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'none' => __('None', 'acfseo'),
                    'standard' => __('Standard', 'acfseo'),
                    'large' => __('Large', 'acfseo')
                ],
                'default_value' => 'large',
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
            ],
            [
                'key' => 'field_6613d9d944887',
                'label' => __('META Robot <code>max-video-preview</code> Settings', 'acfseo'),
                'name' => 'seo_meta_robots_max_video_preview_setting',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => __('Set the number of seconds used as a video snippet for videos on this page in search results.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654ce4ded980f',
                            'operator' => '==',
                            'value' => 'index',
                        ],
                        [
                            'field' => 'field_654ce511d9810',
                            'operator' => '==',
                            'value' => 'follow',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '33.33',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => 0,
                'min' => -1,
                'max' => '',
                'placeholder' => '',
                'step' => '',
                'prepend' => '',
                'append' => '',
            ],
            [
                'key' => 'field_6613d9d944888',
                'label' => __('META Robot <code>max-snippet</code> Settings', 'acfseo'),
                'name' => 'seo_meta_robots_max_snippet_setting',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => __('Set the number of characters used as a textual snippet for this search result.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654ce4ded980f',
                            'operator' => '==',
                            'value' => 'index',
                        ],
                        [
                            'field' => 'field_654ce511d9810',
                            'operator' => '==',
                            'value' => 'follow',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '33.33',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => 0,
                'min' => -1,
                'max' => '',
                'placeholder' => '',
                'step' => '',
                'prepend' => '',
                'append' => '',
            ],
            [
                'key' => 'field_654ce542d9811',
                'label' => __('Duplicates Settings (Canonical URL)', 'acfseo'),
                'name' => 'seo_canonical_url',
                'aria-label' => '',
                'type' => 'url',
                'instructions' => __('If the content of the page is a duplicate of another page, the URL of the original page should be provided here. You can also use URLs from external websites.<br><br>Examples:<br>https://www.yourdomain.tld/news/news-post1<br>https://www.some-other-website.com/news/news-post-xyz', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'placeholder' => '',
            ],
            [
                'key' => 'field_654ce6452fc0a',
                'label' => __('Social Media', 'acfseo'),
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'placement' => 'top',
                'endpoint' => 0,
            ],
            [
                'key' => 'field_654cf05f98d51',
                'label' => __('Type', 'acfseo'),
                'name' => 'seo_social_type',
                'aria-label' => '',
                'type' => 'button_group',
                'instructions' => __('Enter your website type. If you don\'t know what type it is, it will default to <b>Website</b>.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'website' => __('Website'),
                    'blog' => __('Blog', 'acfseo'),
                    'article' => __('Article', 'acfseo'),
                ],
                'default_value' => 'website',
                'return_format' => 'value',
                'allow_null' => 0,
                'layout' => 'horizontal',
            ],
            [
                'key' => 'field_654ce6542fc0b',
                'label' => __('SEO Image', 'acfseo'),
                'name' => 'seo_image',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => __('The selected image must have a resolution of at least <strong>1200 x 630px</strong>.<br>The image will be used when the post appears on Facebook, for example. If no image is defined, Facebook will use a random image present somewhere in the post. By placing the image you can improve the appearance of the article on social media.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ],
                'return_format' => 'array',
                'library' => 'all',
                'min_width' => 1200,
                'min_height' => 630,
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ],
            [
                'key' => 'field_654cf0992d06a',
                'label' => __('Activate X.com settings', 'acfseo'),
                'name' => 'seo_twitter_activate',
                'aria-label' => '',
                'type' => 'checkbox',
                'instructions' => __('If you activate this setting, you will have access to the X.com Card settings.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'true' => __('Activate', 'acfseo'),
                ],
                'default_value' => [
                ],
                'return_format' => 'value',
                'allow_custom' => 0,
                'layout' => 'vertical',
                'toggle' => 0,
                'save_custom' => 0,
                'custom_choice_button_text' => '',
            ],
            [
                'key' => 'field_654cf0d92d06b',
                'label' => __('X.com Card Type', 'acfseo'),
                'name' => 'seo_twitter_card_type',
                'aria-label' => '',
                'type' => 'button_group',
                'instructions' => __('Set the type for your X.com card. This is crucial if you want to share the website via X.com. By default, <b>Summary</b> is selected.', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654cf0992d06a',
                            'operator' => '!=empty',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'summary' => __('Summary', 'acfseo'),
                    'summary_large_image' => __('Summary with large image', 'acfseo'),
                    'player' => __('Videos or images', 'acfseo'),
                ],
                'default_value' => 'summary',
                'return_format' => 'value',
                'allow_null' => 0,
                'layout' => 'horizontal',
            ],
            [
                'key' => 'field_654cf1242d06c',
                'label' => __('X.com @ Name', 'acfseo'),
                'name' => 'seo_twitter_name',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => __('Contains the @username of the website or company. This field is <b>optional</b>', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654cf0992d06a',
                            'operator' => '==',
                            'value' => 'true',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '33.33',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ],
            [
                'key' => 'field_654cf1662d06d',
                'label' => __('X.com @ Author', 'acfseo'),
                'name' => 'seo_twitter_creator',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => __('Contains the author\'s @username. This field is <b>optional</b>', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654cf0992d06a',
                            'operator' => '==',
                            'value' => 'true',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '33.333',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ],
            [
                'key' => 'field_654cf1882d06e',
                'label' => __('X.com Video', 'acfseo'),
                'name' => 'seo_twitter_video',
                'aria-label' => '',
                'type' => 'file',
                'instructions' => __('Upload the video that you want to display on the X.com card here. The video replaces the image (if set).', 'acfseo'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_654cf0992d06a',
                            'operator' => '==',
                            'value' => 'true',
                        ],
                        [
                            'field' => 'field_654cf0d92d06b',
                            'operator' => '==',
                            'value' => 'player',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '33.33',
                    'class' => '',
                    'id' => '',
                ],
                'return_format' => 'url',
                'library' => 'all',
                'min_size' => '',
                'max_size' => '',
                'mime_types' => '.mp4',
            ],
            [
                'key' => 'field_654cf1ed2d070',
                'label' => __('Mobile properties', 'acfseo'),
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'placement' => 'top',
                'endpoint' => 0,
            ],
            [
                'key' => 'field_654cf1fa2d071',
                'label' => __('Theme color', 'acfseo'),
                'name' => 'seo_theme_color',
                'aria-label' => '',
                'type' => 'color_picker',
                'instructions' => __('Set a color here, which is set as the background for the respective tab on Android on Chrome. This setting only affects <b>Chrome on Android</b>', 'acfseo'),
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
        ]);

        acf_add_local_field_group([
            'key'                   => 'group_' . DOMAIN,
            'title'                 => __('SEO Settings', 'acfseo'),
            'location'              => $location,
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => '',
            'show_in_rest'          => 0,
            'fields'                => $fields
        ]);
    }
}
