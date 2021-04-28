<style type="text/css">
.radio label::after{
    top:10px !important;
}
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li><a href="<?php echo base_url("profile/index/".$patient_id."/".$appointment_id); ?>">Patient</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Payment Collection</li>
        </ol>
    </div>
</div>
<?=$res?>
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
                    <div class="col-md-9">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Payment Collection
                            </div>
                        </div>
                        <div class="card-body">						
                            <?php 						
                            echo form_open(site_url("patients/confirm_payment/".$patient_id."/".$appointment_id), array("class" => "form-horizontal","id"=>"app_form")); ?>
                            <div class="col-md-12 billing">
                                <!-- Registration Fee -->
                                <?php if($patient_payment_status <= 1){ ?>
                                    <div class="row col-md-12 payItem">  
                                        <div class="col-md-9 form-group form-check item">
                                            <table cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="chkBox">
                                                        <input id="registration_cb" type="checkbox" class="form-check-input" name="registration" value="<?php echo $get_fee->registration_fee; ?>" onclick="feePayment();" checked>
                                                    </td>
                                                    <td class="itemName">
                                                        <label class="form-check-label" for="registration_cb">Registration Fee</label>
                                                    </td>
                                                </tr>
                                             </table>                                            
                                        </div>
                                        <div class="col-md-2 text-right price">
                                            <label id="registration_fee_lbl"><?php echo number_format($get_fee->registration_fee,2); ?></label>
                                            <input type="hidden" id="registration_fee_tb" name="registration_fee" value="<?php echo $get_fee->registration_fee; ?>">
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                <?php }else{ ?>
                                    <!-- hidden text input storing registration fees -->
                                    <input type="hidden" id="registration_fee_tb" name="registration_fee" value="0">
                                <?php } ?>

                                <!-- Consultation Fee -->
                                <?php if($app_payment_status->payment_status == 0){ ?>
                                    <div class="row col-md-12 payItem">
                                        <div class="col-md-9 form-group form-check item"> 
                                            <table cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="chkBox">
                                                        <input type="checkbox" class="form-check-input" id="consultation_cb" name="consultation" value="<?php echo $get_info->consulting_fee; ?>" onclick="feePayment();" checked> 
                                                    </td>
                                                    <td class="itemName">
                                                        <label class="form-check-label" for="consultation_cb">Consultation Fee</label>
                                                    </td>
                                                </tr>
                                            </table>       			
                                        </div>
                                        <div class="col-md-2 text-right price">
                                            <label id="consultation_fee_lbl"><?php echo number_format($get_info->consulting_fee,2); ?></label>
                                            <input type="hidden" id="consultation_fee_tb" name="consultation_fee" value="<?php echo $get_info->consulting_fee; ?>">
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                <?php }else{ ?>
                                <!-- hidden text input storing registration fees -->
                                <input type="hidden" id="consultation_fee_tb" name="consultation_fee" value="0">
                                <?php } ?>

                                <!-- Procedures select box -->
                                <div class="row col-md-12 xtraItem noBdr">
                                    <div class="col-md-8">
                                        <label for="procedure_select" class="noGap">Select Procedure for billing (if any)</label>
                                        <select id="procedure_select" onchange="return addProcedure(this.value)" name="procedure_select" class="form-control select2">
                                            <option value="">Select Procedure</option>
                                            <?php foreach ($procedures as $value) { ?>
                                                <option data-val="<?php echo preg_replace('/(<br>)+$/', '', $value->procedure_cost);; ?>" value="<?php echo $value->clinic_procedure_id; ?>"><?php echo $value->procedure_name; ?></option>
                                            <?php } ?>
                                        </select> 
                                    </div>
                                    <div class="col-md-4">
                                        <!-- empty for now -->
                                    </div>
                                </div>

                                <!-- Existing procedures list (if any were already selected) -->
                                <div id="cart-table" class="col-md-12">
                                    <?php
                                    // Get un paid procedures list with respect to the current appointment
                                    $this->db->select('PP.patient_procedure_id, PP.clinic_id, PP.patient_id, PP.doctor_id, PP.medical_procedure_id, CP.procedure_name, CP.procedure_cost');
                                    $this->db->from('patient_procedure PP');
                                    $this->db->join('clinic_procedures CP','PP.medical_procedure_id = CP.clinic_procedure_id','left');
                                    $this->db->where('PP.appointment_id =',$appointment_id);
                                    $this->db->where('PP.payment_status =',0);

                                    $procedureList = $this->db->get()->result_array();
                                    $proCost = 0;
                                    if($procedureList > 0) {
                                        foreach($procedureList as $procedureRec){
                                            extract($procedureRec);
                                            $proCost+=$procedure_cost;
                                            echo '<div class="row xtraItem proDivs" id="'.$medical_procedure_id.'_div" data-id="'.$patient_procedure_id.'">';
                                            echo '<div class="col-md-9"><input type="hidden" name="patient_procedure_id[]" value="'.$patient_procedure_id.'"><input type="hidden" name="procedure_id[]" value="'.$medical_procedure_id.'"><input type="hidden" class="cart-service-'.$medical_procedure_id.'" name="cart_services[]" value="'.$procedure_name.'">'.$procedure_name.'</div>';
                                            echo '<div class="col-md-2 text-right xtraPrice pr-0"><input type="hidden" name="cart_prices[]" class="cart-price-'.$medical_procedure_id.'" value="'.$procedure_cost.'">'.number_format($procedure_cost, 2).'</div>';
                                            echo '<div class="col-md-1 text-left error delete-cart-row"><i class="fas fa-times-circle" data-id="'.$patient_procedure_id.'" onclick="return delDiv(\''.$medical_procedure_id.'_div\');"></i></div>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>	       
                                <input type="hidden" id="ProCost" value="<?=$proCost?>">       
                                <hr>
                                <!-- Total Amount -->
                                <div class="row col-md-12" style="background: #fbfbfb;padding-top: 10px">
                                    <div class="col-md-10 btmGap">
                                        <label for="procedure_select" class="noGap font-weight-bold">Total Amount</label> 
                                    </div>
                                    <div class="col-md-2 text-left">
                                        <label for="procedure_select" id="total_amt_td" class="noGap font-weight-bold"></label>
                                    </div>
                                    <input id="billing_amount_tb" type="hidden" name="total_amt" value="">    		
                                </div>

                                <!-- Discount text Field -->
                                <!-- Check Discount -->
                                <div class="row col-md-12" style="background: #fbfbfb; border-bottom:1px solid #ebebeb; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div class="col-md-10 btmGap">
                                        <label for="discount_type_sb" class="noGap control-label font-weight-bold">Discount</label>
                                        <select onchange="changeDiscount();" id="discount_type_sb" name="discount_type" class="form-control" style="width: 70px">
                                            <option>INR</option>
                                            <option>%</option>
                                        </select>
                                        <!-- <i class="small font-weight-bold text-danger discountPro">* Will be discounted from procedure</i> -->
                                    </div>
                                    <div class="col-md-2" style="padding-top:25px;">
                                        <input id="discount_tb" onkeyup = "calcDiscount();" type="number" min="0"  name="discount" class="text-left form-control" value="0" onkeypress="return numeric()">
                                    </div>
                                </div>
                                <!-- Check Discount -->
                                <div class="row col-md-12" style="background: #fbfbfb; border-bottom:1px solid #ebebeb; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div class="col-md-10 btmGap">
                                        <label for="procedure_select" class="noGap control-label font-weight-bold">NET PAY</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label  id="net_pay" for="procedure_select" class="noGap control-label font-weight-bold">NET PAY</label>
                                    </div>
                                </div>
                                <div class="row col-md-12" style="background: #fbfbfb; border-bottom:1px solid #ebebeb; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div class="col-md-10 btmGap mt-1">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div  style="display: flex !important;">
                                                    <div style="margin-top: 6px !important;" class="p-1">
                                                        <input type="checkbox" class="advance_check mt-1" value="">
                                                    </div>
                                                    <label for="procedure_select" class="noGap control-label font-weight-bold">Advance 
                                                        <span style="font-size: 11px;" class="font-italic" id="minimumPayment"></span>
                                                    </label>
                                                </div>
                                                    <i class="small font-weight-bold text-danger">* Advance payment applied only for Procedures.</i>    
                                            </div>
                                            <div class="col-md-3">
                                                <div class="pull-right advance_status"></div>
                                            </div>
                                        </div>
                                        
                                                    
                                        <span id="minimum_advance_line"></span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control advance_input" name="advanceAmnt" readonly="" onkeypress="return decimal()">
                                        <input type="hidden" value="<?=$clinics->minimum_advance?>" class="form-control minimum_advance_payment">
                                    </div>
                                    <div class="col-md-10 btmGap mt-1">
                                        <label for="procedure_select" class="noGap control-label font-weight-bold">Outstanding Payment For Procedure(s) </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control osb" readonly="" disabled="">
                                        <input type="hidden" class="form-control osb" name="osb">
                                    </div>
                                    
                                </div>

                                <div class="row col-md-12" style="background: #fbfbfb; border-bottom:1px solid #ebebeb; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div class="col-md-6">
                                        <label class="col-form-label font-weight-bold">Payment Mode</label>
                                        <select class="form-control payment_mode" name="payment_mode" id="payment_type">
                                            <option disabled>Select Payment Mode</option>
                                            <option value="cash"  selected="">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="online">Online</option>
                                            <option value="paytm">Paytm</option>
                                            <option value="googlepay">Google Pay</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label font-weight-bold">Transaction ID</label>
                                        <input type="text" class="form-control transaction_id" name="transaction_id" disabled="">
                                    </div>
                                </div>

                                <div class="row col-md-12" style="background: #fbfbfb; border-bottom:1px solid #ebebeb; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div class="col-md-10 btmGap mt-1">
                                        <h5 class="font-weight-bold control-label text-primary"><i class="fa fa-dot-circle"></i> Total Amount To Be Paid </h5>
                                    </div>
                                    <div class="col-md-2">
                                        <h5 class="payNow px-0 font-weight-bold text-primary"></h5>
                                    </div>
                                </div>
                                <div class="row col-md-6 text-center" style="margin-top: 20px;float: right;margin-right: 10px">
                                    <input style="margin-right:10px" type="submit" class="btn btn-primary" name='confirm_payment' id="submit" value="Accept Payment">  	   
                                </div>
                            </div>
                            <?php echo form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
if(count($procedureList)>0)
{
    ?>
    <script type="text/javascript">
        $('.advanceRow').show();
    </script>
    <?php
}
else
{
    ?>
    <script type="text/javascript">
        $('.advanceRow').hide();
    </script>
    <?php
}
?>

<!-- JScript -->
<script type="text/javascript">
    $(document).ready(function(){
        advanceCalculation();
        $('#minimum_advance_line').hide();
        // $('.advanceRow').hide();

        $('.payment_mode').on("change",function(){
            var value = $(this).val();
            if(value == "cash")
            {
                $('.transaction_id').val('');
                $('.transaction_id').attr("disabled","disabled");
                $('.transaction_id').attr("disabled","disabled");
            }
            else
            {
                $('.transaction_id').removeAttr("disabled");
            }
        });

        $('.advance_check').on("click",function(){
            var total = parseInt($('#net_pay').html().trim());
            if(total == 0)
            {
                alert("Please Select Any Of The Procedures to Check This.");
                $('.advance_check').prop("checked", false);
            }
            else
            {
                advanceCalculation();  
            }            
        });

        // Check Advance Input min and Max Parameters
        $('.advance_input').on("input", function(){
            var total = parseInt($('#ProCost').val());
            var min = $('.advance_input').attr("min");
            var max = $('.advance_input').attr("max");
            var input = $(this).val();
            var conTotal = parseInt($('#registration_fee_tb').val())+parseInt($('#consultation_fee_tb').val());
            if(parseInt(input)>=parseInt(max))
            {
                $('.advance_input').val(max);
                $('.advance_status').html('ADEQUATE');
                $('.advance_status').removeClass('inadequate');
                $('.advance_status').addClass('adequate');
                $('#submit').removeAttr("disabled","disabled");
                $('.osb').val(total-parseInt(max));
                $('.payNow').html("Rs."+(parseInt(conTotal)+parseInt(max))+"/-")
            }
            else if(parseInt(input)>=parseInt(min))
            {
                $('.advance_status').html('ADEQUATE');
                $('.advance_status').removeClass('inadequate');
                $('.advance_status').addClass('adequate');
                $('#submit').removeAttr("disabled","disabled");
                $('.osb').val(total-parseInt(input));
                $('.payNow').html("Rs."+(parseInt(conTotal)+parseInt(input))+"/-")
            }
            else if(parseInt(input)<parseInt(min))
            {
                // $('.advance_input').val(min);
                $('.advance_status').html('INADEQUATE');
                $('.advance_status').removeClass('adequate');
                $('.advance_status').addClass('inadequate');
                $('#submit').attr("disabled","disabled");
                $('.osb').val(total-parseInt(input));
                $('.payNow').html("Rs."+(parseInt(conTotal)+parseInt(input))+"/-")
            }
            else
            {
                $('.advance_status').html('INADEQUATE');
                $('.advance_status').removeClass('adequate');
                $('.advance_status').addClass('inadequate');                
                $('#submit').attr("disabled","disabled");
                $('.osb').val(total-parseInt(input));
                $('.payNow').html("Rs."+(parseInt(conTotal)+parseInt(input))+"/-")
            }

            // billing();
        });


    });
</script>
<script>

var total = 0;
var payment_status = '<?php echo $patient_payment_status->payment_status; ?>';

if($("#input_registration_val").length == 1) {
    var reg_fee = '<?php echo $get_fee->registration_fee; ?>';
    var reg_val = $("input[id='input_registration_val']").val();
}else{
    var reg_fee= 0;
    var reg_val = 0;
}

if($("#input_consultation_val").length == 1) {
    var cons_fee = '<?php echo $get_info->consulting_fee; ?>'; 
    var cons_val = cons_fee;
}else{
    var cons_fee = 0;
    var cons_val = 0;
}

var payment_status = '<?php echo $patient_payment_status->payment_status; ?>';

$(document).ready(function(){
    billing();    
});

function advanceCalculation(){
    var ProCount = $('.proDivs').length;
    var total = parseInt($('#ProCost').val());
    var conTotal = parseInt($('#registration_fee_tb').val())+parseInt($('#consultation_fee_tb').val());
    // if(ProCount == 0)
    // {
        // $('.advanceRow').hide();
        // $('.advance_check').prop("checked",false);

        // $('#minimumPayment').html(" ");     
    // }
    if(total == 0 || ProCount == 0)
    {
        $('.advanceRow').hide();
        $('.advance_check').prop("checked",false);
        $('#minimumPayment').html(" ");

        var toPayAmnt = 0;
        $('.advance_input').attr("readonly",true);
        $('.advance_input').val('0');
        $('#minimum_advance_line').hide();
        $('#minimumPayment').html("");
        $('.advance_input').val('0');
        $('.osb').val('0');
        $('.payNow').html("Rs."+(parseInt(conTotal)+(total-toPayAmnt))+"/-")
        $('.advance_status').html(' ');
        $('.advance_status').removeClass('inadequate');
        $('.advance_status').removeClass('adequate');  
    }
    else
    {
        if($('.advance_check').is(':checked') == true)
        {
            var minPay = parseInt($('.minimum_advance_payment').val());
            $('.advance_input').attr("readonly",false);
            $('#minimum_advance_line').show();
            var toPayAmnt = Math.round(((total*minPay)/100));
            $('#minimumPayment').html("Minimum Rs."+toPayAmnt+"/- To Be Paid.");
            $('.advance_input').attr("min",toPayAmnt);
            $('.advance_input').val(toPayAmnt);
            $('.osb').val(total-toPayAmnt);
            $('.payNow').html("Rs."+(parseInt(conTotal)+(toPayAmnt))+"/-")
            $('.advance_status').html('ADEQUATE');
            $('.advance_status').removeClass('inadequate');
            $('.advance_status').addClass('adequate');            
            $('.advance_input').attr("max",total);
        }
        else
        {
            var toPayAmnt = 0;
            $('.advance_input').attr("readonly",true);
            $('.advance_input').val('0');
            $('#minimum_advance_line').hide();
            $('#minimumPayment').html("");
            $('.advance_input').val('0');
            $('.osb').val('0');
            $('.payNow').html("Rs."+(parseInt(conTotal)+(total-toPayAmnt))+"/-")
            $('.advance_status').html(' ');
            $('.advance_status').removeClass('inadequate');
            $('.advance_status').removeClass('adequate');  
        }  
    }
    
}

function billing(){

    if($('#discount_tb').val() == '' || $('#discount_tb').val() < 0){
        var disc = 0;
    }else{
        var disc = $("#discount_tb").val();
    }

    var total = 0;
    var procedureTotal = 0;

    $("input[name='cart_prices[]']").each(function( index ) {
        var Price = $(this).val();
        procedureTotal = (procedureTotal + (parseFloat(Price)));
    });

    if(procedureTotal > 0){
        // If procedure added
        disFlagAmount = parseFloat(procedureTotal);
        $("#discountLine").show();
    }else{
        // If no procedure added
        disFlagAmount = parseInt($("#consultation_fee_tb").val()) + parseInt($("#registration_fee_tb").val());
        $("#discountLine").hide();
    }
    var conTotal = parseInt($("#consultation_fee_tb").val()) + parseInt($("#registration_fee_tb").val());

    total = parseInt($("#consultation_fee_tb").val()) + parseInt($("#registration_fee_tb").val()) + parseFloat(procedureTotal);

    $("#billing_amount_tb").val(total);

    $("#total_amt_td").html(total+' /-');

    if($("#discount_type_sb option:selected").val() == 'INR'){
        //var total = total - parseInt(disc);
        var discountedAmount = parseInt(disc);
    }else{
        //var total = total - (total * parseInt(disc)/100);
        var discountedAmount = disFlagAmount * parseInt(disc)/100
    }

    total = total - discountedAmount;
    $('#ProCost').val(total - (parseInt($("#consultation_fee_tb").val()) + parseInt($("#registration_fee_tb").val())));
    $("#net_pay").html(total+"/-");
    $('.payNow').html("Rs."+total+"/-");
    advanceCalculation();

}

function feePayment(){
    //check if both consultation & registration fee check boxes are checked
    if($("#consultation_cb").is(':checked') && $("#registration_cb").is(':checked')){
        $("input[id='free_cb']").prop("checked",false);
        // remove readonly attribute for discount textbox
        $("#discount_tb").removeAttr("readonly");
        // lable color to dark black
        $("#consultation_fee_lbl").css('color','#000');
        $("#consultation_fee_lbl").css('text-decoration','none');
        $("#registration_fee_lbl").css('color','#000');
        $("#registration_fee_lbl").css('text-decoration','none');

        $("#consultation_fee_tb").val($("#consultation_fee_lbl").html());
        $("#registration_fee_tb").val($("#registration_fee_lbl").html());
        billing();
    }else if($("#consultation_cb").is(':checked') || $("#registration_cb").is(':checked')){// if any one is checked
        // uncheck free checkbox
        $("input[id='free_cb']").prop("checked",false);
        // remove readonly attribute for discount textbox
        $("#discount_tb").removeAttr("readonly");

        if($("#consultation_cb").is(':checked')){ // if consultation fee 
        // label color to dark black
        $("#consultation_fee_lbl").css('color','#000');
        $("#consultation_fee_lbl").css('text-decoration','none');
        $("#consultation_fee_tb").val($("#consultation_fee_lbl").html());

        // turn the label color into light grey
        $("#registration_fee_lbl").css('color','#999');
        $("#registration_fee_lbl").css('text-decoration','line-through');
        // Make the textbox value to 0
        $("#registration_fee_tb").val(0);        

    }else if($("#registration_cb").is(':checked')){
        //alert("registration checked");
        // lable color to dark black
        $("#registration_fee_lbl").css('color','#000');
        $("#registration_fee_lbl").css('text-decoration','none');
        $("#registration_fee_tb").val($("#registration_fee_lbl").html());

        // turn the label color into light grey
        $("#consultation_fee_lbl").css('color','#999');
        $("#consultation_fee_lbl").css('text-decoration','line-through');
        // Make the textbox value to 0
        $("#consultation_fee_tb").val(0);        
    }

    billing();// call to billing function

}else{

    if($('#billing_amount_tb').val() == 0) { // if the billing amount is 0;
        $("input[id='free_cb']").prop("checked",true);		
    }

    // label color to light grey 
    $("#consultation_fee_lbl").css('color','#999');
    $("#consultation_fee_lbl").css('text-decoration','line-through');
    $("#registration_fee_lbl").css('color','#999');
    $("#registration_fee_lbl").css('text-decoration','line-through');

    // make the both fee to 0
    $("#consultation_fee_tb").val('0');
    $("#registration_fee_tb").val('0');

    // call to billing function
    billing(); 
    }
}

function delDiv(id){
    var procedure_id = $("#"+id).attr("data-id");
    $.post("<?=base_url('Patients/deletePatientProcedure')?>",{procedure_id:procedure_id},function(data){
        $("#"+id).remove();
        $('#discount_tb').val('0');
        billing(); // call to billing function
    });    
}

function addProcedure(procedure_id) {
    // get procedure name
    var procedureName = $("#procedure_select option:selected").text();
    
    // get procedure id
    var procedureId = $("#procedure_select option:selected").val();

    // get procedure amount
    var procedureAmount = $("#procedure_select option:selected").attr("data-val");

    if(procedureId != ""){
        var isAdded = checkExists(procedureId);
        if(isAdded == 0){
            var cartDiv = '<div class="row xtraItem proDivs" data-id="'+procedureId+'" id="'+procedureId+'_div">\n' + 
            '<div class="col-md-9"><input type="hidden" name="patient_procedure_id[]" value=""><input type="hidden" name="procedure_id[]" value="'+procedureId+'"><input type="hidden" class="cart-service-'+procedureId+'" name="cart_services[]" value="'+procedureName+'">'+procedureName+'</div>\n' +
            '<div class="col-md-2 text-right xtraPrice p-0"><input type="hidden" name="cart_prices[]" class="cart-price-'+procedureId+'" value="'+procedureAmount+'">'+procedureAmount+'.00</div>\n' +
            '<div class="col-md-1 text-left error delete-cart-row"><i class="fas fa-times-circle" onclick="return delDiv(\''+procedureId+'_div\');"></i></div>\n' + 
            '</div>';

            $("#cart-table").append(cartDiv);
            $('#procedure_select').val('').trigger("change");
            $('.advanceRow').show();

            billing(); // call to billing function

        }
    }
}

$(function(){
    $('#procedure_select').select2({
        theme: "bootstrap",
        allowClear: true,
        placeholder: "select procedure"
    });
});

function checkExists(pId) { 
    var isAdded = $(".cart-service-"+pId).length;
    console.log(isAdded);
    if(isAdded > 0){
        alert("procedure already added to list");
        $('#procedure_select').val('').trigger("change");
        return 1;
    }
    return 0;
}

function changeDiscount(){
    $("#discount_tb").val(0);
    billing();// call to billing function
}

function calcDiscount(){
    var total = $("#billing_amount_tb").val();
    var consultationAmnt = $('#consultation_fee_tb').val();
    var registrationAmnt = $('#registration_fee_tb').val();
    var value = $('#discount_tb').val();


    if(total == 0){
        return false;
    }

    if($("#discount_type_sb option:selected").val() == 'INR'){
        // if(parseInt(value)>(parseInt(total)-parseInt(consultationAmnt))) 
        // {
        //     // $("#discount_tb").val(parseInt(total));
        //      $("#discount_tb").val(parseInt(total)-(parseInt(consultationAmnt)+parseInt(registrationAmnt)));
        // }
        if(parseInt(value)>parseInt(total)) 
        {
             $("#discount_tb").val(parseInt(total));
        }
    }else{
        if(parseInt(value)>100) 
        {
            $("#discount_tb").val("100");
        }
    }

    $("#discount_tb").focus();

    // call to billing function
    billing();
}
</script>
</body>
</html>