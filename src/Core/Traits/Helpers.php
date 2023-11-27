<?php

namespace LEXO\AcfSeo\Core\Traits;

use WP_Post;

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
}
