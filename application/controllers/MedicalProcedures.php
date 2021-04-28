<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');
class MedicalProcedures extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
    		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    //   $this->load->library('phpqrcode/qrlib');
    }

public function index(){



    //$data['patients']=$this->Generic_model->getAllRecords('patients',$condition='',$order='');
$data['patients']=$this->db->query('select * from medical_procedures order by medical_procedure_id  desc')->result();
	$data['view'] = 'medicalprocedures/mp_list';

    $this->load->view('layout', $data);

}




}

