<?php

class Access_model extends CI_Model {
    //put your code here


    public function insertingData($tbl, $data)
    {
	$this->db->INSERT($tbl, $data);
        //return $this->db->insert_id();
    }
    
    public function ProcessInOutData($mon_yr)
    {
        $sql = "SELECT * FROM `tb_vehicle_in_out_info` 
                where (date_format(date,'%Y-%m') between (Select date_format(date,'%Y-%m') 
                FROM `tb_vehicle_in_out_info` 
                where process_stage=0 Limit 1) AND '$mon_yr') 
                group by user_id
                order by user_id,date_time_str";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
    }
	
	public function isShortTimeDuplicatedEntry($user_id){
		$sql = "SELECT * FROM `tb_vehicle_in_out_info` where user_id=$user_id ORDER BY id DESC Limit 1";
        
        $query = $this->db->query($sql)->result_array();
        return $query;
	}
	
	public function isDataAlreadyAvailable($user_id, $fp_card_no, $date, $time){
		$sql = "SELECT * FROM `tb_vehicle_in_out_info` where user_id='$user_id' and fp_card_no = '$fp_card_no' and date='$date' and time = '$time'";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
	}
	
		
	public function isCardNumberCreated($fp_card_no){
		$sql = "SELECT * FROM tb_vehicle_cards where card_no='$fp_card_no'";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
	}
	    
    public function isUserIDExist($user_id)
    {
        $sql = "SELECT * FROM  tb_vehicle_cards where user_id='$user_id'";
         $query = $this->db->query($sql)->result_array();
         return $query;
    }  
	    
    public function isCompanyExist($company)
    {
        $sql = "SELECT * FROM  tb_company where company_name like '%$company%'";
         $query = $this->db->query($sql)->result_array();
         return $query;
    }
		    
    public function isOldPasswordExist($employee_code, $oldpassword)
    {
		$emp_master_db = $this->load->database('emp_master', TRUE);
		
        $sql = "SELECT * FROM  tb_employee_master where employee_code = '$employee_code' and password = '$oldpassword'";
        $query = $emp_master_db->query($sql)->result_array();
        return $query;
    }
	 
    public function isCardNoExist($card_no)
    {
        $sql = "SELECT * FROM  tb_vehicle_cards where card_no='$card_no'";
         $query = $this->db->query($sql)->result_array();
         return $query;
    }

    public function getAllCompanies()
    {
        $sql = "SELECT * FROM `tb_company`";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
    }

    public function getVehicleSizes()
    {
        $sql = "SELECT * FROM  `tb_vehicle_types`";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
    }

    public function getAllVehicleCodes()
    {
        $sql = "SELECT t1.*,t2.company_name,t3.vehicle_type 
                FROM `tb_vehicle_cards` as t1 
                Inner Join `tb_company` as t2 on t1.company_id=t2.id
                Inner Join tb_vehicle_types as t3 on t1.vehicle_type_id=t3.id";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
    }
	
	public function getYearsFromExsistingData()
    {
        $sql = "SELECT date_format(date,'%Y') as year FROM `tb_vehicle_in_out_info` group by date_format(date,'%Y')";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
    }

    public function getAllTbl($tbl)
    {
        $this->db->select('*');
        $this->db->from($tbl);
        $query=  $this->db->get();
        $result=$query->result_array();
        return $result;
    }
    
    public function getInOutDataToProcess($user_id, $mon_yr)
    {
      
        $sql = "SELECT * FROM `tb_vehicle_in_out_info` 
                where (date_format(date,'%Y-%m') between (Select date_format(date,'%Y-%m') 
                FROM `tb_vehicle_in_out_info` 
                where process_stage=0 Limit 1) AND '$mon_yr')
                and user_id = $user_id
                order by user_id,date_time_str";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
//         return $sql;
//         return $user_id;
    }
    
