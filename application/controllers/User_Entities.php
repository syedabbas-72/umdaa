<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Entities extends CI_Controller {

	public function __construct() {

		parent::__construct();

		$is_logged_in = $this->session->has_userdata('is_logged_in');

		if($is_logged_in == 0){
			redirect('Authentication');
		}
	}


	public function index(){
		// $data['Main_list'] = $this->db->query("select * from user_entities where category='Main' order by user_entity_name ASC")->result();
		// $data['Masters_list']=$this->db->query("select * from user_entities where category='Masters' order by user_entity_name ASC")->result();
		// $data['Administration_list']=$this->db->query("select * from user_entities where category='Administration' order by user_entity_name ASC")->result();
		// $data['categories']=$this->db->query('select distinct(category) from user_entities')->result();

		$data['categories'] = $categories = $this->db->select('category')->distinct()->from('user_entities')->get()->result_array();

		// $i = 0;
		foreach($categories as $category){
			// Get entities for the category
			$entities = $this->db->select('user_entity_id, user_entity_name, user_entity_alias, method_name, position, parent_id, level, level_alias, is_mobile_module, entity_url, entity_icon')->from('user_entities')->where('category =', $category['category'])->order_by('user_entity_name','ASC')->get()->result_array();

			// $data['entities'][$i]['category'] = $category['category'];

			$i = 0;
			foreach($entities as $entity){
				if($entity['parent_id'] == 0){
					$data['entities'][$category['category']][$i] = $entity;

					// Check if there are any child entities available under this entity
					$data['entities'][$category['category']][$i]['child_entities'] = $this->getChildEntities($entity['user_entity_id'], $category['category'], $i);
				}
				$i++;
			}
		}

		$data['properties'] = $this->db->select('user_property_id, property_name')->from('user_properties')->get()->result_array();

		$data['view'] = 'user_entities/user_entities_list';
		
		$this->load->view('layout', $data);
	}


	public function getChildEntities($parent_id, $category, $i){
		$childEntities = $this->db->select('user_entity_id, user_entity_name, user_entity_alias, method_name, position, parent_id, level, level_alias, is_mobile_module, entity_url, entity_icon')->from('user_entities')->where('parent_id =',$parent_id)->order_by('level','ASC')->get()->result_array();

		$j = 0;
		foreach($childEntities as $entity){

			$childEntities[$j] = $entity;

			// Check if there are any child entities available under this entity
			$childEntities[$j]['child_entities'] = $this->getChildEntities($entity['user_entity_id'], $category['category'], $i);
			
			$j++;
		}

		return $childEntities;		
	}


	public function user_entities_add($category = NULL){

		$user_id = $this->session->userdata('user_id');
		$clinic_id = $this->session->userdata('clinic_id');
		$cond='';
		if($clinic_id!=0)
			$cond = " where clinic_id=".$clinic_id."";

		if($this->input->post('submit')){
			$data['user_entity_name']=$this->input->post('user_entity_name');
			$data['user_entity_alias']=$this->input->post('user_entity_alias');
			$data['category']=$this->input->post('category');
			$data['method_name']=$this->input->post('method_name');
			$data['position']=$this->input->post('position');
			$data['parent_id']=$this->input->post('parent_id');
			$data['level']=$this->input->post('level');
			$data['level_alias']=$this->input->post('level_alias');
			$data['is_mobile_module']=$this->input->post('is_mobile_module');
			$data['entity_url']=$this->input->post('entity_url');
			$data['entity_icon']=$this->input->post('entity_icon');
			$this->Generic_model->insertData('user_entities',$data);
			redirect('User_Entities');
		}else{
			$data['parentEntities'] = $this->db->query("select user_entity_id,user_entity_name from user_entities where parent_id='0' order by user_entity_name ASC")->result();
			$data['category'] = $category;
			$data['view'] = 'user_entities/user_entities_add';
			$this->load->view('layout', $data);
		}
	}

	public function user_property_add(){

		echo '<pre>';
		print_r($_POST);
		echo '</pre>';

		if($this->input->post('submit')){
			unset($_POST['submit']);
			
			$dates = get_CM_by_dates();
			$user_property_data = array_merge($_POST, $dates);

			echo '<pre>';
			print_r($user_property_data);
			echo '</pre>';

			$this->Generic_model->insertData('user_properties',$user_property_data);
			redirect('User_Entities#pillsProperties');
		}

		$data['view'] = 'user_entities/user_property_add';
		$this->load->view('layout', $data);
	}


	public function user_entities_update($id){
		$clinic_id = $this->session->userdata('clinic_id');
		$user_id = $this->session->has_userdata('user_id');

		if($this->input->post('submit')){
			$data['user_entity_name']=$this->input->post('user_entity_name');
			$data['user_entity_alias']=$this->input->post('user_entity_alias');
			$data['category']=$this->input->post('category');
			$data['method_name']=$this->input->post('method_name');
			$data['position']=$this->input->post('position');
			$data['parent_id']=$this->input->post('parent_id');
			$data['level']=$this->input->post('level');
			$data['level_alias']=$this->input->post('level_alias');
			$data['is_mobile_module']=$this->input->post('is_mobile_module');
			$data['entity_url']=$this->input->post('entity_url');
			$data['entity_icon']=$this->input->post('entity_icon');
			$this->Generic_model->updateData('user_entities',$data,array('user_entity_id'=>$id));
			redirect('User_Entities');
		}else{
			$data['EntityData'] = $this->db->query("select * from user_entities where user_entity_id='$id' order by user_entity_name ASC")->row();
			$data['parentEntities'] = $this->db->query("select user_entity_id,user_entity_name from user_entities where parent_id='0' order by user_entity_name ASC")->result();
			$data['view'] = 'user_entities/user_entities_edit';
			$this->load->view('layout', $data);
		}

	}

	public function user_entities_delete($id){
		$this->Generic_model->deleteRecord('user_entities',array('user_entity_id'=>$id));
		redirect('User_Entities');
	}

}
?>