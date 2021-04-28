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

                                $xml_data ='<?xml version="1.0"?>

                                <parent>';

                                $nos = explode(",",$mobilenumbers);

                                for($i=0;$i<count($nos);$i++) {

                                                if($nos[$i] != "") {

                                                                $child = '<child>

                                                                <user>KKumar</user>

                                                                <key>0368e03e63XX</key>

                                                                <mobile>+91' .$nos[$i]. '</mobile>

                                                                <message>' .$message. '</message>

                                                                <accusage>1</accusage>

                                                                <senderid>ALERTT</senderid>

                                                                </child>';

                                                                $xml_data = $xml_data.$child;

                                                }

                                }

                                $xml_data = $xml_data ."</parent>";

 

                                $URL = "smsgateway2.deifysolutions.com/submitsms.jsp?";

 

                                $ch = curl_init($URL);

                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                                curl_setopt($ch, CURLOPT_POST, 1);

                                curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');

                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));

                                curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");

                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                $output = curl_exec($ch);

                                curl_close($ch);

                                return $output;

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

}

?>