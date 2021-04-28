// Checking appointment booking type (Walk in/Phone)
// smsCheckBox should be checked/unchecked by default based on the appointment booking type 
$('input[type=radio][name=btype]').change(function() {
    if (this.value == 'phone') {
        $("#priority_id").hide();
        $("#npSmsCB").prop("checked", true);
        $("#fpSmsCB").prop("checked", true);
    }else if (this.value == 'walkin') {
        $("#priority_id").show();
        $("#npSmsCB").prop("checked", false);
        $("#fpSmsCB").prop("checked", false);
    }
});

$(function () {
    var scrollTime = moment().format("HH:mm:ss");
    $('#calendar').fullCalendar({
        eventLimit: true,
        allDaySlot: false,
        defaultView: 'agendaDay',
        slotDuration: '00:05:00',
        nowIndicator: true,
        lazyFetching: true,
        scrollTime: scrollTime,
        slotLabelInterval: 5,
        agendaEventHeight:50,
        //hiddenDays: [ 0 ],
        minTime: '07:00:00',
        maxTime: '23:59:00',
        axisFormat: 'h:mm a',
        timeFormat: 'h:mm a',
        slotLabelFormat: 'h:mm a',
        slotMinutes: 5,
        displayEventTime: false,
        defaultDate: todayDate,
        views: {
            month: {
                eventLimit: 2
            },
            agendaWeek: {
                columnFormat: 'ddd D',
                eventLimit: 2 
            }
        },
        header: {
            left: '',
            center: 'prev title next today',
            right: 'agendaDay,agendaWeek,month'
        },

        viewRender: function (view, element) {

            var b = $('#calendar').fullCalendar('getDate');

            var month_id = b.month() +1;

            var d_list = $("#doctor_id_list").val();
            var c_id = $("#clinic_id").val();
            var d_id = $("#doctor_id").val();

            if(view.name == "month"){
                var id = $('.doctors-list li.selected').attr("id");
                if(id !='all'){
                    getevents(c_id,d_id,month_id,"","","");
                }else{
                    getevents(c_id,'all',month_id,"","","");
                    updateMonthCount(d_list,month_id);
                }
            }else if(view.name == "agendaWeek"){
                var id = $('.doctors-list li.selected').attr("id");
                if(id =='all'){
                    var beginOfWeek = moment(b).startOf('week');
                    var endOfWeek = moment(b).endOf('week');
                    var d_list = $("#doctor_id_list").val();
                    var start = beginOfWeek.format('YYYY-MM-DD');
                    var end = endOfWeek.format('YYYY-MM-DD');
                    getevents(clinic_id,'all',month_id,"",start,end);
                    updateWeekCount(d_list,start,end);

                }
            }else if(view.name == "agendaDay"){

                var id = $('.doctors-list li.selected').attr("id");
                if(id =='all'){
                    var date = b.format('YYYY-MM-DD');
                    var d_list = $("#doctor_id_list").val();
                    getevents(clinic_id,'all',month_id,date,"","");
                    updateDayCount(d_list,date);

                }
            }

            check_dynamic_slots1(b.format('YYYY-MM-DD'),'');
        },

        dayClick: function(date, jsEvent, view) {
            $('#procedure_div').hide();
            $("#app_info").html("");
            var dispval = $("#dispval").val();
            $("#newDiv").hide();
            $("#followUpDiv").hide();

            $("input[name=new]").prop("checked",false);
            $("input[name=followup]").prop("checked",false);
            $("input[name=sms]").prop("checked",false);

            $("#app_form").find("input[type='text']").not("input[name='app_date']").val("");

            var slot_tr = $("tr").attr("data-time");
            var selected_time = date.format("HH:mm");
            var selected_time1 = date.format("HH:mm:ss");

            $(".popover").popover("hide");
            var current_time = currentTime;

            var moment1 = date.format("YYYY-MM-DD");

            var d = new Date();

            var month = d.getMonth()+1;
            var day = d.getDate();

            var current = d.getFullYear() + '-' +
            (month<10 ? '0' : '') + month + '-' +
            (day<10 ? '0' : '') + day;
            $('.select_doctor').html("");
            if(dispval=='all')
            {
                var b = $('#calendar').fullCalendar('getDate');
                var time1 = b.format('YYYY-MM-DD');
                var selected_time = date.format("HH:mm");
                $("#time_slot").val(selected_time);
                $('#addModal').modal();
                $('#doctor_name').val("");
                $('#docNameErr').show();
                //$('#doctor_name').after('<em class="err help-block select_doctor">* Please Select Doctor</em>')

            }else{
                $('.select_doctor').html("");
                var d_id = $("#doctor_name").val();
                var b = $('#calendar').fullCalendar('getDate');
                var time1 = b.format('YYYY-MM-DD');
                var selected_time = date.format("HH:mm");
                $.ajax({
                    type: "POST",
                    url: base_url+'calendar_view/get_doctor_slots',
                    data:{'did':d_id,'date':time1,'time_slot':selected_time},
                    success: function(result){
                        if(result==1)
                        {
                            $('#addModal').modal('hide');
                        }
                        else
                        {
                            if(moment1 <= current){
                                if(selected_time1 < current_time){
                                    $('#addModal').modal('hide');
                                }else{
                                    $('#addModal').modal();
                                }
                            }else{
                                $('#addModal').modal();
                            }
                        }
                    }  
                });
                check_dynamic_slots(moment1,selected_time);
            }
            $('input[name="app_date"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate: moment(date),
                minDate:moment(date),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        },

        eventRender: function (eventObj, $el) {
            var b = $('#calendar').fullCalendar('getDate');
            var time1 = b.format('YYYY-MM-DD');

            $el.addClass(eventObj.slot1);

            if(eventObj.booking_type == 'walkin'){
                $el.find('.fc-title').prepend('<span class="glyphicon"><i class="fas fa-walking"></i></span> ');
            }else{
                $el.find('.fc-title').prepend('<span class="glyphicon"><i class="fa fa-phone"></i></span> ');
            }
        },
        eventClick: function (calEvent, jsEvent, view) {
            location.href= base_url+'profile/index/'+calEvent.app_patient_id+'/'+calEvent.id;
        },
        eventAfterAllRender: function (view) {
            var moment = $('#calendar').fullCalendar('getDate').format("YYYY-MM-DD");
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var current = d.getFullYear() + '-' +
            (month<10 ? '0' : '') + month + '-' +
            (day<10 ? '0' : '') + day;
            var quantity = $('.fc-event').length;
            var doctor_id = $("#doctor_id").val();
            if(doctor_id!="all"){
                $('.doctors-list li span.num-appoint').html("");
                $('.doctors-list li.selected span.num-appoint').html(quantity);
            }
            else{
                $("#all span.num-appoint").html(quantity);
            }
        },
    });
});

function blockCalendar(){
    $("#BlockModal").modal();
}

function getevents(c_id,d_id,month_id,date='',start='',end=''){
    $.ajax({
        method: "POST",

        url: base_url+'calendar_view/get_events',
        data:{ mid:month_id,cid:c_id, did:d_id,curdate:date,start:start,end:end},
        success: function(result)
        {
            result = $.trim(result);
            if(result!="NULL" || result!=""){ 
                var data = jQuery.parseJSON($.trim(result));
                // destroy the calendar
                $('#calendar').fullCalendar('removeEvents');
                $('#calendar').fullCalendar('addEventSource', data) ;
                $('#calendar').fullCalendar('rerenderEvents' );
            }

            if(d_id!='all'){
                $("#total_apnts").html("");
            }else{
                if(date!="" && start=="" && end==""){
                    var d_list = $("#doctor_id_list").val();
                    updateDayCount(d_list,date);
                }else if(date=="" && start!="" && end!=""){
                    var d_list = $("#doctor_id_list").val();
                    updateWeekCount(d_list,start,end);
                }else{
                    var d_list = $("#doctor_id_list").val();
                    updateMonthCount(d_list,month_id);
                }
            }
        }       
    });
    $(".appointment-loader").hide();
}


function updateMonthCount(d_list,month_id){
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/get_month_count',
        data:{ d_list:d_list,mid:month_id},
        success: function(result)
        {
            var data = jQuery.parseJSON(result);
            $.each(data, function(key, item) 
            {
                $("#"+item.id+"_cnt span.num-appoint").html(item.count);
            });
        }       
    });
}


