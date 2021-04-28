<?php include('inc/head.php'); ?>

<script type="text/javascript">

  $(document).ready(function(){
    $(".dependencyDiv").hide();
  });

    function dependencyDiv(id, div_id) {
        if ($("#"+id).prop("checked") == true) {
            $("#"+div_id).show();
        } else {
            $("#"+div_id).hide();
        }
    }

    function showHide(id,div_class){
      var target = $('#'+id).data('target-id'); 
    $('.'+div_class).hide(); 
    $('.'+div_class+'[data-target="'+target+'"]').show(); 
    }

</script>

<?php include('inc/header.php'); ?>

<?php 
if(isset($_GET['id'])) {
  $res = $obj->selectRecord('form','*',array('form_id'=>$_GET['id']));
  if($res->num_rows) {
    $rec = $res->fetch_assoc(); 
    extract($rec);
  }else{
    echo 'No form data, please check the form id and try again';
    exit();
  }
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="<?php echo ROOT; ?>">Dynamic Forms</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo ucfirst($form_name);?> Form</li>
  </ol>
</nav>

<div>
  <div class="left">
    <a href="index.php">Back</a>
  </div>
  <div class="right">
    <a href="section-add.php?id=<?php echo $_GET['id']; ?>">New Section</a>
  </div>
</div>
<h3><?php echo ucfirst($form_name);?> System Examination</h3>

<div class="SEForm">
<?php 
  $section_res = $obj->selectRecord('section','*',array('form_id'=>$_GET['id']));

  // is section exist
  if($section_res->num_rows){
    
    // Create HTML form
    echo "<form id='dynamicForm' name='dynamicForm' method='post' action='' class='form'>";
    
    while($section_rec = $section_res->fetch_assoc()){

      extract($section_rec);      

      if($format_type == 'normal'){

        // if section title exist
        if($title != '')
          echo "<div class='title'>".$title."</div>"; 

        // if brief exists
        if($brief != '')
          echo "<div class='brief'>".$brief."</div>";

        // get fields for the section whose parent_field_id is NULL
        $field_res = $obj->selectRecord('field','*',array('section_id'=>$section_rec['section_id'], 'parent_field_id' => 'NULL'));

        // if fields exists
        if($field_res->num_rows){

          while($field_rec = $field_res->fetch_assoc()){

            echo $obj->getField($field_rec);
            
          }
        }

      }elseif($format_type == 'tabular') { // close normal

        // draw table structure with columns and rows information
        echo '<pre>';
        print_r($section_rec);
        echo '</pre>';

        // create table
        echo "<table cellspacing='0' cellpadding='0'>";

        // if section title exist
        if($title != '')
          echo "<tr><td colspan='2'><h1>".$title."</h1></td></tr>"; 

        // if brief exists
        if($brief != '')
          echo "<tr><td colspan='2'><h3>".$brief."</h1></td></tr>"; 

        // get fields for the section
        $field_res = $obj->selectRecord('field','*',array('section_id'=>$section_rec['section_id']));

        // if fields exists
        if($field_res->num_rows){

          while($field_rec = $field_res->fetch_assoc()){

            // extracting array to variables
            echo '<pre>';
            print_r($field_rec);
            echo '</pre>';


          }
        }
      }

      // end HTML form
      echo "</form>";

    }

  }else{
    echo "No sections were created yet. Start creating by clicking on the <b>New Section</b>";
  }
      
?>  
</div>

<?php include('inc/footer.php'); ?>