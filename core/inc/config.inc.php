<?php
if($_POST['gravar']){
    unset($_POST['gravar']);

    foreach($_POST as $key=>$valor){
        $params = array(
            'id' => $key,
            'valor' => $valor,
        );

        $msg = $config->updateOptions($params);
        unset($params);
    }

    $status = $msg;
}

/*
 * NOVA CONFIGURAÇÃO
 */
if($_POST['novaconfig']){
    unset($_POST['novaconfig']);
    $params = array(
        'propriedade' => $_POST['propriedade'],
        'tipo' => $_POST['tipo'],
        'valor' => $_POST['valor'],
        'nome' => $_POST['nome'],
    );

    $config->ajustaOpcoes($params);
    // Grava configuração no DB
    $status = $config->GravaConfig();
}

?>

<?php
if(!empty($_POST['inserirmodulo'])){
	$status = $aust->gravaEstrutura(
                                    array(
                                        'nome' => $_POST['nome'],
                                        'categoriaChefe' => $_POST['categoria_chefe'],
                                        'estrutura' => 'estrutura',
                                        'moduloPasta' => $_POST['modulo'],
                                        'autor' => $administrador->LeRegistro('id')
                                    )
                                );
}
?>
<?php if(!empty($status)){ ?>
	<div class="box-full">
		<div class="box alerta">
			<div class="titulo">
				<h2>Status</h2>
			</div>
			<div class="corpo">
				<?=$status;?>
			</div>
		</div>
	</div>
<?php } ?>

<h1>Configurações</h1>
<p>
    Olá. Aqui você pode configurar todas as opções disponíveis do site.
</p>

<?php

/*
 * MOSTRA CONFIGURAÇÕES
 */

if( $administrador->tipo != "Webmaster" ){
    $params = array(
        'where' => "tipo='global'",
    );
}

$options = $config->getOptions($params);

if( $options ){
    ?>
    <form method="post" action="adm_main.php?section=<?=$_GET['section'];?>" class="simples">
    <?php

        foreach($options as $dados ){
            $tipo = $dados['tipo'];

            if($tipo <> $tipo_anterior){
                echo '<h2>'.$tipo.'</h2>';
            }

            echo '<div class="campo">';
            echo '<label>';

            if( empty($dados['nome']) ){
                echo $dados['propriedade'];
            } else {
                echo $dados['nome'];
            }
            echo '</label>';
            echo '<input type="text" name="'.$dados['id'].'" value="'.$dados['valor'].'" class="text" />';
            echo '</div>';
            $tipo_anterior = $tipo;
        }

    ?>
    <input type="submit" name="gravar" value="Enviar" class="submit" />
    </form>
<?php } else { ?>
    <p class="alerta">
    	Nenhuma configuração ajustada ainda.
    </p>
<?php } ?>
<p>
    <a href="javascript: history.back();"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>



<?php
/*
 *
 * NOVA CONFIGURAÇÃO
 *
 */
if( $administrador->tipo == "Webmaster" ){
    ?>
    <h2>Nova configuração</h2>
    <p>A seguir, você pode criar uma nova configuração.</p>
    <form method="post" action="adm_main.php?section=<?=$_GET['section']?>" class="simples">

    <div class="campo">
        <label>Nome humano da configuração:</label>
        <input type="text" name="nome" class="text" />
    </div>
    <div class="campo">
        <label>Nome da config. no DB:</label>
        <input type="text" name="propriedade" class="text" />
    </div>
    <div class="campo">
        <label>Valor:</label>
        <input type="text" name="valor" class="text" />
    </div>
    <div class="campo">
        <label>Tipo:</label>
        <select name="tipo">
            <option value="global">Global (todos tem acesso)</option>
            <option value="mod_conf">mod_conf - configuração de módulos</option>
        </select>
    </div>
    <div class="campo">
        <input type="submit" name="novaconfig" value="Enviar" class="submit" />
    </div>


    </form>
    <p>
        <a href="javascript: history.back();"><img src="img/layoutv1/voltar.gif" border="0" /></a>
    </p>
    <?php
}
?>