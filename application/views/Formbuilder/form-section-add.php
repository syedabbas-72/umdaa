        
 <script type="text/javascript">

	$(document).ready(function(){
		$('.col_row_count').hide();		
	});

	function validateForm(){
		if($("#section_format_type_sb").val() == ''){
			alert("Please select the Section format");
			$("#section_format_type_sb").focus();
			false();
		}else{
			if($("#section_format_type_sb").val() == 'tabular'){
				if($('#columns_tb').val() == ''){
					alert("Please provide number of columns");
					$('#columns_tb').focus();
					false();
				}else if($('#rows_tb').val() == ''){
					alert("Please provide number of rows");
					$('#rows_tb').focus();
					false();
				}
			} // go create a new section
			newSection();
		}
	}

	function newSection(format = null){

		// Some hidden fields which captures the form level information
		html = '<input type="hidden" name="section[title]" value="'+$('#section_title_tb').val()+'" >';
		html += '<input type="hidden" name="section[brief]" value="'+$('#section_brief_tb').val()+'" >';
		html += '<input type="hidden" name="section[format_type]" value="'+$('#section_format_type_sb').val()+'" >';
		html += '<input type="hidden" name="section[columns]" value="'+$('#columns_tb').val()+'" >';
		html += '<input type="hidden" name="section[rows]" value="'+$('#rows_tb').val()+'" >';
		html += '<input type="hidden" name="section[form_id]" value="'+$('#form_id').val()+'" >';
		
		

		if(format == null){
			format = $('#section_format_type_sb').val();	
		}
		
		if(format == 'tabular') { // If format is tabular

			rows = $('#rows_tb').val();
			columns = $('#columns_tb').val();

			if(rows == '' || columns == ''){
				alert("Please provide with column and row values");
				$().addclass('errBdr');
				exit();
			} 

			html += "<h3>"+$('#section_title_tb').val()+"</h3>";
			html += "<p>"+$('#section_brief_tb').val()+"</p>"; 

			html += "<table cellspacing='0' cellpadding='0' class='table table-stripped table-bordered'>";

			// Create rows
			for(x=0; x<rows; x++) { // rows
				if(x==0){// header row
					html += "<tr>";
					for(y=0; y<columns; y++) { // columns
						html += "<th><input type='text' class='form-control hdr_input' id='c"+y+"' onkeyup=\"return labelName(this.id,'col',"+rows+","+columns+");\"></th>";	
					}
					html += '</tr>';					
				}else{
					html += "<tr>";
					for(y=0; y<columns; y++) { //columns 

						record_no = $('#record_no_tb').val();
						var i = $('#record_no_tb').val();

						if(y == 0){
							html += "<td><input type='text' id='r"+x+"' class='form-control hdr_input' onkeyup=\"return labelName(this.id,'row',"+rows+","+columns+");\"></td>";
						}else{
							html += "<td id='c"+y+"r"+x+"_td'>";
							html += "<input type='hidden' name='record["+record_no+"][field][field_name]' id='c"+y+"r"+x+"' class='form-control'>";
							html += "<select id='c"+y+"r"+x+"_sb' name='record["+record_no+"][field][field_type]' class='form-control' onchange=\"return populateSelected(this.value,'c"+y+"r"+x+"_td','"+i+"',0,'c"+y+"r"+x+"');\">";
							html += "<option value=''>Select Type</option>";
							html += "<option value='radio'>Radio Button</option>";
							html += "<option value='checkbox'>Check Box</option>";
							html += "<option value='spinner'>Spinner</option>";
							html += "<option value='text'>Text</option>";
							html += "<option value='sub title'>Sub title</option>";	
							html += "</select>";
							html += "<input type='hidden' name='record["+record_no+"][field][column_index]' value='"+y+"'>";
							html += "<input type='hidden' name='record["+record_no+"][field][row_index]' value='"+x+"'>";
							html += "</td>";

							i++;					
							// increment the tex box value by one(No. of records).		
							$('#record_no_tb').val(i);
						}
					}
					html += '</tr>';					
				}
			}

			html += "<tr><td colspan='"+columns+"'><input type='submit' class='btn_success' name='submit' value='Submit'></td></tr>"
			html += "</table>";
			//html += "</form>";

		}else if(format == 'normal') { // If format is normal

			// get the nmber of records value into var record_no
			record_no = $("#record_no_tb").val();
			var i = record_no;
			
			// Need to Populate with Label Information & Optoins with dependency information
			html += "<h3>"+$('#section_title_tb').val()+"</h3>";
			html += "<p>"+$('#section_brief_tb').val()+"</p>"; 
			//html += "<form method='post' action='build.php'>";

			// normal section div
			html += "<div id='normal_section_div'>";

			// dynamic id div start
			html += "<div class='"+record_no+"_div form-inline dependencyDiv' id='"+record_no+"_div'>";

			// Label Text box
			html += "<div class='form-group'>";
			// Ray image
  			html += "<img src='<?php echo base_url('assets/images/ray.png')?>' text-align='left'>";
			html += "<input type='text' name='record["+record_no+"][field][field_name]' id='"+record_no+"_tb' value='' placeholder='Label Title' class='form-control'>";
			html += "</div>&nbsp;&nbsp;";

			// Select box 
			html += "<div class='form-group'>";
			html += "<select id='"+record_no+"_sb' name='record["+record_no+"][field][field_type]' class='form-control' onchange=\"return populateSelected(this.value,'"+record_no+"_div','"+record_no+"',0,'"+record_no+"_tb');\">";
			html += "<option value=''>Select Type</option>";
			html += "<option value='radio'>Radio Button</option>";
			html += "<option value='checkbox'>Check Box</option>";
			html += "<option value='spinner'>Spinner</option>";
			html += "<option value='text'>Text</option>";
			html += "<option value='sub title'>Sub title</option>";		
			html += "</select>";
			html += "</div>&nbsp;&nbsp;";
			
			// increase the value of i by 1
			i++;

			// plus button for to add
  			html += "<button type='button' class='plus_button' id='"+record_no+"_plus_btn' onclick=\"return newLabelControl();\" style='width:20px; text-align:center'>&nbsp;</button>";

  			// dynamic id div closed
			html += "</div>";
			
			// normal section div closed
			html += "</div>";

			// creating a new div to hold new label control
			//html += "<div class='"+record_no+"_div form-inline dependencyDiv' id='"+i+"_div'></div>";

			html += "<div class='form-group col-sm-12' style='padding-top: 10px;'><div>";
			html += "<input type='submit' name='submit' class='btn btn-success' value='Save Section' />";
			html += "</div>";
			//html += "</form>";

			// increment the number of records value by 1 
			$('#record_no_tb').val(i);
			

		}else if($('#section_format_type_sb').val() == '') {
			$('#section_format_type_sb').css('border-color','red');
			alert("Please choose the section format type");
			exit();
		}
		
		$('#sectionDiv').remove();
		$('#formBuilderDiv').append(html);

	}


	// this function creates the following
	// a text box to capture the label
	// a select box to select the type of label control say Radio button, Check Box & text box
	function newLabelControl(){

		// get the nmber of records value into var record_no
		record_no = $("#record_no_tb").val();
		var i = record_no;

		html = '';

		// dynamic id div start
		html += "<div class='"+record_no+"_div form-inline dependencyDiv' id='"+record_no+"_div'>";

		// Label Text box
		html += "<div class='form-group'>";
		
		// Ray image
		html += "<img src='<?php echo base_url('assets/images/ray.png')?>' text-align='left'>";

		html += "<input type='text' name='record["+record_no+"][field][field_name]' id='"+record_no+"_tb' value='' placeholder='Label Text' class='form-control'>";
		html += "</div>&nbsp;&nbsp;";

		// Select box 
		html += "<div class='form-group'>";
		html += "<select id='"+record_no+"_sb' name='record["+record_no+"][field][field_type]' class='form-control' onchange=\"return populateSelected(this.value,'"+record_no+"_div','"+record_no+"',0,'"+record_no+"_tb');\">";
		html += "<option value=''>Select Type</option>";
		html += "<option value='radio'>Radio Button</option>";
		html += "<option value='checkbox'>Check Box</option>";
		html += "<option value='spinner'>Spinner</option>";	
		html += "<option value='text'>Text</option>";
		html += "<option value='sub title'>Sub title</option>";
		html += "</select>";
		html += "</div>&nbsp;&nbsp;";
		
		// increase the value of i by 1
		i++;

		// plus button for to add
		html += "<button type='button' class='plus_button' id='"+record_no+"_plus_btn' onclick=\"return newLabelControl();\" style='width:20px; text-align:center'>&nbsp;</button>&nbsp;&nbsp;";
		
		// minus button for to remove the div
		html += "<button type='button' class='minus_button' id='"+record_no+"_minus_btn' onclick=\"return removeSelected('"+record_no+"_div');\" style='width:20px; text-align:center'>&nbsp;</button>";

		// dynamic id div closed
		html += "</div>";

		// increment the number of records value by 1 
		$('#record_no_tb').val(i);
		$('#normal_section_div').append(html);

	}

	// this function populate the new section
	function populateSelected(selected_value, td_id, record_array_key, options_array_key = 0, field_name_id = null) {

		// Check if the Field Name is filled or left empty
		field_name = $('#'+field_name_id).val();

		optionNo = $('#option_no_tb').val();
		optionNo = parseInt(optionNo);
		
		$('#option_no_tb').val(optionNo + 1);
		
		$('#'+options_array_key+'_'+td_id+'_plus_btn').hide(); // hides the Plus button of the previous li
		$('#'+options_array_key+'_'+td_id+'_minus_btn').show(); // Shows up the Minus button to the previous li

		options_array_key++;
		if(selected_value == 'radio') {
		
			$("."+td_id+"_cb_components").remove(); // removes any check box components belongs to td_id shell
			$("."+td_id+"_tb_components").remove(); // removes any text box field components belongs to td_id shell
			$("."+td_id+"_img_components").remove(); // removes any text box field components belongs to td_id shell

			option_no = $('#option_no_tb').val();
		
			// Adding new dynamic component
  			newComponent = "<div class='"+td_id+"_rb_components populateDiv form-inline' id='"+td_id+"_"+options_array_key+"_div'>";
  			// Ray image
  			newComponent += "<img src='<?php echo base_url('assets/images/ray.png')?>' text-align='left'>";
  			// Text field to input option name
  			newComponent += "<input id='"+td_id+"_"+option_no+"_tb' type='text' name=\"record["+record_array_key+"][options]["+options_array_key+"][option_name]\" value='' placeholder='Option name' class='form-control'>&nbsp;&nbsp;";
  			// Hidden component to store 0 if there is no default option chosen
  			newComponent += "<input type='hidden' value=0 name=\"record["+record_array_key+"][options]["+options_array_key+"][option_default]\"><input type='checkbox' name=\"record["+record_array_key+"][options]["+options_array_key+"][option_default]\" value=1 class='form-control'> DFLT &nbsp;&nbsp;";
  			// Dependency field to advice dependency for this field
  			newComponent += "<input type='hidden' name=\"record["+record_array_key+"][options]["+options_array_key+"][dependency]\" value=0><input id='"+td_id+"_"+optionNo+"_cb' type='checkbox' name=\"record["+record_array_key+"][options]["+options_array_key+"][dependency]\" value=1 onchange=\"return dependencySection(this.id,'"+td_id+"_"+options_array_key+"_div','"+record_array_key+"','"+options_array_key+"','"+field_name_id+"','"+td_id+"_"+option_no+"_tb');\" class='form-control'> DEP &nbsp;&nbsp;";
  			// Plus button for to add
  			newComponent += "<button type='button' class='plus_button' id='"+options_array_key+"_"+td_id+"_plus_btn' onclick=\"return populateSelected('"+selected_value+"','"+td_id+"','"+record_array_key+"','"+options_array_key+"','"+field_name_id+"');\" style='width:20px; text-align:center'>&nbsp;</button>";
  			// Minus button for to remove the div
  			newComponent += "<button type='button' class='minus_button' id='"+options_array_key+"_"+td_id+"_minus_btn' onclick=\"return removeSelected('"+td_id+"_"+options_array_key+"_div');\" style='width:20px; text-align:center'>&nbsp;</button>";
  			newComponent += "</div>";

  			//alert(newComponent);
			$('#'+td_id).append(newComponent);	
			$("#"+td_id+"_"+option_no+"_tb").focus();
			$('#'+options_array_key+'_'+td_id+'_minus_btn').hide();

		}else if(selected_value == 'checkbox') {

			$("."+td_id+"_rb_components").remove(); // removes any check box components belongs to td_id shell
			$("."+td_id+"_tb_components").remove(); // removes any text box field components belongs to td_id shell
			//$("."+td_id+"_img_components").remove(); // removes any text box field components belongs to td_id shell

			option_no = $('#option_no_tb').val();
		
			// Adding new dynamic component
  			newComponent = "<div class='"+td_id+"_cb_components populateDiv form-inline' id='"+td_id+"_"+options_array_key+"_div'>";
  			// Ray image
  			newComponent += "<img src='<?php echo base_url('assets/images/ray.png')?>' text-align='left'>";
  			// Text field to input option name
  			newComponent += "<input id='"+td_id+"_"+option_no+"_tb' type='text' name=\"record["+record_array_key+"][options]["+options_array_key+"][option_name]\" value='' placeholder='Option name' class='form-control'>&nbsp;&nbsp;";
  			// Hidden component to store 0 if there is no default option chosen
  			newComponent += "<input type='hidden' value=0 name=\"record["+record_array_key+"][options]["+options_array_key+"][option_default]\"><input type='checkbox' name=\"record["+record_array_key+"][options]["+options_array_key+"][option_default]\" value=1 class='form-control'> DFLT &nbsp;&nbsp;";
  			// Dependency field to advice dependency for this field
  			newComponent += "<input type='hidden' name=\"record["+record_array_key+"][options]["+options_array_key+"][dependency]\" value=0><input id='"+td_id+"_"+optionNo+"_cb' type='checkbox' name=\"record["+record_array_key+"][options]["+options_array_key+"][dependency]\" value=1 onchange=\"return dependencySection(this.id,'"+td_id+"_"+options_array_key+"_div','"+record_array_key+"','"+options_array_key+"','"+field_name_id+"','"+td_id+"_"+option_no+"_tb');\" class='form-control'> DEP &nbsp;&nbsp;";
  			// Plus button for to add
  			newComponent += "<button type='button' class='plus_button' id='"+options_array_key+"_"+td_id+"_plus_btn' onclick=\"return populateSelected('"+selected_value+"','"+td_id+"','"+record_array_key+"','"+options_array_key+"','"+field_name_id+"');\" style='width:20px; text-align:center'>&nbsp;</button>";
  			// Minus button for to remove the div
  			newComponent += "<button type='button' class='minus_button' id='"+options_array_key+"_"+td_id+"_minus_btn' onclick=\"return removeSelected('"+td_id+"_"+options_array_key+"_div');\" style='width:20px; text-align:center'>&nbsp;</button>";
  			newComponent += "</div>";

  			//alert(newComponent);
			$('#'+td_id).append(newComponent);
			$("#"+td_id+"_"+option_no+"_tb").focus();	
			$('#'+options_array_key+'_'+td_id+'_minus_btn').hide();

		}else if(selected_value == 'text') {

			$("."+td_id+"_rb_components").remove(); // removes any check box components belongs to td_id shell
			$("."+td_id+"_cb_components").remove(); // removes any check box components belongs to td_id shell
				
		}
	}

	// this function will create the dependency Section
	function dependencySection(id,div_id,record_array_key, options_array_key = 0, parent_field_name_id = null, parent_option_name_id = null) {
	
		if($('#'+id).prop("checked") == true){ // Create a dynamic div
			record_no = $('#record_no_tb').val();
			i = record_no;

			$("#record_no_tb").val(record_no);
			html = "<div class='"+div_id+"_div form-inline dependencyDiv' id='"+div_id+"_"+record_no+"_div'>";
			html += "<div class='form-group'>";
			// Ray image
  			html += "<img src='<?php echo base_url('assets/images/ray.png')?>' text-align='left'>";
			html += "<input type='text' name='record["+record_no+"][field][field_name]' id='"+div_id+"_"+record_no+"_tb' value='' placeholder='Label Title' class='form-control'>";
			html += "</div>&nbsp;&nbsp;";
			html += "<div class='form-group'>";
			html += "<select id='"+div_id+"_"+record_no+"_sb' name='record["+record_no+"][field][field_type]' class='form-control' onchange=\"return populateSelected(this.value,'"+div_id+"_"+record_no+"_div','"+record_no+"',0,'"+div_id+"_"+record_no+"_tb');\">";
			html += "<option value=''>Select Type</option>";
			html += "<option value='radio'>Radio Button</option>";
			html += "<option value='checkbox'>Check Box</option>";
			html += "<option value='text'>Text</option>";
			html += "</select>";
			html += "</div>&nbsp;&nbsp;";

			// plus button for to add
			html += "<button type='button' class='plus_button' id='"+div_id+"_"+record_no+"_plus_btn' onclick=\"return dependencySection('"+id+"','"+div_id+"','"+record_array_key+"','"+options_array_key+"','"+parent_field_name_id+"','"+parent_option_name_id+"');\">&nbsp;</button>&nbsp;&nbsp;";
			
			// minus button for to remove the div
			html += "<button type='button' class='minus_button' id='"+div_id+"_"+record_no+"_minus_btn' onclick=\"return removeSelected('"+div_id+"_"+record_no+"_div');\">&nbsp;</button>";

			html += "<input type='hidden' name='record["+record_no+"][field][parent_field_name]' value='"+$("#"+parent_field_name_id).val()+"'>";
			html += "<input type='hidden' name='record["+record_no+"][field][parent_option_name]' value='"+$("#"+parent_option_name_id).val()+"'>";
			html += "</div>";
			$("#"+div_id).append(html);	

			i++;
			// increment the record no by one
			$('#record_no_tb').val(i);

		}else{ // remove the dynamically added div when unchecked
			// alert('unchecked');
			$("#"+div_id+"_div").remove();
		}
		
	}

	// this function will create a new label as per the column / row
	function labelName(id,hdr_type,row_count,column_count){

		if(hdr_type=='col'){
			for (i = 1; i < row_count; i++){
				//rowHeader_ColumnHeader
				$('#'+id+'r'+i).val($('#r'+i).val()+"_"+$('#'+id).val());
			}
		}else if(hdr_type=='row'){
			for (j = 1; j < column_count; j++){
				//rowHeader_ColumnHeader
				$('#c'+j+id).val($('#'+id).val()+"_"+$('#c'+j).val());	
			}
		}
	}

	// this function will remove the selected section from the page
	function removeSelected(id) {
		// alert(id);
		$('#'+id).remove();
	}

	// this function will show up the form
	function showForm(id){
		if($('#'+id).val() == 'tabular'){
			$('.col_row_count').show();
		}else{
			$('.col_row_count').hide();
		}
	}

