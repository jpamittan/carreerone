<?php

namespace App\Models\Proxies;

use App\Models\Entities\RuntimeConfig;
use Cache;
use Config;

/**
 * Proxy class for runtime config store and retrieval
 */
class ConfigProxy {
    /**
     * Cache duration in minutes
     * @var integer
     */
    const CACHE_DURATION = 5;

    /**
     * Function to get a value from config based on key
     * @param string $key
     * @return mixed $value
     */
    public static function get($key) {
        $config = null;
        if (empty($key) || is_null($key)) {
            return $config;
        }

        // check in runtime_config, if not found then check in config
        $runtime_configs = self::runTimeConfigList();
        if (\in_array($key, $runtime_configs)) {
            $db_config = RuntimeConfig::where('name', '=', $key)->get();
            if ($db_config && $db_config->count() > 0) {
                if ($db_config->count() == 1) {
                    $config = $db_config[0]->value;
                } else {
                    $config = $db_config->lists('value');
                }
            }
        } else {
            $config = Config::get($key);
        }
        return $config;
    }

    /**
     * Function to check whether a key exists in config
     * @param string $key
     * @return boolean
     */
    public static function has($key) {
        $count = 0;
        if (empty($key) || is_null($key)) {
            return $count;
        }

        // check in runtime_config, if not found then check in config
        $runtime_configs = self::runTimeConfigList();
        if (in_array($key, $runtime_configs)) {
            $count = 1;
        } else {
            $count = Config::has($key);
        }
        return (bool) $count;
    }

    /**
     * Private function that has list of run time config values
     * @return array
     */
    private static function runTimeConfigList() {
      $configs = RuntimeConfig::lists('name')->toArray();
        return $configs;
    }
}
