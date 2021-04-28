<style>

    .statusBadge{
        cursor:pointer;
    }
</style>

<div class="page-bar">
   <div class="page-title-breadcrumb">
      <ol class="breadcrumb page-breadcrumb">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li><a class="parent-item active" href="<?php
            echo base_url("ClinicRoles");
            ?>">Clinic Roles</a>
         </li>
      </ol>
   </div>
 </div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRole">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Roles</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="roleDiv">
                    <label class="font-weight-bold">Role Name</label>
                    <div class="row">
                        <div class="col-md-8">
                            <input class="form-control w-100 mr-2" type="text" id="role_name">
                        </div>
                        <div class="col-md-4">
                            <!-- <button class="btn btn-primary btn-block" id="saveRole">Add Role</button> -->
                        </div>
                    </div>
                </div>
                <div class="alert alert-success mt-2 px-2 suc-alert d-none">
                    <p style="padding:0px !important">Role Added Successfully. Please add entities to give access to the role.</p>
                </div>
                <div class="alert alert-danger mt-2 px-2 err-alert d-none">
                    <p style="padding:0px !important">Error Occured. Same Role Already Exists. Please change the role name.</p>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Add Role Modal Ends -->


 <div class="card">
     <div class="card-body">
        <?php
        if(count($clinicRoles) > 0){
            ?>
            <div class="pull-right mt-2">
                <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#addRole">Add Role</button> -->
            </div>
            <?php
        }
        ?>
        <h4 class="page-title">Clinic Roles</h4>
        <div class="row">
            <div class="col-md-12">
                <table class="customTable rolesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <!-- <th>No Of Employees</th> -->
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($clinicRoles) > 0){
                            $i = 1;
                            foreach($clinicRoles as $value){
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><span class="trade_name"><?=$value->clinic_role_name?></span></td>
                                    <!-- <td>0</td> -->
                                    <td class="text-center">
                                        <span id="<?=$value->clinic_role_id?>" class="statusBadge code <?=($value->status == 1)?'bg-success':'bg-danger'?>" style="color:#fff !important"><?=($value->status == 1)?'Active':'Inactive'?></span>
                                    </td>
                                    <td>
                                        <?php if($this->session->userdata('user_id') != 1) { ?>
                                            <a href="<?=base_url('ClinicRoles/role_setup/'.$value->clinic_role_id)?>" class="mr-1"><i class="fas fa-cog"></i></a>
                                        <?php }
                                        else{
                                            ?>
                                        <a class="mr-1" href="#"><i class="fa fa-edit"></i></a>
                                        <a href="#"><i class="fa fa-trash"></i></a>
                                            <?php
                                        } ?>
                                        
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
              
                
            </div>
        </div>

     </div>
</div>

<script>
    $(document).ready(function(){
        $('.rolesTable').DataTable()
        // $('#addRole').modal()s

        // $('#role_name').on('keypress',function(e) {
        //     var role_name = $(this).val()
        //     if(e.which == 13) {
        //         if(role_name.length > 0){
        //             addRole(role_name)
        //         }
        //     }
        // });

        $('#saveRole').on('click',function() {
            var role_name = $(this).val()
            if(role_name.length > 0){
                addRole(role_name)
            }
        });



    })
</script>
<script>
    
    function addRole(role_name){
        if(role_name.length > 0){
            $.post(
                "<?=base_url('ClinicRoles/AddRole')?>",
                {role_name: role_name},
                function(data){
                    if(data == 1){
                        $('.roleDiv').addClass('d-none');
                        $('.suc-alert').removeClass('d-none')
                        $('.err-alert').addClass('d-none')
                    }
                    else{
                        $('.err-alert').removeClass('d-none')
                        $('.suc-alert').addClass('d-none')
                    }
                }
            );
        }
    }
</script>