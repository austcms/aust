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
<div class="listagem cart">
<h2>
	<?php echo $h1;?>
</h2>
<p>
	Os itens abaixo estão cadastrados como pedidos. 
	Você deve preparar as encomendas assinaladas como pendentes (em amarelo).
</p>
<?php


/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">
<a name="list"></a>
<?php
/*
 * Pode excluir conteúdo?
 */
/*
if( StructurePermissions::getInstance()->canDelete($austNode) ){
	?>
	<div class="painel_de_controle"><input type="submit" class="js_confirm" name="deletar" value="Deletar selecionados" />
	</div>
	<?php
}
*/
?>


<?php
$counting = 1;
foreach( $query as $dados ){
	
	$statusClass = 'ready';
	if( $dados['sent'] == '0' ){
		$statusClass = 'pending';
	}
	if( $dados['gateway_complete'] == '0' ){
		$statusClass = 'not_paid';
	}
	if( $dados['gateway_cancelled'] == '1' ){
		$statusClass = 'cancelled';
	}
	?>
	<div class="transaction <?php echo $statusClass ?>">

		<div class="containner">
			<div class="number">
			<?php echo $dados['transaction_nr']?>
			</div>
		</div>

		<div class="status">
			<?php
			if( $dados['gateway_cancelled'] == '1' ){
				?>
				<h2>Cancelado</h2>
				Pedido não pago 
				<?php
			} else if( $dados['gateway_complete'] == '0' ){
				?>
				<h2>Aguardando</h2>
				Pedido não pago 
				<?php
			} else if( $dados['sent'] == '0' ){
				if( $dados['gateway_complete'] == '1' ){
					?>
					<h2>Pago e Pendente</h2>
					Envio pendente
					<?php
				} else if( $dados['gateway_complete'] == '0' ){
					?>
					<h2>Pendente, não pago</h2>
					<?php
				}
			} else {
				?>
				<h2>Completa</h2>
				Nada pendente
				<?php
			}
			?>
			
		</div>
		<div class="containner">

			<div class="gateway">
				<?php
				if( $dados['gateway_complete'] == '1' ){
					?>
					<span class="paid">Gateway confirmou pagamento</span>
					<?php
				} else if( $dados['gateway_cancelled'] == '1' ){
					?>
					<span class="cancelled">Pedido cancelado pelo gateway</span>
					<?php
				} else if( $dados['gateway_analysing'] == '1' ){
					?>
					<span class="analysing">Gateway analisando pagamento</span>
					<?php
				} else if( $dados['gateway_waiting'] == '1' ){
					?>
					<span class="waiting">Gateway está aguardando pagamento</span>
					<?php
				} else {
					?>
					<span class="no_response">Gateway não enviou dados ainda</span>
					<?php
				}
				?>
				
			</div>	
			<div class="date_listing">		
				<?php if( !empty($dados['created_on']) ){ ?>
					<div class="date scheduled_to">
						<strong>Realizada:</strong> <?php echo dateName($dados['created_on'], "Hoje", "Ontem") ?>
					</div>
				<?php } ?>
				<?php if( !empty($dados['scheduled_on']) ){ ?>
					<div class="date scheduled_to">
						<strong>Agendado:</strong> <?php echo ucfirst( dateName($dados['scheduled_on']) );?>
					</div>
				<?php } ?>
				<?php if( !empty($dados['paid_on']) ){ ?>
					<div class="date scheduled_to">
					<strong>Pago:</strong> <?php echo ucfirst( dateName($dados['paid_on']) );?>
					</div>
				<?php } ?>
			</div>

			
			<?php
			
			if( empty($dados['client_name']) ){
				?>
				<em>Sem cliente definido</em>
				<?php
			} else {
				?>
				<div class="client_name">
					<span class="label">
						Ao cliente:
					</span>
					<span class="value name">
						<a href="adm_main.php?section=content&action=edit&aust_node=<?php echo $dados['client_node'] ?>&w=<?php echo $dados['client_id']?>">
							<?php echo $dados['client_name']; ?>
						</a>
					</span>
				</div>
				<?php
			}
			?>
			<div class="actions">
				<a href="adm_main.php?section=content&action=edit&aust_node=<?php echo $austNode?>&w=<?php echo $dados['id']?>">
					Ver detalhes
				</a>
			</div>
			<div>
			<?php
			/*
			if( StructurePermissions::getInstance()->canDelete($austNode) ){
				?>
				<input type='checkbox' name='itens[]' value='<?php echo $dados['id'];?>'>
				<?php
			}
			*/
			?>
			</div>
			
		
		</div>
	</div>
	<?php if( $counting == 4 ){ ?>
		<br clear="all" />
		
	<?php
	}
	$counting++;
}
?>

</form>
<br clear="all" />
<?php
/*
 * PAGINAÇÃO
 * mostra painel de navegação para paginação
 */

	//$sql = $module->getSQLForListing($categorias);
	$total_registros = $module->totalRows;

	$total_paginas = $total_registros/$numPorPagina;
	$prev = $page - 1;
	$next = $page + 1;
	// se página maior que 1 (um), então temos link para a página anterior
	if ($page > 1) {
		$prev_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$prev.'">Anterior</a>';
	} else { // senão não há link para a página anterior
		$prev_link = "Anterior";
	}
	// se número total de páginas for maior que a página corrente,
	// então temos link para a próxima página
	if ($total_paginas > $page) {
		$next_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$next.'">Próxima</a>';
	} else {
	// senão não há link para a próxima página
		$next_link = "Próxima";
	}

	// vamos arredondar para o alto o número de páginas  que serão necessárias para exibir todos os
	// registros. Por exemplo, se  temos 20 registros e mostramos 6 por página, nossa variável
	// $total_paginas será igual a 20/6, que resultará em 3.33. Para exibir os  2 registros
	// restantes dos 18 mostrados nas primeiras 3 páginas (0.33),  será necessária a quarta página.
	// Logo, sempre devemos arredondar uma  fração de número real para um inteiro de cima e isto é
	// feito com a  função ceil()/
	$total_paginas = ceil($total_paginas);
	if($total_paginas > 1){
		$painel = "";
		for ($x=1; $x<=$total_paginas; $x++) {
			if ($x == $page) {
				// se estivermos na página corrente, não exibir o link para visualização desta página
				$painel .= " $x ";
			} else {
			   $painel .= ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$x.'">'.$x.'</a> ';
			}
		}
		// exibir painel na tela
		echo '<div class="paginacao"><strong>Navegação</strong>: '.$prev_link.' | '.$painel.' | '.$next_link.' </div>';
	}

?>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
</div>