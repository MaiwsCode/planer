<head>
    <link rel="stylesheet" type='text/css' href='{$css}/planer/default.css' />
    
</head>

<div class="layer" style="padding: 9px; width: 98%;">
<div style="text-align: left; height:30px">
<div class="css3_content_shadow">
<div style="padding: 5px; background-color: #FFFFFF;">
<h1 style="text-align: left;margin-left: 5px;">PLANY SPRZEDAŻY TUCZNIKA </h1>
    {foreach from=$action_buttons item=button}
        <a class="button" {$button.href}>{$button.label}</a>
    {/foreach}
    <br><br>
    {$select}
    {$test}
<br>
 <table class="Agrohandel__sale__week" style="margin-top:15px;margin-bottom:15px;"> 
    <thead>
        <td class="header_future"> SUMA Z TYGODNIA </td> 
        <td class="header_future">Zakład</td> 
        <td class="header_future">Suma zamówionych</td>
        <td class="header_future">Suma dostarczonych</td>
		<td class="header_future">Suma załadowanych</td>
        <td class="header_future">Suma kupionych</td>
    </thead>
   {foreach from=$sumary_week item=company}
    <tr class="changing" style="font-size:15px;">
        {if ($company === reset($sumary_week))}      
            <td class="inter_future" rowspan="{$sumary_week|@count}" style="color:black;background-color:#FFFFFF;">
            <span style='font-size:18px;'><b>Tydzień - {$week_number} </b></span>
            </td>
        {/if}
        {assign var=last value=$company.name}
        <td class="inter_future" >{$company.name}</td>
        <td class="inter_future" >{$company.val}</td>  
        <td class="inter_future transported">
        {if $week_transported.$last == ""}
            0
        </td>
        {else}
            {$week_transported.$last}
        </td>
        {/if}
		
		<td class="inter_future " style='color:#ffa31a;'>
        {if $week_loads.$last == ""}
            0
        </td>
        {else}
            {$week_loads.$last}
        </td>
        {/if}
        {if ($company === reset($sumary_week))}      
        <td class="inter_future bought"  rowspan="{$sumary_week|@count}">{$all_bought}  </td>
        {/if}
    </tr>
    {/foreach}
    {foreach from=$missing_all item=company}
    <tr class="changing">  
      {if ($company === reset($missing_all))}   
        <td class="inter_future" rowspan="{$missing_all|@count}" style="color:red;background-color:#FFFFFF;"> Brakujące plany</td>
      {/if}
        <td class="inter_future" >{$company.company}</td>
        <td class="inter_future" >---</td>
        <td class="inter_future" >{$company.amm}</td>
        <td class="inter_future " >{$company.iloscrozl}</td>
    </tr>
    {/foreach}
<tr class="changing">
    <td class="inter_future" colspan="2"><span style='font-size:18px;'><b>Razem:</b></span></td>
    <td class="inter_future"><span style='font-size:18px;'><b>{$all_zam}</b></span></td>
    <td class="inter_future transported"><span style='font-size:18px;'><a style='color:#0a07bd;' {$week_link} ><b>{$all_transp}</b></a></span></td>
	<td class="inter_future"> <span style='font-size:18px;color:#ffa31a;'> <b> {$all_loaded_week} </b></span> <br> <span style='color:#ffa31a;'>(w tym {$reload_sum} przeładowanych) </span></td>
    <td class="inter_future bought"><span style='font-size:18px;'><b>{$all_bought}</b></span></td>
