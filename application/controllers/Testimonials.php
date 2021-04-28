<?php



error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');



class Testimonials extends CI_Controller {



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
	$data['testimonials_list']=$this->db->query('select * from testimonials order by testimonial_id ASC')->result();

	$data['view'] = 'testimonials/testimonials_list';

    $this->load->view('layout', $data);

}

public function testimonials_add(){

	$user_id = $this->session->has_userdata('user_id');
	$clinic_id = $this->session->userdata('clinic_id');
	$cond='';
	if($clinic_id!=0)
		$cond = " where clinic_id=".$clinic_id."";
	
	
	
	
	if($this->input->post('submit')){

	$data['title']=$this->input->post('title');

	$data['description']=$this->input->post('description');

	$data['testimonial_given_by']=$this->input->post('testimonial_given_by');

	$data['status']=$this->input->post('status');

 	$this->Generic_model->insertData('testimonials',$data);

    redirect('Testimonials');

	}else{

		$data['view'] = 'testimonials/testimonials_add';

    	$this->load->view('layout', $data);

	}

	

 }

  public function testimonials_update($id){
	  $clinic_id = $this->session->userdata('clinic_id');
	  $user_id = $this->session->has_userdata('user_id');

 	if($this->input->post('submit')){

	$data['title']=$this->input->post('title');

	$data['description']=$this->input->post('description');

	$data['testimonial_given_by']=$this->input->post('testimonial_given_by');

	$data['status']=$this->input->post('status');

 	$this->Generic_model->updateData('testimonials',$data,array('testimonial_id'=>$id));

 	 redirect('Testimonials');

 	}else{

	 	$data['testimonials_list']=$this->db->query('select * from testimonials where testimonial_id='.$id)->row();

	    $data['view'] = 'testimonials/testimonials_edit';

	    $this->load->view('layout', $data);

    }

 }

 public function testimonials_delete($id){

 	$data['status']=0;

  $this->Generic_model->deleteRecord('testimonials',array('testimonial_id'=>$id));

   redirect('Testimonials');

 }



}

