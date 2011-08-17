<?php
/**
 * Listagem dos dados cadastrados deste módulo. É carregado dinamicamente pelo
 * Core do Aust.
 *
 * @category Listagem
 * @author Alexandre de Oliveira <alexandreoliveira@gmail.com>
 * @since 
 */
?>
<div class="listing">
<h2>
	Calendário: <?php echo $h1;?>
</h2>
<p>
	Confira seus compromissos abaixo.
</p>
<?php
?>
<div class="calendario">
	<?php
	if( empty($month_int) )
		$month_int = date("n");
	if( empty($year_int) )
		$year = date("Y");
	else
		$year = $year_int;

	$day = date("d");
	
	$user = User::getInstance();

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
	$week_titles[] = "Dom";
	$week_titles[] = "Seg";
	$week_titles[] = "Ter";
	$week_titles[] = "Qua";
	$week_titles[] = "Qui";
	$week_titles[] = "Sex";
	$week_titles[] = "Sáb";

	/* Determina o total de dias existentes no mês */
	$totaldays = 0;
	while ( checkdate( $month_int, $totaldays + 1, $year ) )
		$totaldays++;

	/* Contrói a Tabela */
	?>
	<table class="calendar">
		<tr>
			<td colspan=7 class="month_year">

				<h3 style="margin-bottom: 0px;"><?php echo $mes_.'/'. $year; ?></h3>

				<?php
				/*
				 * Próximo mês ou anterior
				 */
				$nextMonth = $month_int+1;
				$prevMonth = $month_int-1;
				$prevYear = $year;
				$nextYear = $year;

				if( $nextMonth > 12 ){
					$nextMonth = 1;
					$nextYear++;
				}
				if( $prevMonth < 1 ){
					$prevMonth = 12;
					$prevYear--;
				}


				?>
				<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&action=<?php echo $_GET['action'] ?>&aust_node=<?php echo $_GET['aust_node'] ?>&m=<?php echo $prevMonth ?>&y=<?php echo $prevYear ?>">
				Mês Anterior</a> -
				<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&action=<?php echo $_GET['action'] ?>&aust_node=<?php echo $_GET['aust_node'] ?>&m=<?php echo $nextMonth ?>&y=<?php echo $nextYear ?>">
				Próximo</a>
				<?php
				if( $month_int != date("n") OR
					$year_int != date("Y") )
				{
					?>
					- <a href="adm_main.php?section=<?php echo $_GET['section'] ?>&action=<?php echo $_GET['action'] ?>&aust_node=<?php echo $_GET['aust_node'] ?>&m=<?php echo date("n"); ?>&y=<?php echo date("Y") ?>">
					Mês atual</a>
					<?php
				}
				?>
			</td>
		</tr>
		<?php
		/*
		 * DIAS DA SEMANA
		 *
		 * Dom, seg, ter, qua, qui, sex, sab
		 */
		?>
		<tr>
			<?php
			for ( $x = 0; $x < 7; $x++ ) {
				?>
				<td class="week_day">
					<strong>
					<?php echo $week_titles[$x]; ?>
					</strong>
				</td>
				<?php
			}
			?>
		</tr>
		<tr class="days">
			<?php

			/* Verifica qual celula deverá ficar em branco */
			$offset = date( "w", mktime( 0, 0, 0, $month_int, $day, $year ) ) + 1;

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
							echo '<tr class="days">';
					}
					if($offset == "0")
						$myclass = "sunday";
					else
						$myclass="";
					if($day == date("d") AND $month_int == date("n") AND $year == date("Y")) {
						$myclass = "today";
						$dayString = "Hoje";
					} else {
						$dayString = $day;
					}
					?>
					<td class="<?php echo $myclass;?> day">
						<span class="day_number"><?php echo $dayString;?></span><br clear="all" />
						<?php
						//vd($results[$day]);
						if( !empty($results[$day]) ){
							?>
							<span class="agenda_day_items">
							<?php
							$i = 0;
							$first = '';
							foreach( $results[$day] as $dayAgenda ){
								if( $i == 0 )
									$first = 'agenda_first_day_item';
								else
									$first = '';
								$i++;
								?>
								<div class="agenda_day_item <?php echo $first ?>">
									<?php
									/*
									 * Actor
									 */
									if( !empty($dayAgenda['actor_admin_name']) ){
										$itselfActor = '';
										if( $dayAgenda['actor_admin_id'] == $user->getId() ){
											$dayAgenda['actor_admin_name'] = "Você";
											$itselfActor = 'agenda_day_actor_itself';
										}
										?>
										<div class="agenda_day_actor <?php echo $itselfActor; ?>">
											<?php echo $dayAgenda['actor_admin_name']; ?>
										</div>
										<?php
									}
									/*
									 * Horário
									 */
									if( $dayAgenda['occurs_all_day'] != 1 ){
										?>
										<div class="agenda_day_time">
											<?php
											echo date("H:i", strtotime($dayAgenda['start_datetime']) );
											echo '-';
											echo date("H:i", strtotime($dayAgenda['end_datetime']) );
											?>
										</div>
										<?php
									} else {
										?>
										<div class="agenda_day_time">
											Todo o dia
										</div>
										<?php
									}
									/*
									 * Título
									 */
									?>
									<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&action=edit&aust_node=<?php echo $_GET['aust_node'] ?>&w=<?php echo $dayAgenda['id'] ?>">
									<?php echo $dayAgenda['title'] ?>
									</a>
								</div>
								<?php
							}
							?>
							</span>
							<?php
						}
						?>
					</td>
					<?php
					$offset++;
				}

				/* Preenche o resto das células vazias */
				if ( $offset > 0 )
					$offset = 7 - $offset;

				if ( $offset > 0 )
					echo str_repeat( "<td class=\"empty\"><font face=verdana size=1>&nbsp;</font></td>", $offset );

			}
			?>
		</tr>
	</table>
</div>


<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
</div>