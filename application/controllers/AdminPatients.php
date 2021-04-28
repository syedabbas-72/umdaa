<?php
error_reporting(0);
include "phpqrcode/qrlib.php";

defined('BASEPATH') or exit('No direct script access allowed');

class AdminPatients extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    }

    /**
     * used to get the list of patients 
     * @name index
     * @access public
     */
    public function index()
    {
        $clinic_id = $this->session->userdata('clinic_id');
        $cond = '';

        if ($clinic_id != 0) {
            $this->db->select('*');
        }

        $this->db->from('patients')->where('clinic_id =', $clinic_id)->order_by('patient_id', 'desc')->limit(10);
        $data['patients'] = $this->db->get()->result();

        $this->db->select('*');
        $this->db->from('patients')->where('created_date_time between "' . date('Y-m-d H:i:s', strtotime('-7 days')) . '" and "' . date('Y-m-d H:i:s') . '"');
        $data['recent_added'] = $this->db->get()->result();

        $this->db->select('*');
        $this->db->from('appointments a')->join('patients p', 'a.patient_id = p.patient_id')->where('a.clinic_id = "' . $clinic_id . '" and check_in_time between "' . date('Y-m-d H:i:s', strtotime('-7 days')) . '" and "' . date('Y-m-d H:i:s') . '"');
        $data['recent_visited'] = $this->db->get()->result();

        $data['view'] = 'patients/admin_patients';
        $this->load->view('layout', $data);
    }


    /**
     * used to get the district details  based on the state id 
     * @name getDistricts
     * @access public
     */
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

    /**
     * checking mobile number exists
     * @access public
     * @author Vikram
     */
    public function check_mobile()
    {

        $this->db->select('*');
        $this->db->from('patients')->where('mobile', $this->input->post('mobile'));
        $result = $this->db->get()->num_rows();
        if ($result == 0) {
            $valid = 'true';
        } else {
            $valid = 'false';
        }
        echo $valid;
    }


    /**
     * getting procedures of type other diagnosys
     * @access public
     * @author Vikram
     */
    public function collect_payment($patient_id = '', $appoinment_id = '')
    {
        $clinic_id = $this->session->userdata('clinic_id');

        $data['procedures'] = $this->Generic_model->getAllRecords('clinic_procedures', array('clinic_id' => $clinic_id), '');

        $data['patient_id'] = $patient_id;
        $data['appoinment_id'] = $appoinment_id;
        $data['view'] = 'patients/collect_payment';
        $this->load->view('layout', $data);
    }

    /**
     * used to save other payment collection
     * @access public
     * @author Narasimha
     */

    public function save_collectPayment($appoinment_id = '')
    {
        $user_id = $this->session->userdata('user_id');
        $ap_details = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appoinment_id), '');

        $inr = $this->db->select('*')->from('billing')->where('clinic_id', $ap_details->clinic_id)->get()->num_rows();

        $inv_gen = $inr + 1;
        $receipt_no = 'RCT-' . $ap_details->clinic_id . '-' . $inv_gen; // receipt no
        $invoice_no = 'INV-' . $ap_details->clinic_id . '-' . $inv_gen; // invoice no

        $cart_services = $this->input->post('cart_services');
        $cart_price = $this->input->post('cart_prices');
        if ($this->input->post('cart_discount_0') == 0 || $this->input->post('cart_discount_0') == '') {
            $discount_status = 0;
        } else {
            $discount_status = 1;
        }

        $billing_master['receipt_no'] = $receipt_no;
        $billing_master['discount_status'] = "";
        $billing_master['invoice_no'] = $invoice_no;
        $billing_master['patient_id'] = $ap_details->patient_id;
        $billing_master['appointment_id'] = $ap_details->appointment_id;
        $billing_master['doctor_id'] = $ap_details->doctor_id;
        $billing_master['clinic_id'] = $ap_details->clinic_id;
        $billing_master['umr_no'] = $ap_details->umr_no;
        $billing_master['created_by'] = $user_id;
        $billing_master['created_date_time'] = date('Y-m-d H:i:s');
        $billing_master['modified_by'] = $user_id;
        $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
        $billing_master['billing_type'] = "Investigation";
        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

        for ($i = 0; $i < count($cart_services); $i++) {
            if ($this->input->post('cart_discount_' . $i) != NULL) {
                $cart_discount = $this->input->post('cart_discount_' . $i);
            } else {
                $cart_discount = 0;
            }
            $billing_line_items['billing_id'] = $billing_id;
            $billing_line_items['item_information'] = $cart_services[$i];
            $billing_line_items['quantity'] = 1;
            $billing_line_items['discount'] = $this->input->post('cart_discount_' . $i);
            $billing_line_items['discount_unit'] = $this->input->post('discount_type');
            $billing_line_items['amount'] = $cart_price[$i];
            $billing_line_items['created_by'] = $user_id;
            $billing_line_items['modified_by'] = $user_id;
            $billing_line_items['created_date_time'] = date('Y-m-d H:i:s');
            $billing_line_items['modified_date_time'] = date('Y-m-d H:i:s');
            $ok = $this->Generic_model->insertData('billing_line_items', $billing_line_items);
        }

        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $ap_details->clinic_id), $order = '');

        $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $ap_details->doctor_id), $order = '');

        $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_deatails->department_id), $order = '');

        $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
        $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $ap_details->patient_id), $order = '');

        $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');

        $state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details->state_id), $order = '');

        $data['clinic_logo'] = $clinic_deatails->clinic_logo;
        $data['clinic_phone'] = $clinic_deatails->clinic_phone;
        $data['clinic_name'] = $clinic_deatails->clinic_name;
        $data['address'] = $clinic_deatails->address;
        $data['doctor_name'] = "Dr. " . strtoupper($doctor_deatails->first_name . " " . $doctor_deatails->last_name);
        $data['qualification'] = $doctor_deatails->qualification;
        $data['department_name'] = $departments->department_name;
        $data['patient_name'] = ucfirst($patient_details->title) . "." . strtoupper($patient_details->first_name . " " . $patient_details->last_name);
        $data['age_unit'] = $patient_details->age_unit;
        $data['age'] = $patient_details->age;
        $data['gender'] = $patient_details->gender;
        $data['umr_no'] = $patient_details->umr_no;
        //$data['patient_address']=$patient_details->address_line.",".$district_details->district_name.",".$state_details->state_name.",".$patient_details->pincode;
        $data['patient_address'] = $patient_details->address_line . "," . $patient_details->pincode;
        $data['billing'] = $billing;
        $data['invoice_no'] = $invoice_no;
        $data['receipt_no'] = $invoice_no;
        $data['payment_method'] = $this->input->post("payment_mode");
        $data['discount'] = $this->input->post("cart_discount_0");

        $html = $this->load->view('billing/generate_billing', $data, true);
        $pdfFilePath = "billing_" . $info->patient_id . $billing_id . ".pdf";
        $data['file_name'] = $pdfFilePath;

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
        $billFile['invoice_pdf'] = $data['file_name'];

        $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
        redirect('Calendar_view');
    }





    /**
     * used to get the district details  based on the state id 
     * @name getDistricts
     * @access public
     * @author Rajesh
     */
    public function getDistricts()
    {
        $id = $_POST['id'];

        $districts = $this->db->select('district_id,district_name')->from('districts')->where('state_id', $id)->get()->result();
        $res = '<option value="">--Select--</option>';
        foreach ($districts as $key => $value) {
            $res .= '<option value="' . $value->district_id . '">' . $value->district_name . '</option>';
        }
        // print_r($districts);
        echo $res;
    }


    /**
     * used to update patient data based on patient_id 
     * @name patient_update
     * @access public
     * @author Rajesh
     */

    public function patient_update($id = '', $app_id = '')
    {

        if ($this->input->post('submit')) {
            $config['upload_path'] = "./uploads/patients/";
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
            $this->load->library('upload');
            $this->upload->initialize($config);
            $this->upload->do_upload('profile_image');
            $fileData = $this->upload->data('file_name');

            if ($this->input->post('testimg') != "") {
                $image_parts = explode(";base64,", $this->input->post('testimg'));
                $image_type_aux = explode("data:image/", $image_parts[0]);
                $image_type = $image_type_aux[1];

                $image_base64 = base64_decode($image_parts[1]);

                $path = "./uploads/patients/";
                $img = uniqid() . '.png';

                $file = $path . $img;
                file_put_contents($file, $image_base64);
            } else {
                if ($fileData == "") {
                    $p_img = $this->db->select('photo')->from('patients')->where('patient_id', $id)->get()->row();

                    $img = $p_img->photo;
                } else {
                    $img = $fileData;
                }
            }

            $clinic_id = $this->session->userdata('clinic_id');
            $ids = $this->db->select('p.profile_name,p.profile_id,r.role_id,r.role_name')->from('profiles p')->join('roles r', 'r.role_name = p.profile_name', 'left')->where('p.profile_name', 'Patient')->get()->row();

            $user['username'] = $this->input->post('first_name');
            $user['email_id'] = $this->input->post('email_id');
            $user['mobile'] = $this->input->post('mobile');

            $user['user_type'] = 'Patient';
            $user['role_id'] = $ids->role_id;
            $user['profile_id'] = $ids->profile_id;
            $user['status'] = 1;
            $user['created_date_time'] = date('Y-m-d H:i:s');
            $user['modified_date_time'] = date('Y-m-d H:i:s');

            $this->Generic_model->updateData('users', $user, array('user_id' => $id));

            $data['patient_id'] = $id;
            $month = date('m');
            $year = date('y');
            $data['title'] = $this->input->post('title');
            $data['first_name'] = $this->input->post('first_name');
            $data['location'] = $this->input->post('location');
            $data['last_name'] = $this->input->post('last_name');
            $data['gender'] = $this->input->post('gender');
            if (empty($this->input->post('date_of_birth'))) {
                $appdate = "";
                $data['date_of_birth'] = "";
            } else {
                $appdate = str_replace('/', '-', $this->input->post('date_of_birth'));
                $data['date_of_birth'] = date("Y-m-d", strtotime($appdate));
            }

            $data['age'] = $this->input->post('age');
            $data['mobile'] = $this->input->post('mobile');
            $data['email_id'] = $this->input->post('email_id');
            $data['preferred_language'] = $this->input->post('preferred_language');
            $data['location'] = $this->input->post('location');
            $data['photo'] = $img;
            $data['address_line'] = $this->input->post('address');
            $data['occupation'] = $this->input->post('occupation');
            $data['alternate_mobile'] = $this->input->post('alternate_mobile');
            $data['referred_by_type'] = $this->input->post('referred_by_type');
            $data['clinic_id'] = $clinic_id;
            $data['district_id'] = $this->input->post('district_id');
            $data['state_id'] = $this->input->post('state_id');
            $data['pincode'] = $this->input->post('pincode');
            if ($data['referred_by_type'] == 'WOM') {
                $data['referred_by'] = $this->input->post('referred_by_p');
            } else if ($data['referred_by_type'] == 'Doctor') {
                $data['referred_by'] = $this->input->post('referred_by_d');
            } else if ($data['referred_by_type'] == 'Online') {
                $data['referred_by'] = $this->input->post('referred_by_o');
            }
            $data['status'] = $this->input->post('status');
            $data['created_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $user_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');

            $this->Generic_model->updateData('patients', $data, array('patient_id' => $id));

            redirect("profile/index/" . $id);
        } else {
            $data['doctors'] = $this->db->select("*")->from("referral_doctors")->get()->result_array();
            $data['states'] = $this->db->select("state_id,state_name")->from("states")->order_by("state_name")->get()->result();

            $data['clinics'] = $this->db->select("clinic_id,clinic_name")->from("clinics")->order_by("clinic_name")->get()->result();

            $data['patients_list'] = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $id), '');

            if ($data['patients_list']->state_id == "") {
                $cond = "";
            } else {
                $cond = 'where state_id=' . $data['patients_list']->state_id;
            }

            $data['districts'] = $this->db->select("district_id,district_name")->from("districts")->get()->result();
            $data['view'] = 'patients/patient_edit_new';
            $this->load->view('layout', $data);
        }
    }


    public function confirm_payment($patient_id = NULL, $appointment_id = NULL)
    {

        $clinic_id = $this->session->userdata('clinic_id');

        // Retrieve the appointments
        // For appoitment info header
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P', 'P.patient_id = A.patient_id');
        $this->db->join('doctors Doc', 'A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep', 'Doc.department_id = Dep.department_id');
        $this->db->join('districts D', 'P.district_id = D.district_id', 'left');
        $this->db->join('states S', 'P.state_id = S.state_id', 'left');
        $this->db->where('A.patient_id =', $patient_id);
        $this->db->where_not_in('A.status', $status);

        // If the appointment Id is specified
        if ($appointment_id)
            $this->db->where('A.appointment_id =', $appointment_id);

        $datas['appointmentInfo'] = $this->db->get()->result();

        $doctor_id = $datas['appointmentInfo'][0]->doctor_id;
        $umr_no = $datas['appointmentInfo'][0]->umr_no;

        // Payment page flag, helps in hiding the collect payment button as it might confuse the user
        $datas['paymentPage'] = 1;

        $datas['patient_id'] = $patient_id;
        $datas['procedures'] = $this->Generic_model->getAllRecords('clinic_procedures', array('clinic_id' => $clinic_id), '');
        $datas['appointment_id'] = $appointment_id;

        if ($this->session->userdata('selected_procedures') != "") {
            $datas['selected_procedures'] = $this->db->query('select * from clinic_procedures where clinic_id="' . $clinic_id . '" and clinic_procedure_id IN(' . $this->session->userdata('selected_procedures') . ')')->result();
        }

        $datas['app_info'] = $info = $this->db->select("*,a.status as app_status")->from("appointments a")->join("doctors d", "a.doctor_id = d.doctor_id")->where("a.patient_id='" . $patient_id . "' and a.appointment_id = '" . $appointment_id . "' and a.status!='drop' and a.status!='reschedule'")->order_by("a.appointment_date", "desc")->get()->row();

        // echo '<pre>';
        // print_r($info);

        $datas['get_fee'] = $this->db->select("registration_fee")->from("clinics")->where("clinic_id='" . $info->clinic_id . "'")->get()->row();

        //echo "Get Fee Query: ".$this->db->last_query()."<br>";

        $datas['patient_payment_status'] = $this->db->select("payment_status")->from("patients")->where(" patient_id='" . $info->patient_id . "'")->get()->row();

        // echo $this->db->last_query();
        // exit();
        $datas['app_payment_status'] = $this->db->select("payment_status")->from("appointments")->where("appointment_id='" . $info->appointment_id . "'")->get()->row();

        $datas['get_info'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id='" . $info->clinic_id . "' and doctor_id='" . $info->doctor_id . "'")->get()->row();

        // echo "Get Info Query: ".$this->db->last_query()."<br>";

        $data['patient_info'] = $this->db->select("*")->from("patients")->where("patient_id='" . $info->patient_id . "'")->get()->row();

        $datas['app_id'] = $id;

        if ($this->input->post()) {

            if ($this->input->post("free") == "yes") {
                $ps['payment_status'] = 2;

                $this->Generic_model->updateData('appointments', $ps, array('appointment_id' => $appointment_id));
                redirect("Calendar_view");
            } else {
                if ($this->input->post("registration") != 0 || $this->input->post("consultation") != 0) {

                    $inr = $this->db->query("select count(*) as invoiceno from billing where clinic_id='" . $clinic_id . "'")->row();
                    $inv_gen = ($inr->invoiceno) + 1;

                    $receipt_no = 'RECEIPT-' . $info->clinic_id . '-' . $inv_gen;
                    $invoice_no = 'INV-' . $info->clinic_id . '-' . $inv_gen;

                    $billing_master['receipt_no'] = $receipt_no;
                    $billing_master['discount_status'] = "";
                    $billing_master['invoice_no'] = $invoice_no;
                    $billing_master['patient_id'] = $info->patient_id;
                    $billing_master['appointment_id'] = $info->appointment_id;
                    $billing_master['doctor_id'] = $info->doctor_id;
                    $billing_master['clinic_id'] = $info->clinic_id;
                    $billing_master['umr_no'] = $info->umr_no;
                    $billing_master['created_by'] = 1;
                    $billing_master['created_date_time'] = date('Y-m-d H:i:s');
                    $billing_master['modified_by'] = $billing_master['created_by'] = 1;
                    $billing_master['modified_date_time'] = date('Y-m-d H:i:s');

                    if ($datas['patient_payment_status']->payment_status == 0) {
                        $billing_master['billing_type'] = "Registration & Consultation";
                    } else {
                        $billing_master['billing_type'] = "Consultation";
                    }

                    $billing_master['payment_mode'] = $this->input->post("payment_mode");
                    $billing_master['cheque_no'] = $this->input->post("dd_or_cheque_no");
                    $billing_master['refference_no'] = "";
                    $billing_master['deposit_date'] = date("Y-m-d");
                    $billing_master['discount_status'] = "";

                    $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

                    // Inserting billing line items for Registration
                    if ($this->input->post("registration") != 0) {
                        $reg['billing_id'] = $billing_id;
                        $reg['item_information'] = "Registration";
                        $reg['quantity'] = 1;
                        $reg['amount'] = $this->input->post("registration_fee");
                        $reg['created_by'] = $this->session->userdata("user_id");
                        $reg['created_date_time'] = date('Y-m-d H:i:s');
                        $reg['modified_by'] = $this->session->userdata("user_id");;
                        $reg['modified_date_time'] = date('Y-m-d H:i:s');

                        $this->Generic_model->insertData('billing_line_items', $reg);
                    }

                    // Inserting billing line items for Consultation
                    if ($this->input->post("consultation") != 0) {
                        $patient_bank['billing_id'] = $billing_id;
                        $patient_bank['item_information'] = "Consultation";
                        $patient_bank['quantity'] = 1;
                        $patient_bank['amount'] = $this->input->post("consultation_fee");
                        $patient_bank['created_by'] = $this->session->userdata("user_id");;
                        $patient_bank['created_date_time'] = date('Y-m-d H:i:s');
                        $patient_bank['modified_by'] = $this->session->userdata("user_id");;
                        $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');

                        $this->Generic_model->insertData('billing_line_items', $patient_bank);
                    }
                }

                if ($this->input->post("consultation_fee") == 0) {
                    // Update payment status as 2 which means Registration is FREE
                    $as['payment_status']    = 2;
                } else {
                    $as['payment_status']    = 1;
                }

                if ($this->input->post("registration_fee") == 0) {
                    $ps['payment_status']    = 1;
                } else {
                    $ps['payment_status']    = 1;
                }

                $this->Generic_model->updateData('appointments', $as, array(
                    'appointment_id' => $appointment_id
                ));

                $this->Generic_model->updateData('patients', $ps, array(
                    'patient_id' => $patient_id
                ));
            }

            // Procedure billing
            $cart_services = $this->input->post('cart_services');
            $cart_price = $this->input->post('cart_prices');
            $patient_procedure_id = $this->input->post('patient_procedure_id');
            $procedure_id = $this->input->post('procedure_id');

            if (isset($cart_services) && !empty($cart_services)) {

                $pinr = $this->db->query("select count(*) as invoiceno from billing where clinic_id='" . $info->clinic_id . "'")->row();
                $pinv_gen = ($pinr->invoiceno) + 1;
                $preceipt_no = 'RECEIPT-' . $clinic_id . '-' . $pinv_gen;
                $pinvoice_no = 'INV-' . $clinic_id . '-' . $pinv_gen; // invoice no

                $procedure_billing_master['receipt_no'] = $preceipt_no;
                $procedure_billing_master['discount_status'] = "";
                $procedure_billing_master['invoice_no'] = $pinvoice_no;
                $procedure_billing_master['patient_id'] = $patient_id;
                $procedure_billing_master['appointment_id'] = $appointment_id;
                $procedure_billing_master['doctor_id'] = $doctor_id;
                $procedure_billing_master['clinic_id'] = $clinic_id;
                $procedure_billing_master['payment_mode'] = $this->input->post("payment_mode");
                $procedure_billing_master['umr_no'] = $umr_no;
                $procedure_billing_master['created_by'] = $this->session->userdata("user_id");;
                $procedure_billing_master['created_date_time'] = date('Y-m-d H:i:s');
                $procedure_billing_master['modified_by'] = $this->session->userdata("user_id");;
                $procedure_billing_master['modified_date_time'] = date('Y-m-d H:i:s');
                $procedure_billing_master['billing_type'] = "Procedure";

                $procedure_billing_id = $this->Generic_model->insertDataReturnId('billing', $procedure_billing_master);

                for ($i = 0; $i < count($cart_services); $i++) {

                    echo "Cart Service: " . $cart_services[$i] . ", Cart Price: " . $cart_price[$i] . ", Procedure Id: " . $procedure_id[$i];

                    $procedure_billing_line_items['billing_id'] = $procedure_billing_id;
                    $procedure_billing_line_items['item_information'] = $cart_services[$i];
                    $procedure_billing_line_items['quantity'] = 1;
                    $procedure_billing_line_items['amount'] = $cart_price[$i];
                    $procedure_billing_line_items['created_by'] = $user_id;
                    $procedure_billing_line_items['modified_by'] = $user_id;
                    $procedure_billing_line_items['created_date_time'] = date('Y-m-d H:i:s');
                    $procedure_billing_line_items['modified_date_time'] = date('Y-m-d H:i:s');
                    $this->Generic_model->insertData('billing_line_items', $procedure_billing_line_items);

                    // Insert procedure in to patient_procedure table
                    // existing procedure update with payment_status flagged 1
                    if ($patient_procedure_id[$i] != '' || $patient_procedure_id[$i] != NULL) {
                        // Update with payment_status
                        $this->db->where('patient_procedure_id', $patient_procedure_id[$i]);
                        $this->db->update('patient_procedure', array('payment_status' => 1));
                    } else {
                        // Procedure Required Data
                        $procedureData['medical_procedure_id'] = $procedure_id[$i];
                        $procedureData['clinic_id'] = $clinic_id;
                        $procedureData['patient_id'] = $patient_id;
                        $procedureData['doctor_id'] = $doctor_id;
                        $procedureData['appointment_id'] = $appointment_id;
                        $procedureData['payment_status'] = 1;

                        // Insert new record with payment_status flagged 1
                        $this->Generic_model->insertData('patient_procedure', $procedureData);
                    }
                }

                if ($this->input->post("consultation") != 0 && !empty($this->input->post('cart_services'))) {
                    $update_discount['discount_unit'] = $this->input->post("discount_type");
                    $update_discount['discount'] = $this->input->post("discount");
                    $this->Generic_model->updateData('billing', $update_discount, array(
                        'billing_id' => $procedure_billing_id
                    ));
                } else if ($this->input->post("consultation") != 0) {

                    $update_discount['discount_unit'] = $this->input->post("discount_type");
                    $update_discount['discount'] = $this->input->post("discount");
                    $this->Generic_model->updateData('billing', $update_discount, array(
                        'billing_id' => $billing_id
                    ));
                } else {
                    $update_discount['discount_unit'] = $this->input->post("discount_type");
                    $update_discount['discount'] = $this->input->post("discount");
                    $this->Generic_model->updateData('billing', $update_discount, array(
                        'billing_id' => $procedure_billing_id
                    ));
                }

                //redirect("Billing/patient_invoice/".$patient_id."/".$appointment_id);
                redirect("profile/index/" . $patient_id . "/" . $appointment_id);
            } else {

                if ($this->input->post("consultation") != 0 && !empty($this->input->post('cart_services'))) {

                    $update_discount['discount_unit'] = $this->input->post("discount_type");
                    $update_discount['discount'] = $this->input->post("discount");
                    $this->Generic_model->updateData('billing', $update_discount, array(
                        'billing_id' => $procedure_billing_id
                    ));
                } else if ($this->input->post("consultation") != 0) {

                    $update_discount['discount_unit'] = $this->input->post("discount_type");
                    $update_discount['discount'] = $this->input->post("discount");
                    $this->Generic_model->updateData('billing', $update_discount, array(
                        'billing_id' => $billing_id
                    ));
                }
                //redirect("Billing/patient_invoice/".$patient_id."/".$appointment_id);
                redirect("profile/index/" . $patient_id . "/" . $appointment_id);
            }
        }

        $datas['view'] = 'patients/appointment_payment';
        $this->load->view('layout', $datas);
    }

    public function print_invoice($app_id, $billing_id)
    {
        $info = $this->db->query("select * from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.appointment_id='" . $app_id . "'")->row();
        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $info->clinic_id), $order = '');

        $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $info->doctor_id), $order = '');
        $review_deatails = $this->Generic_model->getSingleRecord('clinic_doctor', array('doctor_id' => $info->doctor_id, 'clinic_id' => $info->clinic_id), $order = '');

        $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_deatails->department_id), $order = '');
        $billing_master = $this->Generic_model->getSingleRecord('billing', array('billing_id' => $billing_id), $order = '');
        $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
        $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $info->patient_id), $order = '');

        $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');

        $state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details->state_id), $order = '');

        $data['clinic_logo'] = $clinic_deatails->clinic_logo;
        $data['review_days'] = $review_deatails->review_days;
        $data['clinic_phone'] = $clinic_deatails->clinic_phone;
        $data['clinic_name'] = $clinic_deatails->clinic_name;
        $data['address'] = $clinic_deatails->address;
        $data['doctor_name'] = "Dr. " . strtoupper($doctor_deatails->first_name . " " . $doctor_deatails->last_name);
        $data['qualification'] = $doctor_deatails->qualification;
        $data['department_name'] = $departments->department_name;
        $data['patient_name'] = strtoupper($patient_details->first_name . " " . $patient_details->last_name);
        $data['age_unit'] = $patient_details->age_unit;
        $data['age'] = $patient_details->age;
        $data['gender'] = $patient_details->gender;
        $data['umr_no'] = $patient_details->umr_no;

        if ($patient_details->pincode !== "") {
            $pincode = "," . $patient_details->pincode;
        } else {
            $pincode = "";
        }

        $data['patient_address'] = $patient_details->address_line . " " . $pincode;
        $data['billing'] = $billing;
        $data['billing_master'] = $billing_master;
        $data['invoice_no'] = $billing_master->invoice_no;
        $data['receipt_no'] = $billing_master->receipt_no;
        if ($this->input->post("payment_mode") != "") {
            $data['payment_method'] = $this->input->post("payment_mode");
        } else {
            $data['payment_method'] = $billing_master->payment_mode;
        }

        $data['discount'] = $billing_master->discount;
        $data['discount_type'] = $billing_master->discount_type;

        $pdfFilePath = "billing_" . $info->patient_id . $billing_id . ".pdf";
        $data['file_name'] = $pdfFilePath;

        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='" . $info->clinic_id . "'")->row();

        $this->load->library('M_pdf');
        $html = $this->load->view('billing/generate_billing', $data, true);
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
        $this->m_pdf->pdf->WriteHTML($html, 2);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
        $billFile['invoice_pdf'] = $data['file_name'];

        $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
        if ($billing_master->billing_type != "Investigation") {
            $this->Generic_model->updateData('appointments', $billFile, array('appointment_id' => $id));
        }

        redirect("uploads/billings/" . $pdfFilePath);
    }


    public function invoice_summary($reg_billing_id = '', $procedure_billing_id = '')
    {
        $data['reg_billing'] = $this->db->query("select * from billing b inner join billing_line_items bl on(b.billing_id = bl.billing_id) where b.billing_id='" . $reg_billing_id . "'")->result();
        if ($procedure_billing_id != "") {
            $data['procedure_billing'] = $this->db->query("select * from billing b inner join billing_line_items bl on(b.billing_id = bl.billing_id) where b.billing_id='" . $procedure_billing_id . "'")->result();
            $data['procedure'] = 1;
        } else {
            $data['procedure'] = 0;
        }
        $data['view'] = 'patients/invoice_summary';
        $this->load->view('layout', $data);
    }



    public function search()
    {
        $mobile = DataCrypt($this->input->post('search'), 'encrypt');
        $patients = $this->db->query('SELECT * FROM `patients` where mobile = "'.$mobile.'"')->result();
        $data = '';

        $data .= '<div class = "col-md-12" style="margin: 80px 200px"><div class ="row">';
        for ($i = 0; $i < count($patients); $i++) {
            $data .=  '<div class = "col-md-4"><a href = ' . base_url('profile/index/' . $patients[$i]->patient_id) . ' class = "card" style = "height:150px;border: 1px solid #ddd;width: 500px;"><div class = "row" style="padding:10px"><div class = "col-md-3"><img src=' . base_url('assets/img/avtar-2.png') . '></div><div class = "col-md-9"><div><b><span style="font-size: 13px;color: black;">Name:</span><span style="font-size: 13px;color: black;">' . $patients[$i]->first_name . '</span></b></div><div><b><span style="font-size: 13px;color: black;">Gender:</span><span style="font-size: 13px;color: black;">' . $patients[$i]->gender . '</span></b></div><div><b><span style="font-size: 13px;color: black;">Mobile:</span><span style="font-size: 13px;color: black;">' . DataCrypt($patients[$i]->mobile, 'decrypt') . '</span></b></div><div><b><span style="font-size: 13px;color: black;">UMR:</span><span style="font-size: 13px;color: black;">' . $patients[$i]->umr_no . '</span></b></div></div></div></a></div>';
        }
        $data .= '</div></div>';
        echo $data;
        $data['view'] = 'patients/admin_patients_search';
        $this->load->view('layout', $data);
    }
}
