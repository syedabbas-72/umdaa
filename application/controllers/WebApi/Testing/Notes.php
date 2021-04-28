<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Notes extends REST_Controller1
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

    public function save_notes_post()
    {
        if(isset($_POST))
        {
            extract($_POST);
            $check = $this->db->query("select * from patient_notes where appointment_id='".$appointment_id."'")->row();
            if(count($check)>0)
            {
                $data1['patient_notes_id'] = $check->patient_notes_id;
                $data1['note_details'] = $note_details;
                $data1['created_by'] = $doctor_id;
                $data1['modified_by'] = $doctor_id;
                $data1['created_date_time'] = date("Y-m-d H:i:s");
                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('patient_notes_line_items',$data1);
                // $note_detailss = $this->db->query("select * from patient_notes_line_items where patient_notes_id='".$check->patient_notes_id."'")->row();
                // $data1['note_details'] = $note_details;
                // $data1['modified_date_time'] = date("Y-m-d H:i:s");
                // $this->Generic_model->updateData("patient_notes_line_items",
                //  $data1,
                // array('patient_notes_line_item_id' => $note_detailss->patient_notes_line_item_id));
                $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Updated'));
            }
           else
           {
                $data['patient_id'] = $patient_id;
                $data['appointment_id'] = $appointment_id;
                $data['clinic_id'] = $clinic_id;
                $data['doctor_id'] = $doctor_id;
                $data['umr_no'] = $umr_no;
                $data['status'] ='1';
                $data['created_by'] = $doctor_id;
                $data['modified_by'] = $doctor_id;
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $cdw_id = $this->Generic_model->insertDataReturnId("patient_notes",$data);
                $data1['patient_notes_id'] = $cdw_id;
                $data1['note_details'] = $note_details;
                $data1['created_by'] = $doctor_id;
                $data1['modified_by'] = $doctor_id;
                $data1['created_date_time'] = date("Y-m-d H:i:s");
                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('patient_notes_line_items',$data1);
                $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Added'));
           }
        }
    }

    public function get_notes_get($appointment_id)
    {
        $notes = $this->db->query("select * from patient_notes where appointment_id='".$appointment_id."' 
        ")->row();
        if(count($notes)>0)
        {
            $note_details = $this->db->query("select * from patient_notes_line_items where patient_notes_id='".$notes->patient_notes_id."'")->result();
            if(count($note_details)>0)
            {
                $c=0;
                foreach($note_details as $notes)
                {
                    $data['patient_notes'][$c]['patient_notes_line_item_id'] = $notes->patient_notes_line_item_id;
                    $data['patient_notes'][$c]['patient_notes_id'] = $notes->patient_notes_id;
                    $data['patient_notes'][$c]['note_details'] = $notes->note_details;
                    $c++;  
                }
                $this->response(array('code'=>'200','message'=>'notes_details','result'=>$data));
            }else{
                $data['patient_notes']=[];
                $this->response(array('code'=>'200','message'=>'notes_details','result'=>$data));
            }
         
        }else{
            $this->response(array('code'=>'201','message'=>'Appointment Id Not Found','result'=>'Appointment Id Not Found'));
        }
    }

    public function getSuggestions_get($appointment_id){
        extract($parameters);
        $docId = $this->db->query("select * from
        appointments where appointment_id='".$appointment_id."'")->row();
        if(empty($appointment_id))
        {
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>'Send The Parameters','requestname'=>$method));
        }
        else
        {
            
            $cdInfo = $this->db->query(" select * from (select pcdl.note_details,pcdl.patient_notes_id, count(*) as count from patient_notes pcd,patient_notes_line_items pcdl where pcd.patient_notes_id=pcdl.patient_notes_id and pcd.doctor_id='".$docId->doctor_id."' and pcd.clinic_id='".$docId->clinic_id."' group by pcdl.note_details order by count DESC LIMIT 15) as pcd order by note_details ASC")->result();
           
            if(count($cdInfo)>0)
            {
                $i = 0;
                foreach($cdInfo as $value)
                {
                    $data['notesSuggestions'][$i]['patient_notes_id'] = $value->patient_notes_id;
                    $data['notesSuggestions'][$i]['note_details'] = $value->note_details;
                    $i++;
                }
                // asort($data['cdSuggestions'], SORT_STRING);
            }
            else
            {
                $data['notesSuggestions'] = [];
            }
            $this->response(array('code'=>'200','message'=>'Success','result'=>$data,'requestname'=>$method));
        }
    }

    public function editNotes_post()
    {
        if(isset($_POST))
        {
            extract($_POST);
            $check = $this->db->query("select * from patient_notes_line_items where patient_notes_line_item_id='".$patient_notes_line_item_id."'")->row();
            // $check = $this->db->query("select * from patient_notes where appointment_id='".$appointment_id."'")->row();
            if(count($check)>0)
            {
                $note_detailss = $this->db->query("select * from patient_notes_line_items where patient_notes_id='".$check->patient_notes_id."'")->row();
                $data1['note_details'] = $note_details;
                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->updateData("patient_notes_line_items",
                 $data1,
                array('patient_notes_line_item_id' => $patient_notes_line_item_id));
                $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Updated'));
            }else{
                $this->response(array('code'=>'201','message'=>'Id Not Found','result'=>'Id Not Found'));
            }
        }
    }

    public function deleteNotes_post()
    {
        if(isset($_POST))
        {
            extract($_POST);
            $check = $this->db->query("select * from patient_notes_line_items where patient_notes_line_item_id='".$patient_notes_line_item_id."'")->row();
            if(count($check)>0)
            {                
                $res = $this->Generic_model->deleteRecord('patient_notes_line_items',
                array('patient_notes_line_item_id'=>
                $patient_notes_line_item_id));
                $this->response(array('code'=>'200','message'=>'Delete Successfully','result'=>'Delete Successfully'));
            }else{
                $this->response(array('code'=>'201','message'=>'Id Not Found','result'=>'Id Not Found'));
            }
        }
    }
}
?>