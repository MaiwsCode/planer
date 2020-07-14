<div class="bootstrap-iso" id='moduleBody' style="font-size: 14px;background-color:#fff;padding:2px;color:#000000;">
    <div class="container-fluid" style="padding-left: 6px; padding-right: 6px;">
        <!-- row -->
        <div class="row">
            <!-- col -->
            <div class='col-6'>
                <h4 class='text-left' style='padding:5px;'>PLANY SPRZEDAŻY TUCZNIKA</h4>
            </div>
            <!-- col -->
        </div>
        <!-- row -->
        <div class='row'>
            <div class='col-1'>
                <!-- dropdown -->
                <div class="dropdown" id='dropdownMenu'>
                    <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Wybierz tydzień
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      {$dropdownWeekItems}
                    </div>
                </div>
                <!-- dropdown -->
            </div>
            <!-- col -->
            <div class='col-4'></div>
            <div class='col-1'>

            </div>
            <div class='col-3'>
                <h6> Cena z tygodnia {math equation='x - y' x=$weekSummary.week y=1 } </h6>
                <table class='table table-bordered table-sm text-center'>
                    <thead style='color:#000000;'>
                        <tr>
                            <th>
                                Cena Euro
                            </th>
                            <th>
                                Cena ZMP
                            </th>
                            <th>
                                Cena tucznika (PLN)
                            </th>
                        </tr>
                    </thead>
                    <tbody style='color:#000000;'>
                        <tr>
                            <td> 
                                {$prevWeekZMP.euro} zł
                            </td>
                            <td>
                                {$prevWeekZMP.zmp} €
                            </td>
                            <td>
                                {$prevWeekZMP.price} zł
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class='col-3'>
                <h6> Cena z tygodnia {$weekSummary.week} </h6>
                <table class='table table-bordered table-sm text-center'>
                    <thead style='color:#000000;'>
                        <tr>
                            <th>
                                Cena Euro
                            </th>
                            <th>
                                Cena ZMP
                            </th>
                            <th>
                                Cena tucznika (PLN)
                            </th>
                        </tr>
                    </thead>
                    <tbody style='color:#000000;'>
                        <tr>
                            <td> 
                                {$thisWeekZMP.euro} zł
                            </td>
                            <td>
                                {$thisWeekZMP.zmp} €
                            </td>
                            <td>
                                {$thisWeekZMP.price} zł
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <!-- col -->
        </div>
        <!-- row -->
        <!-- row -->
        <div classs="row">
            <!-- col -->
            <div class='col-12'>
                <table class='table table-stripped'>
                    <thead style='color:#000000;'>
                        <tr class='tableHeaders text-center bg-warning'>
                            <th>
                                SUMA Z TYGODNIA
                            </th>
                            <th>
                                Zakład
                            </th>
                            <th>
                                Suma zamówionych
                            </th>
                            <th>
                                Suma dostarczonych
                            </th>
                            <th>
                                Suma załadowanych
                            </th>
                            <th>
                                Suma kupionych
                            </th>
                        </tr>
                    </thead>
                    <tbody class='text-center' style='color:#000000;font-size:16px;'>
                        <!-- script -->
                        {assign var="reloaded" value=0 }
                        {foreach from=$weekSummary.records item=week name=records}
                            <tr >
                                {if $smarty.foreach.records.index == 0 }
                                    <td rowspan='{$weekSummary.rowSpan}' style='vertical-align:middle;border-right: 1px solid #eceeef;color:#000000;'>
                                        <h6> Tydzień - {$weekSummary.week} </h6>
                                    </td>
                                {/if}
                                <td style='color:#000000;' > {if $week.company == ''} Przeładunek {assign var="reloaded" value=$reloaded+$week.loaded } {else} {$week.company} {/if} </td>
                                <td style='color:#000000;'>
                                    {$week.amount}
                                </td>
                                <td class='deliveredColor'>
                                    {$week.delivered}
                                </td>
                                <td class='loadedColor'>
                                    {$week.loaded}
                                </td>
                                {if $smarty.foreach.records.index == 0 }
                                    <td rowspan='{$weekSummary.rowSpan}' style='vertical-align:middle;border-left: 1px solid #eceeef;color:#000000;'>
                                    </td>
                                {/if}
                            </tr>
                        {/foreach}
                        <!-- footer -->
                        <tr class='tableHeaders text-center' style='vertical-align:middle;' >
                            <td colspan='2'>
                                Łacznie:
                            </td>
                            <td style='color:#000000;'> <!-- suma zamówionych -->
                                {$weekSummary.sumPlanned}
                            </td>
                            <td style='color:#000000;' class='deliveredColor'><!-- suma dostarczonych -->
                                {$weekSummary.sumDelivered}
                            </td>
                            <td style='color:#000000;' class='loadedColor'><!-- suma załadowanych -->
                                {$weekSummary.sumLoaded}
                                <br>
                                <small>(w tym {$reloaded} przeładowanych) </small>
                            </td>
                             <td style='color:#000000;'><!-- suma kupionych -->
                                {$weekSummary.sumBought}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr />
                <div style="width:100%;height:35px;" class="mt-5 mb-5"> </div>
                <table class='table table-bordered'>
                    <thead class='text-center' style='color:#000000;'>
                        <tr class='bg-warning'>
                            <th>
                                Dzień tygodnia
                            </th>
                            <th style='width:100px;'>
                            </th>
                            <th colspan='2'>
                                Zakład
                            </th>
                            <th>
                                Zamówione
                            </th>
                            <th>
                                Dostarczone
                            </th>
                            <th>
                                Załadowane
                            </th>
                            <th>
                                Kupione
                            </th>
                            <th>
                                Cena
                            </th>
                            <th>
                                Informacje
                            </th>
                        </tr>
                    </thead>
                    <tbody style='color:#000000;font-size:16px;'>
                        {foreach from=$planned item=planedArray key=k name=days}
                           {if $planedArray.rowspan > 0 }
                                {foreach from=$planedArray.records item=planRecords key=nameKey name=planArray}
                                    {foreach from=$planRecords item=plan name=planRecord}
                                        <tr> 
                                            {if $smarty.foreach.planArray.index == 0 && $smarty.foreach.planRecord.index == 0}
                                                <td rowspan='{$planedArray.rowspan}' class='{$plan.statusColor} text-center'  style='vertical-align:middle;' >
                                                    <h5>
                                                        <a {$plan.transportHref} style="text-decoration:none;color:white;" >{$plan.day} </a>
                                                    </h5>
                                                        <hr />
                                                    <h6>
                                                        {$plan.dayText}
                                                    </h6>
                                                    <hr />
                                                    <h6>
                                                        {$planedArray.easyButton} {$planedArray.normalButton} {$planedArray.hardButton} 
                                                    </h6>
                                                </td>
                                            {/if}
                                            <td style='width:100px;'>
                                               {$plan.view} {$plan.edit} {$plan.delete} {$plan.word}
                                            </td>
                                            <td colspan='2'  class='text-left'>
                                                {if $plan.company}
                                                    {$plan.company}
                                                {elseif $plan.missing_comany}
                                                    Brakujący plan: {$plan.missing_comany}
                                                {/if}
                                            </td>
                                            <td class='text-center' >
                                                {if $plan.amount == ''} 0 {else} <div class='zones {$plan.color} list-inline-item'> {$plan.amount} </div> {/if}
                                            </td>
                                            {if $smarty.foreach.planRecord.index == 0}
                                                <td class='text-center deliveredColor'  rowspan='{$planRecords|@count}'  style='vertical-align:middle;' >
                                                     {if $plan.delivered == ''} 0 {else} {$plan.delivered} {/if}
                                                </td>
                                                <td class='text-center loadedColor' rowspan='{$planRecords|@count}'  style='vertical-align:middle;' >
                                                    {if $plan.loaded == ''} 0 {else} {$plan.loaded} {/if}
                                                </td>
                                            {/if}
                                            {if $smarty.foreach.planArray.index == 0 && $smarty.foreach.planRecord.index == 0 }
                                                <td rowspan='{$planedArray.rowspan}' class='text-center'  style='vertical-align:middle;'>
                                                {if $planedArray.zone.white > 0}
                                                    <span class='zones white'>
                                                        {$planedArray.zone.white }
                                                    </span> <br /> <br />
                                                {/if}
                                                {if $planedArray.zone.yellow > 0}
                                                    <span class='zones yellow'>
                                                        {$planedArray.zone.yellow }
                                                    </span> <br /> <br />
                                                {/if}
                                                {if $planedArray.zone.red > 0}
                                                    <span class='zones red'>
                                                        {$planedArray.zone.red }
                                                    </span> <br /> <br />
                                                {/if}
                                                {if $planedArray.zone.dodgerBlue > 0}
                                                    <span class='zones dodgerBlue'>
                                                        {$planedArray.zone.dodgerBlue }
                                                    </span> <br /> <br />
                                                {/if}
                                                {if $planedArray.zone.darkblue >0}
                                                    <span class='zones darkblue'>
                                                        {$planedArray.zone.darkblue }
                                                    </span> <br /> <br />
                                                {/if}
                                                </td>
                                            {/if}
                                            {if $plan.day == $today}
                                                <td  class='text-center font-weight-bold'>
                                            {else}
                                                 <td  class='text-center'>
                                            {/if}
                                                {if $plan.price == ''} 0 {else} {$plan.price} {/if}

                                            </td>
                                            <td  class='text-left'>
                                                {$plan.notka}
                                            </td>
                                        </tr>
                                    {/foreach}
                                {/foreach}
                                <tr></tr>
                                {foreach from=$planedArray.reloads item=reload name=reloadsArray}
                                    <tr>
                                        <td colspan='2' class='text-right text-weight-bold'> Przeładunek:  </td>
                                        <td colspan='2' class='text-right text-weight-bold'>
                                            {$reload.company}
                                        </td>
                                        <td></td>
                                        <td class='text-center deliveredColor'>
                                            {$reload.delivered}
                                        </td>
                                        <td class='text-center loadedColor'>
                                            {$reload.loaded}
                                        </td>
                                        <td colspan='3'>
                                        </td>
                                    </tr>
                                {/foreach}
                                    <tr>
                                        <td colspan='4' class='text-right tableHeaders'>
                                            Łącznie:
                                        </td>
                                        <td class='text-center tableHeaders' style='color:#000000;'>
                                            {if $planedArray.sum.sumPlanned == ''} 0 {else} {$planedArray.sum.sumPlanned } {/if}
                                        </td>
                                        <td class='text-center deliveredColor tableHeaders' style='color:#000000;'>
                                            {if $planedArray.sum.sumDelivered == ''} 0 {else} {$planedArray.sum.sumDelivered } {/if}
                                        </td>
                                        <td class='text-center loadedColor tableHeaders' style='color:#000000;'>
                                            {if $planedArray.sum.sumLoaded == ''} 0 {else} {$planedArray.sum.sumLoaded } {/if}
                                        </td>
                                        <td class='text-center tableHeaders' style='color:#000000;'>
                                            {if $planedArray.sum.sumBought == ''} 0 {else} {$planedArray.sum.sumBought } {/if}
                                        </td>
                                                                                            
                                        <td colspan='2'></td>
                                    </tr>
                                    <tr>
                                        <td colspan='10'></td>
                                    </tr>
                                {/if}
                          {/foreach}
                    </tbody>
                </table>
            </div>
            <!-- col -->
        </div>
        <!-- row -->

    </div>
</div>