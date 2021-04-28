<?php



error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');



class Articles extends CI_Controller {



    public function __construct() {



        parent::__construct();

		$is_logged_in = $this->session->has_userdata('is_logged_in');



        if($is_logged_in == 0){

            redirect('Authentication');

        }

        

    }

	

public function index(){
    $data['articles_list'] = $this->db->query("select * from articles where archieve='0' order by article_id DESC")->result();
	$data['view'] = 'articles/articles_list';
    $this->load->view('layout', $data);
}

public function articles_add(){

	$user_id = $this->session->userdata('user_id');

	$created_date_time = date("Y-m-d H:i:s");	
	$posted_on = date("Y-m-d H:i:s");	
	
	if($this->input->post('submit')){

    $this->load->library('upload',$config);    

    if($fileData1!="")
    {
        $config['upload_path']="./uploads/thumbnails/";
        $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
        $this->upload->initialize($config);
        $this->upload->do_upload('thumbnail');
        $fileData1 = $this->upload->data('file_name');
        $data['article_image']=base_url("uploads/thumbnails/").$fileData1;
    }
    // $docInfo = $this->db->query("select * from doctors where doctor_id='".$this->input->post('doctor')."'")->row();
    extract($_POST);
    $files = $_FILES;
    $fCount = count($_FILES['article_image']['name']);
    $imgType = explode("/",$_FILES['article_image']['type'][0])[0];
    $fileType = pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION);
    if($fileType == "pdf")
    {
        $config['upload_path']="./uploads/article_pdf/";
        $config['allowed_types']='pdf|PDF';
        $this->upload->initialize($config);
        $this->upload->do_upload('pdf');
        $fileData3 = $this->upload->data('file_name');
        $data['posted_url']=$fileData3;
    }
    if($imgType == "image")
    {
        for($n = 0;$n < $fCount;$n++)
        {
            $config['upload_path']="./uploads/article_images/";
            $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 

            $_FILES['article_image']['name']= $files['article_image']['name'][$n];
            $_FILES['article_image']['type']= $files['article_image']['type'][$n];
            $_FILES['article_image']['tmp_name']= $files['article_image']['tmp_name'][$n];
            $_FILES['article_image']['error']= $files['article_image']['error'][$n];
            $_FILES['article_image']['size']= $files['article_image']['size'][$n];    
            
            $this->upload->initialize($config);
            $this->upload->do_upload('article_image');
            $dataInfo[] = $this->upload->data('file_name');
        }
        $data['posted_url'] = implode(",", $dataInfo);
    }
    
    $data['tags']=$this->input->post('tags'); 
    $data['posted_dep']=$this->input->post('posted_dep');  
    $data['article_title']=$this->input->post('article_title');  
    $data['read_article_link'] = $this->input->post('read_link'); 
    $data['article_author'] = $this->input->post('article_author'); 
    $data['article_type']=$this->input->post('article_type');
    $data['short_description']=$this->input->post('short_description');
    $data['video_url']=$this->input->post('videoURL');
    $data['review_by']=$this->session->userdata('user_id');
    $data['citizens']=$this->input->post('patient_visibility');
    $data['doctors']=$this->input->post('doctor_visibility');
    $data['partners']=$this->input->post('partner_visibility');
    $data['article_status']="published";
    $data['posted_by']=$this->session->userdata('user_id');
    $data['created_by']=$this->session->userdata('user_id');
    $data['posted_by_type']=$this->session->userdata('user_name');
    $data['posted_date']=$posted_on;
    $article_id=$this->Generic_model->insertDataReturnId('articles',$data);
    foreach ($departments as $department_id) {
        $data1['article_id']=$article_id;
        $data1['department_id']=$department_id;
        $data1['patient_visibility']=$this->input->post('patient_visibility');
        $data1['doctor_visibility']=$this->input->post('doctor_visibility');
        $data1['partner_visibility']=$this->input->post('partner_visibility');
        $this->Generic_model->insertData('article_department',$data1);
    }
    redirect('Articles');

	}else{
        $data['doctors'] = $this->db->query("select * from doctors")->result();
        $data['departments'] = $this->db->query("select department_id,department_name from department order by department_name ASC")->result();
		$data['view'] = 'articles/articles_add';
    	$this->load->view('layout', $data);
	}
 }

 public function articles_update($id)
 {
     $clinic_id = $this->session->userdata('clinic_id');
     $user_id = $this->session->userdata('user_id');
     if ($this->input->post('submit')) {
         extract($_POST);


         //   echo "<pre>";print_r($_POST);echo "</pre>";
         //   exit();
         $articleData = $this->db->query('select * from articles where article_id=' . $id)->row();
         if ($_FILES['thumbnail']['name'] != "") {
             $config['upload_path'] = "./uploads/thumbnails/";
             $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
             $this->load->library('upload', $config);
             $this->upload->do_upload('thumbnail');
             $fileData1 = $this->upload->data('file_name');

             if ($fileData1 != "") {
                 $data['video_image'] = base_url("uploads/thumbnails/" . $fileData1);
             } else {
                 $data['video_image'] = $articleData->article_image;
             }
         }

         if ($articleStatus == "re-review") {
             $data['article_status'] = "reviewed";
             $notify = "H.E Waiting";
         } elseif ($articleStatus == "approved") {
             $data['article_status'] = "published";
             $notify = "H.E Publish";
         } elseif ($articleStatus == "reviewed") {
             $data['article_status'] = "reviewed";
             $notify = "H.E Waiting";
         } elseif ($articleStatus == "waiting") {
             // $data['article_status'] = "reviewed";
             // $notify = "H.E Waiting";
             $data['article_status'] = "published";
             $notify = "H.E Publish";
         }

         $data['article_title'] = $this->input->post('article_title');
         $data['article_type'] = $this->input->post('article_type');
         $data['article_author'] = $this->input->post('article_author');
         $data['read_article_link'] = $this->input->post('read_link');
         $data['short_description'] = $this->input->post('short_description');
         $data['article_description'] = $this->input->post('article_description');
         $data['video_url'] = $this->input->post('videoURL');
         $data['review_by'] = $this->input->post('review_by');
         extract($_POST);
         if (isset($patient_visibility) && isset($doctor_visibility)) {
             $data['citizens'] = $this->input->post('patient_visibility');
             $data['doctors'] = $this->input->post('doctor_visibility');
         }
         $data['posted_by_type'] = ($this->session->userdata('user_name') == "superadmin") ? 'Admin' : 'Doctor';
         $article_id = $this->Generic_model->updateData('articles', $data, array('article_id' => $id));
         if (isset($departments)) {
             $this->Generic_model->deleteRecord('article_department', array('article_id' => $id));
             foreach ($departments as $department_id) {
                 $data1['article_id'] = $id;
                 $data1['department_id'] = $department_id;
                 $data1['patient_visibility'] = $this->input->post('patient_visibility');
                 $data1['doctor_visibility'] = $this->input->post('doctor_visibility');
                 $this->Generic_model->insertData('article_department', $data1);
             }
         }
         $this->Generic_model->angularNotifications('', '', $articleData->created_by, '', $notify, '');
         $this->Generic_model->pushNotifications('', '', $articleData->created_by, '', $notify, '');

         redirect('Articles');
     } else {
         $data['doctors'] = $this->db->query("select * from doctors")->result();
         $data['departments'] = $this->db->query("select department_id,department_name from department order by department_name ASC")->result();
         $data['dept_ids'] = $this->db->query("select department_id from article_department where article_id='" . $id . "'")->result();
         foreach ($data['dept_ids'] as $value) {
             $data['dept'][] = $value->department_id;
         }
         $data['article_dept'] = $this->db->query("select * from article_department where article_id='" . $id . "'")->result();
         $data['articles_list'] = $this->db->query('select * from articles where article_id=' . $id)->row();
         $data['view'] = 'articles/articles_edit';
         $this->load->view('layout', $data);
     }
 }

 public function articles_delete($id){

  $data['archieve']=1;

  $this->Generic_model->deleteRecord('articles',array('article_id'=>$id));

   redirect('Articles');

 }

 public function Publish($id){
     $articleInfo = $this->Generic_model->getSingleRecord("articles",array('article_id'=>$id));
     $data['article_status'] = "published";
     $this->Generic_model->updateData("articles",$data,array('article_id'=>$id));
     $this->Generic_model->angularNotifications('', '', $articleInfo->posted_by, '', 'H.E Publish', '');
     $this->Generic_model->pushNotifications('', '', $articleInfo->posted_by, '', 'H.E Publish', '');
     $this->session->set_flashdata('msg','Article Published Successfully.');
     redirect("Articles");
 }

