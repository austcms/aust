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

<h2>Configurações</h2>
<p>
    Nesta tela estão as principais configurações do sistema.
</p>


<div class="painel">
    <?php
    /*
     * NOME DAS TABS - GERAL E SISTEMA
     */
    ?>

    <div class="tabs_area">

        <!-- the tabs -->
        <ul class="tabs">

            <?php
            /*
             * NOME DA PRIMEIRA TAB - GERAL
             */
            ?>
            <li><a href="#">Geral</a></li>
            <?php
            /*
             * NOME DA SEGUNDA TAB - SISTEMA
             */
            ?>
            <li><a href="#">Sistema</a></li>


        </ul>

    </div>
    
    
    <div class="panes">

    <?php /* TR Header igual para todas as panes */ ?>
        <table border="0" class="pane_listing">
            <tr class="header">
                <td class="opcoes">Opções</td>
            </tr>
        </table>
        <br clear="all" />
    <?php /***** Até aqui ******/ ?>


    <?php
    /*
     * Background - CONTEÚDO DA PRIMEIRA TAB - GERAL
     */
    ?>
        <div class="background">
            <div class="list">
                <td class="title">
                    <span>NOME DO SITE</span>
                    <span>NOME DO SITE</span>
                </td>

                <div class="input">
                    <ul>
                        <li><input type="text" name="nome_site" /></li>
                        <li><input type="text" name="nome_site" /></li>
                    </ul>
                </div>
                
                <div class="dica">
                    <li>Entre o nome do seu site neste campo.</li>
                    <li>Campo de teste.</li>
                </div>
            </div>

        </div>

    <?php
    /*
     * Background - CONTEÚDO DA SEGUNDA TAB - SISTEMA
     */
    ?>
        <div class="background">
            <tr class="list">
                <td class="title">
                    <span>DAR ACESSO ÀS CATEGORIAS</span>
                    <span>USUÁRIOS ACESSAM PERMISSÕES</span>
                </td>
                <td class="input">
                    <ul>
                        <li><input type="text" name="nome_site" /></li>
                        <li><input type="text" name="nome_site" /></li>
                    </ul>
                </td>

                <td class="dica">
                    <li>Você deseja que o usuário possa configurar as categorias.</li>
                    <li>Usuários podem configurar permissões.</li>
                </td>
            </tr>

        </div>

    </div>

</div>


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