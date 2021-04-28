<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '1024M');
//library for generating code
include "phpqrcode/qrlib.php";
error_reporting(0);
class Calendar_view extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        if (!$this->session->has_userdata('is_logged_in')) {
            redirect('Authentication/login');
        }
    }


    public function index()
    {

        $clinic_id = $this->session->userdata('clinic_id');
        $cond = '';
        if ($clinic_id != 0){
            $cond = "clinic_id='" . $clinic_id . "'";

            $data['doctors']      = $this->db->select("*")->from("referral_doctors")->where($cond)->get()->result_array();
            $data['doctors_list'] = $this->db->select("*")->from("clinic_doctor")->where($cond)->group_by("doctor_id")->get()->result();
            $data['procedures']   = $this->db->select("*")->from("clinic_procedures")->where($cond)->get()->result();
        }else{
            $data['doctors']      = $this->db->select("*")->from("referral_doctors")->get()->result_array();
            $data['doctors_list'] = $this->db->select("*")->from("clinic_doctor")->group_by("doctor_id")->get()->result();
            $data['procedures']   = $this->db->select("*")->from("clinic_procedures")->get()->result();
        }

        $data['clinic_id']    = $clinic_id;

        $new = array();

        foreach ($data['doctors_list'] as $key => $value) {
            $new[] = $value->doctor_id;
        }

        $data['doctor_id'] = implode(",", $new);
        $nr                = array();
        $p_list            = $this->Generic_model->getAllRecords("patients", $condition = '', $order = '');
        
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


    // Block days with respect to doctor
    public function block_calendar()
    {
        $clinic_id   = $this->session->userdata('clinic_id');
        $split       = explode("-", $this->input->post("daterange"));
        $spl1        = explode("/", $split[0]);
        $spl2        = explode("/", $split[1]);
        $start       = $spl1[2]."-".$spl1[1]."-".$spl1[0];
        $end         = $spl2[2]."-".$spl2[1]."-".$spl2[0];
        $dates       = $start." ".$end;
        
        // $results[]   = getDatesFromRange(trim($spli[0]), trim($spli[1]));
        // $myArray     = array_map("unserialize", array_unique(array_map("serialize", $results)));
        // $block_dates = array_flatten($myArray);
        //$block_dates = implode(",",$main_arr);

        $user_id = $this->session->has_userdata('user_id');
        // if (count($block_dates) > 0) {
        //     $i = 0;
        //     foreach ($block_dates as $key => $value) {
        //         $i++;
        //     }
        // }

        $data['doctor_id']          = $this->input->post("block_doctor");
        $data['remark']             = $this->input->post("remark");
        $data['clinic_id']          = $clinic_id;
        $data['dates']              = $this->input->post("daterange");
        $data['status']             = 1;
        $data['created_by']         = $user_id;
        $data['modified_by']        = $user_id;
        $data['created_date_time']  = date('Y-m-d H:i:s');
        $data['modified_date_time'] = date('Y-m-d H:i:s');

        $this->Generic_model->insertData('calendar_blocking', $data);
        redirect("calendar_view");
    }

    // Loading events for calendar in ajax call
    public function get_events()
    {

        if ($this->input->post("did") == "all" && $this->input->post("start") == "" && $this->input->post("mid") != "" && $this->input->post("end") == "") {

            $events = $this->db->select("*")->from("appointments")->where("clinic_id='" . $this->input->post("cid") . "' and month(appointment_date) ='" . $this->input->post("mid") . "' and status !='drop' and status != 'reschedule' and patient_id!=0")->get()->result_array();

        } else if ($this->input->post("did") == "all" && $this->input->post("start") != "" && $this->input->post("mid") != "" && $this->input->post("end") != "") {
            $events = $this->db->select("*")->from("appointments")->where("clinic_id='" . $this->input->post("cid") . "' and month(appointment_date) ='" . $this->input->post("mid") . "' and (appointment_date >= '" . $this->input->post("start") . "' and appointment_date <= '" . $this->input->post("end") . "') and status != 'drop' and status != 'reschedule' and patient_id!=0")->get()->result_array();
        } else if ($this->input->post("did") == "all" && $this->input->post("curdate") != "") {
            $events = $this->db->select("*")->from("appointments")->where("clinic_id='" . $this->input->post("cid") . "' and appointment_date ='" . $this->input->post("curdate") . "' and status !='drop' and status != 'reschedule' and patient_id!=0")->get()->result_array();
        } else {
            $events = $this->db->select("*")->from("appointments")->where("clinic_id='" . $this->input->post("cid") . "' and doctor_id='" . $this->input->post("did") . "' and month(appointment_date) ='" . $this->input->post("mid") . "' and status !='drop' and status != 'reschedule' and patient_id!=0")->get()->result_array();
        }
        
        $data = array();

        if ($events) {
            foreach ($events as $row) {
                $get_patient = $this->db->select("*")->from("patients")->where("patient_id='" . $row['patient_id'] . "'")->get()->row();
                $get_billing = $this->db->select("*")->from("billing b")->join("billing_line_items bl","b.billing_id = bl.billing_id")->where("(bl.item_information='Registration' or bl.item_information='Consultation') and b.appointment_id='" . $row["appointment_id"] . "' ")->get()->row();
                $get_doctor  = $this->db->select("*")->from("doctors")->where("doctor_id='" . $row['doctor_id'] . "'")->get()->row();
                if ($row['check_in_time'] !== "NULL" || $row['check_in_time'] !== "NULL") {
                    $checkin_time = "";
                } else {
                    $checkin_time = date('M d, Y H:i:s', strtotime($row['check_in_time']));
                }

                $data[] = array(
                    'id' => $row["appointment_id"],
                    'app_status' => $row["payment_status"],
                    'app_invoice' => $row['invoice_pdf'],
                    'app_priority' => $row['priority'],
                    'eventOverlap' => false,
                    'checked_in' => $row["check_in_time"],
                    'app_date' => date("d M Y", strtotime($row["appointment_date"])),
                    'billing_id' => $get_billing->billing_id,
                    'app_doctor_id' => $row["doctor_id"],
                    'umr_no' => $row["umr_no"],
                    'mobile' => $get_patient->mobile,
                    'booking_type' => $row["booking_type"],
                    'app_patient_id' => $row["patient_id"],
                    'app_doctor' => "Dr. " . $get_doctor->first_name . " " . $get_doctor->last_name,
                    'astatus' => $row["status"],
                    'patient_status' => $get_patient->payment_status,
                    'title' => $get_patient->first_name." ".$get_patient->last_name,
                    'start' => $row["appointment_date"] . " " . date('H:i', strtotime($row["appointment_time_slot"])),
                    'end' => $row["appointment_date"] . " " . date('H:i', strtotime($row["consultation_end_time"])),
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


    public function get_doctor_slots()
    {
        $did   = $_POST['did'];
        $time  = $_POST['time_slot'];
        $sdate = $_POST['date'];
        $cond  = $this->db->select("count(*) as cnt")->from("appointments")->where("doctor_id=" . $did . " and appointment_date='" . $sdate . "' and appointment_time_slot='" . $time . "' and status!='drop' and status!='reschedule'")->get()->row();
        // echo $this->db->last_query();
        echo $cond->cnt;
    }

    // Checking slot availibility
    public function get_available_slots()
    {

        $time  = $_POST['time_slot'];
        $sdate = $_POST['date'];
        $cond  = $this->db->select("count(*) as cnt")->from("appointments")->where("appointment_date='" . $sdate . "' and appointment_time_slot='" . $time . "' and status!='drop' and status!='reschedule'")->get()->row();
    // echo $this->db->last_query();
        echo $cond->cnt;
    }

    // Updating appointment status to checked_in
    public function check_in()
    {
        $appointment_id            = $this->input->post("appid");
        $a_status['status']        = "checked_in";
        $a_status['check_in_time'] = date("Y-m-d H:i:s");
        $this->Generic_model->updateData("appointments", $a_status, array(
            'appointment_id' => $appointment_id
        ));
        $ap_details = $this->Generic_model->getSingleRecord('appointments', array(
            'appointment_id' => $appointment_id
        ), '');

        $check_nurse_status = $this->db->select("count(*) as num_rows,status")->from("users")->where("role_id='6' and clinic_id ='" . $ap_details->clinic_id . "'")->get();
        $count              = $check_nurse_status->num_rows();
        $res                = $check_nurse_status->row();
        if ($count == 0) {
            $new_status['status'] = "waiting";
            $this->Generic_model->updateData("appointments", $new_status, array(
                'appointment_id' => $appointment_id
            ), '');
            $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');
        } else {
            if ($res->status == 1) {
                $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'check_in', 'dashboard');
            } else {
                $new_status['status'] = "waiting";
                $this->Generic_model->updateData("appointments", $new_status, array(
                    'appointment_id' => $appointment_id
                ), '');
                $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');
            }
        }
    }


    // Updating appointment status to checked_in
    public function patient_check_in($patient_id = NULL, $appointment_id = NULL)
    {

        // echo "Patient id: ".$patient_id.", Appointment id: ".$appointment_id."<br>";

        // Check whether the appointment that a user is trying to check in is today's date or a future's date
        // Get Appointment details
        $this->db->select('A.patient_id, A.appointment_id, A.doctor_id, A.clinic_id, A.status as appointmentStatus, A.appointment_date, A.appointment_time_slot, P.title, P.first_name,P.last_name, D.first_name as docFirstName, D.last_name as docLastName');
        $this->db->from('appointments A');
        $this->db->join('patients P','A.patient_id = P.patient_id');
        $this->db->join('doctors D','A.doctor_id = D.doctor_id');
        $this->db->where('A.appointment_id =',$appointment_id);

        $info = $this->db->get()->row();

        $today = strtotime(date('Y-m-d'));
        $appointmentDate = strtotime($info->appointment_date);

        // Check if the appointment date is today's date
        if($appointmentDate == $today){
            // Update status to checked in
            $appStatus['status'] = "checked_in";
            $appStatus['check_in_time'] = date("Y-m-d H:i:s");

            // run update
            $this->Generic_model->updateData("appointments", $appStatus, array(
                'appointment_id' => $appointment_id
            ));

            // Get 
            $ap_details = $this->Generic_model->getSingleRecord('appointments', array(
                'appointment_id' => $appointment_id
            ), '');

            // Check if the clinic has nurse availability or NO
            $check_nurse_status = $this->db->select("count(*) as num_rows, status")->from("users")->where("role_id='6' and clinic_id ='" . $ap_details->clinic_id . "'")->get();
            
            $count = $check_nurse_status->num_rows();
            $res = $check_nurse_status->row();

            // If nurse available then send notification to the nurse
            if ($count == 0) {
                $new_status['status'] = "waiting";
                $this->Generic_model->updateData("appointments", $new_status, array(
                    'appointment_id' => $appointment_id
                ), '');
                $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');
            } else { // Else to the doctor
                if ($res->status == 1) {
                    $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'check_in', 'dashboard');
                } else {
                    $new_status['status'] = "waiting";
                    $this->Generic_model->updateData("appointments", $new_status, array(
                        'appointment_id' => $appointment_id
                    ), '');
                    $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');
                }
            }

            if($appointment_id){
                redirect("Vitals/add_vitals/" . $patient_id . "/" .$appointment_id);    
            }else{
                redirect("Vitals/add_vitals/" . $patient_id);
            }
            
        }
        
    }

    // Updating appointment priority with choosen on AJAX
    public function change_priority()
    {
        $a_status['priority'] = $this->input->post("priority");
        $this->Generic_model->updateData("appointments", $a_status, array(
            'appointment_id' => $this->input->post("appointment_id")));
    }

    // Dropping an appointment with choosen appointment id
    public function drop_app()
    {
        $a_status['status'] = "drop";
        $this->Generic_model->updateData("appointments", $a_status, array(
            'appointment_id' => $this->input->post("appointment_id")
        ));
    }

    // Closing an appointment
    public function close_app()
    {
        $a_status['status'] = "closed";
        $this->Generic_model->updateData("appointments", $a_status, array(
            'appointment_id' => $this->input->post("appid")
        ));
    }

    // Updating appointment count for each doctor in calendar when changing month
    public function get_month_count()
    {
        $Cdata     = array();
        $split     = explode(",", $this->input->post("d_list"));
        $clinic_id = $this->session->userdata('clinic_id');
        foreach ($split as $key => $value) {
            $count = $this->db->select("count(*) as num_rows")->from("appointments")->where("clinic_id='" . $clinic_id . "' and  doctor_id='" . $value . "' and month(appointment_date) ='" . $this->input->post("mid") . "'  and status !='drop' and status !='reschedule' and patient_id!=0")->get()->row();

            $Cdata[] = array(
                'id' => $value,
                'count' => $count->num_rows
            );


        }

        echo json_encode($Cdata);

    }

    // Updating appointment count for each doctor in calendar when changing week
    public function get_week_count()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $Cdata     = array();
        $split     = explode(",", $this->input->post("d_list"));
        foreach ($split as $key => $value) {
            $count = $this->db->select("count(*) as num_rows")->from("appointments")->where("clinic_id='" . $clinic_id . "' and  doctor_id='" . $value . "' and (appointment_date >= '" . $this->input->post("start") . "' and appointment_date <= '" . $this->input->post("end") . "')  and status !='drop' and status !='reschedule' and patient_id!=0")->get()->row();
            $Cdata[] = array(
                'id' => $value,
                'count' => $count->num_rows
            );
        }
        echo json_encode($Cdata);
    }

    // Updating appointment count for each doctor in calendar when changing day
    public function get_day_count()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $Cdata     = array();
        $split     = explode(",", $this->input->post("d_list"));
        foreach ($split as $key => $value) {
            $count = $this->db->select("count(*) as num_rows")->from("appointments")->where("clinic_id='" . $clinic_id . "' and  doctor_id='" . $value . "' and appointment_date = '" . $this->input->post("curdate") . "'  and status !='drop' and status !='reschedule' and patient_id!=0")->get()->row();
            $Cdata[] = array(
                'id' => $value,
                'count' => $count->num_rows
            );
        }
        echo json_encode($Cdata);
    }


    // Booking an appointment for a followup patient
    public function book_appointment()
    {

        // Get clinic id from the session
        $user_id = $this->session->userdata('user_id');
        $payment = 0;
        
        $clinic_id = $this->session->userdata('clinic_id');
        $clinic_info = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $clinic_id), $order = '');
        
        $patient_id = trim($this->input->post('p_id'));
        $patient_info = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), $order = '');

        $doctor_id = trim($this->input->post('d_id'));
        $doctor_info = $this->db->query("select * from  doctors  where doctor_id='".$doctor_id."'")->row();

        // Convert date to m/d/Y format. Currently it is d/m/Y
        $selectedDate = str_replace("/", "-", $this->input->post('date'));
        $appointment_date = date("Y-m-d", strtotime($selectedDate));

        /*
        Check if there is already an appointment on the following below criteria
        Selected doctor
        Selected Date
        Selected Patient
        */
        $this->db->select('appointment_id, appointment_date, appointment_time_slot, patient_id, doctor_id');
        $this->db->from('appointments');
        $this->db->where('appointment_date =',$appointment_date);
        $this->db->where('clinic_id =',$clinic_id);
        $this->db->where('doctor_id =',$doctor_id);
        $this->db->where('patient_id =',$patient_id);
        $this->db->group_start();
        $this->db->where('status =','booked');
        $this->db->or_where('status =','checked_in');
        $this->db->or_where('status =','vital_signs');
        $this->db->or_where('status =','waiting');
        $this->db->or_where('status =','in_consultation');       
        $this->db->group_end();

        $checkAppointment = $this->db->get()->result();

        if(count($checkAppointment) > 0) {
            // Appointment exist
            echo "existing";
        }else{
            // Get review days & review times for this doctor id
            $get_review_info = $this->db->select('review_days, review_times')->from('clinic_doctor')->where("clinic_id =", $clinic_id)->where('doctor_id =',$doctor_id)->get()->row();

            $review_days = $get_review_info->review_days;
            $review_times = $get_review_info->review_times;

            $today = date('Y-m-d');

            // Get the date in which the review days would fall
            $max_possible_review_date = date('Y-m-d', strtotime('-'.$review_days.' days', strtotime($today)));

            // Check when was the last appointment with this doctor
            $this->db->select("appointment_id, appointment_date, appointment_time_slot, parent_appointment_id, review_no, status");
            $this->db->from("appointments");
            $this->db->where("patient_id =", $patient_id);
            $this->db->where("clinic_id =", $clinic_id);
            $this->db->where("doctor_id =", $doctor_id);
            $this->db->where("appointment_date >",$max_possible_review_date);
            $this->db->where("parent_appointment_id =",0);
            $this->db->where("status =",'closed');
            $this->db->order_by("appointment_id DESC");
            $this->db->limit("1","0");

            $checkLastAppointment = $this->db->get()->row();

            //echo $this->db->last_query();
            // echo "Last review Date: ".$max_possible_review_date;
            // echo "Appointment id: ".$checkLastAppointment->appointment_id.'...';

            if(count($checkLastAppointment) > 0) {
                // Appointment will fall in review days
                // Check for the last appointment record whose is holding this appointment id as its parent appointment id
                $checkChildAppointment = $this->db->select("appointment_id, review_no, parent_appointment_id")->from("appointments")->where("parent_appointment_id =",$checkLastAppointment->appointment_id)->order_by("appointment_id DESC")->limit("1","0")->get()->row();

                // echo "Child appointment details";
                // print_r($checkChildAppointment);

                if(count($checkChildAppointment) > 0){

                    // echo "Review times: ".$review_times."... ";
                    // echo "Parent Appointment Id: ".$checkChildAppointment->parent_appointment_id."... ";

                    // Check its review no with review times
                    if($checkChildAppointment->review_no < $review_times){
                        // echo "1....";
                        $data['parent_appointment_id'] = $checkChildAppointment->parent_appointment_id;    
                        $data['review_no'] = ++$checkChildAppointment->review_no;    
                        $data['payment_status'] = 2; // Free
                        $payment = 0;
                    }else{
                        // echo "2....";
                        // Appointment doesn't fall into review days as no. of review times has reached
                        // Need to Collect payment
                        $data['review_no'] = 0;
                        $data['payment_status'] = 0; // Collect Payment
                        $payment = 1;
                    }                
                }else{
                    // echo "3....";
                    $data['parent_appointment_id'] = $checkLastAppointment->appointment_id;    
                    $data['review_no'] = 1;
                    $data['payment_status'] = 2; // Free
                    $payment = 0;
                }
            }else{
                // echo "4....";
                $data['parent_appointment_id'] = 0;    
                $data['review_no'] = 0;
                $data['payment_status'] = 0; // Collect Payment
                $payment = 1;
            }

            $data['clinic_id'] = $clinic_id;
            $data['patient_id'] = $patient_id;
            $data['appointment_type'] = "Follow-up";
            $data['umr_no'] = trim($this->input->post('umr'));
            $data['booking_type'] = trim($this->input->post('btype'));
            $data['doctor_id'] = $doctor_id;

            $data['appointment_date'] = $appointment_date;
            $data['appointment_time_slot'] = $this->input->post('slot');
            $data['created_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $data['status'] = "booked";
            $data['priority'] = $this->input->post('priority');

            //print_r($data);

            // Capture the last inserted Appointment ID
            $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $data);

            $date_split = explode("-", date("Y-m-d", strtotime($this->input->post('date'))));
            $month = date("F", mktime(0, 0, 0, $date_split[1], 10));
            $sms_content = "Dear " . ucwords($patient_info->first_name) . ",
            Your appointment is fixed with Dr. " . ucwords($doctor_info->first_name . "  " . $doctor_info->last_name . " ,  " . $clinic_info->clinic_name) . " on " . $date_split[2] . " " . $month . " " . $date_split[0] . " at " . date("h:i A", strtotime($this->input->post('slot')));

            if ($this->input->post("sms") == "yes") {
                sendsms($this->input->post('mobile'), $sms_content);
            }

            // Adding choosen procedures to patient procedure records
            if (count($this->input->post("procedures")) > 0) {
                $procedures = $this->input->post("procedures");
                foreach($procedures as $procedure_id){
                    $procedure['patient_id'] = $this->input->post("pid");
                    $procedure['doctor_id'] = $this->input->post('d_id');
                    $procedure['clinic_id'] = $clinic_id;
                    $procedure['medical_procedure_id'] = $procedure_id;
                    $procedure['appointment_id'] = $app_id;
                    $procedure['payment_status'] = 0;

                    // insert patient_procedure
                    $this->Generic_model->insertData("patient_procedure", $procedure);
                }
            }

            echo $appointment_id.":".$payment;

        }
    }


    // Not using this method
    public function set_appointment_info()
    {
        $check = $this->Generic_model->getSingleRecord('doctors', array(
            'doctor_id' => $this->input->post('d_id')
        ), $order = '');

        $newdata = array(
            'dname' => "DR. " . $check->first_name . " " . $check->last_name,
            'did' => $this->input->post('d_id'),
            'app_date' => date("Y-m-d", strtotime($this->input->post('date'))),
            'priority' => $this->input->post('priority'),
            'app_slot' => $this->input->post('slot'),
            'sms' => $this->input->post('sms')
        );

        $this->session->set_userdata($newdata);
    }


    // Getting doctor slots by weekday
    public function dynamic_doctor_slots()
    {

        $clinic_id = $this->session->userdata('clinic_id');
        $cond      = '';

        if ($clinic_id != 0)
            $cond = "cd.clinic_id=" . $clinic_id . " and";

        $date     = date('Y-m-d');
        $week_day = date('N', strtotime($this->input->post('date')));

        $daws = $this->db->select("*")->from("clinic_doctor cd")->join("doctors d", "cd.doctor_id=d.doctor_id")->where($cond . " cd.doctor_id='" . $this->input->post('did') . "'")->get()->result();

        $booked_slots = array();

        //if ($daws) {
        foreach ($daws as $key => $values) {

            //$starttime = date("H:i", strtotime($values->from_time)); // your start time

            //$endtime  = date("H:i", strtotime($values->to_time)); // End time
            $duration = '5'; // split by 30 mins

            $array_of_time = array();
            // $start_time    = strtotime($starttime); //change to strtotime
            // $end_time      = strtotime($endtime); //change to strtotime
            $from          = "07:00";
            $to            = "23:45";
            $start_time    = strtotime($from); //change to strtotime
            $end_time      = strtotime($to);

            $add_mins = $duration * 60;

            while ($start_time <= $end_time) // loop between time
            {
                $array_of_time[] = date("H:i", $start_time);
                $start_time += $add_mins; // to check endtime
            }
        $booked_slots[] = $array_of_time;
    }

    $main_arr = array_flatten($booked_slots);
    $app_date = str_replace("/", "-", $this->input->post('date'));
    $bs       = $this->db->select("*")->from("appointments")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $this->input->post('did') . "' and appointment_date='" . date('Y-m-d', strtotime($app_date)) . "'  and patient_id!=0 and status!='drop' and status !='reschedule'")->get()->result();
    $final    = array();
    $b_slots  = array();
    foreach ($bs as $bss) {
        $b_slots[] = date('H:i', strtotime($bss->appointment_time_slot));
    }

    foreach ($main_arr as $key => $values) {
        if (!in_array($values, $b_slots)) {
            $final[] = $values;
        }
    }
    
    $time_slot1     = preg_replace('/\s+/', '', $this->input->post('time_slot'));
    $current_time   = date("H:i");
    $appointment_dt = date('Y-m-d', strtotime($app_date));
    $current_date   = date("Y-m-d");
    $slot           = '';
    $slot .= '[';
    foreach ($final as $key => $slots) {
        $style = "";

        $slot .= json_encode(date("H:i:s", strtotime($slots)));
        $slot .= ',';

    }

    $slot .= ']';

    echo str_replace(",]", "]", trim($slot, ","));

}

