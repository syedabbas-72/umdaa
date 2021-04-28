<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Investigations extends REST_Controller1
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


    // public function 


    public function userDevices_get(){
        $usersInfo = $this->Generic_model->getAllRecords('users');
        foreach($usersInfo as $value){
            if($value->fcm_id == "")
                continue;
            $data['fcm_id'] = $value->fcm_id;
            $data['device_id'] = $value->device_id;
            $data['user_id'] = $value->user_id;
            $data['last_login_time'] = date('Y-m-d H:i:s');
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertDataReturnId('users_device_info', $data);
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }
    }

    //Drug Search
    public function index_get($id = ' ')
    {
        if(!empty(isset($_GET)))
        {
            extract($_GET);
            if($id != " ")
            {
                $cdInfo = $this->db->query("select investigation_id,item_code,investigation from investigations where investigation LIKE '%".$id."%'")->result();
                if(count($cdInfo)>0)
                {
                    $data = $cdInfo;
                }
                else
                {
                    $data = [];
                }
                $data = $cdInfo;
                $this->response($data);
            }
        }
        else
        {
            $data = "UnAuthorized Access";
            $this->response(array('code'=>'201','message'=>'Error Occured'));
        }
    }

    public function androidInvestigation_get($clinic_id,$id = ' ')
    {
        if(!empty(isset($_GET)))
        {
            extract($_GET);
            if($id != " ")
            {
                $cdInfo = $this->db->query("select * from clinic_investigations where clinic_id='".$clinic_id."'")->row();
                $i = 0;
                $invCount = 0;
                if (count($cdInfo) > 0) {
                    $invInfo = $this->db->query('select * from clinic_investigations ci,investigations i where ci.investigation_id=i.investigation_id and i.investigation LIKE "%'.urldecode($id).'%" LIMIT 20')->result();
                    foreach($invInfo as $inv)
                    {
                        $checkInvClinic = $this->db->query("select clinic_investigation_price.clinic_investigation_id,clinic_investigation_price.price,clinic_investigation_price.clinic_id,clinic_investigation_price.investigation_id from  clinic_investigation_price join clinic_investigations  on clinic_investigations.clinic_investigation_id = clinic_investigation_price.clinic_investigation_id  where clinic_investigations.clinic_id='".$clinic_id."' and  clinic_investigations.investigation_id='". $inv->investigation_id."'")->row();
                        $getLabType = $this->db->query("select * from  lab_departments  where lab_department_id='". $inv->lab_department_id."'")->row();
                        // echo $this->db->last_query();
                        if(count($checkInvClinic)>0)
                        {
                            $data['investigation_object']['investigations'][$i]['category'] = $getLabType->department_name;
                            $data['investigation_object']['investigations'][$i]['investigation_id'] = $inv->investigation_id;
                            $data['investigation_object']['investigations'][$i]['investigation_code'] = $inv->item_code;
                            $data['investigation_object']['investigations'][$i]['investigation_name'] = $inv->investigation;
                            $data['investigation_object']['investigations'][$i]['status'] ='1';
                            $data['investigation_object']['investigations'][$i]['mrp'] =$checkInvClinic->price;
                            $data['investigation_object']['investigations'][$i]['clinic_investigation_id'] =$inv->clinic_investigation_id;
                        }
                        else
                        {
                            $data['investigation_object']['investigations'][$i]['category'] = $getLabType->department_name;
                            $data['investigation_object']['investigations'][$i]['investigation_id'] = $inv->investigation_id;
                            $data['investigation_object']['investigations'][$i]['investigation_code'] = $inv->item_code;
                            $data['investigation_object']['investigations'][$i]['investigation_name'] = $inv->investigation;
                            $data['investigation_object']['investigations'][$i]['status'] ='1';
                            $data['investigation_object']['investigations'][$i]['mrp'] =0;
                            $data['investigation_object']['investigations'][$i]['clinic_investigation_id'] ='';
                        }
                        $ids[$i] = $inv->investigation_id;
                        $i++;
                    }
                    $invCount = count($invInfo); 
                }
                else
                {
                    // echo "here";
                    $Inv = $this->db->query('select * from investigations where investigation LIKE "%'.urldecode($id).'%" LIMIT 20')->result();
                    // echo $this->db->last_query();
                    $i = 0;
                    $invCount = count($Inv);
                    foreach($Inv as $value)
                    {
                        $getLabType = $this->db->query("select * from  lab_departments  where lab_department_id='". $value->lab_department_id."'")->row();
                        $data['investigation_object']['investigations'][$i]['category'] = $getLabType->department_name;
                        $data['investigation_object']['investigations'][$i]['investigation_id'] = $value->investigation_id;
                        $data['investigation_object']['investigations'][$i]['investigation_code'] = $value->item_code;
                        $data['investigation_object']['investigations'][$i]['investigation_name'] = $value->investigation;
                        $data['investigation_object']['investigations'][$i]['status'] ='0';
                        $data['investigation_object']['investigations'][$i]['mrp'] =0;
                        $data['investigation_object']['investigations'][$i]['clinic_investigation_id'] ='';
                        $i++;
                    }
                }

                if($invCount < 20)
                {
                    $twenty = 20;
                    $remCount = $twenty-$invCount;
                    $Inv = $this->db->query('select * from investigations where investigation LIKE "%'.urldecode($id).'%" LIMIT '.$remCount)->result();
                    $i = $invCount;
                    $invCount = count($Inv);
                    $j = 0;
                    foreach($Inv as $value)
                    {
                        if(in_array($value->investigation_id, $ids))
                        {
                            continue;
                        }
                        else
                        {
                            $getLabType = $this->db->query("select * from  lab_departments  where lab_department_id='". $value->lab_department_id."'")->row();
                            $data['investigation_object']['investigations'][$i]['category'] = $getLabType->department_name;
                            $data['investigation_object']['investigations'][$i]['investigation_id'] = $value->investigation_id;
                            $data['investigation_object']['investigations'][$i]['investigation_code'] = $value->item_code;
                            $data['investigation_object']['investigations'][$i]['investigation_name'] = $value->investigation;
                            $data['investigation_object']['investigations'][$i]['status'] ='0';
                            $data['investigation_object']['investigations'][$i]['mrp'] =0;
                            $data['investigation_object']['investigations'][$i]['clinic_investigation_id'] = 0;
                        }
                        
                        $i++;$j++;
                    }
                }
            }
            else
            {
                $data['investigation_object']['investigations'] = [];
            }
                $this->response(array('code' => '200', 'message' => 'Investigations Info', 'result' => $data, 'requestname' => 'Investigations'));
        }
        else
        {
            $data = "UnAuthorized Access";
            $this->response(array('code'=>'201','message'=>'Error Occured'));
        }       // echo $clinic_id;
       
    }
}
?>