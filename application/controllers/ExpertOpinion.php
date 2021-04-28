<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class ExpertOpinion extends MY_Controller {
 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))
    {
        redirect('Authentication/login');
    }      
 }

 public function index(){
     $data['ExpertOpinion'] = $this->db->select("*")->from("expert_opinion")->get()->result();
     $data['view'] = "expert_opinion/expert_opinion";
     $this->load->view('layout',$data);
 }

//  Get Messages w.r.t to expert opinion id
public function getMessages(){
    extract($_POST);
    $messagesInfo = $this->db->query("select * from expert_opinion eo,expert_opinion_conversations eoc where eo.expert_opinion_id=eoc.expert_opinion_id and eo.expert_opinion_id='".$id."'")->result();
    if(count($messagesInfo)>0)
    {
        foreach($messagesInfo as $value)
        {
            if($value->sent_by == $value->parent_doctor_id)
            {
                $class = "text-right";
                $bg = "bg-b-cyan";
            }
            elseif($value->sent_by == $value->referred_doctor_id)
            {
                $class = "text-left";
                $bg = "bg-b-orange";
            }
            ?>
                <div class="row">
                    <div class="col-md-12 <?=$class?>">
                        <span class="font-weight-bold small font-italic"><i class="fa fa-user"></i> <?=getDoctorName($value->sent_by)?></span>&emsp;&emsp;
                        <span class="small font-italic"><?=date("d M Y h:i A",strtotime("$value->created_date_time"))?></span><br>
                        <label class="<?=$bg?> p-2 mb-2 rounded-left rounded-right text-left"><?=$value->message?></label>
                    </div>
                </div>
            <?php
        }
    }
    else
    {
        ?>
        <h4 class="text-center">No Chat History</h4>
        <?php
    }

}
 
}
?>