</script>


<style type="text/css">
@media (min-width:768px){.form-inline {display:block !important} .form-inline .form-group{display:inline-block;margin-bottom:0;vertical-align:middle}.form-inline .form-control{display:inline-block;width:auto;vertical-align:middle}.form-inline .input-group{display:inline-table;vertical-align:middle}.form-inline .input-group .input-group-addon,.form-inline .input-group .input-group-btn,.form-inline .input-group .form-control{width:auto}.form-inline .input-group>.form-control{width:100%}.form-inline .control-label{margin-bottom:0;vertical-align:middle}.form-inline .radio,.form-inline .checkbox{display:inline-block;margin-top:0;margin-bottom:0;vertical-align:middle}.form-inline .radio label,.form-inline .checkbox label{padding-left:0}.form-inline .radio input[type=radio],.form-inline .checkbox input[type=checkbox]{position:relative;margin-left:0}.form-inline .has-feedback .form-control-feedback{top:0}} 
.hdr_input{
	background: #f9f9f9;
	border:1px solid #f1f1f1;
	border-bottom:1px solid #666;
	border-radius: 0;
	text-align: center;
	font-weight: bold;
	color: #000;
	padding: 5px;
	box-shadow: none;
}
.th{
	padding: 5px;
	text-align: center;
}
.l_arrow{
	background: url(<?php echo base_url("assets/images/arw.png")?>) no-repeat top left;
}	
.populateDiv{
	padding: 10px 5px 0px 4px;
	background: url(<?php echo base_url("assets/images/bdr.png")?>) repeat-y top left;
	margin-left: 30px;	
}
.dependencyDiv{
	padding: 10px 5px 10px 4px;
	background: url(<?php echo base_url("assets/images/bdr.png")?>) repeat-y top left;
	margin-left: 30px;
}

