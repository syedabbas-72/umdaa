<div class="row page-header no-background no-shadow margin-b-0">
    <div class="col-lg-6 align-self-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
        <li class="breadcrumb-item">PATIENT</li>
        <li class="breadcrumb-item active">CASE SHEET</li>
      </ol>
    </div>
</div>
<section class="main-content">
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
             
             <div class="row col-md-12"> 
       
                <div class="col-md-3" id="view_casesheet"></div>
                <div class="col-md-9" id="view_caseresults" class="view_caseresults"></div>

             </div>
           </div>
        </div>
    </div>
</div>

<style type="text/css">
ul {
  list-style-type: none;
}
.table tbody tr:hover td,
.table tbody tr:hover th {
  background-color: transparent;
}


.table-striped tbody tr:nth-child(odd):hover td {
   background-color: #F9F9F9;
}
.ulgroup{padding:3px;border:1px solid #ccc;}
.ligroup{ padding: 3px; margin: 2px; background-color: #f7f8f9; font-weight: 600; cursor: pointer;color: rgb(87, 89, 98);}
.view_caseresults{font-family: segoe ui;font-size: 16px;font-weight: 300;}
.whitebg{padding: 3px; margin: 2px; background-color: #ccc; cursor: pointer;color: rgb(87, 89, 98);}
</style>
</section>
<script type="text/javascript">

getPatientDetails(<?php echo $patient_id?>)
//getPatientInfo('Vitals',<?php echo $clinic_id;?>,<?php echo $patient_id;?>,<?php echo $appointment_id;?>);
getvitals(<?php echo $patient_id;?>,<?php echo $appointment_id;?>);

 // function getClinicPatients(id){
   // alert();
    $('#view_casesheet').empty();
  $('#view_caseresults').empty()
  var url = "<?php echo  base_url('CaseSheet/getClinicPatients'); ?>";
 
$.ajax({
type : 'POST',
url : url,
data: {
clinic_id:<?php echo $clinic_id;?>},
beforeSend:function(data){ 
 
 $('#clinic_patients').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
},success:function (data) {
  
 $('#clinic_patients').html(data); 
 
 }
});
//}
function getPatientDetails(patient_id){
  $('#view_casesheet').empty();
  $('#view_caseresults').empty()
  if(patient_id!=''){
var url = "<?php echo  base_url('CaseSheet/getPatientDetails'); ?>";
 var clinic_id = $("#clinic_id").val();
$.ajax({
type : 'POST',
url : url,
data: {
clinic_id:clinic_id,
appointment_id:<?php echo $appointment_id;?>,
patient_id:<?php echo $patient_id?>},
beforeSend:function(data){ 
 
 $('#view_casesheet').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
},success:function (data) {
  
 $('#view_casesheet').html(data); 
 }
});
}
}
function getPatientInfo(name,clinic_id,patient_id,appointment_id){
   $('.ligroup').removeClass('whitebg');
  $('#'+name).addClass('whitebg');
  if(name=="SUMMARY"){
      var url = "<?php echo  base_url('SummaryReports/getAppointments'); ?>";
     var clinic_id = $("#clinic_id").val();
    $.ajax({
    type : 'POST',
    url : url,
    data: {
    clinic_id:clinic_id,
    patient_id:patient_id,
  appointment_id:appointment_id
  },
    beforeSend:function(data){ 
     
     $('#view_caseresults').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
    },success:function (data) {
      
     $('#view_caseresults').html(data); 
     }
    });
  }else{
var url = "<?php echo  base_url('CaseSheet/getPatientInfo'); ?>";
$.ajax({
  type : 'POST',
  url : url,
  data: {
    name:name,
  clinic_id:clinic_id,
    patient_id:patient_id,
  appointment_id:appointment_id
  },
  beforeSend:function(data){ 
   
   $('#view_caseresults').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
  },success:function (data) {
    
   $('#view_caseresults').html(data); 
   }
});
}
}

function getConsentForms(department_id,appointment_id){
  $('#conForm').empty();
  $('#conForm').empty()
  if(patient_id!=''){
var url = "<?php echo  base_url('CaseSheet/getConsentForms'); ?>";
 
$.ajax({
type : 'POST',
url : url,
data: {
department_id:department_id,
appointment_id:appointment_id},
beforeSend:function(data){ 
 
 $('#conForm').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
},success:function (data) {
  
 $('#conForm').html(data); 
 }
});
}
}

function getvitals(patient_id,appointment_id){
  $('#view_caseresults').empty();
var url = "<?php echo  base_url('CaseSheet/vital_add'); ?>";
  
      $.ajax({
        url: url,
        type : 'POST',
        data:{
           patient_id : patient_id,
           appointment_id : appointment_id
         },
           success:function(data){
              $('#view_caseresults').html(data);
           }
      });
}

function download_consent(consent)
{
  
  var url = "<?php echo  base_url('Consentform/download_consent'); ?>";
 var base_url = "<?php echo  base_url(); ?>uploads/consentforms/";
$.ajax({
type : 'POST',
url : url,
data: {
consent:consent},success:function (data) {
  
  //$("#cf").append('<a href="'+base_url+data.trim()+'" id="ca" download>a</a>');

 }
});
}


$(document).on('click','#vital_add',function(){

  
  $('#view_caseresults').empty();
 
     var url = "<?php echo  base_url('CaseSheet/vital_add'); ?>";
     var value = $(this).attr('value');
      var arr = value.split('/');
      $.ajax({
        url: url,
        type : 'POST',
        data:{
           patient_id : arr[0],
           appointment_id : arr[1]
         },
           success:function(data){
              $('#view_caseresults').html(data);
           }
      });
});

$(document).on('click','#vital_edit',function(){

  
  $('#view_caseresults').empty();
 
     var url = "<?php echo  base_url('CaseSheet/vital_edit'); ?>";
     var value = $(this).attr('value');
      var arr = value.split('/');
      $.ajax({
        url: url,
        type : 'POST',
        data:{
           patient_id : arr[0],
           appointment_id : arr[1]
         },
           success:function(data){
              $('#view_caseresults').html(data);
           }
      });
});
</script>
<script type="text/javascript">
   function add_vital(){
     var id = 'vital';
    
    var count=$("#"+id).find('tr').length;  
    $("#"+id).append('<tr id="'+id+'_'+count+'"><td class="vital_row"><input type="text" name="vital_sign[]" style="width:200px;float:left;border-top:0px;border-left:0px;border-right:0px;" class="inline">&nbsp;&nbsp;<span class="inline" style=" display:inline-block;">:</span></td><td><input type="text" name="vital_sign_val[]" class="vital_txt" style="width:200px;border-top:0px;border-left:0px;border-right:0px;"></td><td style="text-align: center;"><button type="button" class="btn btn-success" onclick=add_vital("'+id+'");>+</button><button type="button" class="btn btn-danger" onclick=del_vital("'+id+'_'+count+'");>-</button></td></tr>');

  }
   function del_vital(id){
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

 </script>
 <script type="text/javascript">
   
 </script>
 <script type="text/javascript">
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
   
        
  
 </script>
 <script type="text/javascript">
  $(document).on('click','#back',function(){
    alert('working');
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
        
        }
        else{
          $("#input-allergy").hide();

        }
      }
    });
    </script>
<script type="text/javascript">

function bp(){
  var sbp = $("#SBP").val();
  var dbp = $("#DBP").val();
  $("#BP").val(sbp+"/"+dbp);
}

function validate()
{
  // check if the radio buttons exists
  // if exists perform validation
  if (jQuery('input[type=radio]', '#radio12').length || jQuery('input[type=radio]', '#radio13').length) {
    if ($("#radio12").is(":checked")) {
      var alergy = $("#input-allergy").val();
      // Radio yes is choosen then perform validation to check whether allergy is specified or no
      if(alergy=='')
      {
        alert("Please specify the drug name you are allergic to");
        return false;
      }  
    }else{
      alert("Do you have a drug allergy? Please choose either yes/no");
      return false;
    }   
  } 
}
</script>