<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA CLINICS</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?=base_url('Admin/profiles')?>">Profiles Master</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Profile Permissions</li>
        </ol>

        <div class="pull-right tPadding bcBtn">
            <a href="<?= base_url('Admin/profiles'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-backward"></i> Back</a>
        </div>
    </div>
</div>

<div class="row">

    <!-- Page content start -->
    <div class="col-lg-8 pull-left">
        <!-- page title -->
        <div class="card" style="padding:10px 15px;">
            <div class="page-title">
                Entity Permissions &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; <q> <?=$profile_info->profile_name?> </q>
            </div>
            <div class="accessability">
                <form method="post" id="roleEdit_form" action="<?php echo base_url('Admin/profile_permision_edit');?>" enctype="multipart/form-data">

                    <input type="hidden" class="form-control" id="profile_name" name="profile_name" readonly value="<?=$profile_info->profile_name?>">
                    <input type="hidden" class="form-control" id="profile_id" name="profile_id"  readonly value="<?=$profile_info->profile_id?>">
                    
                    <table class="table customTable">
                        <tr>
                            <th style="width: 40%">Entity</th>
                            <th style="width: 15%" class="checkbox checkbox-success checkbox-inline text-center"><input class="icheckbox_flat-green" id='p_create_all' type='checkbox' /><label for='p_create_all'>Select all</label></th>
                            <th style="width: 15%" class="checkbox checkbox-success checkbox-inline text-center"><input class="icheckbox_flat-green" id='p_read_all' type='checkbox' /><label for='p_read_all'>Select all</label></th>
                            <th style="width: 15%" class="checkbox checkbox-success checkbox-inline text-center"><input class="icheckbox_flat-green" id='p_update_all' type='checkbox'/><label for='p_update_all'>Select all</label></th>
                            <th style="width: 15%" class="checkbox checkbox-success checkbox-inline text-center"><input class="icheckbox_flat-green" id='p_delete_all' type='checkbox' /><label for='p_delete_all'>Select all</label></th>
                        </tr>
                        <?php
                        $i = 1;
                        foreach($entities as $row){
                            $role_permissions_list = $this->db->select('profile_permission_id, profile_id, user_entity_id, p_create, p_read, p_update, p_delete')->from('profile_permissions')->where('user_entity_id =', $row['user_entity_id'])->where('profile_id =',$profile_id)->get()->row();
                            ?>
                            <tr>
                                <td>
                                    <input type='hidden' id='user_entity_id_<?php echo $row['user_entity_id'];?>' 
                                    class='entity_module' name='user_entity_id[]' value='<?php echo $row['user_entity_id'];?>'/>
                                    <span><?=$row['user_entity_name']?></span><br>
                                    <small><i><?=$row['user_entity_alias']?></i></small>
                                </td>
                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $row['user_entity_id'];?>'  type='checkbox'  name='p_create_<?php echo $row['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_create == 1){echo "checked";}?>/><label for='p_create_<?php echo $row['user_entity_id'];?>'> Create </label>
                                </td>
                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="read_checkall comclass  p_read" id='p_read_<?php echo $row['user_entity_id'];?>' type='checkbox' name='p_read_<?php echo $row['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_read == 1){echo "checked";}?>/><label for='p_read_<?php echo $row['user_entity_id'];?>'> Read </label>
                                </td>
                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green update_checkall comclass update" id='p_update_<?php echo $row['user_entity_id'];?>'  type='checkbox'  name='p_update_<?php echo $row['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_update == 1){echo "checked";}?>/><label for='p_update_<?php echo $row['user_entity_id'];?>'> Update </label>
                                </td>
                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green delete_checkall comclass delete" id='p_delete_<?php echo $row['user_entity_id'];?>' type='checkbox'  name='p_delete_<?php echo $row['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_delete == 1){echo "checked";}?>/><label for='p_delete_<?php echo $row['user_entity_id'];?>'> Delete </label>
                                </td>
                            </tr>
                            <?php // Check for child entities
                            if(count($row['child_entities']) > 0){
                                // Child Entitie exists
                                // Make an other table to show up the child entities
                                $childEntities = $row['child_entities'];
                                $count = count($childEntities);
                                $j=1;
                                foreach($childEntities as $child){
                                    $role_permissions_list = $this->db->select('profile_permission_id, profile_id, user_entity_id, p_create, p_read, p_update, p_delete')->from('profile_permissions')->where('user_entity_id =', $child['user_entity_id'])->where('profile_id =',$profile_id)->get()->row();
                                    ?>
                                    <tr>
                                        <td class="<?php if($j == $count){ echo 'child_end'; }else{ echo 'child';} ?>">
                                            <input type='hidden' id='user_entity_id_<?php echo $child['user_entity_id'];?>' 
                                            class='entity_module' name='user_entity_id[]' value='<?php echo $child['user_entity_id'];?>'/>
                                            <span><?=$child['user_entity_name']?></span><br><small><i><?=$child['user_entity_alias']?></i></small>
                                        </td>
                                        <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $child['user_entity_id'];?>'  type='checkbox'  name='p_create_<?php echo $child['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_create == 1){echo "checked";}?>/><label for='p_create_<?php echo $child['user_entity_id'];?>'> Create </label>
                                        </td>
                                        <td class='checkbox checkbox-success checkbox-inline text-center'><input class="read_checkall comclass  p_read" id='p_read_<?php echo $child['user_entity_id'];?>' type='checkbox' name='p_read_<?php echo $child['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_read == 1){echo "checked";}?>/><label for='p_read_<?php echo $child['user_entity_id'];?>'> Read </label>
                                        </td>
                                        <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green update_checkall comclass update" id='p_update_<?php echo $child['user_entity_id'];?>'  type='checkbox'  name='p_update_<?php echo $child['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_update == 1){echo "checked";}?>/><label for='p_update_<?php echo $child['user_entity_id'];?>'> Update </label>
                                        </td>
                                        <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green delete_checkall comclass delete" id='p_delete_<?php echo $child['user_entity_id'];?>' type='checkbox'  name='p_delete_<?php echo $child['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_delete == 1){echo "checked";}?>/><label for='p_delete_<?php echo $child['user_entity_id'];?>'> Delete </label>
                                        </td>
                                    </tr>
                                    <?php // Check for sub child entities
                                    if(count($child['child_entities'] > 0)){
                                        // Sub Child Entitie Exists
                                        // Make an other table to show up the child entities
                                        $subChildEntities = $child['child_entities'];
                                        $subCount = count($subChildEntities);
                                        $z=1;
                                        foreach($subChildEntities as $subChild){
                                            $role_permissions_list = $this->db->select('profile_permission_id, profile_id, user_entity_id, p_create, p_read, p_update, p_delete')->from('profile_permissions')->where('user_entity_id =', $subChild['user_entity_id'])->where('profile_id =',$profile_id)->get()->row();
                                            ?>
                                            <tr>
                                                <td class="<?php if($z == $subCount){ echo 'sub_child_end'; }else{ echo 'sub_child';} ?>">
                                                    <input type='hidden' id='user_entity_id_<?php echo $subChild['user_entity_id'];?>' 
                                                    class='entity_module' name='user_entity_id[]' value='<?php echo $subChild['user_entity_id'];?>'/>
                                                    <span><?=$subChild['user_entity_name']?></span><br><small><i><?=$subChild['user_entity_alias']?></i></small>
                                                </td>
                                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $subChild['user_entity_id'];?>'  type='checkbox'  name='p_create_<?php echo $subChild['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_create == 1){echo "checked";}?>/><label for='p_create_<?php echo $subChild['user_entity_id'];?>'> Create </label>
                                                </td>
                                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="read_checkall comclass  p_read" id='p_read_<?php echo $subChild['user_entity_id'];?>' type='checkbox' name='p_read_<?php echo $subChild['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_read == 1){echo "checked";}?>/><label for='p_read_<?php echo $subChild['user_entity_id'];?>'> Read </label>
                                                </td>
                                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green update_checkall comclass update" id='p_update_<?php echo $subChild['user_entity_id'];?>'  type='checkbox'  name='p_update_<?php echo $subChild['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_update == 1){echo "checked";}?>/><label for='p_update_<?php echo $subChild['user_entity_id'];?>'> Update </label>
                                                </td>
                                                <td class='checkbox checkbox-success checkbox-inline text-center'><input class="icheckbox_flat-green delete_checkall comclass delete" id='p_delete_<?php echo $subChild['user_entity_id'];?>' type='checkbox'  name='p_delete_<?php echo $subChild['user_entity_id'];?>' value='1' <?php if($role_permissions_list->p_delete == 1){echo "checked";}?>/><label for='p_delete_<?php echo $subChild['user_entity_id'];?>'> Delete </label>
                                                </td>
                                            </tr>
                                            <?php                                                    
                                            $z++;
                                        }
                                    }
                                    $j++;
                                }
                            }
                            $i++;
                        }
                        ?>
                        <tr>
                            <td colspan="5">
                                <div class="form-group pull-right">
                                    <input type="submit" name="Submit" Value="Save" class="btn customBtn">
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 pull-right">
        <!-- page title -->
        <div class="card" style="padding: 10px 15px;">
            <div class="page-title">
                Property Access &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; <q> <?=$profile_info->profile_name?> </q>
            </div>
            <div class="accessability">
                <form method="post" id="roleEdit_form" action="<?php echo base_url('Admin/property_accessibility');?>" enctype="multipart/form-data">

                    <table cellpadding="0" cellspacing="0" class="table customTable">
                        <tr>
                            <th style="width: 60%">Property</th>
                            <th style="width: 40%" class="checkbox checkbox-success checkbox-inline text-center">
                                <input class="icheckbox_flat-green" id='property_all' type='checkbox' /><label for='property_all'>Accessibility</label>
                            </th>
                        </tr>
                        <?php
                        if(count($properties) > 0){
                            $i = 0;
                            foreach($properties as $property){
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="propertyAccessibility[<?=$i?>][profile_id]" value="<?=$profile_info->profile_id?>">
                                        <input type="hidden" name="propertyAccessibility[<?=$i?>][user_property_id]" value="<?=$property['user_property_id']?>">
                                        <input type="hidden" name="propertyAccessibility[<?=$i?>][status]" value="0">
                                        <span><?=$property['property_name']?></span>
                                    </td>
                                    <td class="checkbox checkbox-success checkbox-inline text-center">
                                        <input type="checkbox" id="chkBox_<?=$property['user_property_id']?>" class="property_access" value="1" name="propertyAccessibility[<?=$i?>][status]" <?php if(in_array($property['user_property_id'],$properties_accessibility)) { ?> checked="checked" <?php } ?> ><label for='chkBox_<?=$property['user_property_id']?>'> Accessible </label>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="2">
                                <div class="form-group pull-right">
                                    <input type="submit" name="submit" Value="Save" class="btn customBtn">
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<?php /*
<section class="main-content">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="row page-title">
                    <div class="col-md-12">
                        Profile Permissions &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; <q> <?=$profile_info->profile_name?> </q>
                    </div>
                </div>

                <div class="card-body">
                    <form method="post" id="roleEdit_form" action="<?php echo base_url('Admin/profile_permision_edit');?>" enctype="multipart/form-data">

                        <input type="hidden" class="form-control" id="profile_name" name="profile_name" readonly value="<?=$profile_info->profile_name?>">
                        <input type="hidden" class="form-control" id="profile_id" name="profile_id"  readonly value="<?=$profile_info->profile_id?>">

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
                                    $role_permissions_list = $this->db->query("select * from profile_permissions where user_entity_id ='".$values->user_entity_id."' and profile_id = '".$profile_info->profile_id."'" )->row();
                                    ?>
                                    <div class='col-sm-12 setting_edit_padding'>
                                        <div class="form-inline">
                                            <div class='col-md-4'><input type='hidden'  id='user_entity_id_<?php echo $values->user_entity_id;?>' 
                                                class='entity_module' name='user_entity_id[]' value='<?php echo $values->user_entity_id;?>' checked/><label for='user_entity_id_<?php echo $values->user_entity_id;?>' style='background-color: #ffffff;'><b><?php echo strtoupper($values->user_entity_name);?></b></label>
                                            </div>
                                            <div class='checkbox checkbox-success checkbox-inline col-md-2'><input class="icheckbox_flat-green create_checkall comclass create" id='p_create_<?php echo $values->user_entity_id;?>' type='checkbox' name='p_create_<?php echo $values->user_entity_id;?>' value='1' <?php if($role_permissions_list->p_create == 1){echo "checked";}?>/><label for='p_create_<?php echo $values->user_entity_id;?>'> Create </label>
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

                <!-- User Properties Table -->
                <div class="col-lg-4">
                    <table cellpadding="0" cellspacing="0" class="table customTable">
                        <tr>
                            <th class="text-right" style="width: 5%">S.No.</th>
                            <th class="text-center" style="width: 20%">Icon</th>
                            <th style="width: 50%">Property Name</th>
                            <th class='checkbox checkbox-success checkbox-inline col-md-2'>
                                Accessability
                            </th>
                            <th style="width: 20%" class="text-center">Actions</th>
                        </tr>
                        <?php
                        if(count($properties) > 0){
                            $i=1;
                            foreach($properties as $property){
                                echo '<pre>Next'; print_r($property); echo '</pre>';
                                ?>
                                <tr>
                                    <td class="text-right"><?=$i?>. </td>
                                    <td class="text-center"><?=ucwords($property['property_icon'])?></td>
                                    <td><?=$property['property_name']?></td>
                                    <td class="text-center">
                                        <input type="checkbox" value="1" name="status" <?php if(in_array($property['user_property_id'],$properties_accessibility)) { ?> checked="checked" <?php } ?> >
                                    </td>
                                    <td class="actions text-center">
                                        <i class="fas fa-pencil-alt"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
*/?>

<script src="<?php echo base_url('assets/lib/jquery/dist/jquery.min.js'); ?>"></script>
<script type="text/javascript">

// Read
$("#p_read_all").change(function(){   
    $(".p_read").prop('checked', $(this).prop("checked")); 
});

$("#property_all").change(function(){   
    $(".property_access").prop('checked', $(this).prop("checked")); 
});

$('.p_read').change(function(){ 
    if(false == $(this).prop("checked")){ 
        $("#p_read_all").prop('checked', false); 
    }
    if ($('.p_read:checked').length == $('.checkbox').length ){
        $("#p_read_all").prop('checked', true);
    }
});

// Create
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

// Update
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

// Delete
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

var cookies = document.cookie.split(";");  
for (var i = 0; i < cookies.length; i++) {  
    var cookie = cookies[i];      
    var eqPos = cookie.indexOf("=");    
    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;   
    createCookie(name, "", -1);   
}
</script> 