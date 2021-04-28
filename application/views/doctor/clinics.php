<style>
.select2-container{
    width: 100% !important;
    /* height: 50px; */
}
.select2-container .select2-selection--single
{
  height:auto !important;
  padding: 5px !important;
}
</style>

<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-left">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>">HOME</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li class="active"><a href="<?=base_url('Doctor/Profile/'.$doctor_id)?>">DOCTOR WORK INFO</a>&nbsp;<i class="fa fa-angle-right"></i></li>
          <li class="active">CLINICS</li>
      </ol>
  </div>
</div>

<!-- Add Clinic Modal -->
<div class="modal fade" id="addModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ADD CLINIC</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-form-label">Clinic Name</label>
                        <div class="row">
                            <div class="col-12 d-inline-flex">
                                <input type="text" class="w-75 form-control" id="search_clinic"/>
                                <input type="hidden" value="<?=$clinicsInfo[0]->doctor_id?>" id="doctor_id">
                                <button class="btn btn-app ml-2" type="button" id="search_clinic_btn">Search Clinic </button>
                            </div>
                        </div>
                        <div class="row loadRow d-none">
                            <div class="col-12 text-center">
                                <h4>Getting Clinics Data <i class="fa fa-spinner fa-spin"></i></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 clinicsBody">

                            </div>
                        </div>
                        
                    </div>
                </div>
        </div>
    </div>
</div>
<!-- Clinic Modal Ends -->


<!-- New Clinic Modal Starts -->
<div class="modal fade" id="newClinicModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Clinic</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('Doctor/NewClinic')?>" method="post">
                    <div class="form-group">
                        <label class="col-form-label">Clinic Name</label>
                        <input type="text" class="form-control" name="clinic_name" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Clinic Location</label>
                        <input type="text" class="form-control" name="location" required >
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Clinic Registration Fee</label>
                        <input type="text" class="form-control" name="clinic_reg_fee" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Walkin Consulting Fee</label>
                        <input type="text" class="form-control" name="walkin_consulting_fee" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Online Consulting Fee</label>
                        <input type="text" class="form-control" name="tele_consulting_fee" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Review Times</label>
                        <input type="text" class="form-control" name="review_times" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Review Days</label>
                        <input type="text" class="form-control" name="review_days" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group d-inline-flex">
                        <div class="checkbox checkbox-icon-black">
                            <input id="checkbox1" name="fo" value="1" type="checkbox" required >
                            <label for="checkbox1">FO</label>
                        </div>
                        <div class="checkbox checkbox-icon-black">
                            <input id="checkbox2" name="nurse" value="1" type="checkbox">
                            <label for="checkbox2">NURSE</label>
                        </div>
                        <div class="checkbox checkbox-icon-black">
                            <input id="checkbox3" name="lab" value="1" type="checkbox">
                            <label for="checkbox3">LAB</label>
                        </div>
                        <div class="checkbox checkbox-icon-black">
                            <input id="checkbox4" name="pharmacy" value="1" type="checkbox">
                            <label for="checkbox4">PHARMACY</label>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <input type="hidden" name="doctor_id" class="new_doctor_id">
                        <button class="btn btn-app" type="submit">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- New Clinic Modal Ends -->

<!-- Modal Starts -->
<div class="modal fade" id="pairModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pair Clinic</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('Doctor/PairClinic')?>" method="post">
                    <div class="form-group">
                        <label class="col-form-label">Walkin Consulting Fee</label>
                        <input type="text" class="form-control" name="walkin_consulting_fee" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Online Consulting Fee</label>
                        <input type="text" class="form-control" name="tele_consulting_fee" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Review Times</label>
                        <input type="text" class="form-control" name="review_times" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Review Days</label>
                        <input type="text" class="form-control" name="review_days" required onkeypress="return numeric()">
                    </div>
                    <div class="form-group text-center">
                        <input type="hidden" name="clinic_id" class="clinic_id">
                        <input type="hidden" name="doctor_id" class="doctor_id">
                        <button class="btn btn-app" type="submit">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ends -->


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h4 class="page-title w-100">CLINICS LIST
                    <button class="btn btn-app pull-right" data-target="#addModal" data-toggle="modal"><i class="fa fa-plus"></i></button>
                </h4>
                <div class="row">
                    <?php
                    // echo count($clinicsInfo);
                    if(count($clinicsInfo) > 0)
                    {
                        foreach($clinicsInfo as $value){
                            if($value->clinic_logo != "")
                            {
                                $img = base_url('uploads/clinic_logos/').$value->clinic_logo;
                            }
                            else
                            {
                                $img = base_url('uploads/departments/dummyDEPT.png');
                            }
                            ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center d-inline-flex">
                                        <div class="prog-avatar">
                                            <img src="<?=$img?>" alt="" width="80" height="80">
                                        </div>
                                        <div class="details w-100">
                                            <div class="title">
                                                <h4 class="mb-0 text-left"><a href="#"><?=$value->clinic_name?></a></h4>
                                                <p class="w-100 m-0"><?=$value->location?></p>
                                                <p class="text-right">
                                                    <a href="<?=base_url('Doctor/RemovePair/'.$value->clinic_doctor_id.'/'.$value->doctor_id)?>" onclick="return confirm('Are you sure to remove this clinic?')" class="btn btn-xs btn-outline-danger" >remove</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                        <div class="col-12">
                            <h5 class="text-center">No Clinics Found</h5>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    $('.select').select2();
    // $('#newClinicModal').modal();
    $('#search_clinic_btn').on("click", function(){
        var input = $('#search_clinic').val();
        var doctor_id = $('#doctor_id').val();
        $('.loadRow').removeClass('d-none');
        $.post("<?=base_url('Doctor/SearchClinic')?>",{search:input,doctor_id:doctor_id}, function(data){
            $('.clinicsBody').html(data);
            $('.loadRow').addClass('d-none');
        });
    })
})
</script>
<script>
$(document).on("click",".pair",function(){
    var values = $(this).attr('data-values')
    values = values.split("*")
    $('.clinic_id').val(values[0])
    $('.doctor_id').val(values[1])
});
$(document).on("click",".newClinic",function(){
    var doctor_id = $(this).attr('data-id')
    $('.new_doctor_id').val(doctor_id)
});
</script>