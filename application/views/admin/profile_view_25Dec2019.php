  <style type="text/css">
    .checkbox label{
  float: left;
}
.setting_edit_padding{
  padding: 10px;
}
  </style>
   <div class="row page-header">
			<div class="col-lg-6 align-self-center ">
			  
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item"><a href="#">Profiles</a></li>
					<li class="breadcrumb-item active"><a href="#">Profile Permissions</a></li>						
				</ol>
			</div>
			
		</div>
   
   <section class="main-content">
    <div class="row">
                <div class="col-md-12">
                    <div class="card">
           <div class="card-header card-default">
            
                        </div>
            
                        <div class="card-body">
    <form method="post" id="roleEdit_form" action="<?php echo base_url('Admin/profile_permision_edit');?>" enctype="multipart/form-data">
            <div class="row">
               <div class="col-sm-4">
                  <div class="form-inline  setting_edit_padding">
                     <h4 for="profile_name " style="margin-right: 20px">Profile Name :</h4>
                     <input type="text" class="form-control" id="profile_name" name="profile_name"  readonly value="<?php echo $profile_list->profile_name;?>">
                     <input type="hidden" class="form-control" id="profile_id" name="profile_id"  readonly value="<?php echo $profile_list->profile_id;?>">
                  </div>
               </div>
            </div>
          
            <div class="row">
               <div class="col-sm-12   setting_edit_padding">
                  <h4>Profile Entities:</h4>
               </div>
               <div class="col-sm-12">

                  <div class="form-inline setting_edit_padding">
                  
                    <div class='checkbox checkbox-success checkbox-inline  col-md-4'>
                        
                     </div>

        
                     <div class='checkbox checkbox-success checkbox-inline  col-md-2'>
                        <input  class="icheckbox_flat-green"  id='p_create_all' type='checkbox' /><label for='p_create_all'>Select all</label>
                     </div>
                    
                     <div class='checkbox checkbox-success checkbox-inline  col-md-2'>
                       <input class="icheckbox_flat-green" id='p_read_all' type='checkbox' /><label for='p_read_all'>Select all</label>
                     </div>
                
                     <div class='checkbox checkbox-success checkbox-inline  col-md-2'>
                        <input class="icheckbox_flat-green"  id='p_update_all' type='checkbox'   /><label for='p_update_all'>Select all</label>
                     </div>
              
               
                     <div class='checkbox checkbox-success checkbox-inline  col-md-2'>
                        <input class="icheckbox_flat-green"  id='p_delete_all' type='checkbox' /><label for='p_delete_all'>Select all</label>
                     </div>
                
            </div>

              <?php 
			  
			  $i=1;foreach($user_entity as $values){
                $role_permissions_list = $this->db->query("select * from profile_permissions where user_entity_id ='".$values->user_entity_id."' and profile_id = '".$profile_list->profile_id."'" )->row();
                ?>
               <div class='col-sm-12 setting_edit_padding'>
              <div class="form-inline">
                   <div class='col-md-4'><input type='hidden'  id='user_entity_id_<?php echo $values->user_entity_id;?>' 
                  class='entity_module' name='user_entity_id[]' value='<?php echo $values->user_entity_id;?>' checked/><label for='user_entity_id_<?php echo $values->user_entity_id;?>' style='background-color: #ffffff;'><b><?php echo strtoupper($values->user_entity_name);?></b></label>
                  </div>
                

                    <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $values->user_entity_id;?>'  type='checkbox'  name='p_create_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_create == 1){echo "checked";}?>/><label for='p_create_<?php echo $values->user_entity_id;?>'> Create </label>
                    </div>


                 
                    <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="read_checkall comclass  p_read" id='p_read_<?php echo $values->user_entity_id;?>' type='checkbox' name='p_read_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_read == 1){echo "checked";}?>/><label for='p_read_<?php echo $values->user_entity_id;?>'> Read </label>
                    </div>
                   
                     <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green update_checkall comclass update" id='p_update_<?php echo $values->user_entity_id;?>'  type='checkbox'  name='p_update_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_update == 1){echo "checked";}?>/><label for='p_update_<?php echo $values->user_entity_id;?>'> Update </label>
                    </div>
 

                    <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green delete_checkall comclass delete" id='p_delete_<?php echo $values->user_entity_id;?>' type='checkbox'  name='p_delete_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_delete == 1){echo "checked";}?>/><label for='p_delete_<?php echo $values->user_entity_id;?>'> Delete </label>
                    </div>

                </div>
                </div>

                  <?php $i++; } ?>
         
                
    </div>
    

  <div class="col-md-12 text-center setting_edit_padding">
   <button type="submit" class="btn btn-success" id="save" name="Save" value="Save" >Save</button>
   <button type="button" class="btn btn-warning" id="cancel" name="cancel"  value="Cancel" onclick="window.history.go(-1);">Cancel</button>
  </div>
      </div>
   </div>
   
   </form>
</div>
</div>
</div>
</section>

<script src="<?php echo base_url('assets/lib/jquery/dist/jquery.min.js'); ?>"></script>
<script type="text/javascript">
   //read
   
$("#p_read_all").change(function(){   
    $(".p_read").prop('checked', $(this).prop("checked")); 
});
$('.p_read').change(function(){ 
    if(false == $(this).prop("checked")){ 
        $("#p_read_all").prop('checked', false); 
    }
    if ($('.p_read:checked').length == $('.checkbox').length ){
        $("#p_read_all").prop('checked', true);
    }
});


 //create
$("#p_create_all").change(function(){  
    $(".create").prop('checked', $(this).prop("checked")); 
});
$('.create').change(function(){ 
    
    if(false == $(this).prop("checked")){ 
        $("#p_create_all").prop('checked', false); 
    }
    if ($('.create:checked').length == $('.checkbox').length ){
        $("#p_create_all").prop('checked', true);
    }
});

//update
$("#p_update_all").change(function(){  
    $(".update").prop('checked', $(this).prop("checked")); 
});
$('.update').change(function(){ 
    if(false == $(this).prop("checked")){ 
        $("#p_update_all").prop('checked', false); 
    }
    if ($('.update:checked').length == $('.checkbox').length ){
        $("#p_update_all").prop('checked', true);
    }
});

//delete
$("#p_delete_all").change(function(){  
    $(".delete").prop('checked', $(this).prop("checked")); 
});
$('.delete').change(function(){ 
    if(false == $(this).prop("checked")){ 
        $("#p_delete_all").prop('checked', false); 
    }
    if ($('.delete:checked').length == $('.checkbox').length ){
        $("#p_delete_all").prop('checked', true);
    }
});
    </script>
</div>
<script>   
 var cookies = document.cookie.split(";");  
 for (var i = 0; i < cookies.length; i++) {  
 var cookie = cookies[i];      
 var eqPos = cookie.indexOf("=");    
 var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;   
 createCookie(name, "", -1);   
 }
 </script> 
              </div>
            </div>
          </div>
        </div>
