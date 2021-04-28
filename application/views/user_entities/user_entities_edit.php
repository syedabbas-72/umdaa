<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA CLINICS</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?= base_url('User_Entities/'); ?>">User Entity</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Edit &amp; Save Changes</li>
        </ol>

        <div class="pull-right tPadding">
            <a href="<?= base_url('User_Entities/'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-backward"></i> Back</a>
        </div>
    </div>
</div>

<!-- content start -->
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <!-- card start -->
            <div class="card">
                
                <div class="row page-title">
                    <div class="col-md-12">
                        Update User Entity
                    </div>
                </div>

                <div class="card-body">
                    <div class="tabs">

                        <div class="tab-content">
                            <div role="tabpanel" >
                                <form method="post" action="<?php echo base_url('User_Entities/user_entities_update/'.$EntityData->user_entity_id);?>" autocomplete="off" class="form">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Entity Name *</label>
                                                <input type="text" name="user_entity_name" class="form-control" value="<?=$EntityData->user_entity_name?>" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Entity Alias Name</label>
                                                <input type="text" name="user_entity_alias" class="form-control" value="<?=$EntityData->user_entity_alias?>" >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Category</label>
                                                <select class="form-control" name="category">
                                                    <option selected="" disabled="">Select Category</option>
                                                    <option value="Administration" <?=($EntityData->category=="Administration")?'selected':''?> >Administration</option>
                                                    <option value="Main" <?=($EntityData->category=="Main")?'selected':''?> >Main</option>
                                                    <option value="Masters" <?=($EntityData->category=="Masters")?'selected':''?> >Masters</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Method *</label>
                                                <input type="text" name="method_name" class="form-control" value="<?=$EntityData->method_name?>" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Position</label>
                                                <input type="text" name="position" min="0" minlength="1" value="<?=$EntityData->position?>" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Parent Entities</label>
                                                <select class="form-control" name="parent_id">
                                                    <option selected="" value="0">Select Category</option>
                                                    <?php
                                                    foreach ($parentEntities as $value) {
                                                        ?>
                                                        <option value="<?=$value->user_entity_id?>" <?=($EntityData->parent_id==$value->user_entity_id)?'selected':''?> ><?=$value->user_entity_name?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Level</label>
                                                <select class="form-control" name="level">
                                                    <option selected="" disabled="">Select Level</option>
                                                    <option value="0" <?=($EntityData->level=="0")?'selected':''?>>0</option>
                                                    <option value="1" <?=($EntityData->level=="1")?'selected':''?>>1</option>
                                                    <option value="2" <?=($EntityData->level=="2")?'selected':''?>>2</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Level Alias</label>
                                                <select class="form-control" name="level_alias">
                                                    <option selected="" disabled="">Select Level Alias</option>
                                                    <option value="nav" <?=($EntityData->level_alias=="nav")?'selected':''?>>Nav</option>
                                                    <option value="page" <?=($EntityData->level_alias=="page")?'selected':''?>>Page</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Is Mobile Module ?</label>
                                                <div class="row" id="check_ptype">
                                                    <div class="radio radio-success">
                                                        <input type="radio" class="radio-ma" id="radio14" name='is_mobile_module' value="1" <?=($EntityData->is_mobile_module=="1")?'checked':''?> required="required">
                                                        <label for="radio14" > Yes </label>
                                                    </div>
                                                    <div class="radio radio-success">
                                                        <input type="radio" class="radio-ma"  id="radio15" name='is_mobile_module' value="0" <?=($EntityData->is_mobile_module=="0")?'checked':''?>>
                                                        <label for="radio15" > No </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Entity URL *</label>
                                                <input type="text" name="entity_url" class="form-control" value="<?=$EntityData->entity_url?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-form-label">Entity Icon *</label>
                                                <input type='text' name='entity_icon' class="form-control" value='<?=$EntityData->entity_icon?>' placeholder="Ex: <i class='fa fa-dashboard'></i>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-md-12">
                                            <div class="text-center">
                                                <input type="submit" value="Save" name="submit" class="btn btn-success"> 
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>   <!--Tab end -->
                    </div>
                </div>
            </div>
        </div>
    </section>
