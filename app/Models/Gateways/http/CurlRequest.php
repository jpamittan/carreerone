<?php
namespace Application\Models\Gateways\Http;

use Application\Models\Gateways\HttpGateway;

/**
 * Gateway class using curl for API call
 */
class CurlRequest implements HttpGateway
{
    /**
     * Call Status Array structure thats been returned in any case
     * @var mixed
     */
    protected $call_status = array(
        'success' => false,
        'data' => null,
        'code' => 0,
        'time_taken' => 0,
        'error_message' => null,
    );

    /**
     * Implemented function that will get content
     * @param string $url
     * @param integer $timeout
     * @return mixed
     */
    public function get($url, $timeout = 250)
    {
        try {
            $start = microtime(true);
            $ch = curl_init();
            if (!$ch) {
                return array(
                    "success" => false,
                    "data" => "cURL Error: Couldn't initialize handle",
                    "code" => "",
                );
            }
            // set some cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            // execute, get info and close handle
            $results = curl_exec($ch);
            $info = curl_getinfo($ch);
            $error = curl_error($ch);
            curl_close($ch);

            // get code and calculate time taken
            $this->call_status['code'] = (isset($info['http_code'])) ? $info['http_code'] : 0;
            $this->call_status['time_taken'] = microtime(true) - $start;

            // set the call status
            if (empty($results)) {
                $this->call_status['success'] = false;
                $this->call_status['data'] = $error;
            } else {
                $this->call_status['success'] = true;
                $this->call_status['data'] = $results;
            }
        } catch (Exception $exp) {
            $this->call_status['success'] = false;
            $this->call_status['code'] = $exp->getCode();
            $this->call_status['error_message'] = $exp->getMessage();
        }
        return $this->call_status;
    }
}
