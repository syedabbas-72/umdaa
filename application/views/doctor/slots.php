<style>
.remove-icon
{
    margin-top:-5px !important;
    margin-left:-10px !important;
    font-size:18px !important;
    cursor:pointer;
}
input[type="time"].form-control {
    padding: 6px !important;
    /* display: none; */
    /* background: transparent; */
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
          <li class="active">DOCTOR SLOTS</li>
      </ol>
  </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
            <div class="row col-12">
                    <div class="col-12 p-0">
                    <ul class="nav nav-pills ml-0" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="walkin-tab" data-toggle="tab" href="#walkin" role="tab" aria-controls="walkin" aria-selected="true">Walkin Timings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tele-tab" data-toggle="tab" href="#tele" role="tab" aria-controls="tele" aria-selected="false">Tele Consultation Timings</a>
                        </li>
                    </ul>
                    <div class="tab-content px-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="walkin" role="tabpanel" aria-labelledby="walkin-tab">
                            <div class="row mt-3 bg-dark ">
                                <div class="col-2 bg-dark text-white">
                                   <h5>DAY</h5>
                                </div>
                                <div class="col-10  bg-dark text-white">
                                    <div class="row">
                                    <div class="col-4">
                                    <h5>MORNING</h5>
                                    </div>
                                    <div class="col-4">
                                    <h5>AFTERNOON</h5>
                                    </div>
                                    <div class="col-4">
                                    <h5>EVENING</h5>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <form action="<?=base_url('Doctor/addSlots')?>" method="post">
                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" name="clinic_doctor_id" value="<?=$clinic_doctor_id?>">
                                    <input type="hidden" name="doctor_id" value="<?=$doctor_id?>">
                                    <input type="hidden" name="slot_type" value="walkin">
                                    <?php
                                    $daynames = ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                    for($i=1;$i<=7;$i++)
                                    {
                                        $mrSlots = getDoctorSlots('walkin', 'morning', $clinic_doctor_id,$i); //Parameters - Slot type,Morning,Clinic Doctor ID, Day Number
                                        $afSlots = getDoctorSlots('walkin', 'afternoon', $clinic_doctor_id,$i);
                                        $evSlots = getDoctorSlots('walkin', 'evening', $clinic_doctor_id,$i);
                                        ?>
                                        <div class="row py-3 border-bottom">
                                            <div class="col-2 mt-2 p-0">
                                                <span class="trade_name"><?=$daynames[$i]?></span>
                                            </div>
                                            <div class="col-10 p-0">
                                                <div class="row">
                                                    <div class="col-4 d-inline-flex">
                                                    <input type="time" class="form-control w-50" name="mr_start[]" value="<?=($mrSlots->from_time!="")?$mrSlots->from_time:''?>" id="<?=$daynames[$i]?>_mr_walkin_from">
                                                    <input type="time" class="form-control w-50" name="mr_end[]" value="<?=($mrSlots->to_time!="")?$mrSlots->to_time:''?>" id="<?=$daynames[$i]?>_mr_walkin_to">
                                                    <i class="fas fa-minus-circle text-danger remove-icon <?=($mrSlots == "")?'d-none':''?>" onclick="removeSlots(this.id)" id="<?=$daynames[$i]?>_mr_walkin" data-value="<?=$mrSlots->clinic_doctor_weekday_slot_id."*".$daynames[$i]."*Morning*Walk-in"?>"></i>
                                                </div>
                                                <div class="col-4 d-inline-flex">
                                                    <input type="time" class="form-control w-50" name="af_start[]" value="<?=($afSlots->from_time!="")?$afSlots->from_time:''?>" id="<?=$daynames[$i]?>_af_walkin_from">
                                                    <input type="time" class="form-control w-50" name="af_end[]" value="<?=($afSlots->to_time!="")?$afSlots->to_time:''?>" id="<?=$daynames[$i]?>_af_walkin_to">
                                                    <i class="fas fa-minus-circle text-danger remove-icon <?=($afSlots == "")?'d-none':''?>" onclick="removeSlots(this.id)" id="<?=$daynames[$i]?>_af_walkin" data-value="<?=$afSlots->clinic_doctor_weekday_slot_id."*".$daynames[$i]."*Afternoon*Walk-in"?>"></i>
                                                </div>
                                                <div class="col-4 d-inline-flex">
                                                    <input type="time" class="form-control w-50" name="ev_start[]" value="<?=($evSlots->from_time!="")?$evSlots->from_time:''?>" id="<?=$daynames[$i]?>_ev_walkin_from">
                                                    <input type="time" class="form-control w-50" name="ev_end[]" value="<?=($evSlots->to_time!="")?$evSlots->to_time:''?>" id="<?=$daynames[$i]?>_ev_walkin_to">
                                                    <i class="fas fa-minus-circle text-danger remove-icon <?=($evSlots == "")?'d-none':''?>" onclick="removeSlots(this.id)" id="<?=$daynames[$i]?>_ev_walkin" data-value="<?=$evSlots->clinic_doctor_weekday_slot_id."*".$daynames[$i]."*Evening*Walk-in"?>"></i>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-12 text-center my-4">
                                    <button class="btn btn-app" >Submit</button>
                                </div>
                            </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="tele" role="tabpanel" aria-labelledby="tele-tab">
                        <div class="row col-12 mt-3 bg-dark ">
                                <div class="col-2 bg-dark text-white">
                                   <h5>DAY</h5>
                                </div>
                                <div class="col-10  bg-dark text-white">
                                    <div class="row">
                                    <div class="col-4">
                                    <h5>MORNING</h5>
                                    </div>
                                    <div class="col-4">
                                    <h5>AFTERNOON</h5>
                                    </div>
                                    <div class="col-4">
                                    <h5>EVENING</h5>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <form action="<?=base_url('Doctor/addSlots')?>" method="post">
                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" name="clinic_doctor_id" value="<?=$clinic_doctor_id?>">
                                    <input type="hidden" name="doctor_id" value="<?=$doctor_id?>">
                                    <input type="hidden" name="slot_type" value="video call">
                                    <?php
                                    $daynames = ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                    for($i=1;$i<=7;$i++)
                                    {
                                        $mrSlots = getDoctorSlots('video call', 'morning', $clinic_doctor_id,$i); //Parameters - Slot type,Morning,Clinic Doctor ID, Day Number
                                        $afSlots = getDoctorSlots('video call', 'afternoon', $clinic_doctor_id,$i);
                                        $evSlots = getDoctorSlots('video call', 'evening', $clinic_doctor_id,$i);
                                        ?>
                                        <div class="row py-3 border-bottom">
                                            <div class="col-2 mt-2 p-0">
                                                <span class="trade_name"><?=$daynames[$i]?></span>
                                            </div>
                                            <div class="col-10 p-0">
                                                <div class="row">
                                                    <div class="col-4 d-inline-flex">
                                                    <input type="time" class="form-control w-50" name="mr_start[]" value="<?=($mrSlots->from_time!="")?$mrSlots->from_time:''?>" id="<?=$daynames[$i]?>_mr_tele_from">
                                                    <input type="time" class="form-control w-50" name="mr_end[]" value="<?=($mrSlots->to_time!="")?$mrSlots->to_time:''?>" id="<?=$daynames[$i]?>_mr_tele_to">
                                                    <i class="fas fa-minus-circle text-danger remove-icon <?=($mrSlots == "")?'d-none':''?>" onclick="removeSlots(this.id)" id="<?=$daynames[$i]?>_mr_tele" data-value="<?=$mrSlots->clinic_doctor_weekday_slot_id."*".$daynames[$i]."*Morning*Tele Consultation"?>"></i>
                                                </div>
                                                <div class="col-4 d-inline-flex">
                                                    <input type="time" class="form-control w-50" name="af_start[]" value="<?=($afSlots->from_time!="")?$afSlots->from_time:''?>" id="<?=$daynames[$i]?>_af_tele_from">
                                                    <input type="time" class="form-control w-50" name="af_end[]" value="<?=($afSlots->to_time!="")?$afSlots->to_time:''?>" id="<?=$daynames[$i]?>_af_tele_to">
                                                    <i class="fas fa-minus-circle text-danger remove-icon <?=($afSlots == "")?'d-none':''?>" onclick="removeSlots(this.id)" id="<?=$daynames[$i]?>_af_tele" data-value="<?=$afSlots->clinic_doctor_weekday_slot_id."*".$daynames[$i]."*Afternoon*Tele Consultation"?>"></i>
                                                </div>
                                                <div class="col-4 d-inline-flex">
                                                    <input type="time" class="form-control w-50" name="ev_start[]" value="<?=($evSlots->from_time!="")?$evSlots->from_time:''?>" id="<?=$daynames[$i]?>_ev_tele_from">
                                                    <input type="time" class="form-control w-50" name="ev_end[]" value="<?($evSlots->to_time!="")?$evSlots->to_time:''?>" id="<?=$daynames[$i]?>_ev_tele_to">
                                                    <i class="fas fa-minus-circle text-danger remove-icon <?=($evSlots == "")?'d-none':''?>" onclick="removeSlots(this.id)" id="<?=$daynames[$i]?>_ev_tele" data-value="<?=$evSlots->clinic_doctor_weekday_slot_id."*".$daynames[$i]."*Evening*Tele Consultation"?>"></i>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-12 text-center my-4">
                                    <button class="btn btn-app">Submit</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// $(function(){
    // alert('welcome')
    function removeSlots(id){
        // alert(id)
        var data = $('#'+id).attr('data-value');
        var str = data.split("*");
        var a = confirm('Are you sure to Delete '+str[1]+" "+str[2]+" "+str[3]+" Slot Timings ?");
        if(a == true)
        {
            $.post("<?=base_url('Settings/removeSlot')?>", {cdws_id:str[0]}, function(data){
                console.log(data)
                if($.trim(data) == "1")
                {
                    // alert("came in")
                    $('#'+id).addClass('d-none');
                    $("#"+id+"_from").val('');
                    $("#"+id+"_to").val('');
                }
            });
        }
    }
// })
</script>