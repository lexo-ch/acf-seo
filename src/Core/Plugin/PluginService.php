<?php

namespace LEXO\AcfSeo\Core\Plugin;

use LEXO\AcfSeo\Core\Abstracts\Singleton;
use LEXO\AcfSeo\Core\Loader\Loader;
use LEXO\AcfSeo\Core\Updater\PluginUpdater;

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
};

class PluginService extends Singleton
{
    private static string $namespace    = 'custom-plugin-namespace';
    protected static $instance          = null;

    private const CHECK_UPDATE          = 'check-update-' . PLUGIN_SLUG;

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

    public function addUpdateCheckLink()
    {
        add_filter(
            'plugin_action_links_' . BASENAME,
            [$this, 'setUpdateCheckLink']
        );
    }

    public function setUpdateCheckLink($links)
    {
        $url = self::getManualUpdateCheckLink();

        $settings_link = "<a href='$url'>" . __('Update check', 'acfseo') . '</a>';

        array_push(
            $links,
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
            ->setCacheExpiration(12 * HOUR_IN_SECONDS)
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
}
