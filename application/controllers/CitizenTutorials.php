<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class CitizenTutorials extends MY_Controller {
    public function __construct(){      
        parent::__construct();
        if(!$this->session->has_userdata('is_logged_in'))
        {
            redirect('Authentication/login');
        }
    }

    public function index(){
        $data['tutorials']  = $this->db->query("select * from umdaa_tutorials where tutorial_type='citizen'")->result();
        $data['view'] = 'tutorials/citizens';
        $this->load->view('layout', $data);
    }

    // Delete Tutorial
    public function Delete($id = '')
    {
        $check = $this->db->query("select * from umdaa_tutorials where umdaa_tutorial_id='".$id."'")->row();
        if(count($check) > 0)
        {
            $this->Generic_model->deleteRecord('umdaa_tutorials', array('umdaa_tutorial_id'=>$id));
            $this->session->set_flashdata('msg','Tutorial Deleted');
            redirect('CitizenTutorials');
        }
        else
        {
            $this->session->set_flashdata('msg','Tutorial Not Exists. Please Refresh the Page.');
            $data['view'] = 'tutorials/citizens';
            $this->load->view('layout', $data);
        }
    } 

    // ADD Tutorial
    public function add()
    {
        if(isset($_POST['tutorialSubmit']))
        {
            extract($_POST);
            $this->load->library('upload',$config);    
            // $files = $_FILES;
            // $fCount = count($_FILES['article_image']['name']);
            // $imgType = explode("/",$_FILES['article_image']['type'][0])[0];
            // $fileType = pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION);
            $config['upload_path']="./uploads/thumbnails/";
            $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 

            // $_FILES['thumbnail']['name']= $files['thumbnail']['name'];
            // $_FILES['thumbnail']['type']= $files['thumbnail']['type'];
            // $_FILES['thumbnail']['tmp_name']= $files['thumbnail']['tmp_name'];
            // $_FILES['thumbnail']['error']= $files['thumbnail']['error'];
            // $_FILES['thumbnail']['size']= $files['thumbnail']['size'];    
            
            $this->upload->initialize($config);
            $this->upload->do_upload('thumbnail');
            $filename= $this->upload->data('file_name');
        //   exit;

            $data['tutorial_name'] = $tutorial_name;
            $data['tutorial_description'] = $description;
            $data['tutorial_type'] = "citizen";
            $data['tutorial_link'] = $tutorial_link;
            $data['video_thumbnail'] = $filename;
            $data['created_by'] = $this->session->userdata('user_id');
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('umdaa_tutorials', $data);
            $this->session->set_flashdata('msg', 'Tutorial Saved Successfully.');
            redirect('CitizenTutorials');
                
        }
        else
        {
            $data['view'] = 'tutorials/add';
            $this->load->view('layout', $data);
        }
    }

    

    // Edit Tutorial
    public function edit($id = '')
    {
        if(isset($_POST['tutorialSubmit']))
        {
            extract($_POST);
            $this->load->library('upload',$config);    
            if($_FILES['thumbnail']['name'] != "")
            {
                $config['upload_path']="./uploads/thumbnails/";
                $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';   
                
                $this->upload->initialize($config);
                $this->upload->do_upload('thumbnail');
                $data['video_thumbnail'] = $this->upload->data('file_name');
            }
            
            $data['tutorial_name'] = $tutorial_name;
            $data['tutorial_description'] = $description;
            $data['tutorial_link'] = $tutorial_link;
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->updateData('umdaa_tutorials', $data, array('umdaa_tutorial_id'=>$id));
            $this->session->set_flashdata('msg', 'Tutorial Updated Successfully.');
            redirect('CitizenTutorials');
                
        }
        else
        {
            $data['info'] = $this->db->query("select * from umdaa_tutorials where umdaa_tutorial_id='".$id."'")->row();
            $data['view'] = 'tutorials/edit';
            $this->load->view('layout', $data);
        }
    }

}
?>