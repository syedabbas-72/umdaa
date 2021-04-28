 <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>

                                <li><a class="parent-item" href="<?php echo base_url("lab/lab_investigations"); ?>">Lab Investigations</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                               
                                <li class="active">Edit Investigation</li>
                            </ol>
                        </div>
                    </div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
						
                         
                          <form method="POST" action="<?php echo base_url('Lab/edit_investigation/'.$invginfo->clinic_investigation_id);?>" role="form">
						  <input type="hidden" name="clinic_investigation_id" value="<?php echo $invginfo->clinic_investigation_id; ?>" />
                           <table id="orderlist" class="table table-bordered dt-responsive nowrap" >
							<thead>
								<tr>
								
									<th>Investigation Name</th>
									<th>Item Code</th>
									<th>Short Form</th>
									<th>Category</th>
									<th>MRP</th>
									<th>Low Range</th>
									<th>High Range</th>
									<th>Units</th>
									<th>Method</th>	
									<th>Other Information</th>
								</tr>
							</thead>
							<tbody>
							<tr>
							<td><?php echo $invginfo->investigation; ?></td>
							<td><?php echo $invginfo->item_code; ?></td>
							<td><?php echo $invginfo->short_form; ?></td>
							<td><?php echo $invginfo->category; ?></td>
							<td><input class="form-control" type="text" value="<?php echo $invginfo->price; ?>" name="imrp" required /></td>
							<td><input class="form-control" type="text" value="<?php echo $invginfo->low_range; ?>" name="low_range"/></td>
							<td><input class="form-control" type="text" value="<?php echo $invginfo->high_range; ?>" name="high_range" /></td>
							<td><input class="form-control" type="text" value="<?php echo $invginfo->units; ?>" name="units"/></td>
							<td><input class="form-control" type="text" value="<?php echo $invginfo->method; ?>" name="method"/></td>
							<td><input class="form-control" type="text" value="<?php echo $invginfo->other_information; ?>" name="other_information"/></td>
							</tr>
							</tbody>
						</table>
						<table id="orderlist1" class="table table-striped dt-responsive nowrap" >
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
