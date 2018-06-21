<?php
class planer_Recordset4 extends RBO_Recordset {
 
    function table_name() {
        return 'custom_agrohandel_purchase_plans';
    }
 
 
    function fields() {

        $purchase_plan_date = new RBO_Field_Date('Planed Purchase Date');
        $purchase_plan_date->set_required()->set_visible();

		$purchase_plan_company = new RBO_Field_Select('Company');
		$purchase_plan_company->from('company')->set_required()->fields('Company Name')->set_visible();

        $purchase_plan_amount = new RBO_Field_Integer('Amount');
        $purchase_plan_amount->set_required()->set_visible();
        
        $purchase_plan_price = new RBO_Field_Currency('Price');
        $purchase_plan_price->set_visible();

     //   $purchase_plan_deduction = new RBO_Field_Float('Deduction');
     //   $purchase_plan_deduction->set_visible()->set_QFfield_callback(array('Custom_AgrohandelCommon','QFfield_deduction'));

        $purchase_plan_note = new RBO_Field_LongText('Note');
        $purchase_plan_note->set_visible();

		//$purchase_plan_probability = new RBO_Field_CommonData('Probability');
      //  $purchase_plan_probability->from('Agrohandel/purchase_probability')->set_required()->set_visible();

		$purchase_plan_status = new RBO_Field_Text('Status');
        $purchase_plan_status->set_required()->set_visible()->set_length("255");

        return array($purchase_plan_date, $purchase_plan_company, $purchase_plan_amount,$purchase_plan_status,$purchase_plan_price, $purchase_plan_note);
    }
}
?>
