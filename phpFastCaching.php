<?php
require_once 'vendor/autoload.php'; // Adjust the path to autoload.php as needed

error_reporting(E_ALL ^ E_DEPRECATED);

/* Cache adapter for phpFastCache */
$cache_config = new \Phpfastcache\Config\ConfigurationOption([
    'path' => realpath(__DIR__) . '/cache', // The folder where the caching will be created
    'preventCacheSlams' => true,
    'cacheSlamsTimeout' => 20,
]);
\Phpfastcache\CacheManager::setDefaultConfig($cache_config);
$cache = \Phpfastcache\CacheManager::getInstance('files');
