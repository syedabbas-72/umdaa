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
		$query=$this->db->select('*')->from($table)->where($condition)->get();
		return $query->num_rows();
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

        if($notification_type == 'check_in'){

			$user_fcm_id = $this->db->query("select GROUP_CONCAT(fcm_id) as fcm_id from users where clinic_id='".$clinic_id."'  and role_id='6' and status='1' and fcm_id IS NOT NULL")->row();
           
			$patient  = $this->db->query("Select * from  patients  where patient_id='".$patient_id."' and clinic_id='".$clinic_id."'")->row();
			$doctor  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
			$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
			
			$msg= $patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) has checked in and waiting for your assistance to collect vitals";
		}else if($notification_type == 'push_to_consultant'){
			
			$patient  = $this->db->query("Select * from patients  where patient_id='".$patient_id."' and clinic_id='".$clinic_id."'")->row();
			$user_fcm_id  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
			
			$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
			
			$msg= $patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) is waiting for your consultation ";
		}
		// }else if($notification_type == 'investigation'){
		// 	$user_fcm_id = $this->db->query("select GROUP_CONCAT(fcm_id) as fcm_id from users where clinic_id='".$clinic_id."' and role_id='8' and status='1' and fcm_id IS NOT NULL")->row();
		// 	$patient  = $this->db->query("Select * from users u inner join patients p on (u.user_id=p.patient_id) where p.patient_id='".$patient_id."' and u.clinic_id='".$clinic_id."'")->row();
		// 	$doctor  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
			
		// 	$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
			
		// 	$msg= "Dr.".$doctor->first_name." ".$doctor->last_name." prescribed ".$patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) with a few lab investigations, Please collect suitable samples from ".$patient->first_name;
		// }else if($notification_type == 'pharmacy'){
		// 	$user_fcm_id = $this->db->query("select GROUP_CONCAT(fcm_id) as fcm_id from users where clinic_id='".$clinic_id."' and role_id='7' and status='1' and fcm_id IS NOT NULL")->row();
		// 	$patient  = $this->db->query("Select * from users u inner join patients p on (u.user_id=p.patient_id) where p.patient_id='".$patient_id."' and u.clinic_id='".$clinic_id."'")->row();
		// 	$doctor  = $this->db->query("Select * from users u inner join doctors d on (u.user_id=d.doctor_id) inner join clinic_doctor cd on (d.doctor_id=cd.doctor_id) where d.doctor_id='".$doctor_id."' and cd.clinic_id='".$clinic_id."'")->row();
			
		// 	$clinic = $this->db->query("Select * from clinics where clinic_id='".$clinic_id."'")->row();
			
		// 	$msg= "Dr.".$doctor->first_name." ".$doctor->last_name." prescribed ".$patient->title." ".$patient->first_name." ( #".$patient->umr_no." ) with a few medicines, Please assist ".$patient->first_name." on billing the medicines.";
		// }
		
		
		$registrationIds = array($user_fcm_id->fcm_id);


            $message = array
                (
                'body' => $msg,
                'title' => $clinic->clinic_name,
                'appointment_id' => $appointment_id,
                'patient_id' => $patient_id,
                'sound' => 1,
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
                'Authorization: key=' . API_ACCESS_KEY,
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
			//echo json_encode($fields);
			
            curl_close($ch);
		
    }
	
	
	
}

?>
