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
	public function pushNotifications($notification_type,$page,$admin_id,$customer_user_id,$user_id,$product_id,$data="")
	 {
	 	if($notification_type == 'order_insert'){
	 		 $user_details = $this->db->query("select user_name,role_id from users where user_id =".$customer_user_id)->row();
	 		// print_r($user_details );exit;
	 		$condition['role_id'] != 3;
	 		$users = $this->getAllRecords('users',$condition='',$order='');
	 	

	 	foreach ($users as $key => $value) {
	 

	 				
#prep the bundle
     $msg = array
          (
    'body'  => 'Hi '.$value->user_name.' order approved by '.$user_details->user_name,
    'title' => 'Order Indent',
              'icon'  => 'myicon',/*Default Icon*/
                'sound' => 'mySound',
                'page_type' =>$page,
			 			'id' => NULL ,
			 			'role_id'=>$user_details->role_id
          );
  $fields = array
      (
        'to'    => $value->FcmId,
        'notification'  => $msg
      );
  
  $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );
#Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );	
	 }
	}
	else if($notification_type == 'order_approval'){
	 		 $user_details = $this->db->query("select user_name,role_id from users where user_id =".$customer_user_id)->row();
	 		$condition['role_id'] = 2;
	 		$users = $this->getAllRecords('users',$condition='',$order='');
	 	foreach ($users as $key => $value) {
	 

	 				
#prep the bundle
     $msg = array
          (
    'body'  => 'Hi '.$value->user_name.' order approved by '.$user_details->user_name,
    'title' => 'Order Indent',
              'icon'  => 'myicon',/*Default Icon*/
                'sound' => 'mySound',
                'page_type' =>$page,
			 			'id' => NULL ,
			 			'role_id'=>$user_details->role_id
          );
  $fields = array
      (
        'to'    => $value->FcmId,
        'notification'  => $msg
      );
  
  $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );
#Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );

		
	 }
	}

	else if($notification_type == 'payment_approval'){
	 		 $user_details = $this->db->query("select user_name,role_id from users where user_id =".$customer_user_id)->row();
	 		// print_r($user_details );exit;
	 		$condition['role_id'] = 2;
	 		$users = $this->getAllRecords('users',$condition='',$order='');
	 	

	 	foreach ($users as $key => $value) {
	 

	 				
#prep the data
     $msg = array
          (
    'body'  => 'Hi '.$value->user_name.' payment approved by '.$user_details->user_name,
    'title' => 'Payment Collection',
              'icon'  => 'myicon',/*Default Icon*/
                'sound' => 'mySound',
                'page_type' =>$page,
			 			'id' => NULL ,
			 			'role_id'=>$user_details->role_id
          );
  $fields = array
      (
        'to'    => $value->FcmId,
        'notification'  => $msg
      );
  
  $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );
#Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close( $ch );
	
	 }
	}
	else if($notification_type == 'payment_insert'){
	 		 $user_details = $this->db->query("select user_name,role_id from users where user_id =".$customer_user_id)->row();
	 		// print_r($user_details );exit;
	 		$condition['role_id'] != 3;
	 		$users = $this->getAllRecords('users',$condition='',$order='');
	 	

	 	foreach ($users as $key => $value) {
	 

	 				
#prep the data
     $msg = array
          (
    'body'  => 'Hi '.$value->user_name.' payment added by '.$user_details->user_name,
    'title' => 'Payment Collection',
              'icon'  => 'myicon',/*Default Icon*/
                'sound' => 'mySound',
                'page_type' =>$page,
			 			'id' => NULL ,
			 			'role_id'=>$user_details->role_id
          );
  $fields = array
      (
        'to'    => $value->FcmId,
        'notification'  => $msg
      );
  
  $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );
#Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ));
    $result = curl_exec($ch );
    curl_close( $ch );

		
	 }
	}

	else if($notification_type == 'complaint_insert'){
	 		 $user_details = $this->db->query("select user_name,role_id from users where user_id =".$customer_user_id)->row();
	 		// print_r($user_details );exit;
	 		$condition['role_id'] != 3;
	 		$users = $this->getAllRecords('users',$condition='',$order='');
	 	

	 	foreach ($users as $key => $value) {
	 

	 				
#prep the data
     $msg = array
          (
    'body'  => 'Hi '.$value->user_name.' complaint added by '.$user_details->user_name,
    'title' => 'Complaint',
              'icon'  => 'myicon',/*Default Icon*/
                'sound' => 'mySound',
                'page_type' =>$page,
			 			'id' => NULL ,
			 			'role_id'=>$user_details->role_id
          );
  $fields = array
      (
        'to'    => $value->FcmId,
        'notification'  => $msg
      );
  
  $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );
#Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    print_r(json_encode( $fields ));
    exit;
    $result = curl_exec($ch );
    curl_close( $ch );

		
	 }
	}
	else if($notification_type == 'complaint_status'){
	 		 $user_details = $this->db->query("select user_name,role_id from users where user_id =".$customer_user_id)->row();
	 		// print_r($user_details );exit;
	 		$condition['role_id'] != 3;
	 		$users = $this->getAllRecords('users',$condition='',$order='');
	 	

	 	foreach ($users as $key => $value) {
	 

	 				
#prep the data
     $msg = array
          (
    'body'  => 'Hi '.$value->user_name.' complaint closed by '.$user_details->user_name,
    'title' => 'Complaint',
              'icon'  => 'myicon',/*Default Icon*/
                'sound' => 'mySound',
                'page_type' =>$page,
			 			'id' => NULL ,
			 			'role_id'=>$user_details->role_id
          );
  $fields = array
      (
        'to'  => $value->FcmId,
        'notification' => $msg
      );
  
  $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );
#Send Reponse To FireBase Server  
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true);
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );

		
	 }
	}
	 	}
	
}

?>
