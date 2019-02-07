# Cakephp-Paystack
A Cakephp 3.x Plugin for making paystack payments
Installation
PHP 5.4+ , and Composer are required.

To get the latest version of Cakephp Paystack, simply require it

    <?php

        composer require jtad009/cakephp-paystack:dev-master
    ?>

Or add the following line to the require block of your composer.json file.

    <?php

        "jtad009/cakephp-paystack": "dev-master"
    ?>

You'll then need to run composer install or composer update to download it and have the autoloader updated.

Once  Cakephp-Paystack is installed, you need to register the plugin. Open up config/bootstrap.php and add the following .


    <?php 

        Plugin::load("PayStack", ["bootstrap" => false, "routes" => true,"autoload"=>true]);

    ?>

##General payment flow

Though there are multiple ways to pay an order, most payment gateways expect you to follow the following flow in your checkout process:

###1. The customer is redirected to the payment provider After the customer has gone through the checkout process and is ready to pay, the customer must be redirected to site of the payment provider.

The redirection is accomplished by submitting a form with some hidden fields. The form must post to the site of the payment provider. The hidden fields minimally specify the amount that must be paid, the order id and a hash.



##Usage
Open your config/path.php file and add your public key, secret key, merchant email and payment url like so:

    <?php 

        define("PaystackPublicKey",xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx);
        define("PaystackSecretKey",xxxxxxxxxxxxxxxxxxxxxxxxxx);

    ?>
##Simple Example on how to pay with this plugin
--Let's say a cutomer wants to pay for sms

Step 1: Include the following code into your AppController.php to load the paystack component
       
       <?php 
        
            $this->loadComponent('PayStack.PayStack');
            $this->loadComponent('PayStack.CurlConnection');
        ?>
        
Step 2:create view using the following code 
    
    <?php
        //Note This form will be setup as per you requirement. in my case i needed to pay for sms units
        echo $this->Form->create(null,['url'=>['controller'=>'as-per-requirement','action'=>'purchase-sms']]);
        echo $this->Form->input('amount',['templates'=>['inputContainer'=>'<div class="form-group">{{content}}<p class="  mb-3 mt-2"> <span  id="allocatedUnits" class="text-danger pull-right small ">0 </span><span class="small pull-right text-muted mr-2 ">UNIT(S) Worth: </span><span class="small text-muted mr-2"> Send to </span><span  id="reach" class="text-danger small ">0 </span></p></div>'],'class'=>'form-control','style'=>'resize:none','maxlength'=>"290",'options'=>['500'=>'500','1000'=>'1000','1500'=>'1500','2000'=>'2000','3000'=>'3000','5000'=>'5000','7000'=>'7000','10000'=>'10000'],'empty'=>'Select amount you want to pay','id'=>'sms-amount']);
       echo $this->Form->submit('PURCHASE UNITS',['class'=>'btn btn-sm btn-danger btn-block mt-2   ']);
       echo $this->Form->end();
    
    ?>
    
Step 3: in your controller create an action, mine will be PurchaseSMS()
    
    <?php
    
        public function purchaseSMS(array $data){
            $postArray = array(
                'description'=>'SMS UNIT PURCHASE',
                'first_name'=>'EXPECTED_FIRST_NAME',//name of the person paying
                'email' => 'EXPECTED EMAIL', //email of the person paying
                'amount' => $data['amount'].'00',
                'callback_url'=>'https://skole.com.ng/phone-manager/success', //this points back to my website i choose to not use the callback on the dev dashboard of paystack you can choose otherwise
                'metadata.cancel_action'=>'https://skole.com.ng/phone-manager/error',
                "reference" => md5(uniqid()));

            $paystackResponse = $this->PayStack->payWithPaystack($postArray); //send payment details to the paystack API
            if($paystackResponse->status):
            //if status is true then get refereence code for confirmation "$paystackResponse->data->reference"
            
            //take us to the payment page to 
                $this->redirect($paystackResponse->data->authorization_url);
            endif;
        }

        //Authorization url will redirect you to this function 
        //$routes->connect('/success/', ['controller' => 'StudentsProfiles', 'action' => 'complete']);
        //I had set up a route to redirect to complete action when the callback_url above is lookedup by paystack
        
        public function complete(){
            
            $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
            if(!$reference){
            die('No reference supplied');
            }else{
                $callbackResponse = $this->PayStack->callback($reference,$this->Auth->user('id'));
                if(!callbackResponse && is_array($callbackResponse)):
                    // you can then save information to the database if the ref no is valid
                endif;
            }
            
        }
    ?>
    