// Get Article Data by Article ID
public function getArticleData(){
    extract($_POST);
    $article = $this->db->query("select * from articles where article_id='".$article_id."'")->row();
    ?>
    <div class="row">
        <div class="col-md-12">
            <h5 class="page-title"><?=$article->article_title?></h5>
            <p class="font-weight-bold" style="padding: 2px 10px !important">Description</p>
            <p style="padding: 2px 10px !important" style="padding: 2px 10px !important"><?=$article->short_description?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <?php
            if($article->citizens == 0)
                $ct = "None";
            elseif($article->citizens == 1)
                $ct = "All Citizens";
            elseif($article->citizens == 2)
                $ct = "My Citizens";
            
        ?>
            <p style="padding: 2px 10px !important"><b>Citizens Visibility:</b> <?=$ct?></p>
        </div>
        <div class="col-md-12">
            <p style="padding: 2px 10px !important"><b>Doctors Visibility:</b> <?=($article->doctors==1)?'Visible to Doctors With Below Departments':'None'?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p style="padding: 2px 10px !important">
                <b>Selected Departments: </b>
        <?php
        $articleInfo = $this->db->query("select * from article_department where article_id='".$article->article_id."'")->result();
        foreach ($articleInfo as $value) 
        {
            if($value->department_id=="0")
            {
                $dept_name .= "All Departments,";
            }
            else
            {
                $dept = $this->db->query("select * from department where department_id='".$value->department_id."'")->row();
                $dept_name .= $dept->department_name.",";    
            }            
        }
        echo substr($dept_name, 0, -1);
        if(strtolower($article->article_type) == "video")
        {
            $uploadFolder = "article_videos/";
        }
        elseif(strtolower($article->article_type) == "image")
        {
            $uploadFolder = "article_images/";
        }
        elseif(strtolower($article->article_type) == "pdf")
        {
            $uploadFolder = "article_pdf/";
        }
        
        ?>
            </p>
        </div>
        <div class="col-md-12">
            <p style="padding: 2px 10px !important">
                <a href="<?=base_url('uploads/'.$uploadFolder.$article->posted_url)?>" class="btn btn-xs btn-app" target="blank">Show File</a>
                <a href="<?=base_url('Articles/articles_update/'.$article->article_id)?>" class="btn btn-xs btn-default" target="blank"><i class="fa fa-edit"></i> Edit This Article</a>
            </p>
        </div>
    </div>
    <?php
}


}

