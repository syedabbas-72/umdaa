<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">UMDAA CLINICS</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li><a href="<?=base_url('Admin/profiles')?>">Profiles Master</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Profile&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Edit &amp; Save Changes</li>
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
                        Edit Profile Name
                    </div>
                </div>

				<div class="card-body">
					<form role="form" method="post" action="<?php echo base_url('Admin/update_profile/');?><?php echo $profile->profile_id; ?>" class="form">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-form-label">Profile Name</label>
									<input type="text" class="form-control" name="profile_name" required="required" value="<?php echo $profile->profile_name; ?>" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-form-label">Status</label>
									<select name="status" class="form-control">
										<option value="1" <?php echo ($profile->status == 1) ? "Selected" : ""; ?>>Active</option>
										<option value="0" <?php echo ($profile->status == 0) ? "Selected" : ""; ?>>In-Active</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-form-label">HomePage</label>
									<select class="form-control homepage" name="user_entity_id">
										<option selected="" disabled="">Select HomePage</option>
										<?php
										foreach ($user_entities as $value) 
										{
											?>
											<option value="<?=$value->user_entity_id?>" <?=($value->user_entity_id==$profile->user_entity_id)?'selected':''?> ><?=$value->user_entity_name?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="submit" class="btn customBtn" name="submit" value="Update">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
  $(document).ready(function() {
      $('.homepage').select2();
  });
</script>