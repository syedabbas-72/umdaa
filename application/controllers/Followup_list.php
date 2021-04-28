<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Followup_list extends CI_Controller {


	public function _remap($param) {
        $this->index($param);
    }

	public function index($param){
       
       $data['FollowUpTemplates_val'] = $this->db->query("select * from followup")->result();
       $this->load->view("FollowUpTemplates/templates_view",$data);
    }


}
