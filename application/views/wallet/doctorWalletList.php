<style type="text/css">
     td
     {
      white-space: unset !important;
     }
   </style>

<!-- <div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Umdaa Clinic/Wallet Specilization Prices</li>
        </ol>
    </div>
    
</div> -->

<!-- Add Wallet Amount Modal Starts -->

     <div class="modal fade" id="addWalletModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4>Add Wallet Amount</h4>
            <button class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
          <form method="post" action="<?php echo base_url('Wallet/addWalletPrice');?>">
            <div class="form-group">
                <label class="col-form-label">Department</label><br>
                <select name="doctor_department" id="doctor_department" required class="form-control">
                        <option>--Select Department--</option>
                        <?php foreach ($departments as $val) { ?>
                            <option value="<?php echo $val->department_id;?>"><?php echo $val->department_name;?></option>
                        <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Doctor</label>
                <select name="doctor_name" id="doctor_name" required class="form-control" required="">
                  <option>--Select Doctor--</option>
                </select>
              </div>
              <div class="form-group">
                <label>Speciality</label>
                <select class="form-control" name="speciality" required >
                  <option selected disabled>Select Speciality</option>
                  <?php
                  foreach($speciality as $val)
                  {
                    ?>
                    <option value="<?=$val->speciality?>"><?=ucwords($val->speciality)?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Enter Amount</label>
                <input type="text" onkeypress="return numeric()" required autocomplete="off" class="form-control" name="amount" id="exampleInputPassword1">
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>
      </div>
     </div>

<!-- Add Wallet Amount Modal Ends -->

<!-- Edit Wallet Modal Starts -->
<div class="modal fade" id="editWalletModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Edit Wallet Amount</h4>
        <button class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <form method="post" action="<?php echo base_url('Wallet/doctorWalletPriceEdit');?>">
          <h5 class="pl-2 font-weight-bold text-primary">Present Amount in Doctor's Wallet <span class="presentAmount badge badge-danger">Rs. 0.00 /-</span></h5>
          <div class="form-group">
            <label for="exampleInputPassword1">Enter Amount</label>
            <input type="text" onkeypress="return numeric()" required autocomplete="off" class="form-control" name="amount" id="exampleInputPassword1">
            <input type="hidden" name="wallet_id" class="wallet_id">
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
  </div>
<!-- Edit Modal Modal Ends -->


<div class="page-bar">
    <div class="row page-header no-background no-shadow margin-b-0">
    <div class="col-md-6">
      <div class="page-title-breadcrumb">
          <ol class="breadcrumb page-breadcrumb">
              <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a></li>
              <li class="parent-item">Umdaa Clinics <i class="fa fa-angle-right"></i></li>
              <li class="active">Wallet Specilization Prices</li>
          </ol>
      </div>
    </div>
    <div class="col-lg-6 text-right mt-2">
      <button class="btn btn-primary  box-shadow btn-icon btn-rounded" data-toggle="modal" data-target="#addWalletModal"><i class="fa fa-plus"></i> Add Wallet Amount</button>
    </div> 
    </div>

<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="testimonials_list" class="table customTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Doctor Name</th>
                                        <th>Department</th>
                                        <th>Speciality</th>
                                        <th>Amount(In Rupees)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $i=1; 

                                  foreach ($list as $value) { 
                                    $data = "Rs. ".number_format($value->amount,2)." /-*$".$value->doctor_wallet_id;
                                    $deptInfo = $this->db->query("select department_name,qualification from doctors d, department de where d.department_id=de.department_id and d.doctor_id='".$value->doctor_id."'")->row();
                                  ?>
                                    <tr>
                                      <td><?php echo $i++;?></td>
                                      <td><span class="text-uppercase"><?=getDoctorName($value->doctor_id)?></span><br>
                                        <span class="formulation ml-0"><?=$deptInfo->qualification?></span></td>
                                      <td><label class="text-uppercase"><?=$deptInfo->department_name?></label></td>
                                      <td><label class="text-uppercase"><?=$value->speciality?></label></td>
                                      <td><span>Rs. <?php echo number_format($value->amount,2); ?> /-</span></td>
                                      <td>
                                        <a href="#" class="editDoc" data-id="<?=$data?>" data-toggle="modal" data-target="#editWalletModal"><i class="fa fa-edit"></i></a>
                                        <!-- <a href="<?php echo base_url('Wallet/doctor_price_edit/'.$value->id);?>"><i class="fa fa-edit"></i></a> -->
                                        <a href="<?php echo base_url('Wallet/doctor_price_delete/'.$value->doctor_wallet_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a>
                                      </td>
                                    </tr>
                                  <?php } ?>
                               
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
             
    </section>



 <script>
  $(document).ready(function () {
      $('#testimonials_list').dataTable();
      // $('select').select2();
      // $('#addWalletModal').modal();
  });
  </script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('.articleView').on("click",function(){
        var article_id = $(this).attr("data-id");
        $.post("<?=base_url("Articles/getArticleData")?>",{article_id:article_id},function(data){
          $('.article_body').html(data);
        });
      });
    });
  </script>
  <script>
  $(document).ready(function(){
    $('.editDoc').on("click",function(){
      var data = $(this).attr("data-id");
      var str = data.split("*$");
      $('.presentAmount').html(str[0]);
      $('.wallet_id').val(str[1]);
    });
  });
  </script>
  <script>
$(document).ready(function(){
    var url = "<?php echo base_url('Wallet/getDoctor'); ?>";
    $('#doctor_department').on('change', function(){
        var id = $(this).val();
        // alert(id);
        if(id != 0){
            $.ajax({
                type:'POST',
                url:url,
                data:{id: id},
                success:function(html){
                  // console.log(html);
                     $('#doctor_name').html(html);
                 }
                });
            
        }else{
            $('#doctor_name').html('<option value="">Select State first</option>');
            // $('#city').html('<option value="">Select state first</option>'); 
        }
    });
    
});
</script>




  <script>
  function doconfirm()
    {
        if(confirm("Delete this Type ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>

  <script>
  function edit_person(id)
{

    alert(id);
    // save_method = 'update';
    // $('#form')[0].reset(); // reset form on modals
    // $('.form-group').removeClass('has-error'); // clear error class
    // $('.help-block').empty(); // clear error string
 
    // //Ajax Load data from ajax
    // $.ajax({
   
    //     type: "GET",
    //     dataType: "JSON",
    //     success: function(data)
    //     {
 
    //         $('[name="id"]').val(data.id);
    //         $('[name="firstName"]').val(data.firstName);
    //         $('[name="lastName"]').val(data.lastName);
    //         $('[name="gender"]').val(data.gender);
    //         $('[name="address"]').val(data.address);
    //         $('[name="dob"]').datepicker('update',data.dob);
    //         $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
    //         $('.modal-title').text('Edit Person'); // Set title to Bootstrap modal title
 
    //     },
    //     error: function (jqXHR, textStatus, errorThrown)
    //     {
    //         alert('Error get data from ajax');
    //     }
    // });
}
  </script>



 