<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
 
class planer_Recordset3  extends RBO_Recordset {
 
    function table_name() { // - choose a name for the table that will be stored in EPESI database
 
        return 'Transport';
 
    }
 
    function fields() { // - here you choose the fields to add to the record browser

        $date = new RBO_Field_Date(_M("Date"));
        $date->set_required()->set_visible();
        
        $company = new RBO_Field_Select(_M('Company Name'));
        $company->from('company')->fields('company_name')->set_visible()->set_required();
        
        $amount = new RBO_Field_Integer(_M("amount"));
        $amount->set_required()->set_visible();
        
        
        return array($date, $company,$amount); // - remember to return all defined fields
 
 
    }
}   

        
    
?>