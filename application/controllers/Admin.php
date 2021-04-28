<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

     public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }
        
   }


   public function doctorSlots($doctor_id, $clinic_id){
       $datetime = date("Y-m-d H:i:s");
       $check = $this->db->query("select * from clinic_doctor where doctor_id='$doctor_id' and clinic_id='".$clinic_id."'")->row();
       echo $this->db->last_query();
       if(count($check)>0)
       {
           $clinic_doctor_id = $check->clinic_doctor_id;
           for($i=1;$i<=7;$i++)
           {
               $data['weekday'] = $i;
               $data['clinic_doctor_id'] = $clinic_doctor_id;
               $data['created_by'] = $doctor_id;
               $data['modified_by'] = $doctor_id;
               $data['created_date_time'] = $datetime;
               $data['modified_date_time'] = $datetime;
               $clinic_doctor_weekday_id = $this->Generic_model->insertDataReturnId('clinic_doctor_weekdays', $data);

               $morning['clinic_doctor_weekday_id'] = $clinic_doctor_weekday_id;
               $morning['from_time'] = "08:00:00";
               $morning['to_time'] = "12:50:00";
               $morning['created_by'] = $doctor_id;
               $morning['modified_by'] = $doctor_id;
               $morning['created_date_time'] = $datetime;
               $morning['modified_date_time'] = $datetime;
               $this->Generic_model->insertData('clinic_doctor_weekday_slots', $morning);
               
               $afternoon['clinic_doctor_weekday_id'] = $clinic_doctor_weekday_id;
               $afternoon['from_time'] = "13:00:00";
               $afternoon['to_time'] = "17:50:00";
               $afternoon['created_by'] = $doctor_id;
               $afternoon['modified_by'] = $doctor_id;
               $afternoon['created_date_time'] = $datetime;
               $afternoon['modified_date_time'] = $datetime;
               $this->Generic_model->insertData('clinic_doctor_weekday_slots', $afternoon);
               
               $evening['clinic_doctor_weekday_id'] = $clinic_doctor_weekday_id;
               $evening['from_time'] = "18:00:00";
               $evening['to_time'] = "22:00:00";
               $evening['created_by'] = $doctor_id;
               $evening['modified_by'] = $doctor_id;
               $evening['created_date_time'] = $datetime;
               $evening['modified_date_time'] = $datetime;
               $this->Generic_model->insertData('clinic_doctor_weekday_slots', $evening);
           }
           redirect('doctor_settings');
       }
   }

    public function roles() {
        
        $data['roles_list'] = $this->Generic_model->getAllRecords('roles', $condition = array('archieve'=>0), $order = '');
        $data['view'] = 'admin/roles';
        $this->load->view('layout', $data);
    }

    public function add_role() {
        $user_id = $this->session->has_userdata('user_id');
        $data['roles_list'] = $this->Generic_model->getAllRecords('roles', $condition = array('archieve'=>0), $order = '');
        if ($this->input->post('submit')) {
            $role_info['role_name'] = $this->input->post('role_name');
            $role_info['role_reports_to'] = $this->input->post('role_reports_to');
            $role_info['status'] = 1;
            $role_info['created_by'] = $user_id;
            $role_info['modified_by'] = $user_id;
            $role_info['created_date_time'] = date('Y-m-d H:i:s');
            $role_info['modified_date_time'] = date('Y-m-d H:i:s');
           $ok= $this->Generic_model->insertData('roles', $role_info);
           if($ok==1)
           {
               redirect('Admin/roles');
           }
           
        } else {
            $data['view'] = 'admin/role_add';
            $this->load->view('layout', $data);
        }
    }

    public function update_role($id) {
        $user_id = $this->session->has_userdata('user_id');
        $data['roles_list'] = $this->Generic_model->getAllRecords('roles', $condition = array('archieve'=>1), $order = '');
        if ($this->input->post('submit')) {
            $role_info['role_name'] = $this->input->post('role_name');
            $role_info['role_reports_to'] = $this->input->post('role_reports_to');
            $role_info['status'] = $this->input->post('status');
            $role_info['modified_by'] = $user_id;
            $role_info['modified_date_time'] = date('Y-m-d H:i:s');
            $ok=$this->Generic_model->updateData('roles', $role_info, array('role_id' => $id));
            if($ok==1)
           {
               redirect('Admin/roles');
           }else{
               $data['role'] = $this->Generic_model->getSingleRecord($table='roles', array('role_id' => $id), $order = '');
            $data['view'] = 'admin/role_edit';
            $this->load->view('layout', $data);
           }
        } else {
            $data['role'] = $this->Generic_model->getSingleRecord($table='roles', array('role_id' => $id), $order = '');
            $data['view'] = 'admin/role_edit';
            $this->load->view('layout', $data);
        }
    }

    public function delete_role($id) {
        $role_info['archieve'] = 1;
        $this->Generic_model->deleteRecord('roles',$role_info, array('role_id' => $id));
        
        redirect('Admin/roles');
    }
    
    public function profiles(){
            $clinic_id = $this->session->userdata('clinic_id');
            $cond = '';
            //f($clinic_id!=0)
            $cond = "clinic_id=".$clinic_id." and archieve=0";
            $data['profiles_list'] = $this->db->query("select * from profiles  where clinic_id='".$clinic_id."' group by profile_id")->result();
            $data['view'] = 'admin/profiles';
            $this->load->view('layout', $data);
        
    }
    
    public function add_profile() {
        $user_id = $this->session->userdata('user_id');
        if ($this->input->post('submit')) {
            $profile_info['profile_name'] = $this->input->post('profile_name');
            $profile_info['status'] = 1;
            $profile_info['created_by'] = $user_id;
            $profile_info['modified_by'] = $user_id;
            $profile_info['created_date_time'] = date('Y-m-d H:i:s');
            $profile_info['modified_date_time'] = date('Y-m-d H:i:s');
           $ok= $this->Generic_model->insertData('profiles', $profile_info);
           if($ok==1)
           {
               redirect('Admin/profiles');
           }
           
        } else {
            $data['view'] = 'admin/profile_add';
            $this->load->view('layout', $data);
        }
    }
    
    public function update_profile($id) {
        $user_id = $this->session->userdata('user_id');
        if ($this->input->post('submit')) {
            $profile_info['profile_name'] = $this->input->post('profile_name');
            $profile_info['status'] = $this->input->post('status');
            $profile_info['user_entity_id'] = $this->input->post('user_entity_id');
            $profile_info['modified_by'] = $user_id;
            $profile_info['modified_date_time'] = date('Y-m-d H:i:s');
            $ok=$this->Generic_model->updateData('profiles', $profile_info, array('profile_id' => $id));
            if($ok==1)
           {
               redirect('Admin/profiles');
           }else{
                $data['user_entities'] = $this->db->select("*")->from("user_entities")->order_by("user_entity_name ASC")->get()->result();
                $data['profile'] = $this->Generic_model->getSingleRecord($table='profiles', array('profile_id' => $id), $order = '');
                $data['view'] = 'admin/profile_edit';
                $this->load->view('layout', $data);
           }
        } else {
            $data['user_entities'] = $this->db->select("*")->from("user_entities")->order_by("user_entity_name ASC")->get()->result();
            $data['profile'] = $this->Generic_model->getSingleRecord($table='profiles', array('profile_id' => $id), $order = '');
            $data['view'] = 'admin/profile_edit';
            $this->load->view('layout', $data);
        }
    }
    
    public function delete_profile($id) {
        $profile_info['archieve'] = 1;
        $this->Generic_model->deleteRecord('profiles',$profile_info, array('profile_id' => $id));
        
        redirect('Admin/profiles');
    }
    
    public function profile_view($id) {

        $clinic_id = $this->session->userdata('clinic_id');
        
        if($clinic_id == 0){
            $data['user_entity'] = $this->db->query("select * from user_entities ")->result();
        }else{
            $data['user_entity'] = $this->db->query("select * from user_entities e inner join std_uac_entities c on e.user_entity_id=c.user_entity_id and c.category='Clinic'")->result();
        }

        // Get User entities with respect to parent and child
        $entities = $this->db->select('user_entity_id, user_entity_name, user_entity_alias, method_name, position, parent_id, level, level_alias, is_mobile_module, entity_url, entity_icon')->from('user_entities')->order_by('user_entity_name','ASC')->get()->result_array();

        $i = 0;
        foreach($entities as $entity){
            if($entity['parent_id'] == 0){
                $data['entities'][$i] = $entity;

                // Check if there are any child entities available under this entity
                $data['entities'][$i]['child_entities'] = $this->getChildEntities($entity['user_entity_id'], $i);
            }
            $i++;
        }

        $data['profile_id']= $id;
        $data['profile_info'] = $this->db->select('profile_id, profile_name')->from('profiles')->where('profile_id =', $id)->get()->row();

        $data['properties'] = $this->db->select('user_property_id, property_icon, property_name')->from('user_properties')->get()->result_array();

        $accessibleProperties = $this->db->select('user_property_id')->from('profile_property_accessibility')->where('profile_id =',$id)->where('status =',1)->get()->result_array();

        foreach($accessibleProperties as $property){
            $data['properties_accessibility'][] = $property['user_property_id'];
        }

        $data['view'] = 'admin/profile_view';
        $this->load->view('layout', $data);
    }

    public function getChildEntities($parent_id, $i){
        $childEntities = $this->db->select('user_entity_id, user_entity_name, user_entity_alias, method_name, position, parent_id, level, level_alias, is_mobile_module, entity_url, entity_icon')->from('user_entities')->where('parent_id =',$parent_id)->order_by('level','ASC')->get()->result_array();

        $j = 0;
        foreach($childEntities as $entity){

            $childEntities[$j] = $entity;

            // Check if there are any child entities available under this entity
            $childEntities[$j]['child_entities'] = $this->getChildEntities($entity['user_entity_id'], $j);
            
            $j++;
        }

        return $childEntities;      
    }


    public function property_accessibility(){

        if($_POST['submit']){

            $postParams = $_POST['propertyAccessibility'];

            foreach($postParams as $record){
                // Check if the record exist in the table and if exists then update else create/insert
                $res = $this->db->select('profile_property_accessibility_id')->from('profile_property_accessibility')->where('profile_id =',$record['profile_id'])->where('user_property_id =',$record['user_property_id'])->get()->row();

                if(count($res) > 0){
                    // Update
                    $this->Generic_model->updateData('profile_property_accessibility',$record,array('profile_property_accessibility_id'=>$res->profile_property_accessibility_id));
                }else{
                    // Insert                    
                    $record = array_merge($record, get_CM_by_dates());
                    $this->Generic_model->insertData('profile_property_accessibility',$record);
                }
            }

        }

        redirect('Admin/profile_view/'.$postParams[0]['profile_id']);

        // $data['view'] = 'admin/profile_view/'.$postParams[0]['profile_id'];
        // $this->load->view('layout', $data);

    }

    
    public function profile_permision_edit(){

        $user_entity_id = $this->input->post('user_entity_id');
        for($i=0;$i<count($user_entity_id);$i++){
            $data['user_entity_id'] = $user_entity_id[$i];
            $data['profile_id'] = $this->input->post('profile_id');
                if($this->input->post('p_read_'.$user_entity_id[$i].'') == ""){
                    $data['p_read']=0;
                }else{
                    $data['p_read'] = $this->input->post('p_read_'.$user_entity_id[$i].'');
                }
                if($this->input->post('p_create_'.$user_entity_id[$i].'') == ""){
                    $data['p_create']=0;
                }else{
                    $data['p_create'] = $this->input->post('p_create_'.$user_entity_id[$i].'');
                }
                if($this->input->post('p_update_'.$user_entity_id[$i].'') == ""){
                    $data['p_update']=0;
                }else{
                    $data['p_update'] = $this->input->post('p_update_'.$user_entity_id[$i].'');
                }
                if($this->input->post('p_delete_'.$user_entity_id[$i].'') == ""){
                    $data['p_delete']=0;
                }else{
                    $data['p_delete'] = $this->input->post('p_delete_'.$user_entity_id[$i].'');
                }
            $data['created_date_time']=date('Y-m-d');
            $data['modified_date_time'] =date('y-m-d');
            $profile_permissions_list = $this->db->query("select * from profile_permissions where user_entity_id ='".$data['user_entity_id']."' and profile_id ='".$data['profile_id']."'")->row();
            if(count($profile_permissions_list)>0){
                    if($this->input->post('p_read_'.$user_entity_id[$i].'') == ""){
                        $param['p_read']=0;
                        }else{
                        $param['p_read'] = $this->input->post('p_read_'.$user_entity_id[$i].'');
                    }
                    if($this->input->post('p_create_'.$user_entity_id[$i].'') == ""){
                        $param['p_create']=0;
                    }else{
                        $param['p_create'] = $this->input->post('p_create_'.$user_entity_id[$i].'');
                    }
                    if($this->input->post('p_update_'.$user_entity_id[$i].'') == ""){
                        $param['p_update']=0;
                    }else{
                        $param['p_update'] = $this->input->post('p_update_'.$user_entity_id[$i].'');
                    }
                    if($this->input->post('p_delete_'.$user_entity_id[$i].'') == ""){
                        $param['p_delete']=0;
                    }else{
                        $param['p_delete'] = $this->input->post('p_delete_'.$user_entity_id[$i].'');
                    }
                    $param['modified_date_time'] =date('y-m-d');
                $this->Generic_model->updateData('profile_permissions',$param,array('profile_permission_id'=>$profile_permissions_list->profile_permission_id));
            }else{
                $this->Generic_model->insertData("profile_permissions",$data);
            }
            
        }
        redirect('Admin/profiles');
        

    }
    //profile list for new clinic permission setting
    public function User_Access_Control(){
            $clinic_id = $this->session->userdata('clinic_id');
            $cond = '';
            //f($clinic_id!=0)
            $cond = "clinic_id=".$clinic_id." and archieve=0";
            $data['profiles_list'] = $this->Generic_model->getAllRecords('profiles', $cond, $order = '');
            $data['view'] = 'admin/user_access';
            $this->load->view('layout', $data);
        
    }
    //adding profile wise  permissions for new clinic 
    public function settings_view($category='') {
      
         $data['profiles_list'] = $this->db->query("select * from profiles group by profile_name")->result();
    
         $data['category'] = $category;
        $data['profile_permissions'] = $this->db->query("select * from std_uac_settings")->result();
            $data['view'] = 'admin/settings_view';
            $this->load->view('layout', $data);
    }
    public function settings_profile_permission($category='') {
      
         $data['profiles_list'] = $this->db->query("select * from profiles group by profile_name")->result();
    
         $data['category'] = $category;
        $data['profile_permissions'] = $this->db->query("select * from std_uac_settings")->result();
            $data['view'] = 'admin/settings_view_page';
            $this->load->view('layout', $data);
    }
    public function settings_entities($category='')
    {
        $data['profiles_list'] = $this->db->query("select * from profiles group by profile_name")->result();
    
         $data['category'] = $category;
        $data['profile_permissions'] = $this->db->query("select * from std_uac_settings")->result();
            $data['view'] = 'admin/settings_entities';
            $this->load->view('layout', $data);
    }
    public function save_entity_settings()
    {
        $user_id = $this->session->has_userdata('user_id');
        $user_entity_id = $this->input->post('user_entity_id');
        $category = $this->input->post('category_name');
        //echo "<pre>";print_r($_POST);exit;
        for($i=0;$i<count($user_entity_id);$i++){
            $data['user_entity_id'] = $user_entity_id[$i];
            $data['category'] = $category;
            $data['status'] = 1;
            $data['created_by'] = $user_id;
            $data['modified_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $profile_permissions_list = $this->db->query("select * from std_uac_entities where user_entity_id ='".$data['user_entity_id']."'")->row();
            if(count($profile_permissions_list)>0){
                if($this->input->post('p_create_'.$user_entity_id[$i].'') != 1){
                    $this->db->query("delete from std_uac_entities where user_entity_id=".$user_entity_id[$i]." and category='Clinic'");
                }
            }
            else
            {
                
                if($this->input->post('p_create_'.$user_entity_id[$i].'') == 1){
                    $this->Generic_model->insertData("std_uac_entities",$data);
                    
                }
            }
            
        }
        redirect('Admin/settings_view');
    }
    public function save_settings(){
        
  
        $user_entity_id = $this->input->post('user_entity_id');
        $category = $this->input->post('category');
        for($i=0;$i<count($user_entity_id);$i++){
            $data['user_entity_id'] = $user_entity_id[$i];
            $data['profile_name'] = $this->input->post('profile_name');
                if($this->input->post('p_read_'.$user_entity_id[$i].'') == ""){
                    $data['p_read']=0;
                }else{
                    $data['p_read'] = $this->input->post('p_read_'.$user_entity_id[$i].'');
                }
                if($this->input->post('p_create_'.$user_entity_id[$i].'') == ""){
                    $data['p_create']=0;
                }else{
                    $data['p_create'] = $this->input->post('p_create_'.$user_entity_id[$i].'');
                }
                if($this->input->post('p_update_'.$user_entity_id[$i].'') == ""){
                    $data['p_update']=0;
                }else{
                    $data['p_update'] = $this->input->post('p_update_'.$user_entity_id[$i].'');
                }
                if($this->input->post('p_delete_'.$user_entity_id[$i].'') == ""){
                    $data['p_delete']=0;
                }else{
                    $data['p_delete'] = $this->input->post('p_delete_'.$user_entity_id[$i].'');
                }
            $data['created_date_time']=date('Y-m-d');
            $data['category']=$category;
            $data['modified_date_time'] =date('y-m-d');
            $profile_permissions_list = $this->db->query("select * from std_uac_settings where user_entity_id ='".$data['user_entity_id']."' and profile_name ='".$this->input->post('profile_name')."'")->row();
            if(count($profile_permissions_list)>0){
                    if($this->input->post('p_read_'.$user_entity_id[$i].'') == ""){
                        $param['p_read']=0;
                        }else{
                        $param['p_read'] = $this->input->post('p_read_'.$user_entity_id[$i].'');
                    }
                    if($this->input->post('p_create_'.$user_entity_id[$i].'') == ""){
                        $param['p_create']=0;
                    }else{
                        $param['p_create'] = $this->input->post('p_create_'.$user_entity_id[$i].'');
                    }
                    if($this->input->post('p_update_'.$user_entity_id[$i].'') == ""){
                        $param['p_update']=0;
                    }else{
                        $param['p_update'] = $this->input->post('p_update_'.$user_entity_id[$i].'');
                    }
                    if($this->input->post('p_delete_'.$user_entity_id[$i].'') == ""){
                        $param['p_delete']=0;
                    }else{
                        $param['p_delete'] = $this->input->post('p_delete_'.$user_entity_id[$i].'');
                    }
                   $param['modified_date_time'] =date('y-m-d');
                $this->Generic_model->updateData('std_uac_settings',$param,array('std_uac_id'=>$profile_permissions_list->std_uac_id));
                
            }else{
                $this->Generic_model->insertData("std_uac_settings",$data);
            }
            
        }
        
        redirect('Admin/settings_view');
        

    }

    public function uac_entities(){
        $user_entity = $this->db->query("select * from user_entities")->result();
        $data['view'] = 'admin/uac_entities';
            $this->load->view('layout', $data);
    }
    public function get_entities()
    {
        $user_entity = $this->db->query("select * from user_entities")->result();
        $i=1;foreach($user_entity as $values){
            $role_permissions_list = $this->db->query("select * from std_uac_entities where user_entity_id ='".$values->user_entity_id."'")->row();
        ?>
        <div class='col-md-6'><input type='hidden'  id='user_entity_id_<?php echo $values->user_entity_id;?>' 
          class='entity_module' name='user_entity_id[]'' value='<?php echo $values->user_entity_id;?>' checked/><b><?php echo strtoupper($values->user_entity_name);?></b></label>
        </div>
        <div class='checkbox checkbox-success checkbox-inline'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $values->user_entity_id;?>'  type='checkbox'  name='p_create_<?php echo $values->user_entity_id;?>' value='1' <?php if(count($role_permissions_list)>0){echo "checked";}?>/><label for='p_create_<?php echo $values->user_entity_id;?>'> C </label>
        </div>
        <?php
        $i++;
        }
    }
    public function get_permission(){

                     $user_entity = $this->db->query("select * from std_uac_entities a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.category='".$this->input->post('category')."'")->result();
              $i=1;foreach($user_entity as $values){

                $role_permissions_list = $this->db->query("select * from std_uac_settings where user_entity_id ='".$values->user_entity_id."' and profile_name='".$this->input->post('profile_name')."'")->row();
             
                ?>
             
              

                   <div class='col-md-3'><input type='hidden'  id='user_entity_id_<?php echo $values->user_entity_id;?>' 
                  class='entity_module' name='user_entity_id[]'' value='<?php echo $values->user_entity_id;?>' checked/><b><?php echo strtoupper($values->user_entity_name);?></b></label>
                  </div>
                
                 
                   
                   

                    <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $values->user_entity_id;?>'  type='checkbox'  name='p_create_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_create == 1){echo "checked";}?>/><label for='p_create_<?php echo $values->user_entity_id;?>'> C </label>
                    </div>
                     <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green read_checkall comclass  p_read" id='p_read_<?php echo $values->user_entity_id;?>' type='checkbox' name='p_read_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_read == 1){echo "checked";}?>/><label for='p_read_<?php echo $values->user_entity_id;?>'> R </label>
                    </div>


                     <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green update_checkall comclass update" id='p_update_<?php echo $values->user_entity_id;?>'  type='checkbox'  name='p_update_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_update == 1){echo "checked";}?>/><label for='p_update_<?php echo $values->user_entity_id;?>'> U </label>
                    </div>
 
 
                    <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green delete_checkall comclass delete" id='p_delete_<?php echo $values->user_entity_id;?>' type='checkbox'  name='p_delete_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_delete == 1){echo "checked";}?>/><label for='p_delete_<?php echo $values->user_entity_id;?>'> D </label>
                    </div>

                

                  <?php $i++; } 
    }
}
