<!-- Lightbox-->
        <link href="<?php echo base_url(); ?>assets/lib/lightbox2/dist/css/lightbox.css" rel="stylesheet">
        <style type="text/css">
            .close{
    background: red;
    padding: 10px;
    position: absolute;
    color: white !important;
    right: 2px;
    top: 2px;
    z-index: 999;
            }
    
        </style>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active"><a href="<?=base_url("settings/change_password")?>">Change Password</a></li>
        </ol>
    </div>
</div>


 
   <div class="row">
        <div class="col-md-12">
            <div class="card">
             
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane active">
                                	<!-- <form method="POST" action="<?php echo base_url('settings/change_password');?>"> -->
                                		<div class="row">
                                			<div class="col-md-4">
                                				<div class="form-group">
                                				   <label class="col-form-label">Old Password <span style="color:red;">*</span></label>
                                				   <input type="password" maxlength="8" name="old_password" class="form-control old_password" required="">
                                			    </div>
                                			</div>
                                			<div class="col-md-4">
                                				<div class="form-group">
                                				   <label class="col-form-label">New Password <span style="color:red;">*</span></label>
                                				   <input type="password" maxlength="8" name="password" class="form-control new_password" required="">
                                			    </div>
                                			</div>
                                			<div class="col-md-4">
                                				<div class="form-group">
                                				   <label class="col-form-label">Confirm Password <span style="color:red;">*</span></label>
                                				   <input type="password" maxlength="8" name="cnfrm_password" class="form-control cnfrm_password" required="">
                                			    </div>
                                			</div>
                                		</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><span style="color:red;">*</span> Password Max Length Should Be 8 Characters Only</p>
                                            </div>
                                        </div>
                                		<div class="row">
                                			<div class="col-md-12 text-center">
                                                <button class="btn btn-primary save_passwords" name="submit">Save <i class="fa fa-spinner fa-spin spinloader"></i></button>
                                            </div>
                                		</div>
                                	<!-- </form> -->
                                </div>
                            </div>
                              <!-- /.tab-pane -->
                        </div>
                    </div>
                </div>
                    <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
            <!-- /.nav-tabs-custom -->
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.spinloader').hide();
            $('.save_passwords').on("click",function(){
                var new_password = $('.new_password').val();
                var cnfrm_password = $('.cnfrm_password').val();
                var old_password = $('.old_password').val();
                if(new_password=="" || cnfrm_password=="" || old_password=="")
                {
                    alert("Fill Required Fields");
                }
                else if(new_password!=cnfrm_password)
                {
                    alert("Passwords Not Matched");
                }
                else
                {
                    $('.spinloader').show();
                    $.ajax({
                        url : "<?=base_url('settings/change_password')?>",
                        method : "POST",
                        data : {"new_password":new_password,"old_password":old_password,"Action":"ChangePassword"},
                        success : function(data) {                  
                            $('.spinloader').hide();
                            var data = data.trim();
                            if(data=="1")
                            {
                                alert("Password Changed");
                                location = "<?=base_url('settings/change_password')?>";
                            }
                            else
                            {
                                alert("Old Password Entered Was Wrong");
                            }
                        }
                    });
                }

            });
            $("form").submit(function(e){
                e.preventDefault(e);
                var new_password = $('.new_password').val();
                var cnfrm_password = $('.cnfrm_password').val();
                if(new_password == cnfrm_password)
                {
                    $('form').submit();
                }
                else
                {
                    alert("Passwords Not Matched");
                }
            });
        });
    </script>