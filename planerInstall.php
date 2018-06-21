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
        $fields1 = new planer_Recordset2();
        $success = $fields1->install();
        $fields1->add_default_access();
        $fields1->set_caption(_M('Poziomy trudności'));
        $fields = new planer_Recordset();
        $success = $fields->install();
        $fields->add_default_access();
        $fields->set_caption(_M('Plany sprzedaży tucznika'));
       /* $install = new planer_Recordset4();
        $install->install();
        $install->add_default_access();
        $install->set_caption(_M('Sprzedaż'));
        $install  = new planer_Recordset3();
        $install->install();
        $install->add_default_access();
        $install->set_caption(_M("transports"));*/
        $rbo = new RBO_RecordsetAccessor("Difficulty");
        $data = array('Dificulty_level' => 'Normalny', 'Dificulty_level_numeric' => '0');
        $event = $rbo->new_record($data);
        $event->_active = TRUE;
        $event->created_by = Acl::get_user();;
        $now = date("Y-m-d H:i:s");
        $event->created_on = $now;
        $event->save();
        $data = array('Dificulty_level' => 'Łatwy', 'Dificulty_level_numeric' => '1');
        $event = $rbo->new_record($data);
        $event->_active = TRUE;
        $event->created_by = Acl::get_user();;
        $now = date("Y-m-d H:i:s");
        $event->created_on = $now;
        $event->save();
        $data = array('Dificulty_level' => 'Trudny', 'Dificulty_level_numeric' => '2');
        $event = $rbo->new_record($data);
        $event->_active = TRUE;
        $event->created_by = Acl::get_user();
        $now = date("Y-m-d H:i:s");
        $event->created_on = $now;
        $event->save();
	$ret = true;
       // Base_BoxCommon::push_module();

        return $ret; // Return false on success and false on failure
    }

    public function uninstall() {
// Here you can place uninstallation process for the module
        Base_ThemeCommon::uninstall_default_theme($this->get_type());
        $fields = new planer_Recordset();
        $success = $fields->uninstall();
        $fields = new planer_Recordset2();
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