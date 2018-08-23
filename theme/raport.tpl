
<div style='width:90%;margin-right:5%;margin-left:5%;position:relative;'>

<h1 style='text-align:left;margin:5px;'>  {$name_of_month} </h1><br>

<div style='width:80%;margin-left:10%;margin-right:10%;position:relative;font-size:18px;'>
    <div style='width:100%;height:50px;font-weight:bold;'>
        <div style='width:14%;float:left;'>Poniedziałek</div>
        <div style='width:14%;float:left;'>Wtorek</div>
        <div style='width:14%;float:left;'>Środa</div>
        <div style='width:14%;float:left;'>Czwartek</div>
        <div style='width:14%;float:left;'>Piątek</div>
        <div style='width:14%;float:left;'>Sobota</div>
        <div style='width:14%;float:left;'>Niedziela</div>
        <div style='clear:both;'></div>
    </div>
    <div class='calendar' style='width:100%;height:50px;position:relative;'>
    {foreach from=$days item=day}
        {if $day.num != ' '}
            <div class='day' style='width:14%;float:left;min-height:50px;margin-top:5px;margin-bottom:5px;' >
        {else}
            <div style='width:14%;float:left;min-height:50px;margin-top:5px;margin-bottom:5px;'>
        {/if}
       <p class='slideDown'> {$day.num} </p>
        {if 'ilosc'|array_key_exists:$day } 
        <p style='font-size:10px;'> 
        Sztuk: {$day.ilosc}<br>
        Km przej: {$day.km}
        </p>
        {/if}
        <div class='day_drivers hidden'>
        <button style='position:absolute;right:0;top:0;z-index:120;margin-right:3px;margin-top:3px;' class='cls' onclick='hidd(this)'> X </button>
            <ul>
               {foreach from=$drivers[$day.num] item=driver}
                    {foreach from=$driver item=d}
                        <li>{$d.name} - dostarczono: {$d.ilosc}  |  przejechano: {$d.km} km</li>
                    {/foreach}
               {/foreach}
            </ul>
        </div>
        </div>
    {/foreach}

    </div>  


</div>
<div style='margin-top:50px;margin-bottom:50px;clear:both;'>&nbsp;</div>
<br>
<h3>Zestawienie z całego miesiąca</h3>
<table class="Agrohandel__sale__week" cellspacing=0 style="margin-top:15px;margin-bottom:15px;user-select: text;">
    <thead>
        <td class='inter_future'  colspan='2'> Kierowca </td>
        <td class='inter_future' colspan='3'> Suma </td>
    </thead>
    <tr>
        <td class='inter_future' colspan='2'>    </td>
        <td class='inter_future' >   Szt </td>
        <td class='inter_future' >Planowane km    </td>
        <td class='inter_future' > Przejechane km  </td>
    </tr>
    {foreach from=$raports item=raport}
    <tr>
        <td class='inter_future' colspan='2'> {$raport.name}  </td>
        <td class='inter_future'> {$raport.szt}   </td>
        <td class='inter_future'>  {$raport.kmplan}  </td>
        <td class='inter_future'>  {$raport.kmprzej}  </td>
    </tr>
{/foreach}
    <tr>
        <td  class='inter_future' colspan='2'> Łącznie </td>
        <td class='inter_future'> <b>{$raport_sumy[1]}</b>   </td>
        <td class='inter_future'> <b> {$raport_sumy[2]} </b> </td>
        <td class='inter_future'> <b> {$raport_sumy[3]}</b>  </td>
    </tr>
</table>
</div>