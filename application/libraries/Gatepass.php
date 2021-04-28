<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gatepass {

    public function __construct(){
        $CI =& get_instance();
        $i = 1;
        // echo "srinu sri";
        // if ($CI->session->has_userdata('is_logged_in')){
        //     $cur_tab = $CI->uri->segment(1);
        //     $clinic_id = $CI->session->userdata('clinic_id');
        //     if($clinic_id != 0){
        //         if($CI->session->userdata('setup') == "1"){
        //             $empInfo = $CI->db->query("select * from employees where employee_id='".$CI->session->userdata('user_id')."'")->row();
        //             $entities = $CI->db->query("select * from clinic_role_permissions crp,user_entities ue where ue.user_entity_id=crp.entity_id and crp.clinic_role_id IN (".$empInfo->assigned_roles.") and crp.clinic_id='".$clinic_id."'")->result();
        //             // $entities = $CI->db->query("select * FROM `package_features` pf,modules m,user_entities ue where m.module_id=pf.module_id and ue.user_entity_id=pf.entity_id and pf.feature_type='Module' and pf.package_id='".$CI->session->userdata('package_id')."' and m.role_id='".$CI->session->userdata('role_id')."' order by ue.position ASC")->result();							    
        //         }
        //         else{
        //             $entities = $CI->db->query("select * FROM `package_features` pf,modules m,user_entities ue where m.module_id=pf.module_id and ue.user_entity_id=pf.entity_id and pf.feature_type='Module' and pf.package_id='".$CI->session->userdata('package_id')."' and m.role_id='".$CI->session->userdata('role_id')."' order by ue.position ASC")->result();							    
        //         }
        //         // $entities = $CI->db->query("select * FROM `package_features` pf,modules m,user_entities ue where m.module_id=pf.module_id and ue.user_entity_id=pf.entity_id and pf.feature_type='Module' and pf.package_id='".$CI->session->userdata('package_id')."' and m.role_id='".$CI->session->userdata('role_id')."' order by ue.position ASC")->result();							
        //     }
        //     // echo $CI->db->last_query();
        //     // exit;

        //     if(count($entities) > 0){
        //         $i = 0;
        //         foreach($entities as $val){
        //             $support = $CI->db->query("select * from support_entities where entity_id='".$val->entity_id."'")->row();

        //             if(count($support) > 0){
        //                 $smenus = explode(",", $support->entities);
        //                 foreach($smenus as $value){
        //                     // $menus[$i] = strtolower(explode("/", $value)[0]);
        //                     $menus[$i] = strtolower($value);
        //                     $i++;
        //                 }
        //             }
        //             // $menus[$i] = strtolower(explode("/", $val->entity_url)[0]);
        //             $menus[$i] = strtolower($val->entity_url);
        //             $i++;
        //         }
        //         // $menus[$i] = "ipdsetup";
        //         // echo "<pre>";print_r($menus);echo "</pre>";
        //         $auth = $CI->uri->segment(1);
        //         $cur_tab = $CI->uri->segment(1)."/".$CI->uri->segment(2);
        //         if(substr($cur_tab, -1) == "/"){
        //             $cur_tab = substr($cur_tab, 0, -1);
        //         }
        //         // echo strtolower($cur_tab);
        //         // exit;
        //         if(strtolower($auth) != "authentication"){
        //             if(!in_array(strtolower($cur_tab), $menus)){
        //                 // redirect('Authentication/unauthorised');
        //             }
        //         }
                
        //     }
        // }
        
    }

}
?>