//checking review appointment status and showing message while creating followup
public function get_appointments()
{
//print_r($this->input->post());exit;
    $clinic_id = $this->session->userdata('clinic_id');

    $check = $this->db->select("*")->from("appointments a")->join("doctors d", "a.doctor_id= d.doctor_id")->where("a.patient_id='" . $this->input->post("pid") . "' and a.doctor_id='" . $this->input->post("did") . "' and a.clinic_id=" . $clinic_id . " and  a.parent_appointment_id=0 and  a.appointment_date <='" . date('Y-m-d') . "' and a.status!='booked' and a.status!='drop' and a.status!='reschedule'")->order_by("a.appointment_date desc")->get()->row();


    $last_appointment = $this->db->select("*")->from("appointments a")->join("doctors d", "a.doctor_id= d.doctor_id")->where("a.patient_id='" . $this->input->post("pid") . "' and a.doctor_id='" . $this->input->post("did") . "' and a.clinic_id=" . $clinic_id . " and   a.appointment_date <='" . date('Y-m-d') . "'")->order_by("a.appointment_date desc")->get()->row();

//getting doctor review days
    $get_review_days = $this->db->select("*")->from("clinic_doctor")->where("clinic_id='" . $clinic_id . "' and doctor_id ='" . $this->input->post('did') . "'")->get()->row();

    $get_review_times = $get_review_days->review_times;

//getting review date in y-m-d format
    $review_check_date = date('Y-m-d', strtotime($check->appointment_date . ' + ' . $get_review_days->review_days . ' days'));

    $check_review_count = $this->db->select("*")->from("appointments")->where("patient_id='" . trim($this->input->post("pid")) . "' and doctor_id='" . trim($this->input->post("did")) . "' and appointment_date >'" . $check->appointment_date . "' and appointment_date <='" . $review_check_date . "' and status !='booked' and status!='drop' and status!='reschedule'")->get()->num_rows();

    $split_date = explode("/", $this->input->post('date'));
    $new_date   = str_replace("/", "-", $this->input->post());
    $new_date   = date("Y-m-d", strtotime($new_date));

//if appointment exists
    if ($check) {

//if appointment date equals to current date
        if ($new_date == $check->appointment_date) {
            if ($check->status != "closed") {
                echo "<div class='alert alert-danger'><small><div class='text-muted'></div>You have already active appointment with DR. " . strtoupper($check->first_name . " " . $check->last_name) . " Today. Please close your previous appointment .</div><script>$('#submit').hide(); </script>";
            }

        } else {


            if ($new_date > $review_check_date) {
                echo "<div class='alert alert-danger'><small><div class='text-muted'></div><b>Last Appointment Booked On </b>" . date("d F Y", strtotime($last_appointment->appointment_date)) . " at " . date("h:i A", strtotime($last_appointment->appointment_time_slot)) . " with DR. " . strtoupper($last_appointment->first_name . " " . $last_appointment->last_name) . ". You will have to pay consultation fee as your appointment date is falling behind review date.</small></div>";
            } else {

                if ($check_review_count < $get_review_times) {
                    $review_time_diff = $get_review_times - $check_review_count;
                    echo "<div class='alert alert-danger'><small><div class='text-muted'></div><b>Last Appointment Booked </b>" . date("d F Y", strtotime($last_appointment->appointment_date)) . " " . date("h:i A", strtotime($last_appointment->appointment_time_slot)) . " with DR. " . strtoupper($last_appointment->first_name . " " . $last_appointment->last_name) . ". You have " . $review_time_diff . " more review consultation left.</small></div>";
                } else {
                    echo "<div class='alert alert-danger'><small><div class='text-muted'></div><b>Last Appointment Booked </b>" . date("d F Y", strtotime($last_appointment->appointment_date)) . " " . date("h:i A", strtotime($last_appointment->appointment_time_slot)) . " with DR. " . strtoupper($last_appointment->first_name . " " . $last_appointment->last_name) . " You will have to pay consultation fee. </small></div>";
                }

            }




        }

    } else {
        echo "";
    }
}

