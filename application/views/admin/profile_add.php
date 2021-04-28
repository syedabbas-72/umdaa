<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA CLINICS</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?=base_url('Admin/profiles')?>">Profiles Master</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Add New</li>
        </ol>

        <div class="pull-right tPadding bcBtn">
            <a href="<?= base_url('Admin/profiles'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-backward"></i> Back</a>
        </div>
    </div>
</div>

<section class="main-content">
	<div class="row">             
		<div class="col-md-12">
			<div class="card">

			 	<div class="row page-title">
                    <div class="col-md-12">
                        Add New Profile
                    </div>
                </div>

				<div class="card-body">
					<form role="form" method="post" action="<?php echo base_url('Admin/add_profile');?>" class="form">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-form-label">Profile Name</label>
									<input type="text" class="form-control" name="profile_name" required="required"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<input type="submit" class="btn customBtn" name="submit" value="Submit">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>