function  check_dynamic_slots1(date,time='')
{

    var d_id = $("#doctor_name").val();

    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/dynamic_doctor_slots',
        data:{ did:d_id,date:date,time_slot:time},
        success: function(result){
            result = $.trim(result);
            var test = $.parseJSON(result);   
            $.each(test, function(k,v){

                var d = new Date();

                var month = d.getMonth()+1;
                var day = d.getDate();

                var current = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
                var selected_date = new Date(date);

                var s_month = selected_date.getMonth()+1;
                var s_day = selected_date.getDate();

                var moment = selected_date.getFullYear() + '-' + (month<10 ? '0' : '') + s_month + '-' + (s_day<10 ? '0' : '') + s_day;

                var current_time = currentTime;


                if(moment == current){
                    $(".fc-slats tr").each(function () {
                        if($(this).attr("data-time") < current_time){
                            $(this).css("background","#ebebeb");  
                            $(this).attr("readonly","readonly");
                            //$(this).css("cursor","not-allowed"); 
                            $(this).removeAttr("onclick");
                        }
                    });
                    $(".fc-slats tr[data-time='"+v+"']").css("background","white");  
                }else if(moment < current){
                    //console.log("test");
                    $(".fc-slats tr").each(function () {
                        $(this).css("background","#ebebeb");  
                        $(this).attr("readonly","readonly");
                    // $(this).css("cursor","not-allowed");   
                });
                }else if(moment > current){
                    $(".fc-slats tr").each(function () {
                        if($(this).attr("data-time") == v){
                            $(".fc-slats tr[data-time='"+v+"']").css("background","white");  

                        }
                    });
                }
            });
            $("#time_slot").html("");

            $.each(test, function(key, value) {
                var spli_time = value.split(":");   
                var new_time = spli_time[0]+":"+spli_time[1]; 

                if(new_time == time){
                    //alert(new_time);
                    $('select[name=time_slot]').append('<option selected>'+new_time+'</option>');
                }else{
                    $('select[name=time_slot]').append('<option>'+new_time+'</option>');
                }
            }); 
        }       
    });
}


