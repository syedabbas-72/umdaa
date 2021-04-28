<div class="page-bar">
   <div class="page-title-breadcrumb">
      <ol class="breadcrumb page-breadcrumb">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li><a class="parent-item active" href="<?php
            echo base_url("ClinicRoles/setup");
            ?>">Clinic Setup</a>
         </li>
      </ol>
   </div>
 </div>

 <div class="card">
     <div class="card-body">
        <?php
        if(count($roles) > 0){
            ?>
            <div class="pull-right mt-2">
                <button class="btn btn-primary">Add Role</button>
            </div>
            <?php
        }
        ?>
        <h4 class="page-title">Clinic Roles</h4>
        <div class="row">
            <div class="col-md-12">
            <?php 
            if(count($roles) <= 0){
                ?>
                <div class="jumbotron">
                    <h3 class="display-4">Hello, <?=$this->session->userdata('employee_name')?></h3>
                    <p class="lead my-3">To Customize your clinic with your specific roles, you can start from here or else you can use our application with default roles.</p>
                    <hr class="my-4">
                    <p>If you want to start setup click below.</p>
                    <p class="lead">
                        <a class="btn btn-primary btn-lg" href="#" role="button">Start Setup</a>
                    </p>
                </div>
                <?php 
            }
            else{
                ?>
                <table class="customTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($roles) > 0){
                            $i = 1;
                            foreach($roles as $value){
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><span><?=$value->clinic_role_name?></span></td>
                                    <td class="text-center">
                                        <span class="font-weight-normal badge <?=($value->status == 1)?'badge-success':'badge-danger'?>"><?=($value->status == 1)?'Active':'Inactive'?></span>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        else{
                            ?>
                            <tr>
                                <td class="text-center" colspan="4">No Records Found</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
            
                
            </div>
        </div>

     </div>
</div>