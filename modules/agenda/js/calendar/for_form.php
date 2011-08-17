<div class="calendario">
	<?php
	$month_int = date("n");
	$year = date("Y");
	
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
	<table border="0" cellpadding="0" cellspacing="0" class="calendar">
		<tr>
			<td colspan=7 class="month_year">
				<table border=0 cellpadding=0 cellspacing=0 width="130" class="month_year">
					<tr>
						<td width="4"><a href="javascript: MostraCalendario(<?php echo $incremento-1;?>,<?php echo $categoria;?>)" class="incremento">«</a></td>
						<td class="titulo_mesano"><?php echo $mes_ .'/'. $year; ?></td>
						<td width="4"><a href="javascript: MostraCalendario(<?php echo $incremento+1;?>,<?php echo $categoria;?>)" class="incremento">»</a></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<?php
			for ( $x = 0; $x < 7; $x++ ) {
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
		<tr class="days">
			<?php

			/* Verifica qual celula deverá ficar em branco */
			$offset = date( "w", mktime( 0, 0, 0, $month, $day, $year ) ) + 1;

			if ( $offset > 0 ) {
				if( $offset != 7) {
					echo str_repeat( '<td class="dia_vazio day">&nbsp;</td>', $offset);
				}
				/* Começa entrando as informações */
				for ( $day = 1; $day <= $totaldays; $day++ ) {
					/* Se você está no último dia do mês, pula a linha */
					if ( $offset > 6) {
						$offset = 0;
						echo '</tr>';
						if ( $day < $totaldays )
							echo '<tr>';
					}
					if($offset == "0")
						$myclass = "domingo";
					else
						$myclass="dia";
					if($day == date("d") AND $month == date("n") AND $year == date("Y")) {
						$myclass = "hoje";
					}
					if($agenda[sprintf("%02d", $day)]["id"] <> '') {
						?>
						<td class="<?php echo $myclass;?> day"><a href="index.php?section=content&austd=<?php echo $agenda[sprintf("%02d", $day)]["categorianome"];?>&dia=<?php echo sprintf("%02d", $day);?>&mes=<?php echo sprintf("%02d", $month);?>&ano=<?php echo sprintf("%04d", $year);?>"><?php echo $day;?></a></td>
						<?php
					} else {
						?>
						<td class="<?php echo $myclass;?> day"><?php echo $day;?></td>
						<?php
					}
					$offset++;
				}

				/* Preenche o resto das células vazias */
				if ( $offset > 0 )
					$offset = 7 - $offset;

				if ( $offset > 0 )
					echo str_repeat( "<td bgcolor=white><font face=verdana size=1>&nbsp;</font></td>", $offset );

			}
			?>
		</tr>
	</table>
</div>