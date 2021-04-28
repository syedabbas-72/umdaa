
  
       
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC-DOCTOR</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                          <?php echo form_open("calendar_blocking"); ?>
                         <div class="col-md-12">
                          
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME</label>
                                    <select name="clinic_name" id="clinic_name" class="form-control" required="" onchange="getDoctors(this.value)">
                                        <option>--select--</option>
                                          <?php foreach($clinic_list as $val){?>
                                            <option value="<?php echo $val->clinic_id; ?>"><?php echo $val->clinic_name; ?></option>
                                          <?php } ?>
                                        </select>
										
                                </div></div>
                                <div class="col-md-6" id="doctors_details">
                                            <label for="patient_name" class="col-form-label"></label>
                                        </div>
                                  <div class="col-md-6">
                  <h5><small>Select Date Range</small></h5>
                  <div class="form-group">
                    <div class="input-group m-b">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                      <input type="text" class="form-control date" name="blockdates" value="" />
                                        </div>
                  </div>
                </div>
                            </div> 
                           
                             
                            <div class="row col-md-12" id="submitBtn" style="margin-left:40%;margin-top: 2rem;">
                                  <input type="submit" class="btn btn-success" name="submit" value="Disable Schedule">
                                </div>

 
                                  
                              
                              </div>


                               
                               
                                <?php form_close(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </section> 

<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>
<script type="text/javascript">
$('.date').datepicker({
  format: 'yyyy-mm-dd',
    multidate: true
});
function getDoctors(clinic_id) {
        var url = "<?php echo base_url('Calendar_blocking/getDoctors'); ?>";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                clinic_id: clinic_id},
            beforeSend: function (data) {
                $('#doctors_details').html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            },
            success: function (data) {
              //  $("label[for='patient_name']").html(first_name);
                /*  $( "#patient_details" ).slideToggle( "slow", function() {
                 
                 });*/
                //$('#patient_details').toggle();
                $('#doctors_details').html(data);
                // $("#appointment_date").show(1000);
                // $("#get_schedule").show(1000);
                // $("#book_aptmnt").hide(1000);

            }
        });
    }

   function show_days(){
    var selected_doctor = $('#doctor_name :selected').val();
    var selected_clinic = $('#clinic_name :selected').val();
    $("#clinic_id").val(selected_clinic);
    $("#doctor_id").val(selected_doctor);
   
 $("#pick_time").show();
 $("#submitBtn").show();
 $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true,
          default: '12:00' 
    });
   }
//    $('input[name="days"]:checked').each(function() {
//    console.log(this.value);
// });

   function get_picker(id){
    if ($('input[name="days"]:checked').length == 0) {
      $('input[name="days"]:not(:checked)').removeAttr('disabled');
    }
    else {
      $('input[name="days"]:not(:checked)').attr('disabled', 'disabled');
    }
 
    var id = $('#checkbox'+id).attr("id");
    $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true,
          default: '12:00'
    });
    $("#pick_time").show();
    $("#submitBtn").show();

    
   }
   function picker_add(day_id,name,id){
     $("#add_"+id).attr("disabled","disabled");
    var next_row_id = parseInt(id)+1;
      $("#"+name+id).after('<div class="operationDayTimeContainer" id="'+name+next_row_id+'"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="text" class="form-control from_'+day_id+'[]" name="from_'+day_id+'_'+next_row_id+'[]" value="<?php echo date("h: i"); ?>"></div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="text" class="form-control to_'+day_id+'_'+next_row_id+'[]" name="to_'+day_id+'_'+next_row_id+'[]" value="<?php echo date("h: i"); ?>"></div><input type="hidden" name="total[]" value="'+day_id+'_'+next_row_id+'"><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px"><input type="button" id="add_'+next_row_id+'" class="btn btn-success" onclick= "picker_add(\'' + day_id  +'\',\'' + name  +'\',\'' + next_row_id  +'\')" value="+" style="padding: 2px;width: 100%"/></div><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true" style="padding: 6px"><input type="button" id="delete_'+next_row_id+'" class="btn btn-danger" onclick= "picker_delete(\'' + name  +'\',\'' + next_row_id  +'\')" value="-" style="padding: 2px;width: 100%" /></div></div></div>');
      $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true
    });
   }
   function picker_delete(name,id){
    $("#"+name+id).remove();
 $("#add_"+id).removeAttr("disabled");
   }

   $(document).on('click','#submit',function(){
 
       for(i=1;i<=7;i++){
        alert($(".from_"+i).val());
       }
    

   });
   $(function() {
        /* DataRange */
        $('input[name="daterange"]').daterangepicker();

      });
   

</script>