.plus_button, .minus_button{
	background: url(<?php echo base_url("assets/images/plus_icon.png")?>) repeat-y top left;
	background-size: 30px;
	width: 30px !important;
	height: 30px !important;
	border:none;
	box-shadow: none;
}
.minus_button{
	background:url(<?php echo base_url("assets/images/minus_icon.png")?>) repeat-y top left;
	background-size: 30px;
}

input[type=checkbox]{
	border:1px solid red;
}

.errBdr{
	border:1px solid red;
}

</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">SECTION</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
			
            <div class="row">             
                <div class="container-fluid">		

		<form class="form" action="<?php echo base_url('FormBuilder/saveFormSection');?>" method="post">
		<input type="hidden" id="record_no_tb" value="1">
<input type="hidden" id="option_no_tb" value="1">
<input type="hidden" name="form_id" value="<?php echo $form_id;?>" id='form_id'>
			<div id="sectionDiv">
			
			<div class="row col-md-12">
				<div class="form-group col-sm-4" id="newSectionTitle">
					<label for="section_title" class="control-label">Section Title</label>
					<div>
						<input type="text" class="form-control section" id="section_title_tb" name="title">
					</div>
				</div>
				<div class="form-group col-sm-4" id="brief">
					<label for="brief" class="control-label">Brief</label>
					<div>
						<input type="text" class="form-control section" id="section_brief_tb" name="brief">
					</div>
				</div>
				</div>
				
				<div class="row col-md-12">
				<div class="form-group col-sm-4" id="newSectionType">
					<label for="section_title" class="control-label">Format</label>
					<div>
						<select class="form-control section" id="section_format_type_sb" name="form_type" onchange="return showForm(this.id);" required="required">
							<option value="">Select Format</option>
							<option value="normal">Normal</option>
							<option value="tabular">Tabular</option>
						</select>
					</div>
				</div>
				<div class="form-group col-sm-4 col_row_count">
					<label for="brief" class="control-label">Columns</label>
					<div>
						<input type="text" class="form-control section" id="columns_tb" name="columns" required="required">
					</div>
				</div>
				<div class="form-group col-sm-4 col_row_count">
					<label for="brief" class="control-label">Rows</label>
					<div>
						<input type="text" class="form-control section" id="rows_tb" name="rows" required="required">
					</div>	
				</div>
				</div>
				<div class="form-group col-sm-12" style="padding-top: 10px;">
					<div>
						<button type="button" class="btn btn-success section" name="createSection" onclick="return validateForm();">Create Section</button>
					</div>
				</div>
			</div>

			<div id="formBuilderDiv" style="clear:both">
				<!-- Dynamic content generates here -->
			</div>
			
		</form>
	</div>
                </div>
            </div>
        </section>  


