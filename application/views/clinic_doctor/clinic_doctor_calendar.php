
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.css" type="text/css" rel="stylesheet" />

    <style>.ui-dialog-title {background-image:url(http://image005.flaticon.com/1/svg/109/109978.svg);background-repeat:no-repeat;padding-left:25px;}</style>
<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                <div id="bootstrapModalFullCalendar"></div>
            </div>
        </div>
    </div>
  </div>
  <div id="eventContent" title="Event Details">
        <div id="eventInfo"></div>
      
    </div>
</section>

    <script>
        $(document).ready(function() {
            $('#bootstrapModalFullCalendar').fullCalendar({
                header: {
                    left: '',
                    center: 'prev title next',
                    right: ''
                },
                eventClick: function (event, jsEvent, view) {
                    //set the values and open the modal
                    $("#eventInfo").html(event.description);
                    $("#eventLink").attr('href', event.url);
                    $("#eventContent").dialog({
                        modal: true,
                        title: event.title
                    });
                    return false;
                },
                 dayClick: function(date, allDay, jsEvent, view) {
                   $("#eventInfo").html("");
                  var day_id = new Date(date).getDay();
                   if(day_id == 0){

                    <?php foreach ($sun as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                  else if(day_id == 1){

                    <?php foreach ($mon as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                   else if(day_id == 2){

                    <?php foreach ($tue as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                   else if(day_id == 3){

                    <?php foreach ($wed as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                   else if(day_id == 4){

                    <?php foreach ($thr as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                   else if(day_id == 5){

                    <?php foreach ($fri as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                  else if(day_id == 6){

                    <?php foreach ($sat as $key => $row) { 


                      $time_slot = '<p id = "'.$row['clinic_doctor_weekday_slot_id'].'">'.date('H A',strtotime($row['from_time'])).' - '.date('H A',strtotime($row['to_time'])).'</p>';
                    
                      ?>
                      var day_id = new Date(date).getDay();

                      var slot = '<?php echo $time_slot; ?>';
                      console.log(slot);
                      $("#eventInfo").append(slot);
                   

                   <?php  } ?>
                   }
                   if($("#eventInfo").html() ==""){
                     $("#eventInfo").append("<p>No Schedules On This Day</p>");
                }
                    $("#eventContent").dialog({
                        modal: true,
                        title: "Schedule"
                    });
                    return false;
                   
                    
    },
       dayRender: function(date, cell) {

        var d = new Date(date);
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    if (day < 10) {
        day = "0" + day;
    }
    if (month < 10) {
        month = "0" + month;
    }
    var real_date = year + "-" + month + "-" + day;
            
              <?php foreach ($cal_block as $key => $value) { 


                ?>

                var from = '<?php echo $value['from_date']; ?>';
                var to = '<?php echo $value['to_date']; ?>';



               if (real_date >= from && real_date <= to) {
                    cell.css("background", "#e8e8e8");
                    cell.css("cursor", "not-allowed");
                    
     }
     $( ".fc-day-number" ).each(function() {
  if ($(this).attr("data-date") >= from && $(this).attr("data-date") <= to) {
                   
                 $(this).css("cursor","not-allowed");
     }
});
             
              

             <?php }

              ?>
                
}
            });
        });
    </script>
</body>
</html>