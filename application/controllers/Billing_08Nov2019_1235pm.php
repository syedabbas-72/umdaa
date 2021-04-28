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

		$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,p.umr_no,b.billing_type,c.clinic_name,sum(bi.amount) as bamount
			FROM `billing` b
			left join billing_line_items bi on b.billing_id=bi.billing_id
			left join patients p on p.patient_id=b.patient_id
			left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
			group by bi.billing_id order by b.billing_id desc")->result();


		$data['view'] = 'billing/billing_clinic';
		$this->load->view('layout', $data);
	}

	//Get Billings Based On Date
	public function getBillings(){
		$clinic_id = $this->session->userdata('clinic_id');
		$start = $_POST['startDate'];
		$end = $_POST['endDate'];
		if($start==$end){
			$cond = "where b.clinic_id=".$clinic_id."  and b.created_date_time LIKE '".$start."%'";
		}
		else{
			$cond = "where b.clinic_id=".$clinic_id."  and (b.created_date_time BETWEEN '".$start."%' and '".$end."%')";			
		}
		$billing = $this->db->query("SELECT b.*,p.first_name as pname,p.umr_no,b.billing_type,c.clinic_name,sum(bi.amount) as bamount
			FROM `billing` b
			left join billing_line_items bi on b.billing_id=bi.billing_id
			left join patients p on p.patient_id=b.patient_id
			left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
			group by bi.billing_id order by b.billing_id desc")->result();
		$i=1; 
    foreach ($billing as $value) { 
        $discount = $value->discount;
        $discount_unit = $value->discount_unit;
        $total_amount = $value->total_amount;
        $disc = $discount."%";
    ?> 
    <tr>
        <td><?php echo $i++;?></td>
        <td><?php echo date("d-m-Y",strtotime($value->created_date_time));?></td>
        <td><?php echo ucwords($value->pname." ".$value->lname)." [".$value->umr_no."]".'<br>'.$value->mobile; ?></td> 
        <td><?php echo $value->billing_type; ?></td>
        <td><i class="fas fa-rupee-sign"></i> 
        <?php 
        if($discount_unit=="INR")
        {
            echo $total_amount-$discount;
        }
        else
        {
             echo $total_amount-(($disc/100)*$total_amount);
        }
        ?></td>          
        <td>
            <a href="<?php echo base_url('billing/view_order/'.$value->billing_id);?>"><i class="fa fa-eye"></i></a>&nbsp;
            <a href="<?php echo base_url('patients/print_invoice/'.$value->appointment_id.'/'.$value->billing_id);?>"><i class="fas fa-print"></i></a>
        </td>  
    </tr>
  <?php }
	}

	/**
    * Used to get the patient's all open appointment/specific appointment complete information based on the Patient id & Appointment id
    * @name getPatientAppointmentInfo
    * @access public
    * @author Uday Kanth Rapalli
    */
    public function getPatientAppointmentInfo($patient_id, $appointment_id = null) {

        // retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
        $this->db->where('A.patient_id =',$patient_id);
        // $this->db->where_not_in('A.status',$status);
        
        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        return $this->db->get()->result();
    }

	// Patient Invoices
	public function patient_invoice($patient_id = NULL, $appointment_id = NULL) {
		
		$clinic_id = $this->session->userdata('clinic_id');
		$data['appointmentInfo'] = $this->getPatientAppointmentInfo($patient_id, $appointment_id);

		// $this->db->select('B.billing_id, B.invoice_no, B.clinic_id, B.appointment_id, B.doctor_id, B.patient_id, BLI.discount, BLI.discount_unit, B.billing_type, B.created_date_time, C.clinic_name, P.title, P.first_name, P.last_name, D.first_name as docFirstName, D.last_name as docLastName, D.doctor_id, sum(BLI.amount) as totalBillAmount');
		// $this->db->from('billing B');
		// $this->db->join('billing_line_items BLI','B.billing_id = BLI.billing_id','left');
		// $this->db->join('patients P', 'B.patient_id = P.patient_id', 'left');
		// $this->db->join('doctors D', 'B.doctor_id = D.doctor_id', 'left');
		// $this->db->join('clinics C', 'B.clinic_id = C.clinic_id', 'left');

		// if($clinic_id != 0)
		// 	$this->db->where('B.clinic_id =', $clinic_id);

		// $this->db->where('B.patient_id =', $patient_id);

		// if($appointment_id) 
		// 	$this->db->where('B.appointment_id =',$appointment_id);	

		// $this->db->where('B.status !=',0);
		// $this->db->group_by('BLI.billing_id');

		$this->db->select("B.billing_id, B.invoice_no, B.clinic_id, B.appointment_id, B.doctor_id, B.patient_id, B.total_amount as totalBillAmount, B.discount, B.discount_unit, B.billing_type, B.billing_date_time, C.clinic_name, D.first_name as docFirstName, D.last_name as docLastName, D.doctor_id");
		$this->db->from('billing B');
		$this->db->join('doctors D', 'B.doctor_id = D.doctor_id', 'left');
		$this->db->join('clinics C', 'B.clinic_id = C.clinic_id', 'left');

		if($clinic_id != 0)
			$this->db->where('B.clinic_id =', $clinic_id);

		$this->db->where('B.patient_id =', $patient_id);

		if($appointment_id) 
			$this->db->where('B.appointment_id =',$appointment_id);	

		$this->db->where('B.status !=',0);
		$this->db->order_by('B.billing_id DESC');

		$data['billing'] = $this->db->get()->result();


		//echo $this->db->last_query();
		//exit();

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