<div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url('Dashboard')?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="<?=base_url('Indent')?>">INDENT</a>&nbsp;<i class="fa fa-angle-right"></i></li>  
          <li><a class="active">INDENT VIEW</a></li>         
          
      </ol>
  </div>
</div>
   
<section class="main-content">
<div class="row">
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <h4>Indent Line Items</h4>
            <table id="indentlist" class="table table-bordered dt-responsive nowrap customTable">
                <thead>
    <tr>
        <th>S.No:</th>
        <th>Drug Name</th>
        <th>Quantity</th>
        <th>Vendor Name</th>
        <th>Contact Person</th>
       
                               
    </tr>
</thead>
<tbody>
   <?php $i=1; foreach ($indent_info as $value) { 
    $cpi = $this->db->query("select * from clinic_pharmacy_inventory where drug_id='".$value->drug_id."'")->row();
    // echo $this->db->last_query();
    $vendor_list = $this->db->query("select * from vendor_master where vendor_id='".$value->vendor_id."'")->row();
    // echo $this->db->last_query();
    ?> 
    <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $value->trade_name;?></td>
        <td><?php echo $value->quantity;?></td>
        <td><?php echo $vendor_list->vendor_storeName;?></td>
        <td><?php echo $vendor_list->vendor_name;?></td>
        
    </tr>
  <?php $i++;} ?>
               
                    
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>
</section>
<script type="text/javascript">
    $(document).ready(function(){
        $('#indentlist').DataTable();
    });
</script>