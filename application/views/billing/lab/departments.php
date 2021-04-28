<?php
// get CRUD info
$crudInfo = getcrudInfo('Lab/departments');
?>



<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Lab Investigations&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Departments</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card noCardPadding">

				<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">Setup Using Lab Departments</div>
				</div>

				<div class="tabs">
					<!-- Nav tabs -->

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">
							<div class="row col-md-12">
								<div class="col-md-12">
									<h2>List of Departments</h2>
									<p>Select the departments and navigate to next step.</p>
									<form method="POST" action="<?php echo base_url('Lab/department_investigations'); ?>">
										<div class="row col-md-12">
											<div class="col-md-12">
												<div class="form-group">
													<input type="checkbox" id="selectAll" onClick="checkAll(this, 'depCheckBox')" />
													<label for="selectAll">Select All</label>
													<br />
												</div>
											</div>
										</div>
										
										<ul class="checkList">
											<?php
											foreach ($departments as $lab_department) {
											?>
												<li><input id="<?= $lab_department['lab_department_id'] ?>_cb" type="checkbox" name="lab_department[<?= $lab_department['lab_department_id'] ?>]" class="form-group depCheckBox" value="<?= $lab_department['department_name'] ?>">&nbsp;<label for="<?= $lab_department['lab_department_id'] ?>_cb"><?= $lab_department['department_name'] ?></label></li>
											<?php
											}
											?>
										</ul>
										<input type="submit" name="submit" value="Submit" class="btn btn-app">
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<script type="text/javascript">
	// Select all / Unselect checkboxes code
	function checkAll(source, cls) {
		checkboxes = document.getElementsByClassName(cls);
		for (var i = 0, n = checkboxes.length; i < n; i++) {
			checkboxes[i].checked = source.checked;
		}
	}
</script>