function check_dynamic_slots(date,time=''){
    var d_id = $("#doctor_name").val();

    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/dynamic_doctor_slots',
        data:{ did:d_id,date:date,time_slot:time},
        success: function(result){
            result = $.trim(result);
            var test = $.parseJSON(result);

            $.each(test, function(k,v){

                var d = new Date();

                var month = d.getMonth()+1;
                var day = d.getDate();

                var current = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
                var selected_date = new Date(date);
                var s_month = selected_date.getMonth()+1;
                var s_day = selected_date.getDate();

                var moment = selected_date.getFullYear() + '-' + (month<10 ? '0' : '') + s_month + '-' + (s_day<10 ? '0' : '') + s_day;
                var current_time = currentTime;

                if(moment == current){
                    $(".fc-slats tr").each(function () {
                        if($(this).attr("data-time") < current_time){
                            $(this).css("background","#ebebeb");  
                            $(this).attr("readonly","readonly");

                        }

                    });
                    $(".fc-slats tr[data-time='"+v+"']").css("background","white");  
                }
                else if(moment < current){
                    $(".fc-slats tr").each(function () {
                        $(this).css("background","#ebebeb");  
                        $(this).attr("readonly","readonly");  
                    });
                }
                else if(moment > current){
                    $(".fc-slats tr").each(function () {
                        if($(this).attr("data-time") == v){
                            $(".fc-slats tr[data-time='"+v+"']").css("background","white");  

                        }


                    });


                }

            });
            $("#time_slot").html("");
            $.each(test, function(key, value) {
                var spli_time = value.split(":");   
                var new_time = spli_time[0]+":"+spli_time[1]; 

                if(new_time == time){
                    $('select[name=time_slot]').append('<option selected>'+new_time+'</option>');
                }
                else{
                    $('select[name=time_slot]').append('<option>'+new_time+'</option>');
                }

            }); 


        }       


    });
}

