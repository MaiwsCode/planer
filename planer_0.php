<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class planer extends Module { 

public function settings(){
    }    

    public function body(){

    //see record
        //Base_ThemeCommon::install_default_theme($this->get_type());
        Base_ThemeCommon::install_default_theme ('planer');
        $theme = $this->init_module('Base/Theme');
        $theme->assign("css", Base_ThemeCommon::get_template_dir());
        $rbo = new RBO_RecordsetAccessor("Sales_plan");
        $companes = new RBO_RecordsetAccessor("company");
        $date = new PickDate();
        $days = array();
        //array('change_status' => '2018-07-19','status'=> '1'))
        if(isset ($_REQUEST["change_status"])){
            $date = $_REQUEST["change_status"];
            $status = $_REQUEST["status"];
            $records = Utils_RecordBrowserCommon::get_records('Sales_plan', array("date"=> $date),array(),array());
			foreach($records as $record_){
			Utils_RecordBrowserCommon::update_record('Sales_plan', $record_['id'], array('difficulty_level' => $status),$all_fields=false, 
			$date=null, $dont_notify=false);

        }


        if(!isset($_REQUEST['week_number']) && !isset($_SESSION['week'])){
            $today = date("Y-m-d");
            $week_num = $date->get_week_number($today);  
            $_SESSION['week'] = $week_num;     
        }
        elseif(isset($_REQUEST['week_number'])){
            $week_num= $_REQUEST['week_number'];   
            $_SESSION['week'] = $week_num;  
        }
        elseif(isset($_SESSION['week']) && !isset($_REQUEST['week_number'])){
            $week_num= $_SESSION['week'];  
        }
        if(isset ($_REQUEST["delete_record"])){
            $delete_record = $_REQUEST['delete_record'];
            $rbo->delete_record($delete_record);
        }
        if(isset($_REQUEST['copy'])){
            if(Addons::can_copy($week_num)){
                $sales = new RBO_RecordsetAccessor("Sales_plan");
                $from = $week_num - 1;
                $start_date = $date->monday_of_week($from); ;
                $end_date = $date->add_days($date->monday_of_week($from),4);
                $records = $sales->get_records(array('>=date' => $start_date, '<=date' => $end_date));
                foreach($records as $record){
                    $new_record = array("company_name" => $record['company_name'] , "amount" => $record['amount'] ,
                     "date" => $date->add_days($record["date"],7) ,"description_trader" => $record["description_trader"] ,
                      "description_manager" => $record["description_manager"], "difficulty_level" => $record["difficulty_level"]);
                    $now = date("Y-m-d H:i:s");
                    $new = $sales->new_record($new_record);
                    $new->created_by = Acl::get_user();
                    $new->created_on = $now;  
                    $id = $user->id;
                    $new->save();    
                }
                Addons::copied($week_num);
            }
        }
        //sortowanie wg nazw firm
        function sortByCompanyName($array){
            $list_of_company = [];
            $new_list = [];
            foreach($array as $record){
                    $list_of_company[] = strip_tags($record['company_name']);
            }
            sort($list_of_company);
            foreach($list_of_company as $alfabetic){
                foreach($array as $record){
                    if(strip_tags($record['company_name']) == $alfabetic){
                            $new_list[] = $record;
                    }
                }
            }
            return $new_list;
        }

        //nowy record
        $x = 0;
        Base_ActionBarCommon::add(
            'add',
            __('New'), 
            Utils_RecordBrowserCommon::create_new_record_href('Sales_plan', $this->custom_defaults),
            null,
            $x
        );
        $x++;
        //poprzedni tydzien
        if($week_num != 1){
            Base_ActionBarCommon::add(
                Base_ThemeCommon::get_template_file($this->get_type(), 'prev.png'),
                "Poprzedni tydzień",
                $this->create_href ( array ('week_number' => $week_num-1)),
                null,
                $x
            );
            $x++;
        }
        // 7 tygodni do wyboru
        for($i = $week_num - 3 ; $i < $week_num + 4;$i++){
            if($i > 52 || $i <  1) {}
            else{
                if($week_num == $i){ $icon = 'cal2.png'; }else{ $icon = 'cal.png'; }
                    Base_ActionBarCommon::add(
                        Base_ThemeCommon::get_template_file($this->get_type(), $icon),
                        "Tydzień - ".$i,
                        $this->create_href ( array ('week_number' => $i)),
                        null,
                        $x
                    );
                }
            $x = $x +1;
        }
        //nastepny tydzien
        if($week_num != 52){
            Base_ActionBarCommon::add(
                Base_ThemeCommon::get_template_file($this->get_type(), 'next.png'),
                "Następny tydzień",
                $this->create_href ( array ('week_number' => $week_num+1)),
                null,
                $x
            );
            $x++;
        }
        //Kopiuj z poprzedni tydzien
        if(Addons::can_copy($week_num)){
            Base_ActionBarCommon::add('add', 
                     __('Copy from last week'), 
                     $this->create_href ( array ('copy' => TRUE,'week_number' => $week_num )),
                     null,
                     $x
                 );
             $x++;
         }
        $select_options = "<li><a ".$this->create_href(array('week_number' => $date->get_week_number(date('Y-m-d'))))."> Wróć do bieżącego tygodnia </a></li>";
        for($i = 1; $i<=52;$i++){
            $select_options .= "<li><a ".$this->create_href(array('week_number' => $i))."> Tydzień - ".$i." </a></li>";
        }
        
        $select = "<ul class='drops'>
                    <li>
                        <a href='#'>Wybierz tydzień </a> <img src='data/Base_Theme/templates/default/planer/drop.png' width=25 height=25 />
                            <ul>".$select_options."
                        </ul></li></ul>";
        // zamowione 
        $all_zam = 0;
        $pon = $rbo->get_records(array('date' => $date->monday_of_week($week_num)),array(),array('company_name' => "ASC"));
        $pon = Rbo_Futures::set_related_fields($pon, 'company_name');
        foreach($pon as $p){
            $all_zam += $p["amount"];
            $p['amount'] = $p->record_link($p['amount'],$nolink = false,$action = 'view');
            if(Base_AclCommon::check_permission("manager") == "1" || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ){
                if(strlen($p['Description trader']) > 0 || strlen($p['Description Manager']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader'].
                    "</div>", "Manager: " => "<div class='custom_info'>".$p['Description Manager']."</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{$infobox = "---";}
            
                $p['notka'] = $infobox;
                $p["edit"] = $p->record_link('<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/edit.png" border="0" alt="Edytuj">',$nolink=false,'edit');
                $del = $this->create_href(array("delete_record" => $p['id']));
                $deli = "<a $del> <img border='0' src='data/Base_Theme/templates/default/Utils/Calendar/delete.png' alt='Usuń' /></a>";
                $p["delete"] = $deli;
            }
            else{
                if(strlen($p['Description trader']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader']. "</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["delete"] = '';
                $p["edit"] = '';
            }
            
        }
        $pon = sortByCompanyName($pon);
        $wt = $rbo->get_records(array('date' => $date->add_days($date->monday_of_week($week_num), 1)),array(),array('company_name' => "ASC"));
        $wt = Rbo_Futures::set_related_fields($wt, 'company_name');
        foreach($wt as $p){
            $all_zam += $p["amount"];
            $p['amount'] = $p->record_link($p['amount'],$nolink = false,$action = 'view');
            if(Base_AclCommon::check_permission("manager") == "1" || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ){
                if(strlen($p['Description trader']) > 0 || strlen($p['Description Manager']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader'].
                    "</div>", "Manager: " => "<div class='custom_info'>".$p['Description Manager']."</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }
                else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["edit"] = $p->record_link('<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/edit.png" border="0" alt="Edytuj">',$nolink=false,'edit');
                $del = $this->create_href(array("delete_record" => $p['id']));
                $del = "<a $del> <img border='0' src='data/Base_Theme/templates/default/Utils/Calendar/delete.png' alt='Usuń' /></a>";
                $p["delete"] = $del;
            }
            else{
                if(strlen($p['Description trader']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader']. "</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["delete"] = '';
                $p["edit"] = '';
            }
        }
        $wt = sortByCompanyName($wt);
        $sr = $rbo->get_records(array('date' => $date->add_days($date->monday_of_week($week_num), 2)),array(),array('company_name' => "ASC"));
        $sr = Rbo_Futures::set_related_fields($sr, 'company_name');
        foreach($sr as $p){
            $all_zam += $p["amount"];
            $p['amount'] = $p->record_link($p['amount'],$nolink = false,$action = 'view');
            if(Base_AclCommon::check_permission("manager") == "1" || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ){
                if(strlen($p['Description trader']) > 0 || strlen($p['Description Manager']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader'].
                    "</div>", "Manager: " => "<div class='custom_info'>".$p['Description Manager']."</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["edit"] = $p->record_link('<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/edit.png" border="0" alt="Edytuj">',$nolink=false,'edit');
                $del = $this->create_href(array("delete_record" => $p['id']));
                $del = "<a $del> <img border='0' src='data/Base_Theme/templates/default/Utils/Calendar/delete.png' alt='Usuń' /></a>";
                $p["delete"] = $del;
            }
            else{
                if(strlen($p['Description trader']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader']. "</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["delete"] = '';
                $p["edit"] = '';
            }
        }
        $sr = sortByCompanyName($sr);
        $czw = $rbo->get_records(array('date' => $date->add_days($date->monday_of_week($week_num), 3)),array(),array('company_name' => "ASC"));
        $czw = Rbo_Futures::set_related_fields($czw, 'company_name');
        foreach($czw as $p){
            $all_zam += $p["amount"];
            $p['amount'] = $p->record_link($p['amount'],$nolink = false,$action = 'view');
            if(Base_AclCommon::check_permission("manager") == "1" || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ){
                if(strlen($p['Description trader']) > 0 || strlen($p['Description Manager']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader'].
                    "</div>", "Manager: " => "<div class='custom_info'>".$p['Description Manager']."</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["edit"] = $p->record_link('<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/edit.png" border="0" alt="Edytuj">',$nolink=false,'edit');
                $del = $this->create_href(array("delete_record" => $p['id']));
                $del = "<a $del> <img border='0' src='data/Base_Theme/templates/default/Utils/Calendar/delete.png' alt='Usuń' /></a>";
                $p["delete"] = $del;
            }
            else{
                if(strlen($p['Description trader']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader']. "</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["delete"] = '';
                $p["edit"] = '';
            }
        }
        $czw = sortByCompanyName($czw);
        $pt = $rbo->get_records(array('date' => $date->add_days($date->monday_of_week($week_num), 4)),array(),array('company_name' => "ASC"));
        $pt = Rbo_Futures::set_related_fields($pt, 'company_name');
        foreach($pt as $p){
            $all_zam += $p["amount"];
            $p['amount'] = $p->record_link($p['amount'],$nolink = false,$action = 'view');
            if(Base_AclCommon::check_permission("manager") == "1" || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ){
                if(strlen($p['Description trader']) > 0 || strlen($p['Description Manager']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader'].
                    "</div>", "Manager: " => "<div class='custom_info'>".$p['Description Manager']."</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["edit"] = $p->record_link('<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/edit.png" border="0" alt="Edytuj">',$nolink=false,'edit');
                $del = $this->create_href(array("delete_record" => $p['id']));
                $del = "<a $del> <img border='0' src='data/Base_Theme/templates/default/Utils/Calendar/delete.png' alt='Usuń' /></a>";
                $p["delete"] = $del;
            }
            else{
                if(strlen($p['Description trader']) > 0){
                    $ar = array("Handlowiec: " => "<div class='custom_info'>".$p['Description trader']. "</div>");
                    $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                    $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                }else{
                    $infobox = "---";
                }
                $p['notka'] = $infobox;
                $p["delete"] = '';
                $p["edit"] = '';
                }
        }
        $all_bought_week =0;
        $all_transported_week = 0;
        $pt = sortByCompanyName($pt);
        //potrzeba wstawić prawidłową nazwe tabeli
        $bought = new RBO_RecordsetAccessor('custom_agrohandel_purchase_plans');
        $pon_bought = $bought->get_records(array('planed_purchase_date' => $date->monday_of_week($week_num),'~status' => "%purchased%"),
                                           array("Company" => "ASC"));
        $wt_bought = $bought->get_records(array('planed_purchase_date' => $date->add_days($date->monday_of_week($week_num), 1),'~status' => "%purchased%"),
                                           array("Company" => "ASC"));
        $sr_bought = $bought->get_records(array('planed_purchase_date' => $date->add_days($date->monday_of_week($week_num), 2),'~status' => "%purchased%"),
                                           array("Company" => "ASC"));
        $czw_bought = $bought->get_records(array('planed_purchase_date' => $date->add_days($date->monday_of_week($week_num), 3),'~status' => "%purchased%"),
                                           array("Company" => "ASC"));
        $pt_bought = $bought->get_records(array('planed_purchase_date' => $date->add_days($date->monday_of_week($week_num), 4),'~status' => "%purchased%"),
                                           array("Company" => "ASC"));
        // kupione
        $pon_companes = array();
        $wt_companes = array();
        $sr_companes = array();
        $czw_companes = array();
        $pt_companes = array();
        foreach($pon as $pone){
            array_push($pon_companes , $pone['company_name']);
        }
        foreach($wt as $pone){
            array_push($wt_companes , $pone['company_name']);
        }
        foreach($sr as $pone){
            array_push($sr_companes , $pone['company_name']);
        }
        foreach($czw as $pone){
            array_push($czw_companes , $pone['company_name']);
        }
        foreach($pt as $pone){
            array_push($pt_companes , $pone['company_name']);
        }
        $pon_companes = array_count_values($pon_companes);
        $wt_companes = array_count_values($wt_companes);
        $sr_companes = array_count_values($sr_companes);
        $czw_companes = array_count_values($czw_companes);
        $pt_companes = array_count_values($pt_companes);
        $i  = 1;
        $indexer = array();
        foreach($pon_companes as $com){
            $indexer[$i] = $com;
            $i++;
        }
        foreach($wt_companes as $com){
            $indexer[$i] = $com;
            $i++;
        }
        foreach($sr_companes as $com){
            $indexer[$i] = $com;
            $i++;
        }
        foreach($czw_companes as $com){
            $indexer[$i] = $com;
            $i++;
        }
        foreach($pt_companes as $com){
            $indexer[$i] = $com;
            $i++;
        }
        
        //dostarczone
        //potrzena tabela z Raport z rozladunku
        $transported = new RBO_RecordsetAccessor("custom_agrohandel_transporty"); //custom_agrohandel_transporty Transport
        $trans_pon = array();
        $trans_wt = array();
        $trans_sr = array();
        $trans_czw = array();
        $trans_pt = array();
        $transports = [];
        $company_field = "company"; ///company company_name
        $amount = "iloscrozl"; //iloscrozl amount
        $t_pon = $transported->get_records(array('date' => $date->monday_of_week($week_num)),array(),array($company_field => "ASC"));
        foreach($t_pon as $t){
            $x = $t->get_val($company_field,$nolink = TRUE);
            $trans_pon[$x] += $t[$amount];
            $all_transported_week +=  $t[$amount];
        }
        $t_wt = $transported->get_records(array('date' =>$date->add_days($date->monday_of_week($week_num), 1)),array(),array($company_field => "ASC"));
        foreach($t_wt as $t){
            $x = $t->get_val($company_field,$nolink = TRUE);
            $trans_wt[$x] += $t[$amount];
            $all_transported_week +=  $t[$amount];
        }
        $t_sr = $transported->get_records(array('date' => $date->add_days($date->monday_of_week($week_num), 2)),array(),array($company_field => "ASC"));
        foreach($t_sr as $t){
            $x = $t->get_val($company_field,$nolink = TRUE);
            $trans_sr[$x] += $t[$amount];
            $all_transported_week +=  $t[$amount];
        }
        $t_czw = $transported->get_records(array('date' =>$date->add_days($date->monday_of_week($week_num), 3)),array(),array($company_field => "ASC"));
        foreach($t_czw as $t){
            $x = $t->get_val($company_field,$nolink = TRUE);
            $trans_czw[$x] += $t[$amount];
            $all_transported_week +=  $t[$amount];
        }
        $t_pt = $transported->get_records(array('date' => $date->add_days($date->monday_of_week($week_num), 4)),array(),array($company_field => "ASC"));
        foreach($t_pt as $t){
            $x = $t->get_val($company_field,$nolink = TRUE);
            $trans_pt[$x] += $t[$amount];
            $all_transported_week +=  $t[$amount];
        }
        $week_trans = array();
        $week_transported = $transported->get_records(array('>=date' => $date->add_days($date->monday_of_week($week_num),0),
        '<=date' => $date->add_days($date->monday_of_week($week_num), 4)),array(),array($company_field => "ASC"));
        foreach($week_transported as $t){
            $x = $t->get_val($company_field,$nolink = TRUE);
            $week_trans[$x] += $t[$amount];
        }
        $transports[1] = $trans_pon;
        $transports[2] = $trans_wt;
        $transports[3] = $trans_sr;
        $transports[4] = $trans_czw;
        $transports[5] = $trans_pt;
        $theme->assign('trans',$transports);
        $starter = $indexer[0];
        $theme->assign('all_zam',$all_zam);
        $theme->assign('starter',$starter);
        $theme->assign('indexer',$indexer);
        $theme->assign('select',$select);
        //purchased or Kupione => Status   Amount   Company  planed_purchase_date  Company
        $amount_sum = array(1=>$this->sum_records($pon_bought,'Amount'),
        2=>$this->sum_records($wt_bought,'Amount'),3=>$this->sum_records($sr_bought,'Amount'),
        4=>$this->sum_records($czw_bought,'Amount'),
        5=>$this->sum_records($pt_bought,'Amount'));
        foreach($amount_sum as $sum){
            $all_bought_week += $sum;
        }
        array_push($days,$pon);
        array_push($days,$wt);
        array_push($days,$sr);
        array_push($days,$czw);
        array_push($days,$pt);
        //dni tygodnia
        $days_text = array(
            1=>"PONIEDZIAŁEK",
            2=>"WTOREK",
            3=>"ŚRODA",
            4=>"CZWARTEK",
            5=>"PIĄTEK",
        );
        $sel_opt = "";
        $sel_opt .= "<li><a ".$this->create_href(array('change_status' => '2018-07-19','status'=> '1'))."> Normalny </a></li>";
        $sel_opt .= "<li><a ".$this->create_href(array('change_status' => '2018-07-19','status'=> '2'))."> Łatwy </a></li>";
        $sel_opt .= "<li><a ".$this->create_href(array('change_status' => '2018-07-19','status'=> '3'))."> Trudny </a></li>";
        $sel = "<ul class='drops'>
        <li>
            <a href='#'>Wybierz status  </a> <img src='data/Base_Theme/templates/default/planer/drop.png' width=25 height=25 />
                <ul>".$sel_opt."
            </ul></li></ul>";
        $theme->assign('sel',$sel);
        $sumary_week = $rbo->get_records(array('>=date' => $date->monday_of_week($week_num), 
        '<=date' => $date->add_days($date->monday_of_week($week_num), 4)), 
        array(),array());
        $mach_week_with_tr = $rbo->get_records(array('>=date' => $date->monday_of_week($week_num), 
        '<=date' => $date->add_days($date->monday_of_week($week_num), 4)), 
        array(),array());
        $mach_tr_with_week = $transported->get_records(array('>=date' => $date->monday_of_week($week_num), 
        '<=date' => $date->add_days($date->monday_of_week($week_num), 4)),array(),array());  
        $missing_pon = array();
        $missing_wt = array();
        $missing_sr = array();
        $missing_czw = array();
        $missing_pt = array();
        $missing_all = array();
        if(count($mach_week_with_tr) != count($mach_tr_with_week)){
            foreach($mach_tr_with_week as $trans){
                $exist = false;
                foreach($mach_week_with_tr as $plan){
                    if($trans['company'] == $plan['company_name']){
                        $exist = true;
                        break;
                    }
                }
                if($exist == false){
                    $ubojnia = true;
                    $_transport = $companes->get_records(array('id' => $trans['company'], 'group' => 'baza_tr'),array(),array());
                    if($_transport != null){
                        $ubojnia = false;
                    }
                    if($ubojnia == true){
                        $amount = 0;
                        $once = $trans->to_array();
                        $once = $once["zakupy"];
                        foreach($once as $one){
                            $value  = $bought->get_record($one);
                            $amount += $value['amount'];
                            $all_transported_week += $value['iloscrozl'];
                        }
                        $trans['company'] =  $trans->get_val('company');
                        $trans['amm'] = $amount; 
                        $missing_all[] = $trans;
                        $dayofweek = date('w', strtotime($trans['date']));  
                        if($dayofweek == 1){ $missing_pon[] = $trans;}
                        else if($dayofweek == 2){ $missing_wt[] = $trans;}
                        else if($dayofweek == 3){ $missing_sr[] = $trans;}
                        else if($dayofweek == 4){ $missing_czw[] = $trans;}
                        else if($dayofweek == 5){ $missing_pt[] = $trans;} 
                    }               
                }
            }
        }       
        /*$missing_pon = Rbo_Futures::set_related_fields($missing_pon, 'company');
        $missing_wt = Rbo_Futures::set_related_fields($missing_wt, 'company');
        $missing_sr = Rbo_Futures::set_related_fields($missing_sr, 'company');
        $missing_czw = Rbo_Futures::set_related_fields($missing_czw, 'company');
        $missing_pt = Rbo_Futures::set_related_fields($missing_pt, 'company');
        $missing_all = Rbo_Futures::set_related_fields($missing_all, 'company');*/
        $week_amount_sum = 0;   
        for($i=0;$i<=4;$i++){
            $week_bought = $transported->get_records(array('date' =>$date->add_days($date->monday_of_week($week_num), $i)),array(),array());
            foreach($week_bought as $day){
                $once = $day->to_array();
                $once = $once["zakupy"];
                foreach($once as $one){
                    $value  = $bought->get_record($one);
                    $week_amount_sum += $value['amount'];
                }
            }                 
        } 
        $missing = array();
        $missing[1] = $missing_pon;
        $missing[2] = $missing_wt;
        $missing[3] = $missing_sr;
        $missing[4] = $missing_czw;
        $missing[5] = $missing_pt;
        // missing[0] = missing_pon -> records
        $sum_week = array();
        foreach($sumary_week as $sum){
            try{
            $value = $sum_week[$sum->get_val("company_name",$nolink=true)]["val"];
            }catch(Exception $e){$value = 0;}
            $value = intval($value) + intval($sum['amount']); 
            $sum_week[$sum->get_val("company_name",$nolink=true)] = array("val" => $value,
                                                                        "name" =>$sum->get_val("company_name",$nolink=true));
        }
        $week_transported = $this->sum_records($week_transported,$amount);
        $theme->assign("sumary_week",$sum_week);
        $theme->assign("week_bought",$week_amount_sum);
        $theme->assign("week_transported",$week_trans);
        $theme->assign('days_text',$days_text);
        $theme->assign('missing',$missing);
        $theme->assign('missing_all',$missing_all);
        $theme->assign('all_bought',$all_bought_week);
        $theme->assign('all_transp',$all_transported_week);
        $theme->assign('amount_sum',$amount_sum);
        $theme->assign('start',1);
        $theme->assign('days',$days);
        $theme->assign('week_number', $week_num);
        $theme->assign ( 'action_buttons', $buttons );
        $theme->display();
    }
}
    public function sum_records($records,$columnName){
        $value = 0;
        foreach($records as $record){
           $value += $record[$columnName];
        }
        return $value;
    }  
}

class PickDate{
    
    public function current_day(){
        $date = date('Y-m-d');
        return $date; 
    }
    
    public function this_week_start($date){
        $week = date("Y-m-d", strtotime('monday this week',strtotime($date)));
        return $week;
        
    }

    public function monday_of_week($number_of_week){
        $Y = date('Y');
        $week = date("Y-m-d", strtotime($Y.'W'.$number_of_week));
        return $week;
    }
    public function add_days($start_date,$numbers_of_day_to_add){
        $date = strtotime($start_date);
        $days = $numbers_of_day_to_add*(60*60*24);
        $date = $date + $days;
        $date = date('Y-m-d',$date);
        return $date;
    }
    
    public function get_week_number($date){
        if(isset($date)){
        $week = date("W",strtotime($date));
        }
        else{
            $week = date("W");
        }
        return $week;
    }
    public function get_day($date){
        return date('Y-m-d', strtotime($date));
    }
    
    public function get_week_name($date){
        return date('l', strtotime($date));
    }
    
}
class Rbo_Futures{
    public static function set_related_fields($varible, $name){
           foreach($varible as $edit){
            $edit[$name] = ($edit->get_val($name));
        }
        return $varible;
    }
}

class Addons{
    public static function can_copy($week_selected){

        // copied = 1 nocopied = 0
        $settings = fopen("settings.txt", "rw");
        $can = true;
        $date = new PickDate(); 
        $this_week = $week_selected;
        $last_week = $this_week - 1;
        $data = fread($settings,filesize('settings.txt'));
        fclose($settings);
        $data =  explode("\n", $data);
        foreach($data as $day){
            if($day == $this_week){
                $can = false;
            }
        }
        return $can;
    }
    public static function copied($week){

        $date = new PickDate(); 
        $today = date("Y-m-d");
        $settings = fopen("settings.txt", "a");
        fwrite($settings, "\n". $week);
        fclose($settings);
    }
}