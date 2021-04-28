<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Clinic_doctor extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
 public function index(){
 	//$data['clinic_doctor'] = $this->db->query('select * from clinics')->result();
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "where cd.clinic_id=".$clinic_id;
  $data['clinic_doctor']=$this->db->query('select * from clinic_doctor cd inner join clinics c on cd.clinic_id=c.clinic_id inner join doctors d on cd.doctor_id=d.doctor_id '.$cond.' group by cd.clinic_id')->result(); 
 $data['view'] = 'clinic_doctor/clinic_doctor_list';
    	$this->load->view('layout', $data);
    	} 

  public function doctor_profile($doctor_id,$clinic_id)
   {
     $data['doctor_info'] =$this->db->query('select doc.*, dep.department_name from doctors doc INNER JOIN department dep ON doc.department_id = dep.department_id where doctor_id ='.$doctor_id)->row();
	 
	  $data['weekdays']=$this->db->query('select * from clinic_doctor_weekdays cd inner join clinic_doctor_weekday_slots cs on(cd.clinic_doctor_weekday_id = cs.clinic_doctor_weekday_id) left join clinic_doctor cdd on cdd.clinic_doctor_id = cd.clinic_doctor_id where cdd.clinic_id = "'.$clinic_id.'" and cdd.doctor_id = "'.$doctor_id.'" group by cd.clinic_doctor_weekday_id,cd.weekday')->result();

	  $data['education_info'] = $this->db->query("select * from doctor_degrees where doctor_id='".$doctor_id."'")->result();

     $data['clinic_info'] = $doctor_id = $this->db->query('select * from clinic_doctor cd inner join clinics c on(cd.clinic_id= c.clinic_id) where cd.clinic_id = "'.$clinic_id.'"and cd.doctor_id = "'.$doctor_id.'"')->row();
		 
	 $data['clinic_doctor_id']=$doctor_id->clinic_doctor_id;

     $data['view'] = 'clinic_doctor/doctor_profile';
   $this->load->view('layout',$data);
   }
public function add_sloat($weekday='',$clinic_doctor_weekday_id=''){
	$data['weekday']=$weekday;
	$data['clinic_doctor_weekday_id']=$clinic_doctor_weekday_id;
	$array_of_time = array();
		//$start_time = strtotime($starttime); //change to strtotime
		//$end_time = strtotime($endtime); //change to strtotime
		$from = "07:00";
		$to = "23:45";
		$start_time = strtotime($from); //change to strtotime
		$end_time = strtotime($to);
	    $duration = '15';
		$add_mins = $duration * 60;



		while ($start_time <= $end_time) // loop between time
		{
		$array_of_time[] = date("H:i", $start_time);
		$start_time += $add_mins; // to check endtime
		}
		$booked_slots[] = $array_of_time;
	
 
	$data['timings'] = $booked_slots;
	$data['view'] = 'clinic_doctor/doctor_add_slot';
   $this->load->view('layout',$data);
}

