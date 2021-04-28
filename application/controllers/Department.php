<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends MY_Controller {
public function __construct() 
{
	parent::__construct();
}
public function index(){
    $data['department_list']=$this->db->query("select * from department order by department_name ASC")->result();
	$data['view'] = 'department/department_list';
    $this->load->view('layout', $data);
}
public function department_add(){
    if($this->input->post('submit')){
		$this->load->library('upload',$config);    

		$config['upload_path']="./uploads/departments/";
		$config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';    
		
		if($_FILES['department_icon']['name'] != "")
		{
			$this->upload->initialize($config);
			$this->upload->do_upload('department_icon');
			$data['department_icon'] = $this->upload->data('file_name');
		}

    	$data['department_name']=$this->input->post('department_name');
    	$data['status']=1;
    	$data['created_by']=$this->session->userdata('user_id');
    	$data['modified_by']=$this->session->userdata('user_id');
		$data['created_date_time']=date('Y-m-d H:i:s');
		$data['modified_date_time']=date('Y-m-d H:i:s');
	 	$this->Generic_model->insertData('department',$data);
	 	redirect('department');
	}else{
		$data['view'] = 'department/department_add';
		$this->load->view('layout',$data);
    }
    	
 }
 public function department_update($id){
 	if($this->input->post('submit')){

		$this->load->library('upload',$config);    

		$config['upload_path']="./uploads/departments/";
		$config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';    
		
		if($_FILES['department_icon']['name'] != "")
		{
			$this->upload->initialize($config);
			$this->upload->do_upload('department_icon');
			$data['department_icon'] = $this->upload->data('file_name');
		}

        $data['department_name']=$this->input->post('department_name');
        $data['status']=$this->input->post('status');
    	$data['modified_by']=$this->session->userdata('user_id');
		$data['modified_date_time']=date('Y-m-d H:i:s'); 
		$this->Generic_model->updateData('department', $data, array('department_id'=>$id));
 		redirect('department');
 	}else{
 		$data['department_list']=$this->db->query('select * from department where department_id='.$id)->row();
		$data['view'] = 'department/department_edit';
	    $this->load->view('layout', $data);
	}
  
 }
 public function department_delete($id){
 	$this->Generic_model->deleteRecord('department', array('department_id'=>$id));
    redirect('department');
   
 }
 

}