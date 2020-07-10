<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();


class Access extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $employee_code = $this->session->userdata('employee_code');
        $employee_name = $this->session->userdata('employee_name');
        $employee_email = $this->session->userdata('employee_email');
        $department = $this->session->userdata('department');

        if ($employee_code == NULL && $employee_name == NULL && $employee_email == NULL && $department == NULL) {
            redirect('welcome', 'refresh');
        }
    }

    public function index() {
        $data['title'] = 'Dashboard';
        $data['maincontent'] = $this->load->view('dashboard', '', true);
        $this->load->view('master', $data);
    }

    public function company_list() {
        $data['title'] = 'Company List';
	$get_data['companies'] = $this->access_model->getAllTbl('tb_company');
        $data['maincontent'] = $this->load->view('company_list', $get_data, true);
        $this->load->view('master', $data);
    }

    public function vehicle_codes() {
        $data['title'] = 'Vehicle Codes';
		$get_data['v_codes'] = $this->access_model->getAllVehicleCodes();
        $data['maincontent'] = $this->load->view('vehicle_codes', $get_data, true);
        $this->load->view('master', $data);
    }
	
    public function isUserIDExist() {
		$user_id = trim($this->input->post('user_id'));
		$get_data = $this->access_model->isUserIDExist($user_id);
        echo json_encode($get_data);
    }
		
    public function isCompanyExist() {
		$company_name = ($this->input->post('company_name'));
		$get_data = $this->access_model->isCompanyExist($company_name);
        echo json_encode($get_data);
    }
			
    public function isOldPasswordExist() {
		$old_password = ($this->input->post('old_password'));
		$employee_code = $this->session->userdata('employee_code');
		$get_data = $this->access_model->isOldPasswordExist($employee_code, $old_password);
        echo json_encode($get_data);
    }
	
    public function isCardNoExist() {
		$card_no = trim($this->input->post('card_no'));
		$get_data = $this->access_model->isCardNoExist($card_no);
        echo json_encode($get_data);
    }

    public function new_card() {
        $data['title'] = 'New Card';
	$get_data['companies'] = $this->access_model->getAllCompanies();
	$get_data['v_sizes'] = $this->access_model->getVehicleSizes();
        $data['maincontent'] = $this->load->view('new_card', $get_data, true);
        $this->load->view('master', $data);
    }
	
    public function add_new_company() {
        $data['title'] = 'New Company';
        $data['maincontent'] = $this->load->view('new_company', $get_data, true);
        $this->load->view('master', $data);
    }
	
    public function change_password() {
        $data['title'] = 'Change Passord';
        $data['maincontent'] = $this->load->view('change_password', $get_data, true);
        $this->load->view('master', $data);
    }
	
    public function new_time_condition() {
        $data['title'] = 'New Time Condition';
	/*$get_data['times'] = $this->access_model->getAllExsistingTimes();*/
        $data['maincontent'] = $this->load->view('new_time_condition', $get_data, true);
        $this->load->view('master', $data);
    }
	
	public function changing_password(){
		$employee_code = $this->session->userdata('employee_code');
		$data['confirm_new_password'] = $this->input->post('confirm_new_password');
		
		$updated = $this->access_model->update_password($employee_code, $data['confirm_new_password']);
		$data['message']='Password Successfully Updated. Please Log in again.';
        $this->session->set_userdata($data);
		
		$this->session->unset_userdata('employee_code');
        $this->session->unset_userdata('employee_name');
        $this->session->unset_userdata('employee_email');
        $this->session->unset_userdata('department');
        session_destroy();
		
        redirect('welcome');
	}

    public function adding_new_card() {
        $data['company_id'] = $this->input->post('company_id');
        $data['user_id'] = $this->input->post('user_id');
        $data['card_no'] = $this->input->post('card_no');
        $data['vehicle_type_id'] = $this->input->post('vehicle_type_id');
        /*echo '<pre>';
        print_r($data);
        die();*/
        $inserted = $this->access_model->insertingData('tb_vehicle_cards', $data);
        
        $data['message']='Successfully Added New Card No. - '.$data['card_no'];
        $this->session->set_userdata($data);
        redirect('access/new_card');
        
    }
	
    public function adding_new_company() {
        $data['company_name'] = $this->input->post('company_name');
        /*echo '<pre>';
        print_r($data);
        die();*/
        $inserted = $this->access_model->insertingData('tb_company', $data);
        
        $data['message']='Successfully Added New Company - '.$data['company_name'];
        $this->session->set_userdata($data);
		redirect('access/add_new_company');
        
    }
	
	
    public function report() {
        $data['title'] = 'Report';
		$get_data['companies'] = $this->access_model->getAllCompanies();
		$get_data['years'] = $this->access_model->getYearsFromExsistingData();
        $data['maincontent'] = $this->load->view('report', $get_data, true);
        $this->load->view('master', $data);
    }

	
    public function getInOutCostReport() {
        
        $month = $this->input->post('month');
        $year = $this->input->post('year');
		$company = $this->input->post('company');
        
        $mon_yr = date('Y-m', strtotime($year.'-'.$month));
        
        $get_data_3 = $this->access_model->getVehicleInOutReportData($mon_yr, $company);
        
        $sl=1;
        $new_line = '';
        $total_cost = 0;
        foreach ($get_data_3 as $v_3){
			
            $concat_ids .= $v_3['ids'].',';
            
            $diff = strtotime($v_3['out_date_time'])-strtotime($v_3['in_date_time']);
			$staying_time = round(((round(abs($diff) / 60,2))/60), 0);
			
			if($staying_time < 10){
				$s_time = "0".$staying_time;
			}else{
				$s_time = $staying_time;
			}
			$formating_staying_time = $s_time.':'."00".':'."00";
			
//            echo $v_3['vehicle_type_id'];
            $get_data_4 = $this->access_model->getVehicleCostData($formating_staying_time, $v_3['vehicle_type_id']);
            $cost = $get_data_4[0]['cost'];
            $total_cost += $cost;
			
            $new_line .= '<tr>';
            
            $new_line .= '<td>' . $sl .'</td>';
            $new_line .= '<td>' . $v_3['user_id'] . '</td>';
            $new_line .= '<td>' . $v_3['user_name'] . '</td>';
            $new_line .= '<td>' . $v_3['fp_card_no'] . '</td>';
            $new_line .= '<td>' . $v_3['in_date'] . '</td>';
            $new_line .= '<td>' . $v_3['in_time'] . '</td>';
            $new_line .= '<td>' . $v_3['out_date'] . '</td>';
            $new_line .= '<td>' . $v_3['out_time'] . '</td>';
            $new_line .= '<td>' . $s_time ." Hours". '</td>';
            $new_line .= '<td>' . $v_3['vehicle_type'] . '</td>';
            $new_line .= '<td>' . $cost . '</td>';
			
            $new_line .= '</tr>';
			
			
            $sl++;
        }
		$new_line .= '<tr>';
			$new_line .= '<td colspan="10" align="right"><h4><b>Total</b></h4></td>';
			$new_line .= '<td align="left"><h4>' . number_format($total_cost, 2, '.', '') . '</h4></td>';
		$new_line .= '</tr>';
		
        $all_ids = substr("$concat_ids", 0, -1);
        echo $new_line.'<input type="hidden" class="form-control" name="ids" id="ids" value="'. $all_ids .'" required />';
    }
	
    public function process() {
        $data['title'] = 'Process';
	$get_data['years'] = $this->access_model->getYearsFromExsistingData();
        $data['maincontent'] = $this->load->view('process', $get_data, true);
        $this->load->view('master', $data);
    }
	
    public function ProcessAndGetInOutData() {
        
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        
        $mon_yr = date('Y-m', strtotime($year.'-'.$month));
        
        $get_data = $this->access_model->ProcessInOutData($mon_yr);
        
        foreach ($get_data as $v){
             $user_id = $v['user_id'];
//            $user_id = 100;
             
//            $where = '';
//            if($user_id != ''){
////                echo $user_id;
//                $where = " and user_id=$user_id";
//            }
             if($user_id != ''){
//                echo $user_id;
                $get_data_2 = $this->access_model->getInOutDataToProcess($user_id, $mon_yr);
                
                foreach ($get_data_2 as $v_1=>$key){
                    
                    if(($v_1 % 2) == 0){
                        $up_data['flag'] = 1; //1 = IN
                        $id = $key['id'];
//                        echo $id.' '.$user_id.' '.$up_data['flag'] .' ';
                    }else{
//						$id = $get_data_2['id'];
                        $up_data['flag'] = 2; //2 = Out
                        $id = $key['id'];
//                      echo $id.' '.$user_id.' '.$up_data['flag'] .' ';
                    }
                    $updated = $this->access_model->updateTbl('tb_vehicle_in_out_info', $id, $up_data);
                 }   
             }
        }
        

        $get_data_3 = $this->access_model->getVehicleInOutData($mon_yr);
        $get_data_3;
        $sl=1;
        $new_line = '';
        
        foreach ($get_data_3 as $v_3){
            $concat_ids .= $v_3['ids'].',';
            
            $diff = strtotime($v_3['out_date_time'])-strtotime($v_3['in_date_time']);
			$staying_time = round(((round(abs($diff) / 60,2))/60), 0);
			if($staying_time < 10){
				$s_time = "0".$staying_time;
			}else{
				$s_time = $staying_time;
			}
			$formating_staying_time = $s_time.':'."00".':'."00";			
			
			
//            echo $v_3['vehicle_type_id'];
            $get_data_4 = $this->access_model->getVehicleCostData($formating_staying_time, $v_3['vehicle_type_id']);
            $cost = $get_data_4[0]['cost'];
            
            $new_line .= '<tr>';
            
            $new_line .= '<td>' . $sl .'</td>';
            $new_line .= '<td>' . $v_3['user_id'] . '</td>';
            $new_line .= '<td>' . $v_3['user_name'] . '</td>';
            $new_line .= '<td>' . $v_3['fp_card_no'] . '</td>';
            $new_line .= '<td>' . $v_3['in_date'] . '</td>';
            $new_line .= '<td>' . $v_3['in_time'] . '</td>';
            $new_line .= '<td>' . $v_3['out_date'] . '</td>';
            $new_line .= '<td>' . $v_3['out_time'] . '</td>';
            $new_line .= '<td>' . $s_time. " Hours" . '</td>';
            $new_line .= '<td>' . $v_3['vehicle_type'] . '</td>';
            $new_line .= '<td>' . $cost . '</td>';
			
            $new_line .= '</tr>';
            $sl++;
        }
        $all_ids = substr("$concat_ids", 0, -1);
        echo $new_line.'<input type="hidden" class="form-control" name="ids" id="ids" value="'. $all_ids .'" required />';
    }
	
    public function finalProcessDone() {
        $ids = $this->input->post('ids');
        $flag = $this->input->post('process_stage');
        
        $updated = $this->access_model->updateFinalProcessFlag($ids, $flag);
    }
        

    public function upload_in_file() {
        $data['title'] = 'Upload In File';
        $data['maincontent'] = $this->load->view('upload_in_file', '', true);
        $this->load->view('master', $data);
    }

    public function upload_in_out_file() {
        $data['title'] = 'Upload File';
        $data['maincontent'] = $this->load->view('upload_in_out_file', '', true);
        $this->load->view('master', $data);
    }

    public function upload_out_file() {
        $data['title'] = 'Upload Out File';
        $data['maincontent'] = $this->load->view('upload_out_file', '', true);
        $this->load->view('master', $data);
    }

    public function uploading_in_file() {
        $file_location = $_FILES['in_file']['tmp_name'];

        $file = fopen($file_location, "r");

        $lines       = file($file_location);              //file in to an array
        $second_line = explode(',', $lines[1]);     //$lines[1]->.csv second row.[0]->first row.
        $target_id   = $second_line[0];               //target id = .csv id column; $second_line[0] = .csv-> first column of second row

        $file_heading = fgetcsv($file);

        $user_id_index = array_search('user_id', $file_heading);
        $user_name_index            = array_search('user_name', $file_heading);
        $fp_card_no_index           = array_search('fp_card_no', $file_heading);
        $date_index           = array_search('date', $file_heading);
        $day_index          = array_search('day', $file_heading);
        $slave_ip_index        = array_search('slave_ip', $file_heading);
        $time_index        = array_search('time', $file_heading);
        $flag_index        = array_search('flag', $file_heading);

        while( !feof($file)){
            $row_data = fgetcsv($file);
            
            if($row_data){
                        $list = array(
                            'user_id' => $row_data[$user_id_index],
                            'user_name' => $row_data[$user_name_index],
                            'fp_card_no' => $row_data[$fp_card_no_index], 
                            'date' => $row_data[$date_index], 
                            'day' => $row_data[$day_index],
                            'slave_ip' => $row_data[$slave_ip_index], 
                            'time' => $row_data[$time_index],
                            'flag' => 1
                    );
//                    echo '<pre>';
//                    print_r($list);
//                    die();
                    $this->access_model->insert_tbl('tb_vehicle_in_out_info', $list);
                    
            }
        }
        fclose($file);
            
    }

    public function uploading_out_file() {
        $file_location = $_FILES['out_file']['tmp_name'];

        $file = fopen($file_location, "r");

        $lines       = file($file_location);              //file in to an array
        $second_line = explode(',', $lines[1]);     //$lines[1]->.csv second row.[0]->first row.
        $target_id   = $second_line[0];               //target id = .csv id column; $second_line[0] = .csv-> first column of second row

        $file_heading = fgetcsv($file);

        $user_id_index = array_search('user_id', $file_heading);
        $user_name_index            = array_search('user_name', $file_heading);
        $fp_card_no_index           = array_search('fp_card_no', $file_heading);
        $date_index           = array_search('date', $file_heading);
        $day_index          = array_search('day', $file_heading);
        $slave_ip_index        = array_search('slave_ip', $file_heading);
        $time_index        = array_search('time', $file_heading);
        $flag_index        = array_search('flag', $file_heading);

        while( !feof($file)){
            $row_data = fgetcsv($file);
            
            if($row_data){
                        $list = array(
                            'user_id' => $row_data[$user_id_index],
                            'user_name' => $row_data[$user_name_index],
                            'fp_card_no' => $row_data[$fp_card_no_index], 
                            'date' => $row_data[$date_index], 
                            'day' => $row_data[$day_index],
                            'slave_ip' => $row_data[$slave_ip_index], 
                            'time' => $row_data[$time_index],
                            'flag' => 2
                    );
//                    echo '<pre>';
//                    print_r($list);
//                    die();
                    $this->access_model->insert_tbl('tb_vehicle_in_out_info', $list);
                    
            }
        }
        fclose($file);
    }
    
    
     public function uploading_exl_out_file() {
         
		 if (isset($_FILES['out_file']))
		 {
	
		 move_uploaded_file($_FILES["out_file"]["tmp_name"], "assets/uploaded_exl/" . $_FILES["out_file"]["name"]);
		 $n = "assets/uploaded_exl/" . $_FILES["out_file"]["name"];
			
			//load the excel library
            //$this->load->library('reader');
			
			require_once APPPATH."/third_party/Classes/reader.php";
			
			//require_once(base_url().'libraries/reader.php');
			
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('CP1251');
			$data->read("$n");

			error_reporting(E_ALL ^ E_NOTICE);
			
				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
				{
				$check = $data->sheets[0]['cells'][$i][2];
				  if ($check != '')
				  {
				
					$out_data['user_id']=$data->sheets[0]['cells'][$i][2];  // UserID
					$out_data['user_name']=$data->sheets[0]['cells'][$i][3]; // UserName
					$out_data['fp_card_no']=$data->sheets[0]['cells'][$i][5]; //FP/Card No
					$out_data['date']=date('Y-m-d', strtotime($data->sheets[0]['cells'][$i][6])); //Date
					$out_data['day']=$data->sheets[0]['cells'][$i][7];  // Day
					$out_data['slave_ip']=$data->sheets[0]['cells'][$i][9]; //Location
					$out_data['time']=$data->sheets[0]['cells'][$i][8]; // Time
					$out_data['flag']='2'; // Flag Value: 2 = Out Data
					
					$this->access_model->insertingData('tb_vehicle_in_out_info', $out_data);
					
				  }
				}
                            $session_data['message']='Successfully "Vehicle-OUT" Data Uploaded';
                            $this->session->set_userdata($session_data);

                            redirect('access/upload_out_file');
			 }
     }
    
     public function uploading_exl_file() {
//         $d = array(100, 200, 400, 500);
//         foreach ($d as $v=>$key){
//             echo $key.' ';
//             if($v % 2 == 0){
//                 echo 1 .' ';
//             }else{
//                 echo 2 .' ';
//             }
//         }
//         die();
		 if (isset($_FILES['file']))
		 {
	
		 move_uploaded_file($_FILES["file"]["tmp_name"], "assets/uploaded_exl/" . $_FILES["file"]["name"]);
		 $n = "assets/uploaded_exl/" . $_FILES["file"]["name"];
			
			//load the excel library
            //$this->load->library('reader');
			
			require_once APPPATH."/third_party/Classes/reader.php";
			
			//require_once(base_url().'libraries/reader.php');
			
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('CP1251');
			$data->read("$n");

			error_reporting(E_ALL ^ E_NOTICE);
			
				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
				{
				$check = $data->sheets[0]['cells'][$i][2];
				  if ($check != '')
				  {
					$fp_card_no=$data->sheets[0]['cells'][$i][5];
					$isCardCreated = $this->access_model->isCardNumberCreated($fp_card_no);
					
					if(!$isCardCreated)
					{
						$cdata['not_created_cards']="Sorry, Uploading Failed.".' '.$fp_card_no.' '."is not created yet!";
                   		$this->session->set_userdata($cdata);
					}
					if($isCardCreated){
						$v_data['user_id']=$data->sheets[0]['cells'][$i][2];  // UserID
						$v_data['user_name']=$data->sheets[0]['cells'][$i][3]; // UserName
						$v_data['fp_card_no']=$data->sheets[0]['cells'][$i][5]; // FP Card No
						$v_data['date']=date('Y-m-d', strtotime($data->sheets[0]['cells'][$i][6])); //Date
						$v_data['day']=$data->sheets[0]['cells'][$i][7];  // Day
						$v_data['slave_ip']=$data->sheets[0]['cells'][$i][9]; //Location
						$v_data['time']=date("H:i:s", strtotime($data->sheets[0]['cells'][$i][8])); // Time
						$v_data['date_time_str']=strtotime($v_data['date'].' '.$v_data['time']); // Time Converting to String

					// checking data availability...
					
					$checkDuplicatedEntry = $this->access_model->isShortTimeDuplicatedEntry($v_data['user_id']);
					
					$difference_dup_time = $v_data['date_time_str'] - $checkDuplicatedEntry[0]['date_time_str'];
					$range_of_next_entry = 300; // Seconds
					if($difference_dup_time > $range_of_next_entry){
					$is_available = $this->access_model->isDataAlreadyAvailable($v_data['user_id'], $v_data['fp_card_no'], $v_data['date'], $v_data['time']);
					
						if(!$is_available){
							$this->access_model->insertingData('tb_vehicle_in_out_info', $v_data);
						}
                                        }
					}
					
				  }
				}
                            $session_data['message']='Successfully Data Uploaded';
                            $this->session->set_userdata($session_data);

                            redirect('access/upload_in_out_file');
			 }
     }
    
     public function uploading_exl_in_file() {
         
		 if (isset($_FILES['in_file']))
		 {
	
		 move_uploaded_file($_FILES["in_file"]["tmp_name"], "assets/uploaded_exl/" . $_FILES["in_file"]["name"]);
		 $n = "assets/uploaded_exl/" . $_FILES["in_file"]["name"];
			
			//load the excel library
            //$this->load->library('reader');
			
			require_once APPPATH."/third_party/Classes/reader.php";
			
			//require_once(base_url().'libraries/reader.php');
			
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('CP1251');
			$data->read("$n");

			error_reporting(E_ALL ^ E_NOTICE);
			
				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) 
				{
				$check = $data->sheets[0]['cells'][$i][2];
				  if ($check != '')
				  {
				
					$out_data['user_id']=$data->sheets[0]['cells'][$i][2];  // UserID
					$out_data['user_name']=$data->sheets[0]['cells'][$i][3]; // UserName
					$out_data['fp_card_no']=$data->sheets[0]['cells'][$i][5]; //FP/Card No
					$out_data['date']=date('Y-m-d', strtotime($data->sheets[0]['cells'][$i][6])); //Date
					$out_data['day']=$data->sheets[0]['cells'][$i][7];  // Day
					$out_data['slave_ip']=$data->sheets[0]['cells'][$i][9]; //Location
					$out_data['time']=$data->sheets[0]['cells'][$i][8]; // Time
					$out_data['flag']='1'; // Flag Value: 2 = Out Data
					
					$this->access_model->insertingData('tb_vehicle_in_out_info', $out_data);
					
                                        
				  }
				}
                                $session_data['message']='Successfully "Vehicle-IN" Data Uploaded';
                                $this->session->set_userdata($session_data);

                                redirect('access/upload_in_file');
			 }
     }
    
    
