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
            echo base_url("ModuleEntities");
            ?>">Modules</a>
         </li>
      </ol>
   </div>
 </div>

 <!-- Add Modal -->
 <div class="modal fade" role="dialog" id="add_modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add New Module</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="<?=base_url('ModuleEntities/addNewModule')?>" method="post">
            <div class="form-group">
                <label class="font-weight-bold">Module Name</label>
                <input class="form-control" type="text" name="module_name" required >  
            </div>
            <div class="form-group text-center">
                <button class="btn btn-app" name="submitModule">Submit</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
 <!-- Add Modal Ends -->


 <div class="card">
     <div class="card-body">
        <h4 class="page-title">Modules
        <button class="btn btn-app pull-right" data-target="#add_modal" data-toggle="modal">Add Module</button>
        </h4>
        <div class="row">
            <div class="col-md-12">
                <table class="customTable rolesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Module Name</th>
                            <!-- <th>No Of Employees</th> -->
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($modules) > 0){
                            $i = 1;
                            foreach($modules as $value){
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><span class="trade_name"><?=$value->module_name?></span></td>
                                    <!-- <td>0</td> -->
                                    <td class="text-center">
                                        <span id="<?=$value->module_id?>" class="statusBadge code <?=($value->status == 1)?'bg-success':'bg-danger'?>" style="color:#fff !important"><?=($value->status == 1)?'Active':'Inactive'?></span>
                                    </td>
                                    <td>
                                        <a href="<?=base_url('ModuleEntities/module_entities/'.$value->module_id)?>" class="mr-1"><i class="fas fa-cog"></i></a>
                                        <a class="mr-1" href="#"><i class="fa fa-edit"></i></a>
                                        <a href="#"><i class="fa fa-trash"></i></a>
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
