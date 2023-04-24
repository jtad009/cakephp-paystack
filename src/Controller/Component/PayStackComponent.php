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
use Exception;

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
    private $url = "https://api.paystack.co/transaction/initialize";
    private $verifyUrl = "https://api.paystack.co/transaction/verify/";
    private $customerUrl = "https://api.paystack.co/customer/";
    private $subaccountUrl = "https://api.paystack.co/subaccount/";
    private $transactionUrl = "https://api.paystack.co/transaction/";
    private $banks ="https://api.paystack.co/bank?country=nigeria";
    private $verifyAccountNumber ="https://api.paystack.co/bank/resolve?account_number=:accountNumber&bank_code=:bankCode";
  
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

    /**
    * Create subaccount that will be used for split payments
    * @link https://developers.paystack.co/v1.0/reference#create-subaccount see doc for array key options
    * @param array $data
    */
    public function createSubaccount(array $data){
      $options = array(
          CURLOPT_URL => $this->subaccountUrl,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS=>json_encode($data),
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    *Fetch subaccount
    *@param String $account_id account identfier
    */
    public function fetchSubaccount($account_id){
      $options = array(
          CURLOPT_URL => $this->subaccountUrl.$account_id,
          CURLOPT_HTTPGET => 1,
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    * Update subaccount that will be used for split payments
    * @link https://developers.paystack.co/v1.0/reference#update-subaccount see doc for array key options
    * @param string $account_id
    * @param array $data information to send about account
    */
    public function updateSubaccount($account_id,array $data){
      $options = array(
          CURLOPT_URL => $this->subaccountUrl.$account_id,
          CURLOPT_PUT => 1,
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    * Create a new Customer
    * @link https://developers.paystack.co/v1.0/reference#create-customer see doc for array keys options.
    * @param array $data 
    * @return array|false contains an array of the newly created customer details
    */
    public function createCustomer(array $data){
      $options = array(
          CURLOPT_URL => $this->customerUrl,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS=>json_encode($data),
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    * Update a Customer information
    *@link https://developers.paystack.co/v1.0/reference#update-customer see doc for array keys
    * @param array $data
    */
    public function updateCustomer(array $data){
      $options = array(
          CURLOPT_URL => $this->customerUrl.rawurlencode($data['customer_id']),
          CURLOPT_PUT => 1,
          CURLOPT_POSTFIELDS=>json_encode($data),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    * list all registered  Customer
    * 
    */
    public function listCustomers(){
      $options = array(
          CURLOPT_URL => $this->customerUrl,
          CURLOPT_HTTPGET =>1,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".$this->secretKey,
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    * list all transactions
    * @param array $data
    */
    public function listTransactions($data = [] ){
       $accountSetting = \Cake\Datasource\ConnectionManager::get('default')->execute("SELECT pk_, sk_ FROM account_settings where school_id = ?", [$_SESSION['Auth']['User']['school_id']])->fetch('assoc');
      
      $key = $accountSetting['sk_'];
      if(empty($data)):
          $options = array(
            CURLOPT_URL => $this->transactionUrl,
            CURLOPT_HTTPGET =>1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
              "accept: application/json",
              "authorization: Bearer ".(empty($key) ? $this->secretKey : $key),
              "cache-control: no-cache"
            ],
          );
      else:
           $this->transactionUrl = substr($this->transactionUrl, 0 , strlen($this->transactionUrl)-1).'?'.http_build_query($data);
          
            $options = array(
              CURLOPT_URL => $this->transactionUrl,
              CURLOPT_HTTPGET =>1,
              // CURLOPT_POSTFIELDS=>,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer ".(empty($key) ? $this->secretKey : $key),
                "cache-control: no-cache"
              ],
            );
      endif;
       //var_dump($options);
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }

    /**
    * List all transactions by a particular customer
    * @param array $data
    */
    public function fetchTransaction($reference){
      $accountSetting = \Cake\Datasource\ConnectionManager::get('default')->execute("SELECT pk_, sk_ FROM account_settings where school_id = ?",[ $_SESSION['Auth']['User']['school_id']])->fetch('assoc');
      
      $key = $accountSetting['sk_'];
      $options = array(
          CURLOPT_URL => $this->transactionUrl.rawurlencode($reference),
          CURLOPT_HTTPGET =>1,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".(empty($key) ? $this->secretKey : $key),
            "cache-control: no-cache"
          ],
        );
      $response = $this->CurlConnection->payStackConnection($options);
      return json_decode($response);
    }
    public function getTransactionLocationInformation($ip){
      $options = array(
          CURLOPT_URL => 'http://api.ipstack.com/'.($ip).'?access_key='.LOCATION_API_KEY.'&format=1',
          CURLOPT_HTTPGET =>1,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "cache-control: no-cache"
          ],
        );
    $response = $this->CurlConnection->payStackConnection($options);
    return json_decode($response);
  }
  public function banks($bank = ''){
    $options = array(
      CURLOPT_URL => $this->banks,
      CURLOPT_HTTPGET => 1,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer " . (empty($key) ? $this->secretKey : $key),
        "cache-control: no-cache"
      ],
    );
    $response = $this->CurlConnection->payStackConnection($options);
    $data = json_decode($response);
    if($data->status == true):
      if(strlen($bank) > 1):
        $filterered = array_filter($data->data, function($bankData) use ($bank) {
          return false !== stristr(strtolower($bankData->name), strtolower($bank));
        });
       return $filterered;
      endif;
      return $data->data;
    endif;
    throw new Exception($data->message, 1);
  }

   /**
   * Verify a customers account number
   * @param int $accountNumber the account number to verify
   * @param int $sortCode the banks sort code
   */
  public function verifyAccount($accountNumber, $sortCode)
  {
    
    $options = array(
      CURLOPT_URL => str_replace(
        [':accountNumber', ':bankCode'],
      [rawurlencode($accountNumber), rawurlencode($sortCode)],
      $this->verifyAccountNumber),
      CURLOPT_HTTPGET => 1,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer " . ($this->secretKey),
        "cache-control: no-cache"
      ],
    );
    $response = $this->CurlConnection->payStackConnection($options);
    $data = json_decode($response);
   if($data->status === true){
     return $data->data->account_name;
   }

    throw new Exception($data->message, 1);
    
  }
}