//checking booked slots on slot click(ajax call)
public function checkslots()
{
    $clinic_id = $this->session->userdata('clinic_id');
    $cond      = '';
    if ($clinic_id != 0)
        $cond = "and clinic_id=" . $clinic_id;

    $bs = $this->db->select("*")->from("appointments")->where("doctor_id='" . $this->input->post('did') . "' and appointment_date='" . date('Y-m-d', strtotime($this->input->post('date'))) . "' and appointment_time_slot='" . $this->input->post('time_slot') . "' " . $cond)->get()->result();
//echo $this->db->last_query();
    if (count($bs) > 0) {
        echo "Slot already Booked by other Patient";
    } else
    echo '';
}
public function check_slot()
{

    $clinic_id = $this->session->userdata('clinic_id');
    $cond      = '';
    if ($clinic_id != 0)
        $cond = "cd.clinic_id=" . $clinic_id . " and";
    $date         = date('Y-m-d');
    $explode_date = explode("/", $this->input->post('date'));

    $post_date = $explode_date[1] . "/" . $explode_date[0] . "/" . $explode_date[2];
    $week_day  = date('N', strtotime($post_date));

    $daws = $this->db->select("*")->from("clinic_doctor cd")->join("clinic_doctor_weekdays cdw", "cd.clinic_doctor_id=cdw.clinic_doctor_id")->join("clinic_doctor_weekday_slots cdws", "cdws.clinic_doctor_weekday_id=cdw.clinic_doctor_weekday_id")->join("doctors d", "cd.doctor_id=d.doctor_id")->where($cond . " cd.doctor_id='" . $this->input->post('did') . "' and cdw.weekday='" . $week_day . "'")->get()->result();


    $booked_slots = array();

    if ($daws) {
        foreach ($daws as $key => $values) {

$starttime = date("H:i", strtotime($values->from_time)); // your start time

$endtime  = date("H:i", strtotime($values->to_time)); // End time
$duration = '5'; // split by 30 mins

$array_of_time = array();
//$start_time    = strtotime($starttime); //change to strtotime
//$end_time      = strtotime($endtime); //change to strtotime
$from          = "07:00";
$to            = "23:45";
$start_time    = strtotime($from); //change to strtotime
$end_time      = strtotime($to);
$add_mins      = $duration * 60;

while ($start_time <= $end_time) // loop between time
{
    $array_of_time[] = date("H:i", $start_time);
$start_time += $add_mins; // to check endtime
}
$booked_slots[] = $array_of_time;
}


$main_arr = array_flatten($booked_slots);
// print_r($main_arr);exit;

$bs      = $this->db->select("*")->from("appointments")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $this->input->post('did') . "' and appointment_date='" . date('Y-m-d', strtotime($this->input->post('date'))) . "' and status!='drop' and status !='reschedule'")->get()->result();
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
$slot           = "";
$time_slot1     = preg_replace('/\s+/', '', $this->input->post('time_slot'));
$current_time   = date("H:i");
$appointment_dt = date('Y-m-d', strtotime($this->input->post('date')));
$current_date   = date("Y-m-d");
foreach ($final as $key => $slots) {


    $slot .= "<option value='" . $slots . "'>" . date("h:i A", strtotime($slots)) . "</option>";


}


echo $slot;
} else {
    echo "no";

}
}


