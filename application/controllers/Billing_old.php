<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends MY_Controller {

	public function __construct() {

		parent::__construct();

		$this->load->library('mail_send', array('mailtype'=>'html'));		 

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

	}

	// Index
	public function index(){
		$clinic_id = $this->session->userdata('clinic_id');
		$cond = '';
		if(count($this->input->post())>0){
			$data['from'] = $this->input->post('date_from');
			$data['to'] = $this->input->post('date_to');

			if($clinic_id!=0)
				$cond = "where b.clinic_id=".$clinic_id."  and DATE(b.created_date_time) >='".date("Y-m-d",strtotime($this->input->post('date_from'))) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($this->input->post('date_to'))) ."'";
			else
				$cond = "where  DATE(b.created_date_time) >='".date("Y-m-d",strtotime($this->input->post('date_from'))) ."' and DATE(b.created_date_time) <='".date("Y-m-d",strtotime($this->input->post('date_to'))) ."'";
		}
		else{
			if($clinic_id!=0)
				$cond = "where b.clinic_id=".$clinic_id." and  b.created_date_time like'".date('Y-m-d')."%'";
			else
				$cond = "where  b.created_date_time like'".date('Y-m-d')."%'";
		}

		$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,b.billing_type,c.clinic_name,sum(bi.amount) as bamount
			FROM `billing` b
			left join billing_line_items bi on b.billing_id=bi.billing_id
			left join patients p on p.patient_id=b.patient_id
			left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
			group by bi.billing_id order by b.billing_id desc")->result();


		$data['view'] = 'billing/billing_clinic';
		$this->load->view('layout', $data);
	}

	// Patient Invoices
	public function patient_invoice($patient_id = NULL, $appointment_id = NULL) {
		
		$clinic_id = $this->session->userdata('clinic_id');
		
		// $cond = '';

		// if($clinic_id != 0)
		// 	$cond = "where b.clinic_id=".$clinic_id." and b.patient_id='".$patient_id."'";
		// else
		// 	$cond = "where b.patient_id='".$patient_id."'";

		// $data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,b.billing_type,c.clinic_name,sum(bi.amount) as bamount
		// 	FROM `billing` b
		// 	left join billing_line_items bi on b.billing_id=bi.billing_id
		// 	left join patients p on p.patient_id=b.patient_id
		// 	left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
		// 	group by bi.billing_id order by b.billing_id desc")->result();

		$this->db->select('B.billing_id, B.invoice_no, B.clinic_id, B.appointment_id, B.doctor_id, B.patient_id, B.discount, B.discount_unit, B.billing_type, B.created_date_time, C.clinic_name, P.title, P.first_name, P.last_name, D.first_name as docFirstName, D.last_name as docLastName, D.doctor_id, sum(BLI.amount) as totalBillAmount');
		$this->db->from('billing B');
		$this->db->join('billing_line_items BLI','B.billing_id = BLI.billing_id','left');
		$this->db->join('patients P', 'B.patient_id = P.patient_id', 'left');
		$this->db->join('doctors D', 'B.doctor_id = D.doctor_id', 'left');
		$this->db->join('clinics C', 'B.clinic_id = C.clinic_id', 'left');

		if($clinic_id != 0)
			$this->db->where('B.clinic_id =', $clinic_id);

		$this->db->where('B.patient_id =', $patient_id);

		if($appointment_id) 
			$this->db->where('B.appointment_id =',$appointment_id);	

		$this->db->where('B.status !=',0);
		$this->db->group_by('BLI.billing_id');
		$this->db->order_by('B.billing_id DESC');

		$data['billing'] = $this->db->get()->result();

		$data['patient_id'] = $patient_id;
		$data['appointment_id'] = $appointment_id;

		$data['view'] = 'profile/patient_invoice';
		$this->load->view('layout', $data);
	}

	// Procedures
	public function procedures($patient_id=''){
		$clinic_id = $this->session->userdata('clinic_id');
		$cond = '';

		if($clinic_id!=0)
			$cond = "clinic_id=".$clinic_id." and ";

		$data['procedures'] = $this->db->query('select * from clinic_procedures where clinic_id="'.$clinic_id.'"')->result();
		if($clinic_id!=0)
			$cond = "where b.clinic_id=".$clinic_id." and b.patient_id='".$patient_id."' and b.billing_type='investigation'";
		else
			$cond = "where b.patient_id='".$patient_id."' and b.billing_type='investigation'";

		$data['app_info'] = $info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' order by a.appointment_date desc")->row();
		$data['patient_info'] = $this->db->query("select * from patients where patient_id='".$patient_id."'")->row();

		$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,b.billing_type,c.clinic_name,sum(bi.amount) as bamount
			FROM `billing` b
			left join billing_line_items bi on b.billing_id=bi.billing_id
			left join patients p on p.patient_id=b.patient_id
			left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
			group by bi.billing_id order by b.billing_id desc")->result();

		$data['patient_id'] = $patient_id;
		$data['view'] = 'profile/patient_procedure';
		$this->load->view('layout', $data);
	}

	// View all orders
	public function view_order($bid)
	{
		$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$bid)->row();
		$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$bid)->result();

		$data['view'] = 'billing/view_order';
		$this->load->view('layout', $data);
	}
}
?>