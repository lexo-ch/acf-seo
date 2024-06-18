<?php

/**
 * Plugin Name:       LEXO ACF SEO
 * Plugin URI:        https://github.com/lexo-ch/acf-seo/
 * Description:       SEO addon based on ACF.
 * Version:           1.2.1
 * Requires at least: 6.4
 * Requires PHP:      7.4.1
 * Author:            LEXO GmbH
 * Author URI:        https://www.lexo.ch
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acfseo
 * Domain Path:       /languages
 * Update URI:        acf-seo
 * Requires Plugins:  advanced-custom-fields-pro
 */

namespace LEXO\AcfSeo;

use Exception;
use LEXO\AcfSeo\Activation;
use LEXO\AcfSeo\Deactivation;
use LEXO\AcfSeo\Uninstalling;
use LEXO\AcfSeo\Core\Bootloader;

// Prevent direct access
!defined('WPINC')
    && die;

// Define Main plugin file
!defined('LEXO\AcfSeo\FILE')
    && define('LEXO\AcfSeo\FILE', __FILE__);

// Define plugin name
!defined('LEXO\AcfSeo\PLUGIN_NAME')
    && define('LEXO\AcfSeo\PLUGIN_NAME', get_file_data(FILE, [
        'Plugin Name' => 'Plugin Name'
    ])['Plugin Name']);

// Define plugin slug
!defined('LEXO\AcfSeo\PLUGIN_SLUG')
    && define('LEXO\AcfSeo\PLUGIN_SLUG', get_file_data(FILE, [
        'Update URI' => 'Update URI'
    ])['Update URI']);

// Define Basename
!defined('LEXO\AcfSeo\BASENAME')
    && define('LEXO\AcfSeo\BASENAME', plugin_basename(FILE));

// Define internal path
!defined('LEXO\AcfSeo\PATH')
    && define('LEXO\AcfSeo\PATH', plugin_dir_path(FILE));

// Define assets path
!defined('LEXO\AcfSeo\ASSETS')
    && define('LEXO\AcfSeo\ASSETS', trailingslashit(PATH) . 'assets');

// Define internal url
!defined('LEXO\AcfSeo\URL')
    && define('LEXO\AcfSeo\URL', plugin_dir_url(FILE));

// Define internal version
!defined('LEXO\AcfSeo\VERSION')
    && define('LEXO\AcfSeo\VERSION', get_file_data(FILE, [
        'Version' => 'Version'
    ])['Version']);

// Define min PHP version
!defined('LEXO\AcfSeo\MIN_PHP_VERSION')
    && define('LEXO\AcfSeo\MIN_PHP_VERSION', get_file_data(FILE, [
        'Requires PHP' => 'Requires PHP'
    ])['Requires PHP']);

// Define min WP version
!defined('LEXO\AcfSeo\MIN_WP_VERSION')
    && define('LEXO\AcfSeo\MIN_WP_VERSION', get_file_data(FILE, [
        'Requires at least' => 'Requires at least'
    ])['Requires at least']);

// Define Text domain
!defined('LEXO\AcfSeo\DOMAIN')
    && define('LEXO\AcfSeo\DOMAIN', get_file_data(FILE, [
        'Text Domain' => 'Text Domain'
    ])['Text Domain']);

// Define locales folder (with all translations)
!defined('LEXO\AcfSeo\LOCALES')
    && define('LEXO\AcfSeo\LOCALES', 'languages');

!defined('LEXO\AcfSeo\CACHE_KEY')
    && define('LEXO\AcfSeo\CACHE_KEY', DOMAIN . '_cache_key_update');

!defined('LEXO\AcfSeo\UPDATE_PATH')
    && define('LEXO\AcfSeo\UPDATE_PATH', 'https://wprepo.lexo.ch/public/acf-seo/info.json');

if (!file_exists($composer = PATH . '/vendor/autoload.php')) {
    wp_die('Error locating autoloader in LEXO ACF SEO.
        Please run a following command:<pre>composer install</pre>', 'acfseo');
}

!defined('LEXO\AcfSeo\SETTINGS_PAGE_SLUG')
    && define('LEXO\AcfSeo\SETTINGS_PAGE_SLUG', DOMAIN . '-design-options');

require $composer;

register_activation_hook(FILE, function () {
    (new Activation())->run();
});

register_deactivation_hook(FILE, function () {
    (new Deactivation())->run();
});

if (!function_exists('acfseo_uninstall')) {
    function acfseo_uninstall()
    {
        (new Uninstalling())->run();
    }
}
register_uninstall_hook(FILE, __NAMESPACE__ . '\acfseo_uninstall');

add_action('plugins_loaded', function () {
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');

    if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
        wp_admin_notice(
            __('ACF PRO is required in order to use LEXO ACF SEO plugin. Please activate ACF PRO plugin.', 'acfseo'),
            [
                'type'                  => 'error',
                'dismissible'           => false,
                'attributes'            => [
                    'data-slug'     => PLUGIN_SLUG,
                    'data-action'   => 'acf-pro-missing'
                ]
            ]
        );
    }
});

try {
    Bootloader::getInstance()->run();
} catch (Exception $e) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');

    deactivate_plugins(FILE);

    wp_die($e->getMessage());
}
