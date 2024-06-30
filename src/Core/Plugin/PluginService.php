<?php

namespace LEXO\AcfSeo\Core\Plugin;

use LEXO\AcfSeo\Core\Abstracts\Singleton;
use LEXO\AcfSeo\Core\Loader\Loader;
use LEXO\AcfSeo\Core\Updater\PluginUpdater;
use LEXO\AcfSeo\Core\Traits\Helpers;

use const LEXO\AcfSeo\{
    ASSETS,
    PLUGIN_NAME,
    PLUGIN_SLUG,
    VERSION,
    MIN_PHP_VERSION,
    MIN_WP_VERSION,
    DOMAIN,
    BASENAME,
    CACHE_KEY,
    UPDATE_PATH,
    SETTINGS_PAGE_SLUG
};

class PluginService extends Singleton
{
    use Helpers;

    private static string $namespace    = 'custom-plugin-namespace';
    protected static $instance          = null;

    private const ACF_DIR               = ASSETS . '/' . 'acf';
    private const CHECK_UPDATE          = 'check-update-' . PLUGIN_SLUG;
    private const MANAGE_PLUGIN_CAP     = 'edit_posts';
    private const SETTINGS_PARENT_SLUG  = 'options-general.php';

    public function setNamespace(string $namespace)
    {
        self::$namespace = $namespace;
    }

    public function registerNamespace()
    {
        $config = require_once trailingslashit(ASSETS) . 'config/config.php';

        $loader = Loader::getInstance();

        $loader->registerNamespace(self::$namespace, $config);

        add_action('admin_post_' . self::CHECK_UPDATE, [$this, 'checkForUpdateManually']);

        add_action('acf/render_field/type=text', [$this, 'renderCounter'], 20, 1);
        add_action('acf/render_field/type=textarea', [$this, 'renderCounter'], 20, 1);
    }

    public function importFields()
    {
        if (function_exists('acf_add_local_field_group')) {
            $assets = self::getFilesFromdirectory(self::ACF_DIR);

            if (is_array($assets) && !empty($assets)) {
                foreach ($assets as $file) {
                    acf_add_local_field_group(require_once trailingslashit(self::ACF_DIR) . $file);
                }
            }
        }
    }

    public static function getManagePluginCap()
    {
        $capability = self::MANAGE_PLUGIN_CAP;

        $capability = apply_filters(self::$namespace . '/options-page/capability', $capability);

        return $capability;
    }

