<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class FormsAdmin extends CI_Controller {

	public function __construct() {

		parent::__construct();

		$is_logged_in = $this->session->has_userdata('is_logged_in');

		if($is_logged_in == 0){
			redirect('Authentication');
		}
    }

    public function demo(){
        $data['forms_list'] = $this->db->query("select * from form_list")->result();
        $data['view'] = "formsAdmin/formslist";
        $this->load->view('layout', $data);
    }

    public function addNewForm(){

        $data['form_name']=$this->input->post('name');
        $data['modified_date_time'] =date('Y-m-d H:i:s');
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->insertData('form_list',$data);
        redirect('formsAdmin/demo');
    }

    public function edit_details($id)
    {
        $data['edit_details'] = $this->db->query("select * from form_list where id= '".$id."'")->row();
        $data['view'] = "formsAdmin/editDetails";
        $this->load->view('layout', $data);
    }

    public function editData($id){

        $data['form_name']=$this->input->post('name');
        $data['modified_date_time'] =date('Y-m-d H:i:s');
        $this->Generic_model->updateData("form_list",$data, array('id'=>$id));
        redirect('FormsAdmin/demo');
    }

    public function delete_form($id){
    
        $this->db->query("DELETE from form_list where id='".$id."'" );
        redirect('FormsAdmin/demo');

        }

public function add_details($id){
    $data['form_list'] = $this->db->query("select * from form_list where id='".$id."'")->row();
    $data['form_list_line_items'] = $this->db->select("*")->from("form_list_line_items where form_list_id='".$id."'")->get()->result();
    // $data['speciality'] = $this->db->query("select * from wallet_specilization_prices group by speciality")->result();
    
    $data['view'] = "formsAdmin/addform";
    $this->load->view('layout', $data);
}

public function add_description_details($id)
{
        $data['form_list_id']=$id;
        $data['name']=$this->input->post('name');
        $data['description']=$this->input->post('description');
        $data['modified_date_time'] =date('Y-m-d H:i:s');
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->insertData('form_list_line_items',$data);
        redirect('formsAdmin/add_details/'.$id);
}

    public function add($id){
    
        $data['form_list'] = $this->db->query("select * from form_list where id='".$id."'")->row();
        $data['view'] = "formsAdmin/add_description";
        $this->load->view('layout', $data);
    // redirect('formsAdmin/add_description/'.$id);

    }
    
    public function edit_form_details($id){
    
        $data['form_list_line_items'] = $this->db->query("select * from form_list_line_items where id='".$id."'")->row();
        $data['view'] = "formsAdmin/edit_description";
        $this->load->view('layout', $data);
        // redirect('formsAdmin/add_description/'.$id);

    }

    public function edit_description_details($id)
    {
        $form_list= $this->db->query("select * from form_list_line_items where id='".$id."'")->row();
        $data['name']=$this->input->post('name');
        $data['description']=$this->input->post('description');
        $data['modified_date_time'] =date('Y-m-d H:i:s');
        // print_r($data);
        $this->Generic_model->updateData("form_list_line_items",$data, array('id'=>$id));
        redirect('FormsAdmin/add_details/'.$form_list->form_list_id);
        // $data['view'] = "formsAdmin/addform";
        // $this->load->view('layout', $data);
    }

    public function delete_form_value($id)
    {
        $form_list= $this->db->query("select * from form_list_line_items where id='".$id."'")->row();
        $this->db->query("DELETE from form_list_line_items where id='".$id."'" );
        redirect('FormsAdmin/add_details/'.$form_list->form_list_id);
    }
  
 
}

?>
