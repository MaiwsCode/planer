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
  
        Base_LangCommon::install_translations('planer');
        Base_ThemeCommon::install_default_theme ('planer');
        Base_ThemeCommon::install_default_theme($this->get_type());
        Utils_RecordBrowserCommon::register_processing_callback('Sales_plan', array($this->get_type () . 'Common', 'on_create_new'));
        $fields1 = new planer_Recordset2();
        $success = $fields1->install();
        $fields1->add_default_access();
        $fields1->set_caption(_M('Poziomy trudności'));
        $fields = new planer_Recordset();
        $success = $fields->install();
        $fields->add_access('view', 'ACCESS:employee');
        $fields->add_access('edit', 'ACCESS:manager');
        $fields->add_access('delete', 'ACCESS:manager');
        $salePlans->add_access('add', 'ACCESS:u_trader');
        $salePlans->add_access('add', 'ACCESS:manager');
        
        $fields->add_default_access();
        $fields->set_caption(_M('Plany sprzedaży tucznika'));
        $fields->set_icon (Base_ThemeCommon::get_template_filename ( 'planer', 'pig.png' ));
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
        Utils_RecordBrowserCommon::enable_watchdog('Sales_plan', array($this->get_type () . 'Common','watchdog_label'));
	$ret = true;
        return $ret; 
    }

    public function uninstall() {
        Base_ThemeCommon::uninstall_default_theme($this->get_type());
        $fields = new planer_Recordset();
        $success = $fields->uninstall();
        $fields = new planer_Recordset2();
        $success = $fields->uninstall();
        $ret = true;
        unlink("settings.txt");
        return $ret; 
    }

    public function requires($v) {
        return array(); 
    }
    public function version() {
        return array('1.0'); 
    }

    public function simple_setup() {
        return true; 
    }

}

?>