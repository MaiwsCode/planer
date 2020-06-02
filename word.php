<?php
/**
 * Created by PhpStorm.
 * User: Mati
 * Date: 29.05.2019
 * Time: 17:12
 */


    $cid = $_REQUEST['cid'];
    define('CID', $cid);
    define('READ_ONLY_SESSION',true);

    require_once('../../include.php');

    ModuleManager::load_modules();
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    //custom_agrohandel_purchase_plans custom_agrohandel_transporty  custom_agrohandel_vehicle
    $rboTransports = new RBO_RecordsetAccessor("custom_agrohandel_transporty");
    $rboVehicles = new RBO_RecordsetAccessor("custom_agrohandel_vehicle");
    $rboPurchasePlans = new RBO_RecordsetAccessor("custom_agrohandel_purchase_plans");
    $rboCompany = new RBO_RecordsetAccessor("company");
    $rboContact = new RBO_RecordsetAccessor("contact");

    $section = $phpWord->addSection();
    $company = $_REQUEST['company'];
    $companyRecord = $rboCompany->get_record($company);
    $contactRecord = $rboContact->get_records(array("postal_code" => $companyRecord['postal_code'], 'group' => ['vet'] ), array(), array()); 
    foreach ($contactRecord as $cr) { $contactRecord = $cr; }

    $styleT = array('color' => '000000', 'name' => 'Calibri', 'size' => '12', 'bold' => true);
    $styleP = array("alignment" => 'right');
    $section->addText("Szewce \${data}", $styleT, $styleP);
    $section->addTextBreak(1);
    $section->addText("\${firma}", array('color' => '000000', 'name' => 'Calibri', 'size' => '14', 'bold' => true), $styleP);
    $section->addText("\${adres}", $styleT, $styleP);
    $section->addText("\${email}", $styleT, $styleP);
    $section->addTextBreak(1);
    $styleT = array('color' => '000000', 'name' => 'Calibri', 'size' => '12');
    $phpWord->addParagraphStyle('pJustify', array('align' => 'both', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
    $text = $section->addTextRun( array('alignment' => 'center'));
    $text->addText("Awizo dostawy tucznika na dzień ", array('color' => '000000', 'name' => 'Calibri', 'size' => '14'));
    $text->addText("\${day}" , array('color' => '000000', 'name' => 'Calibri', 'size' => '14' , 'bold'=>true));
    $section->addTextBreak(1);
    $section->addText("Szanowni Państwo, ", array('color' => '000000', 'name' => 'Calibri', 'size' => '11'));
    $section->addText("Uprzejmie informujemy, iż na dzień \${day}  dostawy tucznika planujemy w następujących ilościach:",
        array('color' => '000000', 'name' => 'Calibri', 'size' => '11') );
    $section->addTextBreak(1);
    $pCell = array("spacing" => '25');
    $table = $section->addTable(array('borderSize' => 1, 'borderColor' => '000000', 'alignment' => 'center',  'topFromText '=> '25'));
    $table->addRow();
    $table->addCell()->addText("Lp.   ", array("bold" => true), $pStyle=array('alignment'=> 'center'));
    $txt = $table->addCell()->addTextRun($pStyle=array('alignment'=> 'center'));
    $txt->addText("Numer rejestracyjny środka transportu ", array("bold" => true));
    $txt->addText("[Samochód]", array("bold" => false , 'size' => '8'));
    $txt = $table->addCell()->addTextRun($pStyle=array('alignment'=> 'center'));
    $txt->addText("Numer rejestracyjny środka transportu ", array("bold" => true), $pCell);
    $txt->addText(" [Naczepa / przyczepa]", array("bold" => false , 'size' => '8'), $pCell);
    $table->addCell()->addText("Ilość sztuk ", array("bold" => true), $pStyle=array('alignment'=> 'center'));

    $date =  $_REQUEST['date'];
    $transports = $rboTransports->get_records(array("company" => $company, 'date' => $date , 'type' => 'tucznik'), array(), array('load_time'=>'ASC'));
    $index = 1;
    foreach ($transports as $transport){
        $vehicle = $rboVehicles->get_record($transport->vehicle);
        $loadTime = $transport->get_val('load_time', true);
        $amountSum = 0;
        $numbers = "";
        $zakupy = $transport['zakupy'];
        foreach ($zakupy as $zakup){
            $purchase = $rboPurchasePlans->get_record($zakup);
            $amountSum += $purchase['amount'];
            $rolnik = $rboCompany->get_record($purchase['company']);
        }

        $subs = $transport['transporty'];
        foreach ($subs as $sub) {
            $_sub = $rboTransports->get_record($sub);
            $zakupy = $_sub['zakupy'];
            foreach ($zakupy as $zakup) {
                $purchase = $rboPurchasePlans->get_record($zakup);
                $amountSum += $purchase['amount'];
                $rolnik = $rboCompany->get_record($purchase['company']);
                $postal = $rolnik->get_val("postal_code", false);
                $string ="";
                if($postal[0] == "0" || $postal[0] == "1" || $postal[0] == "2" || $postal[0] == "3"  ){
                    $string = $purchase['numer_ubojowy']."(".$purchase['amount']." szara), ";
                }
                else{
                    $string = $purchase['numer_ubojowy']."(".$purchase['amount']."), ";
                }
                $numbers .= $string;
            }
        }

        $table->addRow();
        $table->addCell()->addText($index,array(), $pStyle=array('alignment'=> 'center'));
        $table->addCell()->addText($vehicle['vehicle_rn'],array(), $pStyle=array('alignment'=> 'center'));
        $table->addCell()->addText($vehicle['trailer_rn'],array(), $pStyle=array('alignment'=> 'center'));
        $table->addCell()->addText($amountSum,array(), $pStyle=array('alignment'=> 'center'));
        $index++;
    }

    $section->addTextBreak(2);
    $text = $section->addTextRun();
    $footer = "Kontakt do ";
    $text->addText($footer, $styleT);
    $text->addText("SPEDYTORA  DYŻURNEGO  +48 663 920 567", array('color' => '000000', 'name' => 'Calibri', 'size' => '12', 'bold' => true));

    $phpWord->save("data/template.docx" );

    $template = new \PhpOffice\PhpWord\TemplateProcessor("data/template.docx");
    $now = date("Y-m-d");
    $template->setValue("data", $now);

    $address = $contactRecord['address_1'].", ".$contactRecord['postal_code']." ".$contactRecord['city'];

    $template->setValue("firma", $contactRecord["title"]);
    $template->setValue("adres", $address);
    $template->setValue("email", $contactRecord["email"]);
    $template->setValue("day", $date);

    $template->saveAs("data/template.docx");

    $docName = $date.'_'.$company.'.docx';
    $phpReader = new \PhpOffice\PhpWord\Reader\Word2007();
    $word = $phpReader->load("data/template.docx");
    $section = $word->getSection(0);
    $header = $section->addHeader();
    $header->addWatermark( "modules/planer/theme/bg.jpg", array('marginTop' => '0', 'marginLeft' => '0'));
    header('Content-Disposition: attachment; filename="' .$docName. '"');
    header('Expires: 0');
    $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
    $xmlWriter->save("php://output");
    exit();
