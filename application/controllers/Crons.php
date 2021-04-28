<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Crons extends CI_Controller {

	public function __construct() {
		parent::__construct();
    }

    // Close Appointments which are in consultation even after 12 hours.
    public function CloseAppointments(){
        $presentTime = date("Y-m-d H:i:s");
        $status = ['booked','checked_in','vital_signs','waiting','in_consultation'];
        $stats = "('".implode("', '", $status)."')";
        // echo "select * from appointments where status IN ".$stats." and (check_in_time <= '" . date('Y-m-d H:i:s', strtotime("-12 hours"))."' or check_in_time IS NULL ) and CONCAT(appointment_date,' ',appointment_time_slot) <= '". date('Y-m-d H:i:s', strtotime('-15 hours')) ."' ";
        $appInfo = $this->db->query("select * from appointments where status IN ".$stats." and (check_in_time <= '" . date('Y-m-d H:i:s', strtotime("-12 hours"))."' or check_in_time IS NULL ) and CONCAT(appointment_date,' ',appointment_time_slot) <= '". date('Y-m-d H:i:s', strtotime('-15 hours')) ."' ")->result();
        echo $this->db->last_query();
        // exit;
        foreach($appInfo as $value)
        {
            $data['status'] = "closed";
            $this->Generic_model->updateData("appointments", $data, array('appointment_id'=>$value->appointment_id));           
        }
    }
    
    public function naveen(){
        $check = $this->db->query("select a.doctor_id,pf.patient_form_id as pf_id from patient_form pf,appointments a where a.appointment_id=pf.appointment_id and pf.created_by='0'")->result();
        foreach($check as $value){
            $data['doctor_id'] = $value->doctor_id;
            $data['created_by'] = $value->doctor_id;
            $this->Generic_model->updateData('patient_form', $data, array('patient_form_id'=>$value->pf_id));
            echo "<br>".$this->db->last_query();
        }
    }

    // Register doctor packages with premium package for all doctors
    public function regDoc(){
        $doc = $this->db->query("select * from doctors")->result();
        foreach($doc as $value){
            $clinicInfo = $this->db->query("select * from clinic_doctor where doctor_id='".$value->doctor_id."' order by clinic_doctor_id ASC")->row();
            $data['doctor_id'] = $value->doctor_id;
            $data['clinic_id'] = $clinicInfo->clinic_id;
            $data['package_id'] = "5";
            $data['package_subscription_date'] = date("Y-m-d H:i:s");
            $data['package_validity'] = 1;
            $data['package_price'] = "30000";
            $data['coupon'] = "";
            $data['coupon_discount'] = "";
            $data['status'] = 1;
            $data['created_by'] = $value->doctor_id;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_by'] = $value->doctor_id;
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('doctor_packages', $data);
        }
    }

    // create admins for all clinics
    public function addAdmins(){
        $clinicInfo = $this->Generic_model->getAllRecords('clinics');
        // exit;
        $i = 1;
        ?>
        
        <h4>All Clinics Admin Credentials</h4>
        <?php
        foreach($clinicInfo as $value){
            $admroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =', 'Clinic Head')->get()->row();
            $check = $this->db->query("select * from users where clinic_id='".$value->clinic_id."' and role_id='".$admroleInfo->role_id."'")->row();
            // echo $this->db->last_query();
            // echo "<br>";
            if(count($check) > 0){
                continue;
            }
            $adminPwd = strtoupper(substr(str_replace(" ","",$value->clinic_name), 0, 5))."123";
        
            /* creating admin*/
            // $adminData['username'] = "";
            $adminData['password'] = md5($adminPwd);
            $adminData['clinic_id'] = $value->clinic_id;
            $adminData['user_type'] = 'employee';
            // Get role id for Clinic Head
            $admroleInfo = $this->db->select('role_id')->from('roles')->where('role_name =', 'Clinic Head')->get()->row();
            $adminData['role_id'] = $admroleInfo->role_id;
            // Get profile id for Clinic Head
            $admprofileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =', 'Clinic Head')->get()->row();
            $adminData['profile_id'] = $admprofileInfo->profile_id;
            $adminData['status'] = 1;
            $adminData['created_date_time'] = date('Y-m-d H:i:s');
            $adminData['modified_date_time'] = date('Y-m-d H:i:s');
            $admin_id = $this->Generic_model->insertDataReturnId('users', $adminData);
    
            $empcode = strtoupper(substr($value->clinic_name,0,3)).'-' . date('mY') . $admin_id;
            $adminUname = $empcode;
    
            $emp1['employee_id'] = $admin_id;
            $emp1['employee_code'] = $empcode;
            $emp1['first_name'] = $adminUname;
            $emp1['clinic_id'] = $value->clinic_id;
            $emp1['status'] = 1;
            $emp1['created_date_time'] = date('Y-m-d H:i:s');
            $emp1['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('employees', $emp1);
            $udata['username'] = $empcode;
            $this->Generic_model->updateData('users', $udata, array('user_id' => $admin_id));

            // Clinic Info
            ?>
            <div style="margin: 10px;margin-bottom:30px;width: 100%">
                <p><?=$i?>. <?=$value->clinic_name?></p>
                <p>Username : <?=$empcode?></p>
                <p>Password : <?=$adminPwd?></p>
            </div>
            <?php
            $i++;
        }    
        
        echo "1<br>";
    }

    // Schedule SMS
    public function  scheduleSMS(){
        $tomorrow = date("Y-m-d", strtotime('+24 hours'));
        $appInfo = $this->db->query("select * from appointments where appointment_type='Follow-up' and status='booked' and appointment_date='".$tomorrow."'")->result();
        // Account details
        // $apiKey = urlencode('UVeJillKP5k-DdP0aRMj0OG2B9TLn07IOfVzdC5ohu');
    
        // // Prepare data for POST request
        // $data = array('apikey' => $apiKey);
    
        // // Send the POST request with cURL
        // $ch = curl_init('https://api.textlocal.in/get_templates/');
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($ch);
        // curl_close($ch);
        
        // Process your response here
        // echo $response;

        if(count($appInfo) > 0)
        {
            foreach($appInfo as $value){
                $clinicsInfo = $this->db->query("select * from clinics where clinic_id='".$value->clinic_id."'")->row();
                $patientInfo = getPatientDetails($value->patient_id);
                if($patientInfo->mobile == ""){
                    $mobile = DataCrypt($patientInfo->alternate_mobile, 'decrypt');
                }
                else{
                    $mobile = DataCrypt($patientInfo->mobile, 'decrypt');
                }
                // echo $mobile."<br>";
                $PName = $patientInfo->first_name.$patientInfo->last_name;
                $DName = getDoctorName($value->doctor_id);
                $CName = $clinicsInfo->clinic_name;
                $Date = $value->appointment_date;
                $Time = date('h:i A',strtotime($value->appointment_time_slot));
                $sms = "Dear ".$PName.",%n Your Appointment is fixed with ".$DName." on ".$Date." @ ".$Time.".%nFrom,%n ".$CName;
                // echo "<br>".$sms;
            //    echo sendsms('6302758875', $sms);
                sendsms($mobile, $sms);
            }
        }
    }
     

    public function send(){
        // Account details
        $apiKey = urlencode('UVeJillKP5k-DdP0aRMj0OG2B9TLn07IOfVzdC5ohu');
        
        // Message details
        $numbers = array(916302758875);
        $sender = urlencode('OUMDAA');
        $PName = "Naveen Sriraj";
        $Name = $DName = "Dr. Naveen Sriraj Kamireddy";
        $CName = "NVN Clinics";
        $Date = "Aug 08 2020";
        $Time = "12:00 PM";
        $number = "6302758875";
        $dept = "Cardiology";
        $sms = "Dear ".$PName.",Your Appointment is fixed with ".$DName." on ".$Date." @ ".$Time.".%nFrom,%n".$CName;
        $otpsms = "Dear User,%nYour One Time Password (OTP) is ".rand(1111,9999).". Please enter the OTP to proceed.%nThank You,%nUMDAA Health Care.";
        $welcome = "Dear Dr.NaveenSrirajKamireddy,%nWelcome to UMDAA Family. Your account has been created successfully. You can log into our App with your Reg. Mobile/Email. For further information check you Reg. email account. Please visit this link for further instructions https://tx.gl/r/26DIV/XXXXX/g8buZK3 .";
        $reg = "New Doctor has Registered. Dr. Name: Naveen Sriraj; Mobile No.: 6302758875; Specialty: Cardiology.";
        echo $message = rawurlencode($otpsms);
    
        $numbers = implode(',', $numbers);
            echo "hi";
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
    
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Process your response here
        echo $response;
    }

    // Hits everyday at evening 05:30PM for google review request
    public function googleReviews(){
        $yesterday = date('Y-m-d');
        $time = $yesterday." 15:30:00";
        $check = $this->db->query("select * from google_reviews where status='0' and created_date_time < '".$time."'")->result();
        // echo $this->db->last_query();
        if(count($check) > 0){
            foreach($check as $value){
                $appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id'=>$value->appointment_id));

                $userInfo = getPatientDetails($value->user_id);
                $docInfo = doctorDetails($value->doctor_id);
                $clinicInfo = clinicDetails($appInfo->clinic_id);
                // echo $this->db->last_query();

                $GLink = $docInfo->google_review_link;
                $PName = getPatientName($userInfo->patient_id);
                $DName = getDoctorName($value->doctor_id);
                $CName = $clinicInfo->clinic_name;

                if($userInfo->mobile != ""){
                    $mobile = DataCrypt($userInfo->mobile, 'decrypt');
                }
                else{
                    $mobile = DataCrypt($userInfo->alternate_mobile, 'decrypt');
                }
                
                $gbusiness = "Thanks $PName for consulting with me at $CName.%nIt would be useful for my practice if you can take sometime to post words of appreciation about me in Google Reviews ($GLink).%nThanks in advance%n$DName.";
                // $message = "Thanks Mrs. AYESHA BEGUM for consulting with me at Health inn clinic . It would be useful for my practice if you can take sometime to post  words of appreciation about me in Google Reviews. Thanks in advance Dr Ayesha Khaliq. https://g.page/dr-ayesha-khaliq/review?av";
                if($GLink != "" || $GLink != 0){
                    textlocalSend($mobile, $gbusiness);
                }    
                
                $data['status'] = 1;
                $this->Generic_model->updateData('google_reviews', $data, array('google_review_id'=>$value->google_review_id));
            }
        }
    }

    
}

?>
