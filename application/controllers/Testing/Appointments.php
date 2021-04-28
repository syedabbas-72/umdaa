<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Appointments extends REST_Controller1
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
    }

    //check mobile number is there or not
    public function checkExists_post()
    {
        extract($_POST);
        $mobile = Datacrypt($mobile, 'encrypt');
        $check = $this->db->query("select * from patients where mobile='".$mobile."' or alternate_mobile='".$mobile."'")->result();
        if(count($check)>0)
        {
            $i = 0;
            foreach($check as $value)
            {
                $data['patients'][$i]['patient_id'] = $value->patient_id;
                $data['patients'][$i]['patient_name'] = $value->title.". ".$value->first_name." ".$value->last_name;
                $data['patients'][$i]['age'] = $value->age;
                $data['patients'][$i]['mobile'] = ($value->mobile != "")?DataCrypt($value->mobile, 'decrypt'):DataCrypt($value->alternate_mobile, 'decrypt');
                $data['patients'][$i]['dob'] = $value->dob;
                $data['patients'][$i]['gender'] = $value->gender;
                $data['patients'][$i]['umr_no'] = $value->umr_no;
                $data['patients'][$i]['location'] = $value->location;   
                $i++;             
            }
        }
        else
        {
            $data['patients'] = [];
        }
        $this->response(array('code'=>'200','message'=>'success','result'=>$data));
    }   

    // get Patient Info
    public function getPatientInfo_get($patient_id)
    {
        $patientRec = $this->db->select('patient_id,title,first_name,last_name,umr_no, date_of_birth, age, email_id as email,payment_status,clinic_id,referred_by_type,referred_by,referral_doctor_id,country,occupation,mobile,alternate_mobile,age,gender,location,state_id,district_id,preferred_language, address_line,pincode, photo, qrcode, created_date_time as registration_date, status')
        ->from('patients')
        ->where('patient_id ='.$patient_id)->get()->row(); 
        // echo $this->db->last_query();

        if(count($patientRec) > 0)
        {
            $patientRec->full_name =  ($patientRec->title ? ucfirst($patientRec->title).". " : "").strtoupper(trim($patientRec->first_name)." ".trim($patientRec->last_name));
            $patientRec->date_of_birth = ($patientRec->date_of_birth == '0000-00-00' ? "" : $patientRec->date_of_birth);
            $patientRec->priority = null;
            $patientRec->patient_condition = null;
            $patientRec->appointment_date = null;
            $patientRec->appointment_time = null;
            $patientRec->appointment_type = null;
            $patientRec->condition_months = null;
            $patientRec->appointment_id = null;
            $patientRec->status = $patientRec->status;
            $patientRec->check_in_time = null;
            $patientRec->department = null;
            $patientRec->doctor_comments = null;
            $patientRec->address = $patientRec->address_line;
            $patientRec->pincode = $patientRec->pincode;
            $patientRec->doctor_id = null;
            $patientRec->doctor_name = null;
            $patientRec->department_id = null;
            $patientRec->color_code = null;
            $patientRec->email = DataCrypt($patientRec->email, 'decrypt');
            $patientRec->mobile = DataCrypt($patientRec->mobile, 'decrypt');
            $patientRec->alternate_mobile = DataCrypt($patientRec->alternate_mobile, 'decrypt');
            $patientRec->photo = ($patientRec->photo ? base_url() . 'uploads/patients/'.$patientRec->photo : null);
            $patientRec->qrcode =  base_url() . 'uploads/qrcodes/patients/'.$patientRec->qrcode;
            $patientRec->registration_date = date('Y-m-d', strtotime($patientRec->registration_date));
            $patientRec->qb_user_id = null;
            $patientRec->qb_user_login = null;
            $patientRec->qb_user_fullname = null;
            $patientRec->qb_user_tag = null;
            $patientRec->immunization_status = 0;
            $this->response(array('code'=>'200','message'=>'Success','result'=>$patientRec));
        }
        else
        {
            $data = "Patient Not Exists";
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));
        }
    }

}
?>