<?php
if( !empty($query['results']) &&
 	is_array($query['results']) )
{

	foreach( $query['results'] as $value ){
		if( ! empty($value['email_paypal']) )
			$eachData[] = $value['email_paypal'];
	}

	if( !empty($eachData) ){
		?>

		<table cellspacing="0" cellpadding="0" border="0" class="listagem">
		<tr class="titulo">
			<td class="">
				Dados separados por ponto e vírgula
			</td>
		</tr>
	    <tr class="conteudo">
	        <td colspan="1">
				<?php echo implode("; ", $eachData); ?>
	        </td>
	    </tr>
		</table>
		
		<?php
	}
}
?>