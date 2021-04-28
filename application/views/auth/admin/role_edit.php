<div class="row page-header">
			<div class="col-lg-6 align-self-center ">
			  
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item"><a href="#">Roles</a></li>
					<li class="breadcrumb-item active"><a href="#">Role Edit</a></li>					
				</ol>
			</div>
		</div>
<section class="main-content">
    <div class="row">             

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
						<form role="form" method="post" action="<?php echo base_url('Admin/update_role/');?><?php echo $role->role_id; ?>">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
									<label>Role</label>
										<div class="input-group m-b">
											<input type="text" class="form-control" name="role_name" required="required" value="<?php echo $role->role_name; ?>" />
                                        </div>
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
									<label>Reports To</label>
										<div class="input-group m-b">
										
										<select name="role_reports_to" class="form-control m-b">
                                                <option value="0">Selef</option>
                                                <?php
										foreach($roles_list as $roles)
										{?>
											<option value="<?php echo $roles->role_id; ?>"
											<?php if($roles->role_id==$role->role_reports_to){?>
										selected="Selected"
											<?php }?>><?php echo $roles->role_name; ?></option>
										<?php } ?>
                                            </select>
										</div>
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-group">
									<label>Status</label>
										<div class="input-group m-b">
										
										<select name="status" class="form-control m-b">
											<option value="1"
											<?php if($role->status==1){?>
										selected="Selected"
											<?php }?>>Active</option>
											<option value="0"
											<?php if($role->status==0){?>
										selected="Selected"
											<?php }?>>In-Active</option>
                                            </select>
										</div>
									</div>
								</div>
							</div>
							<br/>
							<input type="submit" class="btn btn-warning margin-l-5 mx-sm-3" name="submit" value="Update">
						</form>
                        </div>
						
					  </div>
                    </div>
                </div>