After verification of reference code  the following response is sent and you can use this to update DB 


<?php 

    object(stdClass)#224 (3) { 
                ["status"]=> bool(true) 
                ["message"]=> string(23) "Verification successful" 
                ["data"]=> object(stdClass)#315 (24) { 
                ["id"]=> int(108909776) 
                ["domain"]=> string(4) "test" 
                ["status"]=> string(7) "success" 
                ["reference"]=> string(32) "947d2594fe0d79a6b88a0bb3868dbf8c"
                ["amount"]=> int(700000) 
                ["message"]=> NULL 
                ["gateway_response"]=> string(10) "Successful"
                ["paid_at"]=> string(24) "2019-02-07T08:00:14.000Z"
                ["created_at"]=> string(24) "2019-02-07T07:59:08.000Z" 
                ["channel"]=> string(4) "card" 
                ["currency"]=> string(3) "NGN" 
                ["ip_address"]=> string(14) "105.112.10.202" 
                ["metadata"]=> string(0) "" 
                ["log"]=> object(stdClass)#328 (8) { 
                        ["start_time"]=> int(1549526177) 
                        ["time_spent"]=> int(41) 
                        ["attempts"]=> int(1)
                        ["errors"]=> int(0) 
                        ["success"]=> bool(true) 
                        ["mobile"]=> bool(false) 
                        ["input"]=> array(0) { } 
                        ["history"]=> array(2) { [0]=> object(stdClass)#322 (3) { 
                            ["type"]=> string(6) "action" 
                            ["message"]=> string(26) "Attempted to pay with card"
                            ["time"]=> int(27) } [1]=> object(stdClass)#277 (3) {
                                      ["type"]=> string(7) "success" 
                                      ["message"]=> string(27) "Successfully paid with card"
                                      ["time"]=> int(41) } } } ["fees"]=> int(20500)
                                      ["fees_split"]=> NULL 
                                      ["authorization"]=> object(stdClass)#319 (12) { 
                                            ["authorization_code"]=> string(15) "AUTH_dffoja54zz"
                                            ["bin"]=> string(6) "408408" 
                                            ["last4"]=> string(4) "4081" 
                                            ["exp_month"]=> string(2) "12" 
                                            ["exp_year"]=> string(4) "2020" 
                                            ["channel"]=> string(4) "card"
                                            ["card_type"]=> string(10) "visa DEBIT" 
                                            ["bank"]=> string(9) "Test Bank" 
                                            ["country_code"]=> string(2) "NG"
                                            ["brand"]=> string(4) "visa" 
                                            ["reusable"]=> bool(true) 
                                            ["signature"]=> string(24) "SIG_3GW2W7IImwHivl3ErQcX" } 
                                            ["customer"]=> object(stdClass)#256 (8) {
                                                ["id"]=> int(1792984) 
                                                ["first_name"]=> string(0) "" 
                                                ["last_name"]=> string(0) "" 
                                                ["email"]=> string(23) "israel4real@hotmail.com" 
                                                ["customer_code"]=> string(19) "CUS_z8fhmn1y8ke9tmi"
                                                ["phone"]=> NULL ["metadata"]=> object(stdClass)#320 (0) { } 
                                                ["risk_action"]=> string(7) "default" } 
                                                ["plan"]=> NULL 
                                                ["paidAt"]=> string(24) "2019-02-07T08:00:14.000Z" 
                                                ["createdAt"]=> string(24) "2019-02-07T07:59:08.000Z" 
                                                ["transaction_date"]=> string(24) "2019-02-07T07:59:08.000Z" 
                                                ["plan_object"]=> object(stdClass)#307 (0) { } 
                                                ["subaccount"]=> object(stdClass)#310 (0) { } } }
                                                
             ?>

##Todo
Charge Returning Customers
Add Comprehensive Tests
Implement Transaction Dashboard to see all of the transactions in your Cakephp app
List/add Customers
Create/fetch/update Payment plans
Get all transaction history
Manage Subsctiptions
Export Transactions as csv


##How can I thank you?
Why not star the github repo? I'd love the attention! Why not share the link for this repository on Twitter or HackerNews? Spread the word!

Don't forget to follow me on <a href="http://linkedin.com/in/%D0%B8%D1%81%D1%80%D0%B0%D0%B5%D0%BB-%D0%B5%D0%B4%D0%B5%D1%82-502b27174">LinkedIn</a>

Thanks! Israel Edet.

