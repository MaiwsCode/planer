<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class planerCommon extends ModuleCommon
{

	public static function menu()
	{
		if (Base_AclCommon::check_permission('Plan sprzedaży') || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1") {
			return array(__('Module') => array('__submenu__' => 1, __('Plan sprzedaży') => array(
				'__icon__' => 'pig.png', '__icon_small__' => 'pig.png'
			)));
		} else {
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
	public static function write_date()
	{
		$settings = fopen("data.txt", "w");
		$txt = date("Y-m-d H:i:s");
		fwrite($settings, $txt);
		fclose($settings);
	}
	public static function critOnlyUbojnia()
	{
		return array('group' => array('ubojnia'));
	}

	/*public static function cron() {
        return array(
           'downloadDay' => 24*60,
        );
	}*/
	public static function downloadDay($date = null){
		if ($date == null){
			$date = date("Y-m-d");
		}
		$eur = planerCommon::downloadEUR($date);
		$zmp = planerCommon::downloadZMP($date);
		planerCommon::saveCurrencyOnDay($eur, $zmp, $date);
	}

	public static function downloadZMP($date) {
		$zmp = planerCommon::downloadPriceZMP(date("W", strtotime($date)));
		$zmpDate = date('Y-m-d', strtotime($zmp['date']));
		$zmp = str_replace("€","",$zmp['priceValue']);
		$zmp = floatval($zmp);
		if ($zmpDate != $date && $date >= date("Y-m-d") ) {
			$zmp = '';
		}
		return $zmp;
	 }

	 public static function downloadEUR($date) {
		$eur = planerCommon::downloadPriceFromNBP("EUR", $date);
		$eur = json_decode($eur,true);
		$eur = floatval($eur['rates'][0]['mid']);
		if ($eur == 0 ){
			$eur = '';
		}
		return $eur;
	 }

	 public static function saveCurrencyOnDay($eur, $zmp, $date){
		 $rbo = new RBO_RecordsetAccessor("currency_history");
		 if( $rbo->get_records(array("date" => $date), array(), array() ) == null ) {
			$newRecord = $rbo->new_record();
			$newRecord->zmp = $zmp;
			$newRecord->euro = $eur;
			$newRecord->date = $date;
			$newRecord->save();
		} else {
			$records = $rbo->get_records(array("date" => $date), array(), array());
			$record = null;
			foreach ($records as $r) {$record = $r;}
			if ($record['zmp'] == '' || $record['zmp'] == 0) {
				$zmp = planerCommon::downloadZMP($date);
				Utils_RecordBrowserCommon::update_record("currency_history", $record['id'], array('zmp' => $zmp ));
			}
			if ($record['euro'] == '' || $record['euro'] == 0) {
				$eur = planerCommon::downloadEUR($date);
				Utils_RecordBrowserCommon::update_record("currency_history", $record['id'], array('euro' => $eur ));
			}
		 }
	 }

	/**
	 * return JSON form NBP
	 * 
	 */
	public static function downloadPriceFromNBP($symbol, $date)
	{
		$url = "http://api.nbp.pl/api/exchangerates/rates/a/$symbol/$date/";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$price = curl_exec($ch);
		curl_close($ch);
		return $price;
	}
	public static function downloadPriceZMP($week)
	{
		$currentWeek = date("W");
		$currentWeek -= $week;
		$index = 1 * $currentWeek;
		if ($index == 0) {
			$offset = 0;
		} else if ($index >= 1) {
			$offset = 7 * $index;
		}

		if ($currentWeek >= 0) {
			$url = "https://www.griem-vh.de/marktdaten/";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$document = curl_exec($ch);
			curl_close($ch);

			$page = new DOMDocument();
			$page->loadHTML($document);
			$table = $page->getElementById("tablepress-3");
			$td = $table->getElementsByTagName("td");
			$price = $td[5 + $offset]->nodeValue;
			$price[strlen($price) - 1] = "";
			$price = str_replace(",", ".", $price);

			return [
				'week'  =>  $td[0 + $offset]->nodeValue,
				'date'  =>  $td[1 + $offset]->nodeValue,
				'price' =>  $td[3 + $offset]->nodeValue,
				'priceValue' => $price,
			];
		} else {
			return false;
		}
	}

	public static function on_create_new($defaults, $mode)
	{
		if ($mode === 'adding') {

			$week = $_SESSION['week'];
			if ($week < 10) {
				$week = "0" . $week;
			}
			$Y = $_SESSION['year'];
			$week = date("$Y-m-d", strtotime($Y . 'W' . $week));
			$defaults['date'] = $week;
			$records = Utils_RecordBrowserCommon::get_records(
				'Sales_plan',
				$crits = array("date" => $week),
				$cols = array(),
				$order = array(),
				$limit = array(),
				$admin = false
			);
			if ($records != null) {
				$defaults['difficulty_level'] = $records[0]['difficulty_level'];
			} else {
				$defaults['difficulty_level'] = '1';
			}
			return $defaults;
		}
		if ($mode === 'added') {
			planerCommon::update_records($defaults['difficulty_level'], $defaults['date']);
		}
		if ($mode === 'edited') {
			planerCommon::update_records($defaults['difficulty_level'], $defaults['date']);
		}
	}
	public static function update_records($status, $date)
	{
		$rbo_sales_plan = new RBO_RecordsetAccessor("Sales_plan");
		$records = $rbo_sales_plan->get_records(array("date" => $date), array(), array());
		foreach ($records as $record) {
			$record->difficulty_level = $status;
			$record->save();
		}
	}
	public static function getVechicleInfo($record)
	{
		$ret = "";
		$args = array("vachicle" => $record['vehicle'], 'driver' => $record['driver_1'], 'date' => $record['date']);
		$ret .= Utils_RecordBrowserCommon::record_link_open_tag('custom_agrohandel_transporty', $record->id);
		$ret .= Utils_TooltipCommon::ajax_create($record['number'], array(
			'planerCommon',
			'vechicle_get_tooltip'
		), array($args));
		return $ret;
	}
	public static function vechicle_get_tooltip($record)
	{
		$contact = new RBO_RecordsetAccessor("contact");
		$car = new RBO_RecordsetAccessor("custom_agrohandel_vehicle");
		$_contact = $contact->get_record($record["driver"]);
		$_car = $car->get_record($record["vachicle"]);
		return Utils_TooltipCommon::format_info_tooltip(array(
			'Kierowca:' => $_contact['first_name'] . " " . $_contact['last_name'],
			'Pojazd:' => $_car['name'],
			'Data' => $record['date']
		));
	}
}
