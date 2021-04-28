<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_view extends CI_Controller
{
    
    
    
    public function index()
    {
		$clinic_id = $this->session->userdata('clinic_id');

		$cond = '';
		if($clinic_id!=0)
			$cond = "where clinic_id=".$clinic_id;
        $data['doctors_list'] = $this->db->query("select * from clinic_doctor group by doctor_id")->result();
        
        $new = array();
        foreach ($data['doctors_list'] as $key => $value) {
            $new[] = $value->doctor_id;
        }
        $data['doctor_id'] = implode(",", $new);
        $nr                = array();
        $p_list            = $this->db->query("select *  from patients")->result();
        foreach ($p_list as $key => $value) {
            
            
            $nr[] = array(
                'id' => $value->patient_id,
                'umr' => $value->umr_no,
                'pname' => $value->first_name,
                'mobile' => $value->mobile
            );
            
            
        }
        $data['patients_list'] = $nr;
        
        $data['view'] = 'calendar_view_android';
         $this->load->view('layout', $data);
    
    }
    public function get_events()
    {
        
        if ($this->input->post("did") == "all" && $this->input->post("start") == "" && $this->input->post("mid") != "" && $this->input->post("end") == "") {
            $events = $this->db->query("select * from appointments where clinic_id='" . $this->input->post("cid") . "' and month(appointment_date) ='" . $this->input->post("mid") . "'")->result_array();
        } else if ($this->input->post("did") == "all" && $this->input->post("start") != "" && $this->input->post("mid") != "" && $this->input->post("end") != "") {
            $events = $this->db->query("select * from appointments where clinic_id='" . $this->input->post("cid") . "' and month(appointment_date) ='" . $this->input->post("mid") . "' and (appointment_date >= '" . $this->input->post("start") . "' and appointment_date <= '" . $this->input->post("end") . "')")->result_array();
        } else if ($this->input->post("did") == "all" && $this->input->post("curdate") != "") {
            echo "test";
            exit;
            $events = $this->db->query("select * from appointments where clinic_id='" . $this->input->post("cid") . "' and appointment_date ='" . $this->input->post("curdate") . "'")->result_array();
        }
        
        
        else {
            $events = $this->db->query("select * from appointments where clinic_id='" . $this->input->post("cid") . "' and doctor_id='" . $this->input->post("did") . "' and month(appointment_date) ='" . $this->input->post("mid") . "'")->result_array();
        }
        
        
        $data_events = array();
        
        if (count($events) > 0) {
            foreach ($events as $row) {
                $get_patient = $this->db->query("select * from patients where patient_id='" . $row['patient_id'] . "'")->row();
                $get_doctor  = $this->db->query("select * from doctors where doctor_id='" . $row['doctor_id'] . "'")->row();
                $data[]      = array(
                    'id' => $row["appointment_id"],
                    'title' => "Mr. " . $get_patient->first_name,
                    'start' => $row["appointment_date"] . " " . date('H:i', strtotime($row["appointment_time_slot"])),
                    'slot' => date('h:i A', strtotime($row["appointment_time_slot"])),
                    'backgroundColor' => $get_doctor->color_code,
                    'allDay' => false
                );
            }
        } else {
            $data[] = NULL;
        }
        
        echo json_encode($data);
        
    }
    
    
    public function get_month_count()
    {
        $Cdata = array();
        $split = explode(",", $this->input->post("d_list"));
        foreach ($split as $key => $value) {
            $count = $this->db->query("select count(*) as num_rows from appointments where doctor_id='" . $value . "' and month(appointment_date) ='" . $this->input->post("mid") . "'")->row();
            
            $Cdata[] = array(
                'id' => $value,
                'count' => $count->num_rows
            );
            
            
        }
        
        echo json_encode($Cdata);
        
    }
    public function get_week_count()
    {
        $Cdata = array();
        $split = explode(",", $this->input->post("d_list"));
        foreach ($split as $key => $value) {
            $count = $this->db->query("select count(*) as num_rows from appointments where doctor_id='" . $value . "' and (appointment_date >= '" . $this->input->post("start") . "' and appointment_date <= '" . $this->input->post("end") . "')")->row();
            
            $Cdata[] = array(
                'id' => $value,
                'count' => $count->num_rows
            );
            
            
        }
        
        echo json_encode($Cdata);
        
    }
    public function get_day_count()
    {
        $Cdata = array();
        $split = explode(",", $this->input->post("d_list"));
        foreach ($split as $key => $value) {
            $count = $this->db->query("select count(*) as num_rows from appointments where doctor_id='" . $value . "' and appointment_date = '" . $this->input->post("curdate") . "'")->row();
            
            $Cdata[] = array(
                'id' => $value,
                'count' => $count->num_rows
            );
            
            
        }
        
        echo json_encode($Cdata);
        
    }
    
    public function book_appointment()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $data['clinic_id']  = $clinic_id;
        $data['patient_id'] = $this->input->post('patient_id');
        $data['umr_no'] = $this->input->post('umr_no');
        $data['doctor_id'] = $this->input->post('doctor_name');
        $data['appointment_date'] = date("Y-m-d",strtotime($this->input->post('app_date')));
        $data['appointment_time_slot'] = $this->input->post('time_slot');
        $data['status'] = "booked";
        $data['priority'] = $this->input->post('priority');
        $app_id = $this->Generic_model->insertDataReturnId("appointments",$user_reg);
        $inr=$this->db->query("select count(*) as invoiceno from billing where clinic_id='".$parameters['clinic_id']."'")->row();
                $inv_gen=($inr->invoiceno)+1;
                $receipt_no='RECEIPT-'.$parameters['clinic_id'].'-'.$inv_gen;
                $invoice_no='INV-'.$parameters['clinic_id'].'-'.$inv_gen;
                
                $billing_master['receipt_no']=$receipt_no;
                $billing_master['discount_status']=$parameters['discount_status'];
                $billing_master['invoice_no']=$invoice_no;
                $billing_master['patient_id']=$patient_id;
                $billing_master['clinic_id']=$parameters['clinic_id'];
                $billing_master['umr_no']=$umr_no;
                $billing_master['created_by'] = $user_id;
                $billing_master['created_date_time'] = date('Y-m-d H:i:s');
                $billing_master['modified_by'] = $user_id;
                $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
                $billing_master['billing_type'] =$billing_type;
                $billing_master['payment_mode'] =$parameters['payment_mode'];
                $billing_master['cheque_no'] =$parameters['cheque_no'];
                $billing_master['refference_no'] =$parameters['refference_no'];
                $billing_master['deposit_date'] =$parameters['deposit_date'];
                $billing_master['discount_status'] =$parameters['discount_status'];
                
                $billing_id=$this->Generic_model->insertDataReturnId('billing',$billing_master);
                $billing_line_items=$parameters['billing'];
                if($billing_id !=''){
                for($b=0; $b<count($billing); $b++)
                {
                    $patient_bank['billing_id'] = $billing_id;
                    $patient_bank['item_information'] = $billing_line_items[$b]['item_information'];
                    $patient_bank['quantity'] = $billing_line_items[$b]['quantity'];
                    $patient_bank['discount'] = $billing_line_items[$b]['discount'];
                    $patient_bank['amount'] = $billing_line_items[$b]['amount'];
                    $patient_bank['created_by'] = $user_id;
                    $patient_bank['created_date_time'] = date('Y-m-d H:i:s');
                    $patient_bank['modified_by'] = $user_id;
                    $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');

                    $this->Generic_model->insertData('billing_line_items',$patient_bank);
                }
                }
                
                $clinic_deatails = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$parameters['clinic_id']),$order='');
                $doctor_deatails = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$parameters['doctor_id']),$order='');
                $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_deatails->department_id),$order='');
                $billing = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing_id),$order='');
                $patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$patient_id),$order='');                
                $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');
                
                $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details_state_id),$order='');
                
                $data['clinic_logo']=$clinic_deatails->clinic_logo;
                $data['clinic_phone']=$clinic_deatails->clinic_phone;
                $data['clinic_name']=$clinic_deatails->clinic_name;
                $data['clinic_address']=$clinic_deatails->address;
                $data['doctor_name']="Dr. ".strtoupper($doctor_deatails->first_name." ".$doctor_deatails->last_name);
                $data['qualification']=$doctor_deatails->qualification;
                $data['department_name']=$departments->departmentname;
                $data['patient_name']=ucfirst($patient_details->title).".".strtoupper($patient_details->first_name." ".$patient_details->last_name);
                $data['age']=$patient_details->age.' '.$patient_details->age_unit;
                $data['gender']=$patient_details->gender;
                $data['umr_no']=$umr_no;
                $data['patient_address']=$patient_details->address_line.",".$district_details->district_name.",".$state_details->state_name.",".$patient_details->pincode;
                $data['billing']=$billing;
                $data['invoice_no']=$invoice_no;
                $data['payment_method']=$parameters['mode_of_payment'];
                $data['discount']=$parameters['discount'];
               $html=$this->load->view('billing/generate_billing',$data,true);
                 $pdfFilePath = "billing_".$patient_id.$billing_id.".pdf";
                 $data['file_name'] = $pdfFilePath;

                 $this->load->library('M_pdf');
                  $this->m_pdf->pdf->WriteHTML($html);
                  $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 
                $billFile['invoice_pdf']=$data['file_name'];
                $this->Generic_model->updateData('billing',$billFile,array('billing_id'=>$billing_id));
                $pdf=base_url().'uploads/billings/'.$pdfFilePath;
        
    }
    
    public function get_appointments()
    {
        $check = $this->db->query("select * from appointments a inner join doctors d on(a.doctor_id= d.doctor_id) where a.patient_id='" . $this->input->post("pid") . "' order by a.appointment_date desc")->row();
        
        if (count($check) > 0) {
            echo "<div class='alert alert-warning'><small><div class='text-muted'></div><b>Last Appointment:</b>" . date('d M Y', strtotime($check->appointment_date)) . ", " . date('H:i A', strtotime($check->appointment_time_slot)) . " with DR. " . strtoupper($check->first_name . " " . $check->last_name) . "</small></div>";
        } else {
            echo "";
        }
    }
    
    public function check_slot()
    {
        $clinic_id = $this->session->userdata('clinic_id');
		$cond = '';
		if($clinic_id!=0)
			$cond = "cd.clinic_id=".$clinic_id." and";
        $date         = date('Y-m-d');
        $week_day     = date('N', strtotime($this->input->post('date')));
        $daws         = $this->db->query("select * from clinic_doctor cd inner join clinic_doctor_weekdays cdw on (cd.clinic_doctor_id=cdw.clinic_doctor_id) inner join clinic_doctor_weekday_slots cdws on (cdws.clinic_doctor_weekday_id=cdw.clinic_doctor_weekday_id) inner join doctors d on (cd.doctor_id=d.doctor_id)  where ".$cond." cd.doctor_id='" . $this->input->post('did') . "' and cdw.weekday='" . $week_day . "'")->result();

      
        $booked_slots = array();
        
        if (count($daws) > 0) {
            foreach ($daws as $key => $values) {
                
                $starttime = date("H:i", strtotime($values->from_time)); // your start time
                
                $endtime  = date("H:i", strtotime($values->to_time)); // End time
                $duration = '20'; // split by 30 mins
                
                $array_of_time = array();
                $start_time    = strtotime($starttime); //change to strtotime
                $end_time      = strtotime($endtime); //change to strtotime
                
                $add_mins = $duration * 60;
                
                while ($start_time <= $end_time) // loop between time
                    {
                    $array_of_time[] = date("H:i", $start_time);
                    $start_time += $add_mins; // to check endtime
                }
                $booked_slots[] = $array_of_time;
            }
        } else {
            echo "no";
           
        }
       
        $main_arr = array_flatten($booked_slots);
          // print_r($main_arr);exit;
        
        $bs = $this->db->query("select * from appointments where clinic_id='".$clinic_id."' and doctor_id='".$this->input->post('did')."' and appointment_date='".date('Y-m-d',strtotime($this->input->post('date')))."'")->result();
        $final   = array();
        $b_slots = array();
        foreach ($bs as $bss) {
            $b_slots[] = date('H:i', strtotime($bss->appointment_time_slot));
        }
       
        foreach ($main_arr as $key => $values) {
            if (!in_array($values, $b_slots)) {
                $final[] = $values;
            }
        }
        $slot = "";
        foreach ($final as $key => $slots) {
            $slot .= "<option value='" . $slots . "'>" . $slots . "</option>";
        }
        echo $slot;
    }
}