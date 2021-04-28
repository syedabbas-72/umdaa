
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('Package');?>">PACKAGE</a></li>
          <li class="breadcrumb-item active"><a href="#">View</a></li>          
        </ol>
  </div>
  <div class="col-lg-6 align-self-center text-right">
	<a href="<?php echo base_url('Package/package_entity_add/'.$packages->package_id);?>" class="btn btn-success btn-icon btn-rounded"><i class="fa fa-plus"></i> Add Entities</a>
    <a href="<?php echo base_url('Package/package_price_add/'.$packages->package_id);?>" class="btn btn-success btn-icon btn-rounded"><i class="fa fa-plus"></i> Add Package Price</a>
  </div>
</div>
<section class="main-content">
<div class="row">
  <div class="col-md-12">
    <!-- card start -->
    <div class="card">
      <!-- <div class="card-header card-default">Inline form</div> -->
       <div class="card-body">
          <div class="tabs">            
              <div class="tab-content">
                  <div role="tabpanel">
				  <h4 class="heading_h4"> <b><?php echo $packages->package_name; ?>&nbsp;Package Price</b></h4>
				  <!-- <h3>Price List</h3> -->
				  <table id="userlist" class="table table-striped dt-responsive nowrap">
					<thead>
						<tr>
							<th>S.No:</th>
							<th>Price</th>   
							<th>From Date</th>
							<th>To Date</th>							
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					  <?php 
					  $i=1; 
					  foreach ($packages_price_list as $value) { 
					  ?> 
						<tr>
						  <td><?php echo $i++;?></td>
						  <td><?php echo $value->price; ?></td>
						  <td><?php echo $value->from_date; ?></td>
						  <td><?php echo $value->to_date; ?></td>
						  <td>							
							<a href="<?php echo base_url('Package/package_price_update/'.$value->package_price_id);?>"><i class="fa fa-edit"></i></a>
							<a href="<?php echo base_url('Package/package_price_delete/'.$value->package_price_id.'/'.$packages->package_id);?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a>
						  </td>
						</tr>
					  <?php } ?>
				   
						
					</tbody>
				</table>
			
				  </div>
			  </div>
		  </div>
		</div>
	</div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
<div class="card">
      <!-- <div class="card-header card-default">Inline form</div> -->
       <div class="card-body">
       	<div class="tabs">            
              <div class="tab-content">
                  <div role="tabpanel">
       		<h4 class="heading_h4"><b>Entities</b></h4>
				<table id="userlist1" class="table table-striped dt-responsive nowrap">
					<thead>
						<tr>
							<th>S.No:</th>
							<th>Entity Name</th>   												
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					  <?php 
					  $i=1; 
					  foreach ($packages_entities_list as $value) { 
					  ?> 
						<tr>
						  <td><?php echo $i++;?></td>
						  <td><?php echo $value->user_entity_name; ?></td>
						  
						  <td>														
							<a href="<?php echo base_url('Package/package_entity_delete/'.$value->package_user_entities_id.'/'.$packages->package_id);?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a>
						  </td>
						</tr>
					  <?php } ?>
				   
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
	</div>
</div></div></div>

</section>
<script>
  $(document).ready(function () {
      $('#userlist,#userlist1').dataTable();
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