<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class VideoSharing extends REST_Controller1
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

    // Articles Post
    public function index_post(){
        extract($_POST);
        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";
        // exit;
        $date = date("Y-m-d H:i:s");
        $this->load->library('upload'); 
        $config = array();

        $config['upload_path'] = './uploads/article_videos';
        // Upload Files Based on Article Type
        if(strtolower(trim($article_type,'"')) == "video")
        {
            $config['upload_path'] = './uploads/article_videos';
        }
        elseif(strtolower(trim($article_type,'"')) == "image")
        {
            $config['upload_path'] = './uploads/article_images';
        }
        elseif(strtolower(trim($article_type,'"')) == "pdf")
        {
            $config['upload_path'] = './uploads/article_pdf';
        }

        $config['allowed_types']='mp4|MP4|jpg|JPG|png|PNG|jpeg|JPEG|pdf|PDF'; 

       	$fileCount = count($_FILES['file_i']['name']);

       	if($fileCount>0)
       	{
       		$data['article_title']=trim($article_title,'"');   
            $data['article_type']=trim($article_type,'"');
            $data['short_description']=trim($short_description,'"');
            $data['posted_by']=trim($user_id,'"');
            $data['created_by']=trim($user_id,'"');
            $data['article_status']="waiting";
            $data['posted_by_type']='Doctor';
            $data['posted_date']=$date;
            $data['article_author']=$article_author;
            $data['read_article_link']=$read_article_link;
            $data['doctors'] = trim($doctor_visibility,'"'); //1 for doctor visibility, 0 for none
            $data['citizens'] = trim($citizens,'"'); //1 for all citizens, 2 for my citizens
            $data['created_date_time'] = $date;
            $data['modified_date_time'] = $date;
            $article_id=$this->Generic_model->insertDataReturnId('articles',$data);
       		for($i = 0;$i < $fileCount;$i++)
       		{
       			$_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
		        $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
		        $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
		        $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
		        $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];

		        $filetype = pathinfo($_FILES["file_i[]"]["name"], PATHINFO_EXTENSION);
		        $files = ['mp4','jpg','png','pdf','jpeg'];
		        $filename = $_FILES['file_i[]']['name'];
		        if(!in_array($filetype, $files))
		        {
		        	$param['type'] = pathinfo($_FILES["file_i[]"]["name"], PATHINFO_EXTENSION);
		            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
		        }
		        else
		        {  
		        	$this->upload->initialize($config);
            		$this->upload->do_upload('file_i[]');            		
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
		        }
            }
            
            $data['posted_url'] = implode(",",$fileName);
            $this->Generic_model->updateData("articles", $data, array('article_id' => $article_id));

            $dept = explode(",",$departments);
            // echo "<pre>";print_r($dept);echo "</pre>";
            // echo $dept[0];
            // exit;
            if(!empty($dept))
            {
                if($dept[0] == 0)
                {
                    $data1['article_id']=$article_id;
                    $data1['department_id']=0;
                    $data1['patient_visibility']=trim($patient_visibility,'"');
                    $data1['doctor_visibility']=trim($doctor_visibility,'"');
                    $this->Generic_model->insertData('article_department',$data1);
                }
                else
                {
                    // $dept = explode(",",trim($departments,'"'));
                    foreach ($dept as $value) 
                    {
                        $data1['article_id']=$article_id;
                        $data1['department_id']=$value;
                        $data1['patient_visibility']=trim($patient_visibility,'"');
                        $data1['doctor_visibility']=trim($doctor_visibility,'"');
                        $this->Generic_model->insertData('article_department',$data1);
                    }
                }
                
            }
            else
            {
                $docInfo = $this->db->query("select * from doctors d, department de where d.department_id=de.department_id and d.doctor_id='".$user_id."'")->row();
                $data1['article_id']=$article_id;
                $data1['department_id']=$docInfo->department_id;
                $data1['patient_visibility']=trim($patient_visibility,'"');
                $data1['doctor_visibility']=trim($doctor_visibility,'"');
                $this->Generic_model->insertData('article_department',$data1);
            }
            
            $param = "Successfully Posted. Sent For Review.";
            $this->Generic_model->angularNotifications('', '', trim($user_id,'"'), '', 'H.E Uploaded', '');
            $this->response(array('code'=>'200','message'=>'Sent For Review','result'=>$param));
       	}
    }

    //Articles Search
    public function ArticlesSearch_post(){
        if(isset($_POST))
        {
            extract($_POST);
            $docInfo = $this->db->query("select department_id from doctors where doctor_id='".$user_id."'")->row();
            $dept = "0,".$docInfo->department_id;
            $articles = $this->db->query("select * from articles a,article_department ad where a.article_id=ad.article_id and a.article_status='published' and ad.doctor_visibility='1' and ad.department_id IN (".$dept.") and a.article_title LIKE '%".$search."%' order by a.posted_date DESC" )->result();
            $i = 0;
            foreach ($articles as $value) 
            {
                $doctor_list = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$value->posted_by."'")->row();
                if(count($doctor_list)<=0)
                    $posted_by = "UMDAA";
                else
                    $posted_by = "Dr. ".$doctor_list->first_name." ".$doctor_list->last_name;
                $data['articles'][$i]['department_name'] = $doctor_list->department_name;
                $data['articles'][$i]['fullname'] = $posted_by;
                $data['articles'][$i]['profile_image'] = base_url("uploads/doctors/".$doctor_list->profile_image);
                $data['articles'][$i]['work_place_location'] =$doctor_list->work_place_location; 
                $data['articles'][$i]['article_title'] = $value->article_title;
                $data['articles'][$i]['article_id'] = $value->article_id;
                $data['articles'][$i]['posted_by'] = $value->posted_by;
                $data['articles'][$i]['description'] = $value->short_description;
                $data['articles'][$i]['type'] = strtolower($value->article_type);
                if(strtolower($value->article_type) == "video")
                {
                    $data['articles'][$i]['image_url'] = $value->video_image;
                    $data['articles'][$i]['video'] = $value->video_url;
                }
                elseif(strtolower($value->article_type) == "image")
                {
                    $images = explode(",", $value->posted_url);
                    foreach($images as $value)
                    {
                        if($value == ""){
                            $igm = "2t.jpg";
                        }
                        else{
                            $igm = $value;
                        }
                        $data['articles'][$i]['article_image'][] = base_url('uploads/article_images/'.$igm);
                    }                    
                }
                elseif(strtolower($value->article_type) == "pdf")
                {
                    $files = explode(",", $value->posted_url);
                    foreach($files as $value)
                    {
                        $data['articles'][$i]['pdf'] = base_url('uploads/article_pdf/'.$value);
                    }
                }
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Articles List','result'=>$data));    
        }
        else
        {
            $param['result'] = "Unauthorized Access.";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));            
        }
    }

    //Get Individual Articles based on Doctor ID
    public function IndArticles_get($id){
        $i = 0;
        $doctor_list = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$id."'")->row();
        if(count($doctor_list)==0)
        {
            $data['result'] = "Doctor Doesnot Exist";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$data));    
        }
        else
        {
            $articles = $this->db->query("select * from articles a,article_department ad where a.article_id=ad.article_id and a.posted_by='".$id."' group by a.article_id order by a.article_id DESC")->result();
            foreach ($articles as $value) 
            {
                $data['articles'][$i]['department_name'] = $doctor_list->department_name;
                $data['articles'][$i]['fullname'] = "Dr. ".$doctor_list->first_name." ".$doctor_list->last_name; 
                $data['articles'][$i]['profile_image'] = base_url("uploads/doctors/".$doctor_list->profile_image);
                $data['articles'][$i]['work_place_location'] =$doctor_list->work_place_location; 
                $data['articles'][$i]['article_title'] = $value->article_title;
                $data['articles'][$i]['article_id'] = $value->article_id;
                $data['articles'][$i]['posted_by'] = $value->posted_by;
                $data['articles'][$i]['description'] = $value->short_description;
                $data['articles'][$i]['article_status'] = $value->article_status;
                $data['articles'][$i]['type'] = strtolower($value->article_type);
                if($value->article_status == "waiting")
                {
                    $data['articles'][$i]['posted_url'] = base_url('uploads/article_videos/'.$value->posted_url);
                }
                if(strtolower($value->article_type) == "video")
                {
                    $data['articles'][$i]['image_url'] = $value->video_image;
                    $data['articles'][$i]['video'] = $value->video_url;
                }
                elseif(strtolower($value->article_type) == "image")
                {
                    $images = explode(",", $value->posted_url);
                    foreach($images as $value)
                    {
                        if($value == ""){
                            $igm = "2t.jpg";
                        }
                        else{
                            $igm = $value;
                        }
                        $data['articles'][$i]['article_image'][] = base_url('uploads/article_images/'.$igm);
                    }                    
                }
                elseif(strtolower($value->article_type) == "pdf")
                {
                    $files = explode(",", $value->posted_url);
                    foreach($files as $value)
                    {
                        $data['articles'][$i]['pdf'] = base_url('uploads/article_pdf/'.$value);
                    }
                }
                $i++;
            }
            // echo "<pre>";print_r($data); echo "</pre>";
            $this->response(array('code'=>'200','message'=>'Articles List','result'=>$data));    
        }
    }

    //Get Articles List
    public function Articles_get($id){
        $i = 0;
        $doctors = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$id."'")->row();
        if(count($doctors)==0)
        {
            $data['result'] = "Doctor Doesnot Exist";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$data));    
        }
        else
        {
            $dept_ids = "(0,".$doctors->department_id.")";
            $articles = $this->db->query("select * from articles a,article_department ad where a.article_id=ad.article_id and a.article_status='published' and ad.doctor_visibility='1' and ad.department_id IN ".$dept_ids." order by a.article_id DESC limit 10")->result();
            // echo $this->db->last_query();
            foreach ($articles as $value) 
            {
                $doctor_list = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$value->posted_by."'")->row();
                if(count($doctor_list)<=0)
                    $posted_by = "UMDAA";
                else
                    $posted_by = "Dr. ".$doctor_list->first_name." ".$doctor_list->last_name;
                $data['articles'][$i]['department_name'] = $doctor_list->department_name;
                $data['articles'][$i]['fullname'] = $posted_by;
                $data['articles'][$i]['profile_image'] = base_url("uploads/doctors/".$doctor_list->profile_image);
                $data['articles'][$i]['work_place_location'] =$doctor_list->work_place_location; 
                $data['articles'][$i]['article_title'] = $value->article_title;
                $data['articles'][$i]['article_id'] = $value->article_id;
                $data['articles'][$i]['article_author'] = $value->article_author;
                $data['articles'][$i]['posted_by'] = $value->posted_by;
                $data['articles'][$i]['read_article_link'] = $value->read_article_link;
                $data['articles'][$i]['description'] = $value->short_description;
                $data['articles'][$i]['type'] = strtolower($value->article_type);
                if(strtolower($value->article_type) == "video")
                {
                    $data['articles'][$i]['image_url'] = $value->video_image;
                    $data['articles'][$i]['video'] = $value->video_url;
                }
                elseif(strtolower($value->article_type) == "image")
                {
                    $images = explode(",", $value->posted_url);
                    foreach($images as $value)
                    {
                        if($value == ""){
                            $igm = "2t.jpg";
                        }
                        else{
                            $igm = $value;
                        }
                        $data['articles'][$i]['article_image'][] = base_url('uploads/article_images/'.$igm);
                    }                    
                }
                elseif(strtolower($value->article_type) == "pdf")
                {
                    $files = explode(",", $value->posted_url);
                    foreach($files as $value)
                    {
                        $data['articles'][$i]['pdf'] = base_url('uploads/article_pdf/'.$value);
                    }
                }
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Articles List','result'=>$data));    
        }
    }

    //Approval From Doctor
    public function Approve_post(){
        if(isset($_POST))
        {
            extract($_POST);
            // $this->Generic_model->angularNotifications('', '', $user_id, '', 'H.E Approval', '');
            if(!empty($article_id))
            {
                $data['article_status'] = "approved";
                $res = $this->Generic_model->updateData("articles",$data,array('article_id'=>$article_id));
                $para = 'Approved. Published Soon.';
                $this->Generic_model->angularNotifications('', '', $user_id, '', 'H.E Approval', '');
                $this->response(array('code'=>'200','message'=>'Approval Given','result'=>$para));
            }
            else
            {
                $para = "Article ID Missing";
                $this->response(array('code'=>'201','message'=>'Error','result'=>$para)); 
            }
        }
        else
        {
            $para = 'UnAuthorized Access';
            $this->response(array('code'=>'201','message'=>'UnAuthorized Access','result'=>$para));
        }   
    }

    public function Reject_post(){
        if(isset($_POST))
        {
            extract($_POST);
            if(!empty($article_id))
            {
                $data['article_status'] = "re-review";
                $data['doctor_comments'] = $comments;
                $res = $this->Generic_model->updateData('articles',$data,array('article_id'=>$article_id));
                $para = 'Rejected and Sent To Review Again';
                $this->Generic_model->angularNotifications('', '', $user_id, '', 'H.E Rejection', '');
                $this->response(array('code'=>'200','message'=>'Rejected','result'=>$para));
            }
            else
            {
                $para = "Article ID Missing";
                $this->response(array('code'=>'201','message'=>'Error','result'=>$para));
            }
        }
        else
        {
            $para = 'UnAuthorized Access';
            $this->response(array('code'=>'201','message'=>'UnAuthorized Access','result'=>$para));
        }   
    }

}
