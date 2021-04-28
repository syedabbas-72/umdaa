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

<div class="page-bar">
    <div class="row page-header no-background no-shadow margin-b-0">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Umdaa Clinic/Wallet Specilization Prices</li>
        </ol>
    </div>
    <div class="col-lg-6 align-self-center text-right">
    <a  href="<?=base_url('wallet/demo')?>">
  Back
</a>



       <!-- <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('wallet/walletAddPrice');?>" class="btn btn-success box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i>Add Specilization</a>
        </div> -->
    </div> 
    </div>
            


<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                        <form  method="post" action="<?php echo base_url('Wallet/demo12345/'.$price_edit->id);?>">
  <div class="form-group">

    <label for="exampleInputEmail1">Doctor Department</label>
    <input type="text" required value="<?=$price_edit->department_name?>"  disabled class="form-control" name="type" id="type" aria-describedby="emailHelp">
    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
  </div>




  <div class="form-group">
    <label for="exampleInputEmail1">Doctor Name</label>
    <input type="text" required value="<?=$price_edit->doctor_name?>"  disabled class="form-control" name="type" id="type" aria-describedby="emailHelp">

  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Amount</label>
    <input type="text" value="<?=$price_edit->amount?>" required class="form-control" name="amount" id="amount">
  </div>
  <!-- <div class="form-group form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Check me out</label>
  </div> -->
  <button type="submit" class="btn btn-primary">Submit</button>
</form>


                        </div>
                    </div>
                </div>
            </div>
             
    </section>



 <script>
  $(document).ready(function () {
      $('#testimonials_list').dataTable();
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
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
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



 