function check_doctor_slots(date,time=''){
    var d_id = $("#doctor_name").val();

    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/dynamic_doctor_slots',
        data:{ did:d_id,date:date,time_slot:time},
        success: function(result){
            result=$.trim(result);
            $("#time_slot").html("");
            var test = $.parseJSON(result); 
            $.each(test, function(key, value) {
                var spli_time = value.split(":");   
                var new_time = spli_time[0]+":"+spli_time[1]; 

                if(new_time == time){
                    $('select[name=time_slot]').append('<option selected>'+new_time+'</option>');
                }
                else{

                    if(new_time < cTime){

                        $('select[name=time_slot]').append('<option disabled>'+new_time+'</option>');
                    }
                    else{

                        $('select[name=time_slot]').append('<option>'+new_time+'</option>');
                    }

                }

            }); 


        }       


    });
}

function check_patient_mobile()
{
    var chk_length = $("#nmobile").val().length;
    var mobile = $("#nmobile").val();
    if(chk_length  == 10){
        $.ajax({
            type: "POST",
            url: base_url+'calendar_view/check_patient_mobile',
            data:{ mobile:mobile},
            success: function(result)
            {
                result= $.trim(result);
                var split_result = result.split(":");
                if(split_result[1] == "Yes"){
                    $("#napp_info").html('<div class="row col-md-12"><div class="col-md-12"><span style="color:red;font-size:14px; font-weight:bold;padding10px;">Patient Exist with mobile Number. Family member?</span></div><div class="col-md-6"><div class="form-group has-success"><label for="city-code">Relative Name<span class="color-red">*</span></label><input class="form-control" id="relative_name" name="relative_name" value="" type="text" required=""></div></div><div class="col-md-6"><div class="form-group"><label for="city-code">Relation With Patient<span class="color-red">*</span></label><input class="form-control" id="relation" name="relation" value="" type="text" required=""></div></div></div>');
                    $("#npname").val(split_result[0]);
                }
                else{
                    $("#napp_info").html('<input class="form-control"  id="relation" name="relation" value="norelation" type="hidden">');
                    $("#npname").val("");
                    $("#addSubmit").show();
                }
            }       


        });
    }
    else{
        $("#napp_info").html("");
        $("#npname").val("");
    }
}

$( function() {

// Single Select
$("#mobile").autocomplete
({
    source: function (request, response)
    {   
//Pass the selected country to the php  to limit the stateProvince selection to just that country
$.ajax(
{
    url: base_url+"calendar_view/confirm_mobile",
    data: {
        mobile: request.term, 
//Pass the selected countryAbbreviation
},
type: "POST",  // POST transmits in querystring format (key=value&key1=value1) in utf-8
success: function (data)
{

    if($.trim(data) == "no"){
        $("#app_info").html("<div class='alert alert-danger'><small><div class='text-muted'></div><b>No results Found. Check your number</b></small></div>");
        $("#id").val("");
        $("#pname").val("");
        $("#mobile").val("");
        $("#umr").val("");
        $("#submit").hide();
    }
    else{
        $("#submit").show();
        $("#app_info").html("");
        response($.parseJSON($.trim(data)));
    }
}
});
},
select: function (event, ui)
{   
    $("#id").val(ui.item.key);
    $("#pname").val(ui.item.mobile);
    $("#mobile").val(ui.item.value);
    $("#umr").val(ui.item.label);
    checkappointments(ui.item.key);
},
create: function () {
    $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
        .append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoPic"><img src="'+base_url+'/assets/img/default-avatar-user.png"></td><td class="infoDiv"><h1>'+item.mobile+'<br><span><strong>PID:</strong> '+item.label+'</h1><p><strong>M: </strong>'+item.value+'</p></td></tr></table></div></a>')
        .appendTo(ul);
    };
},
minLength: 3
});
});


$( function() {

// Single Select
$("#umr").autocomplete
({
    source: function (request, response)
    {   
        $.ajax(
        {
            url: base_url+"calendar_view/search_umr",
            data: {
                umr: request.term, 

            },
            type: "POST",  
            success: function (data)
            {

                if($.trim(data) == "no"){
                    $("#app_info").html("<div class='alert alert-danger'><small><div class='text-muted'></div><b>No results Found.</b></small></div>");
                    $("#id").val("");
                    $("#pname").val("");
                    $("#mobile").val("");
                    $("#umr").val("");
                    $("#app_info").html("");
                    $("#submit").hide();
                }
                else{
                    $("#submit").show();
                    $("#app_info").html("");
                    response($.parseJSON($.trim(data)));
                }
            }
        });
    },
    select: function (event, ui)
    {   
        $("#id").val(ui.item.key);
        $("#pname").val(ui.item.mobile);
        $("#mobile").val(ui.item.value);
        $("#umr").val(ui.item.label);
        checkappointments(ui.item.key);
    },
    create: function () {
        $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $('<li>')
            .append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoPic"><img src="'+base_url+'/assets/img/default-avatar-user.png"></td><td class="infoDiv"><h1>'+item.mobile+'<br><span><strong>PID:</strong> '+item.label+'</h1><p><strong>M: </strong>'+item.value+'</p></td></tr></table></div></a>')
            .appendTo(ul);
        };
    },
    minLength: 3
});
});

