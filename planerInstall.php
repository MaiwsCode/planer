<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class planerInstall extends ModuleInstall {

    public function install() {
// Here you can place installation process for the module
        Base_ThemeCommon::install_default_theme($this->get_type());
        $fields = new planer_Recordset();
        $success = $fields->install();
        $fields->add_default_access();
        $fields->set_caption(_M('Plany sprzedaży tucznika'));
	$ret = true;
       // Base_BoxCommon::push_module();

        return $ret; // Return false on success and false on failure
    }

    public function uninstall() {
// Here you can place uninstallation process for the module
        Base_ThemeCommon::uninstall_default_theme($this->get_type());
        $fields = new planer_Recordset();
        $success = $fields->uninstall();
        $ret = true;
        return $ret; // Return false on success and false on failure
    }

    public function requires($v) {
// Returns list of modules and their versions, that are required to run this module
        return array(); 
    }
    public function version() {
	// Return version name of the module
        return array('1.0'); 
    }

    public function simple_setup() {
// Indicates if this module should be visible on the module list in Main Setup's simple view
        return true; 
    }

}

?>