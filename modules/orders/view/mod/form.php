<?php
$params = array(
	"aust_node" => $_GET["aust_node"],
);
$moduloConfig = $module->loadModConf($params);

$austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
?>
<h2>Pedido: <?php echo $cart['transaction_nr'] ?></h2>
<p>Detalhes do pedido.</p>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >

Status: 
<?php
if( $cart['sent'] == '0' ){
	echo '<strong>ENVIO PENDENTE</strong>';
	?>
	(<a href="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $_GET['action']?>&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $_GET['w']?>&sent=1">definir como enviado</a>)
	<?php
} else {
	echo 'Envio realizado';
	?>
	(<a href="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $_GET['action']?>&aust_node=<?php echo $_GET['aust_node']?>&w=<?php echo $_GET['w']?>&sent=0">definir como não enviado</a>)
	<?php
}
?>
<br />

<?php
if( !empty($cart['paid_on']) )
	echo 'Pedido pago em '. date("d/m/Y H:i:s", strtotime($cart['paid_on']));
elseif( $cart['paid'] == '1' )
	echo 'Pedido já foi pago (data não definida).';
else
	echo 'Pedido ainda não pago.';
?>
<br />
<?php
echo 'Frete: '.$cart["freight_service"].' no valor de '.Resources::numberToCurrency($cart["freight_value"], "R$");
?>
<br />
<?php
echo 'Prazo de entrega: '.$cart["deadline_days"];
?>
<br />
<?php
echo 'Valor total: '.Resources::numberToCurrency($cart["total_price"], "R$");
?>
<br />
<br />

<table class="listing">
<tr class="header">
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
