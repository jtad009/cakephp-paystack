<?php
/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) israel Edet <jtad009@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PayStack\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * PayStack component
 */
class PayStackComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    public $components = ['CurlConnection'];
    
    private $secretKey = PaystackTestSecretKey;
    private $publicKey = PaystackTestPublicKey;
    private  $url = "https://api.paystack.co/transaction/initialize";
    private  $verifyUrl = "https://api.paystack.co/transaction/verify/";
    
    /* Initiate a payment request to Paystack
     * Included the option to pass the payload to this method for situations 
     * when the payload is built on the fly (not passed to the controller from a view)
     * @param payload to be sent to paystack
     * @return array|false
     */
    public function payWithPaystack($postdata){
      $result = array();

      $options = array(
          CURLOPT_URL => $this->url,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS=>json_encode($postdata),
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$this->secretKey,
                'Content-Type: application/json',
          ],
        );
      $request = $this->CurlConnection->payStackConnection($options); //make reques via Curl
       if ($request) {
          $result = json_decode($request);
          
          if(!$result->status){
            // there was an error from the API
            die('API returned error: ' . $result->message);
          }else{
           
            return $result;
          }
        }
    }
    

    /**
     * Hit Paystack Gateway to Verify that the transaction is valid
     * @param String $reference
     * @return array|false
     */
    public function callback($reference){
    	$options = array(
          CURLOPT_URL => $this->verifyUrl . rawurlencode($reference),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );

		$response = $this->CurlConnection->payStackConnection($options);
    		$tranx = json_decode($response);
		return $tranx;
    

    }
    
}
