<?php

namespace LEXO\AcfSeo\Core;

use LEXO\AcfSeo\Core\Abstracts\Singleton;
use LEXO\AcfSeo\Core\Plugin\Fields;
use LEXO\AcfSeo\Core\Plugin\Injector;
use LEXO\AcfSeo\Core\Plugin\PluginService;

use const LEXO\AcfSeo\{
    DOMAIN,
    PATH,
    LOCALES
};

class Bootloader extends Singleton
{
    protected static $instance = null;

    public function run()
    {
        add_action('acf/init', [$this, 'onAcfInit'], 10);
        add_action(DOMAIN . '/localize/admin-' . DOMAIN . '.js', [$this, 'onAdminProjectsJsLoad']);
        add_action('after_setup_theme', [$this, 'onAfterSetupTheme']);
        add_action('admin_head', [$this, 'onAdminHead'], 10);
    }

    public function onAdminHead()
    {
        $plugin_settings = PluginService::getInstance();
        $plugin_settings->removeOldSeoMetaBox();
    }

    public function onAcfInit()
    {
        do_action(DOMAIN . '/init');

        $plugin_settings = PluginService::getInstance();
        $plugin_settings->setNamespace(DOMAIN);
        $plugin_settings->registerNamespace();
        $plugin_settings->addPluginLinks();
        $plugin_settings->addOptionPage();
        $plugin_settings->noUpdatesNotice();
        $plugin_settings->updateSuccessNotice();
        $plugin_settings->setToolbars();
        $plugin_settings->importFields();
        $plugin_settings->excludePostsFromSitemap();

        Injector::getInstance()->run();
    }

    public function onAdminProjectsJsLoad()
    {
        PluginService::getInstance()->addAdminLocalizedScripts();
    }

    public function onAfterSetupTheme()
    {
        $this->loadPluginTextdomain();
        $plugin_settings = PluginService::getInstance();
        $plugin_settings->updater()->run();

        Injector::getInstance()->addTitleTag();
    }

    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(DOMAIN, false, trailingslashit(trailingslashit(basename(PATH)) . LOCALES));
    }
}
