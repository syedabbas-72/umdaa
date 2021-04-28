<?php

error_reporting(0);
include "phpqrcode/qrlib.php";

defined('BASEPATH') OR exit('No direct script access allowed');

class Registrations extends MY_Controller {

    public function __construct() {

        parent::__construct();

    }

    /**
    * used to get the list of patients 
    * @name index
    * @access public
    */
    public function index() {
		 $clinic_id = $this->session->userdata('clinic_id');
        $cond = '';
        if($clinic_id!=0)
            $cond = "where clinic_id=".$clinic_id;
        $data['patients'] = $this->db->query('select * from patients '.$cond.' order by patient_id desc')->result();
        //echo $this->db->last_query();exit();
        $data['recent_added'] = $this->db->query('select * from patients where created_date_time between "'.date('Y-m-d H:i:s',strtotime('-7 days')).'" and "'.date('Y-m-d H:i:s').'"')->result();
         $data['recent_visited'] = $this->db->query('select * from appointments a inner join patients p on(a.patient_id = p.patient_id) where a.clinic_id = "'.$clinic_id.'" and check_in_time between "'.date('Y-m-d H:i:s',strtotime('-7 days')).'" and "'.date('Y-m-d H:i:s').'"')->result(); 
        $data['view'] = 'patients/patient_list';
        $this->load->view('layout', $data);
    }

    /**
    * used to get the doctor details  based on the clinic id (ajax call)
    * @name getDoctors
    * @access public
    * @author Vikram
    */

   
    

	
	
}