//getting patients list if mobile number matches (family tree)
public function confirm_mobile()
{
    $mobile = DataCrypt($this->input->post('mobile'),'encrypt');
    $patient_info = $this->db->select("*")->from("patients")->where("mobile like '" . $mobile . "%'")->get();
    $count        = $patient_info->num_rows();
    $data         = $patient_info->result();
    $nr           = array();

    if ($count > 0) {


        foreach ($data as $key => $value) {
            $nr[] = array(
                'key' => $value->patient_id,
                'value' => DataCrypt($value->mobile,'decrypt'),
                'mobile' => $value->first_name . " " . $value->last_name,
                'label' => $value->umr_no
            );
        }


        echo json_encode($nr);
    } else {
        echo "no";
    }
}

//searching patients with umr (followup appointment)
public function search_umr()
{
    $patient_info = $this->db->select("*")->from("patients")->where("umr_no like '%" . $this->input->post('umr') . "%'")->get();

    $count = $patient_info->num_rows();
    $data  = $patient_info->result();
    $nr    = array();

    if ($count > 0) {


        foreach ($data as $key => $value) {
            $nr[] = array(
                'key' => $value->patient_id,
                'value' => $value->mobile,
                'mobile' => $value->first_name . " " . $value->last_name,
                'label' => $value->umr_no
            );
        }

        echo json_encode($nr);
    } else {
        echo "no";
    }
}

