<?php

namespace Application\Models\Factories;

class ReportFactory {
    /**
     * Widget types
     */
    private static $reports = array(
        'admin_logins' => 'Application\Models\Reports\AdminLoginReport',
        'admin_security_audit' => 'Application\Models\Reports\AdminSecurityReport',
    );

    /**
     * Function to create report object
     * @param string $type
     * @param mixed $params optional
     * @return View
     */
    public static function create($type, $params = array()) {
        $class = array_get(static::$reports, $type, null);
        if (is_null($class)) {
            return null;
        }
        if (count($params) > 0) {
            $report = new $class($params);
        } else {
            $report = new $class;
        }
        return $report;
    }
}
