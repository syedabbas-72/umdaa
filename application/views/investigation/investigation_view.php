<div class="row page-header">

   <div class="col-lg-6 align-self-center ">       

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="#">Home</a></li>

          <li class="breadcrumb-item"><a href="<?php echo base_url('Patients/');?>">Patients</a></li>

          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          

        </ol>

  </div>

</div>

              <!-- content start -->

<section class="main-content">

<div class="row">

  <div class="col-md-12">

    <!-- card start -->

    <div class="card">

<!--       <div class="card-header card-default">Inline form</div>  -->

       <div class="card-body">
        <!-- <div class="responsive"> -->
          <div class="col-md-12 row">
            <div class="col-md-6 row">
              <label class="col-md-3"><b>PATIENT NAME :</b></label><div class="col-md-3">test1
            </div></div>
            <div class="col-md-6 row">
                <label class="col-md-3"><b>UMR NO :</b></label><div class="col-md-3">test2
            </div></div>
          </div>

          <div class="col-md-12 row">
            <div class="col-md-6 row">
              <label class="col-md-3"><b>AGE :</b></label><div class="col-md-3">test1
            </div></div>
            <div class="col-md-6 row">
                <label class="col-md-3"><b>GENDER :</b></label><div class="col-md-3">test2
            </div></div>
          </div>        
         
        </div>
      </div>
    </div>
  </div>
                      <!--next row-->
<div class="row">
  <div class="col-md-12">
    <div class="card">
       <div class="card-body">
          <!--tab panel start-->
        <div class="tabs">
              <ul class="nav nav-tabs">
                  <li class="nav-item" role="presentation"><a class="nav-link active" href="#vitals" aria-controls="home" role="tab" data-toggle="tab" aria-selected="false">VITALS</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" href="#consent" aria-controls="profile" role="tab" data-toggle="tab" aria-selected="true">CONSENT FORM</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" href="#previous" aria-controls="messages" role="tab" data-toggle="tab" aria-selected="false">PREVIOUS REPORT</a></li>
                  <li class="nav-item" role="presentation"><a class="nav-link" href="#hopi" aria-controls="messages" role="tab" data-toggle="tab" aria-selected="false">HOPI</a></li>
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="vitals">
                  <h5><u>VITALS</u></h5>

                  <div class="form-group row">
                    <label for="pr" class="col-md-2">PR</label>
                    <div class="col-md-2">
                      <input type="text" name="pr" value="" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="bp" class="col-md-2">BP</label>
                    <div class="col-md-2">
                      <input type="textbox" name="BP" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="temparature" class="col-md-2">TEMPERATURE</label>
                    <div class="col-md-2">
                      <input type="textbox" name="temparature" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="sa" class="col-md-2 ">SA02</label>
                    <div class="col-md-2">
                      <input type="textbox" name="sa" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="height" class="col-md-2">HEIGHT</label>
                    <div class="col-md-2">
                      <input type="textbox" name="height" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="weight" class="col-md-2">WEIGHT</label>
                    <div class="col-md-2">
                      <input type="textbox" name="weight" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="bmi" class="col-md-2">BMI</label>
                    <div class="col-md-2">
                      <input type="textbox" name="bmi" class="form-control">
                  </div>
                  </div>
                  <table id="other" width="50%">
                    <tr>
                      <td><label style="width: 120px;">OTHERS</label></td>
                      <td><Input type="textbox" name="" class="form-control" style="width: 210px;"></td>
                      <td><button onclick="add_row('other');">+</button></td>
                    </tr> 
                  </table>
              </div>
                  <div role="tabpanel" class="tab-pane" id="consent">
                   consent
                  </div>
                  <div role="tabpanel" class="tab-pane" id="previous">
                   previous
                  </div>
                  <div role="tabpanel" class="tab-pane" id="hopi">
                   HOPI
                  </div>
              </div>
        </div>   
             <!--tab panel end-->

        </div>
      </div>
    </div>
  </div>

</section>
<script type="text/javascript">
  function add_row(id){
    count=$("#"+id).find('tr').length;   
    $("#"+id).append('<tr id="'+id+'_'+count+'"><td><label style="width: 120px;">OTHERS</label></td><td><input type="text" name="" class="" style="width: 210px;"></td><td> <button onclick=add_row("'+id+'");>+</button><button id="test" onclick=del_row("'+id+'_'+count+'")>-</button></td></tr>');

  }
  function del_row(id){
    $("#"+id).remove();
  }
</script>