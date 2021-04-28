<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends CI_Controller {

	public function __construct() {

		parent::__construct();

		$is_logged_in = $this->session->has_userdata('is_logged_in');

		if($is_logged_in == 0){
			redirect('Authentication');
		}
    }

    public function index()
    {
        echo "welcome";
        // $this->load->view('layout', $data);
    }

    public function demo(){
        $data['wallet_specilization_prices'] = $this->db->query("select * from wallet_specilization_prices")->result();
        $data['view'] = "wallet/walletPrice";
        $this->load->view('layout', $data);
    }

    public function demo123(){

        $data['speciality']=$this->input->post('type');
        $data['created_by'] = $this->session->userdata('user_id');
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $data['amount']=$this->input->post('amount');
        $this->Generic_model->insertData('wallet_specilization_prices',$data);
        redirect('Wallet/demo');
    }

    public function demo1234($id){

        $data['speciality']=$this->input->post('type');
        $data['amount']=$this->input->post('amount');
        $this->Generic_model->updateData("wallet_specilization_prices",$data, array('wallet_specialization_prices_id'=>$id));
        redirect('Wallet/demo');
    }

    public function demo12345($id)
    {
        // $data['type']=$this->input->post('type');
        $data['amount']=$this->input->post('amount');
        $this->Generic_model->updateData("doctor_wallet_prices",$data, array('id'=>$id));
        redirect('Wallet/anotherDemo');
    }

    public function price_edit($id)
    {
        $data['price_edit'] = $this->db->query("select * from wallet_specilization_prices where wallet_specialization_prices_id= '".$id."'")->row();
        $data['view'] = "wallet/priceEdit";
        $this->load->view('layout', $data);
    }

    public function doctor_price_edit($id)
    {
        $data['price_edit'] = $this->db->query("select * from doctor_wallet_prices where id= '".$id."'")->row();
        $data['departments'] = $this->db->query("select * from department")->result();
        $data['view'] = "wallet/doctorWalletEdit";
        $this->load->view('layout', $data);
    }


    public function delete_procedure($id){
    
        $this->db->query("DELETE from wallet_specilization_prices where wallet_specialization_prices_id='".$id."'" );
        redirect('Wallet/demo');

}

public function doctor_price_delete($id)
{
    $this->db->query("DELETE from doctor_wallet_prices where doctor_wallet_id='".$id."'" );
    // $this->session->set_flashdata('Deleted');
    redirect('Wallet/anotherDemo');
}


    public function doctorWalletPriceEdit(){
        if(isset($_POST))
        {
            extract($_POST);
            $walletInfo = $this->db->select("*")->from("doctor_wallet_prices")->where("doctor_wallet_id",$wallet_id)->get()->row();

            $data['amount'] = $amount+$walletInfo->amount;
            $data['modified_by'] = $this->session->userdata('user_id');
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->updateData('doctor_wallet_prices',$data,array('doctor_wallet_id'=>$wallet_id));
            $data1['transaction_amount'] = $amount;
            $data1['transaction_type'] = "Credit";
            $data1['doctor_id'] = $walletInfo->doctor_id;
            $data1['created_by'] = $this->session->userdata('user_id');
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData("eo_wallet_history",$data1);
            redirect('Wallet/anotherDemo');
        }
    }


    public function addView(){
        $data['departments'] = $this->db->query("select * from department")->result();
         $data['doctors'] = $this->db->query("select * from doctors")->result();
        $data['view'] = "wallet/walletAddPrice";
        $this->load->view('layout', $data);
    }

    public function anotherDemo(){
        $data['list'] = $this->db->query("select * from doctor_wallet_prices")->result();
        $data['departments'] = $this->db->select("*")->from("department")->get()->result();
        $data['speciality'] = $this->db->query("select * from wallet_specilization_prices group by speciality")->result();
        
        $data['view'] = "wallet/doctorWalletList";
        $this->load->view('layout', $data);
    }


    public function getDoctor()
    {
        $id = $_POST['id'];
        $doctors = $this->db->query("select * from doctors where department_id='".$id."'")->result();
        // $districts = $this->db->select('district_id,district_name')->from('districts')->where('state_id',$id)->get()->result();
        $res = '<option value="" selected disabled >--Select Doctor--</option>';
        foreach ($doctors as $value) {
            $walletDoc = $this->db->query("select * from doctor_wallet_prices where doctor_id='".$value->doctor_id."'")->row();
            if(count($walletDoc)==1)
                continue;
            $res .= '<option value="' . $value->doctor_id. '">' . $value->salutation." ".$value->first_name." ".$value->last_name . '</option>';
        }
       // print_r($districts);
        echo $res;

    }

    public function addWalletPrice(){

     $doctor_id = $this->input->post('doctor_name');

      $check = $this->db->query("select * from doctor_wallet_prices where doctor_id = '".  $doctor_id."'")->row();

      if($check != '')
      {
            redirect('Wallet/anotherDemo',$data); 
      }
      else{
        $deptInfo = $this->db->query("select department_name from department where department_id = '".$id."'")->row();
        $data['doctor_id']= $this->input->post('doctor_name'); 
        $data['speciality'] =  $this->input->post('speciality');
        $data['amount']= $this->input->post('amount'); 
        $data['created_by'] = $this->session->userdata('user_id');
        $data['created_date_time'] = date("Y-m-d H:i:s");
        $this->Generic_model->insertData('doctor_wallet_prices',$data);
        
        $data1['transaction_amount'] = $this->input->post('amount');
        $data1['transaction_type'] = "Credit";
        $data1['doctor_id'] = $this->input->post('doctor_name');
        $data1['created_by'] = $this->session->userdata('user_id');
        $data1['created_date_time'] = date("Y-m-d H:i:s");
        $this->Generic_model->insertData("eo_wallet_history",$data1);
        redirect('Wallet/anotherDemo');
      }
    }  
    
    //Requests list
    public function walletRequests()
    {
        $data['requestsList'] = $this->db->select("*")->from("wallet_amount_requests")->order_by("wallet_amount_request_id","DESC")->get()->result();
        $data['view'] = "wallet/requestslist";
        $this->load->view('layout',$data);
    }

    //add money to wallet from request page
    public function addReqMoney(){
        if(isset($_POST))
        {
            extract($_POST);
            $check = $this->db->query("select * from doctor_wallet_prices where doctor_id='".$doctor_id."'")->row();
            if(count($check)>0)
            {
                $data['amount'] = $amount+$check->amount;
                $this->Generic_model->updateData("doctor_wallet_prices", $data, array('doctor_wallet_id'=>$check->doctor_wallet_id));
                // make status as amount added.
                $para['req_status'] = 1;
                $this->Generic_model->updateData("wallet_amount_requests",$para,array('wallet_amount_request_id'=>$req_id));
                // Transaction History
                $data1['transaction_amount'] = $amount;
                $data1['transaction_type'] = "Credit";
                $data1['doctor_id'] = $doctor_id;
                $data1['created_by'] = $this->session->userdata('user_id');
                $data1['created_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData("eo_wallet_history",$data1);
                $this->session->set_flashdata('msg','Amount Added.');
                redirect('Wallet/walletRequests');
            }
            else
            {
                $data['amount'] = $amount;
                $data['doctor_id'] = $doctor_id;
                $data['created_by'] = $this->session->userdata('user_id');  
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData("doctor_wallet_prices", $data);
                // make status as amount added.
                $para['req_status'] = 1;
                $this->Generic_model->updateData("wallet_amount_requests",$para,array('wallet_amount_request_id'=>$req_id));
                // Transaction History
                $data1['transaction_amount'] = $amount;
                $data1['transaction_type'] = "Credit";
                $data1['doctor_id'] = $doctor_id;
                $data1['created_by'] = $this->session->userdata('user_id');
                $data1['created_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData("eo_wallet_history",$data1);
                $this->session->set_flashdata('msg','Amount Added.');
                redirect('Wallet/walletRequests');
            }
        }
        else
        {
            $this->session->set_flash_data('msg','Access Denied');
            redirect('Wallet/walletRequests');
        }
    }


    // public function anotherDemo(){
    //     $data['doctors'] = $this->db->query("select * from doctors")->result();
    //     redirect('Wallet/anotherDemo');
    // }
    
}

?>
