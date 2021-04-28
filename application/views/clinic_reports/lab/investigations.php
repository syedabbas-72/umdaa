<?php
// get CRUD info
$crudInfo = getcrudInfo('Lab/investigations');
?>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url($_SESSION['home']); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Lab Investigations</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card noCardPadding">

				<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">Investigations in Clinic</div>
					<div class="pull-right col-md-6 text-right actionButtons">
						<?php /* <a href="<?= base_url('Lab/add_clinic_investigation'); ?>"><i class="fas fa-plus add"></i></a> */ ?>
						<a href="<?= base_url('Lab/setup'); ?>"><i class="fas fa-plus add"></i></a>
					</div>
				</div>

				<div class="tabs">
					<!-- Nav tabs -->

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">
							<div class="row">
								<div class="col-md-12">
									<?php if (count($newInvestigations) > 0 || count($investigations) > 0) { ?>
										<!-- <h4>New Investigations (No Price Tags)</h4>
										<p>Below is the list of new investigations for which the price tags were not attached</p> -->
										<table id="investigationsList_tbl" class="table customTable">
											<thead>
												<tr>
													<th class="text-right" style="width: 5%">S.No.</th>
													<th style="width: 45%">Investigation / Sample Type / Item Code<br><small><i>Method / Short Form</i></small></th>
													<th style="width: 15%">Condition</th>
													<th style="width: 15%" class="text-center">Range / Units</th>
													<th style="width: 10%" class="text-right">MRP</th>
													<th style="width: 10%" class="text-center">Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$i = 1;
												foreach ($newInvestigations as $value) {

													$short_form = $value->short_form;

													if ($short_form == '' || $short_form == NULL) {
														$short_form = '';
													} else {
														$short_form = ' (' . $short_form . ')';
													}
												?>
													<!-- <tr>
														<td colspan="6">
															<pre>
																<?php print_r($value); ?>
															</pre>
														</td>
													</tr> -->
													<tr></tr>
													<tr class="redRows">
														<td class="text-right" style="vertical-align: top"><?php echo $i; ?>. </td>
														<td>
															<span><?php echo strtoupper($value['investigation']) . $short_form; ?></span>
															<?php echo ($value['sample_type'] == '') ? '' : '<span class="sample">' . $value['sample_type'] . '</span>'; ?>
															<br>
															<small><i><?php echo ucwords($value['conditions'][0]['method']); ?></i></small>
															<span class="code"><?= $value['item_code'] ?></span>
															<span class="code"><?= $value['template_type'] ?></span>
															<?php if ($value['package'] == 1) { ?><span class="package">GOI</span><?php } ?>
														</td>
														<td><?= $value['conditions'][0]['condition'] ?></td>
														<td class="text-center">
															<?php
															if ($value['conditions'][0]['low_range'] == '' && $value['conditions'][0]['high_range'] == '') {
																echo '-';
															} else {
																if ($value['conditions'][0]['low_range'] == 0 && $value['conditions'][0]['high_range'] != 0) {
																	echo "<span> < " . $value['conditions'][0]['high_range'] . '</span>';
																} elseif ($value['conditions'][0]['low_range'] != 0 && ($value['conditions'][0]['high_range'] == 0 || $value['conditions'][0]['high_range'] == '')) {
																	echo "<span> > " . $value['conditions'][0]['low_range'] . '</span>';
																} else {
																	echo "<span>" . $value['conditions'][0]['low_range'] . '</span> - <span>' . $value['conditions'][0]['high_range'] . '</span>';
																}
															}
															echo '&nbsp;&nbsp;';
															echo "<span>" . $value['conditions'][0]['units'] . "</span>";
															// if($value['other_information'] != '')
															// echo "<br><small>".$value['other_information']."</small>";
															?>
														</td>
														<td class="text-right">
															<i class="fas fa-rupee-sign currencySmall"></i><span class="priceSmall"><?php echo number_format($value['price'], 2); ?></span>
														</td>
														<td class="text-center actions">
															<!-- Read -->
															<?php if ($value['package'] == 1) { ?>
																<?php if ($crudInfo->p_read) { ?>
																	<i class="fas fa-search viewSmall" onclick="return openPopup('viewPackageModal','package','<?= $value["investigation_id"] ?>','<?= $value["clinic_investigation_id"] ?>','<?= $value["price"] ?>','<?= $value["clinic_investigation_price_id"] ?>','<?= $value['template_type'] ?>');" title="View Packaged Investigations &amp; Edit"></i>
																<?php } ?>
															<?php } ?>

															<!-- Edit -->
															<?php if ($value['package'] == 0) { ?>
																<?php if ($crudInfo->p_update) { ?>
																	<i class="fas fa-pencil-alt editSmall" title="Edit Investigation" onclick="return openPopup('viewInvestigationModal','investigation','<?= $value["investigation_id"] ?>','<?= $value["clinic_investigation_id"] ?>','<?= $value["price"] ?>','<?= $value["clinic_investigation_price_id"] ?>','<?= $value['template_type'] ?>');"></i>
																<?php } ?>
															<?php } ?>

															<!-- Delete -->
															<?php if ($crudInfo->p_delete) { ?>
																<a onclick="return doconfirm()" href="<?= base_url("Lab/delete_lab_investigation/" . $value['clinic_investigation_id']) ?>"><i class="fas fa-trash-alt deleteSmall" title="Edit"></i></a>
															<?php } ?>
														</td>
													</tr>
													<?php
													// If conditions are more than 1 then
													$count = 0;
													$conditions = '';
													$count = count($value['conditions']);
													if ($count > 1) {
														$conditions = $value['conditions'];
														$j = 1;
														foreach ($conditions as $condition) {
															if ($j < $count) {
													?>
																<tr class="redRows">
																	<td class="text-right" style="vertical-align: top"></td>
																	<td <?php if ($j == (int) $count - 1) {
																			echo 'class="conditionRecEnd"';
																		} else {
																			echo 'class="conditionRec"';
																		} ?>>&nbsp;</td>
																	<td><?= $value['conditions'][$j]['condition'] ?></td>
																	<td class="text-center">
																		<?php
																		if ($value['conditions'][$j]['low_range'] == '' && $value['conditions'][$j]['high_range'] == '') {
																			echo '-';
																		} else {
																			if ($value['conditions'][$j]['low_range'] == 0 && $value['conditions'][$j]['high_range'] != 0) {
																				echo "<span> < " . $value['conditions'][$j]['high_range'] . '</span>';
																			} elseif ($value['conditions'][$j]['low_range'] != 0 && ($value['conditions'][$j]['high_range'] == 0 || $value['conditions'][$j]['high_range'] == '')) {
																				echo "<span> > " . $value['conditions'][$j]['low_range'] . '</span>';
																			} else {
																				echo "<span>" . $value['conditions'][$j]['low_range'] . '</span> - <span>' . $value['conditions'][$j]['high_range'] . '</span>';
																			}
																		}
																		echo '&nbsp;&nbsp;';
																		echo "<span>" . $value['conditions'][$j]['units'] . "</span>";
																		// if($value['other_information'] != '')
																		// 	echo "<br><small>".$value['other_information']."</small>";
																		?>
																	</td>
																	<td class="text-center"> - </td>
																	<td class="text-center actions">
																		--
																	</td>
																</tr>
													<?php
															}
															$j++;
														}
													}
													$i++;
												}

												// Investigations list whose prices were mentioned
												foreach ($investigations as $value) {

													$short_form = $value->short_form;

													if ($short_form == '' || $short_form == NULL) {
														$short_form = '';
													} else {
														$short_form = ' (' . $short_form . ')';
													}
													?>
													<!-- <tr>
														<td colspan="6">
															<pre>
																<?php print_r($value); ?>
															</pre>
														</td>
													</tr> -->
													<tr></tr>
													<tr>
														<td class="text-right" style="vertical-align: top"><?php echo $i; ?>. </td>
														<td>
															<span><?php echo strtoupper($value['investigation']) . $short_form; ?></span>
															<?php echo ($value['sample_type'] == '') ? '' : '<span class="sample">' . $value['sample_type'] . '</span>'; ?>
															<br>
															<small><i><?php echo ucwords($value['conditions'][0]['method']); ?></i></small>
															<span class="code"><?= $value['item_code'] ?></span>
															<span class="code"><?= $value['template_type'] ?></span>
															<?php if ($value['package'] == 1) { ?><span class="package">GOI</span><?php } ?>
														</td>
														<td><?= $value['conditions'][0]['condition'] ?></td>
														<td class="text-center">
															<?php
															if ($value['conditions'][0]['low_range'] == '' && $value['conditions'][0]['high_range'] == '') {
																echo '-';
															} else {
																if ($value['conditions'][0]['low_range'] == 0 && $value['conditions'][0]['high_range'] != 0) {
																	echo "<span> < " . $value['conditions'][0]['high_range'] . '</span>';
																} elseif ($value['conditions'][0]['low_range'] != 0 && ($value['conditions'][0]['high_range'] == 0 || $value['conditions'][0]['high_range'] == '')) {
																	echo "<span> > " . $value['conditions'][0]['low_range'] . '</span>';
																} else {
																	echo "<span>" . $value['conditions'][0]['low_range'] . '</span> - <span>' . $value['conditions'][0]['high_range'] . '</span>';
																}
															}
															echo '&nbsp;&nbsp;';
															echo "<span>" . $value['conditions'][0]['units'] . "</span>";
															// if($value['other_information'] != '')
															// 	echo "<br><small>".$value['other_information']."</small>";
															?>
														</td>
														<td class="text-right"><i class="fas fa-rupee-sign currencySmall"></i><span class="priceSmall"><?php echo number_format($value['price'], 2); ?></span></td>
														<td class="text-center actions">
															<!-- Read -->
															 <?php if ($value['package'] == 1) { ?>
																<?php if ($crudInfo->p_read) { ?>
																	<i class="fas fa-search viewSmall" onclick="return openPopup('viewPackageModal','package','<?= $value["investigation_id"] ?>','<?= $value["clinic_investigation_id"] ?>','<?= $value["price"] ?>','<?= $value["clinic_investigation_price_id"] ?>','<?= $value['template_type'] ?>');" title="View Packaged investigations &amp; Edit"></i>
																<?php } ?>
															<?php } ?> 

															<!-- Edit -->
															<?php if ($value['package'] == 0) { ?>
																<?php if ($crudInfo->p_update) { ?>
																	<i class="fas fa-pencil-alt editSmall" title="Edit Investigation" onclick="return openPopup('viewInvestigationModal','investigation','<?= $value["investigation_id"] ?>','<?= $value["clinic_investigation_id"] ?>','<?= $value["price"] ?>','<?= $value["clinic_investigation_price_id"] ?>','<?= $value['template_type'] ?>');"></i>
																<?php } ?>
															<?php } ?>

															<!-- Delete -->
															<?php if ($crudInfo->p_delete) { ?>
																<a onclick="return doconfirm()" href="<?= base_url("Lab/delete_lab_investigation/" . $value['clinic_investigation_id']) ?>"><i class="fas fa-trash-alt deleteSmall" title="Edit"></i></a>
															<?php } ?>
														</td>
													</tr>
													<?php
													// If conditions are more than 1 then
													$count = 0;
													$conditions = '';
													$count = count($value['conditions']);
													if ($count > 1) {
														$conditions = $value['conditions'];
														$j = 1;
														foreach ($conditions as $condition) {
															if ($j < $count) {
													?>
																<tr class="conditionRow">
																	<td class="text-right" style="vertical-align: top"></td>
																	<td <?php if ($j == (int) $count - 1) {
																			echo 'class="conditionRecEnd"';
																		} else {
																			echo 'class="conditionRec"';
																		} ?>>&nbsp;</td>
																	<td><?= $value['conditions'][$j]['condition'] ?></td>
																	<td class="text-center">
																		<?php
																		if ($value['conditions'][$j]['low_range'] == '' && $value['conditions'][$j]['high_range'] == '') {
																			echo '-';
																		} else {
																			if ($value['conditions'][$j]['low_range'] == 0 && $value['conditions'][$j]['high_range'] != 0) {
																				echo "<span> < " . $value['conditions'][$j]['high_range'] . '</span>';
																			} elseif ($value['conditions'][$j]['low_range'] != 0 && ($value['conditions'][$j]['high_range'] == 0 || $value['conditions'][$j]['high_range'] == '')) {
																				echo "<span> > " . $value['conditions'][$j]['low_range'] . '</span>';
																			} else {
																				echo "<span>" . $value['conditions'][$j]['low_range'] . '</span> - <span>' . $value['conditions'][$j]['high_range'] . '</span>';
																			}
																		}
																		echo '&nbsp;&nbsp;';
																		echo "<span>" . $value['conditions'][$j]['units'] . "</span>";
																		// if($value['other_information'] != '')
																		// 	echo "<br><small>".$value['other_information']."</small>";
																		?>
																	</td>
																	<td class="text-center"> - </td>
																	<td class="text-center actions">
																		--
																	</td>
																</tr>
												<?php
															}
															$j++;
														}
													}

													$i++;
												}
												?>
											</tbody>
										</table>
									<?php } 
									else { ?>
										<div class="row caution" id="adddrug">
											<div class="col-md-12">
												<p>Hope this is your first visit here!<br> You haven't installed your lab yet. Please start setting it up below.</p>
												<a href="<?= base_url('Lab/setup'); ?>" class="customBtn">START SETUP</a>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- view Investigation Package -->
<div id="viewPackageModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-fluid">
		<!-- Modal content-->
		<div class="modal-content">
			<form class="form" action="<?php echo base_url('Lab/investigations'); ?>" method="post" role="form">
				<div class="modal-header">
					<h4 class="modal-title">List of Investigations/Params</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<div class="modal-body">
					<div class="row tabular" id="packageInfoDiv">

					</div>
				</div>

				<div class="modal-footer text-center">
					<input type="submit" name="submit" value="Submit Ka" class="btn customBtn" onclick="" />
				</div>
			</form>
		</div>
		<!-- Modal content-->
	</div>
</div>

<!-- view Investigation -->
<div id="viewInvestigationModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-fluid">
		<!-- Modal content-->
		<div class="modal-content">
			<form class="form" action="<?php echo base_url('Lab/investigations'); ?>" method="post" role="form">
				<div class="modal-header">
					<h4 class="modal-title">List of Investigations/Params</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<div class="modal-body">
					<div class="row tabular" id="investigationInfoDiv">

					</div>
				</div>

				<div class="modal-footer text-center">
					<input type="submit" name="submit" value="Submit Pa" class="btn customBtn" onclick="" />
				</div>
			</form>
		</div>
		<!-- Modal content-->
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#investigationsList_tbl').DataTable();
	});

	function doconfirm() {
		var doIt = confirm("Delete selected investigation ?");
		if (doIt) {
			return true;
		} else {
			return false;
		}

	}

	function openPopup(modal, type, investigation_id, clinic_investigation_id, price, clinic_investigation_price_id, template_type) {

		console.log(modal + ' : ' + type + ' : ' + investigation_id + ' : ' + price + ' : ' + clinic_investigation_price_id + ' : ' + template_type);

		console.log(type);

		$("#investigationsInfoDiv").empty();

		$.ajax({
			url: base_url + "/Lab/view_package_info",
			method: "POST",
			data: {
				"investigation_id": investigation_id
			},
			success: function(result) {

				console.log("add_parameter "+result);
				
				// return false;

				var colspan = 1;

				var investigationsInfo = $.parseJSON(result);
				console.log("result imp length "+investigationsInfo['templateInfo'].length);
				var methodsCount = investigationsInfo['methods'].length;
				var unitsCount = investigationsInfo['units'].length;
				var itemCount = investigationsInfo['packageItems'].length;
				var method = investigationsInfo['packageItems'][0].method;
				var html = "";
				html += '<table class="customTable" cellspacing="0" cellpadding="0">';
				html += '<tr>';
				if (template_type == 'Excel') {
					html += '<th colspan="3" class="title">' + investigationsInfo['package'].template_name + '<input type="hidden" name="clinic_investigation_id" value="' + clinic_investigation_id + '"><input type="hidden" name="clinic_investigation_price_id" value="' + clinic_investigation_price_id + '"><input type="hidden" name="template_type" value="' + template_type + '"></th>';
					html += '<th colspan="3" class="title"><div class="pull-left">';
					if (investigationsInfo['package'].package == 0) {
						html += '<div style="display:table-cell"><span>Method: </span></div><div style="display:table-cell; padding-left:10px;">';
						if (methodsCount > 0) {
							html += '<select id="method_sb" class="form-control sb2" onchange="show_hide_method()">';
							html += '<option value="">Select Method</option>';
							for (x = 0; x < methodsCount; x++) {
								if (method == investigationsInfo['methods'][x]['method']) {
									var selected = 'selected';
								} else {
									var selected = '';
								}
								html += '<option value="' + investigationsInfo['methods'][x]['method'] + '" ' + selected + '>' + investigationsInfo['methods'][x]['method'] + '</option>';
							}
							html += '<option value="Other">Other</option>';
							html += '</select>';
							html += '<input type="hidden" name="method" class="form-control pull-left" style="width:85%; border-radius:4px " id="method_tb" placeholder="Specify Method" value="' + investigationsInfo['packageItems'][0].method + '" onkeyup="methodUpdate(this.value)"><input type="hidden" id="old_method_tb" value="' + investigationsInfo['packageItems'][0].method + '"><i id="method_icon" class="fas fa-times-circle pull-right error" style="display:none; padding-top:10px; font-size:30px; cursor:pointer;" onclick="closeMethodTxtBx()"></i>';
						} else {
							html += '<input type="text" name="method" class="form-control" id="method_tb" placeholder="Specify Method" value="">';
						}
						html += '</div></div>';
					}
					html += '</th>';
					html += '<th class="text-right"><div class="pull-right"><div style="display:table-cell"><span>Price : </span></div><div style="display:table-cell; padding-left:10px;"><input type="text" name="price" value="' + price + '" class="form-control" onkeypress="return decimal();" placeholder="M.R.P." style="width:100px"></div></div></th>';
				}
				else if (template_type == 'General') {
					colspan = 2;
					html += '<th colspan="2" class="title">' + investigationsInfo['package'].template_name + '<input type="hidden" name="clinic_investigation_id" value="' + clinic_investigation_id + '"><input type="hidden" name="clinic_investigation_price_id" value="' + clinic_investigation_price_id + '"><input type="hidden" name="template_type" value="' + template_type + '"></th>';
					html += '<th colspan="1" class="text-right"><div class="pull-right"><div style="display:table-cell"><span>Price: </span></div><div style="display:table-cell; padding-left:10px;"><input type="text" name="price" value="' + price + '" class="form-control" onkeypress="return decimal();" placeholder="M.R.P." style="width:100px"></div></div></th>';
				}
				html += '</tr>';
				html += '<tr>';
				html += '<th style="width:5%">S.no.</th>';
				html += '<th style="width:23%">Investigation</th>';
				if (template_type == 'Excel') {
					html += '<th style="width:20%">Condition</th>';
					html += '<th style="width:8%" class="text-center">Low Range</th>';
					html += '<th style="width:8%" class="text-center">High Range</th>';
					html += '<th style="width:15%" class="text-center">Units</th>';
					html += '<th style="width:20%">Other Information</th>';
				} else if (template_type == 'General') {
					html += '<th style="width:72%">Remark</th>';
				}
				html += '</tr>';

				for (var i = 0; i < itemCount; i++) {

					// var method = investigationsInfo['packageItems'][i].method;
					var unit = investigationsInfo['packageItems'][i].units;

					var sno = i + 1;
					html += '<tr>';
					html += '<td>' + sno + '</td>';
					html += '<td>' + investigationsInfo['packageItems'][i].parameter + '<input type="hidden" name="range[' + i + '][clinic_investigation_range_id]" value="' + investigationsInfo['packageItems'][i].clinic_investigation_range_id + '"><input type="hidden" name="range[' + i + '][method]" value="' + investigationsInfo['packageItems'][i].method + '" class="methodCls"></td>';

					if (template_type == 'Excel') {
						html += '<td>' + investigationsInfo['packageItems'][i].condition + '</td>';
						// if(methodsCount > 0){
						// 	html += '<input type="hidden" name="clinic_investigation['+i+'][clinic_investigation_id]" value="'+investigationsInfo['packageItems'][i].clinic_investigation_id+'">';
						// 	html += '<select id="'+investigationsInfo['packageItems'][i].clinic_investigation_id+'_method_sb" class="form-control sb2" onchange="showTxtBx('+investigationsInfo['packageItems'][i].clinic_investigation_id+',\'method\','+i+')">';
						// 	html += '<option value="">Select Method</option>';
						// 	for(x=0; x<methodsCount; x++){
						// 		if(method == investigationsInfo['methods'][x]['method']){
						// 			var selected = 'selected';
						// 		}else{
						// 			var selected = '';
						// 		}
						// 		html += '<option value="'+investigationsInfo['methods'][x]['method']+'" '+selected+'>'+investigationsInfo['methods'][x]['method']+'</option>';
						// 	}
						// 	html += '<option value="Other">Other</option>';
						// 	html += '</select>';
						// 	html += '<input type="text" name="range['+i+'][method]" class="form-control pull-left" style="width:85%" id="'+investigationsInfo['packageItems'][i].clinic_investigation_id+'_method_tb" placeholder="Specify Method" value="'+investigationsInfo['packageItems'][i].method+'"><i id="'+investigationsInfo['packageItems'][i].clinic_investigation_id+'_method_icon" class="fas fa-times-circle pull-right error" style="display:none; padding-top:10px; font-size:30px; cursor:pointer;" onclick="closeTxtBx(\''+investigationsInfo['packageItems'][i].clinic_investigation_id+'\',\'method\')"></i>';
						// }else{
						// 	html += '<input type="text" name="range['+i+'][method]" class="form-control" id="'+investigationsInfo['packageItems'][i].clinic_investigation_id+'_method_tb" placeholder="Specify Method" value="">';
						// }
						// html += '</td>';

						if (investigationsInfo['packageItems'][i].low_range == null) {
							investigationsInfo['packageItems'][i].low_range = '';
						}
						html += '<td><input type="text" name="range[' + i + '][low_range]" class="form-control text-center" onkeypress="return decimal();" value="' + investigationsInfo['packageItems'][i].low_range + '"></td>';

						if (investigationsInfo['packageItems'][i].high_range == null) {
							investigationsInfo['packageItems'][i].high_range = '';
						}
						html += '<td><input type="text" name="range[' + i + '][high_range]" class="form-control text-center" onkeypress="return decimal();" value="' + investigationsInfo['packageItems'][i].high_range + '"></td>';

						html += '<td>';
						if (unitsCount > 0) {
							html += '<select name="range[' + i + '][units]" id="' + investigationsInfo['packageItems'][i].clinic_investigation_id + '_unit_sb" class="form-control sb2" onchange="showTxtBx(' + investigationsInfo['packageItems'][i].clinic_investigation_id + ',\'unit\',' + i + ')">';
							html += '<option value="">Select Unit</option>';
							for (x = 0; x < unitsCount; x++) {
								if (unit == investigationsInfo['units'][x]['units']) {
									var selected = 'selected';
								} else {
									var selected = '';
								}
								html += '<option value="' + investigationsInfo['units'][x]['units'] + '" ' + selected + '>' + investigationsInfo['units'][x]['units'] + '</option>';
							}
							html += '<option value="Other">Other</option>';
							html += '</select>';
							html += '<input type="text" class="form-control pull-left" style="display:none; width:78%" id="' + investigationsInfo['packageItems'][i].clinic_investigation_id + '_unit_tb" placeholder="Specify..." value="' + investigationsInfo['packageItems'][i].units + '"><i id="' + investigationsInfo['packageItems'][i].clinic_investigation_id + '_unit_icon" class="fas fa-times-circle pull-right error" style="display:none; padding-top:10px; font-size:30px; cursor:pointer;" onclick="closeTxtBx(\'' + investigationsInfo['packageItems'][i].clinic_investigation_id + '\',\'unit\')"></i>';
						} else {
							html += '<input type="text" name="range[' + i + '][units]" class="form-control" id="' + investigationsInfo['packageItems'][i].clinic_investigation_id + '_unit_tb" placeholder="Specify..." value="">';
						}
						html += '</td>';
						
						if (investigationsInfo['packageItems'][i].other_information == null) {
							investigationsInfo['packageItems'][i].other_information = '';
						}
						html += '<td><textarea name="range[' + i + '][remarks]" rows="1" cols="25" class="form-control">' + investigationsInfo['packageItems'][i].remarks + '</textarea></td>';
					} else if (template_type == 'General') {
						html += '<td><textarea name="range[' + i + '][remarks]" rows="2" cols="50" class="form-control">' + investigationsInfo['packageItems'][i].remarks + '</textarea></td>';
					}
					html += '</tr>';
				}
				html += '</table>';

				$("#" + type + "InfoDiv").html(html);
				$("#" + modal).modal();
			}
		});
	}


	// function showTxtBx(id, type, cntr){
	// 	console.log('Id: '+id+', Type: '+type+', Counter: '+cntr);
	// 	var selectedMethod = $("#"+id+"_"+type+"_sb").val();
	// 	var oldMethod = $("#"+id+"_"+type+"_tb").val();

	// 	if(selectedMethod == 'Other'){
	// 		console.log('1....');
	// 		console.log("Selected: "+selectedMethod);
	// 		// var oldValue = $("#"+id+"_"+type+"_tb").val();
	// 		console.log("Old Value: "+oldMethod);
	// 		$("#"+id+"_"+type+"_tb").attr('type','text');
	// 		console.log('3...');
	// 		$("#"+id+"_"+type+"_tb").focus();
	// 		console.log('4...');
	// 		$("#"+id+"_"+type+"_icon").show();
	// 		console.log('5...');
	// 		$("#"+id+"_"+type+"_tb").val('');
	// 		console.log('6...');
	// 		$("#"+id+"_"+type+"_sb").hide();
	// 	}else{
	// 		console.log('2....');
	// 		console.log("Selected: "+selectedMethod);
	// 		console.log("Old Value: "+oldMethod);
	// 		var val = $("#"+id+"_"+type+"_sb").val();
	// 		console.log("Current Selected value: "+val);
	// 		$("#"+id+"_"+type+"_tb").val(val);
	// 		console.log('done');
	// 	}		
	// }

	function show_hide_method() {
		var selectedMethod = $("#method_sb").val();
		var oldMethod = $("#method_tb").val();

		if (selectedMethod == 'Other') {

			$("#method_tb").prop('type', 'text');
			$("#method_tb").focus();
			$("#method_icon").show();
			$("#method_tb").val('');
			$(".methodCls").val('');
			$("#method_sb").hide();
		} else {
			$("#method_tb").prop('type', 'hidden');
			var val = $("#method_sb").val();
			$("#method_tb").val(val);
			$(".methodCls").val(val);
			console.log('done');
		}
	}

	// function closeTxtBx(id, type){
	// 	$("#"+id+"_"+type+"_icon").hide();
	// 	$("#"+id+"_"+type+"_tb").attr('type','hidden');
	// 	$("#"+id+"_"+type+"_sb").prop('selectedIndex',0);
	// 	$("#"+id+"_"+type+"_sb").show();
	// }

	function closeMethodTxtBx(id, type) {
		$("#method_icon").hide();
		$("#method_tb").attr('type', 'hidden');
		$("#method_sb").val($("#old_method_tb").val());
		$("#method_sb").show();
		$(".methodCls").val($("#old_method_tb").val());
		$("#method_tb").val($("#old_method_tb").val());
	}

	function methodUpdate(val) {
		$(".methodCls").val(val);
	}
</script>