</tr>

    </table>
    <br><br><br>
    <table  class="Agrohandel__sale__week" cellspacing=0 style="margin-top:15px;margin-bottom:15px;">
     <thead>
        <tr>
            <td class="header_company"><span style='font-size:15px;'>Dzień tygodnia</span> <br></td>
            <td class="header_future">Zakład</td>
            <td class="header_future">Zamówione</td>
            <td class="header_future">Dostarczone</td>
			<td class="header_future">Załadowane</td>
            <td class="header_future">Kupione</td>
            <td class="header_future">Cena</td>
            <td class="header_future">Informacje</td>
        </tr>
     </thead>
     <tr class='separator'></tr>
        {assign var=val value=1}
        {assign var=arr value=$trans[1]}
		{assign var=loadA value=$load[1]}
        {assign var=transported value=$week_trans}
        {assign var=row value=1}
        {assign var=iter value=1}
        {assign var=extra value=""}
        {assign var=last value="testLocal"}
                {foreach from=$days item=day}
                    {foreach from=$day item=record name=y}
                        {if $smarty.now|date_format:"%Y-%m-%d" == $record.date} 
                            {if ($record === reset($day)) && $day|@count == 1}  
                                {assign var=useClass value="lineUp lineDown"}
                                {assign var=extra value="border-bottom: 2px solid #336699;"}
                            {elseif ($record === reset($day))}
                                {assign var=useClass value="lineUp"}
                                {assign var=extra value="border-bottom: 2px solid #336699;"}
                            {elseif ($record === end($day))} 
                                {assign var=useClass value="lineDown"}
                            {else}
                                {assign var=useClass value=""}
                                {assign var=extra value=""}
                            {/if}
                        {else}
                            {assign var=useClass value=""}
                            {assign var=extra value=""}
                        {/if}
                        {if $record.difficulty_level == 2}
                            <tr class="prop100 {$useClass}">
                        {elseif  $record.difficulty_level == 3}
                            <tr class="prop30 {$useClass}">
                        {else}
                            <tr class="prop50 {$useClass}" >
                        {/if}
                        {if ($record === reset($day))}      
                            {assign var="selector" value="`$val``$val`"}
                            <td class="inter_company" rowspan="{$day|@count}" style='{$extra} text-align:center;font-size:15px;'>
                               <span>{$record.date}</span><br>
                                <span>{$days_text[$val]}</span><br>
                                {$days_text[$selector]} </td>

                        {/if}
                        {if ($record === end($day)) && $extra ==''}  
                            <td class="inter_future" style='font-size:15px;border-bottom: 1px solid #B3B3B3;'> {$record.company_name}</td>
                        {else}
                            <td class="inter_future" style='font-size:15px;'> {$record.company_name}</td> 
                        {/if}
                        {if ($record === end($day)) && $extra ==''}  
                            <td class="inter_future" style="border-bottom: 1px solid #B3B3B3;"><span style='font-size:18px;'> {$record.amount} </span> &nbsp;
                                {$record.edit}
                                {$record.delete}
                            </td>
                        {else}
                            <td class="inter_future"><span style='font-size:18px;'> {$record.amount} </span> &nbsp;
                            {$record.edit}
                            {$record.delete}
                            </td>
                        {/if}
                        {if $iter == $row}
                            {assign var=last value=$record.company_name|strip_tags:false|substr:0}
                            {if $arr.$last}
                                {if ($record === end($day)) && $extra ==''} 
                                    <td class="inter_future transported" style='border-bottom: 1px solid #B3B3B3;font-size:15px;'  rowspan="{$indexer[$iter]}">{$arr.$last}</td>
									<td class="inter_future transported" style='border-bottom: 1px solid #B3B3B3;font-size:15px;color:#ffa31a;'  rowspan="{$indexer[$iter]}">{$loadA.$last}</td>
                                {else}
                                    <td class="inter_future transported" style='{$extra} font-size:15px;'  rowspan="{$indexer[$iter]}">{$arr.$last}</td>
									<td class="inter_future " style='border-bottom: 1px solid #B3B3B3;font-size:15px;color:#ffa31a;'  rowspan="{$indexer[$iter]}">{$loadA.$last}</td>
                                {/if}
                            {else}
                                {if ($record === end($day)) && $extra ==''} 
                                    <td class="inter_future transported" style='border-bottom: 1px solid #B3B3B3; font-size:15px;'  rowspan="{$indexer[$iter]}">0</td>
									<td class="inter_future " style='border-bottom: 1px solid #B3B3B3;font-size:15px;color:#ffa31a;'  rowspan="{$indexer[$iter]}">0</td>
                                {else}
                                    <td class="inter_future transported" style="font-size:15px;"   rowspan="{$indexer[$iter]}"> 0 </td>
									<td class="inter_future" style="font-size:15px;color:#ffa31a;"   rowspan="{$indexer[$iter]}"> 0 </td>
                                {/if}
                            {/if}
                            {assign var=row value=$row+$indexer[$iter]}
                            {assign var=iter value=$iter+1}
                        {else}
                                {assign var=row value=$row-1}
                        {/if}
                        {if ($record === reset($day))}      
                            <td class="inter_future bought" style="{$extra} font-size:15px;" rowspan="{$day|@count}"> {$amount_sum[$val]} </td>
                        {/if}
                          {if ($record === end($day)) && $extra ==''}  
                            <td class="inter_future" style="border-bottom: 1px solid #B3B3B3;font-size:15px;">{$record.Price|floatval|replace:'.':','}</td>
                        {else}
                            <td class="inter_future" style='font-size:15px;'>{$record.Price|floatval|replace:'.':','}</td>
                        {/if}
                            {if ($record === end($day)) && $extra ==''} 
                                <td class="inter_future" style="border-bottom: 1px solid #B3B3B3;">{$record.notka}</td>
                            {else}
                                <td class="inter_future" >{$record.notka}</td>
                            {/if}
                        </tr>
                        {if ($record === end($day))} 
                        <tr class='separator' style='font-size:14px;'></tr>
                            {foreach from=$missing[$val] item=rec}
                            <tr class="changing">
                                 {if ($rec === reset($missing[$val]))}   
                                    <td class="inter_future" rowspan="{$missing[$val]|@count}" style="color:red;background-color:#F0F0F0;"> Brakujące plany</td>
                                 {/if}
                                 <td class="inter_future">{$rec.company}</td>
                                 <td class="inter_future">---</td>
                                 <td class="inter_future">---</td>
                                 <td class="inter_future">{$rec.amm}</td>
                                 <td class="inter_future">{$rec.iloscrozl}</td>
                                 <td class="inter_future" >---</td>
                                 </tr>
                            {/foreach}
                        {/if}
                    {/foreach}
					{if $reloads[$val] != null } 
						{foreach from=$reloads[$val] item=reload}
								<tr>
								<td class="inter_future">Przeładunek</td>
									<td class="inter_future" colspan='3'> {$reload.name}</td>
									<td class="inter_future" style='color:#ffa31a;'>{$reload.count}</td>
									<td class="inter_future" colspan='3'></td>
								</tr>
						{/foreach}
					{/if}	
                    {if $days_zam[$val] != null }
					  <tr class='separator' style='font-size:14px;'></tr>
                      <tr>
                            <td colspan='2' class="inter_future"> <b>Razem: </b></td>
                            <td class="inter_future"> <b>{$days_zam[$val]}</b> </td>
                            <td class="inter_future transported">  <a style='color:#0a07bd;' {$days_link[$val]} ><b>{$transports_sum_of_day[$val]} </b> </a></td>
							<td class="inter_future" style='color:#ffa31a;'> <b>{$loadings_sum_of_day[$val]}</b> </td>
                            <td class="inter_future" colspan='3'>  </td>
                        </tr>
                        <tr class='separator'></tr>
                        <tr class='separator'></tr>
                        {/if}
                   {assign var=val value=$val+1}
                   {assign var=arr value=$trans[$val]}
				   {assign var=loadA value=$load[$val]}
                {/foreach}      
                </table>
            <br><br><br>
            </div>
        </div>
    </div>
</div>