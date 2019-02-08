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
            use jtad009\Controller\Component\PayStack;
            
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
                $transactionResponse =  $this->PayStack->callback($reference);
                   if(!$transactionResponse->status){
                    // there was an error from the API
                    $this->flash->error('API returned error: ' . $tranx->message);
                  }

                  if('success' == $transactionResponse->data->status){
                    // transaction was successful...
                    // print out reponse and use as required
                   debug(transactionResponse);

                  }
            }
            
        }
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

Don't forget to follow me on <a href="http://linkedin.com/in/israel-edet">LinkedIn</a>

Thanks! Israel Edet.

