<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
 
class planer_Recordset  extends RBO_Recordset {
 
    function table_name() { // - choose a name for the table that will be stored in EPESI database
 
        return 'Sales_plan';
 
    }
 
    function fields() { // - here you choose the fields to add to the record browser

      //  Base_LangCommon::
        $ammount = new RBO_Field_Integer(_M('Amount'));
       // $ammount->
        $ammount->set_required()->set_visible();
                    
        $company = new RBO_Field_Select(_M('Company Name'));
        $company->from('company')->fields('company_name')->set_visible()->set_required();
        
        $price = new RBO_Field_Float(_M("Price"));
        $price->set_required()->set_visible();
        
        $date = new RBO_Field_Date(_M('Date'));
        $date->set_required()->set_visible(); //->set_extra(new RBO_Field_Timestamp);
 
        $description_trader = new RBO_Field_LongText(_M('Description trader'));
        $description_trader->set_visible();
        
        $description_manager = new RBO_Field_LongText(_M('Description Manager'));
        $description_manager->set_visible();
        
       // $status = array(0 => "Normalny", 1=>"Łatwy (wysyp świń)",2 => "Trudny (świński dołek)" );
        
        $type = new RBO_Field_Select(_M('Difficulty level'));
     //   $type->from('difficulty_level')->fields('difficulty_level')->set_visible()->set_required();
        $type->from('Difficulty')->fields('Dificulty_level')->set_visible()->set_required();
        return array($ammount, $company, $price,$date,$description_trader,$description_manager,$type); // - remember to return all defined fields
 
 
    }
    
}

?>