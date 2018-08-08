<head>
    <link rel="stylesheet" type='text/css' href='{$css}/planer/default.css' />
</head>

<div style='width:80%;margin-right:10%;margin-left:10%;position:relative;'>

<h1 style='text-align:left;margin:5px;'>  {$day} </h1>


 <table class="Agrohandel__sale__week" style="margin-top:15px;margin-bottom:15px;"> 
    <thead>
        <td class='header_future'> Number </td>
         <td class='header_future'> Zakład </td>
        <td class='header_future'> Ilość sztuk zaplanowanych </td>
        <td class='header_future'> Ilość sztuk rozładowanych  </td>
        <td class='header_future'> Róznica  </td>
        <td class='header_future'> Sztuki padłe  </td>
        <td class='header_future'> Kilometry planowane </td>
        <td class='header_future'> Kilometry przejechane </td>
        <td class='header_future'> Róznica </td>
    </thead>
      {foreach from=$transports item=transport}
    <tr class='info'>
        <td class='inter_future'>
        {$transport.link}
        
         </td>
         <td class='inter_future'>{$transport.company}</td>
        <td class='inter_future'> {$transport.bought} </td>
        <td class='inter_future'> {$transport.iloscrozl}  </td>
        {assign var="roznica" value=$transport.bought|strip_tags:false-$transport.iloscrozl}
        {if $roznica == 0}
            <td class='inter_future'>  {$roznica} </td>
        {elseif $roznica > 0}
            <td class='inter_future'>  <span style='color:red'>-{$roznica}</span> </td>
        {else}
            <td class='inter_future'>  <span style='color:green'>+{$roznica|abs}</span> </td>
        {/if}
        <td class='inter_future'> {$transport.iloscpadle}  </td>
        <td class='inter_future'> {$transport.kmplan} </td>
        <td class='inter_future'> {$transport.kmprzej} </td>
        {assign var="km" value=$transport.kmplan-$transport.kmprzej}
        {if $km == 0}
            <td class='inter_future' >  {$km} </td>
        {elseif $km > 0 }
            <td class='inter_future' > <span style='color:green;'>-{$km}</span> </td>
        {else}
            <td class='inter_future' >  <span style='color:red'>+{$km|abs}</span> </td>  
        {/if}

    </tr>
    <tr class='separator'></tr>
  {/foreach}
    <tr class='info' style='font-weight:bold;'>
        <td class='inter_future' colspan='2'>Suma: </td>
        <td class='inter_future'> {$sumy[1]} </td>
        <td class='inter_future'> {$sumy[2]} </td>
        <td class='inter_future'>  </td>
        <td class='inter_future'> {$sumy[3]} </td>
        <td class='inter_future'> {$sumy[4]} </td>
        <td class='inter_future'> {$sumy[5]} </td>
        <td class='inter_future'>  </td>
    </tr>
    </table>


</div>