//
public function check_patient_mobile()
{
    $mobile = DataCrypt($this->input->post('mobile'),'encrypt');
    $patient_info = $this->db->select("*")->from("patients")->where("mobile='" . $mobile . "'")->get()->row();
    if (count($patient_info) > 0) {
        echo $patient_info->first_name . ":Yes";
    } else {
        echo "no";
    }
}

//Not using this method
public function confirm_payment($id)
{

    echo '<pre>';
    print_r($datas);
    echo '</pre>';
    exit();

    $datas['app_info'] = $info = $this->db->select("*")->from("appointments a")->join("doctors d", "a.doctor_id = d.doctor_id")->where("a.appointment_id='" . $id . "'")->get()->row();
    $get_fee           = $this->db->select("registration_fee")->from("clinics")->where(" clinic_id='" . $info->clinic_id . "'")->get()->row();

    $datas['patient_payment_status'] = $this->db->select("payment_status")->from("patients")->where(" patient_id='" . $info->patient_id . "'")->get()->row();
    if ($info->payment_status == 0) {
        $datas['registration_fee'] = $get_fee->registration_fee;
    } else {
        $datas['registration_fee'] = 0;
    }
    $datas['app_id'] = $id;
    if ($this->input->post()) {

        $inr        = $this->db->select("count(*) as invoiceno")->from("billing")->where("clinic_id='" . $info->clinic_id . "'")->get()->row();
        $inv_gen    = ($inr->invoiceno) + 1;
        $receipt_no = 'RECEIPT-' . $info->clinic_id . '-' . $inv_gen;
        $invoice_no = 'INV-' . $info->clinic_id . '-' . $inv_gen;

        $billing_master['receipt_no']         = $receipt_no;
        $billing_master['discount_status']    = "";
        $billing_master['appointment_id']     = $id;
        $billing_master['invoice_no']         = $invoice_no;
        $billing_master['patient_id']         = $info->patient_id;
        $billing_master['clinic_id']          = $info->clinic_id;
        $billing_master['umr_no']             = $info->umr_no;
        $billing_master['created_by']         = 1;
        $billing_master['created_date_time']  = date('Y-m-d H:i:s');
        $billing_master['modified_by']        = $billing_master['created_by'] = 1;
        $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
        if ($datas['patient_payment_status'] == 0) {
            $billing_master['billing_type'] = "Registration";
        } else {
            $billing_master['billing_type'] = "Consultation";
        }
        $billing_master['payment_mode']    = $this->input->post("payment_mode");
        $billing_master['cheque_no']       = $this->input->post("dd_or_cheque_no");
        $billing_master['refference_no']   = "";
        $billing_master['deposit_date']    = date("Y-m-d");
        $billing_master['discount_status'] = "";

        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

        if ($datas['patient_payment_status']->payment_status == 0) {
            $reg['billing_id']         = $billing_id;
            $reg['item_information']   = "Registration";
            $reg['quantity']           = 1;
            $reg['discount']           = 0;
            $reg['amount']             = $this->input->post("registration");
            $reg['created_by']         = 1;
            $reg['created_date_time']  = date('Y-m-d H:i:s');
            $reg['modified_by']        = 1;
            $reg['modified_date_time'] = date('Y-m-d H:i:s');

            $this->Generic_model->insertData('billing_line_items', $reg);
        }

        $patient_bank['billing_id']         = $billing_id;
        $patient_bank['item_information']   = "Consultation";
        $patient_bank['quantity']           = 1;
        $patient_bank['discount']           = 0;
        $patient_bank['amount']             = $this->input->post("consultation");
        $patient_bank['created_by']         = 1;
        $patient_bank['created_date_time']  = date('Y-m-d H:i:s');
        $patient_bank['modified_by']        = 1;
        $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');

        $this->Generic_model->insertData('billing_line_items', $patient_bank);

        $clinic_details = $this->Generic_model->getSingleRecord('clinics', array(
            'clinic_id' => $info->clinic_id
        ), $order = '');

        $doctor_details = $this->Generic_model->getSingleRecord('doctors', array(
            'doctor_id' => $info->doctor_id
        ), $order = '');

        $departments = $this->Generic_model->getSingleRecord('department', array(
            'department_id' => $doctor_deatails->department_id
        ), $order = '');

        $billing         = $this->Generic_model->getAllRecords('billing_line_items', array(
            'billing_id' => $billing_id
        ), $order = '');
        $patient_details = $this->Generic_model->getSingleRecord('patients', array(
            'patient_id' => $info->patient_id
        ), $order = '');


        $district_details = $this->Generic_model->getSingleRecord('districts', array(
            'district_id' => $patient_details->district_id
        ), $order = '');

        $state_details = $this->Generic_model->getSingleRecord('states', array(
            'state_id' => $patient_details->state_id
        ), $order = '');

        $data['clinic_logo']     = $clinic_details->clinic_logo;
        $data['clinic_phone']    = $clinic_details->clinic_phone;
        $data['clinic_name']     = $clinic_details->clinic_name;
        $data['address']         = $clinic_details->address;
        $data['doctor_name']     = "Dr. " . strtoupper($doctor_details->first_name . " " . $doctor_details->last_name);
        $data['qualification']   = $doctor_details->qualification;
        $data['department_name'] = $departments->department_name;
        $data['patient_name']    = ucfirst($patient_details->title) . "." . strtoupper($patient_details->first_name . " " . $patient_details->last_name);
        $data['age_unit']        = $patient_details->age_unit;
        $data['age']             = $patient_details->age;
        $data['gender']          = $patient_details->gender;
        $data['umr_no']          = $patient_details->umr_no;

        $data['patient_address'] = $patient_details->address_line . "," . $patient_details->pincode;
        $data['billing']         = $billing;
        $data['invoice_no']      = $invoice_no;
        $data['receipt_no']      = $invoice_no;
        $data['payment_method']  = $this->input->post("payment_mode");
        $data['discount']        = 0;

        $html              = $this->load->view('billing/generate_billing', $data, true);
        $pdfFilePath       = "billing_" . $info->patient_id . $billing_id . ".pdf";
        $data['file_name'] = $pdfFilePath;

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
        $billFile['invoice_pdf'] = $data['file_name'];
        if ($this->input->post("consultation") == 0) {
            $as['payment_status'] = 2;
        } else {
            $as['payment_status'] = 1;
        }

        if ($this->input->post("registration") == 0) {
            $ps['payment_status'] = 2;
        } else {
            $ps['payment_status'] = 1;
        }

        $this->Generic_model->updateData('appointments', $as, array(
            'appointment_id' => $id
        ));
        $this->Generic_model->updateData('patients', $ps, array(
            'patient_id' => $info->patient_id
        ));
        $this->Generic_model->updateData('billing', $billFile, array(
            'billing_id' => $billing_id
        ));
        $this->Generic_model->updateData('appointments', $billFile, array(
            'appointment_id' => $id
        ));
        $pdf = base_url() . 'uploads/billings/' . $pdfFilePath;

        redirect("calendar_view/appointment_success/" . $id);
    }

    $datas['view'] = 'appointment_payment';
    $this->load->view('layout', $datas);

}


