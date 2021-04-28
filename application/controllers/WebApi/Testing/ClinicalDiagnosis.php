<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class ClinicalDiagnosis extends REST_Controller1
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

    //Drug Search
    public function index_get($id = ' ')
    {
        if(!empty(isset($_GET)))
        {
            extract($_GET);
            if($id != " ")
            {
                $cdInfo = $this->db->query("select clinical_diagnosis_id,disease_name,code from clinical_diagnosis where disease_name LIKE '%".urldecode($id)."%' limit 20")->result();
                // echo $this->db->last_query();
                
                // $data = $cdInfo;
                if(count($cdInfo)>0)
                {
                    $data = $cdInfo;
                }
                else{
                    $data=[];
                }
                $this->response($data);
            }
        }
        else
        {
            $data = "UnAuthorized Access";
            $this->response(array('code'=>'201','message'=>'Error Occured'));
        }
    }

    public function androidclinicalDiagnosis_get($id = ' ')
    {
        if(!empty(isset($_GET)))
        {
            extract($_GET);
            if($id != " ")
            {
                $cdInfo = $this->db->query("select clinical_diagnosis_id,disease_name,code 
                from clinical_diagnosis where disease_name LIKE '%".urldecode($id)."%' limit 20")->result();
                // echo $this->db->last_query();
                
                // $data = $cdInfo;
                if(count($cdInfo)>0)
                {
                    $data['clinical_diagnosis_object']['clinical_diagnosis'] = $cdInfo;
                }
                else{
                    $data['clinical_diagnosis_object']['clinical_diagnosis']=[];
                }
                // $this->response($data);
                // $data['clinical_diagnosis_object']['clinical_diagnosis'] = $cdInfo;
                $this->response(array('code' => '200', 'message' => 'clinical_diagnosis Info', 'result' => $data, 'requestname' => 'clinical_diagnosis'));
            }
        }
        else
        {
            $data = "UnAuthorized Access";
            $this->response(array('code'=>'201','message'=>'Error Occured'));
        }       // echo $clinic_id;
       
    }
}
?>