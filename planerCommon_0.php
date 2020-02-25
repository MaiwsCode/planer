<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class planerCommon extends ModuleCommon {
	
   public static function menu() {
	if (Base_AclCommon::check_permission('Plan sprzedaży') || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ){
		return array(__('Module') => array('__submenu__' => 1, __('Plan sprzedaży') => array(
	    	'__icon__'=>'pig.png','__icon_small__'=>'pig.png'
			)));
		}else{
			return array();
		}
	}
    public static function watchdog_label($rid = null, $events = array(), $details = true)
    {
    	return Utils_RecordBrowserCommon::watchdog_label(
    			'Sales_plan',
    			__('Plan sprzedaży tucznika'),
    			$rid,
    			$events,
    			'text',
    			$details
    	);
	}
	public static function write_date(){
		$settings = fopen("data.txt", "w");
		$txt = date("Y-m-d H:i:s");
        fwrite($settings, $txt);
        fclose($settings);

	}
	public static function critOnlyUbojnia() {
    	return array('group' => array('ubojnia') );
	}
	public static function on_create_new($defaults, $mode){
		if ($mode === 'adding'){

			$week = $_SESSION['week'];
			if($week< 10){
				$week = "0".$week;
			}
			$Y = $_SESSION['year'];
			$week = date("$Y-m-d", strtotime($Y.'W'.$week));
			$defaults['date'] = $week;
			$records = Utils_RecordBrowserCommon::get_records('Sales_plan', $crits = array("date" =>$week ), $cols = array(), $order = array(), 
			$limit = array(), $admin = false);
			if($records != null){
				$defaults['difficulty_level'] = $records[0]['difficulty_level'];
			}
			else{
				$defaults['difficulty_level'] = '1';
			}
			return $defaults;
		}
		if ($mode === 'added'){
			planerCommon::update_records($defaults['difficulty_level'],$defaults['date']);
		}
		if ($mode === 'edited'){
			planerCommon::update_records($defaults['difficulty_level'],$defaults['date']);
		}
	}
	public static function update_records($status,$date){
		$rbo_sales_plan = new RBO_RecordsetAccessor("Sales_plan");
		$records = $rbo_sales_plan->get_records(array("date"=>$date),array(),array());
		foreach($records as $record){
			$record->difficulty_level = $status;
			$record->save();
		}
	}
	public static function getVechicleInfo($record){
		$ret = "";
		$args = array("vachicle"=>$record['vehicle'], 'driver'=> $record['driver_1'],'date'=>$record['date']);
		$ret .= Utils_RecordBrowserCommon::record_link_open_tag ( 'custom_agrohandel_transporty', $record->id );
		$ret .= Utils_TooltipCommon::ajax_create ( $record['number'], array (
				'planerCommon',
				'vechicle_get_tooltip'
		), array ($args));
		return $ret;
	}
	public static function vechicle_get_tooltip($record){
		$contact = new RBO_RecordsetAccessor("contact");  
		$car = new RBO_RecordsetAccessor("custom_agrohandel_vehicle");  
		$_contact = $contact->get_record($record["driver"]);
		$_car = $car->get_record($record["vachicle"]);
		return Utils_TooltipCommon::format_info_tooltip(array(
			'Kierowca:' => $_contact['first_name']." ".$_contact['last_name'],
			'Pojazd:' => $_car['name'],
			'Data' => $record['date']));
	}
}
