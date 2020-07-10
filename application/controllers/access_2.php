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

    public function upload_in_file() {
        $data['title'] = 'Upload In File';
        $data['maincontent'] = $this->load->view('upload_in_file', '', true);
        $this->load->view('master', $data);
    }

    public function upload_out_file() {
        $data['title'] = 'Upload Out File';
        $data['maincontent'] = $this->load->view('upload_out_file', '', true);
        $this->load->view('master', $data);
    }

    public function uploading_in_file() {
        $file = $_FILES['in_file']['tmp_name'];
        
            //load the excel library
            $this->load->library('excel');

            //read file from path
            $objPHPExcel = PHPExcel_IOFactory::load($file);

            //get only the Cell Collection
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

                //header will/should be in row 1 only. of course this can be modified to suit your need.
                
                if ($row == 1) {
                    $header[$row][$column] = $data_value; 
                } else {
                    $arr_data[$row][$column] = $data_value;
                }
            }
            
            //send the data in an array format
            $data['header'] = $header;
            $data['values'] = $arr_data;
            
    }
    
    
    
    public function uploading_out_file() {
        $file_location = $_FILES['out_file']['tmp_name'];

        $file = fopen($file_location, "r");

        $lines       = file($file_location);              //file in to an array
        $second_line = explode(',', $lines[1]);     //$lines[1]->.csv second row.[0]->first row.
        $target_id   = $second_line[0];               //target id = .csv id column; $second_line[0] = .csv-> first column of second row

        $file_heading = fgetcsv($file);
        
//        $content = file_get_contents($file_location); 
//        $lines = array_map("rtrim", explode("\n", $content));
//        echo '<pre>';
//        print_r($lines);
//        die();
        
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
        $data['message'] = 'Successfully Logged out!';
        $this->session->set_userdata($data);
        redirect('welcome');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */