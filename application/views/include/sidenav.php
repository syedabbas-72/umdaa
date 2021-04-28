<style type="text/css">
	.nav-heading {
		padding: 10px 15px;
	}
	.nav-heading span {
		font-weight: 600;
		font-size: 16px;
		color: white;
	}
</style>
<!-- start sidebar menu -->
            <div class="sidebar-container">
                <div class="sidemenu-container fixed-sidebar navbar-collapse collapse ">
                    <div id="remove-scroll" class="left-sidemenu">
                        <ul class="sidemenu page-header-fixed" style="padding-top: 20px">
                            <li class="sidebar-toggler-wrapper hide">
                                <div class="sidebar-toggler">
                                    <span></span>
                                </div>
                            </li>
                          
                            <li class="nav-heading"><span>MAIN</span></li>
<?php
$CI =& get_instance();
$profile_id = $CI->session->userdata('profile_id');
$clinic_id = $this->session->userdata('clinic_id');
if($clinic_id==0)
{
	$entities = $CI->db->query("select * from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and parent_id=0 and p_read=1 ORDER BY FIELD(category, 'Main', 'Masters', 'Administration'), position,user_entity_name asc")->result_array();
}
else
{
	$entities = $CI->db->query("select * from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and parent_id=0 and p_read=1 ORDER BY FIELD(c.category, 'Main', 'Masters', 'Administration'), position,user_entity_name asc")->result_array();
}
//echo $this->db->last_query();
//echo "<pre>";print_r($entities);
$msc=0;$amc=0;
foreach($entities as $result)
{
	if($clinic_id==0)
	{
		$sub_chk = $CI->db->query("select * from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where parent_id=".$result['user_entity_id']." and p_read=1 and profile_id=".$profile_id." and level_alias='nav'")->result_array();
	}
	else
	{
		$sub_chk = $CI->db->query("select * from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where c.parent_id=".$result['user_entity_id']." and p_read=1 and profile_id=".$profile_id." and c.level_alias='nav'")->result_array();
	}
		
	$cnt = count($sub_chk);
?>
<?php if(in_array("Masters", $result)){ ?>
<?php if($result['category']=='Masters' && ($msc==0)){ $msc++; ?>

<li class="nav-heading"><span>MASTERS</span></li>
<?php } } ?>
<?php if(in_array("Administration", $result)){ ?>
<?php if($result['category']=='Administration' && ($amc==0)){ $amc++; ?>
	<li class="nav-heading"><span>ADMINISTRATION</span></li>		
<?php  }}?>                            
                           
                            
                          
                            <li class="nav-item <?=(strtolower($this->uri->segment(1))==strtolower($result['entity_url']))?'active':''?>" >
                                <a href="<?php echo base_url($result['entity_url']); ?>" aria-expanded="false" class="nav-link nav-toggle"> <?php if($result['entity_icon'] != '' || $result['entity_icon'] != NULL){ echo $result['entity_icon']; } ?><span class="title"><?php echo $result['user_entity_alias']; ?><?php if($cnt>0){?><span class="arrow"></span></span><?php } ?></a>
                                <?php if($sub_chk){?>
                                <ul class="sub-menu ">
                                	<?php foreach($sub_chk as $sresult){ ?>
                                    <li class="nav-item">
                                        <a href="<?php echo base_url($sresult['entity_url']); ?>" class="nav-link ">
                                            <span class="title"><?php echo $sresult['user_entity_alias']; ?></span>
                                        </a>
                                    </li>
                                  <?php } ?>
                                </ul>
                                <?php } ?>
                                
                            </li>
                          <?php
}
?>
                            
                                </ul>
                          
                    </div>
                </div>
            </div>
             <!-- end sidebar menu -->