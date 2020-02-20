<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\JobAssetService;
use View, Redirect;

class DynamicsClient {
    function __construct($email, $password, $dynamicsUrl, $debug = 0) {
        $this->deviceUserName = "11".$this->random_string();
        $this->devicePassword = $this->random_string();
        $this->messageid = $this->create_guid();
        $this->clientId  = $this->create_guid();
        $this->orgPoint    = "/XRMServices/2011/Organization.svc";
        $this->dynamicsUrl = $dynamicsUrl;
        $this->debug       = $debug;
        $dynamicsRegionArray = explode(".",$dynamicsUrl);
        $this->dynamicsRegion = $dynamicsRegionArray[1];
        $this->email    = $email;
        $this->password = $password;
        $this->securityToken0 = '';
        $this->securityToken1 = '';
        $this->keyIdentifier  = '';
        $this->_lastRequest   = '';
        $this->_lastResponse  = '';
        $this->_error         = '';
        $this->currentTime = substr(date('c'),0,-6) . ".00";
        $this->nextDayTime = substr(date('c', strtotime('+1 day')),0,-6) . ".00"; 
        $this->login();
    }

    /**
     * Logs into Windows Live
     * 
     * @return true on success else error
     */
    public function login() {
        $register = $this->registerDevice();
        $response = $this->getBinaryDAToken($this->messageid,$this->deviceUserName,$this->devicePassword);
        $responsedom = new DomDocument();
        $responsedom->loadXML($response);
        $cipherValues = $responsedom->getElementsbyTagName("CipherValue");
        $this->cipherValue = $cipherValues->item(0)->textContent;
        if (!empty($this->cipherValue)){
            $response = $this->getSecurityTokens($this->cipherValue);
            $responsedom = new DomDocument();
            $responsedom->loadXML($response);
            print_R($response);exit;
            $cipherValues = $responsedom->getElementsbyTagName("CipherValue");
            $this->securityToken0 = $cipherValues->item(0)->textContent;
            $this->securityToken1 = $cipherValues->item(1)->textContent;
            $this->keyIdentifier = $responsedom->getElementsbyTagName("KeyIdentifier")->item(0)->textContent; 
            if (empty($this->keyIdentifier) || empty($this->securityToken0) || empty($this->securityToken1)){
                $this->_error = "Failed to get security tokens.";
            } else {
                return true;
            }
        } else {
            $this->_error = "Failed to get binary DA token.";
        }
    }

    /**
     * Makes the SOAP call
     * 
     * @param  string $request    Soap method
     * 
     * @return result
     */
    public function sendQuery($request){
        $xml = '
        <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            '.$this->getHeader().'
            <s:Body>
                '.$request.'
            </s:Body>
        </s:Envelope>'; 
        return $this->doCurl($this->orgPoint, $this->dynamicsUrl, "https://".$this->dynamicsUrl.$this->orgPoint, $xml);
    }

    /**
     * Generate Soap Header
     * Generates valid crm auth header
     * 
     * @return soap header
     */
    private function getHeader() {
        if (empty($this->keyIdentifier) || empty($this->securityToken0) || empty($this->securityToken1)){
            $this->login();
        }
        $header = '
        <s:Header>
            <a:Action s:mustUnderstand="1">http://schemas.microsoft.com/xrm/2011/Contracts/Services/IOrganizationService/RetrieveMultiple</a:Action>
            <a:MessageID>
                urn:uuid:'.$this->messageid.'
            </a:MessageID>
            <a:ReplyTo><a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address></a:ReplyTo>
            <VsDebuggerCausalityData xmlns="http://schemas.microsoft.com/vstudio/diagnostics/servicemodelsink">uIDPozJEz+P/wJdOhoN2XNauvYcAAAAAK0Y6fOjvMEqbgs9ivCmFPaZlxcAnCJ1GiX+Rpi09nSYACQAA</VsDebuggerCausalityData>
            <a:To s:mustUnderstand="1">
                https://'.$this->dynamicsUrl.'/XRMServices/2011/Organization.svc
            </a:To>
            <o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <u:Timestamp u:Id="_0">
                <u:Created>'.$this->currentTime.'Z</u:Created>
                <u:Expires>'.$this->nextDayTime.'Z</u:Expires>
            </u:Timestamp>
            <EncryptedData Id="Assertion0" Type="http://www.w3.org/2001/04/xmlenc#Element" xmlns="http://www.w3.org/2001/04/xmlenc#">
                <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#tripledes-cbc"></EncryptionMethod>
                <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                    <EncryptedKey>
                        <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p"></EncryptionMethod>
                        <ds:KeyInfo Id="keyinfo">
                            <wsse:SecurityTokenReference xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                <wsse:KeyIdentifier EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary" ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509SubjectKeyIdentifier">
                                    '.$this->keyIdentifier.'
                                </wsse:KeyIdentifier>
                            </wsse:SecurityTokenReference>
                        </ds:KeyInfo>
                        <CipherData>
                            <CipherValue>
                                '.$this->securityToken0.'
                            </CipherValue>
                        </CipherData>
                    </EncryptedKey>
                </ds:KeyInfo>
                <CipherData>
                    <CipherValue>
                        '.$this->securityToken1.'
                    </CipherValue>
                </CipherData>
            </EncryptedData>
            </o:Security>
        </s:Header>';
        return $header;
    }

