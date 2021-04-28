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
                            <button class="btn btn-primary btn-block" id="saveRole">Add Role</button>
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
        
        <h4 class="page-title"><?=$moduleInfo->module_name?> Module Entities</h4>
        <form method="post" action="<?=base_url('ModuleEntities/addEntities')?>">
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <input type="hidden" class="module_id" name="module_id" value="<?=$module_id?>">
                        <select class="form-control select2" id="" multiple="multiple" name="entities[]">
                            <?php 
                            if(count($user_entities) >  0){
                                $entities = explode(",", $selected_entities->entities);
                                foreach($user_entities as $value){
                                    if(in_array($value->user_entity_id, $entities)){
                                        continue;
                                    }
                                    ?>
                                    <!-- <option value="<?=$value->user_entity_id?>"><?=$value->user_entity_name?> <span class="badge badge-primary"><?=$value->category?></span></option> -->
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-block mt-3" name="add">Add Entities</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-12">
                <table class="customTable rolesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Entity Name</th>
                            <th>Parent Entity</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($module_entities) > 0){
                            $i = 1;
                            foreach($module_entities as $value){
                                
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><span class="trade_name"><?=$value->user_entity_name?></span></td>
                                    <td><span class="trade_name"><?=getUserEntityName($value->parent_id)?></span></td>
                                    <td><span><?=$value->category?></span></td>
                                    <td class="text-center">
                                        <span id="<?=$value->module_entity_id?>" class="statusBadge code <?=($value->status == 1)?'bg-success':'bg-danger'?>" style="color:#fff !important"><?=($value->status == 1)?'Active':'Inactive'?></span>
                                    </td>
                                    <td>
                                      <a href="<?=base_url('ModuleEntities/delMap/'.$value->module_entity_id)?>" onclick="return confirm('Are you sure to delete?')"><i class="fa fa-trash"></i></a>
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
  var module_id = $('.module_id').val()
  $.post("<?=base_url('ModuleEntities/getEntities')?>", {module_id: module_id}, function(response){
      var data = JSON.parse(response)
      $('.select2').select2({
        data: data,
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },
        templateSelection: function(data) {
            return data.text;
        }
    });
  })  
//      var data = [{
//   id: 0,
//   text: '<div style="color:green">enhancement</div>',
//   html: '<div style="color:green">enhancement</div><div><b>Select2</b> supports custom themes using the theme option so you can style Select2 to match the rest of your application.</div>',
//   title: 'enchancement'
// }, {
//   id: 1,
//   text: '<div style="color:red">bug</div>',
//   html: '<div style="color:red">bug</div><div><small>This is some small text on a new line</small></div>',
//   title: 'bug'
// }];
    
})
</script>