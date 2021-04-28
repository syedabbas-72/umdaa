   <link href="<?php echo base_url('assets/lib/sweet-alerts2/sweetalert2.min.css');?>" rel="stylesheet">
   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">CLINIC LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('Clinic/clinic_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>&emsp;
          <a href = ""  class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">BULK UPLOAD</a>
        </div>
    </div>

<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="clinic_list" class="table customTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>CLINIC NAME</th>
                                        <!-- <th>CLINIC LOGO</th> -->
                                        <th>CLINIC TYPE</th>
                                        <th>CLINIC PHONE</th>
                                        <th>CLINIC HEAD</th>
                                        <th>EMAIL</th> 
                                        <!-- <th>ADDRESS</th> -->
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($clinic_list as $value) { ?> 
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td><?php echo $value->clinic_name;?></td>
                                        <!-- <td><img width="150px" src="<?php echo base_url('uploads/clinic_logos/'.$value->clinic_logo);?>" alt="clinic"></td> -->
                                        <td><?php if($value->clinic_type==0){ echo "--";}elseif($value->clinic_type==1){echo "SELF";}else{echo "Customer";}?></td>
                                        <td><?php echo $value->clinic_phone; ?></td>
                                        <td><?php echo $value->incharge_name; ?></td>
                                        <td><?php echo $value->clinic_email; ?></td>
                                        <!-- <td><?php echo $value->address; ?></td> -->
                                        <td>
                                          <a href="<?php echo base_url('clinic/clinic_view/'.$value->clinic_id);?>"> <i class="fa fa-eye"></i></a>
                                          <a href="<?php echo base_url('clinic/clinic_update/'.$value->clinic_id);?>"><i class="fa fa-edit"></i></a>
                                          <a data-toggle="modal" data-target="#doctorsModal"
                                          onclick="getDocDetails('<?=$value->clinic_id?>','<?=$value->clinic_name?>')">
                                    <i class="fa fa-trash"></i>
                                </a>
                                          <!-- <a href="<?php echo base_url('clinic/clinic_delete/'.$value->clinic_id);?>" onclick="doconfirm()"><i class="fa fa-trash"></i></a> -->
                                          <!-- <a href="<?php echo base_url('clinic/clinic_delete/'.$value->clinic_id);?>"><input type="button" class="fa fa-trash" name="<?php echo $value->clinic_id ;?>" id="sa-params"></a> -->
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            <?php foreach($appInfoo as $app){
              echo $app;
              }?>

<!-- Doctors Modal -->

<div class="modal fade" id="doctorsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"  style="color:white;"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="dayNumm" value="">
        <input type="hidden" id="clinicName" value="">
        <div class="row">
                <div class="col-md-12">
                <p class="p-2 font-italic ml-4 rounded-bottom rounded-top my-2 bg-danger">
                By choosing below clinics, You can erase enrolled doctors information of choosen clinics (Appointments and its related data, Patients and Doctor Relation. Including Billings.). Once deleted can't be reverted back.</p>
                </div>
                
            </div>
      </div>
      <div class="modal-footer">
      <!-- <p>Are You Sure You Want To Delete</p> -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary"  data-dismiss="modal" id="submitForm" style="margin-right:200px;">Delete</button>
      </div>
    </div>
  </div>
</div>


    </section>

 <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Upload File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<div class="modal-body">
     <?php
$output = ''; 
$output .= form_open(base_url('clinic/save'), 'class="form-horizontal" enctype="multipart/form-data"');  
$output .= '<div class="row">';
$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group">';
$output .= form_label('Choose file', 'file');
$data = array(
    'name' => 'userfile',
    'id' => 'userfile',
    'class' => 'form-control filestyle',
    'value' => '',
    'data-icon' => 'false'
);
$output .= form_upload($data);
$output .= '</div> <span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group text-right">';
$data = array(
    'name' => 'importfile',
    'id' => 'importfile-id',
    'class' => 'btn btn-primary',
    'value' => 'Import',
);
$output .= form_submit($data, 'Import Data');
$output .= '</div>
                        </div></div>';
$output .= form_close();
echo $output;
?>
</div>
     </div>
   </div>
 </div>

 <script>
  $(document).ready(function () {
      $('#clinic_list').dataTable();
  });
  </script>
  <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>
  <!--Sweet Alerts-->
       <script src="<?php echo base_url('assets/lib/sweet-alerts2/sweetalert2.min.js');?>"></script>
  <!-- <script>
            $(document).ready(function () {

                //Parameter
                $('#sa-params').click(function () {
                  var $this = $(this);
                  var userid = $this.attr('name');
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this imaginary file!",
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonClass: 'btn-secondary ',
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    $.ajax({

                      type: "GET",
                      url: 'check_del.php',
                      data: {
                      'id': userid

                          },
                      success: function(data) {

                      

                    }); 
                    // function (isConfirm) {
                    //     if (isConfirm) {
                    //         swal("Deleted!", "Your imaginary file has been deleted.", "success");
                    //     } else {
                    //         swal("Cancelled", "Your imaginary file is safe :)", "error");
                    //     }
                    // });
                });

            });

        </script> -->

        <script>
function getDocDetails(id,clinicName) {
  $('#doctorsModal .modal-title').html(clinicName);
     $("#dayNumm").val(id);
     $("#clinicName").val(clinicName);
    //  $.ajax({
    //             type:'POST',
    //             url:'<?php echo base_url('Clinic/deleteClinicData'); ?>',
    //             // dataType : "json",
    //             data:{id: id},
    //             success:function(data){
    //               alert('success');
                 
    //  }
    //       });

}
</script>

<script>
        $('#submitForm').click(function(){
           var id = $('#dayNumm').val();
           var clinicName = $('#clinicName').val();
            // alert(id);
            // alert(clinicName);
            $.ajax({
                type:'POST',
                url:'<?php echo base_url('Clinic/deleteClinicData'); ?>',
                // dataType : "json",
                data:{id:id},
                success:function(res){
                  // alert(res);
                  if(res == 1)
                {
                   alert("Successfully Deleted.");
                    location.reload();
                  
             
                }
                else
                {
                    alert("Error Occured.");
                    location.reload();
                }
           }
          });
        
       
        });
    </script>  

<script>

</script>

<script>
$(document).on("click", ".submitDel", function() {
    var clinics = [];
    $.each($("input[name='clinics']:checked"), function() {
        clinics.push($(this).val());
    });
    if (clinics.length == 0) {
        alert("Please Select Clinic.");
    } 
    else {
        var confirm = window.confirm("Are you sure to continue?");
        if (confirm == true) {
            var docId = $('.docId').val();
            $('#delLoading').removeClass("hidden");
            $.post("<?=base_url('Doctor/DeleteData')?>", {
                id: docId,
                clinics: clinics.toString()
            }, function(data) {
                console.log(data)
                if(data == 1)
                {
                    alert("Successfully Deleted.");
                    location.reload();
                }
                else
                {
                    alert("Error Occured.");
                    location.reload();
                }
            });
        }
    }
});
</script>



