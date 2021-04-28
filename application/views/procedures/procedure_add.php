<link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" rel="stylesheet">
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="#">DEPARTMENT</a></li>
          <li class="breadcrumb-item active"><a href="#">Add</a></li>          
        </ol>
  </div>
</div>

<section class="main-content">
<div class="row">             
<div class="col-md-12">
<div class="card">
  <div class="card-body">
   
<form method="POST" action="<?php echo base_url('procedure/add');?>" role="form">
  <div class="row col-md-12">

<div class="col-md-6">
  <div class="form-group">
    <label for="procedure_name" class="col-form-label">PROCEDURE NAME</label>
    <input id="procedure_name" name="procedure_name" type="text" placeholder="" class="form-control">
  </div>
</div>
</div>
                               
<div class="col-md-12">
  <div class="form-group">
   <textarea name="procedure_description" id="summernote" cols="30" rows="10"></textarea>
  </div>
</div>

<div class="col-sm-6">
  <div class="pull-right">
      <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
  </div>
</div>

</form>
</div>
</div>
</div>
</div>
</div>
</section>  
<script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.js" ></script>
<script type="text/javascript">
 $(document).ready(function () {
   $('#summernote').summernote({
    placeholder: '',
    tabsize: 2,
    height: 200
  });
});
</script>

