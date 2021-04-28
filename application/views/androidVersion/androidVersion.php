<style type="text/css">
td {
    white-space: unset !important;
}
</style>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item"
                    href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a></li>
            <li class="active">Android Version</li>
        </ol>
    </div>
</div>



<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row col-md-12 subHeader">
                        <span class="font-weight-bold text-uppercase">Version Update</span>
                    </div>
                    <div class="row col-md-12 margin-top-10">
                        <div class="col-md-4">
                            <label class="font-weight-bold">Current Version  <span class="code currentVersion"><?=$versionInfo->app_version_name?></span></label>
                        </div>
                    </div>
                    <div class="row col-md-12 margin-top-10">
                        <div class="col-md-4">
                            <label class="col-form-label font-weight-bold">Version</label>
                            <input type="text" class="form-control" id="version" onkeypress="return decimal()">
                            <input type="hidden" class="form-control" id="versionID" onkeypress="return decimal()" value="<?=($versionInfo->app_version_id)+1?>">
                            <input type="hidden" class="form-control" id="app_id" value="<?=$versionInfo->app_id?>">
                        </div>
                        <div class="col-md-2">
                            <p class="m-0">&emsp;</p>
                            <button class="btn btn-xs btn-app mt-1" id="versionChange"><i class="fa fa-check"></i></button>
                        </div>
                        <div class="col-md-4">
                            <div class="alert mt-2 alert-success font-weight-bold" role="alert"></div>
                            <div class="alert mt-2 alert-error font-weight-bold" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('.alert').hide();
    $('#versionChange').on("click",function(){
        var value = $('#version').val();
        var versionID = $('#versionID').val();
        var app_id = $('#app_id').val();
        if(value == "")
            alert("Please Fill The Version.");
        else
        {
            $.post("<?=base_url('AndroidVersion/changeVersion')?>",{version:value,versionID:versionID,app_id:app_id},function(data){
                if(data == 1)
                {
                    $('.alert-success').toggle('2000');
                    $('.alert-error').hide();
                    $('.alert-success').html("Successfully Changed");
                    $('.currentVersion').html(value);
                }
                else
                {
                    $('.alert-success').hide();
                    $('.alert-error').toggle('2000');
                    $('.alert-error').html("Error Occurred");
                }
            });
        }
    });
});
</script>