function getdetails(c_id,d_id){
    $("#dispval").val(d_id);
    $("#clinic_id").val(c_id);
    $("#doctor_id").val(d_id);
    $("#doctor_name").val(d_id);
    $("#ndoctor").val(d_id);
    $(".doctors-list li").removeClass("selected");
    if(d_id == 'all'){
        $("#all").addClass("selected");

    }
    else{
        $("#"+d_id+"_cnt").addClass("selected");
        $("#total_apnts").html("");
    }

    var view = $('#calendar').fullCalendar('getView');
    var b = $('#calendar').fullCalendar('getDate');
    var month_id = b.month() +1;

    var d_list = $("#doctor_id_list").val();

    if(view.name == "month"){

        if(d_id !='all'){
            getevents(c_id,d_id,month_id,"","","");
        }
        else{
            getevents(clinic_id,'all',month_id,"","","");
            updateMonthCount(d_list,month_id);
        }
    }
    else if(view.name == "agendaWeek"){

        if(did =='all'){
            var beginOfWeek = moment(b).startOf('week');

            var endOfWeek = moment(b).endOf('week');
            var d_list = $("#doctor_id_list").val();
            var start = beginOfWeek.format('YYYY-MM-DD');
            var end = endOfWeek.format('YYYY-MM-DD');
            getevents(clinic_id,'all',month_id,"",start,end);
            updateWeekCount(d_list,start,end);
        }
    }
    else if(view.name == "agendaDay"){
        var id = d_id;
        if(d_id =='all'){
            var date = b.format('YYYY-MM-DD');
            var d_list = $("#doctor_id_list").val();
            getevents(clinic_id,'all',month_id,date,"","");
            updateDayCount(d_list,date);
        }
        else{
            getevents(c_id,d_id,month_id,"","","");
        }
    }
    check_dynamic_slots(b.format('YYYY-MM-DD'),'');
}


function updateWeekCount(d_list,start,end){

    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/get_week_count',
        data:{ d_list:d_list,start:start,end:end},
        success: function(result)
        {
            var data = jQuery.parseJSON(result);
            $.each(data, function(key, item) 
            {
                $("#"+item.id+"_cnt span.num-appoint").html(item.count);
            });
        }       
    });
}


$(document).on("click",".checkin",function(){
    var app_id = $(this).attr("id");
    var pid = $(this).attr("pid");
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/check_in',
        data:{ appid:app_id},
        success: function(result)
        {
            location.href = base_url+'calendar_view'
        }       
    });
});


$(document).on("click",".change_priority",function(){
    var id=$(this).attr("id");
    var val = $("#select_priority option:selected").val();
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/change_priority',
        data:{ appid:id,priority:val},
        success: function(result)
        {
            $("#ps_msg").show();
            setTimeout(function() {
                $("#ps_msg").hide('blind', {}, 500)
            }, 2000);
        }       
    });
});


$(document).on("click",".drop",function(){
    var app_id = $(this).attr("id");
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/drop_app',
        data:{ appid:app_id},
        success: function(result)
        {
            location.href= base_url+'calendar_view';
        }       
    });
});


$(document).on("click",".close_app",function(){
    var app_id = $(this).attr("id");
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/close_app',
        data:{ appid:app_id},
        success: function(result)
        {
            location.href= base_url+'calendar_view';
        }       
    });
});


function updateDayCount(d_list,date){
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/get_day_count',
        data:{ d_list:d_list,curdate:date},
        success: function(result)
        {
            var data = jQuery.parseJSON(result);
            $.each(data, function(key, item) 
            {
                $("#"+item.id+"_cnt span.num-appoint").html(item.count);
            });
        }       
    });
}


