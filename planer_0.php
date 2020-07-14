<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class planer extends Module
{


    public function body()
    {
		Base_ThemeCommon::install_default_theme($this->get_type());
        //global variables
        $rboSalesPlan = new RBO_RecordsetAccessor("Sales_plan");
        $rboComanies = new RBO_RecordsetAccessor("company");
        $rboBought = new RBO_RecordsetAccessor("custom_agrohandel_purchase_plans"); 
        $rboTransport = new RBO_RecordsetAccessor("custom_agrohandel_transporty");  
        
        $deleteImg = "<img border='0' src='data/Base_Theme/templates/default/Utils/Calendar/delete.png' alt='Usuń' />";
        $viewImg = '<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/view.png" border="0" alt="Podgląd">';
        $editImg = '<img class="action_button" src="data/Base_Theme/templates/default/Utils/GenericBrowser/edit.png" border="0" alt="Edytuj">';
        $wordPNG = '<img src="data/Base_Theme/templates/default/planer/word.png" height="16" width="16" />';
        $normalImg  = "<img src='data/Base_Theme/templates/default/planer/normal.png'  width=15 height=15 />";
        $hardImg  = "<img src='data/Base_Theme/templates/default/planer/bad.png'  width=15 height=15 />";
        $easyImg  = "<img src='data/Base_Theme/templates/default/planer/good.png'  width=15 height=15 />";


        //jump to record
        if (isset($_REQUEST['__jump_to_RB_table'])) {
            $rs = new RBO_RecordsetAccessor($_REQUEST['__jump_to_RB_table']);
            $rb = $rs->create_rb_module($this);
            $this->display_module($rb);
        }

        //REQUESTS
        if (!$this->get_module_variable("year")) {
            $this->set_module_variable('year', date("Y"));
        }
        if (isset($_REQUEST['year'])) {
            $this->set_module_variable('year', $_REQUEST['year']);
        }
        $year = $this->get_module_variable("year");
        $date = new PickDate($year);

        if (!$this->get_module_variable('weekNumber')) {
            $today = date("Y-m-d");
            $time = strtotime($today);
            $dayInWeek = date("N", $time);
            $week = date("W", $time);
            if ($dayInWeek > 1 && date("n", $time) == 1 && $week == 1) {
                $time -= ($dayInWeek - 1)  * 60 * 60 * 24;
                $year = date("Y", $time);
                $date->update_year($year);
            }
            $today = date("Y-m-d", $time);
            $wn = $date->get_week_number($today);
            $this->set_module_variable('weekNumber', $wn);
        }

        if (isset($_REQUEST['weekNumber'])) {
            $this->set_module_variable('weekNumber', $_REQUEST['weekNumber']);
        }
        if (!$this->get_module_variable('blueZone')) {
            $this->set_module_variable('blueZone', '0');
        }

        if (isset($_REQUEST['blueZone'])) {
            $blueZone = $_REQUEST['blueZone'];
            $this->set_module_variable('blueZone', $blueZone);
        }
        $blueZone =  $this->get_module_variable("blueZone");

        $weekNumber = $this->get_module_variable("weekNumber");

        if (isset($_REQUEST["delete_record"])) {
            $delete_record = $_REQUEST['delete_record'];
            $rboSalesPlan->delete_record($delete_record);
        }

        if (isset($_REQUEST["changeStatus"])) {
            $day = $_REQUEST["changeStatus"];
            $status = $_REQUEST["status"];
            $records = Utils_RecordBrowserCommon::get_records('Sales_plan', array("date" => $day), array(), array());
            foreach ($records as $record) {
                Utils_RecordBrowserCommon::update_record(
                    'Sales_plan',
                    $record['id'],
                    array('difficulty_level' => $status),
                    $all_fields = false,
                    null,
                    $dont_notify = false
                );
            }
        }

        if (isset($_REQUEST['copy'])) {
            if (Addons::can_copy($weekNumber, $year)) {
                if ($weekNumber - 1 < 2) {
                    $from = 53;
                    $y = $year - 1;
                } else {
                    $from = $weekNumber - 1;
                    $y = $year;
                }
                if (strlen($from) == 1) {
                    $from = "0" . $from;
                }

                $s = date("$y-m-d", strtotime($y . 'W' . $from));
                $start_date = $s;
                $end_date = $date->add_days($s, 4);
                $records = $rboSalesPlan->get_records(array('>=date' => $start_date, '<=date' => $end_date));
                foreach ($records as $record) {
                    $new_record = array(
                        "company_name" => $record['company_name'],
                        "amount" => $record['amount'],
                        "date" => $date->add_days($record["date"], 7),
                        "description_trader" => $record["description_trader"],
                        "description_manager" => $record["description_manager"],
                        "difficulty_level" => $record["difficulty_level"]
                    );
                    $new = $rboSalesPlan->new_record($new_record);
                    $new->save();
                }
                Addons::copied($weekNumber, $year);
            }
        }

        //MODULE
        //new record
        $x = 0;
        Base_ActionBarCommon::add(
            'add',
            __('New'),
            Utils_RecordBrowserCommon::create_new_record_href('Sales_plan', $this->custom_defaults),
            null,
            $x
        );
        $x++;
        $this->createWeekButtons($weekNumber, $year);

        if (Addons::can_copy($weekNumber, $year) && !isset($_REQUEST['copy']) ) {
            Base_ActionBarCommon::add(
                'add',
                __('Copy from last week'),
                $this->create_href(
                    [
                        'copy' => TRUE,
                        'weekNumber' => $weekNumber,
                        'year' => $year,
                    ]
                ),
                null,
                $x
            );
            $x++;
        }

        $dropdownWeekItems = "<a class='dropdown-item' " . $this->create_href(
            [
                'weekNumber' => $date->get_week_number(date("Y-m-d")),
                'year' => date("Y"),
            ]
        ) . "> Wróć do bieżącego tygodnia </a>";
        for ($i = 1; $i <= 52; $i++) {
            $dropdownWeekItems .= "<a class='dropdown-item' " . $this->create_href(
                [
                    'weekNumber' => $i,
                    'year' => $year,
                ]
            ) . "> Tydzień - " . $i . " </a>";
        }
        $plan = [];
        $weekSummary = [];
        $daysReadable = [
            0 => "Poniedziałek",
            1 => "Wtorek",
            2 => "Środa",
            3 => "Czwartek",
            4 => "Piątek", 
            5 => "Sobota",
            6 => "Niedziela",
        ];
        //weeksummary sumPlanned // sumDelivered //  sumLoaded // sumBought
        //$weekSummary['rowspan'] = '';//rbo count;
        
        $loginContact = CRM_ContactsCommon::get_contact_by_user_id(Base_AclCommon::get_user ());
        $is_manager = $loginContact['access']['manager'];
        for ($i = 0; $i <= 6; $i++) {
            $day = $date->add_days($date->monday_of_week($weekNumber), $i);
            $crits = ['date' => $day];
            $records = $rboSalesPlan->get_records(
                $crits, 
                [], 
                ['company_name' => "ASC"] 
            );
            //$today = date("Y-m-d");
            foreach ($records as $record){
                $newRecord = [];
                $companyName = $record->get_val('company_name',false);
                $newRecord['company'] = $companyName;
                $newRecord['amount'] += $record['amount'];
                $newRecord['price'] = $record->get_val('price', true);
                $newRecord['color'] = $record['asf_zone'];
                $href = 'href="modules/planer/word.php?'.http_build_query(['date'=> $day, 'company' => $record['company_name'], 'cid'=>CID ] ).'"';
                $newRecord['word'] = " <a ".$href ." > ".$wordPNG. "</a>" ;
                $newRecord['day'] = $day;
                $newRecord['dayText'] = $daysReadable[$i];
               
                $transportHref = Base_BoxCommon::create_href(
                    'Custom/Agrohandel/Transporty',
                    'Custom/Agrohandel/Transporty',
                    null, 
                    [],
                    [],
                    [
                    'day'=> $day,
                    ]
                );
                $newRecord['transportHref'] = $transportHref;

                if($record['difficulty_level'] == 1) {
                    $newRecord['statusColor'] = 'bg-warning';
                }
                elseif($record['difficulty_level'] == 2) {
                    $newRecord['statusColor'] = 'bg-success';
                }
                elseif($record['difficulty_level'] == 3) {
                    $newRecord['statusColor'] = 'bg-danger';
                }

                if ($is_manager  || Base_AclCommon::i_am_sa() == "1" || Base_AclCommon::i_am_admin() == "1" ) {
                    if(strlen($record['Description trader']) > 0 || strlen($record['Description Manager']) > 0) {
                        $ar = array("Handlowiec: " => "<div class='custom_info'> ". $record['Description trader'] .
                        "</div>", "Manager: " => "<div class='custom_info'> " . $record['Description Manager'] . " </div> ");
                        $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                        $infobox = Utils_TooltipCommon::create("Informacje dodatkowe", $infobox, $help=true, $max_width=300);
                    }else{
                        $infobox = "---";
                    }
                 } else { 
                    if(strlen($record['Description trader']) > 0){
                        $ar = array("Handlowiec: " => "<div class='custom_info'>".$record['Description trader']. "</div>");
                        $infobox = Utils_TooltipCommon::format_info_tooltip($ar);
                        $infobox = Utils_TooltipCommon::create("Informacje dodatkowe",$infobox,$help=true, $max_width=300);
                    }else{
                        $infobox = "---";
                    }
                }

                $newRecord['notka'] = $infobox;

                $deleteHref = $this->create_href(array("delete_record" => $record['id']));
                $deleteButton = "<a $deleteHref> $deleteImg </a>";
                $newRecord["delete"] = $deleteButton;

                $editButton =  $record->record_link($editImg, $nolink=false,'edit');
                $newRecord["edit"] = $editButton;

                $viewButton =  $record->record_link($viewImg, $nolink=false, 'view');
                $newRecord["view"] = $viewButton;

                $plan[$day]['records'][$record['company_name']][] = $newRecord;
                $plan[$day]['sum']['sumPlanned'] += $record['amount'];

                $weekSummary['records'][$record['company_name']]['company'] = $companyName;
                $weekSummary['records'][$record['company_name']]['amount'] += $record['amount'];
                $weekSummary['sumPlanned'] += $record['amount'];
                $plan[$day]['rowspan'] += 1;
            }

            $easyButton   = "<a ".$this->create_href(
                [
                    'changeStatus' => $day,
                    'status'=> '2'
                ]
            )."> $easyImg </a>";

            $normalButton = "<a ".$this->create_href(
                [
                    'changeStatus' => $day,
                    'status'=> '1'
                ]
            )."> $normalImg </a>";

            $hardButton   = "<a ".$this->create_href(
                [
                    'changeStatus' => $day,
                    'status'=> '3'
                ]
            )."> $hardImg </a>";


            $plan[$day]['easyButton'] = $easyButton;
            $plan[$day]['normalButton'] = $normalButton;
            $plan[$day]['hardButton'] = $hardButton;

        }
        for ($i = 0; $i <= 6; $i++) {
            $day = $date->add_days($date->monday_of_week($weekNumber), $i);
            $transports = $rboTransport->get_records(
                [
                    'date' => $day,  
                    'type' => 'tucznik',
                ],
                [],
                ['company_name' => "ASC"]
            );
            foreach ($transports as $transport) {
               // $plan['records'][$i][$record['id']] = $newRecord;
                $is_ubojnia = $rboComanies->get_record($transport['company']);
                if($is_ubojnia['group']['baza_tr']) {
                    //przeładunki
                    $purchases = $transport["zakupy"];
                    $index = 0;
					foreach($purchases as $purchase) {
                        $purchase  = $rboBought->get_record($purchase);
                        $plan[$day]['reloads'][$transport['company']]['company'] =  $transport->get_val('company', $nolink = TRUE);
                        $plan[$day]['reloads'][$transport['company']]['loaded'] += $purchase['sztukzal'];
                        if($index == 0) { 
                            $plan[$day]['reloads'][$transport['company']]['delivered'] += $transport['iloscrozl'];
                            $index++;
                        }
                        $plan[$day]['sum']['sumLoaded'] += $purchase['sztukzal'];
                        $plan[$day]['sum']['sumBought'] += $purchase['amount'];

                        $weekSummary['sumLoaded'] += $purchase['sztukzal'];
                        $weekSummary['sumBought'] += $purchase['amount'];

                        $plan[$day]['zone'][$purchase['asf_zone']] += $purchase['amount'];
                        // $plan[$day]['records'][$transport['company']]['bought'] += $purchase['amount'];
                        $weekSummary['records'][$transport['company']]['loaded'] += $purchase['sztukzal'];
                        $weekSummary['records'][$transport['company']]['bought'] += $purchase['amount'];
					}		
				} else {
                    $purchases = $transport["zakupy"];
					foreach($purchases as $purchase){     
                        $purchase  = $rboBought->get_record($purchase);
                            
                        $plan[$day]['sum']['sumLoaded'] += $purchase['sztukzal'];
                        $plan[$day]['sum']['sumBought'] += $purchase['amount'];

                        $weekSummary['sumLoaded'] += $purchase['sztukzal'];
                        $weekSummary['sumBought'] += $purchase['amount'];

                        $plan[$day]['records'][$transport['company']][0]['loaded'] += $purchase['sztukzal'];
                        $plan[$day]['records'][$transport['company']][0]['bought'] += $purchase['amount'];  
                        $plan[$day]['zone'][$purchase['asf_zone']] += $purchase['amount'];

                        $weekSummary['records'][$transport['company']]['loaded'] += $purchase['sztukzal'];
                        $weekSummary['records'][$transport['company']]['bought'] += $purchase['amount'];
                    }

                    if(!$plan[$day]['records'][$transport['company']][0]['company']) {
                        $plan[$day]['records'][$transport['company']][0]['missing_comany'] = $transport->get_val('company', $nolink = TRUE);
                        $plan[$day]['rowspan'] += 1;
                    }

                    $plan[$day]['records'][$transport['company']][0]['delivered'] += $transport['iloscrozl'];
                    $weekSummary['records'][$transport['company']]['delivered'] += $transport['iloscrozl'];

                    $weekSummary['sumDelivered'] += $transport['iloscrozl'];
                    $plan[$day]['sum']['sumDelivered'] += $transport['iloscrozl'];
                }
            }
            $weekSummary['week'] = $weekNumber;
        }
        $weekSummary['rowSpan'] = count($weekSummary['records']);

        $rboCurrency = new RBO_RecordsetAccessor("currency_history");
        $thisWeek = $date->add_days($date->monday_of_week($weekNumber), 2);
        $prevWeek = $date->add_days($date->monday_of_week($weekNumber - 1), 2);

        $prevWeekRecords = $rboCurrency->get_records(array('date' => $weekNumber - 1, '!euro' => '', '!zmp' => ''), array(), array());
        foreach($prevWeekRecords as $p){$prevWeekRecords = $p;}

        $thisWeekRecords = $rboCurrency->get_records(array('date' => $weekNumber, '!euro' => '', '!zmp' => ''), array(), array());
        foreach($thisWeekRecords as $t){$thisWeekRecords = $t;}

        if( $prevWeekRecords == null){
            planerCommon::downloadDay($prevWeek);
        }
        else if($prevWeekRecords['euro'] == 0 || $prevWeekRecords['zmp'] == 0 ) {
            planerCommon::downloadDay($prevWeek);
        }

        if( $thisWeekRecords == null){
            planerCommon::downloadDay($thisWeek);
        }
        else if($thisWeekRecords['euro'] == 0 || $thisWeekRecords['zmp'] == 0 ) {
            planerCommon::downloadDay($thisWeek);
        }

        $prevWeekRecords = $rboCurrency->get_records(array('date' => $prevWeek), array(), array());
        $thisWeekRecords = $rboCurrency->get_records(array('date' => $thisWeek), array(), array());
        foreach($prevWeekRecords as $p){$prevWeekRecords = $p;}
        foreach($thisWeekRecords as $t){$thisWeekRecords = $t;}

        $prevWeekRecords['price'] = $prevWeekRecords['euro'] * $prevWeekRecords['zmp'];
        $thisWeekRecords['price'] = $thisWeekRecords['euro'] * $thisWeekRecords['zmp'];

        $prevWeekRecords['price'] = str_replace(".", ",", round($prevWeekRecords['price'],2));
        $prevWeekRecords['euro'] = str_replace(".", ",", $prevWeekRecords['euro']);
        $prevWeekRecords['zmp'] = str_replace(".", ",", $prevWeekRecords['zmp']);

        $thisWeekRecords['price'] = str_replace(".", ",", round($thisWeekRecords['price'],2));
        $thisWeekRecords['euro'] = str_replace(".", ",", $thisWeekRecords['euro']);
        $thisWeekRecords['zmp'] = str_replace(".", ",", $thisWeekRecords['zmp']);

        if($blueZone == 0){
            $bz['href'] = $this->create_href(['blueZone' => '1']);
            $bz['text'] = 'Bez niebieskiej';
            $bz['status'] = 0;
        }else{
            $bz['href']  = $this->create_href(['blueZone' => '0']);
            $bz['text']  = 'Z niebieską';
            $bz['status']  = 1;
        }
        $theme = $this->init_module('Base/Theme');
        $theme->assign("dropdownWeekItems", $dropdownWeekItems);
        $theme->assign("blueZone", $bz);
        $theme->assign("planned", $plan);
        $theme->assign("today", date("Y-m-d"));
        $theme->assign("thisWeekZMP", $thisWeekRecords);
        $theme->assign("prevWeekZMP", $prevWeekRecords);
        $theme->assign("weekSummary", $weekSummary);
        $theme->display();
        epesi::js('
            displayBar();
        ');
        load_js($this->get_module_dir(). "theme/main.js");
        load_css($this->get_module_dir(). "theme/custom.css");
    }

    function createWeekButtons($weekNum, $year)
    {
        $x = 1;
        if ($weekNum - 1 < 2) {
            $w = 53;
            $y = $year - 1;
        } else {
            $w = $weekNum - 1;
            $y = $year;
        }
        //previus button
        Base_ActionBarCommon::add(
            Base_ThemeCommon::get_template_file($this->get_type(), 'prev.png'),
            "Poprzedni tydzień",
            $this->create_href(array('weekNumber' => $w, 'year' => $y)),
            null,
            $x
        );
        $x++;
        $icon = ["1" => 'cal2.png', "2" => 'cal.png'];
        for ($i = $weekNum - 3; $i < $weekNum + 4; $i++) {
            $y = $year;
            $iconType = 2;
            if ($i == $weekNum) {
                $iconType = 1;
            }
            if ($i > 53) {
                $week = $i - 53;
                $y = $year + 1;
            } else if ($i < 1) {
                $week = $i + 53;
                $y = $year - 1;
            }else{
                $week = $i;
                $y = $year;
            }
            Base_ActionBarCommon::add(
                Base_ThemeCommon::get_template_file($this->get_type(), $icon[$iconType]),
                "Tydzień - " . $week,
                $this->create_href(array('weekNumber' => $week, 'year' => $y)),
                null,
                $x
            );
            $x = $x + 1;
        }
        if ($weekNum + 1 > 53) {
            $w = 2;
            $y = $year + 1;
        } else {
            $w = $weekNum + 1;
            $y = $year;
        }

        Base_ActionBarCommon::add(
            Base_ThemeCommon::get_template_file($this->get_type(), 'next.png'),
            "Następny tydzień",
            $this->create_href(array('weekNumber' => $w, 'year' => $y)),
            null,
            $x
        );
        $x++;
    }
}


class PickDate
{
    private $year;

    function __construct($y)
    {
        $this->year = $y;
    }

    public function current_day()
    {
        $date = date("$this->year-m-d");
        return $date;
    }

    function update_year($year)
    {
        $this->year = $year;
    }

    public function this_week_start($date)
    {
        $week = date("$this->year-m-d", strtotime('monday this week', strtotime($date)));
        return $week;
    }

    public function monday_of_week($number_of_week)
    {
        $y = $this->year;
        if (strlen($number_of_week) ==  1) {
            $number_of_week = "0" . $number_of_week;
        }
        $week = date("$y-m-d", strtotime($this->year . 'W' . $number_of_week));

        return $week;
    }

    public function add_days($start_date, $numbers_of_day_to_add)
    {

        $date = strtotime($start_date);
        $days = $numbers_of_day_to_add * (60 * 60 * 24);
        $date = $date + $days;
        $date = date("Y-m-d", $date);
        return $date;
    }

    public function get_week_number($date)
    {
        if (isset($date)) {
            $week = date("W", strtotime($date));
        } else {
            $week = date("W");
        }
        if ($week == "01") {
            $week = "53";
        }
        return $week;
    }

    public function get_day($date)
    {
        return date("$this->year-m-d", strtotime($date));
    }

    public function get_week_name($date)
    {
        return date('l', strtotime($date));
    }
}
class Rbo_Futures
{
    public static function set_related_fields($varible, $name)
    {
        foreach ($varible as $edit) {
            $edit[$name] = ($edit->get_val($name));
        }
        return $varible;
    }
}

class Addons
{
    public static function can_copy($week_selected, $year)
    {
        $y = $year;
        $w = $week_selected;
        // copied = 1 nocopied = 0
        if ($week_selected - 1 < 1) {
            $y -= 1;
            $w = 53;
        } else {
            $w -= 1;
        }

        $settings = fopen("settings.txt", "rw");
        $can = true;
        $date = new PickDate($year);
        $this_week = $week_selected . "-" . $y;
        $last_week = $w;
        $data = fread($settings, filesize('settings.txt'));
        fclose($settings);
        $data =  explode("\n", $data);
        foreach ($data as $day) {
            if ($day == $this_week) {
                $can = false;
            }
        }
        return $can;
    }
    public static function copied($week, $year)
    {

        $date = new PickDate($year);
        $today = date("Y-m-d");
        $week = $week . "-" . $year;
        $settings = fopen("settings.txt", "a");
        fwrite($settings, "\n" . $week);
        fclose($settings);
    }
}
