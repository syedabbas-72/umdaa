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
    <div class="page-title-breadcrumb col-lg-6">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Umdaa Clinic/Forms/Add/<?php echo $form_list->form_name?></li>
        </ol>
    </div>
    <div class="col-lg-6 ]mt-3 text-right">
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Add 
</button> -->

<a class="btn btn-primary" href="<?php echo base_url('FormsAdmin/add/'.$form_list->id);?>">
        Add 
        </a>



       <!-- <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('FormsAdmin/addNewForm');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i>Add Specilization</a>
        </div> -->
    </div> 
    </div>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Add New Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form  method="post" action="<?php echo base_url('FormsAdmin/add_description_details/'.$form_list->id);?>">
            <div class="form-group">
                <label for="exampleInputEmail1">Name</label>
                <input type="text" class="form-control" name="name" id="type" required autocomplete="off" aria-describedby="emailHelp">

            </div>
            <!-- <div class="form-group"> -->
            <!-- <div class="col-md-12"> -->
                <div class="form-group">
                    <label  class="col-form-label"> Description</label>
                    <textarea name="description" style="height: 800px"></textarea>
                    <!-- <textarea name="description" id="summernote" style="height: 200px"></textarea> -->
                </div>
            <!-- </div> -->
                <!-- <label for="exampleInputPassword1">Description</label>
                <input type="text" class="form-control" required autocomplete="off" id="amount" name="description"> -->
            <!-- </div> -->
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>
</div>


<section class="main-content">   
    <div class="row">             
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">


                    <form  method="post" action="<?php echo base_url('FormsAdmin/add_description_details/'.$form_list->id);?>">
            <div class="form-group">
                <label for="exampleInputEmail1">Name</label>
                <input type="text" class="form-control" name="name" id="type" required autocomplete="off" aria-describedby="emailHelp">

            </div>

            <div class="form-group">
                    <label for="benefits" class="col-form-label"> Description</label>
                    <textarea name="description" id="summernote" style="height: 400px"></textarea>
                </div>
   
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
        if(confirm("Are you sure you want to Delete")){
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


<script type="text/javascript">
 $(document).ready(function () {
      $('#summernote').summernote({});
});
</script>

 
 