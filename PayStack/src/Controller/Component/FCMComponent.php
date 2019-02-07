<?php

namespace Paystack\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * FCM component
 */
class FCMComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    public $components = ['CurlConnection'];
    /**
     * Build data to push ro users
     * @param type $to to topic {/topics/topic_title} or to particular user token or to every user
     * @param type $title title of the message to be sent (OPtional)
     * @param type $message body of message to send
     */
    public function buildPayload($to,$title,$message)
    {
        return  $fields = array(
            'to' => $to,
            'notification' => array(
                "body" => $message,
                "title" => $title,
                "icon" => "skole"
            )
        );
    }
    /**
     * Send notification to a particular topic
     * @param type $data data array to send
     */
    public function pushToServer($data) {
        $url = 'https://fcm.googleapis.com/fcm/send';
       
        $fields = json_encode($data);
        
        $headers = array(
            // ent-Type: application/json'
        );
        $options = array(
          CURLOPT_URL => $url,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS=>$fields,
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_HTTPHEADER => $headers,
        );
         $result = $this->CurlConnection->payStackConnection($options);
        
        curl_close($ch);
        return $result;
    }

    /**
     * Send User Tokens to server
     * this tokens are for specific users
     * @param array $token user personal tokens
     */
    public function pushToServerViaToken(array $token) {
        
    }

}
