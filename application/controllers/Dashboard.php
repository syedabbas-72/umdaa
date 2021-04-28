<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));         
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        if(!$this->session->has_userdata('is_logged_in'))
        {
            redirect('Authentication/login');
        }    
    }

    public function index(){

        $clinic_id = $this->session->userdata('clinic_id');

        $cond = '';

        if($clinic_id != 0)
            $cond = "where clinic_id=".$clinic_id;

        if($this->session->userdata('role_id') == 4){
            // $data['doctors_list'] = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".."'")->result();
            $data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id,'doctor_id'=>$this->session->userdata('user_id')), $order = '');    
        }else{
            $this->db->select('distinct(doctor_id)');
            $this->db->from('clinic_doctor');
            if($clinic_id != 0)
                $this->db->where("clinic_id = ",$clinic_id);
            $data['doctors_list'] = $this->db->get()->result();
        }

        $data['clinic_id'] = $clinic_id;

        $new = array();

        foreach ($data['doctors_list'] as $key => $value) {
            $new[] = $value->doctor_id;
        }

        $data['doctor_id'] = implode(",", $new);
        $nr = array();
        $p_list = $this->Generic_model->getAllRecords('patients', array('clinic_id' => $clinic_id), $order = ''); 

        foreach ($p_list as $key => $value) {
            $nr[] = array(
                'id' => $value->patient_id,
                'umr' => $value->umr_no,
                'pname' => $value->first_name,
                'mobile' => $value->mobile
            );
        }

        $data['patients_list'] = $nr;

        $tdate = date('Y-m-d');
        $this->db->select('count(patient_id) as pcnt');
        $this->db->from('patients');
        $this->db->like('created_date_time',$tdate);
        $this->db->where('clinic_id',$clinic_id);

        $data['registrations']=$this->db->get()->row();

        $this->db->select('sum(billing_line_items.amount) as camt');
        $this->db->from('billing');
        $this->db->join('billing_line_items', 'billing.billing_id = billing_line_items.billing_id');
        $this->db->where("billing.created_date_time like '%".$tdate."%' and billing.clinic_id='".$clinic_id."' and billing_line_items.item_information='Consultation'");

        $data['consultations'] = $this->db->get()->row();

        $data['procedures'] = $this->db->query('select count(p.patient_procedure_id) as procedureCount from patient_procedure p, appointments a where p.appointment_id=a.appointment_id and p.clinic_id="'.$clinic_id.'"')->row();

        // echo $this->db->last_query();
        // echo "<pre>";print_r($data);echo "</pre>";
        // exit();

        $data['view'] = 'dashboard/dashboard';

        $this->load->view('layout', $data);

    }

    public function getFinances(){

        $clinic_id = $this->session->userdata('clinic_id');
        $start = $_POST['startDate'];
        $end = $_POST['endDate'];
        $d_id = $_POST['d_id'];

        $proAmount='';
        $invAmount='';

        if($d_id=="all")
        {
            if($start==$end)
            {
                $consultations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Consultation' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%'";
                $registrations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Registration' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%'";
                $procedures = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.billing_type='Procedure' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%'";
                $investigations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.billing_type='Investigation' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%'";
            }
            else
            {
                $consultations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Consultation' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%')";
                $registrations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Registration' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%')";
                $procedures = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.billing_type='Procedure' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%')";
                $investigations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.billing_type='Investigation' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%')";
            }
        }
        else
        {
            if($start==$end)
            {
                $consultations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Consultation' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%' and b.doctor_id='".$d_id."'";
                $registrations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Registration' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%' and b.doctor_id='".$d_id."'";
                $procedures = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.doctor_id='".$d_id."' and b.status!=2 and b.status!=3 and b.billing_type='Procedure' and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%'";
                $investigations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.doctor_id='".$d_id."' and b.status!=2 and b.status!=3 and b.billing_type='Investigation' and b.clinic_id='".$clinic_id."' and bl.created_date_time LIKE '".$start."%'";
            }
            else
            {
                $consultations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Consultation' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%') and b.doctor_id='".$d_id."'";
                $registrations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where bl.billing_id=b.billing_id and bl.item_information='Registration' and b.status!=2 and b.status!=3 and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%') and b.doctor_id='".$d_id."'";
                $procedures = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.doctor_id='".$d_id."' and b.status!=2 and b.status!=3 and b.billing_type='Procedure' and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%')";
                $investigations = "select bl.amount,bl.discount,bl.discount_unit from billing_line_items bl,billing b where b.billing_id=bl.billing_id and b.doctor_id='".$d_id."' and b.status!=2 and b.status!=3 and b.billing_type='Investigation' and b.clinic_id='".$clinic_id."' and (bl.created_date_time BETWEEN '".$start."%' AND '".$end."%')";
            }
        }
        
        $conInfo = $this->db->query($consultations)->result();
        // echo $this->db->last_query();
        $regInfo = $this->db->query($registrations)->result();
        $proInfo = $this->db->query($procedures)->result();
        $invInfo = $this->db->query($investigations)->result();
        $regdisc = 0;$condisc = 0;
        $prodisc = 0;$invdisc = 0;
        foreach ($conInfo as $value) {
            if($value->discount!="0")
            {
                if($value->discount_unit=="%")
                {
                    $disc = ($value->amount*$value->discount)/100;
                }   
                elseif($value->discount_unit=="INR")
                {
                    $disc = $value->discount;
                } 
                else
                {
                    $disc = 0;
                }
                $conAmount += $value->amount - $disc;
                $condisc += $disc;
            }
            else
            {
                $conAmount += $value->amount;
                $condisc += 0;
            }
        }
        $disc = 0;
        foreach ($regInfo as $value) {
            if($value->discount!=0)
            {
                if($value->discount_unit=="%")
                {
                    $disc = ($value->amount*$value->discount)/100;
                }   
                elseif($value->discount_unit=="INR")
                {
                    $disc = $value->discount;
                } 
                else
                {
                    $disc = 0;
                }
                $regAmount += $value->amount - $disc;
                $regdisc += $disc;
            }
            else
            {
                $regAmount += $value->amount;
                $regdisc += 0;
            }
        }

        $disc = 0;
        foreach ($proInfo as $value) {
            if($value->discount!=0)
            {
                if($value->discount_unit=="%")
                {
                    $disc = ($value->amount*$value->discount)/100;
                }   
                elseif($value->discount_unit=="INR")
                {
                    $disc = $value->discount;
                } 
                else
                {
                    $disc = 0;
                }
                $proAmount += $value->amount - $disc;
                $prodisc += $disc;
            }
            else
            {
                $proAmount += $value->amount;
                $prodisc += 0;
            }
        }

        $disc = 0;
        foreach ($invInfo as $value) {
            if($value->discount!=0)
            {
                if($value->discount_unit=="%")
                {
                    $disc = ($value->amount*$value->discount)/100;
                }   
                elseif($value->discount_unit=="INR")
                {
                    $disc = $value->discount;
                } 
                else
                {
                    $disc = 0;
                }
                $invAmount += $value->amount - $disc;
                $invdisc += $disc;
            }
            else
            {
                $invAmount += $value->amount;
                $invdisc += 0;
            }
        }
        $conAmount = ($conAmount=="")?'0':$conAmount;
        $regAmount = ($regAmount=="")?'0':$regAmount;
        $proAmount = ($proAmount=="")?'0':$proAmount;
        $invAmount = ($invAmount=="")?'0':$invAmount;
        $discounts = $condisc+$regdisc+$prodisc+$invdisc;

        
        $data['consultationAmount'] = number_format($conAmount,2);
        $data['registrationAmount'] =  number_format($regAmount,2);
        $data['proAmount'] =  number_format($proAmount,2);
        $data['invAmount'] =  number_format($invAmount,2);
        $data['conCount'] = count($conInfo);
        $data['regCount'] = count($regInfo);
        $data['proCount'] = count($proInfo);
        $data['invCount'] = count($invInfo);
        $data['discountAmount'] = number_format($discounts,2);
        echo json_encode($data);
    }

}