    public static function getLocationArray()
    {
        $location = [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ]
            ]
        ];

        return apply_filters(DOMAIN . '/location', $location);
    }

    public function getPostIdsForExcluding()
    {
        $post_ids = get_posts([
            'post_type'      => Helpers::getPostTypesFromLocationArray(),
            'post_status'    => 'publish',
            'meta_key'       => 'seo_meta_robots_index_setting',
            'meta_value'     => 'noindex',
            'fields'         => 'ids',
            'posts_per_page' => -1,
        ]);

        if (!$post_ids) {
            return [];
        }

        return $post_ids;
    }

    public function excludePostsFromSitemap()
    {
        $post_ids = $this->getPostIdsForExcluding();

        if (!$post_ids) {
            return false;
        }

        add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) use ($post_ids) {
            if (!in_array($post_type, Helpers::getPostTypesFromLocationArray())) {
                return $args;
            }

            $args['post__not_in'] = isset($args['post__not_in']) ? $args['post__not_in'] : [];
            $args['post__not_in'] = array_merge($args['post__not_in'], $post_ids);

            return $args;
        }, 10, 2);
    }

    public static function getSettingsPageParentSlug()
    {
        $slug = self::SETTINGS_PARENT_SLUG;

        $slug = apply_filters(self::$namespace . '/options-page/parent-slug', $slug);

        return $slug;
    }

    public function addAdminLocalizedScripts()
    {
        $vars = [
            'plugin_name'       => PLUGIN_NAME,
            'plugin_slug'       => PLUGIN_SLUG,
            'plugin_version'    => VERSION,
            'min_php_version'   => MIN_PHP_VERSION,
            'min_wp_version'    => MIN_WP_VERSION,
            'text_domain'       => DOMAIN
        ];

        $vars = apply_filters(self::$namespace . '/admin_localized_script', $vars);

        wp_localize_script(trailingslashit(self::$namespace) . 'admin-' . DOMAIN . '.js', DOMAIN . 'AdminLocalized', $vars);
    }

    public function addPluginLinks()
    {
        add_filter(
            'plugin_action_links_' . BASENAME,
            [$this, 'setPluginLinks']
        );
    }

    public function setPluginLinks($links)
    {
        $update_check_url = self::getManualUpdateCheckLink();
        $update_check_link = "<a href='{$update_check_url}'>" . __('Update check', 'acfseo') . '</a>';

        $settings_url = self::getSettingsLink();
        $settings_link = "<a href='{$settings_url}'>" . __('Settings', 'acfseo') . '</a>';

        array_push(
            $links,
            $update_check_link,
            $settings_link
        );

        return $links;
    }

    public function updater()
    {
        return (new PluginUpdater())
            ->setBasename(BASENAME)
            ->setSlug(PLUGIN_SLUG)
            ->setVersion(VERSION)
            ->setRemotePath(UPDATE_PATH)
            ->setCacheKey(CACHE_KEY)
            ->setCacheExpiration(HOUR_IN_SECONDS)
            ->setCache(true);
    }

    public function checkForUpdateManually()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], self::CHECK_UPDATE)) {
            wp_die(__('Security check failed.', 'acfseo'));
        }

        $plugin_settings = PluginService::getInstance();

        if (!$plugin_settings->updater()->hasNewUpdate()) {
            set_transient(
                DOMAIN . '_no_updates_notice',
                sprintf(
                    __('Plugin %s is up to date.', 'acfseo'),
                    PLUGIN_NAME
                ),
                HOUR_IN_SECONDS
            );
        } else {
            delete_transient(CACHE_KEY);
        }

        wp_safe_redirect(admin_url('plugins.php'));

        exit;
    }

    public function noUpdatesNotice()
    {
        $message = get_transient(DOMAIN . '_no_updates_notice');
        delete_transient(DOMAIN . '_no_updates_notice');

        if (!$message) {
            return false;
        }

        wp_admin_notice(
            $message,
            [
                'type'                  => 'success',
                'dismissible'           => true,
                'attributes'            => [
                    'data-slug'     => PLUGIN_SLUG,
                    'data-action'   => 'no-updates'
                ]
            ]
        );
    }

    public function updateSuccessNotice()
    {
        $message = get_transient(DOMAIN . '_update_success_notice');
        delete_transient(DOMAIN . '_update_success_notice');

        if (!$message) {
            return false;
        }

        wp_admin_notice(
            $message,
            [
                'type'                  => 'success',
                'dismissible'           => true,
                'attributes'            => [
                    'data-slug'     => PLUGIN_SLUG,
                    'data-action'   => 'updated'
                ]
            ]
        );
    }

    public static function getManualUpdateCheckLink(): string
    {
        return esc_url(
            add_query_arg(
                [
                    'action' => self::CHECK_UPDATE,
                    'nonce' => wp_create_nonce(self::CHECK_UPDATE)
                ],
                admin_url('admin-post.php')
            )
        );
    }

    public static function getSettingsLink(): string
    {
        $path = self::getSettingsPageParentSlug();

        if (strpos($path, '.php') === false) {
            $path = 'admin.php';
        }

        return esc_url(
            add_query_arg(
                'page',
                SETTINGS_PAGE_SLUG,
                admin_url($path)
            )
        );
    }

    public function removeOldSeoMetaBox(): void
    {
        remove_meta_box(
            'seo_metabox_container',
            array_keys(
                array_merge(
                    get_post_types(),
                    get_taxonomies()
                )
            ),
            'normal'
        );
    }

    public function setToolbars(): void
    {
        add_filter('acf/fields/wysiwyg/toolbars', [$this, 'filterToolbars']);
    }

    public function filterToolbars($toolbars): array
    {
        $toolbars['acf_seo_h1_editor'] = [];
        $toolbars['acf_seo_h1_editor'][1] = [
            'styleselect',
            'undo',
            'redo',
            'pastetext',
            'removeformat'
        ];

        return $toolbars;
    }

    public function renderCounter($field): array
    {
        if (
            !$this->shouldRun() ||
            !$field['maxlength'] ||
            ($field['type'] != 'text' && $field['type'] != 'textarea')
        ) {
            return $field;
        }

        $len = function_exists('mb_strlen')
            ? mb_strlen($field['value'])
            : strlen($field['value']);

        $max = $field['maxlength'];

        echo $this->counterContent($len, $max);

        return $field;
    }

    private function counterContent(int $len, int $max): string
    {
        $classes = [
            'char-count-wrapper'
        ];

        $attrs = [
            'class' => implode(', ', $classes)
        ];

        ob_start(); ?>
            <span <?php echo acf_esc_attrs($attrs); ?>>
                <span class="char-count"><?php echo $len; ?></span>
                <span class="char-separator">/</span>
                <span class="char-max"><?php echo $max; ?></span>
            </span>
        <?php return ob_get_clean();
    }

    private function shouldRun(): bool
    {
        global $post;

        if ($post && $post->ID && get_post_type($post->ID) == 'acf-field-group') {
            return false;
        }

        return true;
    }

    public function addOptionPage()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title'    => sprintf(
                    __('%s Settings', 'acfseo'),
                    PLUGIN_NAME
                ),
                'menu_title'    => PLUGIN_NAME,
                'menu_slug'     => SETTINGS_PAGE_SLUG,
                'parent_slug'   => self::getSettingsPageParentSlug(),
                'capability'    => self::getManagePluginCap(),
                'redirect'      => true
            ]);
        }
    }
}
