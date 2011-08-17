<?php header("Content-Type: text/html; charset=ISO-8859-1",true) ?>
<div class="calendario">
<?php
/****************************************************
*	Script desenvolvido por Alexandre de Oliveira.
*	Pedro Osório, RS - Brasil
*	chavedomundo@gmail.com
*	28/07/2008 - Atualização do sistema
*	Qualquer dúvida referente a este script,		
*	mail-me!										
****************************************************/

	$modulo = 0;
	if($modulo == 1){
		$dbname = "razaoaurea"; 
		$usuario = "root"; 
		$password = "2104"; 
		$con = mysql_connect("localhost", $usuario);
	//	$con = mysql_connect("localhost", $usuario, $password);
		
	} else {
		$dbname = "razaoaurea"; 
		$usuario = "razaoaurea"; 
		$password = "mysql2104"; 
	
		$con = mysql_connect("mysql01.razaoaurea.com.br", $usuario, $password);
	}
	mysql_select_db($dbname, $con);

	error_reporting(134);
	/* Torna a variável igual $month e $year para número do mês e ano, respectivamente */
	
	if(empty($incremento))
		$incremento = 0;
	
	if(empty($month)){
		echo $month;
		$month = sprintf("%02d", date("n")+$incremento);
		$month_int = date("n")+$incremento;
		if($month > 12){
			while($month > 12){
				$month = sprintf("%02d", $month - 12);
				$month_int = $month_int - 12;
				$incrementoano++;
			}
		} else if ($month < 1){
			while($month < 1){
				$month = sprintf("%02d", $month + 12);
				$month_int = $month_int + 12;
				$incrementoano = $incrementoano - 1;
			}
		
		}
	}
	if(empty($year)){
		$year = date("Y")+$incrementoano;
	}
	
	// Pega informações no banco de dados sobre eventos
	$calendar_table = (empty($calendar_table)) ? "content" : $calendar_table;
	$sql = "
			SELECT
				id,dia,mes,ano,titulo,categorianome
			FROM
				$calendar_table
			WHERE
				classe='Agenda de Eventos' AND
				categorianome='".$categoria."' AND
				mes='".$month."' AND
				ano='".$year."'";

	$mysql = mysql_query($sql);
	while($dados = mysql_fetch_array($mysql)){
		$agenda[$dados[dia]]["id"] = $dados[id];
		$agenda[$dados[dia]]["titulo"] = $dados[titulo];
		if(empty($agenda[$dados[dia]]["categorianome"])) $agenda[$dados[dia]]["categorianome"] = $dados[categorianome];
	}

	// Array com o nome do mês respectivo ao número do mesmo
	$mes[1] = "Janeiro";
	$mes[2] = "Fevereiro";
	$mes[3] = "Março";
	$mes[4] = "Abril";
	$mes[5] = "Maio";
	$mes[6] = "Junho";
	$mes[7] = "Julho";
	$mes[8] = "Agosto";
	$mes[9] = "Setembro";
	$mes[10] = "Outubro";
	$mes[11] = "Novembro";
	$mes[12] = "Dezembro";

	$mes_ = $mes[$month_int];
	
	/* Como mostrar o título do dia da semana no topo */
	$week_titles[] = "D";
	$week_titles[] = "S";
	$week_titles[] = "T";
	$week_titles[] = "Q";
	$week_titles[] = "Q";
	$week_titles[] = "S";
	$week_titles[] = "S";
	
	/* Determina o total de dias existentes no mês */
	$totaldays = 0;
	while ( checkdate( $month_int, $totaldays + 1, $year ) )
		$totaldays++;
	
	/* Contrói a Tabela */
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="130">
	
	<tr>
		<td color=white colspan=7>
		
			<table border=0 cellpadding=0 cellspacing=0 width=130>
			<tr>
				<td width="4"><a href="javascript: MostraCalendario(<?php $incremento-1;?>,<?php $categoria;?>)" class="incremento">«</a></td>
				<td class="titulo_mesano"><?php echo $mes_ .'/'. $year; ?></td>
				<td width="4"><a href="javascript: MostraCalendario(<?php $incremento+1;?>,<?php $categoria;?>)" class="incremento">»</a></td>
			</tr>
			</table>
		
		</td>
	</tr>
	<tr>
		<?php
		for ( $x = 0; $x < 7; $x++ ){
		?>
			<td class="semana">
				<center>
				<b>
				<?php echo $week_titles[$x]; ?>
				</b>
				</center>
			</td>
		<?php
		}
		?>
	</tr>
	<tr>
		<?php
		
		/* Verifica qual celula deverá ficar em branco */
		$offset = date( "w", mktime( 0, 0, 0, $month, $day, $year ) ) + 1;

		if ( $offset > 0 ){
				if( $offset != 7){
					echo str_repeat( '<td class="dia_vazio">&nbsp;</td>', $offset);
				}
			/* Começa entrando as informações */
			for ( $day = 1; $day <= $totaldays; $day++ ){
				/* Se você está no último dia do mês, pula a linha */
				if ( $offset > 6){
					$offset = 0;
					echo '</tr>';
					if ( $day < $totaldays )
						echo '<tr>';
				}
				if($offset == "0")
					$myclass = "domingo";
				else
					$myclass="dia";
				if($day == date("d") AND $month == date("n") AND $year == date("Y")){
					$myclass = "hoje";
				}
				if($agenda[sprintf("%02d", $day)]["id"] <> ''){
					?>
					<td class="<?php $myclass;?>"><a href="index.php?section=content&austd=<?php $agenda[sprintf("%02d", $day)]["categorianome"];?>&dia=<?php sprintf("%02d", $day);?>&mes=<?php sprintf("%02d", $month);?>&ano=<?php sprintf("%04d", $year);?>"><?php $day;?></a></td>
					<?php
				} else {
					?>
					<td class="<?php $myclass;?>"><?php $day;?></td>
					<?php
				}
				$offset++;
			}
			
			/* Preenche o resto das células vazias */
			if ( $offset > 0 )
				$offset = 7 - $offset;
			
			if ( $offset > 0 )
				echo str_repeat( "<td bgcolor=white><font face=verdana size=1>&nbsp;</font></td>", $offset );
			
			?>
	<?php } ?>
	</tr>
	<tr height="10">
		<td colspan=7 bgcolor="white">
		</td>
	</tr>
	<tr height=1>
		<td colspan=7>
		</td>
	</tr>
	</table>
</div>