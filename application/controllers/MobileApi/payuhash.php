<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class payuhash extends REST_Controller1
{

	   public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
    }

    public function testing_post()                                                                   
    {
        //  extract($_POST);
        if(isset($_POST))
        {
            $key="fort367"; //posted merchant key from client
            $salt="hyliop876"; // add salt here from your credentials in payUMoney dashboard
            $txnid=$_POST['txnid']; //posted txnid from client
            $amount=$_POST["amount"]; //posted amount from client 
            $productInfo=$_POST["productInfo"]; // posted product info from client
            $firstName=$_POST["firstName"]; // posted firstname from and must be without space
            $email=$_POST["email"];

            /***************** USER DEFINED VARIABLES GOES HERE ***********************/
            //all varibles posted from client
            $udf1="umdaa1";
            $udf2="health2";
            $udf3="patient3";
            $udf4="care007";
            $udf5="private7";
            
            /***************** DO NOT EDIT ***********************/
     $payhash_str = $key . '|' . check($txnid) . '|' .check($amount)  . '|' .check($productInfo)  . '|' . check($firstName) . '|' . check($email) . '|' . check($udf1) . '|' . check($udf2) . '|' . check($udf3) . '|' . check($udf4) . '|' . check($udf5) . '||||||'. $salt;

 // $payhash_str = $key . '|' . ($txnid) . '|' .($amount)  . '|' .($productInfo)  . '|' . ($firstName) . '|' . ($email) . '|' . ($udf1) . '|' . ($udf2) . '|' . ($udf3) . '|' . ($udf4) . '|' . ($udf5) . '||||||'. $salt;

function check($value) {
            if ($value == null) {
                  return '';
                 //  	$status = 0;
	                // $errormsg = 'Please Check your Amount!!!';
            } else {
                  return $value;
                 //  	$status = 200;
	                // $errormsg = 'No errors';
            }
      }
            $hash = strtolower(hash('sha512', $payhash_str));
            /***************** DO NOT EDIT ***********************/
            // $errormsg = "";
            $status = 0;
            if (empty($txnid)){
                $errormsg = "Transaction ID should not be empty.";
            } elseif ($amount == null || $amount == "0"){
                $errormsg = "Amount should not be empty  or '0'.";
            } elseif ($productInfo == null){
                $errormsg = "Please fill Info.";
            } elseif ($firstName == null){
                $errormsg = "Please Enter Name.";
            } elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
                 $errormsg = "Invalid email.";
            } else {
                $status = 200;
                $errormsg = "No errors Found";
            }
            $arr['result'] = $hash;
            $arr['status'] = $status;
            $arr['errormessage']=$errormsg;
            $output=$arr;
            // $this->response(array('code'=>'200','message'=>'success','result'=>$output));
            echo json_encode($output);
            echo json_encode($txnid);
            echo json_encode($amount);
            echo json_encode($email);
        }
        else
        {
            $param = "UnAuthorized Access";
            $this->response(array('code'=>'200','message'=>'UnAuthorized Access'));
        }
    }


}