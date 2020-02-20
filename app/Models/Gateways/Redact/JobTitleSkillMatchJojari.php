<?php

namespace App\Models\Gateways\Redact;

use App\Models\Containers\ResumeExtract;
use App\Models\Gateways\RedactGateway;
use App\Models\Proxies\FileProxy;
use Exception;
use Format;
use Guzzle\Service\Client;
use Config;

/**
 * Implementation of Gateway class
 */
class JobTitleSkillMatchJojari  {

    /**
     * Config options
     */
    private $config = array(
        'endpoint' => null,
        'username' => null,
        'password' => null,
        'auth' => null,
    );


    /**
     * Error placeholder
     * @var mixed
     */
    private $error = array(
        'code' => 0,
        'message' => '',
        'details' => null,
    );

    /**
     * Placeholder for post params
     * @var mixed
     */
    private $type  ;

    /**
     * Placeholder array for extracted profile info
     * @var mixed
     */
    protected $extract = null;

    /**
     * Constructor
     */
    public function __construct($type) {
        $this->type = $type;
        $url = '';
        switch($type){
            case 'pairs' ; $url = 'url_pairs'; break;
             case 'similar' ; $url = 'url_similar'; break;

        } 

        $this->config['endpoint'] = Config::get('jojari.jobtitleskillmatch.'.$url);
    
        $this->config['username'] = Config::get('jojari.jobtitleskillmatch.username');
        $this->config['password'] =Config::get('jojari.jobtitleskillmatch.password');
        $this->config['auth'] = Config::get('jojari.jobtitleskillmatch.auth');

      
    }

    /**
     * Implemented function to clean uploaded resume
     * @param FileProxy $resume
     */
    public function clean( $data) {
        try {
            $jsondata ='';
            if($this->type == 'similar'){
                unset($data['type']);
                $jsondata = json_encode($data);
              }elseif($this->type == 'pairs'){
                unset($data['type']);
                
                $data['queries'] = $data['query'];
                unset($data['query']);

                $jsondata = json_encode($data);
              }
 
               
           $ch = curl_init();
                    $options = array(CURLOPT_URL => $this->config['endpoint'],
                                 CURLOPT_RETURNTRANSFER => true,
                                 CURLINFO_HEADER_OUT => true, //Request header
                               
                                 //CURLOPT_SSL_VERIFYPEER => false, //Don't veryify server certificate
                                 CURLOPT_POST => true,
                                  CURLOPT_POSTFIELDS => $jsondata
                                );
            curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json')); 
         
          curl_setopt_array($ch, $options);
          $response = curl_exec($ch);
          //$err = curl_error($ch);
          curl_close($ch);

            
 
            // parse response
            $status = $this->parse($response);
 

            return $this->extract;
        } catch (Exception $exp) {
            print_r($exp->getMessage());exit;
             
        }
        return null;
    }

    

    /**
     * Private function to parse api response
     * @param $response
     * @return boolean
     */
    private function parse($response) {
        // extract content
       $arr = array();
        if($this->type == 'similar'){
          $content = json_decode($response);
          $counter =0 ;
          if(isset($content->jobTitles)){
            foreach($content->jobTitles as $jt){
            $word = $jt->word;
            $distance = $jt->distance;
            $arr['job_title'][$counter]['word'] = $word;
            $arr['job_title'][$counter]['distance'] = $distance;
             $counter++;
          }
          }
           if(isset($content->skills)){
              $counter =0 ;
              foreach($content->skills as $jt){
                $word = $jt->word;
                $distance = $jt->distance;
                $arr['skills'][$counter]['word'] = $word;
                $arr['skills'][$counter]['distance'] = $distance;
                 $counter++;
              }
      }

          $this->extract = $arr;
       



        }elseif($this->type == 'pairs'){

          $content = json_decode($response);
          $counter =0 ;
          foreach($content as $k=>$v){
 
            $arr['pairs'][$counter]['word'] = $k ;
            $arr['pairs'][$counter]['distance'] = $v;
             $counter++;
          }

           $this->extract = $arr;

            

        }


       
        
         
    }

  
  

 
 

}
