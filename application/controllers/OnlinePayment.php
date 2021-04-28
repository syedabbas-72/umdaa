<?php
error_reporting(0);
// defined('BASEPATH') OR exit('No direct script access allowed');

class OnlinePayment extends CI_Controller {
 public function __construct(){      
    parent::__construct();     
 }


public function eo_payment_webview($transaction_id){
    extract($_POST);
    $check = $this->Generic_model->getSingleRecord('eo_wallet_history', array('transaction_id'=>$transaction_id));
    $data['list']["MID"] = "sOynRz56677151483389";
    $data['list']["ORDER_ID"] = $transaction_id;
    $data['list']["CUST_ID"] = $check->doctor_id;
    $data['list']["INDUSTRY_TYPE_ID"] = "Retail";
    $data['list']["CHANNEL_ID"] = "WEB";
    $data['list']["TXN_AMOUNT"] = $check->transaction_amount;
    $data['list']["WEBSITE"] = "WEBSTAGING";
    $data['list']["CALLBACK_URL"] = base_url("OnlinePayment/paymentStatus");
    $data['list']['CHECKSUMHASH'] = getChecksumFromArray($data['list'], "6tQiV1MAx4LeizE&");
    // echo "<pre>";
    
    // print_r($data);echo "</pre>";
    // exit;

    $this->load->view("onlinepayment/payment",$data);
}

public function paymentStatus(){
    $data['postdata'] = $_POST;
    
    extract($_POST);
    if($STATUS == "TXN_SUCCESS"){
        $eoHistoryInfo = $this->Generic_model->getSingleRecord('eo_wallet_history', array('transaction_id'=>$ORDERID));
        $doctorWallet = $this->Generic_model->getSingleRecord('doctor_wallet_prices', array('doctor_id'=>$eoHistoryInfo->doctor_id));
        if(count($doctorWallet) > 0){
            $data1['amount'] = $TXNAMOUNT+$doctorWallet->amount;
            $this->Generic_model->updateData("doctor_wallet_prices", $data1, array('doctor_wallet_id'=>$doctorWallet->doctor_wallet_id));
        }
        else{
            $data1['doctor_id'] = $eoHistoryInfo->doctor_id;
            $data1['created_by'] = $eoHistoryInfo->doctor_id;
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $data1['amount'] = $TXNAMOUNT+$doctorWallet->amount;
            $this->Generic_model->insertData("doctor_wallet_prices", $data1);
        }
        

        $updateData['payment_status'] = 1;
        $this->Generic_model->updateData('eo_wallet_history', $updateData, array('transaction_id' => $ORDERID));
        $this->Generic_model->angularNotifications('', '', $eoHistoryInfo->doctor_id, '', 'EO_Wallet_Money_Added', '');
    }
    elseif($STATUS == "TXN_FAILURE"){
        $updateData['payment_status'] = 2;
        $this->Generic_model->updateData('eo_wallet_history', $updateData, array('transaction_id' => $ORDERID));
    }

    $this->load->view("onlinepayment/status",$data);
}


}
