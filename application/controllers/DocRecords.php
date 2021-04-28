<?php



error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');



class DocRecords extends CI_Controller {
    public function __construct() {
        parent::__construct();
		$is_logged_in = $this->session->has_userdata('is_logged_in');
        if($is_logged_in == 0){
            redirect('Authentication');
        }
    }
    
    public function index()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        if($clinic_id == "0")
        {
            $data['docname']=$this->db->query("select * from doctors")->result();
        }
        else
        {
            $data['doctors'] = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."'")->result();
        }
        $data['view'] = "docrecords/docList";
        $this->load->view('layout',$data);
    }

    public function getRecords(){
        extract($_POST);
        // print_r($_POST);
        $clinic_id = $this->session->userdata('clinic_id');
        $appInfo = $this->db->query("select * from appointments where clinic_id='".$clinic_id."' and doctor_id='".$doctor."' and DATE(appointment_date) BETWEEN '".$startDate."' and '".$endDate."'")->result();
        // echo $this->db->last_query();
        if($clinic_id == "0")
        {
            $data['docname']=$this->db->query("select * from doctors")->result();
        }
        else
        {
            $data['doctors'] = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."'")->result();
        }
        if(count($appInfo) > 0)
        {
            $data['appInfo'] = $appInfo;
            $data['view'] = 'docrecords/docList';
            $this->load->view('layout', $data);
            
        }
        else{

            $data['view'] = 'docrecords/docList';
            $this->load->view('layout', $data);
        }
    }

    

}
