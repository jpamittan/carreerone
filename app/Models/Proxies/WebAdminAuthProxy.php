<?php
namespace Application\Models\Proxies;

use App;
use Application\Models\Entities\WebAdmin;
use Application\Models\Repositories\WebAdminPermissionRepository;
use Application\Models\Services\WebAdminAuthService;

/**
 * Authentication Proxy class to WebAdmin Module
 */
class WebAdminAuthProxy {
    /**
     * Placeholder for web dmin
     * @var WebAdmin
     */
    public static $user = null;

    /**
     * Function to load user
     */
    public static function loadUser() {
        App::singleton('admin', function () {
            $auth = new WebAdminAuthService();
            static::$user = $auth->getUser();
            return static::$user;
        });
    }

    /**
     * Function to get currently logged in webadmin
     * @return WebAdmin|null
     */
    public static function user() {
        // if already set
        if (isset(static::$user)) {
            return static::$user;
        }
        // if not shared, in case of test calls
        if (!App::isShared('admin')) {
            self::loadUser();
        }
        // get from shared
        static::$user = app('admin');
        return static::$user;
    }

    /**
     * Function to check user is a guest
     * @return WebAdmin|null
     */
    public static function guest() {
        if (static::user()) {
            return false;
        }
        return true;
    }

    /**
     * Function to check access to the rule
     * @param string $rule
     * @param integer user_id
     */
    public static function hasAccess($rule) {
        // if permission checks are disabled then return
        $config = \ConfigProxy::get('admin.permission_checks');
        if (is_null($config) || $config == 0) {
            return true;
        }
        // get user
        $user = static::user();
        if (is_null($user)) {
            return false;
        }
        $repo = new WebAdminPermissionRepository;
        $has_access = $repo->checkPermissionForUser($user->id, $rule);

        return (bool) $has_access;
    }

    /**
     * Function to check access to a particular route
     * @param string $route
     */
    public static function hasRouteAccess($route) {
        // if permission checks are disabled then return
        $config = \ConfigProxy::get('admin.permission_checks');
        if (!is_null($config) && $config != 0) {
            $user = static::user();
            if (is_null($user)) {
                return false;
            }
            // get route attributes
            $route_name = $route->getName();
            $params = array();
            // user admin and management
            if ($route_name == 'admin-users' ||
                $route_name == 'admin-users-create' ||
                $route_name == 'admin-user-create' ||
                $route_name == 'admin-user-edit' ||
                $route_name == 'admin-runtime-settings' ||
                $route_name == 'admin-runtime-settings-edit'
            ) {
                $params['scope'] = 'admin-user-access';
                // user role management
            } else if ($route_name == 'admin-user-assign' ||
                $route_name == 'admin-roles') {
                $params['scope'] = 'admin-role-access';
                // employee management
            } else if ($route_name == 'admin-employees') {
                $params['scope'] = 'admin-employees-access';
                // audit management
            } else if ($route_name == 'admin-audit') {
                $params['scope'] = 'admin-audit-access';
                // email management
            } else if ($route_name == 'admin-email') {
                $params['scope'] = 'admin-email-access';
                // organisation view management
            } else if ($route_name == 'admin-organisations' ||
                $route_name == 'admin-organisation-search' ||
                $route_name == 'admin-organisation-children') {
                $params['scope'] = 'admin-organisation-view';
                // organisation and its permission edit management
            } else if ($route_name == 'admin-organisation-edit' ||
                $route_name == 'admin-organisation-permission-edit') {
                $params['scope'] = 'admin-organisation-edit-access';
            } else if ($route_name == 'admin-transition-settings') {
                $params['scope'] = 'admin-transition-settings';
            }
            if (!empty($params)) {
                $repo = new WebAdminPermissionRepository;
                $has_access = $repo->checkRouteAccessForUser($user->id, $params);
                if (!$has_access) {
                    return \Redirect::route('admin-login');
                }
            }
        }
    }
}
