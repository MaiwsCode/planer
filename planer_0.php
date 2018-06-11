<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class planer extends Module { 

public function settings(){
   
    Base_ActionBarCommon::add('save', __('Save'), $this->create_back_href_js());
    Base_ActionBarCommon::add('retry', __('Refresh'), $this->create_back_href());
    
  
    
    
    }    
    

public function body(){
	
       $rs = new planer_Recordset();
        $this->rb = $rs->create_rb_module($this, 'planer');
        $this->display_module($this->rb);
        
       
    } 
}
