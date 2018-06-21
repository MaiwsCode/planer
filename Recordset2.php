<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
 
class planer_Recordset2  extends RBO_Recordset {
 
    function table_name() { // - choose a name for the table that will be stored in EPESI database
 
        return 'Difficulty';
 
    }
 
    function fields() { // - here you choose the fields to add to the record browser

        $dificulty_text = new RBO_Field_Text(_M("Dificulty level"));
        $dificulty_text->set_required()->set_visible()->set_length(50);
        
        $dificulty_numeric = new RBO_Field_Integer(_M("Dificulty level numeric"));
        $dificulty_numeric->set_visible()->set_required();       
        
        return array($dificulty_text, $dificulty_numeric); // - remember to return all defined fields
 
 
    }
}   

        
    
?>