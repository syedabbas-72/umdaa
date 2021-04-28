
<style type="text/css">

.padded_table td, .padded_table th {
  padding: 6px;
}
.toppadding_10 {
  padding-top: 10px;
}
.allcaps {
  text-transform: uppercase;
}
.boldtext {
  font-weight: bold;

}
.allpadding_5 {
  padding: 5px;
}
input[type="radio"], input[type="checkbox"] {
  line-height: normal;
  margin: 4px;
}
.settingsInfoText {
  font-style: italic;
  padding-left: 25px;
  padding-bottom: 5px;
}
</style>

<div class="page-bar">
<div class="page-title-breadcrumb">
    <!-- <div class=" pull-left">
      <div class="page-title">Form Layouts</div>
    </div> -->
    <ol class="breadcrumb page-breadcrumb pull-left">
        <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>"><?php echo $clinic_name ?></a>&nbsp;<i class="fa fa-angle-right"></i>
        </li>  
        <li><a class="parent-item" href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
        </li>       
        <li class="active">PRINT</li>
    </ol>
</div>
</div>

      <section class="main-content">
        <div class="row">
      <div class="col-2 list-group ">
          <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
               <?php $this->view("settings/settings_left_nav"); ?>
              
          
          </div>
      </div>
      <div class="col-10">
          <div class="card">
            <div class="card-body">
                <form method="post" action="<?=base_url('Settings/pdfSettings')?>">
                  <input type="hidden" name="form_type" value="<?=(count($pdf_settings)>0)?'2':'1'?> ">
                  <!-- Paper Settings -->
                  <div class="row text-center docInfoHdr">
                      <div class="col-md-6 text-left">
                          Paper Settings
                      </div>
                  </div>
                  <div class="row col-md-12">
                    <div class="col-md-12">
                      <p class="p-0">Paper Type</p>
                      <input type="radio" name="paper_type" checked="true" value="A4"> A4
                    </div>
                  </div>
                  <!-- Paper Settings -->
                  <!-- Header Settings -->
                  <div class="row text-center docInfoHdr">
                      <div class="col-md-6 text-left">
                          Header Settings
                      </div>
                  </div>
                  <div class="row col-md-12">
                    <div class="col-md-12">
                      <h5 class="p-0"><input type="checkbox" class="headerInfo" name="header" value="1" <?=($pdf_settings->header==1)?'checked':''?> > Clinic Header
                        <p style="font-style: italic;">( If you tick (&#10004;) this one, You can be able to print the Summaries, Prescription, Vitals on your official Letter Heads. )</p>
                      </h5>
                    </div>
                  </div>
                  <div class="row col-md-12 header_div   <?=($pdf_settings->header==0)?'hidden':''?> ">
                    <div class="col-md-4">
                      <p class="p-0">With Doctor Details</p>
                      <input type="radio" name="doc_details" value="1" <?=($pdf_settings->doc_details==1)?'checked':''?> > Yes&emsp;
                      <input type="radio" name="doc_details" value="0" <?=($pdf_settings->doc_details==0)?'checked':''?> > No
                    </div>
                    <div class="col-md-4">
                      <p class="p-0">Header Height ( In CMS )</p>
                      <input type="text" name="head_height" onkeypress="return decimal()" minlength="1" class="form-control" value="<?=$pdf_settings->head_height?>">
                    </div>
                  </div>
                  <!-- Header Settings -->
                  <!-- Footer Settings -->
                  <div class="row text-center docInfoHdr">
                      <div class="col-md-6 text-left">
                          Footer Settings
                      </div>
                  </div>
                  <div class="row col-md-12">
                    <div class="col-md-12">
                      <h5 class="p-0"><input type="checkbox" class="footerInfo" name="footer" value="1" <?=($pdf_settings->footer==1)?'checked':''?> > Clinic Footer
                        <p style="font-style: italic;">( If you tick (&#10004;) this one, You can be able to print the Summaries, Prescription, Vitals on your official Letter Heads. )</p></h5>
                    </div>
                  </div>
                  <div class="row col-md-12 footer_div  <?=($pdf_settings->footer==0)?'hidden':''?> ">
                    <div class="col-md-4">
                      <p class="p-0">Footer Height ( In CMS )</p>
                      <input type="text" name="foot_height" onkeypress="return decimal()" minlength="1" class="form-control" value="<?=$pdf_settings->foot_height?>">
                    </div>
                  </div>
                  <!-- Footer Settings -->
                  <!-- Submit -->
                  <div class="row col-md-12 text-center">
                    <div class="col-md-12">
                      <button class="btn btn-success" name="submit" type="submit">Save</button>
                    </div>
                  </div>
                  <!-- submit -->

                </form>
            </div>
          </div><!-- /.card-body -->
          </div>
          <!-- /.nav-tabs-custom -->
      </div>
                                             

                       
                          </div>
                   


      </section>  


         
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<script type="text/javascript">
  $(document).ready(function(){

    $('.headerInfo').on("click",function(){
      if($(this). prop("checked") == true){
        $('.header_div').removeClass("hidden");
      }
      else
      {
        $('.header_div').addClass("hidden");
      }
    });

    $('.footerInfo').on("click",function(){
      if($(this). prop("checked") == true){
        $('.footer_div').removeClass("hidden");
      }
      else
      {
        $('.footer_div').addClass("hidden");
      }
    });

  });
</script>

