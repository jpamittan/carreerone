<?php

namespace App\Models\Services;

use App\Models\Containers\DataObject; 
use App\Models\Entities\Audit;
use App\Models\Repositories\AuditRepository;
use DateTime;
use Format;
use Sanitize;
use Util;

/**
 * Service class that handles audit record tracking
 */
class AuditService {
 
     /**
     * Service methd to log Error
     * @param string $context
     * @param integer $code
     * @param string $message
     * @param string $details
     */
    public static function log($context, $code, $trace = array(), $request_info = array()) {
        $audit = new Audit();
        $audit->context = $context;
        $audit->code = $code;
        $audit->trace = !empty($trace) ? json_encode($trace) : null;
        $audit->request_info = !empty($request_info) ? json_encode($request_info) : null;
        $audit->save();
    }
}
