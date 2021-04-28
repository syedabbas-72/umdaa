
<div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
          	<li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
          	<li class="breadcrumb-item"><a href="#">FORM_DEPARTMENT</a></li>
            <li class="breadcrumb-item active"><a href="#">VIEW</a></li>
          </ol>
        </div>
    </div>
<section class="main-content">
	<div class="row">
	    <div class="col-md-12">
	        <div class="card"><div class="card-body">
                
<table class="table">
	<tr>
		<th colspan="2">DEPARTMENT:&nbsp;&nbsp;<b><?php echo $form_department->department_name; ?></b></th>
	</tr>
	<tr>
		<td><b>FORMS</b><td></td></td>
	</tr>
	<?php foreach ($forms as $key => $value) {?>
	<tr>
		<td><?php echo $value->form_name; ?></td><td><a href="<?php echo base_url('form_department/delt_form/'.$value->form_id); ?>"><i class="fa fa-trash"></i></a></td>
	</tr>
	<?php } ?>
</table>
			</div>
		</div>
	</div>
</section>
