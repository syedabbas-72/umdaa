<?php



error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');



class Users extends CI_Controller {



    public function __construct() {



        parent::__construct();

		

		$is_logged_in = $this->session->has_userdata('is_logged_in');



        if($is_logged_in == 0){

            redirect('Authentication');

        }

        

    }

	

public function index(){
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "U.clinic_id=".$clinic_id." and";
	$data['users_list']=$this->db->query('select U.*, C.clinic_name, R.role_name, P.profile_name from users U LEFT JOIN roles R on U.role_id = R.role_id LEFT JOIN profiles P on U.profile_id = P.profile_id LEFT JOIN clinics C ON U.clinic_id = C.clinic_id where '.$cond.' U.status=1 order by clinic_id ASC')->result();

	$data['view'] = 'users/users_list';

    $this->load->view('layout', $data);

}

public function users_add(){

	$user_id = $this->session->has_userdata('user_id');
	$clinic_id = $this->session->userdata('clinic_id');
	$cond='';
	if($clinic_id!=0)
		$cond = " where clinic_id=".$clinic_id."";
	
	
	
	
	if($this->input->post('submit')){

	$data['username']=$this->input->post('username');

	$data['password']=md5($this->input->post('password'));

	$data['email_id']=$this->input->post('email_id');
	$data['clinic_id']=$clinic_id;

	$data['user_type']=$this->input->post('user_type');

	$data['role_id']=$this->input->post('role_id');

	$data['profile_id']=$this->input->post('profile_id');

	$data['status']='1';

	$data['reports_to']=$this->input->post('reports_to');

	$data['created_by'] = $user_id;

	$data['modified_by'] = $user_id;

	$data['created_date_time'] = date('Y-m-d H:i:s');

	$data['modified_date_time'] = date('Y-m-d H:i:s');

 	$this->Generic_model->insertData('users',$data);

    redirect('Users');

	}else{

		echo $cond;

		$data['roles']=$this->db->query('select * from roles')->result();

		$data['profile']=$this->db->query('select * from profiles where clinic_id='.$clinic_id.'')->result();

		$data['reports_users']=$this->db->query('select user_id,username,status from users where status=1 and clinic_id='.$clinic_id.'')->result();

		$data['view'] = 'users/users_add';

    	$this->load->view('layout', $data);

	}

	

 }

  public function user_update($id){
	  $clinic_id = $this->session->userdata('clinic_id');
	  $user_id = $this->session->has_userdata('user_id');

 	if($this->input->post('submit')){

 	$data['username']=$this->input->post('username');

	//$data['password']=md5($this->input->post('password'));

	$data['email_id']=$this->input->post('email_id');

	$data['user_type']=$this->input->post('user_type');

	$data['role_id']=$this->input->post('role_id');

	$data['profile_id']=$this->input->post('profile_id');

	$data['status']=$this->input->post('status');

	$data['reports_to']=$this->input->post('reports_to');

	$data['created_by'] = $user_id;

	$data['modified_by'] = $user_id;

	$data['created_date_time'] = date('Y-m-d H:i:s');

	$data['modified_date_time'] = date('Y-m-d H:i:s');

 	$this->Generic_model->updateData('users',$data,array('user_id'=>$id));

 	 redirect('Users');

 	}else{

 		$data['roles']=$this->db->query('select * from roles')->result();

		$data['profile']=$this->db->query('select * from profiles where clinic_id='.$clinic_id.'')->result();

	 	$data['usetrs_list']=$this->db->query('select * from users where user_id='.$id)->row();

	 	$data['reports_users']=$this->db->query('select user_id,username,status from users where status=1 and clinic_id='.$clinic_id.'')->result();

	    $data['view'] = 'users/users_edit';

	    $this->load->view('layout', $data);

    }

 }

 public function users_delete($id){

 	$data['status']=0;

  $this->Generic_model->updateData('users',$data,array('user_id'=>$id));

   redirect('Users');

 }



}

