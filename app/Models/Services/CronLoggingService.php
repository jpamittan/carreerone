<?php

namespace App\Models\Services;

use Carbon\Carbon;
use Config;

class CronLoggingService {
    protected function curl($action) {
        $ch = curl_init();
        if (! $ch) {
            throw new \Exception('cURL Error: Couldn\'t initialize handle');
        }
        curl_setopt($ch, CURLOPT_URL, 'https://lux-staging.careeronecdn.com.au/api/cron/' . $action);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        return $ch;
    }

    public function start($cronName) {
        $ch = $this->curl('add-log');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'start_time' => Carbon::now(), 
            'cron_name' => $cronName
        ]);
        $id = curl_exec($ch);
        curl_close($ch);
        return $id;
    }

    public function complete($logId, $total = 0, $processed = 0) {
        $ch = $this->curl('update-log/' . $logId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'end_time' => Carbon::now(), 
            'status' => 'complete',
            'total_records' => $total,
            'processed_records' => $processed
        ]));
        curl_exec($ch);
        curl_close($ch);
        return null;
    }

    public function error($logId, \Exception $e) {
        $filepath = str_replace("/", "\\\\", $e->getFile());
        $message = "Line: " . $e->getLine() . ", Code: " . $e->getCode() . ", File:" .  $filepath . ", Message: " . $e->getMessage();
        $ch = $this->curl('update-log/' . $logId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'end_time' => Carbon::now(), 
            'status' => 'error',
            'message' => $message
        ]));
        curl_exec($ch);
        curl_close($ch);
        return null;
    }
}
