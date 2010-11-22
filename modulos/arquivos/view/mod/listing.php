<?php
$h1 = 'Listando conteúdo: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
$nome_modulo = $this->aust->LeModuloDaEstrutura($_GET['aust_node']);
$sql = "SELECT
            id,nome
        FROM
            ".Aust::$austTable."
        WHERE
            id='".$_GET['aust_node']."'";


$query = $modulo->connection->query($sql);

$cat = $query[0]['nome'];?>
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section'];?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<h2><?php echo $h1; ?></h2>
<p>Abaixo você encontra a listagem dos últimos itens desta categoria.</p>
<?php

if((!empty($filter)) AND ($filter <> 'off')){
	$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
}

$categorias = $aust->LeCategoriasFilhas('',$_GET['aust_node']);
$categorias[$_GET['aust_node']] = 'Estrutura';
//print_r($categorias);
$sql = $modulo->loadSql($categorias);
	//	echo '<br><br>'.$sql .'<br>';


$query = $modulo->connection->query($sql);
$query = $modulo->replaceFieldsValueIfEmpty($query);
//pr($query);

/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET[aust_node];?>">
<a name="list"></a>
<?php
/*
 * Pode excluir conteúdo?
 */
if( $permissoes->canDelete($austNode) ){
    ?>
    <div class="painel_de_controle"><input type="submit" name="deletar" value="Deletar selecionados" />
    </div>
    <?php
}

?>

<div id="listing_table">
	<?php
	include($modulo->getIncludeFolder().'/view/mod/_listing_thumbs_view.php');
	?>
</div>



</form>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>