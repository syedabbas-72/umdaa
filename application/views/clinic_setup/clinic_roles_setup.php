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
            ?>">Clinic Role Setup</a>
         </li>
      </ol>
   </div> 
 </div>



 <div class="card">
     <div class="card-body">
        <h4 class="page-title">Clinic Role Permissions <i class="fas fa-caret-right mx-3"></i> <?=$clinic_role_name?></h4>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                   <div class="col-12">
                     <form action="<?=base_url('ClinicRoles/storePermissions')?>" method="post">

                        <input type="hidden" value="<?=$clinic_role_id?>" name="clinic_role_id">
                        <input type="hidden" value="<?=$role_id?>" name="role_id">
                        <?php
                        if($role_id == "7" || $role_id == "2"){
                            ?>
                            <h4 class="border-bottom">Property Access</h4>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="checkbox checkbox-icon-black mb-2">
                                        <input id="pharmacy_delete_access" type="checkbox" name="pharmacy_delete_access" value="1" <?=($propertyPermissions->pharmacy_del_access == "1")?'checked':''?>>
                                        <label for="pharmacy_delete_access">
                                            Drug Deletion Access
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-icon-black mb-2">
                                        <input id="pharmacy_edit_access" type="checkbox" name="pharmacy_edit_access" value="1" <?=($propertyPermissions->pharmacy_edit_access == "1")?'checked':''?>>
                                        <label for="pharmacy_edit_access">
                                            Quantity Edit Access
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        
                        <h4 class="border-bottom">List Of Entities</h4>
                        <?php
                        if(count($entities) > 0){
                            foreach($entities as $val){
                                $entityInfo = userEntityInfo($val->entity_id);
                                // if($entityInfo->category == "Ionic" || $entityInfo->category == "Angular")
                                //     continue;
                                $check = $this->db->query("select * from clinic_role_permissions where clinic_id='".$clinic_id."' and clinic_role_id='".$clinic_role_id."' and entity_id='".$val->entity_id."'")->num_rows();

                                ?>
                                <div class="checkbox checkbox-icon-black mb-2">
                                    <input id="entity_<?=$val->entity_id?>" type="checkbox" name="entities[]" value="<?=$val->entity_id?>" <?=($check == 1)?'checked':''?>>
                                    <label for="entity_<?=$val->entity_id?>">
                                        <?=$entityInfo->user_entity_name?>
                                    </label>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <button class="btn btn-app my-2" name="submit" type="submit">Submit</button>
                    </form>
                   </div>
                </div>
                
            </div>
        </div>

     </div>
</div>
