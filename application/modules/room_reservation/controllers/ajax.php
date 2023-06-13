<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ajax extends MX_Controller {
    public function room_cal($id = null,$request){
		
		$hall_room = $this->db->where('directory', 'hall_room')->where('status', 1)->get('module')->num_rows();
        if ($hall_room == 1) {
			$data["roomlist"] = $this->db->select('*')->from('tbl_roomnofloorassign')->where('roomid<>',NULL)->get()->result();
		}else{
			$data["roomlist"] = $this->roomreservation_model->get_all('*','tbl_roomnofloorassign','floorid');
		}
		if($request->ajax()){
			$roomdata = $data["roomlist"];
		}
		
        // $data['module'] = "room_reservation";
        // $data['page']   = "calender";   
        echo Modules::run('template/layout', $data,$roomdata);
	}
}