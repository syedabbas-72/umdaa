<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Vitals</li>
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
                <div class="row col-md-12 "> 
                    <div class="col-md-3" id="view_casesheet">
                        <div class="col-md-12 ">
                            <?php $this->load->view('profile/patient_info_left_nav'); ?>
                        </div>
                    </div>
                    <div class="col-md-9" id="view_caseresults" class="view_caseresults">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Vitals Information
                                <div class="pull-right actionButtons">
                                    <?php 
                                    // echo '<pre>';
                                    // print_r($appointmentInfo);
                                    // echo '</pre>';
                                    // exit();
                                    // $appointmentInfo[0]->registration_payment_status != 0 && 
                                    if($appointmentInfo[0]->appointment_payment_status != 0) { 
                                        //if($appointmentInfo[0]->appintment_status != 'booked' && $appointmentInfo[0]->appintment_status != 'reschedule' && $appointmentInfo[0]->appintment_status && 'drop' || $appointmentInfo[0]->appintment_status != 'closed') {
                                        $possibleStatus = array('checked_in','vital_signs','waiting','in_consultation');

                                        $appointmentStatus = $appointmentInfo[0]->appointment_status;
                                        
                                        if(in_array($appointmentStatus,$possibleStatus)){
                                            ?>
                                            <a href="<?php echo base_url('Vitals/add_vitals/'.$patient_id.'/'.$appointment_id); ?>"><i class="fas fa-plus add"></i></a>
                                            <?php 
                                        }
                                    }
                                    ?>
                                    <?php 
                                    /*
                                    - For Edit Vitals
                                    - There should be atleast one vital in the current date for the patient
                                    - Check if any vitals captured on a current date
                                    */
                                    $this->db->select('*');
                                    $this->db->from('patient_vital_sign');
                                    $this->db->where('vital_sign_recording_date_time >',date('Y-m-d'));
                                    $this->db->where('patient_id =',$patient_id);
                                    $this->db->where('clinic_id =',$clinic_id);
                                    $vitalCount = $this->db->get()->num_rows();
                                    // echo $this->db->last_query();
                                    if($vitalCount > 0){
                                    ?>
                                        <a href="<?php echo base_url('Vitals/vital_edit/'.$patient_id.'/'.$appointment_id); ?>"><i class="fas fa-pencil-alt edit"></i></a>
                                    <?php } 
                                    $this->db->select('*');
                                    $this->db->from('patient_vital_sign');
                                    $this->db->where('appointment_id =',$appointment_id);
                                    $count = $this->db->get()->num_rows();
                                    // echo $this->db->last_query();
                                    if($count!=0)
                                    {
                                        ?>
                                        <a href="<?php echo base_url('vitals/print_vitals/'.$patient_id.'/'.$appointment_id); ?>" target="_blank"><i class="fas fa-print print"></i></a>
                                        <?php
                                    }
                                    ?>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding: 10px" >
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 drugAllergy">
                                        DRUG ALLERGY: &nbsp; 
                                        <?php
                                        if($patient_info->allergy == ''){
                                            $cls = 'noInfo';
                                        }else if($patient_info->allergy == 'No'){
                                            $cls = 'noAllergy';
                                        }else if($patient_info->allergy != '' || $patient_info->allergy != NULL){
                                            $cls = 'allergy';
                                        }
                                        ?>
                                        <span class="<?=$cls?>">
                                            <?php 
                                            if($patient_info->allergy != ''){
                                                echo str_replace(',',', ',$patient_info->allergy);
                                            }else if($patient_info->allergy == '' || $patient_info->allergy != NULL){
                                                echo 'Not specified';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <?php
                                for($j=0; $j<count($result); $j++)
                                {
                                    $doctor_data = $this->db->query("select a.doctor_id,d.first_name,d.last_name,d.qualification from appointments a,doctors d where a.doctor_id=d.doctor_id and a.appointment_id='".$result[$j]->appointment_id."'")->row();

                                    $vital_result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and vital_result != "" and vital_sign_recording_date_time = "'.$result[$j]->vital_sign_recording_date_time.'" order by position asc')->result();
                                    
                                    $apInfo = $this->db->query("select * from appointments a,patient_vital_sign p where a.appointment_id=p.appointment_id and a.appointment_id='".$vital_result[0]->appointment_id."' group by p.appointment_id ")->row();
                                    // echo $this->db->last_query()."<br>";
                                    if($apInfo->doctor_id != $appointmentInfo[0]->doctor_id)
                                        continue;
                                    ?>
                                    <!-- Vitals & Doctor Info -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Dr. <?=ucwords($doctor_data->first_name.' '.$doctor_data->last_name); ?>
                                            <sub>(<?=$doctor_data->qualification;?>)</sub>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <?=date('d M Y',strtotime($result[$j]->vital_sign_recording_date_time)).'&nbsp;&nbsp;&nbsp;'.strtoupper(date('h:i a',strtotime($result[$j]->vital_sign_recording_date_time)));?> 
                                        </div>
                                    </div>
                                    <div class = "row text-center">
                                        <?php
                                        $sbp = '';
                                        for($k = 0; $k<count($vital_result); $k++){
                                            
                                            if($vital_result[$k]->vital_result != ""){
                                                $test = $this->db->query('select * from vital_sign where short_form = "'.$vital_result[$k]->vital_sign.'"')->row();
                                                if($vital_result[$k]->vital_sign == "SBP"){
                                                    $status_info1 = $this->db->query('select * from vital_sign where short_form ="SBP"')->row();
                                                    if( $vital_result[$k]->vital_result >= $status_info1->low_range && $vital_result[$k]->vital_result <= $status_info1->high_range){
                                                        $sbpVitalStatus = "vsNormal";
                                                    }else{
                                                        $sbpVitalStatus = "vsWarning";
                                                    }
                                                    $vital_render[$vital_result[$k]->position]['vital_sign_name'] = 'BP';
                                                    $vital_render[$vital_result[$k]->position]['sbp'] = $vital_result[$k]->vital_result;
                                                    $vital_render[$vital_result[$k]->position]['status'] = $sbpVitalStatus;
                                                    $vital_render[$vital_result[$k]->position]['unit'] = 'mmHg';
                                                    
                                                }else if($vital_result[$k]->vital_sign == "DBP"){
                                                    $status_info2 = $this->db->query('select * from vital_sign where short_form ="DBP"')->row();
                                                    if($vital_result[$k]->vital_result >= $status_info2->low_range && $vital_result[$k]->vital_result <= $status_info2->high_range){
                                                        $dbpVitalStatus = "vsNormal";
                                                    }else{
                                                        $dbpVitalStatus = "vsWarning";
                                                    }
                                                    $vital_render[$vital_result[$k]->position]['vital_sign_name'] = 'BP';
                                                    $vital_render[$vital_result[$k]->position]['dbp'] = $vital_result[$k]->vital_result;
                                                    $vital_render[$vital_result[$k]->position]['status'] = $dbpVitalStatus;
                                                    $vital_render[$vital_result[$k]->position]['unit'] = 'mmHg';
                                                }else{
                                                    $status_info = $this->db->query("select * from vital_sign where short_form ='".$vital_result[$k]->vital_sign."'")->row();
                                                    if($vital_result[$k]->vital_result >= $status_info->low_range  && $vital_result[$k]->vital_result <= $status_info->high_range){
                                                        $vitalStatus = 'vsNormal';
                                                    }else{
                                                        $vitalStatus = 'vsWarning';
                                                    }
                                                    $vital_render[$vital_result[$k]->position]['vital_sign_name'] = $vital_result[$k]->vital_sign;
                                                    $vital_render[$vital_result[$k]->position]['result'] = $vital_result[$k]->vital_result;
                                                    $vital_render[$vital_result[$k]->position]['status'] = $vitalStatus;
                                                    $vital_render[$vital_result[$k]->position]['unit'] = $test->unit;
                                                }
                                            }

                                        }

                                        $i = 0;
                                        for($a=0; $a<=count($vital_render); $a++){
                                            ?>
                                                <?php
                                                if(count($vital_render[$a]) > 0) { 
                                                    if(isset($vital_render[$a]['dbp'])){
                                                        ?>
                                                        <div class = "col-md-4 vitalSign <?php echo ($i == 2 ? 'noRytBdr' : '') ?>">
                                                            <h4><?=$vital_render[$a]['vital_sign_name']; ?></h4>
                                                            <h3 class = "<?=$vital_render[$a]['status']; ?>">
                                                                <span class="<?=$sbpVitalStatus;?>"><?=$vital_render[$a]['sbp']; ?></span>/<span class="<?=$dbpVitalStatus;?>"><?=$vital_render[$a]['dbp']; ?></span><span class="vitalSignUnit">mmHg</span>
                                                            </h3>
                                                        </div>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <div class = "col-md-4 vitalSign <?php echo ($i == 2 ? 'noRytBdr' : '') ?>" >
                                                            <h4><?=$vital_render[$a]['vital_sign_name']; ?></h4>
                                                            <h3 class="<?=$vital_render[$a]['status']; ?>">
                                                                <?=$vital_render[$a]['result']; ?><span class="vitalSignUnit"><?=$vital_render[$a]['unit']; ?></span>
                                                            </h3>
                                                        </div>
                                                        <?php
                                                    }

                                                    // Increase counter i
                                                    ($i == 2 ? $i = 0 : $i++);

                                                } // Counter count close
                                                ?>
                                            <?php 
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    // exit(); 
                                }
                                ?>                            
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
        $("#"+id).append('<tr id="'+id+'_'+count+'"><td class="vital_row"><input type="text" name="vital_sign[]" style="width:200px;float:left;border-top:0px;border-left:0px;border-right:0px;" class="inline">&nbsp;&nbsp;<span class="inline" style=" display:inline-block;">:</span></td><td><input type="text" name="vital_sign_val[]" class="vital_txt" style="width:200px;border-top:0px;border-left:0px;border-right:0px;"></td><td style="text-align: center;"><button type="button" class="btn btn-success" onclick=add_vital("'+id+'");>+</button><button type="button" class="btn btn-danger" onclick=del_vital("'+id+'_'+count+'");>-</button></td></tr>');

    }
    function del_vital(id){
        $("#"+id).remove();
    }

    function add_allergy(){
        var id = 'allergy';

        var count=$("#"+id).find('tr').length;  
        $("#"+id).append('<tr id="'+id+'_'+count+'" class="allergy"><td colspan="3"><input type = "text" id="input-ma" class = "form-control input-ma" name = "more_allergies[]" style="margin-top:10px;border-top:0px;border-left:0px;border-right:0px;width: 300px;"/></td><td style="text-align: center;"><button type="button" class="btn btn-success test2" onclick=add_allergy("'+id+'");>+</button><button type="button" class="btn btn-danger test2" onclick=del_allergy("'+id+'_'+count+'");>-</button></td></tr>');

    }
    function del_allergy(id){
        $("#"+id).remove();
    }


</script>
<script type="text/javascript">
$(document).on('keyup','#Weight',function(){
    var height = $('#Height').val()/100;
    var weight = $('#Weight').val();

    var bmi = Math.round(weight/(height*height));
    var bsa = Math.sqrt((height/weight)*3600).toFixed(2);

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

    if(value1.length >= 2){
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
                if(data_1 == "normal" ){
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
    //alert('working');
    window.history.back();
});

$(document).on('click','#check_allergy',function(){
    if($(this).is(':checked')){
        $("#input-allergy").show();

    }
    else{
        $("#input-allergy").hide();

    }
});

$(document).on("click",".radio-ip",function(){ // bind a function to the change event

    if( $(this).is(":checked") ){
        $('.radio-ip').not(this).attr('checked',false);
        var val = $(this).val(); // retrieve the value
        if(val == "yes"){
            $("#input-allergy").show();
        }else{
            $("#input-allergy").val("No");
            $(".input-ma").val("No");
            $("#input-allergy").hide();
        }
    }
});

$(document).on("click",".radio-ma",function(){ // bind a function to the change event

    if( $(this).is(":checked") ){
        $('.radio-ma').not(this).attr('checked',false);
        // retrieve the value
        var val = $(this).val(); 
        if(val == "yes"){
            $(".input-ma").show();
            $(".input-ma").val("");
            $(".test2").show();
        }
        else{
            $(".allergy").empty();
            $(".input-ma").val("No");
            $(".input-ma").hide();
            $(".test2").hide();
        }
    }
});

function bp(){
    var sbp = $("#SBP").val();
    var dbp = $("#DBP").val();

    $("#BP").val(sbp+"/"+dbp);    
}

function validate() { 
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

    var dg = $(".dg").text();

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
    }
}
</script>