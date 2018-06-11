<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
 
class planer_Recordset  extends RBO_Recordset {
 
    function table_name() { // - choose a name for the table that will be stored in EPESI database
 
        return 'Sales_plan';
 
    }
 
    function fields() { // - here you choose the fields to add to the record browser
        
        
        $ammount = new RBO_Field_Integer(_M('Ammount'));
       // $ammount->
        $ammount->set_required()->set_visible();
                    
        $company = new RBO_Field_Select('Select');
        $company->from('company')->fields('company_name')->set_visible();
        
        $price = new RBO_Field_Float(_M("Price"));
        $price->set_required()->set_visible();
        
        $date = new RBO_Field_Date(_M('Date'));
        $date->set_required()->set_visible(); //->set_extra(new RBO_Field_Timestamp);
 
        $description_trader = new RBO_Field_LongText(_M('Description trader'));
        $description_trader->set_visible();
        
        $description_manager = new RBO_Field_LongText(_M('Description Manager'));
        $description_manager->set_visible();
        
        
 
        return array($ammount, $company, $price,$date,$description_trader,$description_manager); // - remember to return all defined fields
 
 
    }
}
?>