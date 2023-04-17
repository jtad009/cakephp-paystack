<?php
namespace jtad009\PayStack\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * CurlConnection component
 */
class CurlConnectionComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    
    public function  urlOpen($url) {
       // Fake the browser type 
        ini_set('user_agent', 'MSIE 4\.0b2;');
        $dh = fopen("$url", 'r') != FALSE ? fopen("$url", 'r') : "0";
        $result = fread($dh, 8192);
        return $result;
    }
    /**
     * Using CURL to open connection outside of this aplication 
     * @param array $options indicates the data sent out of this App
     */
    public function payStackConnection($options){
        $curl = curl_init();
        curl_setopt_array($curl,$options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if($err){
            // there was an error contacting the Paystack API
          die('Curl returned error: ' . $err);
        }else{
            return $response;
        }
    }
}
