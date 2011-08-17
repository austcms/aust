<?php
/**
 * Controller principal deste módulo
 *
 * @since v0.1.6 06/07/2009
 */



?>
<h2>FlexFields: nova estrutura</h2>
<form action="" method="post" class="normal">

<?php
/**
 * Escreve cada exPOST
 */
foreach($exPOST as $chave=>$value){
	echo '<input type="hidden" name="'.$chave.'" value="'.$value.'" />';
}
?>
<input type="hidden" name="setupAction" value="criarcampos" />


<div class="campo">
	<label>Quantos campos terá sua nova estrutura?</label>
	<div class="input">
	<select name="qtd_campos" style="width: 70px;">
		<?php
		// cria um select com 20 números
		for($i = 1; $i <= 100; $i++){
		?>
			<option value="<?php echo $i;?>"><?php echo $i;?></option>
		<?php
		}
		?>
	</select>
	</div>
</div>
<br />
<br />
<div class="campo">
	<input type="submit" value="Enviar" class="submit" />
</div>

</form>