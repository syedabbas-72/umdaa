<?php

// defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
ini_set('memory_limit', '-1');
require APPPATH . '/libraries/REST_Controller1.php';
class OnlinePayment extends REST_Controller1
{
//  public function __construct(){      
//     parent::__construct();     
//  }


public function eo_payment_webview_post(){
    extract($_POST);
    $data['list']["MID"] = "sOynRz56677151483389";
    $data['list']["ORDER_ID"] = "ORDR1234".rand(1111,99999);
    $data['list']["CUST_ID"] = "001";
    $data['list']["INDUSTRY_TYPE_ID"] = "Retail";
    $data['list']["CHANNEL_ID"] = "WEB";
    $data['list']["TXN_AMOUNT"] = $amount;
    $data['list']["WEBSITE"] = "WEBSTAGING";
    $data['list']["CALLBACK_URL"] = "http://devumdaa.in/dev/OnlinePayment/paymentStatus";
    $data['list']['CHECKSUMHASH'] = getChecksumFromArray($data['list'], "6tQiV1MAx4LeizE&");

    // $param['url'] = "http://devumdaa.in/dev/OnlinePayment/paymentStatus";

    // $this->response(array('code'=>'200','message'=>'success','result'=>$param));

    $this->load->view("onlinepayment/payment",$data);
}

public function paymentStatus(){
    $data['postdata'] = $_POST;
    $this->load->view("onlinepayment/status",$data);
}


}