//not using this method
public function appointment_success($app_id)
{
    $data['app_info'] = $this->db->select("*")->from("appointments a")->join("doctors d", "a.doctor_id = d.doctor_id")->where("a.appointment_id='" . $app_id . "'")->get()->row();
    $data['view']     = 'appointment_success';
    $this->load->view('layout', $data);

}

// Saving data for new patient and creating appointment
public function patient_add_save()
{

    $appdate   = str_replace('/', '-', $this->input->post('date'));
    
    $user_id   = $this->session->userdata('user_id');
    $clinic_id = $this->session->userdata('clinic_id');
    $clinic_info = $this->db->select("clinic_name")->from("clinics")->where("clinic_id='" . $clinic_id . "'")->get()->row();
    $doctor_id = $this->input->post('d_id');
    $doctor_info = $this->db->select("doctor_id, first_name, last_name")->from("doctors")->where("doctor_id ='" . $doctor_id . "'")->get()->row();

    // Get last generated UMR No.                
    $last_umr = $this->db->select("umr_no")->from("patients")->order_by("patient_id DESC")->get()->row();

    // Generate UMR No.
    if(count($last_umr) > 0){
        $umr_str   = trim($last_umr->umr_no);
        $split_umr = substr($umr_str, 1, 4);
        if ($split_umr == date("my")) {
            $replace = str_replace("P" . $split_umr, "", $last_umr->umr_no);
            $next_id = (++$replace);
            $umr_no  = "P" . date("my") . $next_id;
        } else {
            $umr_no = "P" . date("my") . "1";
        }  
    }else{
        // No records found. Generate New UMR#
        $umr_no = "P" . date("my") . "1";
    }

    if ($this->input->post('rbt') == 'WOM') {
        $referred_by = $this->input->post('rbp');
        // rbp - referred by another patient (Referred by Patient)
    } else if ($this->input->post('rbt') == 'Doctor') {
        $referred_by = $this->input->post('rbd');
        // rbd - referred by a doctor (Referred by Doctor)
    } else if ($this->input->post('rbt') == 'Online') {
        // rbo - got reference via online (Referred by Online)
        $referred_by = $this->input->post('rbo');
    }

    $data['umr_no'] = $umr_no;
    $data['password'] = md5("1234");
    $data['clinic_id'] = $clinic_id;
    $data['payment_status'] = 0;
    $data['referred_by_type'] = $this->input->post('rbt');
    $data['referred_by'] = $referred_by;
    $data['status'] = 1;
    $data['created_by'] = $user_id;
    $data['created_date_time'] = date('Y-m-d H:i:s');
    $data['modified_by'] = $user_id;
    $data['modified_date_time'] = date('Y-m-d H:i:s');

    $tempDir = './uploads/qrcodes/patients/';
    $codeContents = $umr_no;
    $qrname = $umr_no . md5($codeContents) . '.png';
    $pngAbsoluteFilePath = $tempDir . $qrname;
    $urlRelativeFilePath = base_url() . 'uploads/qrcodes/patients/' . $qrname;

    if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($codeContents, $pngAbsoluteFilePath);
    }

    $data['qrcode'] = $qrname;
    $data['username'] = $umr_no;

    $family = 0;

    // Check if the patient is one's relative
    // If yes get the guardian's information
    if ($this->input->post('relation') != "norelation") {

        // Yes ::: Get guardian's data
        $guardian = $this->db->select("*")->from("patients")->where("mobile='" . $this->input->post('mobile') . "'")->get()->row();

        $data['guardian_id'] = $guardian->patient_id;

        // Mobile will become relative Patient's alternative mobile
        $data['alternate_mobile'] = DataCrypt($this->input->post('mobile'), 'encrypt');

        // Relative Name from the post will be relative patient's first name
        $data['first_name'] = $this->input->post('relative_name');

        $family = 1;

    }else{
        $data['mobile'] = DataCrypt($this->input->post('mobile'), 'encrypt');
        $data['first_name'] = ucwords($this->input->post('pname'));
    }

    // Referral Doctor Information Mapping
    if ($this->input->post('rbt') == 'Doctor') {
        
        if ($this->input->post('rbd') == "others") {

            // New referral doctor information
            $ref_doctor['doctor_name'] = $this->input->post("nrd_name");
            $ref_doctor['mobile'] = DataCrypt($this->input->post('nrd_mobile'), 'encrypt');
            $ref_doctor['location'] = $this->input->post("nrd_location");
            $ref_doctor['clinic_id'] = $clinic_id;

            // Insert new referral doctor
            $data['referral_doctor_id'] = $this->Generic_model->insertDataReturnId("referral_doctors", $ref_doctor);

        } else {
            // Referral doctor id
            $data['referral_doctor_id'] = $this->input->post('rbd');

            // Get ReferralDoctor Information to sens SMS 
            $ref_doctor = $this->db->select("doctor_name, mobile")->from("referral_doctors")->where("rfd_id ='" . $data['refferal_doctor_id'] . "'")->get()->result();
        }

        if (!empty($ref_doctor['mobile'])) {
            // SMS to Referral Doctor
            $refSMSContent = "Dear " . ucwords($ref_doctor['doctor_name']) . ", thanks for referring " . $data['first_name'] . " to Dr" . ucwords($doctor_info->first_name . " " . $doctor_info->last_name) . " at " . ucwords($clinic_info->clinic_name) . " for Consultation/Procedure. We value your association and ensure you that your patient will be well taken care of. Have a Good Day.";
            
            sendsms($this->input->post('nrd_mobile'), $refSMSContent);
        }        
    }

    // Insert Patient Data
    $patient_id = $this->Generic_model->insertDataReturnId("patients", $data);

    if($family){
        $relation['guardian_id'] = $data['guardian_id'];
        $relation['patient_id'] = $patient_id;
        $relation['relation'] = $this->input->post("relation");
        $relation['created_date_time'] = date("Y-m-d H:i:s");

        $this->Generic_model->insertData("patient_family", $relation);
    }

    // Create Appointment
    $appointmentData['clinic_id'] = $clinic_id;
    $appointmentData['patient_id'] = $patient_id;
    $appointmentData['umr_no'] = $umr_no;
    $appointmentData['doctor_id'] = $this->input->post('d_id');
    $appointmentData['appointment_type'] = "New";

    $appdate = str_replace('/', '-', $this->input->post('date'));
    
    $appointmentData['appointment_date'] = date("Y-m-d", strtotime($appdate));
    $appointmentData['appointment_time_slot'] = $this->input->post('slot');
    $appointmentData['priority'] = $this->input->post('priority');
    $appointmentData['description'] = '';
    $appointmentData['parent_appointment_id'] = 0;
    $appointmentData['payment_status'] = 0;
    $appointmentData['booking_type'] = $this->input->post('btype');
    $appointmentData['status'] = 'booked';
    $appointmentData['created_by'] = $user_id;
    $appointmentData['created_date_time'] = date('Y-m-d H:i:s');
    $appointmentData['modified_by'] = $user_id;
    $appointmentData['modified_date_time'] = date('Y-m-d H:i:s');

    $appointment_id = $this->Generic_model->insertDataReturnId('appointments', $appointmentData);

    $date_split = explode("-", date("Y-m-d", strtotime($appdate)));

    $month = date("F", mktime(0, 0, 0, $date_split[1], 10));

    $patientSMSContent = "Dear " . ucwords($data['first_name']) . ", Your appointment is fixed with Dr. " . ucwords($doctor_info->first_name . " " . $doctor_info->last_name) . ",  " . ucwords($clinic_info->clinic_name) . " on " . $date_split[2] . " " . $month . " " . $date_split[0] . " at " . date("h:i A", strtotime($this->input->post('slot')));

    $Msg = "Appointment Successfully Created with Dr. " . ucwords($doctor_info->first_name . " " . $doctor_info->last_name) . ",  " . ucwords($clinic_info->clinic_name) . " on " . $date_split[2] . " " . $month . " " . $date_split[0] . " at " . date("h:i A", strtotime($this->input->post('slot')));

    if ($this->input->post("sms") == "yes") {
        sendsms($this->input->post('mobile'), $patientSMSContent);
    }

    // Adding choosen procedures to patient procedure records
    if (count($this->input->post("procedures")) > 0) {
        $procedures = $this->input->post("procedures");
        foreach($procedures as $procedure_id){
            $procedure['patient_id'] = $patient_id;
            $procedure['doctor_id'] = $doctor_id;
            $procedure['clinic_id'] = $clinic_id;
            $procedure['medical_procedure_id'] = $procedure_id;
            $procedure['appointment_id'] = $appointment_id;
            $procedure['payment_status'] = 0;

            // insert patient_procedure
            $this->Generic_model->insertData("patient_procedure", $procedure);
        }
    }

    // Return patient id and appointment id of the new patient 
    echo $patient_id.':'.$appointment_id;
    
}


