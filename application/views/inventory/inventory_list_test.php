<!--  <style type="text/css">
 .nav.nav-tabs {
    float: left;
    display: block;
    margin-right: 20px;
    border-bottom:0;
    border-right: 1px solid #ddd;
    padding-right: 15px;
}
.glyphicon {
    position: relative;
    top: -10px;
    font-size: 30px;
    display: inline-block;
    font-family: 'Glyphicons Halflings';
    font-style: normal;
    font-weight: 700;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
.glyphicon-plus:before {
    content: "\002b";
}
.glyphicon-minus:before {
    content: "\2212";
}
.nav-tabs .nav-link {
    border: 1px solid transparent;
    color: #000;
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
    background: #ddd;
}

.nav-tabs .nav-link.active {
    color: #ffffff;
    background-color:#007bff !important;
    border-color: transparent !important;
}
.nav-tabs .nav-link {
	font-weight: 500;
    border: 1px solid transparent;
    border-top-left-radius: 0rem!important;
    border-top-right-radius: 0rem!important;
}
.tab-content>.active {
    display: block;
    min-height: 165px;
}
.nav-tabs .nav-item {
    margin-bottom: 2px;
}
.nav.nav-tabs {
    float: left;
    display: block;
    margin-right: 20px;
    border-bottom: 0;
    border-right: 1px solid transparent;
    padding-right: 15px;
}

.search-form .form-control {
    width: 500px;
    border: 0px;
    background: rgba(95, 90, 90, 0.18);
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    -ms-box-shadow: none;
    box-shadow: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 40px;
    color: #000;
    border-radius: 30px;
    font-size: 18px;
}
  .panel-group .panel {
        border-radius: 0;
        box-shadow: none;
        border-color: #EEEEEE;
    }

    .panel-default > .panel-heading {
        padding: 0;
        border-radius: 0;
        color: #212121;
        background-color: #FAFAFA;
        border-color: #EEEEEE;
    }

    .panel-title {
        font-size: 14px;
    }

    .panel-title > a {
        display: block;
        padding: 15px;
        color: #000;
        font-weight: 500;
        text-decoration: none;
    }

    .more-less {
        float: right;
        color: #212121;
    }
    .add-space { 
       display:inline-block; 
       margin-left: 40px; 
}
    .panel-default > .panel-heading + .panel-collapse > .panel-body {
        border-top-color: #EEEEEE;
    }
   </style>

 --> 
  <style type="text/css">
    .search-form .form-control {
    width: 500px;
    border: 0px;
    background: rgba(95, 90, 90, 0.18);
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    -ms-box-shadow: none;
    box-shadow: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 40px;
    color: #000;
    border-radius: 30px;
    font-size: 18px;
    margin-left: 288px;

}

  </style>




   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Inventory</a></li>
            <li class="breadcrumb-item active">Inventory List</li>
          </ol>
        </div>
        <!-- <div class="col-lg-6 align-self-center text-right">
          <a href="<?php echo base_url('inventory/inventory_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a><a href = ""  class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">BULK UPLOAD</a>
        </div> -->
    </div>



<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         <div class="container-fluid">
  <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#search" role="tab" aria-controls="search">All Medicines</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#shortage" role="tab" aria-controls="shortage">Shortage</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#expiring" role="tab" aria-controls="expiring">Expiring</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#expired" role="tab" aria-controls="settings">Expired</a>
  </li>
    
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="search" role="tabpanel">
		<div class="search-form hidden-xs">
		<form>
		  <label class="control-lable" style="color: #fff;">Country Name</label>
                    <input style="height:56px" type="text" id="country" autocomplete="off" name="country" class="form-control" placeholder="Type to get Medicines">        
                    <ul class="list-group txtcountry" style="margin-left:15px;margin-right:0px;" id="DropdownCountry"></ul>
		</form>
		</div>
	</div>
  <div class="tab-pane" id="shortage" role="tabpanel" style = "margin-top: 20px;">
  	<table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug Name</th>
                                <th>Composition</th>
                                <th>Formulation</th>
                                <th>MRP</th>
                                <th>Stock</th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php
  	
		if(count($shortage)>0)
		{		
				$i=1;
				foreach($shortage as $pav)
				{
?>
					<tr>
						<td><?php echo $i++; ?></td>
						<td><?php echo $pav->trade_name; ?></td>
						<td><?php echo $pav->composition; ?></td>
						<td><?php echo $pav->formulation; ?></td>
						<td><?php echo  round($pav->mrp,2) ?></td>
						<td><?php echo $pav->total_qty; ?></td>
						
						</tr>
			<?php	}
			
		}else{
			echo "<tr style='text-align:center'><td  colspan='6'>No Data Available</td></tr>";
		}
		?>
                            </tbody>
                        </table>
  	
  </div>
  <div class="tab-pane" id="expiring" role="tabpanel" style = "margin-top: 20px;">
  	<table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug Name</th>
                                <th>Composition</th>
                                <th>Formulation</th>
                                <th>Expiry Date</th>
                               
                            </tr>
                            </thead>
                            <tbody>
                           <?php
  	
		if(count($ytExpire)>0)
		{		
				$i=1;
				foreach($ytExpire as $pav)
				{
?>
					<tr>
						<td><?php echo $i++; ?></td>
						<td><?php echo $pav->trade_name; ?></td>
						<td><?php echo $pav->composition; ?></td>
						<td><?php echo $pav->formulation; ?></td>
						<td><?php echo $pav->expiry_date; ?></td>
					
						
						</tr>
			<?php	}
			
		}else{
			echo "<tr style='text-align:center'><td  colspan='5'>No Data Available</td></tr>";
		}
		?>
  </tbody>
                        </table>
  </div>
  <div class="tab-pane" id="expired" role="tabpanel" style = "margin-top: 20px;">
  	<table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug Name</th>
                                <th>Composition</th>
                                <th>Formulation</th>
                                <th>Expiry Date</th>
                               
                            </tr>
                            </thead>
                            <tbody>
                           <?php
  	
		if(count($expiry)>0)
		{		
				$i=1;
				foreach($expiry as $pav)
				{
?>
					<tr>
						<td><?php echo $i++; ?></td>
						<td><?php echo $pav->trade_name; ?></td>
						<td><?php echo $pav->composition; ?></td>
						<td><?php echo $pav->formulation; ?></td>
						<td><?php echo $pav->expiry_date; ?></td>
					
						
						</tr>
			<?php	}
			
		}else{
			echo "<tr style='text-align:center'><td  colspan='5'>No Data Available</td></tr>";
		}
		?>
  </tbody>
                        </table>
  </div>
  <div class="tab-pane" id="indent" role="tabpanel">
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

        	<?php $k=1; foreach ($indent_list as $key => $value) {
        		$clinic_name = $this->db->query("select * from clinics where clinic_id='".$value->clinic_id."'")->row(); ?>
        			

        <div class="panel panel-default">
        		<div class="panel-heading" role="tab" id="heading<?php echo $k; ?>">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $k; ?>" aria-expanded="true" aria-controls="panel<?php echo $k; ?>">
                        <i class="more-less glyphicon glyphicon-plus"></i>
                        <p><?php echo $clinic_name->clinic_name; ?> <span class="add-space"><?php echo $value->indent_date; ?></span></p>
                    </a>
                </h4>
            </div>
            <div id="panel<?php echo $k; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $k; ?>">
                <div class="panel-body" style = "margin-top: 20px;">
                     <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug Name</th>
                               	<th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
        <?php 

        $litems = $this->db->query("select * from pharmacy_indent_line_items where pharmacy_indent_id='".$value->pharmacy_indent_id."'")->result();
      	$i=1;
        foreach ($litems as $key => $items) { 
        $drug_name = $this->db->query("select trade_name from drug where drug_id='".$items->drug_id."'")->row();

        	?>
        	
       <tr>
                            		<td><?php echo $i++; ?></td>
                            		<td><?php echo $drug_name->trade_name; ?></td>
                            		<td><?php echo $items->quantity; ?></td>
                            	</tr>

       <?php  } ?>

       </tbody>
                        </table>
                </div>
            </div>
            </div>
              

        <?php $k++;	} ?>
        </div><!-- panel-group -->
            
            
                            	
                            		
                            
        

      

  
  </div>
 
</div>

<script>
 
</script>
</div>
                    </div>
                </div>
            </div>
          </div>

    </section>

 
 <script>
  $(document).ready(function () {
      $('#inventory_list1').dataTable();
  });
  $(document).ready(function () {
      $('#inventory_list2').dataTable();
  });
  $(document).ready(function () {
      $('#inventory_list3').dataTable();
  });
  $(document).ready(function () {
      $('#inventory_list4').dataTable();
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

 
            <script type="text/javascript">
            	

function toggleIcon(e) {
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('glyphicon-plus glyphicon-minus');
}
$('.panel-group').on('hidden.bs.collapse', toggleIcon);
$('.panel-group').on('shown.bs.collapse', toggleIcon);

            </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript">

    $("#country").keyup(function () {
    
    	if($("#country").val() == ""){
    		 $('#DropdownCountry').html("");
    	}
    	else{
    	var url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "POST",
            url: url+"inventory/autocomplete",
            data: {
                query: $("#country").val()
            },
            dataType: "json",
            success: function (data) {
            	
                $('#DropdownCountry').html("");
                $.each(data, function (key,value) {
                	
                    if (data.length > 0){
                    	$('#DropdownCountry').append('<li class="list-group-item" role="displayCountries" ><a>' + value['trade_name'] + '</a></li>');
                    }
                    else{
                    	$('#DropdownCountry').html("");
                    }
                         
                });
            }

        });
    }
});
    
    $('ul.txtcountry').on('click', 'li a', function () {
        $('#country').val($(this).text());
    });

    

</script>
<script type="text/javascript">
  $(document).on('change','#country',function(){
      var url1 = '<?php echo base_url();?>';
         var name = $('#country').val();
         $.ajax({
          url:url1+"inventory/search",
          type:'POST',
          contentType: 'application/json; charset=utf-8',

          dataType: "json",

          data:{
            search_name:name
          },
        
          success:function(data)
          {
            alert(data);
          }


         });
         
    });
</script>


