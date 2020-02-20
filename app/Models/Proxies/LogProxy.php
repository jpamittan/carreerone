<?php

namespace App\Models\Proxies;
 
use App\Models\Services\AuditService;
use App\Models\Proxies\SystemAlert;
use Constants, Exception, Log; 

/**
 * Log Proxy class to bypass all logging requests
 */
class LogProxy {
    /**
     * Add info to the log
     * @param  string $context
     * @param  array $details
     * @return boolean
     */
    public static function info($context, $details = array()) {
        self::log('info', $context, Constants::RESP_CODE_DEFAULT_INFO, $details);
    }

    /**
     * Add debug statements
     * @param  string $context
     * @param  array $details
     * @return boolean
     */
    public static function debug($context, $details = array()) {
        self::log('debug', $context, Constants::RESP_CODE_DEFAULT_DEBUG, $details);
    }

    /**
     * Method to add bench mark
     * @param  string $context
     * @param  array $details
     * @return boolean
     */
    public static function bench($context, $details = array()) {
        self::log('bench', $context, Constants::RESP_CODE_DEFAULT_INFO, $details);
    }

    /**
     * Add error to the log
     * @param  string $context
     * @param  array $details
     * @return boolean
     */
    public static function error($context, $details = array()) {
        self::log('error', $context, Constants::RESP_CODE_DEFAULT_ERROR, $details);
        // create a system alert and trigger
        $alert = new SystemAlert;
        $alert->populate(array(
            'ci' => "Application",
            'metric_name' => $context,
            'metric_type' => "AppEvent",
            'severity' => "Major",
            'message' => \Util::implode_entry(',', '=>', $details),
        ));
        $alert->trigger();
    }

    /**
     * Add exception to the log
     * @param  string $context
     * @param  Exception $exp
     * @param  array $details
     * @return boolean
     */
    public static function exception($context, Exception $exp, $details = array()) {
        $trace = array(
            'code' => $exp->getCode(),
            'message' => $exp->getMessage(),
            'line' => $exp->getLine(),
            'file' => $exp->getFile(),
        );
        // log exception
        self::log('exception', $context, $exp->getCode(), $trace, $details);
        // create a system alert and trigger
        $alert = new SystemAlert;
        $alert->populate(array(
            'ci' => "Application",
            'metric_name' => $context,
            'metric_type' => "AppEvent",
            'severity' => "Critical",
            'message' => \Util::implode_entry(',', '=>', $trace),
        ));
        $alert->trigger();
    }

    /**
     * Private function to log based on environment
     * @param string $context
     * @param integer $code
     * @param array $details
     * @param array $info
     * @return boolean
     */
    public static function log($type, $context, $code, $details, $info = array()) {
        // check what config demands
        if (\ConfigProxy::get('log_to_db') == 1) {
            if ((\ConfigProxy::get('log_to_db_trace_mode') == 0) && $type== 'debug' ) { return;}
            AuditService::log($context, $code, $details, $info);
           //additional to bd we log in the log file 
           if ( $type == 'debug'){
               $message = 'DEBUG:' . $context . ' - ' . self::stringifyDetails($details);
               Log::debug($message);
           }

        } else {
            switch ($type) {
                case 'info':
                    $message = 'INFO:' . $context . ' - ' . self::stringifyDetails($details);
                    Log::info($message);
                    break;
                case 'debug':
                    $message = 'DEBUG:' . $context . ' - ' . self::stringifyDetails($details);
                    Log::debug($message);
                    break;
                case 'bench':
                    $message = 'BENCHMARK:' . $context . ' - ' . self::stringifyDetails($details);
                    Log::debug($message);
                    break;
                case 'error':
                    $message = 'ERROR:' . $context . ' - ' . self::stringifyDetails($details);
                    Log::error($message);
                    break;
                case 'exception':
                    $details['code'] = $code;
                    $message = 'EXCEPTION:' . $context . ' - ' . self::stringifyDetails($details);
                    Log::error($message);
                    break;
                default:
                    $message = 'DEFAULT:' . $context . ' - ' . self::stringifyDetails($details);
                    Log::error($message);
                    break;
            }
        }
    }

    /**
     * Stringify details passed to the log
     * @param array
     * @return string
     */
    private static function stringifyDetails(array $details) {
        $output = array();
        array_walk($details, function ($value, $key) use (&$output) {
            $output[] = $key . ' : ' . is_array($value) ? 'array' : $value;
        });
        return implode(', ', $output);
    }
}
