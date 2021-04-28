<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Forms extends REST_Controller1
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

    public function formlist_get()
    {
        $formInfo = $this->db->query("select id,form_name from 
        form_list")->result();

        if(count($formInfo) > 0)
        {
            $i = 0;
            foreach ($formInfo as $value) 
            {
                $param['form_list'][$i]['form_id'] = $value->id;
                $param['form_list'][$i]['form_name'] = $value->form_name;
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'FormList','result'=>$param));
        }
        else{
            $this->response(array('code'=>'201','message'=>'No Form List'));
        }
    }

    public function formlist_details_get($form_id,$search)
    {
        $form_list = $this->db->query("select *
        from form_list_line_items 
        where form_list_id='".$form_id."'")->result();

        if(count($form_list)>0)
        {
           
            // $this->db->query("select id,name,form_name as type from  form_list_line_items a 
            // join form_list b on a.form_list_id=b.id where name LIKE '".urldecode($search)."%' LIMIT 20")->result();
            $form_list_details = $this->db->select("a.id,a.name,b.form_name as type,b.id as type_id")
            ->from("form_list_line_items a")
            ->join("form_list b","a.form_list_id=b.id")
            ->where("a.form_list_id=",$form_id)
            ->where("a.name LIKE '".urldecode($search)."%' LIMIT 20")
            
            // ->where("a.form_list_id='".$form_id."'")
            ->get()
            ->result();
            if(count($form_list_details)>0)
            {
                $i=0;
                foreach($form_list_details as $value)
                {
                    $data['form_list_details'][$i]['id'] = $value->id;
                    $data['form_list_details'][$i]['name'] =  $value->name;
                    $data['form_list_details'][$i]['type'] =  $value->type;
                    $data['form_list_details'][$i]['type_id'] =  $value->type_id;
                    $i++;
                }
                $this->response(array('code'=>'200','message'=>'FormListData','result'=>$data));
            }
            else{
                $this->response(array('code'=>'200','message'=>'No Data Found','result'=>'No data Found'));
            }
           
        }
        else{
            $this->response(array('code'=>'201','message'=>'No Data Found'));
        }
    }

    public function patient_form_save_post()
    {
        if(isset($_POST))
        {
           extract($_POST);

           $form_list_details = $this->db->select("*")
           ->from("patient_form_list a")
           ->where("a.appointment_id='".$appointment_id."'")
           ->where("a.doctor_id='".$doctor_id."'")
           ->where("a.patient_id='".$patient_id."'")
           ->where("a.clinic_id='".$clinic_id."'")
           ->where("a.form_list_id='".$form_list_id."'")
           ->where("a.form_list_line_item_id='".$form_list_line_item_id."'")
           ->get()
           ->row();
            if(count($form_list_details)>0)
            {
                $this->response(array('code'=>'201','message'=>'Already Exsits','result'=>'Already Exists'));
            }
            else
            {
                $data['patient_id'] = $patient_id;
                $data['clinic_id'] = $clinic_id;
                $data['appointment_id'] = $appointment_id;
                $data['doctor_id'] = $doctor_id;
                $data['form_list_id'] = $form_list_id;
                $data['form_list_line_item_id'] = $form_list_line_item_id;
                $data['name'] = $name;
                $doctor_form_list_description = $this->db->query("select * from 
                doctor_form_list where doctor_id='".$doctor_id."' and form_list_line_item_id='".$form_list_line_item_id."'")->row();
                if(count($doctor_form_list_description)>0){
                    $data['description'] = $doctor_form_list_description->description;
                }else{
                    $patient_form_list_description = $this->db->query("select * from 
                    form_list_line_items where id='".$form_list_line_item_id."'")->row();
                    $data['description'] = $patient_form_list_description->description;
                }
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $data['patient_form_save_id'] = $this->Generic_model->insertDataReturnId("patient_form_list",$data);
                $param['patient_form_save_id']=  $data['patient_form_save_id'];
                $param['name']=  $name;
                $this->response(array('code'=>'200','message'=>'Successfully Saved','result'=>$param));
            }
        
        }
    }

    public function doctor_saveastemplate_post()
    {
        if(isset($_POST))
        {
           extract($_POST);

           $doctor_form_list = $this->db->select("*")
           ->from("doctor_form_list a")
           ->where("a.doctor_id='".$doctor_id."'")
           ->where("a.clinic_id='".$clinic_id."'")
           ->where("a.form_list_line_item_id='".$form_list_line_item_id."'")
           ->get()
           ->row();
            if(count($doctor_form_list)>0)
            {
                $data['clinic_id'] = $clinic_id;
                $data['doctor_id'] = $doctor_id;
                $data['form_list_line_item_id'] = $form_list_line_item_id;
                $data['name'] = $name;
                $data['description'] = $description;
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->updateData('doctor_form_list',$data,array('id'=>$doctor_form_list->id));
                $this->response(array('code'=>'200','message'=>'Successfully Saved','result'=>$param));
            }
            else
            {
                $data['clinic_id'] = $clinic_id;
                $data['doctor_id'] = $doctor_id;
                $data['form_list_line_item_id'] = $form_list_line_item_id;
                $data['name'] = $name;
                $data['description'] = $description;
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $data['doctor_form_save_id'] = $this->Generic_model->insertDataReturnId("doctor_form_list",$data);
                $param['doctor_form_save_id']=  $data['doctor_form_save_id'];
                $this->response(array('code'=>'200','message'=>'Successfully Saved','result'=>$param));
            }
        
        }
    }

    public function getDescription_get($id)
    {
    
        $form_list_details = $this->db->select("*")
        ->from("patient_form_list a")
        ->where("a.id='".$id."'")
        ->get()
        ->row();
        if(count($form_list_details)>0)
        {
            $data['form_details'][0]['id'] = $form_list_details->id;
            $data['form_details'][0]['name'] = $form_list_details->name;
            $data['form_details'][0]['description'] =  $form_list_details->description;
            $this->response(array('code'=>'200','message'=>'form_data','result'=>$data));
        }
        else{
            $this->response(array('code'=>'201','message'=>'No data found','result'=>'No data found'));
        }
       
    }


    public function deleteForm_post()
    {
        extract($_POST);

        $form_list_details = $this->db->select("*")
        ->from("patient_form_list a")
        ->where("a.id='".$id."'")
        ->get()
        ->row();
        if(count($form_list_details)>0)
        {
            $this->Generic_model->deleteRecord('patient_form_list',array('id'=>$id));
            $this->response(array('code'=>'200','message'=>'Deleted Successfully','result'=>'Success'));
        }
        else{
            $this->response(array('code'=>'201','message'=>'No Id Found','result'=>'No Id Found'));
        }
       
    }

    public function editForm_post()
    {
        extract($_POST);

        $form_list_details = $this->db->select("*")
        ->from("patient_form_list a")
        ->where("a.id='".$id."'")
        ->get()
        ->row();
        if(count($form_list_details)>0)
        {
            $data['description']=$description;
            $this->Generic_model->updateData('patient_form_list',$data,array('id'=>$id));
            $this->response(array('code'=>'200','message'=>'Successfully Updated','result'=>$data));
        }
        else{
            $this->response(array('code'=>'201','message'=>'No Id Found','result'=>'No Id Found'));
        }
       
    }

    public function getFormList_get($appointment_id)
    {
    
        $form_list_details = $this->db->select("*")
        ->from("patient_form_list a")
        ->where("a.appointment_id='".$appointment_id."'")
        // ->where("a.form_list_id='".$form_id."'")
        ->get()
        ->result();

        if(count($form_list_details)>0)
        {
            $i=0;
            foreach($form_list_details as $value)
            {
            $data['form_details'][$i]['id'] = $value->id;
            $data['form_details'][$i]['name'] = $value->name;
            $i++;
            }
            // $data['form_details'][0]['description'] =  $form_list_details->description;
            $this->response(array('code'=>'200','message'=>'form_data','result'=>$data));
        }
        else{
            $this->response(array('code'=>'201','message'=>'No data found','result'=>'No data found'));
        }
       
    }

}
?>