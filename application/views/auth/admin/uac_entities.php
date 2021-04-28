   <div class="row page-header">
			<div class="col-lg-6 align-self-center ">
			  
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item"><a href="#">SETTINGS</a></li>
					<li class="breadcrumb-item active"><a href="#">standard uac settings</a></li>						
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
    <form method="post" id="roleEdit_form" action="<?php echo base_url('Admin/save_settings');?>" enctype="multipart/form-data">
   

            <div class="row">
              
               <div class="col-sm-12">

                  <div class="form-inline">
                    
                  
                    <div class='col-md-3'>
                        <label><b>Entity Name</b></label>
                     </div>
                     <input type="hidden" id="category" name="category" value="<?php echo $category; ?>">
                    <?php 
                 $i=1;foreach($user_entity as $values){

              
             
                ?>
             
              

                   <div class='col-md-3'><input type='hidden'  id='user_entity_id_<?php echo $values->user_entity_id;?>' 
                  class='entity_module' name='user_entity_id[]'' value='<?php echo $values->user_entity_id;?>' checked/><b><?php echo strtoupper($values->user_entity_name);?></b></label>
                  </div>
                   <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green create_checkall comclass create" id='<?php echo $values->user_entity_id;?>'  type='checkbox'  name='<?php echo $values->user_entity_id;?>' value='1'/><label for='<?php echo $values->user_entity_id;?>'> <?php echo strtoupper($values->user_entity_name);?></label>
                    </div>
                
                 
                   
                   

                

                  <?php $i++; } 
    ?>
            </div>

              <div class='col-sm-12 setting_edit_padding'>
              <div class="form-inline" id="permission_list">


              
         </div>
                </div>
                
    </div>
    

  <div class="col-md-12">
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

function get_permission_by_profiles(profile){
var url = "<?php echo base_url('admin/get_permission'); ?>";
$.ajax({
            type: 'POST',
            url: url,
            data: {
                profile_name: profile},
            beforeSend: function (data) {
                $('#permission_list').html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            },
            success: function (data) {
              
               $('#permission_list').html("");
                $('#permission_list').html(data);
          

            }
        });
}
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
