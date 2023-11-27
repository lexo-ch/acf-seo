<?php

namespace LEXO\AcfSeo;

use const LEXO\AcfSeo\{
    CACHE_KEY
};

class Deactivation
{
    public static function run()
    {
        delete_transient(CACHE_KEY);
    }
}