public function generateRandomString($length = 8)
{

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $charactersLength = strlen($characters);

    $randomString = '';

    for ($i = 0; $i < $length; $i++) {

        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

//sms testing
public function sms_test()
{
    $sms_content = "Dear Dr.A,  thanks for referring X to Dr. D at clinic for consultation. We value your association and ensure you that your patient will be well taken care of";


    sendsms("8374348452", $sms_content);

}

//generating drugs master json
public function drug_json()
{
    $drugs_list = $this->db->select("CONCAT(formulation,' ', trade_name) as drug_name,drug_id")->from(" drug")->get()->result();


    $response = array();
    $drugs    = array();
    foreach ($drugs_list as $drug) {
        $drugs[] = array(
            "id" => $drug->drug_id,
            "name" => $drug->drug_name
        );
        ;


    }
    $json_file = json_encode($drugs);



    $fp = fopen('./uploads/drugs.json', 'w');
    fwrite($fp, $json_file);
}
//creating json with investigation masters
public function investigation_json()
{
    $investigation_list = $this->db->select("investigation")->from("investigations")->get()->result();

    $prefix = '';
    $prefix .= '[';
    foreach ($investigation_list as $row) {
        $prefix .= json_encode($row->investigation);
        $prefix .= ',';
    }
    $prefix .= ']';

    $json_file = str_replace(",]", "]", trim($prefix, ","));

    $path_user = './uploads/investigation.json';

    if (!file_exists($path_user)) {
        $fp = fopen('./uploads/investigation.json', 'w');
        fwrite($fp, $json_file);
    } else {
        unlink($path_user);
        $fp = fopen('./uploads/investigation.json', 'w');
        fwrite($fp, $json_file);
    }
}


}