<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Checklist_department extends MY_Controller {

	public function __construct() 
	{
	    parent::__construct();
	}

	public function index(){
         $data['consentform_list']=$this->Generic_model->getAllRecords("consent_form");  
    	 $data['view'] = 'checklist/checklist_department';
    	 $this->load->view('layout', $data);
	}

    public function getSelectedChecklist(){
        extract($_POST);
        $checklist=$this->db->query("select * from checklist_master")->result();
        ?>
        <input type="hidden" name="consent_form_id" value="<?=$consent_form_id?>">
        <div class="row">
        <?php  
        foreach ($checklist as $value) 
        {
            $selected_check = $this->db->query("select * from checklist_consent_form where checklist_id='".$value->checklist_id."' and patient_consent_form_id='".$consent_form_id."'")->row();
            ?>
            <div class="col-md-4">
                <input type="hidden" name="check[]" value="<?=$selected_check->checklist_consent_form_id?>">
                <input type="checkbox" name="checklist[]" value="<?=$value->checklist_id?>" <?=($value->checklist_id==$selected_check->checklist_id)?'checked':''?> > 
                <span <?=($value->type=="title")?'style="font-weight:bold"':''?> ><?=$value->description?></span>
                <p>
                    <input type="radio" name="category_<?=$value->checklist_id?>" value="Before" <?=($selected_check->category=="Before")?'checked':''?> > Before
                    <input type="radio" name="category_<?=$value->checklist_id?>" value="during" <?=($selected_check->category=="during")?'checked':''?> > During
                    <input type="radio" name="category_<?=$value->checklist_id?>" value="after" <?=($selected_check->category=="after")?'checked':''?> > After
                </p>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
    }

    public function checklist_update($id){
        $data['consentformInfo'] = $this->db->query("select * from consent_form where consent_form_id='".$id."'")->row();
        $before = $this->db->query("select checklist_id from checklist_consent_form where patient_consent_form_id='".$id."' and category='before'")->result();
        foreach ($before as $value) 
        {
            $data['before_ids'][] = $value->checklist_id;
        }
        $after = $this->db->query("select checklist_id from checklist_consent_form where patient_consent_form_id='".$id."' and category='after'")->result();
        foreach ($after as $value) 
        {
            $data['after_ids'][] = $value->checklist_id;
        }
        $during = $this->db->query("select checklist_id from checklist_consent_form where patient_consent_form_id='".$id."' and category='during'")->result();
        foreach ($during as $value) 
        {
            $data['during_ids'][] = $value->checklist_id;
        }

        $data['checklist_master'] = $this->db->query("select * from checklist_master")->result();
        $data['updatedCount'] = $this->db->query("select count(*) as count from checklist_consent_form where patient_consent_form_id='".$id."'")->row();

        $data['view'] = "checklist/checklist_mapping_update";
        $this->load->view('layout',$data);
    }

    public function save_checklist($id){  
        if(isset($_POST))
        {
            extract($_POST);

            foreach ($before as $value) {
                $data['patient_consent_form_id'] = $id;
                $data['checklist_id'] = $value;
                $data['category'] = "Before";
                $data['created_by'] = $this->session->userdata("user_id");
                $data['modified_by'] = $this->session->userdata("user_id");
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $check = $this->db->query("select count(*) as count from checklist_consent_form where patient_consent_form_id='".$id."' and checklist_id='".$value."' and category='Before'")->row();
                if($check->count == 0)
                {
                    $this->Generic_model->insertData("checklist_consent_form",$data);
                }
                else
                {
                    $this->db->set($data);
                    $this->db->where('patient_consent_form_id',$id);
                    $this->db->where('checklist_id',$value);
                    $this->db->where('category','Before');
                    $this->db->update('checklist_consent_form',$data);
                }
            }
            foreach ($during as $value1) {
                $data1['patient_consent_form_id'] = $id;
                $data1['checklist_id'] = $value1;
                $data1['category'] = "during";
                $data1['created_by'] = $this->session->userdata("user_id");
                $data1['modified_by'] = $this->session->userdata("user_id");
                $data1['created_date_time'] = date("Y-m-d H:i:s");
                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                $check = $this->db->query("select count(*) as count from checklist_consent_form where patient_consent_form_id='".$id."' and checklist_id='".$value1."' and category='during'")->row();
                if($check->count == 0)
                {
                    $this->Generic_model->insertData("checklist_consent_form",$data1);
                }
                else
                {
                    $this->db->set($data1);
                    $this->db->where('patient_consent_form_id',$id);
                    $this->db->where('checklist_id',$value1);
                    $this->db->where('category','after');
                    $this->db->update('checklist_consent_form',$data1);
                }
            }
            foreach ($after as $value2) {
                $data2['patient_consent_form_id'] = $id;
                $data2['checklist_id'] = $value2;
                $data2['category'] = "after";
                $data2['created_by'] = $this->session->userdata("user_id");
                $data2['modified_by'] = $this->session->userdata("user_id");
                $data2['created_date_time'] = date("Y-m-d H:i:s");
                $data2['modified_date_time'] = date("Y-m-d H:i:s");
                $check = $this->db->query("select count(*) as count from checklist_consent_form where patient_consent_form_id='".$id."' and checklist_id='".$value2."' and category='after'")->row();
                if($check->count == 0)
                {
                    $this->Generic_model->insertData("checklist_consent_form",$data2);
                }
                else
                {
                    $this->db->set($data2);
                    $this->db->where('patient_consent_form_id',$id);
                    $this->db->where('checklist_id',$value2);
                    $this->db->where('category','after');
                    $this->db->update('checklist_consent_form',$data2);
                }
            }
        redirect("Checklist_department");
        }

    }
}
?>