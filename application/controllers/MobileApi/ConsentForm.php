<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class ConsentForm extends REST_Controller1
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

    public function searchForm_get($department_id,$search)
    {
        $consent_forms = $this->db->select("a.consent_form_id, a.consent_form_title, b.department_id")
        ->from("consent_form a")
        ->join("consent_form_department b", "a.consent_form_id = b.consent_form_id")
        ->where("b.department_id='" . $department_id. "'")
        ->where("a.consent_form_title LIKE '".urldecode($search)."%' LIMIT 20")
        ->get()->result();

        if(count($consent_forms)>0)
        {
            $i=0;
            foreach($consent_forms as $value)
            {
                $data['consent_form'][$i]['consent_form_id'] = $value->consent_form_id;
                $data['consent_form'][$i]['consent_form_title'] =  $value->consent_form_title;
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Consent Form Data','result'=>$data));
        }
        else{
            $data['consent_form'] ="No data Found";
            $this->response(array('code'=>'201','message'=>'No Data Found','result'=>$data));
        }
    }
}
?>