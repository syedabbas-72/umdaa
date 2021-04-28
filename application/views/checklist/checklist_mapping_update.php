<div class="page-bar">
    <div class="page-title-breadcrumb">
        <div class=" pull-left">
            <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
        </div>
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>

            <li class="active">Consent Form Mapping</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?=base_url('Checklist_department/save_checklist/').$consentformInfo->consent_form_id?>" method="post">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="page-title"><?=$consentformInfo->consent_form_title?><input type="submit" name="submit" value="Save Checklist" class="btn btn-success pull-right"></span>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="<?=($updatedCount->count==0)?'New':'Update'?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-form-label">Before</label>
                                <select class="form-control" name="before[]" multiple="" >
                                    <?php
                                    foreach ($checklist_master as $value) 
                                    {
                                        ?>
                                        <option value="<?=$value->checklist_id?>" <?=(in_array($value->checklist_id, $before_ids))?'selected':''?> ><?=$value->description?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-form-label">During</label>
                                <select class="form-control" name="during[]" multiple="">
                                    <?php
                                    foreach ($checklist_master as $value1) 
                                    {
                                        ?>
                                        <option value="<?=$value1->checklist_id?>" <?=(in_array($value1->checklist_id, $during_ids))?'selected':''?> ><?=$value1->description?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-form-label">After</label>
                                <select class="form-control" name="after[]" multiple="" >
                                    <?php
                                    foreach ($checklist_master as $value2) 
                                    {
                                        ?>
                                        <option value="<?=$value2->checklist_id?>" <?=(in_array($value2->checklist_id, $after_ids))?'selected':''?> ><?=$value2->description?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                        </div>
                    </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <script>
        $(document).ready(function () {
            $('#clinic_doctor_list').dataTable();
            $('select').select2();

            $('.edit').on("click",function(){
                var consent_form_id = $(this).attr("data-id");
                $.post("<?=base_url('Checklist_department/getSelectedChecklist')?>",{consent_form_id:consent_form_id},function(data){
                    console.log(data)
                    $('.checklist_modal_body').html(data);
                }); 
            });

        });
    </script>