<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Generic_model extends CI_Model{
	
	public function insertData($table,$data)
	{
		$result=$this->db->insert($table,$data);
		if($result)
 		return true;
		else
		return false;

	}

	public function insertDataReturnId($table,$data)
	{
	
		$this->db->insert($table,$data);
		$insert_id = $this->db->insert_id();
		return  $insert_id;

	}

	public function updateData($table,$data,$condition)
	{

		$this->db->where($condition);
		$result=$this->db->update($table,$data);		

		if($result)
			return true;
		else
			return false;
			
	}

	public function getNumberOfRecords($table,$condition)
	{
		$query=$this->db->select('*')->from($table)->where($condition)->get()->result_array();
		return count($query);
	}

	public function getrandomString($length)
	{
		$string = "abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVQXYZ0123456789";
		$shuffled = str_shuffle($string);
		return substr($shuffled, 0, $length);
	}
	
	public function getAllRecords($table,$condition='',$order='')
	{
		
	    if($condition=='' && $order=='')
		{
			return $this->db->select('*')->from($table)->get()->result();
		}
		else if($condition=='' && $order!='')
		{
			return $this->db->select('*')->from($table)->order_by($order['field'],$order['type'])->get()->result();
		}
		else if($condition!='' && $order=='')
		{
			return $this->db->select('*')->from($table)->where($condition)->get()->result();
		}
		else
		{
			
			return $this->db->select('*')->from($table)->where($condition)->order_by($order['field'],$order['type'])->get()->result();
		}

	}
	public function getgroupbyRecords($table, $condition)
	{
		return $this->db->select('*')->from($table)->group_by($condition)->get()->result();
	}
	
	
	public function deleteRecord($table, $condition)
	{
		$this->db->where($condition);
		$result=$this->db->delete($table);
		if($result)
			return true;
		else
			return false;
	}
	
	
	public function getJoinRecords($table,$jointable,$oncondition,$condition=array(),$type_join="",$select)
	{
		$this->db->select($select);
		$this->db->from($table);
		$this->db->join($jointable,$oncondition,$type_join);
        if(!empty($condition))
		$this->db->where($condition);
	    return $this->db->get()->result();
    }
		
	
	public function getSingleRecord($table,$condition='',$order='')
	{
		
	    if($condition=='' && $order=='')
		{
			return $this->db->select('*')->from($table)->get()->row();
		}
		else if($condition=='' && $order!='')
		{
			return $this->db->select('*')->from($table)->order_by($order['field'],$order['type'])->get()->row();
		}
		else if($condition!='' && $order=='')
		{
			return $this->db->select('*')->from($table)->where($condition)->get()->row();
		}
		else
		{
			return $this->db->select('*')->from($table)->where($condition)->order_by($order['field'],$order['type'])->get()->row();
		}

	}
	public function pushNotifications($patient_id='',$appointment_id='',$doctor_id='',$clinic_id='',$notification_type='',$page='')
     {
		 $type = "";
		// echo "Notification Type : ".$notification_type;

		if($notification_type == 'check_in'){

			// $nurses = $this->db->query("select * from users ")->result();
			$userInfo = $this->db->query("select GROUP_CONCAT(ud.fcm_id) as fcm_id from users_device_info ud,users u where u.user_id=ud.user_id and u.clinic_id='".$clinic_id."'  and u.role_id='6' and ud.platform='Android' and u.status='1' ")->row();
			
			$user_fcm_id = explode(",", $userInfo->fcm_id);
			
			// $userInfo = $this->db->query("Select * from users where user_id='".$value->doctor_id."'")->row();
			// $user_fcm_id[] = $userInfo->fcm_id;
			$patient  = $this->db->query("Select * from  patients  where patient_id='".$patient_id."' and clinic_id='".$clinic_id."'")->row();
			$doctor  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
			$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
			
			$msg= $patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) has checked in and waiting for your assistance to collect vitals";
		}else if($notification_type == 'push_to_consultant'){
			// echo $doctor_id;
			$fcmInfo = $this->db->query("select GROUP_CONCAT(fcm_id) as fcm_id from users_device_info where user_id='".$doctor_id."' and platform='Android'")->row();
			$user_fcm_id = explode(",", $fcmInfo->fcm_id);
			$patient  = $this->db->query("Select * from patients  where patient_id='".$patient_id."' and clinic_id='".$clinic_id."'")->row();
			$userInfo  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
			
			$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
			
			$msg= $patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) is waiting for your consultation ";
		}
		elseif ($notification_type == "EO_Sent_Notification")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "You have new request for Expert Opinion.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Received_Notification")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "You request has been sent for Expert Opinion.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Accept")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			// echo  $this->db->last_query();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "You request has been Accepted.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Close")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			// echo  $this->db->last_query();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "You request has been Closed. View FI in Expert Opinion.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_FI")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "FI written for your Request.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Reject")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "You request has been Rejected.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Cancel")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "You request has been Cancelled.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Comment")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			// echo $this->db->last_query();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Doctor Commented on Expert Opinion.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Message_Sent")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Message sent.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "EO_Wallet_Money_Added")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Money Added in Wallet.";
			$type ="Expert Opinion";
		}
		elseif ($notification_type == "H.E Approval")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Your Article has been approved. It will be published soon.";
			$type = "Articles";
		}
		elseif ($notification_type == "H.E Waiting")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Your Article has been Reviewed. Waiting for your Approval to Publish.";
			$type = "Articles";
		}
		elseif ($notification_type == "H.E Rejection")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Your Article has been rejected. It has been sent to review again.";
			$type = "Articles";
		}
		elseif ($notification_type == "H.E Uploaded")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Your Article has been uploaded.";
			$type = "Articles";
		}
		elseif ($notification_type == "H.E Publish")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			// foreach($fcm_ids as $value){
			// 	$user_fcm_id[] = $value;	
			// }
			
			$msg = "Your Article has been Published.";
			$type = "Articles";
		}
		elseif ($notification_type == "Wallet Request")
		{
			$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Android'")->get()->row();
			$user_fcm_id = explode(",", $doctors->fcm_id);
			$msg = "Wallet Request Sent Successfully.";
			$type = "Wallet";
		}
		elseif ($notification_type == "TelecallNotification") {
			$checkMobileNumber = $this->db->select("*")
			->from("patients_device_info")
			->where("mobile ='" .$patient_id. "'")
			->get()
			->row();
			$user_fcm_id[] = $checkMobileNumber->fcm_id;
			$type = "Call Notification";
			$msg = $checkMobileNumber->mobile;
			'API_ACCESS_KEY=AAAATO6VxOA:APA91bHem94yKBB7SlqMEwq_0gTZ1kkdc4xdPrPc7ZbuWfpHfL09QhQy3vTIVpxLNOuTSqnvlTVRB9a6nHhBjHlpQGS2-3T7uMd807awcyLyaZJ10syNuNN_VelZQ4DU2AVy-iuUbv_Z';
		}


		$registrationIds = $user_fcm_id;


            $message = array
                (
                'body' => $msg,
                'title' => $clinic->clinic_name,
                'appointment_id' => $appointment_id,
                'patient_id' => $patient_id,
				'sound' => 1,
				'type' =>$type,
            //    'largeIcon' => 'large_icon',
            //    'smallIcon' => 'small_icon',
                'screen' =>$page,
                'image' => base_url("uploads/clinic_logos/".$clinic->clinic_logo)
            );

           
            $fields = array
                (
                'registration_ids' => $registrationIds,
                'data' => $message
            );

            $headers = array
                (
				'Authorization: key=AAAAzdyHEZs:APA91bHgcrcPw_oWO5Yd3GUJDO4n01coxdNZezNGwOYkKk9a-vJrgD_i0BOFtCzRBtNplZ4c-cCUTdNjDiCGdXj4VZgxbhhP7TFAnd8Tr0pIDVCpDFQ883m9JLPff9bcauTYCcHckEbp',
				// 'Authorization: key=AAAATO6VxOA:APA91bHem94yKBB7SlqMEwq_0gTZ1kkdc4xdPrPc7ZbuWfpHfL09QhQy3vTIVpxLNOuTSqnvlTVRB9a6nHhBjHlpQGS2-3T7uMd807awcyLyaZJ10syNuNN_VelZQ4DU2AVy-iuUbv_Z',
                'Content-Type: application/json'
                );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
			//  echo "<pre>";
			//  print_r($fields);
			//  echo "</pre>";
			// echo $result;
			// echo json_encode($headers);
            curl_close($ch);
		
	}
	
	public function angularNotifications($patient_id='',$appointment_id='',$doctor_id='',$clinic_id='',$notification_type='',$page='')
	{
		// $type = "";
	// echo "Notification Type : ".$notification_type;

	if($notification_type == 'check_in'){

		// $nurses = $this->db->query("select * from users ")->result();
		$userInfo = $this->db->query("select GROUP_CONCAT(ud.fcm_id) as fcm_id from users_device_info ud,users u where u.user_id=ud.user_id and u.clinic_id='".$clinic_id."'  and u.role_id='6' and ud.platform='Angular' and u.status='1' ")->row();
		
		$user_fcm_id = explode(",", $userInfo->fcm_id);
		
		// $userInfo = $this->db->query("Select * from users where user_id='".$value->doctor_id."'")->row();
		// $user_fcm_id[] = $userInfo->fcm_id;
		$patient  = $this->db->query("Select * from  patients  where patient_id='".$patient_id."' and clinic_id='".$clinic_id."'")->row();
		$doctor  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
		$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
		
		$msg= $patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) has checked in and waiting for your assistance to collect vitals";
		$type="front-office";
	}else if($notification_type == 'push_to_consultant'){
		// echo $doctor_id;
		$fcmInfo = $this->db->query("select GROUP_CONCAT(fcm_id) as fcm_id from users_device_info where user_id='".$doctor_id."' and platform='Angular'")->row();
		$user_fcm_id = explode(",", $fcmInfo->fcm_id);
		$patient  = $this->db->query("Select * from patients  where patient_id='".$patient_id."' and clinic_id='".$clinic_id."'")->row();
		$userInfo  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
		
		$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
		
		$msg= $patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) is waiting for your consultation ";
		$type="front-office";
	}
	elseif ($notification_type == "EO_Sent_Notification")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "You have new request for Expert Opinion.";
		$type ="Expert Opinion Request Sent";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Received_Notification")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "You request has been sent for Expert Opinion.";
		$type ="Expert Opinion Requested Received";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Accept")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		// echo  $this->db->last_query();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "You request has been Accepted.";
		$type ="Expert Opinion Accepted";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Close")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		// echo  $this->db->last_query();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "You request has been Closed. View FI in Expert Opinion.";
		$type ="Expert Opinion Closed";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_FI")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "FI written for your Request.";
		$type ="Expert Opinion FI";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Reject")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "You request has been Rejected.";
		$type ="Expert Opinion Rejected";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Cancel")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "You request has been Cancelled.";
		$type ="Expert Opinion Cancelled";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Comment")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		// echo $this->db->last_query();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Doctor Commented on Expert Opinion.";
		$type ="Expert Opinion Commented";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Message_Sent")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Message sent";
		$type ="Expert Opinion Message Sent";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "EO_Wallet_Money_Added")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Money Added in Wallet.";
		$type ="Expert Opinion Wallet Updated";
		$lang = "Expert Opinion";
	}
	elseif ($notification_type == "H.E Approval")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Your Article has been approved. It will be published soon.";
		$type = "Articles";
	}
	elseif ($notification_type == "H.E Waiting")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Your Article has been Reviewed. Waiting for your Approval to Publish.";
		$type = "Articles";
	}
	elseif ($notification_type == "H.E Rejection")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Your Article has been rejected. It has been sent to review again.";
		$type = "Articles";
	}
	elseif ($notification_type == "H.E Uploaded")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Your Article has been uploaded.";
		$type = "Articles";
	}
	elseif ($notification_type == "H.E Publish")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		// foreach($fcm_ids as $value){
		// 	$user_fcm_id[] = $value;	
		// }
		
		$msg = "Your Article has been Published.";
		$type = "Articles";
	}
	elseif ($notification_type == "Wallet Request")
	{
		$doctors = $this->db->select("GROUP_CONCAT(fcm_id) as fcm_id")->from("users_device_info")->where("user_id='".$doctor_id."' and platform='Angular'")->get()->row();
		$user_fcm_id = explode(",", $doctors->fcm_id);
		$msg = "Wallet Request Sent Successfully.";
		$type = "Wallet";
	}
	// echo $this->db->last_query();

	$fcm = explode(",", $user_fcm_id);
	foreach($user_fcm_id as $value){
		// $registrationIds[] = $value;
		
		$data = array(
			"to" => $value, 
			"notification" => array( 
				"title" => "UMDAA Health Care", 
				"lang" => $lang,
				'success' => 200,
				"body" => $msg,
				"icon" => base_url("uploads/clinic_logos/".$clinic->clinic_logo),
				"click_action" => $type,
				"data" => "https://upload.wikimedia.org/wikipedia/en/thumb/3/34/AlthepalHappyface.svg/256px-AlthepalHappyface.svg.png",
				"actions" =>  array(
					0 => array(
						'title' => 'Like',
						'action' => 'like',
						'icon' => 'icons/heart.png',
					),
					1 => array(
						'title' => 'Unsubscribe',
						'action' => 'unsubscribe',
						'icon' => 'icons/cross.png',
					),
				),
				)
		); 

		$data_string = json_encode($data); 

		$headers = array ( 
			'Authorization: key=AAAAexh6T4M:APA91bEE79cl7h24gnDeSCODmujio-q3ZhZaGWWew2rpM6yJsC0NSPiElFOI0qQB5j7rF1410mqSa_ZrUZuhPxTT1G4mZqDS3QrN_Ksp5iZJ5XfxnSHteJf_JYw9NsCXXF4leszOKn7_', 
			'Content-Type: application/json' 
		); 

		$ch = curl_init(); curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' ); 
		curl_setopt( $ch,CURLOPT_POST, true ); 
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers ); 
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true ); 
		curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string); 
		$result = curl_exec($ch); 
		// curl_close ($ch); 
		
		

		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		// curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		// $result = curl_exec($ch);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// echo $result;
		// echo json_encode($headers);
		curl_close($ch);
		
	}
	
	}
	

	public function pushNotificationsCitizens($patient_id='',$appointment_id='',$doctor_id='',$clinic_id='',$notification_type='',$page='')
	{
		$type = "";
	   // echo "Notification Type : ".$notification_type;
	   if ($notification_type == "TelecallNotification") {
		   $checkMobileNumber = $this->db->select("*")
		   ->from("patients_device_info")
		   ->where("mobile ='" .$patient_id. "'")
		   ->get()
		   ->row();
		   $user_fcm_id[] = $checkMobileNumber->fcm_id;
		   $type = "Call Notification";
		   $msg = $checkMobileNumber->mobile;
	   }

	   $registrationIds = $user_fcm_id;


		   $message = array
			   (
			   'body' => $msg,
			   'title' => $clinic->clinic_name,
			   'appointment_id' => $appointment_id,
			   'patient_id' => $patient_id,
			   'sound' => 1,
			   'type' =>$type,
		   //    'largeIcon' => 'large_icon',
		   //    'smallIcon' => 'small_icon',
			   'screen' =>$page,
			   'image' => base_url("uploads/clinic_logos/".$clinic->clinic_logo)
		   );

		  
		   $fields = array
			   (
			   'registration_ids' => $registrationIds,
			   'data' => $message
		   );

		   $headers = array
			   (
			//    'Authorization: key=' . API_ACCESS_KEY,
			   'Authorization: key=AAAATO6VxOA:APA91bHem94yKBB7SlqMEwq_0gTZ1kkdc4xdPrPc7ZbuWfpHfL09QhQy3vTIVpxLNOuTSqnvlTVRB9a6nHhBjHlpQGS2-3T7uMd807awcyLyaZJ10syNuNN_VelZQ4DU2AVy-iuUbv_Z',
			   'Content-Type: application/json'
			   );

		   $ch = curl_init();
		   curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		   curl_setopt($ch, CURLOPT_POST, true);
		   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
		//    echo json_encode($fields);
		//    echo $result;
		//    echo json_encode($headers);
		   curl_close($ch);
	   
   }
   
	
	/*
	Function getFieldValue will return the value of that particular field
	Uday Kanth Rapalli
	*/
	public function getFieldValue($table, $field, $condition=array()){
		$this->db->select($field);
		$this->db->from($table);
        if(!empty($condition))
		$this->db->where($condition);
	    
	    $result = $this->db->get()->row();

	    if(count($result) > 0){
	    	return $result->$field;
	    }else{
	    	return 0;
	    }
	}
	
}

?>
