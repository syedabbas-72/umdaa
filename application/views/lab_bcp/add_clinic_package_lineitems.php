<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>
          <li><a class="parent-item" href="<?php echo base_url('lab/lab_packages'); ?>">LAB PACKAGES</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">ADD ITEM</li>
      </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
						<div class="row col-md-12" id="adddrug" style="display:none">
						<span style="color: red;font-weight: bold">Investigation Is not Available Please add to Clinic Master<span> <a href="<?= base_url('Lab/add_clinic_investigation'); ?>" class="btn btn-primary btn-rounded btn-xs box-shadow"> ADD NEW INVESTIGATION</a>
						</div>
                         <div class="row">
                         	<div class="col-md-11">
						<input class="form-control" type="text" name="search_investigation" placeholder="search by investigation name" id="search_investigation" onclick="clinicinvestigationsearch();" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span>
					</div>
					<div class="col-md-1 buttonAction">
				    	<button type="button" class="btn btn-success"  onclick="add_investigation_package_row('orderlist');"><i class="fas fa-plus"></i></button>
				    </div>
						</div>
                          <form method="POST" action="<?php echo base_url('Lab/add_clinic_package_lineitems/'.$package_id);?>" role="form">
                           <table id="orderlist" class="table table-bordered dt-responsive nowrap" >
							<thead>
								<tr>
								
									<th style="width: 250px">Investigation Name</th>
									<th style="width: 150px">Item Code</th>
									<th>Category</th>
									<th style="width: 100px">Short Form</th>
									<!--<th style="width: 100px">MRP</th>-->
									<th style="width: 100px">Low Range</th>
									<th style="width: 100px">High Range</th>
									<th style="width: 100px">Units</th>
									<th style="width: 100px">Method</th>
									<th style="width: 100px">Other Information</th>
									<th style="width: 50px"></th>															
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<table id="orderlist1" class="table table-bordered dt-responsive nowrap" style="display:none">
						<tbody>						
						<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-success"></td></tr>
						</tbody>
						</table>						
                          </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>  
