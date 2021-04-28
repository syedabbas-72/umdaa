<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class DrugModel extends CI_Model{

    public function get_count() 
	{
        return $this->db->query("SELECT * FROM drug")->num_rows();
    }
    public function drugs_list($limit,$start)
    {
        return $this->db->query("SELECT * FROM drug LIMIT ".$start.",".$limit."")->result();
    }
    
    public function getDrugSearch($limit,$start,$search) 
	{
        // return "search".$search;
        return $data = $this->db->query("select * from drug where trade_name LIKE '%".$search."%' LIMIT ".$start.",".$limit."")->result();
        // return $this->db->last_query();
    
    }

    public function get_search_count($search) 
	{
        $data = $this->db->query("select * from drug where trade_name LIKE '%".$search."%'")->num_rows();
    
        return $data;
    }

}
?>