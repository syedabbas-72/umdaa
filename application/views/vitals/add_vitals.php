<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Add Vitals</li>
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
                                Add Vitals                                
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding: 10px" >
                            <div class="col-md-12">		  
                                <form action="<?php echo base_url('Vitals/vitals_save/'.$patient_id.'/'.$appointment_id) ?>" method="POST" onsubmit="return validate();"   class="form customForm">
                                    <input type = "hidden" name = "patient_id" value = "<?=$patient_id; ?>"/>
                                    <input type = "hidden" name = "appointment_id" value = "<?=$appointment_id; ?>"/>
                                    <div class="row col-md-12">
                                        <div class="col-md-9">
                                            <table id="vital" class="table vitals_info">
                                                <tr>
                                                    <td><label class="col-form-label">Pulse Rate</label></td>
                                                    <td>:</td>
                                                    <td colspan="3">
                                                        <input type="number" name="vitals[PR]" id="PR" min="0" step=".01" class ="check form-control pr" onkeyup="checkVitalsMax('pr')" value = "" max="300" />
                                                    </td>
                                                    <td><label>Per Min</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">BP</label></td>
                                                    <td>:</td>
                                                    <td><input type="number" value="" name="vitals[SBP]" id="SBP"  min="0" max="250" step=".01" class="check form-control sbp" onkeyup="checkVitalsMax('sbp')" placeholder="Systolic" onkeyup="return bp();" /></td>
                                                    <td> / </td>
                                                    <td><input type="number" name="vitals[DBP]" value="" id="DBP"  min="0" max="250" step=".01" class="check form-control dbp" onkeyup="checkVitalsMax('dbp')" placeholder="Diastolic" onkeyup="return bp();"/>
                                                        <input type = "hidden" id = "BP" style="border-top:0px;border-left:0px;border-right:0px;"/>
                                                    </td>
                                                    <td class="text-left"><label>mm/HG</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">Respiratory Rate</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[RR]"  min="0" step=".01" value = "" max="60" id = "RR" class = "check form-control rr" onkeyup="checkVitalsMax('rr')" /></td>
                                                    <td class="text-left"><label>Per Min</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">Temperature</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[Temp]"  min="0" max="110" step=".01" value = "" id = "Temp" class = "check form-control temp" onkeyup="checkVitalsMax('temp')" /></td>
                                                    <td class="text-left"><label>°F</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">SaO2</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[SaO2]"  min="0" max="100" step=".01" value = "" id = "SaO2" class = "check form-control sao" onkeyup="checkVitalsMax('sao')"/></td>
                                                    <td class="text-left"><label></label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">Height</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[Height]"  min="0" max="250" step=".01" value = "" id = "Height" class = "check form-control height" onkeyup="checkVitalsMax('height')"/></td>
                                                    <td class="text-left"><label>CM</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">Weight</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[Weight]"  min="0" step=".01" max="500" value = "" class = "check form-control weight" onkeyup="checkVitalsMax('weight')" id = "Weight" /></td>
                                                    <td class="text-left"><label>KG</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">BMI</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[BMI]"  min="0" step=".01" readonly value = "" id = "BMI" class = "check form-control" onclick = "myFunction(this.id)" /></td>
                                                    <td class="text-left"><label>kg/m2</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">BSA</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[BSA]"  min="0" step=".01" readonly value = "" id = "bsa" class = "check form-control" onclick="myFunction(this.id)" /></td>
                                                    <td class="text-left"><label>kg/m2</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label class="col-form-label">WH Ratio</label></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number"  name = "vitals[WH_ratio]"  min="0" step=".01" value = "" id = "vhr" class = "check form-control" /></td>
                                                    <td class="text-left"><label></label></td>
                                                </tr>
                                                <tr>
                                                    <td><input type = "text" class = "form-control"  min="0" step=".01" name = "vital_sign[]"></td>
                                                    <td>:</td>
                                                    <td colspan="3"><input type = "number" class = "form-control" name = "vital_sign_val[]" /></td>
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
                                            if($drug_allergy == 'no'){
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

                                        <div class="col-md-12" id="drugAllergyTB_div" style="display: none;">
                                            <strong>Specify the drug names you are allergic to (Eg: Dolo 650, Caterpil, etc.)</strong><br>
                                            <textarea class="form-control" style="width:600px; border-radius:4px;" rows="2" name="allergy" id="allergy_ta" maxlength="250"><?=ucwords($patient_info->allergy); ?></textarea>
                                        </div>

                                        <?php /*$drug_allergy = $patient_info->allergy;
                                        if($drug_allergy != '') { ?>
                                            <div class="col-md-12" style="padding-top:20px;">
                                                <strong>Drug Allergy</strong><br>
                                                <p class="dg"><?php echo trim($drug_allergy,","); ?></p>
                                                <input type="hidden" id="drug_allery_tb" name="drug_allergy" value="<?php echo trim($drug_allergy,","); ?>" >
                                            </div>

                                            <!-- Any more drug allergy -->
                                            <div class="col-md-12" style="padding-top:15px;">
                                                <strong>Any More Drug Allergies<span class="error" style="color:red">*</span>
                                                    <div class="form-group">
                                                        <div class="row" id="check_ptype">
                                                            <div class="form-check">
                                                                <input type="radio" class="radio-ma form-check-input" id="yes_ma_rb" name="d_allergy" required="required">
                                                                <label class="form-check-label" for="yes_ma_rb">Yes</label>
                                                                <!-- <input type="radio" class="radio-ma" id="yes_ma_rb" name='d_allergy' value="yes" required="required">
                                                                <label for="yes_ma_rb" > Yes </label> -->
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="radio-ma form-check-input" id="no_ma_rb" name="d_allergy" required="required">
                                                                <label class="form-check-label" for="no_ma_rb">No</label>
                                                                <!-- <input type="radio" class="radio-ma" id="no_rb" name='d_allergy' value="no">
                                                                <label for="norb" > No </label> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </strong>
                                            </div>
                                        <?php } else{?>
                                            <div class="col-md-12" style="padding-top:15px;">
                                                <strong>Drug Allergy<span class="error" style="color:red">*</span>
                                                    <div class="form-group">
                                                        <div class="row" id="check_ptype">
                                                            <div class="form-check">
                                                                <input type="radio" class="radio-ip form-check-input" id="yes_ip_rb" name="md_allergy" required="required">
                                                                <label class="form-check-label" for="yes_ip_rb">Yes</label>

                                                               <!--  <input type="radio" class="radio-ip" id="yes_ip_rb" name='md_allergy' value="yes" required="required">
                                                                <label for="yes_ip_rb" >Yes</label> -->
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="radio-ip form-check-input" id="no_ip_rb" name="md_allergy" required="required">
                                                                <label class="form-check-label" for="no_ip_rb">No</label>
                                                                <!-- <input type="radio" class="radio-ip" name='md_allergy' id="no_ip_rb" value="no">
                                                                <label for="no_ip_rb" >No</label> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </strong>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-12" > 
                                            <input type = "text" id="input-allergy" class = "form-control" name = "allergy" style="display:none;margin-top:10px;border-top:0px;border-left:0px;border-right:0px;width: 300px;"/>
                                        </div>
                                        <div class="col-md-12">
                                            <textarea class="form-control input-ma" rows="5" name = "more_allergies" id="input-ma" style="display:none;margin-top: 0px; margin-bottom: 0px; height: 89px;" ></textarea>
                                        </div>
                                        */?>
                                        <!-- <input type = "submit" name="save_vitals" class = "btn btn-success" value = "Submit" style="margin-top:20px;margin-right:50px;"/> -->
                                        <button type = "submit" class="btn btn-success" name="save_vitals" value="Submit" style="margin-top:20px;margin-right:10px;">Submit</button>
                                        <button type = "submit" class="btn btn-success" name="save_vitals" value="SubmitPrint" style="margin-top:20px;margin-right:10px;">Submit&Print</button>
                                        <!--   <input type = "submit" name="print" class = "btn btn-success" value = "Submit & Print" style="margin-top:20px;margin-right:50px;"/> -->
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
}

