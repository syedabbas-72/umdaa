<div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="#">INDENT LIST</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          
      </ol>
  </div>
</div>
   


<section class="main-content">
<div class="row">
<div class="col-md-12">
    <div class="card">

        <div class="card-body">
             <div class="col-lg-12 align-self-center text-right">
                 <a href="<?= base_url('Indent/indent_add'); ?>" class="btn btn-primary btn-rounded box-shadow btn-icon"><i class="fa fa-plus"></i> ADD</a> 
        </div> 
            <table id="doctorlist" class="table table-striped dt-responsive nowrap">
                <thead>
    <tr>
        <th>S.No:</th>
        <th>Indent Number</th>
        <th>Indent Date</th>
       <!-- <th>Doctor</th>-->
        <th>Drug Count</th>
        <!--<th>Payment</th>-->
        
        <th>Action</th>
                               
    </tr>
</thead>
<tbody>
   <?php $i=1; foreach ($indent_list as $value) { ?> 
    <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $value->indent_no;?></td>
        <td><?php echo date("d-m-Y",strtotime($value->indent_date));?></td>
        <td><?php echo $value->licnt; ?></td>
		<td><a href="<?php echo base_url('Indent/indent_view/'.$value->pharmacy_indent_id);?>"><i class="fa fa-eye"></i></a></td>
    </tr>
  <?php $i++;} ?>
               
                    
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>
</section>
 <script>
  $(document).ready(function () {
      $('#doctorlist').dataTable();
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



