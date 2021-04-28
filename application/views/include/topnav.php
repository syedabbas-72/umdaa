 
<style type="text/css">
    .ui-autocomplete {
        z-index: 9999;
        width: 200px;
    }
    .page-header.navbar .page-logo
    {
        padding: 6px 20px 0px 20px !important;
    }
</style>

<!-- start header -->
<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <!-- logo start -->
        <div class="page-logo">
            <?php $clinic_logo=$this->session->userdata('clinic_logo');
            $clinic_emblem=$this->session->userdata('clinic_emblem');
            $clinic_id=$this->session->userdata('clinic_id');
            if($this->session->userdata('assigned_roles') != ""){
                $displayRoles = $this->db->query("select group_concat(clinic_role_name) as dispName from clinic_roles where clinic_role_id IN (".$this->session->userdata('assigned_roles').")")->row();
                $displayRole = $displayRoles->dispName;
            }
            else{
                $sesroleInfo = $this->db->query("select * from roles where role_id='".$this->session->userdata('role_id')."'")->row();
                $displayRole = $sesroleInfo->role_name;
            }
            ?>
            <img height="40" id="navLogo" alt="" src="<?php echo base_url('assets/img/umdaa_clinic_logo.png'); ?>">
            <img alt="" style="display: none;width: 100%;" id="emblem" src="<?php echo base_url('assets/img/umdaa_logo.png'); ?>" >
        </div>
        <!-- logo end -->
        
        <ul class="nav navbar-nav navbar-left in">
            <li><a href="#" class="menu-toggler sidebar-toggler font-size-20"><i class="fas fa-exchange-alt" aria-hidden="true"></i></a></li>
        </ul>

        <ul class="nav navbar-nav navbar-left in">
            <!-- start full screen button -->
            <li><a href="javascript:;" class="fullscreen-click font-size-20"><i class="fa fa-arrows-alt"></i></a></li>
            <!-- end full screen button -->
        </ul>
        <?php if(getPropertyAccess('Patient Search')) {  // Check Property Access ?>
            <ul class="nav navbar-nav navbar-left in">
                <!-- start full screen button -->
                <li style="padding: 10px;"><input class="form-control srchInput" id="psearch" placeholder="Search Patient..." type="text"></li>
                <!-- end full screen button -->
            </ul>
        <?php } ?>
        <!-- start mobile menu -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- end mobile menu -->
        <!-- start header menu -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">

                <!-- start manage user dropdown -->
                <?php
                if($this->session->userdata('clinic_id') != 0){
                    ?>
                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span class="code bg-primary text-uppercase" style="color:#fff !important"><?php echo ucwords($this->session->userdata('package_name')); ?></span>
                        </a>
                    </li>
                    <?php
                }
                ?>

                <li class="dropdown dropdown-user">
                    <a style="color: #555; padding-top: 5px !important" href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <div style="min-width: 150px; margin-top: 0px !important">
                        <div class="pull-left">
                            <img alt="" class="img-circle user_avatar" style="width: 40px" src="<?php echo base_url(); ?>assets/img/profile_pic.png" />
                        </div>
                        <div class="pull-right user_avatar_info text-right" style="padding-top: 3px;">
                            <?php 
                            if(trim($this->session->userdata('employee_name')) == ""){
                                $display_name = $this->session->userdata('user_name');
                            }
                            else{
                                $display_name = $this->session->userdata('employee_name');
                            }
                            ?>
                            <b><?=ucwords($display_name)?></b><br>
                            <small><i><?php echo ucwords($displayRole); ?></i></small>
                            <i class="fa fa-angle-down"></i>
                        </div>
                    </div>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li><a href="<?php echo base_url('settings/change_password'); ?>"><i class="fa fa-lock"></i>Change Password</a></li>
                        <li><a href="<?php echo base_url('authentication/logout'); ?>"><i class="fa fa-sign-out-alt"></i>Log Out</a></li>
                    </ul>
                </li>
                

            </ul>
        </div>
    </div>
</div>
<!-- end header -->