<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel_reports extends MY_Controller {

 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }

   }
public function index() {
		$clinic_id = $this->session->userdata('clinic_id');

	$data['view'] = 'excel_reports/patient_excel_report';
    $this->load->view('layout', $data);
    }

}