function checkappointments(p_id){
    var d_id = $('#doctor_name option:selected').val();
    var date = $("#app_date").val();
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/get_appointments',
        data:{ pid:p_id,did:d_id,date:date},
        success: function(result)
        {
            //  console.log(result);
            $("#app_info").html(result);
        }       
    });
}

function checkslots(){

    var d_id = $("#doctor_name").val();

    var date = $("#app_date").val();
    var time = $("#time_slot").val()+":00";

    if(d_id == ""){
        d_id = $('#doctor_name option:selected').val();
    }
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/checkslots',
        data:{ did:d_id,date:date,time_slot:time},
        success: function(result)
        {
            if(result!='')
            {
                $("#app_info").empty();
                $("#app_info").append(result);
            }
        }       
    });
}


$(function() {
    $('input[name="app_date"]').change(function () {
        var date = $('input[name="app_date"]').val();
        $("#app_info").html("");
    });
});


// Patient type is two types (New/Followup)         
$(document).on("click",".ptype",function(){ // bind a function to the change event
    $("#app_form").find("input[type='text']").not("input[name='app_date']").not("input[id='nppname']").not("input[id='npmobile']").val("");
    $('#procedure_div').show();  
    $('.ptype').not(this).removeClass('btn-default active');
    $(this).addClass('btn-default active');
    var val = $(this).attr("id"); // Retrieve the value
    if(val == "new"){
        $("#app_info").html("");
        $("#newDiv").show(); // Show new div    
        $("#addSubmit").show();
        $("#followUpDiv").hide(); // hide follow up div        
        $("#npname").val("");    
        $("#submit").hide();
    }else if(val == "followup"){
        $("#app_info").html("");  
        $("#newDiv").hide(); // Hide new div    
        $("#followUpDiv").show(); // Show follow up div
        $("#submit").show();
        $("#npname").val("");          
        $("#addSubmit").hide();
    }

    // sms reminder
    // Check what type of booking is it
    var bookingType = $("input[name='btype']:checked"). val()

    // Uncheck if the booking type is walk in 
    // Check if the booking type is Phone 
    if(bookingType == "phone") {
        $("#npSmsCB").prop("checked", true);
        $("#fpSmsCB").prop("checked", true);
    }else{
        $("#npSmsCB").prop("checked", false);
        $("#fpSmsCB").prop("checked", false);
    }
});


function referStatus(val) {
    if (val == "WOM") {
        $("#WOM").show(1000);
        //$("#referred_by_person").prop("required",true);
        $("#Doctor").hide(1000);
        //$("#doctor_name").prop("required",false);
        $("#Online").hide(1000);
        //$("#online_sb").prop("required",false);
    } else if(val=='Doctor'){
        $("#WOM").hide(1000);
        //$("#referred_by_person").prop("required",false);
        $("#Doctor").show(1000);
        //$("#doctor_name").prop("required",true);
        $("#Online").hide(1000);
        //$("#online_sb").prop("required",false);
    }else if(val=='Online'){
        $("#WOM").hide(1000);
        //$("#referred_by_person").prop("required",false);
        $("#Doctor").hide(1000);
        //$("#doctor_name").prop("required",false);
        $("#Online").show(1000);
        //$("#online_sb").prop("required",true);
    }else{
        $("#WOM").hide(1000);
        $("#Doctor").hide(1000);

        $("#Online").hide(1000);
    }
}

