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

        wp_localize_script(trailingslashit(self::$namespace) . DOMAIN . '-.js', DOMAIN . 'AdminLocalized', $vars);
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

    public function removeOldSeoMetaBox()
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
}
