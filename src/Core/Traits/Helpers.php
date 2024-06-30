<?php

namespace LEXO\AcfSeo\Core\Traits;

use LEXO\AcfSeo\Core\Plugin\PluginService;

trait Helpers
{
    public static function getClassName($classname)
    {
        if ($name = strrpos($classname, '\\')) {
            return substr($classname, $name + 1);
        };

        return $name;
    }

    public static function setStatus404()
    {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }

    public static function printr(mixed $data): string
    {
        return "<pre>" . \print_r($data, true) . "</pre>";
    }

    public static function getFilesFromdirectory($directory)
    {
        $assets = array_values(array_diff(scandir($directory), array('..', '.')));

        return array_filter($assets, function ($item) use ($directory) {
            return !is_dir(trailingslashit($directory) . $item);
        });
    }

    public static function getPostTypesFromLocationArray()
    {
        return array_map(function ($item) {
            return $item[0]['value'];
        }, PluginService::getLocationArray());
    }
}
