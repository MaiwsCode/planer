<head>
    <link rel="stylesheet" type='text/css' href='{$css}/planer/default.css' />
    
</head>
<div>
    <div>
{foreach from=$action_buttons item=button}
&nbsp;&nbsp;<a class="button" {$button.href}>{$button.label}</a>
{/foreach}
    </div>


<h1 style="text-align: left;margin-left: 5px;">PLANY SPRZEDAŻY TUCZNIKA </h1>
    <table  class="planer" border="1" rules="all">
        <tr>
            <th>Dzień tygodnia
            <h3>Tydzień - {$week_number} </h3</th><th>Zakład</th><th>Zamówione</th><th>Cena</th><th>Kupione</th><th>Dostarczone</th><th>Akcje</th>
        </tr>
        {assign var=val value=1}
        {assign var=arr value=$trans[1]}
        {assign var=row value=1}
        {assign var=iter value=1}
        {assign var=last value="testLocal"}

                {foreach from=$days item=day}
                    {foreach from=$day item=record name=y}
                        <tr>
                            {if ($record === reset($day))}      
                                <td rowspan="{$day|@count}">{$record.date}   <br> {$days_text[$val]}</td>
                            {/if}
                            {if $record.difficulty_level == 2}
                                <td class='elements easy'>{$record.company_name}</td>
                            {elseif  $record.difficulty_level == 3}
                                <td class='elements hard'>{$record.company_name}</td>
                            {else}
                                <td class='elements'>{$record.company_name}</td>
                            {/if}
                                <td class='elements'>{$record.amount}</td>
                                <td class='elements'>{$record.Price}</td>
                            {if ($record === reset($day))}      
                                <td rowspan="{$day|@count}"> {$amount_sum[$val]} </td>
                            {/if}
                            {if $iter == $row}
                                {assign var=last value=$record.company_name|strip_tags:false|substr:0}
                                {if $arr.$last}
                                    <td rowspan="{$indexer[$iter]}">{$arr.$last}</td>
                                {else}
                                    <td rowspan="{$indexer[$iter]}">0</td>
                                {/if}
                                 {assign var=row value=$row+$indexer[$iter]}
                                 {assign var=iter value=$iter+1}
                            {else}
                                {assign var=row value=$row-1}
                            {/if}

                                <td class='elements'>{$record.link}</td>
                            
                        </tr>
                    {/foreach}
                   {assign var=val value=$val+1}
                   {assign var=arr value=$trans[$val]}
                {/foreach}      
    </table>
    <br>


    <table class="planer" border="1" rules="all">
    <tr>
        <th>  </th> <th>Zakład</th> <th>Suma zamówionych</th><th>Suma kupionych</th><th>Suma rozładowanych</th>
    </tr>
   {foreach from=$sumary_week item=company}
    <tr>
        {if ($company === reset($sumary_week))}      
            <td style="text-align: center;padding:10px;" rowspan="{$sumary_week|@count}">Suma <br> Tydzień - {$week_number} </td>
        {/if}
        <td style="text-align: center;padding:10px;">{$company.name}</td>
        <td style="text-align: center;padding:10px;">{$company.val}</td>  
        {if ($company === reset($sumary_week))}      
        <td style="text-align: center;padding:10px;" rowspan="{$sumary_week|@count}">{$week_bought}  </td>
        <td style="text-align: center;padding:10px;" rowspan="{$sumary_week|@count}">{$week_transported}</td>
        {/if}
            

    </tr>
    {/foreach}
    </table>
    <br><br><br>
</div>