//    public function uploading_out_file() {
//        $file = $_FILES['out_file']['tmp_name'];
//        
//            //load the excel library
//            $this->load->library('excel');
//
//            //read file from path
//            $objPHPExcel = PHPExcel_IOFactory::load($file);
//
//            //get only the Cell Collection
//            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
//
//            //extract to a PHP readable array format
//            $data = array();
//            foreach ($cell_collection as $cell) {
//                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
//                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
//                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
//
//                //header will/should be in row 1 only. of course this can be modified to suit your need.
//                if ($row == 1) {
//                    $header[$row][$column] = $data_value;
//                } else {
//                    $arr_data[$row][$column] = $data_value;
//                    array_push($data,$arr_data[$row][$column]);
//                    if($column == 'H'){
//                        $col = 8;
//                    }
//                    if($column == 'G'){
//                        $col = 7;
//                    }
//                    if($column == 'F'){
//                        $col = 6;
//                    }
//                    if($column == 'E'){
//                        $col = 5;
//                    }
//                    if($column == 'D'){
//                        $col = 4;
//                    }
//                    if($column == 'C'){
//                        $col = 3;
//                    }
//                    if($column == 'B'){
//                        $col = 2;
//                    }
//                    if($column == 'A'){
//                        $col = 1;
//                    }
//                    
////                    for ($row = 1; $row <= (count($data)/8); $row++) {
////                        
////                    }
//                    
//                
//                    
//                }
//                 
////                $is_excel_data_inserted = $this->access_model->insert_tbl('tb_vehicle_in_out_info', $arr_data[$row][$column]);
//                }
//                
//                
//                $total_row = count($data)/$col;
//                
//                foreach ($data as $v=>$key){
//                echo '<pre>';
//                print_r($data);
//                die();
//                    
//                    $s_data = array(
//                            'user_id' => $v,
//                            'user_name' => $v,
//                            'fp_card_no' => $v,
//                            'date' => $v,
//                            'day' => $v,
//                            'slave_ip' => $v,
//                            'time' => $v,
//                            'flag' => 2
//                        );
//                    echo '<pre>';
//                    print_r($s_data);
////                    if((($v+1)/8) != 0){
////                        echo 'true';
////                        die();
//                        
//                        
//                        
////                        $is_excel_data_inserted = $this->access_model->insert_tbl('tb_vehicle_in_out_info', $s_data);
//                        
////                    }                    
//                }
//                
////            send the data in an array format
////            $data['header'] = $header;
////            $data['values'] = $arr_data;
//    }

    public function logout() {
        $this->session->unset_userdata('employee_code');
        $this->session->unset_userdata('employee_name');
        $this->session->unset_userdata('employee_email');
        $this->session->unset_userdata('department');
        session_destroy();
        $data['message'] = 'Successfully Logged out.';
        $this->session->set_userdata($data);
        redirect('welcome');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */