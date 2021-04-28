<?php
error_reporting(0);


/**
 * 
 */
class RegistrationV1_1 extends CI_Controller
{
	
	function __construct(argument)
	{
		# code...
	}

	//Registration Part 1 Includes CLinic Basic Details
	public function clinicRegistration($parameters,$method,$user_id){
		extract($parameters);
		$clinicData['clinic_name']=$parameters['clinic_name'];
        $clinicData['clinic_phone']=$parameters['clinic_phone'];
        $clinicData['incharge_mobile']=$parameters['incharge_mobile'];
        $clinicData['email']=$parameters['email'];
        $clinicData['address']=$parameters['address'];
     	$clinicTotalData=$this->Generic_model->insertDataReturnId("clinics",$clinicData);
     	$this->response(array('code' => '200', 'message' => 'Success', 'result' => $clinicData, 'requestname' => $method));
	}

}

?>