<?php
namespace Application\Models\Gateways\Http;

use Constants;
use Exception;
use SoapClient;

/**
 * Gateway class using soap for API call
 */
class SoapRequest
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
    public function get($url, $xml, $function = "", $timeout = 250)
    {
        // set error hanlder to custom one
        set_error_handler(array($this, 'soapErrorHandler'));

        try {
            $context = stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'allow_self_signed' => true,
                ),
            ));

            // create client
            $client = new SoapClient($url, array(
                'stream_context' => $context,
                'trace' => 1,
                'exceptions' => true,
            ));

            $time_start = time();
            $soap_response = $client->__doRequest($xml, $url, $function, 1.1);
            $time_end = time();

            $this->call_status['time_taken'] = $time_end - $time_start;
            if (isset($client->__soap_fault)) {
                $this->call_status['code'] = $client->__soap_fault->faultcode;
                $this->call_status['error_message'] = $client->__soap_fault->faultstring;
                $this->call_status['success'] = false;
                $this->call_status['data'] = null;
            } else {
                $this->call_status['code'] = 0;
                $this->call_status['error_message'] = "";
                $this->call_status['success'] = true;
                $this->call_status['data'] = $soap_response;
            }
        } catch (Exception $exp) {
            $this->call_status['code'] = Constants::RESP_CODE_DEFAULT_ERROR;
            $this->call_status['error_message'] = $exp->getMessage();
            $this->call_status['success'] = false;
            $this->call_status['data'] = null;
        }
        return $this->call_status;
    }

    /**
     * Error handler for fatal errors since Soap cant read wsdl
     */
    public function soapErrorHandler($code, $message, $file, $line)
    {
        throw new Exception($message.' - '.$file.' - '.$line, $code);
    }
}
