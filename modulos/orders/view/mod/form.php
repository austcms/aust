<?php

/*
 * FORMULÁRIO
 */

/*
 * Carrega configurações automáticas do DB
 */
    $params = array(
        "aust_node" => $_GET["aust_node"],
    );
    $moduloConfig = $module->loadModConf($params);


/*
 * Ajusta variáveis iniciais
 */
    $austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';


// $module->getStructureConfig("generate_preview_url")
?>
<h2>Pedido: <?php echo $cart['transaction_nr'] ?></h2>
<p>Detalhes do pedido.</p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >

Status: 
<?php
if( $cart['pending'] == '1' ){
	echo 'PENDENTE';
	?>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $_GET['action']?>&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $_GET['w']?>&pending=0">
		Não pendente
	</a>
	<?php
} else {
	echo 'Não pendente';
	?>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $_GET['action']?>&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $_GET['w']?>&pending=1">
		pendente
	</a>
	<?php
}
?>
<br />

<?php
if( $cart['paid'] == '1' )
	echo 'Pedido já foi pago.';
else
	echo 'Pedido ainda não pago.';
?>
<br />
<br />

<table class="listagem">
<tr class="titulo">
	<td>Id</td>
	<td>Título</td>
	<td>Descrição</td>
	<td>Preço</td>
	<td>Quantidade</td>
</tr>
<?php
foreach( $dados as $item ){
	?>
	<tr style="font-size: 0.9em">
		<td>
			<?php echo $item['id'] ?>
		</td>
		<td>
			<?php echo $item['product_title'] ?>
		</td>
		<td>
			<?php echo $item['product_description'] ?>
		</td>
		<td>
			R$ <?php echo number_format($item['price'], 2, ',' ,'.') ?>
		</td>
		<td>
			<?php echo $item['quantity'] ?>
		</td>
		
		
	</tr>
	
	<?php
}
?>
</table>

</form>
<br />
<p>
    <a href="javascript: history.back()"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
