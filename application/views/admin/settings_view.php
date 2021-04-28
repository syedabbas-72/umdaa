   <div class="row page-header">
			<div class="col-lg-6 align-self-center ">
			  
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item"><a href="#">SETTINGS</a></li>
					<li class="breadcrumb-item active"><a href="#"></a></li>						
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
    
    	<div class="row">
    		<div class="col-sm-12">
			<ul>
			<li>Hub Settings</li>
			<li>Clinic Settings
				<ul>
				<li><a href="<?php echo base_url('Admin/settings_entities'); ?>">Entities</a></li>
				<li>Roles</li>
				<li><a href="<?php echo base_url('Admin/settings_profile_permission'); ?>">Profile Permissions</a></li>
				</ul>
			</li>
			</ul>
            </div>   
   </div>
   
  
</div>
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
