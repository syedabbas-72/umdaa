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

		$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,p.mobile,p.umr_no,b.billing_type,c.clinic_name,sum(bi.amount) as bamount
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
		$crudInfo = getcrudInfo('Billing');		
		$start = $_POST['startDate'];
		$end = $_POST['endDate'];
		if($start==$end){
			$cond = "where b.clinic_id=".$clinic_id."  and b.created_date_time LIKE '".$start."%'";
		}
		else{
			$cond = "where b.clinic_id=".$clinic_id."  and (b.created_date_time BETWEEN '".$start."%' and '".$end."%')";			
		}
		$billing = $this->db->query("SELECT b.*,bi.item_information,bi.discount as disc,bi.quantity,bi.unit_price,bi.discount_unit as disc_unit,p.first_name as pname,p.mobile as pmob,p.umr_no,p.title,p.last_name as lname,c.clinic_name,sum(bi.amount) as bamount,sum(bi.total_amount) as tamount
							FROM `billing` b
							left join billing_line_items bi on b.billing_id=bi.billing_id
							left join patients p on p.patient_id=b.patient_id
							left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
							group by bi.billing_id order by b.billing_id desc")->result();
							// echo $this->db->last_query();
		$i=1; 
		// echo count($billing);
	    foreach ($billing as $value) { 
			$billInvoice = $this->db->query("select * from billing_invoice where billing_id='".$value->billing_id."'")->row();
			// $billing_line_items = $this->db->query()->row
	    	$discAmount = 0;
	    	$paid = 0;
	    	if($value->billing_type == "Pharmacy" || $value->billing_type == "Lab")
	    		continue;
	    	if($value->status==2)
			  		continue;
			if($value->disc == 0)
			{
				$discAmount = $value->disc;
			}
			elseif($value->disc != 0)
			{
				if($value->disc_unit == "INR")
				{
					$discAmount = $value->disc;
				}
				elseif($value->disc_unit == "%")
				{
					$discAmount = (($value->bamount*$value->disc)/100);
				}
			}

			if($value->status == 2)
					$payStatus = "Dropped";
			elseif($value->status == 1)
				$payStatus = "Payment Completed";	
				
			if($value->billing_type == "")	
			
			$paid = $value->bamount-$discAmount;
			$docInfo = doctorDetails($value->doctor_id);
    		?> 
		    <tr>
		        <td class="text-center"><?php echo $i;?></td>
		        <td><span><?=$value->invoice_no?></span><br><small><b>D: </b><?=date("d-m-Y",strtotime($value->created_date_time))?></small></td>
		        <td><?php
			    	if($value->patient_id!="")
			    	{
			    		if($value->title == "")
			    		{
			    			$pname = $value->pname." ".$value->lname;
			    		}
			    		else
			    		{
			    			$pname = $value->title.". ".$value->pname." ".$value->lname;
			    		}
			    		echo '<span class="trade_name">'.$pname.'</span><br><span class="formulation m-0">'.$value->umr_no.'</span><br>'.DataCrypt($value->pmob,'decrypt');
			    	}
			    	else
			    	{
			    		echo '<span class="trade_name">'.$value->guest_name.'</span><br>'.$value->guest_mobile;
			    	}
			    	if($value->osa != 0)
			    	{
			    		$status = "<span class='text-center formulation bg-danger'>Pending</span>";
			    	}
			    	if($value->status == 2)
			    	{
			    		$status = "<span class='text-center formulation bg-danger'>Dropped</span>";
			    	}
			    	elseif($value->status == 3)
			    	{
			    		$status = "<span class='text-center formulation bg-warning'>Refunded</span>";
			    	}
			    	?></td> 
		        <td>
					<span class="m-0 trade_name"><?=getDoctorName($value->doctor_id)?></span><br>
					<span class="formulation m-0"><?=$docInfo->department_name?></span>
				</td>
				<td>
				<?php echo $value->billing_type; ?>
				</td>
			    <td class="text-right"><span><?php echo number_format($value->bamount,2); ?></span></td> 
			    <td class="text-right"><span><?php echo number_format($value->osa,2); ?></span></td>  
			    <td class=""><span><?php echo $payStatus; ?></span></td>         
		        <td class="text-center actions" style="padding: 0px !important">
		        	<?php if($value->status != 2 && $value->status != 3){ ?>
			            <a href="<?php echo base_url('billing/view_order/'.$value->billing_id);?>"><i class="fas fa-eye viewSmall"></i></a>&nbsp;
			            <!-- <a href="<?php echo base_url('patients/print_invoice/'.$value->appointment_id.'/'.$value->billing_id);?>" target="blank"><i class="fas fa-print"></i></a> -->

			            <!-- Delete -->	
			            <?php if($crudInfo->p_delete == 1){ ?>
			                <!-- <i class="fas fa-trash-alt deleteSmall" title="Drop Invoice" onclick="return drop_invoice('<?=$value->billing_id?>')"></i> -->
			            <?php } ?>
		            <?php }else{ ?>
		            	<!-- <i class="fa fa-eye"></i>
		            	<i class="fa fa-print"></i>
		            	<i class="fa fa-trash-alt"></i> -->
		            <?php } ?>
		        </td>  
		    </tr>
  		<?php 
  		$i++;
  		}
	}


	/**
    * Used to get the patient's all open appointment/specific appointment complete information based on the Patient id & Appointment id
    * @name getPatientAppointmentInfo
    * @access public
    * @author Uday Kanth Rapalli
    */
    public function getPatientAppointmentInfo($patient_id, $appointment_id = null, $doctor_id) {
		$clinic_id = $this->session->userdata('clinic_id');

        // retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
		$this->db->where('A.patient_id =',$patient_id);
		$this->db->where('A.doctor_id =',$doctor_id);
		$this->db->where('A.clinic_id =',$clinic_id);
        // $this->db->where_not_in('A.status',$status);
        
        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        return $this->db->get()->result();
    }


    // Dropping Invoice
    public function drop_invoice(){

    	$bid = $_POST['bid'];   	
    	$data['status'] = 2;
    	// Update the billing status to 2-drop/cancel
    	$this->Generic_model->updateData('billing', $data, array('billing_id'=>$bid));   	

    	redirect("Billing");

    }


    // Dropping Invoice
    public function refund_invoice(){

    	$bid = $_POST['bid'];   	
    	$data['status'] = 3;
    	// Update the billing status to 2-drop/cancel
    	$this->Generic_model->updateData('billing', $data, array('billing_id'=>$bid));   	

    	redirect("Billing");

    }


	// Patient Invoices
	public function patient_invoice($patient_id = NULL, $appointment_id = NULL) {
		
		$clinic_id = $this->session->userdata('clinic_id');
		$appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id'=>$appointment_id));
		$data['appointmentInfo'] = $this->getPatientAppointmentInfo($patient_id, $appointment_id, $appInfo->doctor_id);

		$billInfo = $this->db->query("select * from billing where appointment_id='".$appointment_id."' and status!='0' and billing_type!='Procedure' order by billing_id DESC")->result();

		$data['billing'] = $billInfo;

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
		$data['billing_invoice'] = $this->db->query("select * from billing_invoice where billing_id='".$bid."'")->result();

		$data['view'] = 'billing/view_order';
		$this->load->view('layout', $data);
	}

	//clear Dues
	public function clearDues(){
		if(isset($_POST))
		{
			$clinic_id = $this->session->userdata('user_id');
			extract($_POST);
			/** 
			* Code Commenting 
			* @dated: 17 Feb 2020 06:40am
			* @desc: Commenting will stop creating a new invoice in billing_invoice table. Will update billing table's existing record's OSA, modified by, modifie_date_time fields information
			
            $Invoice_no_alias = generate_invoice_no($clinic_id);
            $Invoice_no = $clinic_id.$Invoice_no_alias;   
            // Pushing Data into billing_invoice table 
            $billing_invoice['billing_id'] = $billing_id;
            $billing_invoice['invoice_no']=$Invoice_no;
            $billing_invoice['invoice_no_alias']=$Invoice_no_alias;
            $billing_invoice['invoice_date'] = date('Y-m-d');
            $billing_invoice['payment_mode'] = "Cash";
            $billing_invoice['payment_type'] = "OSA";
            $billing_invoice['invoice_amount'] = $osb;
            $billing_invoice['status'] = 1;
            $billing_invoice['created_by'] = $this->session->userdata("user_id");
            $billing_invoice['created_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData("billing_invoice",$billing_invoice);
			*/
			
            $data['osa'] = 0;
            $data['modified_by'] = $_SESSION['user_id'];
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $data['modification_remark'] = "Clearing OSA";
            // Updates existing billing data with '0' OSA, modified_by, modified_date_time
            $this->Generic_model->updateData("billing",$data,array("billing_id"=>$billing_id));
            redirect('Billing/view_order/'.$billing_id);
		}
		else
		{
			redirect('Billing');
		}
	}


	//Print Bill
	public function printBill($bid)
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$data['billing_invoice'] = $this->db->query("select * from billing_invoice where billing_invoice_id=".$bid)->row();
		$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$data['billing_invoice']->billing_id)->row();
		$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$data['billing_invoice']->billing_id)->result();
		$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
		$patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$data['billing_master']->patient_id),$order='');     

        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$clinic_id),$order='');

        $doctor_deatails = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$data['billing_master']->doctor_id),$order='');

        $data['billInfo'] = $this->db->query("select sum(invoice_amount) as invoiceSum from billing_invoice where billing_id='".$data['billing_invoice']->billing_id."'")->row();

        $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_deatails->department_id),$order='');

        $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

        $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

        $data['clinic_logo']=$clinic_deatails->clinic_logo;
        $data['review_days']=$review_deatails->review_days;
        $data['clinic_phone']=$clinic_deatails->clinic_phone;
        $data['clinic_name']=$clinic_deatails->clinic_name;
        $data['address']=$clinic_deatails->address;
        $data['doctor_name']="Dr. ".strtoupper($doctor_deatails->first_name." ".$doctor_deatails->last_name);
        $data['qualification']=$doctor_deatails->qualification;
        $data['department_name']=$departments->department_name;
        $data['patient_name']=strtoupper($patient_details->first_name." ".$patient_details->last_name);
        $data['age_unit']=$patient_details->age_unit;
        $data['age']=$patient_details->age;
        $data['gender']=$patient_details->gender;
        $data['umr_no']=$patient_details->umr_no;
        $data['doctorInfo']=$doctor_deatails;
        $data['patientInfo']=$patient_details;

        if($patient_details->pincode!=="") { 
            $pincode = ",".$patient_details->pincode; 
        } else { 
            $pincode="";
        }

        $data['patient_address']=$patient_details->address_line." ".$pincode;
        $data['billing']=$billing;
        $data['invoice_no']=$data['billing_invoice']->invoice_no;
        $data['invoice_no_alias']=$data['billing_invoice']->invoice_no_alias;
		// echo "<pre>";print_r($data);exit;
		$this->load->library('M_pdf');
		$html = $this->load->view('billing/billPrint',$data,true);
		$pdfFilePath = $data['billing_invoice']->invoice_no.".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 
		redirect('uploads/billings/'.$pdfFilePath);
	}

	// public function printProcedureBill($billing_id)
	// {
	// 	$clinic_id = $this->session->userdata('clinic_id');
	// 	$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$billing_id)->row();
	// 	$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$billing_id)->result();

	// 	$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
		
	// 	$patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$data['billing_master']->patient_id),$order='');     

    //     $clinic_deatails = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$clinic_id),$order='');

	// 	$doctor_deatails = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$data['billing_master']->doctor_id),$order='');
		
	// 	$clinicDocInfo =$this->Generic_model->getSingleRecord('clinic_doctor', array('clinic_id'=>$clinic_id,'doctor_id'=>$data['billing_master']->doctor_id));

    //     // $data['billInfo'] = $this->db->query("select sum(invoice_amount) as invoiceSum from billing_invoice where billing_id='".$data['billing_invoice']->billing_id."'")->row();

    //     $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_deatails->department_id),$order='');

    //     $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

    //     $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

    //     $data['clinic_logo']=$clinic_deatails->clinic_logo;
    //     $data['review_days']=$clinicDocInfo->review_days;
    //     $data['clinic_phone']=$clinic_deatails->clinic_phone;
    //     $data['clinic_name']=$clinic_deatails->clinic_name;
    //     $data['address']=$clinic_deatails->address;
    //     $data['doctor_name']="Dr. ".strtoupper($doctor_deatails->first_name." ".$doctor_deatails->last_name);
    //     $data['qualification']=$doctor_deatails->qualification;
    //     $data['department_name']=$departments->department_name;
    //     $data['patient_name']=strtoupper($patient_details->first_name." ".$patient_details->last_name);
    //     $data['age_unit']=$patient_details->age_unit;
    //     $data['age']=$patient_details->age;
    //     $data['gender']=$patient_details->gender;
    //     $data['umr_no']=$patient_details->umr_no;
    //     $data['doctorInfo']=$doctor_deatails;
    //     $data['patientInfo']=$patient_details;

    //     if($patient_details->pincode!=="") { 
    //         $pincode = ",".$patient_details->pincode; 
    //     } else { 
    //         $pincode="";
    //     }

    //     $data['patient_address']=$patient_details->address_line." ".$pincode;
    //     $data['billing']=$billing;
    //     $data['invoice_no']=$data['billing_master']->invoice_no;
    //     $data['invoice_no_alias']=$data['billing_master']->invoice_no_alias;
    //     $data['total_paid_amount'] = ((float)$data['billing_master']->total_amount) - ((float)$data['billing_master']->osa);
	// 	// echo "<pre>";print_r($data);exit;
	// 	$this->load->library('M_pdf');
	// 	$html = $this->load->view('billing/billPrint',$data,true);
	// 	$pdfFilePath = $data['billing_master']->invoice_no.time().".pdf";
    //     $stylesheet  = '';
    //     $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    //     $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    //     $this->m_pdf->pdf->autoScriptToLang = true;
    //     $this->m_pdf->pdf->autoLangToFont = true;

    //     $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    //     $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    //     $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    //     $this->m_pdf->pdf->defaultheaderline = 0;


    //     $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    //     $this->m_pdf->pdf->WriteHTML($html,2);
    //     $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 
	// 	redirect('uploads/billings/'.$pdfFilePath);
	// }

	public function printProcedureBill($billing_id)
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$billing_id)->row();
		$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$billing_id)->result();

		$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
		
		$patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$data['billing_master']->patient_id),$order='');     

        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$clinic_id),$order='');

		$doctor_deatails = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$data['billing_master']->doctor_id),$order='');
		
		$clinicDocInfo =$this->Generic_model->getSingleRecord('clinic_doctor', array('clinic_id'=>$clinic_id,'doctor_id'=>$data['billing_master']->doctor_id));

        // $data['billInfo'] = $this->db->query("select sum(invoice_amount) as invoiceSum from billing_invoice where billing_id='".$data['billing_invoice']->billing_id."'")->row();

        $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_deatails->department_id),$order='');

        $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

        $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

        $data['clinic_logo']=$clinic_deatails->clinic_logo;
        $data['review_days']=$clinicDocInfo->review_days;
        $data['clinic_phone']=$clinic_deatails->clinic_phone;
        $data['clinic_name']=$clinic_deatails->clinic_name;
        $data['address']=$clinic_deatails->address;
        $data['doctor_name']="Dr. ".strtoupper($doctor_deatails->first_name." ".$doctor_deatails->last_name);
        $data['qualification']=$doctor_deatails->qualification;
        $data['department_name']=$departments->department_name;
        $data['patient_name']=strtoupper($patient_details->first_name." ".$patient_details->last_name);
        $data['age_unit']=$patient_details->age_unit;
        $data['age']=$patient_details->age;
        $data['gender']=$patient_details->gender;
        $data['umr_no']=$patient_details->umr_no;
        $data['doctorInfo']=$doctor_deatails;
        $data['patientInfo']=$patient_details;

        if($patient_details->pincode!=="") { 
            $pincode = ",".$patient_details->pincode; 
        } else { 
            $pincode="";
        }

        $data['patient_address']=$patient_details->address_line." ".$pincode;
        $data['billing']=$billing;
        $data['invoice_no']=$data['billing_master']->invoice_no;
        $data['invoice_no_alias']=$data['billing_master']->invoice_no_alias;
        $data['total_paid_amount'] = ((float)$data['billing_master']->total_amount) - ((float)$data['billing_master']->osa);
		// echo "<pre>";print_r($data);exit;
		$this->load->library('M_pdf');
		$html = $this->load->view('billing/billPrint',$data,true);
		$pdfFilePath = $data['billing_master']->invoice_no.time().".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 
		redirect('uploads/billings/'.$pdfFilePath);
	}

	
	public function printBilling($billing_id)
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$billing_id)->row();
		$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$billing_id)->result();

		$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
		
		$patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$data['billing_master']->patient_id),$order='');     

        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$clinic_id),$order='');

		$doctor_deatails = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$data['billing_master']->doctor_id),$order='');
		
		$clinicDocInfo =$this->Generic_model->getSingleRecord('clinic_doctor', array('clinic_id'=>$clinic_id,'doctor_id'=>$data['billing_master']->doctor_id));

        // $data['billInfo'] = $this->db->query("select sum(invoice_amount) as invoiceSum from billing_invoice where billing_id='".$data['billing_invoice']->billing_id."'")->row();

        $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_deatails->department_id),$order='');

        $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

        $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

        $data['clinic_logo']=$clinic_deatails->clinic_logo;
        $data['review_days']=$clinicDocInfo->review_days;
        $data['clinic_phone']=$clinic_deatails->clinic_phone;
        $data['clinic_name']=$clinic_deatails->clinic_name;
        $data['address']=$clinic_deatails->address;
        $data['doctor_name']="Dr. ".strtoupper($doctor_deatails->first_name." ".$doctor_deatails->last_name);
        $data['qualification']=$doctor_deatails->qualification;
        $data['department_name']=$departments->department_name;
        $data['patient_name']=strtoupper($patient_details->first_name." ".$patient_details->last_name);
        $data['age_unit']=$patient_details->age_unit;
        $data['age']=$patient_details->age;
        $data['gender']=$patient_details->gender;
        $data['umr_no']=$patient_details->umr_no;
        $data['doctorInfo']=$doctor_deatails;
        $data['patientInfo']=$patient_details;

        if($patient_details->pincode!=="") { 
            $pincode = ",".$patient_details->pincode; 
        } else { 
            $pincode="";
        }

        $data['patient_address']=$patient_details->address_line." ".$pincode;
        $data['billing']=$billing;
        $data['invoice_no']=$data['billing_master']->invoice_no;
        $data['invoice_no_alias']=$data['billing_master']->invoice_no_alias;
        $data['total_paid_amount'] = ((float)$data['billing_master']->total_amount) - ((float)$data['billing_master']->osa);
		// echo "<pre>";print_r($data);exit;
		$this->load->library('M_pdf');
		$html = $this->load->view('billing/appointmentBill',$data,true);
		$pdfFilePath = $data['billing_master']->invoice_no.time().".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 
		redirect('uploads/billings/'.$pdfFilePath);
	}

	

}
?>