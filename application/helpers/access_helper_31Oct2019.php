<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('is_logged_in'))

{

    function is_logged_in()
    {
        $CI =& get_instance();

        $user = $CI->session->userdata('is_logged_in');

        if (!isset($user)) { return false; } else { return true; }

    }

    function getvitalsbydate($date,$sign){
        $CI =& get_instance();
        $res = $CI->db->query("select * from patient_vital_sign where vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign where vital_sign_recording_date_time like '".$date."%') and vital_sign='".$sign."'  order by vital_sign_recording_date_time DESC")->row();

        if($res){
            return $res->vital_result;
        }else{
            return "";
        }
    }



    function hasPermission($entity_id,$field){
        $CI =& get_instance();
        $profile_id = $CI->session->userdata('role_id');

        $entity_list_id = $CI->db->query("select * from user_entities where user_entity_alias ='".$entity_id."'")->row();

        $role_permessions = $CI->db->query("select * from profile_permissions where user_entity_id ='".$entity_list_id->user_entity_id."' and profile_id ='".$profile_id."' and $field = 1")->row();

        if(count($role_permessions)>0){
            return true;
        }else{
            return false;
        }


    }


    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }



    function getparameters($id,$date){
        $CI =& get_instance();
        $res = $CI->db->query("select * from patient_followup pf inner join patient_followup_line_items pfl on(pf.patient_followup_id = pfl.patient_followup_id)  where pfl.parameter_id = '".$id."' and pfl.appointment_id='".$date."'")->row();

        if(count($res)>0){
            return $res->parameter_value;
        }else{
            return "";
        }
    }

    function sendsms($mobilenumbers, $message) {

        /*
        $api_key = '55CEFE04537378';
        $contacts = $mobilenumbers;
        $from = 'OUMDAA';
        $sms_text = urlencode($message); 

        //Submit to server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://smsgateway4.deifysolutions.com/app/smsapi/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$api_key."&campaign=7279&routeid=3&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
        */

        $profile_id = 't5umdaa';
        $api_key = '010km0X150egpk3lD9dQ';
        $sender_id = 'UMDAAO';
        $mobile = $mobilenumbers;
        $sms_text = urlencode($message); 

        //Submit to server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.nimbusit.info/api/pushsms.php?");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=".$profile_id."&key=".$api_key."&sender=".$sender_id."&mobile=".$mobile."&text=".$sms_text);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

    }


    function send_otp($mobilenumber, $otp,$message) {

        $api_key = '55CEFE04537378';
        $contacts = $mobilenumber;
        $from = 'OUMDAA';
        $sms_text = urlencode($message); 

        //Submit to server
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "http://smsgateway4.deifysolutions.com/app/smsapi/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$api_key."&campaign=7279&routeid=3&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

    }
    function getDatesFromRange($start, $end, $format = 'Y-m-d') {

        $array = array();

        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }

        return $array;
    }

