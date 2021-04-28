<?php
//error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Immunization extends MY_Controller {
	public function __construct() 
	{
	    parent::__construct();
	}
	public function index(){
		$data['im_info']         = $this->Generic_model->getAllRecords('vaccine', '', $order = '');
		$data['view'] = 'Immunization/Immunization_list';

    	$this->load->view('layout', $data);
	}
	public function Immunization_insert(){
		$param = $this->input->post();

		$user_id = $this->session->has_userdata('user_id');
		$i=1;
		if(count($param)>0){
			$data['vaccine'] = $this->input->post("vaccine");
			$data['parent_vaccine_id'] = $this->input->post("relates_with");
			$data['from_age'] = $this->input->post("from_age");
			$data['to_age'] = $this->input->post("to_age");
			$data['age_unit'] = $this->input->post("unit_of_age");
			if($this->input->post("unit_of_age") == "DAYS"){
				$data['no_of_days'] = $data['from_age'];
			}
			else if($this->input->post("unit_of_age") == "WEEKS"){
				$data['no_of_days'] = $data['from_age'] * 7;
			}
			else if($this->input->post("unit_of_age") == "MONTHS"){
				$data['no_of_days'] = $data['from_age'] * 30;
			}
			else if($this->input->post("unit_of_age") == "YEARS"){
				$data['no_of_days'] = $data['from_age'] * 365;
			}
			
			$data['position'] = $i++;
			$data['status']             = 1;
            $data['created_by']         = $user_id;
            $data['modified_by']        = $user_id;
            $data['created_date_time']  = date('Y-m-d H:i:s');
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData("vaccine",$data);
            redirect("immunization");
		}
		$data['im_info']         = $this->Generic_model->getAllRecords('vaccine', '', $order = '');
		$data['view'] = 'Immunization/Immunization_insert';
    	$this->load->view('layout', $data);
	}

}