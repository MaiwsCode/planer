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

    public static function installer($tableClassName,$ViewText){
        $rbo = $tableClassName;
        $install = new $tableClassName();
        $install->install();
        $install->add_default_access();
        $install->set_caption(_M($ViewText));
    }

}