//using this for charts by vikram
    function getDatesFromRange1($start, $end, $format = 'd-m-Y') {

        $array = array();

        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }

        return $array;
    }

    function array_flatten($array) { 
        if (!is_array($array)) { 
            return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, array_flatten($value)); 
            } 
            else { 
                $result[$key] = $value; 
            } 
        } 
        return $result; 
    } 

    function accessprofile($entity_id,$field){

        $CI =& get_instance();
        $profile_id = $CI->session->userdata('profile_id');
        $entity_list_id = $CI->db->query("select * from user_entities where method_name ='".$entity_id."'")->row();

        $profile_permessions = $CI->db->query("select * from profile_permissions where user_entity_id ='".$entity_list_id->user_entity_id."' and profile_id ='".$profile_id."' and ".$field." = 1")->row();
        //echo $CI->db->last_query();
        if($profile_permessions){
            return true;
        }else{
            return false;
        }


    }

    function getvalue($name){

        $CI =& get_instance();
        $vital_id = $CI->db->query("select * from patient_vital_sign where vital_sign ='".$name."'")->row();

        if(count($vital_id)>0){
            return $vital_id->vital_result;
        }
        else{
            return false;
        }
    }

    function generate_invoice_no($clinic_id = ''){

        $CI =& get_instance();

        // Get last invoice no w.r.ro the clinic id
        $invRec = $CI->db->select("invoice_no_alias")->from('billing')->where('clinic_id =',$clinic_id)->order_by('billing_id','DESC')->get()->row();
        $last_inv_no = $invRec->invoice_no_alias; 

        if(count($invRec) == 0){
            // Make new invoice no.
            $generatedNo = date('ymd')."1";
        }else{
            // Generate a new invoice no.
            $last_no = trim(substr($last_inv_no,6));
            $current_no = $last_no + 1;
            $generatedNo = date('ymd').$current_no;
        }

        return $generatedNo;
    }

    function checkexpiry(){
        $month = date('n');
        $year = date('Y');
        $IsLeapYear = date('L');
        $NextYear = $year + 1;
        $IsNextYearLeap = date('L', mktime(0, 0, 0, 1, 1, $NextYear));
        $TodaysDate = date('j');
        if (strlen($month+3) < 10)
        {
            $UpdateMonth = "0".($month+3);
        }
        if ($month > 9) {
            if ($month == 10)
            {
                $UpdateMonth = "01";
            }
            else if ($month == 11)
            {
                $UpdateMonth = "02";
            }
            else
            {
                $UpdateMonth = "03";
            }
        }

        if (($month != 10) && ($month != 11) && ($month != 12))
        {
            if(($month&1) && ($TodaysDate != 31))
            {
                $DateAfterThreeMonths = $year."-".$UpdateMonth."-".$TodaysDate;
            }
            else if (($month&1) && ($TodaysDate == 31))
            {
                $DateAfterThreeMonths = $year."-".$UpdateMonth."-30";
            } 
            else {
                $DateAfterThreeMonths = $year."-".$UpdateMonth."-".$TodaysDate;
            }
        }
        else if ($month == 11)
        {
            if (($TodaysDate == 28) || ($TodaysDate == 29) || ($TodaysDate == 30))
            {
                if ($IsLeapYear == 1)
                {
                    $DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-28";
                }
                else if ($IsNextYearLeap == 1)
                {
                    $DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-29";
                }
                else
                {
                    $DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-28";
                }
            }
            else
            {
                $DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-".$TodaysDate;
            }
        }
        else
        {
            $DateAfterThreeMonths = ($year+1)."-".$UpdateMonth."-".$TodaysDate;
        }
        return $DateAfterThreeMonths;
    }

    //Encryption And Decryption
    function DataCrypt( $string, $action ) {

        $CI =& get_instance();
        
        $secret_key = 'Goldfish';
        $secret_iv = 'UMDAA';

        $output = "";
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        if($string == "")
        {
            return null;
        }
        else
        {
            if( $action == 'encrypt' ) {
                $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
            }
            else if( $action == 'decrypt' ){
                $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
            }

            return $output;
        }

        
    }

    function translate($text,$source,$target){

        $apiKey = 'AIzaSyCbqzSzf6y6CBmfaXzwiGj1zLQIjbRVVsA';
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source='.$source.'&target='.$target;
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseDecoded = json_decode($response, true);

        curl_close($handle);
        // echo $responseDecoded['data']['translations'][0]['translatedText'];exit;
        return $responseDecoded['data']['translations'][0]['translatedText'];
    }

    function getreferalDoctorname($doc_id){
        $CI =& get_instance();
        $docInfo = $CI->db->query("select * from referral_doctors where rfd_id='$doc_id'")->row();
        return $docInfo->doctor_name;
    }

    //getProfile Completeness percentage
    function profileCompletion($id){
        $CI =& get_instance();
        $query = "SELECT IF (title IS NULL OR title = '', 1, 0) +  IF (first_name IS NULL OR first_name = '', 1, 0) + IF (gender IS NULL OR gender = '', 1, 0) + IF (age IS NULL OR age = '', 1, 0) + IF (age_unit IS NULL OR age_unit = '', 1, 0) + IF (country IS NULL OR country = '', 1, 0) + IF (email_id IS NULL OR email_id = '', 1, 0) + IF (address_line IS NULL OR address_line = '', 1, 0) + IF (district_id IS NULL OR district_id = '', 1, 0) + IF (state_id IS NULL OR state_id = '', 1, 0) + IF (preferred_language IS NULL OR preferred_language = '', 1, 0) + IF (pincode IS NULL OR pincode = '', 1, 0) as empty_field_count FROM patients where patient_id='$id'";
        $result = $CI->db->query($query)->row();
        $percentage = round(($result->empty_field_count*100)/12);
        return 100-$percentage;
    }
    
    //Generate Access Token
    function generateAccessToken()
    {
        $time = time();
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $randStr = substr(str_shuffle($str),0,15);
        $token = md5(str_shuffle($randStr.$time));
        return $token;        
    }

    //getAccessToken By UserID
    function getAccessToken($id)
    {
        $CI = & get_instance();
        $data = $CI->db->select("secure_accessToken")->from("users")->where("user_id='".$id."'")->get()->row();
        return $data->secure_accessToken;
    }

}

?>