    /**
     * Gets Security Tokens
     * 
     * @return result
     */
    private function getSecurityTokens($cipherValue) {
        $securityTokenSoapTemplate = '
        <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <s:Header>
                <a:Action s:mustUnderstand="1">http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue</a:Action>
                <a:MessageID>
                    urn:uuid:'.$this->messageid.'
                </a:MessageID>
                <a:ReplyTo><a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address></a:ReplyTo>
                <VsDebuggerCausalityData xmlns="http://schemas.microsoft.com/vstudio/diagnostics/servicemodelsink">uIDPozBEz+P/wJdOhoN2XNauvYcAAAAAK0Y6fOjvMEqbgs9ivCmFPaZlxcAnCJ1GiX+Rpi09nSYACQAA</VsDebuggerCausalityData>
                <a:To s:mustUnderstand="1">https://login.live.com/liveidSTS.srf</a:To>
                <o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <u:Timestamp u:Id="_0">
                        <u:Created>'.$this->currentTime.'Z</u:Created>
                        <u:Expires>'.$this->nextDayTime.'Z</u:Expires>
                    </u:Timestamp>
                    <o:UsernameToken u:Id="user">
                        <o:Username>'.$this->email.'</o:Username>
                        <o:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">
                            '.$this->password.'
                        </o:Password>
                    </o:UsernameToken>
                    <wsse:BinarySecurityToken ValueType="urn:liveid:device" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <EncryptedData Id="BinaryDAToken0" Type="http://www.w3.org/2001/04/xmlenc#Element" xmlns="http://www.w3.org/2001/04/xmlenc#">
                            <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#tripledes-cbc"></EncryptionMethod>
                            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#"><ds:KeyName>http://Passport.NET/STS</ds:KeyName></ds:KeyInfo>
                            <CipherData>
                                <CipherValue>
                                    '.$this->cipherValue.'
                                </CipherValue>
                            </CipherData>
                        </EncryptedData>
                    </wsse:BinarySecurityToken>
                </o:Security>
            </s:Header>
                <s:Body>
                    <t:RequestSecurityToken xmlns:t="http://schemas.xmlsoap.org/ws/2005/02/trust">
                        <wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
                            <a:EndpointReference><a:Address>urn:'.$this->dynamicsRegion.':dynamics.com</a:Address></a:EndpointReference>
                        </wsp:AppliesTo>
                        <wsp:PolicyReference URI="MBI_FED_SSL" xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy" />
                        <t:RequestType>http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</t:RequestType>
                    </t:RequestSecurityToken>
                </s:Body>
            </s:Envelope>
        ';
        return $this->doCurl("/liveidSTS.srf", "login.live.com", "https://login.live.com/liveidSTS.srf", $securityTokenSoapTemplate);
    }

