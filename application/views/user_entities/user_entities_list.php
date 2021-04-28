
<?php
// get CRUD info
$crudInfo = getcrudInfo('User_Entities/');
?>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA CLINICS</a>&nbsp;<i class="fa fa-angle-right"></i></li>

            <li class="active">User Entity Master</li>
        </ol>

        <div class="pull-right tPadding bcBtn">
            <a href="<?= base_url('User_Entities/user_entities_add'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> Add User Entity</a>
            <a href="<?= base_url('User_Entities/user_property_add'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> Add User Property</a>
        </div>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <?php
                    $i = 0;
                    foreach($categories as $category){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if($i == 0){ echo ' active'; } ?>" id="pills<?=ucwords($category['category'])?>_tab" data-toggle="pill" href="#pills<?=ucwords($category['category'])?>" role="tab" aria-controls="pills-home" aria-selected="true"><?=ucwords($category['category'])?></a>
                        </li>    
                        <?php
                        $i++;
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" id="pillsProperties_tab" data-toggle="pill" href="#pillsProperties" role="tab" aria-controls="pills-home" aria-selected="true">Properties</a>
                    </li>
                </ul>
                <div class="tab-content pt-2 pl-1" id="pills-tabContent">
                    <?php $active = 0; ?>
                    <?php foreach($entities as $key => $value){ ?>
                        <div class="tab-pane fade show <?php echo ($active == 0 )? 'active' : ''; ?>" id="pills<?=ucwords($key)?>" role="tabpanel" aria-labelledby="pills<?=ucwords($key)?>_tab">
                            <table cellspacing="0" cellpadding="0" class="table customTable">
                                <tr>
                                    <th style="width: 6%">S.No</th>
                                    <th style="width: 25%">Entity / Device Name</th>
                                    <th style="width: 9%" class="text-center">Icon</th>
                                    <th style="width: 22%">Method &amp; URL</th>
                                    <th style="width: 9%" class="text-center">Position</th>
                                    <th style="width: 15%" class="text-center">Device Module</th>
                                    <th style="width: 10%" class="text-right">Action</th>
                                </tr>
                                <?php
                                $i = 1;
                                foreach($value as $row){
                                    ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><span><?=$row['user_entity_name']?></span><br><small><i><?=$row['user_entity_alias']?></i></small></td>
                                        <td class="text-center">
                                        
                                        <?php
                                        if($key == "Ionic"){
                                            ?>
                                            <span class="bg-primary p-2 rounded-circle text-white" style="width: 50px !important;height:50px !important;color:white !important"><?=substr($row['user_entity_alias'],0,1)?></span>
                                            <?php
                                        }
                                        elseif($key == "Angular"){
                                            ?>
                                            <!-- <span style="font-size: 22px !important;"><?=$row['entity_icon']?></span> -->
                                            <span style="font-size: 22px !important;">-</span>
                                            <?php
                                        }
                                        else{
                                            ?>
                                                <span style="font-size: 22px !important;"><?=$row['entity_icon']?></span>
                                            <?php
                                        }
                                        ?>
                                        </td>
                                        <td><span><?=$row['method_name']?></span><br><small><?=$row['entity_url']?></small></td>
                                        <td class="text-center"><?=$row['position']?></td>
                                        <td class="text-center">
                                            <?php if($row['is_mobile_module'] == 1) { echo '<span style="padding-right:10px; border-right:1px dotted #cccccc; margin-right:10px; font-size: 22px !important;"><i class="fas fa-mobile-alt"></i></span>'; }?><span style="font-size: 22px !important;"><i class="fas fa-desktop"></i></span>
                                        </td>
                                        <td class="actions text-center">
                                            <!-- Edit -->
                                            <?php // if($crudInfo->p_update){ ?>
                                                <a href="<?php echo base_url('User_Entities/user_entities_update/'.$row['user_entity_id']);?>"><i class="fas fa-pencil-alt editSmall" title="Edit Entity Information"></i></a>
                                            <?php // } ?>

                                            <!-- Delete -->
                                            <?php // if($crudInfo->p_delete){ ?>
                                                <a href="<?php echo base_url('User_Entities/user_entities_delete/'.$row['user_entity_id']);?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall" title="Edit"></i></a>
                                            <?php // } ?>
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
                                            ?>
                                            <tr>
                                                <td class="child_no"><?=$j?>. </td>
                                                <td class="<?php if($j == $count){ echo 'child_end'; }else{ echo 'child';} ?>"><span><?=$child['user_entity_name']?></span><br><small><i><?=$child['user_entity_alias']?></i></small></td>
                                                <td class="text-center"><span style="font-size: 22px !important;"><?=$child['entity_icon']?></span></td>
                                                <td><span><?=$child['method_name']?></span><br><small><?=$child['entity_url']?></small></td>
                                                <td class="text-center"><?=$child['position']?></td>
                                                <td class="text-center">
                                                    <?php if($child['is_mobile_module'] == 1) { echo '<span style="padding-right:10px; border-right:1px dotted #cccccc; margin-right:10px; font-size: 22px !important;"><i class="fas fa-mobile-alt"></i></span>'; }?><span style="font-size: 22px !important;"><i class="fas fa-desktop"></i></span>
                                                </td>
                                                <td class="actions text-center">
                                                
                                                    <!-- Edit -->
                                                    <?php // if($crudInfo->p_update){ ?>
                                                        <a href="<?php echo base_url('User_Entities/user_entities_update/'.$child['user_entity_id']);?>"><i class="fas fa-pencil-alt editSmall" title="Edit Entity Information"></i></a>
                                                    <?php // } ?>

                                                    <!-- Delete -->
                                                    <?php // if($crudInfo->p_delete){ ?>
                                                        <a href="<?php echo base_url('User_Entities/user_entities_delete/'.$child['user_entity_id']);?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall" title="Edit"></i></a>
                                                    <?php // } ?>
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
                                                    ?>
                                                    <tr>
                                                        <td class="sub_child_no"><?=$z?>. </td>
                                                        <td class="<?php if($z == $subCount){ echo 'sub_child_end'; }else{ echo 'sub_child';} ?>"><span><?=$subChild['user_entity_name']?></span><br><small><i><?=$subChild['user_entity_alias']?></i></small></td>
                                                        <td class="text-center"><span style="font-size: 22px !important;"><?=$subChild['entity_icon']?></span></td>
                                                        <td><span><?=$subChild['method_name']?></span><br><small><?=$subChild['entity_url']?></small></td>
                                                        <td class="text-center"><?=$subChild['position']?></td>
                                                        <td class="text-center">
                                                            <?php if($subChild['is_mobile_module'] == 1) { echo '<span style="padding-right:10px; border-right:1px dotted #cccccc; margin-right:10px; font-size: 22px !important;"><i class="fas fa-mobile-alt"></i></span>'; }?><span style="font-size: 22px !important;"><i class="fas fa-desktop"></i></span>
                                                        </td>
                                                        <td class="actions text-center">
                                                            <!-- Edit -->
                                                            <?php // if($crudInfo->p_update){ ?>
                                                                <a href="<?php echo base_url('User_Entities/user_entities_update/'.$subChild['user_entity_id']);?>"><i class="fas fa-pencil-alt editSmall" title="Edit Entity Information"></i></a>
                                                            <?php // } ?>

                                                            <!-- Delete -->
                                                            <?php // if($crudInfo->p_delete){ ?>
                                                                <a href="<?php echo base_url('User_Entities/user_entities_delete/'.$subChild['user_entity_id']);?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall" title="Edit"></i></a>
                                                            <?php // } ?>
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
                            </table>
                        </div>
                        <?php $active++; ?>
                    <?php } ?>

                    <div class="tab-pane fade show" id="pillsProperties" role="tabpanel" aria-labelledby="pillsProperties_tab">
                        <table cellspacing="0" cellpadding="0" class="table customTable">
                            <tr>
                                <th style="width: 5%">S.No</th>
                                <th style="width: 70%">Property Name</th>
                                <th style="width: 15%" class="text-center">Icon</th>
                                <th style="width: 10%" class="text-right">Action</th>
                            </tr>
                            <?php
                            if(count($properties) > 0){
                                $i=1;
                                foreach($properties as $property){
                                    ?>
                                    <tr>
                                        <td class="text-right"><?=$i?>. </td>
                                        <td><?=ucwords($property['property_name'])?></td>
                                        <td><?=$property['propert_icon']?></td>
                                        <td class="actions">
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
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function () {
        $('.Entity_table').dataTable();
    });

    function doconfirm()
    {
        if(confirm("Are you sure you want to delete the entity?")){
            return true;
        }else{
            return false;  
        } 
    }
</script>