// New Patient On Click Event   
$(document).on("click","#addSubmit",function(){ 

    if($("#doctor_name").val() == "" || $("#doctor_name").val() == null ){
        alert("please select doctor");
        return false;
    }

    var procedures = $("#procedures").val();
    var date = $('input[name="app_date"]').val();
    var d_id = $('#doctor_name option:selected').val();
    var time_slot = $('#time_slot option:selected').val();
    var priority = $('input[name="priority"]:checked').val();
    var sms = $('input[name="sms"]:checked').length;
    var mobile = $('input[name="nmobile"]').val();
    var pname = $('input[name="npname"]').val();

    if(pname == ""){
        alert("enter patient name");
        return false;
    }

    if(mobile == ""){
        alert("enter mobile number");
        return false;
    }

    var btype = $('input[name="btype"]:checked').val();
    var rbt = $('#referred_by_type').val();
    var rbp = $('#referred_by_person').val();
    var rbd = $('#referred_by_doctor').val();
    var rbo = $('#online_sb').val();
    var relation = $('#relation').val();
    var relative_name = $('#relative_name').val();
    var nrd_name = $('#ref_doctor_name').val();
    var nrd_mobile = $('#ref_doctor_mobile').val();
    var nrd_location = $('#ref_doctor_location').val();

    //alert(btype);

    if(sms == 1){
        sms = "yes";
    }else{
        sms = "no";
    }

    $('#addModal').modal('hide');
    $(".appointment-loader").show();
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/patient_add_save',
        data:{
            d_id:d_id,
            date:date,
            slot:time_slot,
            priority:priority,
            sms:sms,
            pname:pname,
            mobile:mobile,
            btype:btype,
            rbp:rbp,
            rbt:rbt,
            rbd:rbd,
            rbo:rbo,
            relation:relation,
            relative_name:relative_name,
            nrd_name:nrd_name,
            nrd_mobile:nrd_mobile,
            nrd_location:nrd_location,
            procedures:procedures
        },
        success: function(result) {

            result = $.trim(result);
            var split_result = result.split(":");

            var patient_id = split_result[0];
            var appointment_id = split_result[1];

            // alert('Patient ID: '+patient_id);
            // alert('Appointment ID: '+appointment_id);

            // Hide the loader
            $(".appointment-loader").hide();

            if(btype == "walkin"){                
                location.href= base_url+'patients/patient_update/'+patient_id+'/'+appointment_id;
            }else{
                location.href= base_url+'calendar_view';
                // location.href= base_url+'profile/index/'+result;
            }            
        }                   
    });
});


// Follow up Patient Booking Appointment
$(document).on("click","#submit",function(){ 

    if($("#doctor_name").val() == "" || $("#doctor_name").val() == null ){
        alert("please select doctor");
        return false;
    }

    $('#addModal').modal('hide');
    $(".appointment-loader").show();

    var date = $('input[name="app_date"]').val();
    var d_id = $('#doctor_name option:selected').val();
    var patient_id = $('#id').val();
    var umr = $('#umr').val();
    var time_slot = $('#time_slot option:selected').val();
    var priority = $('input[name="priority"]:checked').val();
    var sms = $('input[name="sms"]:checked').length;
    var mobile = $('input[name="nmobile"]').val();
    var btype = $('input[name="btype"]:checked').val();
    var procedures = $("#procedures").val();

    if(sms == 1){
        sms = "yes";
    }
    else{
        sms = "no";
    }
    
    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/book_appointment',
        data:{ 
            d_id:d_id,
            date:date,
            slot:time_slot,
            priority:priority,
            sms:sms,
            p_id:patient_id,
            mobile:mobile,
            umr:umr,
            btype:btype,
            procedures:procedures
        },
        success: function(result)
        {
            // alert(result);
            // exit();
            result = $.trim(result);

            if(result == 'existing'){
                alert("An open appointment already exist with the doctor on the choosen date. You can reschedule the appointment. Thank you.");
            }else{
                // alert(result);
                // exit();
                result = $.trim(result);
                var split_result = result.split(":");

                var appointment_id = split_result[0];
                var payment = split_result[1];

                // alert("Appointment id: "+appointment_id);
                // alert("Payment: "+payment);

                if(result){
                    //appointment_id = result;
                    if(btype == 'walkin'){
                        if(payment == 1){
                            location.href= base_url+'patients/confirm_payment/'+patient_id+'/'+appointment_id;    
                        }else if(payment == 0){
                            location.href= base_url+'profile/index/'+patient_id+'/'+appointment_id;    
                        }                    
                    }else{ // By a phone Redirect back to the Calendar
                        location.href= base_url+'calendar_view';
                    }
                }else{
                    location.href= base_url+'calendar_view';
                }
            }
        }       
    });
});


function get_doctor(id){
    var time_slot = $('#time_slot option:selected').val();
    var date = $('input[name="app_date"]').val();
    //$(".select_doctor").html("");
    $('#docNameErr').hide();
    $("#doctor_slot").val(id);
    // $("#"+id+"_cnt").click();
    // console.log(time_slot);

    // console.log(date);
    //checkslots(date,'');
    check_doctor_slots(date,time_slot);
}

