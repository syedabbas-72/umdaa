<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class FormBuilder extends MY_Controller {
public function __construct() 
{
    parent::__construct();
	$this->load->library('mail_send', array('mailtype'=>'html'));		 

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
}

public function index(){
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "archieve=0";
	$data['forms_list']=$this->Generic_model->getAllRecords('form',$cond,$order='');
	$data['view'] = 'Formbuilder/forms_list';
    $this->load->view('layout', $data);
}

public function form_add(){
	
	
	$user_id=$this->session->userdata('user_id');
	$clinic_id = $this->session->userdata('clinic_id');
	if($this->input->post('submit')){
		
			$forminsert = $_POST;
			
			$forminsert['created_by']=$user_id;
			$forminsert['modified_by']=$user_id;
			$forminsert['created_date_time'] = date("Y-m-d H:i:s");
			$forminsert['modified_date_time'] = date("Y-m-d H:i:s");
			unset($forminsert['submit']);			
			$ok=$this->Generic_model->insertData("form",$forminsert);
			if($ok){
				
				redirect('FormBuilder');
			}	
	}else{
		$data['departments']=$this->Generic_model->getAllRecords('department',array('status'=>1),$order='');
		$data['view'] = 'Formbuilder/form_add';
		$this->load->view('layout', $data);
	}
	
}

public function form_delete($form_id=''){
   $form_info['archieve'] = 1;
   $ok=$this->Generic_model->updateData('form', $form_info, array('form_id'=>$form_id));
   if($ok){
   redirect('FormBuilder');
   }
	
}

public function form_section($form_id=''){
	$data['form_id']=$form_id;
	$data['view'] = 'Formbuilder/form-section-add';
	$this->load->view('layout', $data);
	
	
}

public function saveFormSection(){
	
	
	// get count of sections to give position to the section
	$getCount = $this->Generic_model->getNumberOfRecords('section',array('form_id'=>$_POST['section']['form_id']));

	if($getCount){
		$_POST['section']['position'] = $getCount + 1;
	}else{
		$_POST['section']['position'] = 1;
	}

	// create section
	// get last inserted section id
	
	
	
	$section_id =$this->Generic_model->insertDataReturnId('section',$_POST['section']);

	// if section inserted
	// then insert section fields 	
 	if($section_id){ 
 		
 		// record[field] carries field table entries
 		// get number of field records by counting record array items
 		$records = $_POST['record'];

 		foreach($records as $key => $fieldRecord) {

 			// add section_id field to record array
 			$fieldRecord['field']['section_id'] = $section_id;

 			// check dependency existing
 			// if exist update parent_field_id & parent_option_id
 			if(isset($fieldRecord['field']['parent_field_name'])) {

 				// get field_id with section_id and parent_field_name to update parent_field_id
				$parent_field_id_res=$this->Generic_model->getValue('field','field_id',array('section_id'=>$section_id, 'field_name' => $fieldRecord['field']['parent_field_name']));
 				$fieldRecord['field']['parent_field_id'] = $parent_field_id_res['field_id'];

 				// get option_id with section_id and parent_option_name to update parent_option_id
				
				$parent_option_id_res=$this->Generic_model->getValue('field_option','option_id',array('field_id'=>$fieldRecord['field']['parent_field_id'], 'option_name' => $fieldRecord['field']['parent_option_name']));
 				$fieldRecord['field']['parent_option_id'] =$parent_option_id_res['option_id'];

 				// unset parent_field_name & parent_option_name
 				unset($fieldRecord['field']['parent_field_name']);
 				unset($fieldRecord['field']['parent_option_name']);

 			}

 			// insert field record into field table
 			$field_id = $this->Generic_model->insertDataReturnId('field',$fieldRecord['field']);

 			// if field inserted
 			// then insert options into option db
 			if($field_id) {
 				
 				foreach($fieldRecord['options'] as $key => $optionRecord) {
 					// add field_id to options array
 					$optionRecord['field_id'] = $field_id;

 					// insert option records
 					$option_id = $this->Generic_model->insertDataReturnId('field_option',$optionRecord);
 				}
 			}
 		}
 	}
	
	redirect('FormBuilder');
}


public function display_form($form_id=''){	

	$form_res=$this->Generic_model->getSingleRecord('form',array('form_id'=>$form_id),'');	
	$data['form_sections']=$this->Generic_model->getAllRecords('section',array('form_id'=>$form_id),$order='');
	$data['form_id']=$form_res->form_id;
	$data['form_name']=$form_res->form_name;
	

	$data['view'] = 'Formbuilder/dynamic_form_display';
	$this->load->view('layout', $data);
}

function getCheck(){
$res=$this->Generic_model->selectRecord('field_option','*',array('field_id'=>1),'');
	$rec = $res->row_array();	
	extract($rec);
	echo $option_name;
}


function getField($field_rec) {
	
		$fieldHtml = "";		

		// Div for field name & field options
		$fieldHtml .= "<div class='form_group field_label'><span style='color:#000'>".$field_rec->field_name.": ";	

		// create a hidden text for to save the field_id
		$fieldHtml .= "<input type='hidden' name='field_id' value='".$field_rec->field_id."'>";

		// get field_type and get field options if the type is not text
		if($field_rec->field_type == 'text'){
			$fieldHtml .= "<input type='text' class='form-control' id='".$field_rec->field_id."_tb' name='field_value' value='' style='margin-bottom:15px; border-radius:4px;' >";
		}else{
			// field type may be a checkbox OR radio 
			// get options for the fields
			$option_res = $this->Generic_model->selectRecord('field_option','*',array('field_id'=>$field_rec->field_id))->result();

			// if options exists
			if(count($option_res) >0){

				$dependencyFields = array();

				$fieldHtml .= "<div style='clear:both; padding:10px 0px 0px 0px; margin:0px 0px 10px 0px;'>";

				//while($option_rec = $option_res->row_array()){
					foreach($option_res as $key2=>$value2){

					if($value2->option_default)
						$checked = "checked";
					else
						$checked = "";

					if($field_rec->field_type == 'radio'){
						$fieldHtml .= "<label class='".$field_rec->field_type."-inline' style='margin-right:10px; padding-right:10px;'><input type='".$field_rec->field_type."' id='".$value2->option_id."_".$field_rec->field_type."' name='".$field_rec->field_id."_radio' ".$checked." data-target-id='".$value2->option_id."' onchange=\"return showHide(this.id,'".$field_rec->field_id."_div');\"> ".$value2->option_name."</label>";
					}else{
						$fieldHtml .= "<label class='".$field_rec->field_type."-inline' style='margin-right:10px; padding-right:10px; min-width:180px;'><input type='".$field_rec->field_type."' id='".$value2->option_id."_".$field_rec->field_type."' name='".$field_rec->field_id."_radio' ".$checked." data-target-id='".$value2->option_id."' onchange=\"return dependencyDiv(this.id, '".$value2->option_id."_div');\"> ".$value2->option_name."</label>";
					}
					// get dependency existence
					$dependencyRes =$this->Generic_model->selectRecord('field','field_id, field_name, field_type, parent_field_id, parent_option_id', array('parent_field_id'=>$field_rec->field_id,'parent_option_id'=>$value2->option_id));

					if($dependencyRes->num_rows()){
						// get dependency fields
						$dependencyFields[$value2->option_id] = $this->getDependencyFields($field_rec->field_id,$value2->option_id);
					}
								
				}

				$fieldHtml .= "</div>";

				foreach($dependencyFields as $key => $value){
					$fieldHtml .= $dependencyFields[$key];	
				}	
			}							
		}

		$fieldHtml .= "</div>";

		return $fieldHtml;
		
	}

	function getDependencyFields($parent_field_id, $parent_option_id){

		// Taking Parent Option id as an array key 
		$arrayKey = $parent_option_id;
		$html = "";

		// get dependency existence from parent field id & parent option id
		$dependencyRes = $this->Generic_model->selectRecord('field','field_id, field_name, field_type, parent_field_id, parent_option_id', array('parent_field_id'=>$parent_field_id,'parent_option_id'=>$parent_option_id))->result();

		if(count($dependencyRes)){
			$html .= "<div style='border-left:1px dotted #ccc; border-bottom:1px dotted #ccc; padding:5px 0px 0px 25px; margin:10px 0px 15px 0px; clear:both' id='".$parent_option_id."_div' class='dependencyDiv ".$parent_field_id."_div' data-target='".$parent_option_id."'>";
			foreach($dependencyRes as $key=>$value){
				$html .= $this->getField($value);
			}
			$html .= "</div>";

			return $html;
		}
	}


	


}