    public function getVehicleInOutReportData($mon_yr, $company)
    {
		$where = '';
        if(!empty($company)){
            $where .= " and t1.user_name Like '%$company%'";
        }
		
		if($mon_yr != '1970-01'){
		 $where .= " and date_format(t1.date,'%Y-%m') = '$mon_yr'";
		}
		
        $sql = "Select t1.id as in_id,t1.user_id,t1.user_name,t1.fp_card_no,t1.date as in_date,t1.time as in_time,t1.flag as in_flag,
        t1.process_stage as in_stage_flag, t1.date_time_str as in_date_time_str,
        t2.id as out_id,t2.user_id,t2.date as out_date,t2.time as out_time,
        t2.flag as out_flag,t2.process_stage as out_stage_flag, t2.date_time_str as out_date_time_str,
        CONCAT(t1.date,' ',t1.time) AS in_date_time, CONCAT(t2.date,' ',t2.time) AS out_date_time, 
        t3.vehicle_type_id, t4.vehicle_type, CONCAT(t1.id,',',t2.id) as ids
        From 
        (SELECT * FROM tb_vehicle_in_out_info 
        where (date_format(date,'%Y-%m') between (Select date_format(date,'%Y-%m') FROM `tb_vehicle_in_out_info` where process_stage=0 Limit 1) and '$mon_yr')  AND flag = 1 and process_stage=1) as t1

        Inner Join

        (SELECT * FROM tb_vehicle_in_out_info 
        where (date_format(date,'%Y-%m') between (Select date_format(date,'%Y-%m')  FROM `tb_vehicle_in_out_info` where process_stage=0 Limit 1) and '$mon_yr')  AND flag = 2 and process_stage=1) as t2

        On t1.user_id = t2.user_id and CONCAT(t1.date,' ',t1.time) < CONCAT(t2.date,' ',t2.time)

        Left Join 

        tb_vehicle_cards as t3 On t1.user_id=t3.user_id and t1.fp_card_no=t3.card_no

        Left Join 

        tb_vehicle_types as t4 On t4.id=t3.vehicle_type_id
where 1 $where
        group by t1.user_id,t1.date,t1.time
        Order By t1.user_id";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
//         echo $sql;
    }
	
	
    public function getVehicleInOutData($mon_yr)
    {
		
        $sql = "Select t1.id as in_id,t1.user_id,t1.user_name,t1.fp_card_no,t1.date as in_date,t1.time as in_time,t1.flag as in_flag,
        t1.process_stage as in_stage_flag, t1.date_time_str as in_date_time_str,
        t2.id as out_id,t2.user_id,t2.date as out_date,t2.time as out_time,
        t2.flag as out_flag,t2.process_stage as out_stage_flag, t2.date_time_str as out_date_time_str,
        CONCAT(t1.date,' ',t1.time) AS in_date_time, CONCAT(t2.date,' ',t2.time) AS out_date_time, 
        t3.vehicle_type_id, t4.vehicle_type, CONCAT(t1.id,',',t2.id) as ids
        From 
        (SELECT * FROM tb_vehicle_in_out_info 
        where (date_format(date,'%Y-%m') between (Select date_format(date,'%Y-%m') FROM `tb_vehicle_in_out_info` where process_stage=0 Limit 1) and '$mon_yr')  AND flag = 1 and process_stage=0) as t1

        Inner Join

        (SELECT * FROM tb_vehicle_in_out_info 
        where (date_format(date,'%Y-%m') between (Select date_format(date,'%Y-%m')  FROM `tb_vehicle_in_out_info` where process_stage=0 Limit 1) and '$mon_yr')  AND flag = 2 and process_stage=0) as t2

        On t1.user_id = t2.user_id and CONCAT(t1.date,' ',t1.time) < CONCAT(t2.date,' ',t2.time)

        Left Join 

        tb_vehicle_cards as t3 On t1.user_id=t3.user_id and t1.fp_card_no=t3.card_no

        Left Join 

        tb_vehicle_types as t4 On t4.id=t3.vehicle_type_id
        group by t1.user_id,t1.date,t1.time
        Order By t1.user_id";
        
         $query = $this->db->query($sql)->result_array();
         return $query;
//         echo $sql;
    }
    
    public function getVehicleCostData($staying_time, $vehicle_type_id)
    {
	$sql = "Select B.*,C.vehicle_type_id,C.cost,C.high_time_id 
                From (Select A.* From (SELECT t1.*,IF(t2.highest_staying_time IS NULL, '00:00:00', 
                t2.highest_staying_time) as starting_time FROM `tb_time_conditions` as t1 
                left Join `tb_time_conditions` as t2 on t1.starting_time_id=t2.id) as A 
                where '$staying_time' between A.starting_time and A.highest_staying_time) as B 
                Inner Join
                tb_vehicle_type_cost as C On C.vehicle_type_id=$vehicle_type_id and C.high_time_id=B.id";
        
        $query = $this->db->query($sql)->result_array();
        return $query;
//        return $sql;
    }
    
    public function updateFinalProcessFlag($ids, $flag)
    {
        $sql = "Update `tb_vehicle_in_out_info` set process_stage = $flag
               where id in ($ids)";

        $query = $this->db->query($sql);
        return $query;
    }
	
	public function update_password($employee_code, $confirm_new_password){
		$emp_master_db = $this->load->database('emp_master', TRUE);
		
		$sql = "Update tb_employee_master set password = '$confirm_new_password'
               where employee_code='$employee_code'";

        $query = $emp_master_db->query($sql);
        return $query;
	}
    
    public function updateTbl($tbl, $id, $data)
    {
        $this->db->where('id', $id);
        $query = $this->db->update($tbl, $data);

        return $query;
    }
    
}

?>