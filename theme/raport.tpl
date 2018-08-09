
<div style='width:80%;margin-right:10%;margin-left:10%;position:relative;'>

<h1 style='text-align:left;margin:5px;'>  Raport z EPESI za {$name_of_month} </h1>
<table>
    <thead>
        <td  colspan='2'> Kierowca </td>
        <td  colspan='3'> Suma </td>
    </thead>
    <tr>
        <td colspan='2'>    </td>
        <td >   szt </td>
        <td >Plan km    </td>
        <td >  km  </td>
    </tr>
    {foreach from=$raports item=raport}
    <tr>
        <td colspan='2'> {$raport.name}  </td>
        <td> {$raport.szt}   </td>
        <td>  {$raport.kmplan}  </td>
        <td>  {$raport.kmprzej}  </td>
    </tr>
{/foreach}

</table>