function del_vital(id){
    $("#"+id).remove();
}



// Drug Allergy Radio button Secltion
$('input[type="radio"]').change(function() {
    if(this.value == 'Yes'){
        $("#drugAllergyTB_div").show();
        $('#allergy_ta').attr("required","required");
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

// function add_allergy(){
//     var id = 'allergy';
//     var count=$("#"+id).find('tr').length;  
//     $("#"+id).append('<tr id="'+id+'_'+count+'" class="allergy"><td colspan="3"><input type = "text" id="input-ma" class = "form-control input-ma" name = "more_allergies[]" style="margin-top:10px;border-top:0px;border-left:0px;border-right:0px;width: 300px;"/></td><td style="text-align: center;"><button type="button" class="btn btn-success test2" onclick=add_allergy("'+id+'");>+</button><button type="button" class="btn btn-danger test2" onclick=del_allergy("'+id+'_'+count+'");>-</button></td></tr>');
// }

// function del_allergy(id){
//     $("#"+id).remove();
// }
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
    // console.log(value1);
    //alert(value1);
    var id = $(this).attr('id');
    //  console.log(id);
    var url = "<?php echo  base_url('CaseSheet/change');?>";


    if(value1.length >= 2)
    {
        //  console.log(value1);
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
   
$(document).on('click','#back',function(){
    window.history.back();
});

// $(document).on('click','#check_allergy',function(){
//     if($(this).is(':checked')){
//         $("#input-allergy").show();

//     }
//     else{
//         $("#input-allergy").hide();

//     }
// });

// $(document).on("click",".radio-ip",function(){ 

//     if( $(this).is(":checked") ){
//         $('.radio-ip').not(this).attr('checked',false);
//         var val = $(this).val(); 
//         if(val == "yes"){
//             $("#input-allergy").show();

//         }
//         else{
//             $("#input-allergy").val("No");
//             $(".input-ma").val("No");
//             $("#input-allergy").hide();

//         }
//     }
// });

// $(document).on("click",".radio-ma",function(){ 
//     if( $(this).is(":checked") ){
//         $('.radio-ma').not(this).attr('checked',false);
//         var val = $(this).val(); 
//         if(val == "yes"){
//             $(".input-ma").show();
//             $(".input-ma").val("");
//             $(".test2").show();
//         }
//         else{
//             $(".allergy").empty();
//             $(".input-ma").val("No");
//             $(".input-ma").hide();
//             $(".test2").hide();
//         }
//     }
// });


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
    $(".check").each(function () {
        if($(this).val()  !== ""){
            hasInput=true;        
        }
    });

    if(!hasInput){
        alert("Please Enter Atleast One Vital");
        return false;
    }

    // var dg = $(".dg").text();

    // if(dg === ''){
    //     if ($("#yes_ip_rb").is(":checked")) {
    //         var alergy = $("#input-allergy").val();

    //         // Radio yes is choosen then perform validation to check whether allergy is specified or no
    //         if(alergy === ''){
    //             alert("Please specify the drug name you are allergic to");
    //             return false;
    //         }  

    //     }else if($("#no_ip_rb").is(":checked")){
    //         return true;
    //     }else{
    //         alert("Do you have a drug allergy? Please choose either yes/no");
    //         return false;
    //     } 
    // }else{
    //     if ($("#yes_ma_rb").is(":checked")) {
    //         var alergy1 = $("#input-ma").val();

    //         // Radio yes is choosen then perform validation to check whether allergy is specified or no
    //         if(alergy1===''){
    //             alert("Please specify the drug name you are allergic to");
    //             return false;
    //         }  
    //     }else if($("#no_ma_rb").is(":checked")){
    //         return true;
    //     }else{
    //         alert("Do you have a more drug allergies? Please choose either yes/no");
    //         return false;
    //     }
    // }
    setTimeout(function(){location.reload();}, 1000);
}
</script>