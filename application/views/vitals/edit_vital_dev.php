<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Edit Vitals</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
                <div class="pull-left page-title">
                    <?php count($appointmentInfo) > 0 ? $this->load->view('profile/appointment_info_header', $patient_dt->patient_id) : ''; ?>
                </div>
                <div class="row col-md-12"> 
                    <div class="col-md-3" id="view_casesheet">
                        <div class="col-md-12">
                            <?php $this->load->view('profile/patient_info_left_nav'); ?>
                        </div>
                    </div>
                    <div class="col-md-9" id="view_caseresults" class="view_caseresults">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Edit Vitals                                
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding: 10px">
                            <div class="col-md-12">
                                <form action="<?php echo base_url('Vitals/edit_save/'.$patient_id.'/'.$appointment_id) ?>" method="POST" onsubmit="return validate();" class="form customForm">
                                    <input type = "hidden" name = "patient_id" value = "<?=$patient_id;?>"/>
                                    <input type = "hidden" name = "appointment_id" value = "<?=$appointment_id;?>"/>
                                    <input type = "hidden" name = "vital_sign_date_time" value = "<?php echo $vital_r_time[0];?>"/>

                                    <div class="row col-md-12">
                                        <div class="col-md-9">
                                            <table id="vital" class="table vitals_info">
                                                <tr>
                                                    <td><label class="col-form-label">Pulse Rate</label></td>
                                                    <td>:</td>
                                                    <td colspan="3">
                                                        <input type="number" name="vitals[PR]" id="PR" class ="check form-control pr" onkeyup="checkVitalsMax('pr')" value = "<?php
                                                        if(array_key_exists('PR',$vital_key_sign))
                                                        {
                                                            echo $vital_key_sign['PR'];
                                                        }
                                                        ?>" />
                                                    </td>
                                                    <td><label>Per Min</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">BP</label></td>
                                                    <td>:</td>
                                                    <td><input type="number" name="vitals[SBP]"   min="0" step=".01" id="SBP" class="check form-control sbp" onkeyup="checkVitalsMax('sbp')" placeholder="Systolic" onkeyup="return bp();" value="<?php
                                                    if(array_key_exists('SBP',$vital_key_sign))
                                                    {
                                                        echo $vital_key_sign['SBP'];
                                                    }
                                                    ?>"/></td>
                                                    <td> / </td>
                                                    <td><input type="number" name="vitals[DBP]"   min="0" step=".01" id="DBP" class="check form-control dbp" onkeyup="checkVitalsMax('dbp')" placeholder="Diastolic" onkeyup="return bp();" value="<?php
                                                    if(array_key_exists('DBP',$vital_key_sign))
                                                    {
                                                        echo $vital_key_sign['DBP'];
                                                    }
                                                    ?>" />
                                                    <input type = "hidden" id = "BP" style="border-top:0px;border-left:0px;border-right:0px;"/>
                                                </td>
                                                <td class="text-left"><label>mm/HG</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">Respiratory Rate</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number"   min="0" step=".01" name = "vitals[RR]" id = "RR" class = "check form-control rr" onkeyup="checkVitalsMax('rr')" value="<?php
                                                if(array_key_exists('RR',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['RR'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>Per Min</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">Temperature</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number"   min="0" step=".01" name = "vitals[Temp]" id = "Temp" class = "check form-control temp" onkeyup="checkVitalsMax('temp')" value="<?php
                                                if(array_key_exists('Temp',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['Temp'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>°F</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">SaO2</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number"   min="0" step=".01"  name = "vitals[SaO2]" id = "SaO2" class = "check form-control sao" onkeyup="checkVitalsMax('sao')" value="<?php
                                                if(array_key_exists('SaO2',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['SaO2'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>%</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">Height</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number"  min="0" step=".01"  name = "vitals[Height]" id = "Height" class = "check form-control height" onkeyup="checkVitalsMax('height')" value="<?php
                                                if(array_key_exists('Height',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['Height'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>CM</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">Weight</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number"   min="0" step=".01" name = "vitals[Weight]" class = "check form-control weight" onkeyup="checkVitalsMax('weight')" id = "Weight" value="<?php
                                                if(array_key_exists('Weight',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['Weight'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>KG</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">BMI</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number" min="0" step=".01" name = "vitals[BMI]" readonly id = "BMI" class = "check form-control" onclick = "myFunction(this.id)" value="<?php
                                                if(array_key_exists('BMI',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['BMI'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>kg/m2</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">BSA</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number" min="0" step=".01" name = "vitals[BSA]" readonly id = "bsa" class = "check form-control" onclick="myFunction(this.id)" value="<?php
                                                if(array_key_exists('BSA',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['BSA'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label>kg/m2</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="col-form-label">WH Ratio</label></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "text" onkeypress="return numeric()" minlength="" maxlength="5"  min="0" step=".01" name = "vitals[WH_ratio]" id = "vhr" class = "check form-control" value="<?php
                                                if(array_key_exists('WH_ratio',$vital_key_sign))
                                                {
                                                    echo $vital_key_sign['WH_ratio'];
                                                }
                                                ?>"/></td>
                                                <td class="text-left"><label></label></td>
                                            </tr>
                                            <?php
                                            if(count($vital_key_sign_other)>0) {
                                                foreach($vital_key_sign_other as $key=>$vkso) {
                                                    ?>
                                                    <tr>
                                                        <td><input type = "text"  min="0" step=".01" class = "form-control" name = "vital_sign[]" value="<?php echo $key; ?>"></td>
                                                        <td>:</td>
                                                        <td colspan="3"><input type = "number"   min="0" step=".01" class = "form-control" name = "vital_sign_val[]" value="<?php echo $vkso; ?>"/></td>
                                                    </tr>
                                                    <?php 
                                                } 
                                            }
                                            ?>
                                            <tr>
                                                <td><input type = "text" min="0" step=".01" class = "form-control" name = "vital_sign[]"></td>
                                                <td>:</td>
                                                <td colspan="3"><input type = "number"   min="0" step=".01" class = "form-control" name = "vital_sign_val[]" /></td>
                                                <td style="text-align: center;"><button type="button" id="test1" onclick="add_vital()" class="btn btn-success">+</button></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-3">&nbsp;</div>

                                    <!-- Drug Allergy -->
                                    <?php
                                    // Get Patient's Drug Allergy
                                    $drug_allergy = $patient_info->allergy;
                                    $no = "";
                                    // If drug allergy is not mentioned or mentioned NO then
                                    if($drug_allergy == 'no' || $drug_allergy == '' || $drug_allergy == NULL){
                                        // Display drug allergy radio buttons Yes / No
                                        $drugLabelTitle = "Are you allergic to any drug?";
                                        if($drug_allergy == "no"){
                                            $no = "checked";
                                        }else{
                                            $no = "";
                                        }
                                    }else{
                                        $drugLabelTitle = "Any more drugs you are allergic to?";
                                        ?>  
                                        <div class="col-md-12" style="padding-top:20px;">
                                            <strong>Drug Allergy:</strong><br>
                                            <p><?=ucwords($patient_info->allergy); ?></p>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php /*
                                    <div class="col-md-12" style="padding-top:20px; padding-bottom:10px;">
                                        <strong><?=$drugLabelTitle; ?></strong><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="allergyCheck" id="allergyCheckYes_rb" value="Yes" required="required">
                                            <label class="form-check-label" for="allergyCheckYes_rb">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="allergyCheck" id="allergyCheckNo_rb" value="No" <?=$no; ?> required="required">
                                            <label class="form-check-label" for="allergyCheckNo_rb">No</label>
                                        </div>
                                    </div>
                                    */ ?>

                                    <div class="col-md-12" id="drugAllergyTB_div">
                                        <strong>Edit/Specify the drug names you are allergic to (Eg: Dolo 650, Caterpil, etc.)</strong><br>
                                        <textarea class="form-control" style="width:600px; border-radius:4px;" rows="2" name="allergy" id="allergy_ta"><?=ucwords($patient_info->allergy); ?></textarea>
                                    </div> 
                                    
                                    <input type = "submit" name="save_vitals" class = "btn btn-success" value = "Submit" style="margin-top:20px;margin-right:10px;"/>
                                    <input type = "submit" name="save_vitals" class = "btn btn-success" value = "Submit & Print" style="margin-top:20px;margin-right:0px;"/>
                                    <a href="<?=base_url('Vitals/index/'.$patient_id.'/'.$appointment_id)?>" class="btn btn-danger"  style="margin-top:20px;margin-left:10px;">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script type="text/javascript">
    function add_vital(){
        var id = 'vital';

        var count=$("#"+id).find('tr').length;  
        $("#"+id).append('<tr id="'+id+'_'+count+'"><td><input type="text" class="form-control"min="0" step=".01" name = "vital_sign[]"></td><td>:</td><td colspan="3"><input type="text" class = "form-control" name="vital_sign_val[]"></td><td style="text-align: center;"><button type="button" class="btn btn-danger" onclick=del_vital("'+id+'_'+count+'");>-</button></td></tr>');
    //$("#"+id).append('<tr id="'+id+'_'+count+'"><td class="vital_row"><input type="text" name="vital_sign[]" style="width:200px;float:left;border-top:0px;border-left:0px;border-right:0px;" class="inline">&nbsp;&nbsp;<span class="inline" style=" display:inline-block;">:</span></td><td><input type="text" name="vital_sign_val[]" class="vital_txt" style="width:200px;border-top:0px;border-left:0px;border-right:0px;"></td><td style="text-align: center;"><button type="button" class="btn btn-success" onclick=add_vital("'+id+'");>+</button><button type="button" class="btn btn-danger" onclick=del_vital("'+id+'_'+count+'");>-</button></td></tr>');
}

function del_vital(id){
    $("#"+id).remove();
}

// $(document).on('keyup','#Weight',function(){
//     var height = $('#Height').val()/100;
//     var weight = $('#Weight').val();

//     var bmi = Math.round(weight/(height*height));
//     var bsa = Math.sqrt((height/weight)*3600).toFixed(2);

//     if($('#Height').val() != '' && $('Weight').val() != ''){
//         $('#BMI').val(bmi);
//         $('#bsa').val(bsa);
//     }else{
//         $('#BMI').val('');
//         $('#bsa').val('');
//     }
// });

$(document).on('keyup','#Weight',function(){
    var height = $('#Height').val()/100
    var cm_height = $('#Height').val()
    var weight = $('#Weight').val();

    var bmi = Math.round(weight/(height*height));
    var bsa = Math.sqrt((cm_height*weight)/3600).toFixed(2);

    if($('#Height').val() != '' && $('Weight').val() != ''){
        $('#BMI').val(bmi);
        $('#bsa').val(bsa);
    }else{
        $('#BMI').val('');
        $('#bsa').val('');
    }
});


$(document).on('keyup','.check',function(){

    var value1 = $(this).val();
    var id = $(this).attr('id');
    var url = "<?php echo  base_url('CaseSheet/change');?>";


    if(value1.length >= 2)
    {
        $.ajax({
            url: url,
            type : 'POST',
            data:{
                value : value1,
                id: id
            },
            success:function(data_1){
                data_1 = $.trim(data_1);
                if(data_1 == "normal" )
                {

                    $('#'+id).css({'color':'black'});

                }else{

                    $('#'+id).css({'color':'red'});
                }
                var sbp = $("#SBP").val();
                var dbp = $("#DBP").val();
                var bp = sbp+'/'+dbp;
                $("#BP").val(bp);
            }
        });

    }
});


// Drug Allergy Radio button Secltion
$('input[type="radio"]').change(function() {
    if(this.value == 'Yes'){
        $("#drugAllergyTB_div").show();
        if($("#allergy_ta").val() == 'No'){
            $("#allergy_ta").val("");    
        }        
    }else if(this.value == 'No'){
        $("#drugAllergyTB_div").hide();
        if($("#allergy_ta").val() == ""){
            $("#allergy_ta").val() = "No";
        }        
    }
});


$(document).on('click','#back',function(){
    window.history.back();
});

$(document).on('click','#check_allergy',function(){
    if($(this).is(':checked')){
        $("#input-allergy").show();
    }else{
        $("#input-allergy").hide();
    }
});


$(document).on("click",".radio-ip",function(){
    if( $(this).is(":checked") ){
        $('.radio-ip').not(this).attr('checked',false);
        var val = $(this).val();
        if(val == "yes"){
            $("#input-allergy").show();
        }
        else{
            $("#input-allergy").val("No");
            $(".input-ma").val("No");
            $("#input-allergy").hide();

        }
    }
});


// $(document).on("click",".radio-ma",function(){ 

//       if( $(this).is(":checked") ){
//       $('.radio-ma').not(this).attr('checked',false);
//           var val = $(this).val(); // retrieve the value
//           if(val == "yes"){
//           $(".input-ma").show();
//            $(".input-ma").val("");
//     $(".test2").show();
//       }
//       else{
//     $(".allergy").empty();
//     $(".input-ma").val("No");
//         $(".input-ma").hide();
//   $(".test2").hide();
//       }
//     }
//   });


function bp(){
    var sbp = $("#SBP").val();
    var dbp = $("#DBP").val();

    $("#BP").val(sbp+"/"+dbp);    
}

function validate()
{ 

    var sbp = $("#SBP").val();
    var dbp = $("#DBP").val();

    // BP text validation
    if((sbp != '' && dbp == '') || (sbp == '' && dbp != '')) {
        alert('Please make sure both the BP paramaters are specified');
        if(sbp == ''){
            $("#SBP").focus();
        }  
        if(dbp == ''){
            $("#DBP").focus();        
        }
        return false;
    }  

    var hasInput=false;
    $( ".check" ).each(function () {
        if($(this).val()  !== ""){
            hasInput=true;        
        }
    });

    if(!hasInput){
        alert("Please Enter Atleast One Vital");
        return false;
    }

	/*var dg = $(".dg").text();

  if(dg === ''){
    if ($("#radio12").is(":checked")) {
      var alergy = $("#input-allergy").val();
      
      // Radio yes is choosen then perform validation to check whether allergy is specified or no
      if(alergy === ''){
        alert("Please specify the drug name you are allergic to");
        return false;
      }  
    
    }else if($("#radio13").is(":checked")){
      return true;
    }else{
		  alert("Do you have a drug allergy? Please choose either yes/no");
      return false;
    } 
  }else{
    if ($("#radio14").is(":checked")) {
      var alergy1 = $("#input-ma").val();
      
      // Radio yes is choosen then perform validation to check whether allergy is specified or no
      if(alergy1===''){
        alert("Please specify the drug name you are allergic to");
        return false;
      }  
    }else if($("#radio15").is(":checked")){
      return true;
    }else{
		  alert("Do you have a more drug allergies? Please choose either yes/no");
      return false;
    }
}*/
}
</script>