// Reschedule Button Click Event
// $(document).on("click",".reschedule",function(){
//     $("#rescheduleModal").modal();
//     $('input[id="res_date"]').daterangepicker({
//             singleDatePicker: true,
//             startDate: moment(),
//             minDate:moment(),
//             locale: {
//                 format: 'DD-MM-YYYY'
//             }
//         }, function(start, end, label) {
//         // console.log('New date range selected: ' + start.format('dd-MM-YYYY'));
//         var sel_date = start.format('dd-MM-YYYY');
//         //var sel_date = document.getElementById("solo_date").value;
//         var doctor_id = $('#doctor_name option:selected').val();
//         //$("#solo_date").val('test');
//         //  console.log(sel_date);
//         check_res_slots(doctor_id,sel_date);
//         return false;
//     });
// });

// Reschedule Button Click Event
function rescheduleModal(app_id) {
    $("#rescheduleModal"+app_id).modal();
    $('input[name="date"]').daterangepicker({
        singleDatePicker: true,
        startDate: moment(),
        minDate:moment(),
        locale: {
            format: 'DD-MM-YYYY'
        }
    }, function(start, end, label) {
        // console.log('New date range selected: ' + start.format('dd-MM-YYYY'));
        var sel_date = start.format('DD-MM-YYYY');
        //var sel_date = document.getElementById("solo_date").value;
        var doctor_id = $('#doctor_id'+app_id).val();
        //$("#solo_date").val('test');
        //  console.log(sel_date);
        check_res_slots(doctor_id,sel_date,app_id);
        return false;
    });
}


$(document).on("click","#patient_add",function(){
    var date = $('input[name="app_date"]').val();
    var d_id = $('#doctor_name option:selected').val();
    var time_slot = $('#time_slot option:selected').val();
    var priority = $('input[name="priority"]:checked').val();
    var rbt = $('input[name="referred_by_type"]').val();
    var rb = $('input[name="referred_by"]').val();
    var rbd = $('input[name="referred_by_doctor"]').val();

    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/set_appointment_info',
        data:{ d_id:d_id,date:date,slot:time_slot,priority:priority,rb:rb,rbt:rbt,rbd:rbd},
        success: function(result)
        {
            location.href= base_url+'patients/patient_add';
        }       
    });
});


function show_selected_priority(val){
    $('#select_priority option').filter(function() { 
        return ($(this).val() == val); 
    }).prop('selected', true);
}


function check_ref_doctor(val){
    if(val == "others"){
        $("#new_ref_doctor_div").show();
    }else{
        $("#ref_doctor_name").val("");
        $("#ref_doctor_mobile").val("");
        $("#ref_doctor_location").val("");
        $("#new_ref_doctor_div").hide();
    }
}


$(document).ready(function(){
    $("#procedures").select2({
        multiple:true,
        width: '100%',
        placeholder: "Add Procedure"
    });
    $("#procedures").val(null).trigger("change");
});


$(document).ready(function(){
    $("#referred_by_doctor").select2({
        placeholder: "Select doctor"
    });
    $("#referred_by_doctor").val(null).trigger("change");
});


function checknull(val){
    if(val==""){
        $("#id").val("");
        $("#pname").val("");
        $("#mobile").val("");
        $("#umr").val("");
        $("#submit").hide();
        $("#app_info").html("");
    }
}

// Checking Available Doctor Time Slots
function check_res_slots(d_id,date,app_id = null){

    var current_time = currentTime;

    $.ajax({
        type: "POST",
        url: base_url+'calendar_view/dynamic_doctor_slots',
        data:{did:d_id,date:date},
        success: function(result){
            result = $.trim(result);
            if(result == "no"){
                var slot_html = "<option value=''>NA</option>";
                $("#slots"+app_id).html(result);
            }else{
                $("#slots"+app_id).html("");
                var test = $.parseJSON(result); 
                $.each(test, function(key, value) {
                    var spli_time = value.split(":");   
                    var new_time = spli_time[0]+":"+spli_time[1]; 
                    $('select[name=slots]').append('<option>'+new_time+'</option>');
                }); 
            }
        }                    
    });
}


function capitalize(id){
    // alert('Hi');
    $("#"+id).css('text-transform','capitalize');
}