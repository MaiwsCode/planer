<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class planerCommon extends ModuleCommon {

   public static function menu() {
		return array(__('Module') => array('__submenu__' => 1, __('Plan sprzedaży') => array(
	    	'__icon__'=>'pig.png','__icon_small__'=>'pig.png'
			)));
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

}