    /**
     * Gets DA token
     * 
     * @return result
     */
    private function getBinaryDAToken() {
        $deviceCredentialsSoapTemplate = '
        <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <s:Header>
                <a:Action s:mustUnderstand="1">http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue</a:Action>
                <a:MessageID>
                    urn:uuid:'.$this->messageid.'
                </a:MessageID>
                <a:ReplyTo><a:Address>http://www.w3.org/2005/08/addressing/anonymous</a:Address></a:ReplyTo>
                <VsDebuggerCausalityData xmlns="http://schemas.microsoft.com/vstudio/diagnostics/servicemodelsink">uIDPoy9Ez+P/wJdOhoN2XNauvYcAAAAAK0Y6fOjvMEqbgs9ivCmFPaZlxcAnCJ1GiX+Rpi09nSYACQAA</VsDebuggerCausalityData>
                <a:To s:mustUnderstand="1">https://login.live.com/liveidSTS.srf</a:To>
                <o:Security s:mustUnderstand="1" xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <u:Timestamp u:Id="_0">
                        <u:Created>'.$this->currentTime.'Z</u:Created>
                        <u:Expires>'.$this->nextDayTime.'Z</u:Expires>
                    </u:Timestamp>
                    <o:UsernameToken u:Id="devicesoftware">
                        <o:Username>'.$this->deviceUserName.'</o:Username>
                        <o:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">
                            '.$this->devicePassword.'
                        </o:Password>
                    </o:UsernameToken>
                </o:Security>
            </s:Header>
            <s:Body>
                <t:RequestSecurityToken xmlns:t="http://schemas.xmlsoap.org/ws/2005/02/trust">
                    <wsp:AppliesTo xmlns:wsp="http://schemas.xmlsoap.org/ws/2004/09/policy">
                        <a:EndpointReference>
                            <a:Address>http://passport.net/tb</a:Address>
                        </a:EndpointReference>
                    </wsp:AppliesTo>
                    <t:RequestType>http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</t:RequestType>
                </t:RequestSecurityToken>
            </s:Body>
        </s:Envelope>';
        return $this->doCurl("/liveidSTS.srf" , "login.live.com" , "https://login.live.com/liveidSTS.srf", $deviceCredentialsSoapTemplate);
    }

    /**
     * Registers the device
     * 
     * @return result
     */
    private function registerDevice() {
        $registration = '
        <DeviceAddRequest>
            <ClientInfo name="'.$this->clientId.'" version="1.0"/>
            <Authentication>
                <Membername>'.$this->deviceUserName.'</Membername>
                <Password>'.$this->devicePassword.'</Password>
            </Authentication>
        </DeviceAddRequest>';
        return $this->doCurl('/DeviceAddCredential.srf','login.live.com','https://login.live.com/ppsecure/DeviceAddCredential.srf',$registration);
    }

    /**
     * Generates random string
     * 
     * @return random string
     */
    private function random_string() {
        $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        $length = 24;
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }

    /**
     * Create microsoft-compatible GUID
     * @param  string $namespace optional namespace
     * @return MS GUID
     * 
     * Modified from http://www.php.net/manual/en/function.uniqid.php#107512
     * 
     */
    private function create_guid($namespace = '') {     
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash,  0,  8) . 
                '-' .
                substr($hash,  8,  4) .
                '-' .
                substr($hash, 12,  4) .
                '-' .
                substr($hash, 16,  4) .
                '-' .
                substr($hash, 20, 12);
        return $guid;
    }

    /**
     * Prints readable XML
     * @param  string $title  Title of the output
     * @param  string $output Content
     * @return formatted XML
     */
    function printXml($title,$output) {
        echo '<h1>'.$title.'</h1><pre>' . wordwrap(htmlspecialchars($output, ENT_QUOTES), 40, "<br />\n") . '</pre><br /><br />';
    }

    /**
     * Sends and recives SOAP messages via cURL
     * @param  string $postUrl  File to post to
     * @param  string $hostname Hostname
     * @param  string $soapUrl  URL to post to
     * @param  string $content  SOAP content
     * @return result
     */
    private function doCurl($postUrl, $hostname, $soapUrl, $content){
        
        $headers = array(
                "POST ". $postUrl ." HTTP/1.1",
                "Host: " . $hostname,
                'Connection: Keep-Alive',
                "Content-type: application/soap+xml; charset=UTF-8",
                "Content-length: ".strlen($content),
        );
        $this->_lastRequest = $content;
        if ($this->debug == 1){$this->printXml('REQUEST: ',$content);}
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $soapUrl);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cURL, CURLOPT_TIMEOUT, 60);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $content);
        $response = curl_exec($cURL);
        curl_close($cURL);
        $this->_lastResponse = $response;
        if ($this->debug == 1){$this->printXml('RESPONSE: ',$response);}
        return $response;
    }    
}
