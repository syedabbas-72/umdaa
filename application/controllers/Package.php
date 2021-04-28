<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends MY_Controller {

public function __construct() {

    parent::__construct();

    $this->load->library('mail_send', array('mailtype'=>'html'));		 

	$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

}

public function index(){
	$data['features_list'] = $this->Generic_model->getAllRecords('features');
	$data['packages_list'] = $this->Generic_model->getAllRecords('packages');
	$data['view'] = 'Package/Package_list';
	$this->load->view('layout', $data);
}

public function package_add()
{
	$user_id = $this->session->userdata('user_id');
	extract($_POST);
		
	if(isset($_POST['package_add'])){
		$data['package_name'] = $package_name;
		if(!isset($free)){
			$data['coupon'] = $coupon;
			$data['coupon_discount'] = $coupon_discount;
			$data['mrp'] = $mrp;
			$data['sale_price'] = $sale_price;
		}
		else{
			$data['free'] = 1;
		}
		$data['status'] = 1;
		$data['created_by'] = $user_id;
		$data['modified_by'] = $user_id;
		$data['created_date_time'] = date('Y-m-d H:i:s');
		$data['modified_date_time'] = date('Y-m-d H:i:s');
		$this->Generic_model->insertData('packages',$data);
		redirect('Package');
	}
	else{		
		$data['view'] = 'Package/Package_list';
    	$this->load->view('layout', $data);
	}
}

// add features to pacakge
public function package_feature(){
	extract($_POST);
    // echo "<pre>";print_r($_POST);echo "</pre>";
    // exit;
	$user_id = $this->session->userdata('user_id');
	$check = $this->Generic_model->getAllRecords('package_features', array('package_id'=>$package_id));
	if(count($check) > 0){
		$this->Generic_model->deleteRecord('package_features', array('package_id'=>$package_id));
	}
	
	if(count($entity) > 0){
		foreach($entity as $val){

			$data['package_id'] = $package_id;
			$data['module_id'] = explode(",",$val)[1];
			$data['entity_id'] = explode(",",$val)[0];
			$data['feature_type'] = "Module";
			$data['status'] = 1;
			$data['created_by'] = $user_id;
			$data['created_date_time'] = date('Y-m-d H:i:s');
			$data['modified_by'] = $user_id;
			$data['modified_date_time'] = date('Y-m-d H:i:s');
			$this->Generic_model->insertData('package_features', $data);
			unset($data);
		}
	}
	if(count($functionality) > 0){
		foreach($functionality as $val){
			$data['package_id'] = $package_id;
			$data['module_id'] = explode(",",$val)[1];
			$data['functionality_id'] = explode(",",$val)[0];
			$data['feature_type'] = "Functionality";
			$data['status'] = 1;
			$data['created_by'] = $user_id;
			$data['created_date_time'] = date('Y-m-d H:i:s');
			$data['modified_by'] = $user_id;
			$data['modified_date_time'] = date('Y-m-d H:i:s');
			$this->Generic_model->insertData('package_features', $data);
			unset($data);
		}
	}
	redirect('Package');
}

// Get Package Features
public function features_list(){
	extract($_POST);
	$featInfo = getFeaturesIDByPackage($package_id);
	$featArray = explode(",", $featInfo->feat);
	$functionalityInfo = getFunctionalitiesByPackageID($package_id);
	$funcArray = explode(",", $functionalityInfo->func);
	
	$modulesList = $this->Generic_model->getAllRecords('modules');
	$features_list = $this->Generic_model->getAllRecords('features');
	?>
	<form action="<?=base_url('Package/package_feature')?>" method="post">
		<input type="hidden" name="package_id" value="<?=$package_id?>">
          <h4 class="page-title">Properties</h4>
          <div class="row">
            <div class="col-12">
            <?php
            if(count($features_list) > 0){
              foreach($features_list as $value){
                if($value->feature_type != "Functionality")
                  continue;
                
                
                ?>
                <h5 class="font-weight-bold text-uppercase p-0">
                  <div class="checkbox checkbox-icon-black">
                    <input id="feat_<?=$value->feature_id?>" type="checkbox" name="functionality[]" <?=(in_array($value->feature_id, $funcArray)?'checked':'')?> value="<?=$value->feature_id?>,<?=$value->module_id?>">
                    <label for="feat_<?=$value->feature_id?>" class="font-weight-bold">
                      <?=$value->feature_name?>
                    </label>
                  </div>
                </h5>
                <?php
              }
            }
            ?>
            </div>
          </div>
          <h4 class="page-title">Modules</h4>
          <div class="row">
            <div class="col-12">
            <?php
            if(count($modulesList) > 0){
              foreach($modulesList as $value){
				$userEntityInfo = $this->db->query("select * from module_entities where module_id='".$value->module_id."'")->row();
                // if($value->feature_type != "Module")
                //   continue;
                $moduleInfo = $this->Generic_model->getAllRecords('module_entities', array('module_id'=>$value->module_id));
                
                if(count($moduleInfo) > 0){?>
                  <h5 class="font-weight-bold text-uppercase"><label class="font-weight-bold"><?=$value->module_name?></label></h5>
				  
                  <div class="pl-5">
                    <div class="row">
                      <div class="col-12">
                        <!-- <div class="card shadow-none"> -->
                          <div class="row">
                          <?php
                          foreach($moduleInfo as $val){
                            $entityInfo = userEntityInfo($val->entity_id);
                            $checkFeatMap = $this->db->query("select * from package_features where package_id='".$package_id."' and module_id='".$value->module_id."' and entity_id='".$val->entity_id."'")->num_rows();
                            ?>
                            <div class="col-4 p-0">
                              <div class="checkbox checkbox-icon-primary">
                                <input id="entity_<?=$value->module_id?>_<?=$val->entity_id?>" type="checkbox" name="entity[]" <?=($checkFeatMap > 0)?'checked':''?> value="<?=$val->entity_id?>,<?=$value->module_id?>">
                                <label for="entity_<?=$value->module_id?>_<?=$val->entity_id?>">
                                  <?=getUserEntityName($val->entity_id)?>
                                  <!-- .<?=$value->module_id?>_<?=$val->entity_id?> -->
                                </label>
                              </div>
                            </div>
                            <?php
                          }
                          ?>
                          </div>
                        <!-- </div> -->
                      </div>
                    </div>
                  </div>
                  <?php
                }
              }
            }
            ?>
            </div>
          </div>
        </div>
        <button class="btn btn-primary pull-right my-2" >Submit</button>
        </form>
	<?php

	// $features = $this->Generic_model->getAllRecords('features');
	// if(count($features) > 0){
	// 	f
	// }
}


// Package Delete
public function package_delete($package_id){
	$check = $this->Generic_model->getSingleRecord('packages', array('package_id'=>$package_id));
	if(count($check) > 0){
		$this->Generic_model->deleteRecord('packages', array('package_id'=>$package_id));
	}
	redirect('Package');
}
 
}