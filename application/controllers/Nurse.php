<?php

error_reporting(0);
defined('BASEPATH') or exit('No direct script access allowed');

class Nurse extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('second', TRUE);
    }


    public function index()
    {

        $is_logged_in = $this->session->userdata('logged_in')['is_logged_in'];

        if (!isset($is_logged_in)) {
            $this->load->view('nurse/authentication');
        }
    }

    public function login()
    {

        $value = $this->db2->query("select * from clinic_admin where clinic_email='" . $_POST['email'] . "' and clinic_password='" . md5($_POST['password']) . "'")->row();

        $condition = $value->clinic_id;

        if ($condition > 0) {
            $sess_data = array(
                'clinic_id' => $value->clinic_id,
                'badge_id' => $value->badge_id,
                'clinic_name' => $value->Clinic_name,
                'clinic_mobile_number' => $value->clinic_mobile_number,
                'clinic_email' => $value->clinic_email,
                'clinic_area' => $value->clinic_area,
                'clinic_location' => $value->clinic_location,
                'clinic_postal_code' => $value->clinic_postal_code,
                'status' => $value->status,
                'is_logged_in' => TRUE
            );
            $this->session->set_userdata($sess_data);
            redirect('nurse/totalNurse');
        } else {
            $data['msg'] = "User name and Password is not correct";
            $this->load->view('nurse/authentication', $data);
            // redirect('nurse');
        }
    }

    public function totalNurse()
    {

        $clinic_id = $this->session->userdata('clinic_id');
        $cname = $this->db2->query("select * from clinic_admin where clinic_id='" . $clinic_id . "'")->row();
        $nursearray = $this->db2->query("select * from nurse_registration where clinic_id='" . $clinic_id . "'")->result();
        $data['nursearray'] = $nursearray;
        $data['cname'] = $cname;
        // $data['view'] = 'nurse_clinic';
        $data['val'] = "guduri";
        $this->load->view('nurse/totalnurse', $data);
    }



    public function logout()
    {
        $this->session->sess_destroy();
        redirect('nurse');
    }

    public function nurse_creation()
    {

        $this->load->view('nurse/nurse_creation');
    }


    public function nurse_profile_creation()
    {
        
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $mobile = $_POST['nurse_mobile'];
        $password = md5($_POST['nurse_password']);
        // $passwordplain = $_POST['nurse_password'];
        $age = $_POST['nurse_age'];
        $email = $_POST['nurse_email'];
        $gender = $_POST['nurse_gender'];
        $address = $_POST['nurse_address'];
        $clinic_id = $this->session->userdata('clinic_id');
        // $password = md5($passwordplain);
       
        $mobile_result = $this->db2->query("select * from nurse_registration where nurse_phone_number='" . $mobile . "' and clinic_id='".$clinic_id."'")->row();
        $email_result = $this->db2->query("select * from nurse_registration where nurse_email='" . $email . "' and  clinic_id='".$clinic_id."' ")->row();

         
        if(count($mobile_result) > 0 or count($email_result) > 0)
        {
            if(count($mobile_result) > 0){
                       echo 0;
                    }
                    if(count($email_result) > 0){
                        echo 1;
                    }
        }
        else{
            
        $this->db2->query("INSERT INTO nurse_registration (first_name,last_name,nurse_email,nurse_phone_number,nurse_gender,nurse_location,nurse_password,nurse_age,clinic_id) VALUES ('$fname','$lname','$email','$mobile','$gender','$address','$password','$age','$clinic_id')");
        echo 2;
    }
    
// if(count($mobile_result)>0 or count($email_result)>0){
//     if(count($mobile_result)>0){
//         echo 0;
//     }
//     if(count($email_result)>0){
//         echo 1;
//     }
// }else{
//     echo 3;
//     $this->db2->query("INSERT INTO nurse_registration (first_name,last_name,nurse_email,nurse_phone_number,nurse_gender,nurse_location,nurse_password,nurse_age,clinic_id) VALUES ('$fname','$lname','$email','$mobile','$gender','$address','$password','$age','$clinic_id')");

// }

    }

    public function nurse_delete($id)
    {


        $del = $this->db2->query("delete from nurse_registration WHERE nurse_id ='" . $id . "'");

        redirect('nurse/totalNurse');
    }

    public function nurse_edit($id)
    {

        $data['id'] = $id;
        $nursedetails = $this->db2->query("select * from nurse_registration where nurse_id='" . $id . "'")->row();
        $data['nursedetails'] = $nursedetails;
        $this->load->view('nurse/Edit_nurse', $data);
    }


    public function nurse_edit_save($id)
    {

        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $mobile = $_POST['mobile'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $gender = $_POST['gender'];
        $age = $_POST['Age'];
        $address = $_POST['address'];

        $this->db2->query("update nurse_registration set first_name='" . $fname . "',last_name='" . $lname . "',nurse_phone_number='" . $mobile . "',nurse_email='" . $email . "',nurse_password='" . $password . "',nurse_gender='" . $gender . "',nurse_age='" . $age . "',nurse_location='" . $address . "' where nurse_id='" . $id . "'");
        redirect('nurse/totalNurse');
    }


    public function clinic_creation()
    {

        $this->load->view('nurse/clinic_creation');
    }


    public function clinic_profile_creation()
    {
        $email = $_POST['clinic_email'];
        $pas=$_POST['clinic_password'];
        $password = md5($pas);
        $Location = $_POST['clinic_location'];
        $Postal_code = $_POST['clinic_postal'];
        $address = $_POST['clinic_address'];
        $mobile = $_POST['clinic_mobile'];
        $clinicName = $_POST['clinic_name'];
        

        $mobile_result = $this->db2->query("select * from clinic_admin where clinic_mobile_number='" . $mobile . "'")->row();
        $email_result = $this->db2->query("select * from clinic_admin where clinic_email='" . $email . "'")->row();

        if (count($mobile_result) > 0 or count($email_result) > 0) {

            if (count($mobile_result) > 0) {
                echo 0;
                }
                if (count($email_result) > 0) {
                    echo 1;
                }

        } 
        else{
            

            $this->db2->query("INSERT INTO clinic_admin (clinic_name,clinic_mobile_number,clinic_email,clinic_password,clinic_location,clinic_postal_code,clinic_area) VALUES ('$clinicName','$mobile','$email','$password','$Location','$Postal_code','$address')");
            echo 2;
        }
    }

    public function clinic_profile_edit()
    {
        $testid = $this->session->userdata('clinic_id');
        $data['id'] = $testid;
        $clinicdetails = $this->db2->query("select * from clinic_admin where clinic_id='" . $testid . "'")->row();
        $data['clinicdetails'] = $clinicdetails;
        $this->load->view('nurse/Edit_clinic', $data);
    }

    public function clinic_edit_save($id)
    {
        // $val=$_POST;
        // echo "<pre>";
        // print_r($val);
        // echo "</ pre>";
        $ClinicName = $_POST['ClinicName'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $password = $_POST['password'];
        $Location = $_POST['Location'];
        $Postal_code = $_POST['Postal_code'];
        $Clinic_address = $_POST['Clinic_address'];

        $this->db2->query("update clinic_admin set clinic_name='" . $ClinicName . "',clinic_email='" . $email . "',clinic_mobile_number='" . $mobile . "',clinic_password='" . $password . "',clinic_location='" . $Location . "',clinic_postal_code='" . $Postal_code . "',clinic_area='" . $Clinic_address . "' where clinic_id= '" . $id . "'");
        redirect('nurse');
    }

    public function actionstatus($id)
    {
        $t = $this->db2->query("select * from nurse_registration where nurse_id='" . $id . "'")->row();
        $value = $t->status;

        if ($value == "1") {
            $value = "0";
        } else if ($value == "0") {
            $value = "1";
        }

        $this->db2->query("update nurse_registration set status='" . $value . "' where nurse_id='" . $id . "'");
        redirect('nurse/totalNurse');
    }

    public function totalpatients()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $totalpat=$this->db2->query("select * from nurse_patient where clinic_id='".$clinic_id."'")->result();
        $cname = $this->db2->query("select * from clinic_admin where clinic_id='" . $clinic_id . "'")->row();
       $data['totalpat']=$totalpat;
       $data['cname'] = $cname;
       $this->load->view('nurse/totalpatients',$data);
    }
}


// $clinic_id = $this->session->userdata('clinic_id');
// $cname = $this->db2->query("select * from clinic_admin where clinic_id='" . $clinic_id . "'")->row();
// $nursearray = $this->db2->query("select * from nurse_registration where clinic_id='" . $clinic_id . "'")->result();
// $data['nursearray'] = $nursearray;
// $data['cname'] = $cname;
// // $data['view'] = 'nurse_clinic';
// $data['val'] = "guduri";
// $this->load->view('nurse/totalnurse', $data);