public function add_week_day($clinic_doctor_id=''){
	
	$data['doctor_weekday']=$this->db->query("SELECT weekday FROM clinic_doctor_weekdays where clinic_doctor_id='".$clinic_doctor_id."'")->result_array();
	$data['clinic_doctor_id']=$clinic_doctor_id;	
	$data['view'] = 'clinic_doctor/doctor_add_week_days';
	$this->load->view('layout',$data);
}


   
public function view($id){
    
    $data['mon'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =1')->result_array();
    $data['tue'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =2')->result_array();
    $data['wed'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =3')->result_array();
    $data['thr'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =4')->result_array();
    $data['fri'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =5')->result_array();
    $data['sat'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =6')->result_array();
    $data['sun'] = $this->db->query('select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id =7')->result_array();
    $data['cal_block'] = $this->db->query('select * from calendar_blocking')->result_array();

 // 	 foreach($result as $row)
	// {
	//  if($row['clinic_doctor_weekday_id'] == 1)
	//  {
	//  $data1['monday'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }
	// else if($row['clinic_doctor_weekday_id'] == 2)
	// {
	// 	$data1['tuesday'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }
	// else if($row['clinic_doctor_weekday_id'] == 3)
	// {
	// 	$data1['wed'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }
	// else if($row['clinic_doctor_weekday_id'] == 4)
	// {
	// 	$data1['thur'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }
	// else if($row['clinic_doctor_weekday_id'] == 5)
	// {
	// 	$data1['fri'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }
	// else if($row['clinic_doctor_weekday_id'] == 6)
	// {
	// 	$data1['sat'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }
	// else if($row['clinic_doctor_weekday_id'] == 7)
	// {
	// 	$data1['sun'] = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H:i A',strtotime($row['from_time'])).' - '.date('H:i A',strtotime($row['from_time'])).'</p>';

	// }


	// }
 

     $data['view'] = 'clinic_doctor/clinic_doctor_calendar';
    $this->load->view('layout', $data);
}
/*
public function clinic_doctor_add(){

	if($this->input->post('submit')){
		print_r($this->input->post('weekdays'));exit();
		$data['doctor_id']=$this->input->post('doctor_id');
		 $data['clinic_id']=$this->input->post('clinic_id');
		$clinic_doctor_id = $this->Generic_model->insertDataReturnId('clinic_doctor',$data);
	}else{
		$data['clinic_list']=$this->Generic_model->getAllRecords('clinics', $condition='', $order='');
		$data['doctor_list']=$this->Generic_model->getAllRecords('doctors', $condition='', $order='');
		$data['view'] = 'clinic_doctor/clinic_doctor_add';
    	$this->load->view('layout', $data);
	}
}*/


public function clinic_doctor_add(){
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';$condition='';
	if($clinic_id!=0){
		$cond = "archieve=0";
		//$condition = "clinic_id=".$clinic_id;
	}
	if($this->input->post('submit')){
		$from_array = array();
		$to_array = array();
		$total_count =  $this->input->post('total');
		$weekdays=$this->input->post('weekdays');
		
		if(count($weekdays)>0)
		{
			$data['doctor_id']=$this->input->post('doctor_id');
			$data['clinic_id']=$this->input->post('clinic_id');
		 	$data['consulting_fee']=$this->input->post('consultation_fee');
			$data['consulting_time']=$this->input->post('consultation_time');
			$data['review_days']=$this->input->post('review_days');
			$clinic_doctor_id = $this->Generic_model->insertDataReturnId('clinic_doctor',$data);
			
			foreach($weekdays as $key=>$value1){
				
				$total_split1 = explode("_",$value1);
			$data2['clinic_doctor_id']=$clinic_doctor_id;
			$data2['weekday'] = $total_split1[0];
			$data2['created_by']=$this->session->userdata('user_id');
			$data2['modified_by']=$this->session->userdata('user_id');
			$data2['created_date_time']=date('Y-m-d H:i:s');
			$data2['modified_date_time']=date('Y-m-d H:i:s');
			 $cwd_id=$this->Generic_model->insertDataReturnId('clinic_doctor_weekdays',$data2);
			
			foreach ($total_count as $key => $value) {
			
			$total_split = explode("_",$value);
			if($total_split[0]==$value1)
			{
			$data1['clinic_doctor_weekday_id']=$cwd_id;
			$data1['from_time']=preg_replace('/\s+/', '', $this->input->post('from_'.$value)[0]);
			$data1['to_time']=preg_replace('/\s+/', '', $this->input->post('to_'.$value)[0]);
			$data1['created_by']=$this->session->userdata('user_id');
			$data1['modified_by']=$this->session->userdata('user_id');
			$data1['created_date_time']=date('Y-m-d H:i:s');
			$data1['modified_date_time']=date('Y-m-d H:i:s');

			$cwd_id2=$this->Generic_model->insertDataReturnId('clinic_doctor_weekday_slots',$data1);
			
			
			}
			
			}

			}
			
	 redirect('clinic_doctor');
			
		}else{
			
		}	 
	
	}else{
		//$data['clinic_list']=$this->Generic_model->getAllRecords('clinics', $condition='', $order='');
    $data['clinic_list']=$this->Generic_model->getAllRecords('clinics',$cond,$order='');
	$data['doctor_list']=$this->Generic_model->getAllRecords('doctors', $condition, $order='');
	$data['view'] = 'clinic_doctor/clinic_doctor_add';
    $this->load->view('layout', $data);
	}
	
 }
 
 
  public function getDoctors_not_in_weekslot() {
        $clinic_id = $_POST['clinic_id'];
        $clinic_doctor = $this->db->query("select * from doctors  a inner join clinic_doctor b on a.doctor_id =b.doctor_id where clinic_id='".$clinic_id."' ")->result();
        $docters_list = '<div class="row col-md-12">
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pincode" class="col-form-label">SELECT DOCTOR</label>';
        $docters_list .= '<select name="doctor_name" id="doctor_name" class="form-control" onchange="show_days()"">
							<option value=""> SELECT DOCTOR </option>';
        foreach ($clinic_doctor as $key => $value) {
            $docters_list .= '<option value="' . $value->doctor_id . '">' . $value->first_name . '</option>';
        }
        $docters_list .= '</select></div>';
        $docters_list .= '</div>
		  <div class="col-md-6" id="select_available_date">
		</div>';
        echo $docters_list;
    }
 
 
 
 
 public function clinic_doctor_add_sloat(){
	
	if($this->input->post('submit')){
		
		$from_array = array();
		$to_array = array();

		$total_count =  $this->input->post('total');	
		foreach ($total_count as $key => $value) {
		$data1['clinic_doctor_weekday_id']=$this->input->post('clinic_doctor_weekday_id');
		$data1['from_time']=preg_replace('/\s+/', '', $this->input->post('from_'.$value)[0]);
		$data1['to_time']=preg_replace('/\s+/', '', $this->input->post('to_'.$value)[0]);
		$data1['created_by']=$this->session->userdata('user_id');
		$data1['modified_by']=$this->session->userdata('user_id');
		$data1['created_date_time']=date('Y-m-d H:i:s');
		$data1['modified_date_time']=date('Y-m-d H:i:s');
		$cwd_id2=$this->Generic_model->insertDataReturnId('clinic_doctor_weekday_slots',$data1);
		}
     $doctor_id = $this->db->query('select * from clinic_doctor cd inner join clinic_doctor_weekdays cw on(cd.clinic_doctor_id = cw.clinic_doctor_id) where clinic_doctor_weekday_id = '.$data1['clinic_doctor_weekday_id'])->row()->doctor_id;
		
			
	 redirect('settings/doctor_timings/'.$doctor_id);
			
		
	
	}
	
 }
 
 
 
 public function clinic_doctor_add_weekday_slot(){
	
	if($this->input->post('submit')){
		$from_array = array();
		$to_array = array();
		$total_count =  $this->input->post('total');
		$weekdays=$this->input->post('weekdays');
		
		
		if(count($weekdays)>0)
		{
						
			foreach($weekdays as $key=>$value1){
				
			$total_split1 = explode("_",$value1);
			$data2['clinic_doctor_id']=$this->input->post('clinic_doctor_id');
			$data2['weekday'] = $total_split1[0];
			$data2['created_by']=$this->session->userdata('user_id');
			$data2['modified_by']=$this->session->userdata('user_id');
			$data2['created_date_time']=date('Y-m-d H:i:s');
			$data2['modified_date_time']=date('Y-m-d H:i:s');
			 $cwd_id=$this->Generic_model->insertDataReturnId('clinic_doctor_weekdays',$data2);
			
			foreach ($total_count as $key => $value) {
			
			$total_split = explode("_",$value);
			if($total_split[0]==$value1)
			{
			$data1['clinic_doctor_weekday_id']=$cwd_id;
			$data1['from_time']=preg_replace('/\s+/', '', $this->input->post('from_'.$value)[0]);
			$data1['to_time']=preg_replace('/\s+/', '', $this->input->post('to_'.$value)[0]);
			$data1['created_by']=$this->session->userdata('user_id');
			$data1['modified_by']=$this->session->userdata('user_id');
			$data1['created_date_time']=date('Y-m-d H:i:s');
			$data1['modified_date_time']=date('Y-m-d H:i:s');
			$cwd_id2=$this->Generic_model->insertDataReturnId('clinic_doctor_weekday_slots',$data1);
			
			
			}
			
			}

			}
			$doctor_id = $this->db->query('select * from clinic_doctor cd inner join clinic_doctor_weekdays cw on(cd.clinic_doctor_id = cw.clinic_doctor_id) where clinic_doctor_weekday_id = '.$data1['clinic_doctor_weekday_id'])->row()->doctor_id;
			
	 redirect('settings/doctor_timings/'.$doctor_id);
			
		} 
	
	}
	
 }
 
 public function clinic_doctor_update($id)
 {
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';$condition='';
	if($clinic_id!=0){
		$cond = "clinic_id=".$clinic_id;		
	}
 	if($this->input->post('submit'))
 	{
 		$data['clinic_id']=$this->input->post('clinic_name');
		$data['doctor_id']=$this->input->post('doctor_name');
		$data['modified_date_time']=date('Y-m-d H:i:s');
 		$this->Generic_model->updateData('clinic_doctor', $data, array('clinic_doctor_id'=>$id));
 		redirect('clinic_doctor');
 	}
 	else
 	{
	 	$data['clinic_doctor_list']=$this->db->query('select * from clinic_doctor where clinic_doctor_id='.$id)->row();
	 	$data['clinic_list']=$this->Generic_model->getAllRecords('clinics', $cond, $order='');
		$data['doctor_list']=$this->Generic_model->getAllRecords('doctors', $cond, $order='');
	    $data['view'] = 'clinic_doctor/clinic_doctor_edit';
	    $this->load->view('layout', $data);
    }
 }

 public function clinic_doctor_delete($id){
 	$info['archive']=1;
 	$this->Generic_model->deleteRecord('clinic_doctor',$info, array('clinic_doctor_id'=>$id));
    redirect('clinic_doctor');

 }
 public function delete()
 {
 	 	$this->db->query('delete from clinic_doctor_weekday_slots where clinic_doctor_weekday_slot_id = '.$this->input->post('id'));
 }

public function delete_week_day_slot($clinic_doctor_weekday_slot_id=''){
	$doctor_res=$this->db->query("SELECT doctor_id,clinic_id FROM clinic_doctor_weekdays a inner join clinic_doctor_weekday_slots b on a.clinic_doctor_weekday_id=b.clinic_doctor_weekday_id inner join clinic_doctor c on a.clinic_doctor_id=c.clinic_doctor_id where b.clinic_doctor_weekday_slot_id='".$clinic_doctor_weekday_slot_id."'")->row();

	$isdeleted=$this->db->query('delete from clinic_doctor_weekday_slots where clinic_doctor_weekday_slot_id = '.$clinic_doctor_weekday_slot_id);
if($isdeleted){

	
redirect('settings/doctor_timings/'.$doctor_res->doctor_id);

}




}

// public function add_slot($weekday='',$clinic_doctor_weekday_id=''){
// 	$data['weekday']=$weekday;
// 	$data['clinic_doctor_weekday_id']=$clinic_doctor_weekday_id;
// 	$array_of_time = array();
// 		//$start_time = strtotime($starttime); //change to strtotime
// 		//$end_time = strtotime($endtime); //change to strtotime
// 		$from = "07:00";
// 		$to = "23:45";
// 		$start_time = strtotime($from); //change to strtotime
// 		$end_time = strtotime($to);
// 	    $duration = '15';
// 		$add_mins = $duration * 60;



// 		while ($start_time <= $end_time) // loop between time
// 		{
// 		$array_of_time[] = date("H:i", $start_time);
// 		$start_time += $add_mins; // to check endtime
// 		}
// 		$booked_slots[] = $array_of_time;
	
 
// 	$data['timings'] = $booked_slots;
// 	$data['view'] = 'clinic_doctor/doctor_add_slot';
//    $this->load->view('layout',$data);
// }

}
?>