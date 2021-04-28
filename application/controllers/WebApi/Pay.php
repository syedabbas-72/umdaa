<?php


defined('BASEPATH') OR exit('No direct script access allowed');

// error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';
// require APPPATH . '/libraries/paytm.php';
// require_once("Payment/Payment.php");

class Pay extends REST_Controller1
{

	   public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        // $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
        $this->load->model('PayModel');
        // $this->load->library('Payment');
       }

       public function generateHash_get($number)
       {  
        extract($_POST);

        // $number = "UMDAA0072".rand(10,1000);
     
        $paytmParams = array();

        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            // "mid"           => "VxAXDZ21424666393729",
            "mid"           => "nCOgOt45031947680542",
            "websiteName"   => "WEBSTAGING",
            "orderId"       => $number,
            "callbackUrl"   => "http://localhost:8100/#/payment/26",
            "txnAmount"     => array(
                "value"     =>"1.00",
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => "CUST_001",
            ),
        );
        
        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
        */
        $checksum = $this->PayModel->generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "vpnFB25M5balcys@");
        
        $paytmParams["head"] = array(
            "signature"    => $checksum
        );
        
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        
        /* for Staging */
        $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=nCOgOt45031947680542&orderId=$number";
        
        /* for Production */
        // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
        $response = curl_exec($ch);
        // print_r($response);
        $token = json_decode($response,true);
        // $token['sriraj'] = $number;
        $this->response(array('code'=>'200','message'=>'Success','result'=>$token));
        // print_r(json_decode($response,true));
        // print_r($token['body']['txnToken']);
        // print_r(json_decode($response)['body'].txnToken);
        // print_r($response['txnToken']);

        // Process Transaction
        // $paytmParamss = array();

        // $paytmParamss["body"] = array(
        //     "requestType" => "Payment",
        //     "mid"         => "VxAXDZ21424666393729",
        //     "orderId"     => $number,
        //     "paymentMode" => "CREDIT_CARD",
        //     "cardInfo"    => "|4111111111111111|111|122032",
        //     "authMode"    => "otp",
        // );

        // $paytmParamss["head"] = array(
        //     "txnToken"    => $token['body']['txnToken']
        // );

        // $post_data = json_encode($paytmParamss, JSON_UNESCAPED_SLASHES);

        // /* for Staging */
        // $url = "https://securegw-stage.paytm.in/theia/api/v1/processTransaction?mid=VxAXDZ21424666393729&orderId=$number";

        // /* for Production */
        // // $url = "https://securegw.paytm.in/theia/api/v1/processTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";

        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
        // $responsee = curl_exec($ch);
        // print_r($responsee);


        // fetch api
        // $paytmParamsss = array();
        // $paytmParamsss["head"] = array(
        // "tokenType" => "TXN_TOKEN",
        // 'token'     => $token['body']['txnToken']
        // );
        // $paytmParamsss["body"] = array(
        // "mid" => "VxAXDZ21424666393729"
        // );
        // /* prepare JSON string for request */
        // $post_data = json_encode($paytmParamsss, JSON_UNESCAPED_SLASHES);
        // /* for Staging */
        // $url = "https://securegw-stage.paytm.in/theia/api/v2/fetchPaymentOptions?mid=VxAXDZ21424666393729&orderId=$number";

        // /* for Production */
        // //$url = "https://securegw.paytm.in/theia/api/v2/fetchPaymentOptions?mid=YOUR_MID_HERE&orderId=ORDERID_98765";


        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
        // $responsee = curl_exec($ch);
        // print_r($responsee);          



               
        }

        public function getDecode_get($data)
        {
            $encrypted      = urldecode($data);
            // $pass           = 'PaymentAbcD';
            // $method         = 'aes-128-cbc';
            // $initVector     = "0123456789012345";    // MUST BE 16 BYTES LONG...
            // $decrypted = openssl_decrypt($encrypted, $method, $pass, false, $initVector);
            $decrypted = DataCrypt($encrypted, 'decrypt');

            $this->response(array('code'=>'200','messsage'=>'Success','result'=>json_decode($decrypted)));
        }

        public function checkArray_post()
        {
            // extract($_POST);
            // echo "<pre>";
            // print_r($_POST);
            // print_r($_POST['ORDERID']);
            // print_r($_POST['CHECKSUMHASH']);
            // echo "</pre>"; 
           $data['postdata'] = $_POST;

           $userDataJSON   = json_encode($_POST);

           $encrypted = DataCrypt($userDataJSON, 'encrypt');
           if($_POST['STATUS'] == 'TXN_FAILURE')
           {
            // header("Location:localhost:8100/#/success/" . urlencode($encrypted));
            //    header("Location:http://192.168.29.95:8080/#/success/" . urlencode($encrypted));
            header("Location:https://citizen.umdaa.co/#/success/" . urlencode($encrypted));
           }
           else{
            // header("Location:localhost:8100/#/transactioninfo/" . urlencode($encrypted));
            //    header("Location:http://192.168.29.95:8080/#/transactioninfo/" . urlencode($encrypted));
            header("Location:https://citizen.umdaa.co/#/transactioninfo/" . urlencode($encrypted));
           }

        //    $pass           = 'PaymentAbcD';
        //    $method         = 'aes-128-cbc';
        //    $initVector     = "0123456789012345"; 
        //    // encrypt the data (and base64 encode)
        //    $encrypted      = openssl_encrypt($userDataJSON, $method, $pass, false, $initVector);
        //    echo $encrypted;
        //    $decrypted = openssl_decrypt($encrypted, $method, $pass, false, $initVector);
        //        echo "decrypted data".$decrypted;
            //    exit();
        //    header("Location:http://localhost:8100/#/transactioninfo/" . urlencode($encrypted));
            //    $data['checkData']
            // $this->load->view("onlinepayment/citizenstatus",$data);
                
                /**
                * import checksum generation utility
                * You can get this utility from https://developer.paytm.com/docs/checksum/
                */
                // require_once("PaytmChecksum.php");

                /* initialize an array */
                // $paytmParams = array();

                /* body parameters */
                // $paytmParams["body"] = array(

                //     /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
                //     "mid" => "nCOgOt45031947680542",

                //     /* Enter your order id which needs to be check status for */
                //     "orderId" => $_POST['ORDERID'],
                // );

                /**
                * Generate checksum by parameters we have in body
                * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
                */
                // $checksum = $this->PayModel->generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "vpnFB25M5balcys@");

                /* head parameters */
                // $paytmParams["head"] = array(

                //     /* put generated checksum value here */
                //     "signature"	=> $checksum
                // );

                /* prepare JSON string for request */
                // $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

                /* for Staging */
                // $url = "https://securegw.paytm.in/v3/order/status";

                /* for Production */
                // $url = "https://securegw.paytm.in/v3/order/status";

                // $ch = curl_init($url);
                // curl_setopt($ch, CURLOPT_POST, 1);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  
                // $response = curl_exec($ch);
                // echo "<pre>";
                // print_r(json_decode($response,true));
                // echo "</pre>";


        }

        public function checkArrayData_get($order_id)
        {
            // extract($_POST);
            // echo "<pre>";
            // print_r($_POST);
            // print_r($_POST['ORDERID']);
            // print_r($_POST['CHECKSUMHASH']);
            // echo "</pre>";

        
                /**
                * import checksum generation utility
                * You can get this utility from https://developer.paytm.com/docs/checksum/
                */
                // require_once("PaytmChecksum.php");

                /* initialize an array */
                $paytmParams = array();

                /* body parameters */
                $paytmParams["body"] = array(

                    /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
                    "mid" => "nCOgOt45031947680542",

                    /* Enter your order id which needs to be check status for */
                    "orderId" => $order_id,
                );

                /**
                * Generate checksum by parameters we have in body
                * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
                */
                $checksum = $this->PayModel->generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "vpnFB25M5balcys@");

                /* head parameters */
                $paytmParams["head"] = array(

                    /* put generated checksum value here */
                    "signature"	=> $checksum
                );

                /* prepare JSON string for request */
                $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

                /* for Staging */
                $url = "https://securegw.paytm.in/v3/order/status";

                /* for Production */
                // $url = "https://securegw.paytm.in/v3/order/status";

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  
                $response = curl_exec($ch);
                echo "<pre>";
                print_r(json_decode($response,true));
                echo "</pre>";


        }


        public function naveen_get(){
                        
            $data['list']["MID"] = "VxAXDZ21424666393729";
            $data['list']["ORDER_ID"] = "ORDRNVN143";
            $data['list']["CUST_ID"] = "1";
            $data['list']["INDUSTRY_TYPE_ID"] = "Retail";
            $data['list']["CHANNEL_ID"] = "WAP";
            $data['list']["TXN_AMOUNT"] = "1000";
            $data['list']["WEBSITE"] = "WEBSTAGING";
            $data['list']["CALLBACK_URL"] = base_url("OnlinePayment/paymentStatus");
            $data['list']['CHECKSUMHASH'] = getChecksumFromArray($data['list'], "eQT7e!V9Pff5UALH");

            echo "<pre>";
            print_r($data);
            echo "</pre>";


        }

    
            public function safe_get()
            {  
                 
                $number = "NVN04".rand(10,1000);
     
                 $paytmParams = array();
     
                 $paytmParams["body"] = array(
                     "requestType"   => "Payment",
                     "mid"           => "VxAXDZ21424666393729",
                     "websiteName"   => "WEBSTAGING",
                     "orderId"       => $number,
                     "callbackUrl"   => "https://devumdaa.in/dev",
                     "txnAmount"     => array(
                         "value"     => "1.00",
                         "currency"  => "INR",
                     ),
                     "userInfo"      => array(
                         "custId"    => "CUST_001",
                     ),
                 );
                 
                 /*
                 * Generate checksum by parameters we have in body
                 * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
                 */
                 $checksum = $this->PayModel->generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "eQT7e!V9Pff5UALH");
                 
                 $paytmParams["head"] = array(
                     "signature"    => $checksum
                 );
                 
                 $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
                 
                 /* for Staging */
                 $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=VxAXDZ21424666393729&orderId=$number";
                 
                 /* for Production */
                 // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";
                 
                 $ch = curl_init($url);
                 curl_setopt($ch, CURLOPT_POST, 1);
                 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                 curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
                 $response = curl_exec($ch);
                 print_r($response);
     
                    
             }

             public function generate_signature_get( $api_key, $api_sercet, $meeting_number, $role){

                $time = time() * 1000; //time in milliseconds (or close enough)
                
                $data = base64_encode($api_key . $meeting_number . $time . $role);
                
                $hash = hash_hmac('sha256', $data, $api_sercet, true);
                
                $_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
                
                //return signature, url safe base64 encoded
                // return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
                $data = rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');

                $this->response(($data));
            }

            public function IVS_post()
    {
        extract($_POST);
        $api_key     = "6ff35f3db1b7a138e764177e1e9a61ddbe72a648158df1e4";
        $api_token   = "5c80b1322e205f4d8bf2dfc12ff932369678de300ab0c561";
        $exotel_sid  = "umdaa1";
        $post_data = array(
            'From'=> $from_number,
            'To' =>  $to_number,
            'CallerId' => "04048212440",
            'CallType' => "trans"
        );
        $headers = array(
            'Content-Type' => 'application/json'
        );
        $url = "https://" . $api_key .  ":"  . $api_token . "@api.exotel.com/v1/Accounts/" . $exotel_sid . "/Calls/connect.json";
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $http_result = curl_exec($ch);
        curl_close($ch);
        // echo "<pre>";
        $res = json_decode($http_result, true);
        // print_r($res);
        // if()
        // echo $res;
        if(@$res['RestException']['Status'] == "403"){
            $message = "Call can not be made because of TRAI NDNC regulations";
            $code = "201";
        }
        elseif(@$res['Call']['Status'] == "in-progress"){
            $message = "Connecting the call";
            $code = "200";
        }
        else{
            $message = "Error Occured";
            $code = "202";
        }

        $this->response(array("code"=>$code,"message"=>$message));

    }
    

}