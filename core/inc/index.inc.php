<?php

/*
 * WIDGETS
 */

$widgets = new Widgets($envParams, $administrador->getId());

?>

<h2>Painel Principal</h2>
<p>Este é o sistema onde você gerencia o conteúdo do seu site.</p>

<div id="painel">

    <?php /* Widget Group - Coluna (Primeira) */ ?>
    <div class="widget_group">

        <ul id="sortable1" class="connectedSortable draganddrop">
        <?php
        $c = $widgets->getInstalledWidgets();

        /*
         * WIDGETS - COLUNA 1
         */
        if( !empty($c['1']) ):

            foreach( $c['1'] as $widget ){
                ?>
                    <li id="widgets_<?php echo $widget->getId(); ?>">
                        <div class="widget">
                            <div class="titulo">
                                <h3><?php echo $widget->getTitle(); ?></h3>
                            </div>
                            <div class="content">
                                <?php echo $widget->getHtml(); ?>
                            </div>
                            <div class="footer">
                            </div>
                        </div>
                    </li>
                <?php
            }
            
        else:
            ?>
            Esta coluna não possui Widgets.
            <?php
        endif;
        ?>
        </ul>

        <br/>
        <a href="adm_main.php?section=widgets&column_nr=1">Adicionar Widget</a>
              
    </div>

    <?php /* Widget Group - Coluna (Segunda) */ ?>
    
    <div class="widget_group">

        <ul id="sortable2" class="connectedSortable draganddrop">
        <?php
        /*
         * WIDGETS - COLUNA 1
         */
        if( !empty($c['2']) ):

            foreach( $c['2'] as $widget ){
                ?>
                <li id="widgets_<?php echo $widget->getId(); ?>">
                    <div class="widget">
                        <div class="titulo">
                            <h3><?php echo $widget->getTitle(); ?></h3>
                        </div>
                        <div class="content">
                            <?php echo $widget->getHtml(); ?>
                        </div>
                        <div class="footer">
                        </div>
                    </div>

                </li>
                <?php
            }

        else:
            ?>
            Esta coluna não possui Widgets.
            <?php
        endif;

        ?>
        </ul>
        <br/>
        <a href="adm_main.php?section=widgets&column_nr=2">Adicionar Widget</a>

    </div>
    
    
</div>


<div class="painel-metade painel-dois">
    <?php /*<div class="painel">
        <div class="titulo">
            <h2>Pendências</h2>
        </div>
        <div class="corpo">


        </div>
        <div class="rodape"></div>
    </div>*/?>

<?php
/**
 * RESUMO DOS MÓDULOS INSTALADOS
 *
 * Mostra conteúdos cadastrados ultimamente
 */
/**
 * Carregas os módulos em uma array contendo algumas informações sobre ele, como endereço físico
 */
$painel = $modulos->LeModulosParaArray();
/**
 * Se há módulos a serem carregados
 */
if( !empty($painel) ){
    /**
     * Faz um loop por cada módulo
     */
    foreach($painel AS $chave=>$valor){
        
        /**
         * Se o módulo possui arquivo de resumo para a interface frontal do CMS
         * Carrega arquivos e mostra dados das arrays que vem.
         *
         * Aqui há uma verificação se o módulo possui responser
         *
         */
        $responser = false;
        unset($configFile);
        if( is_file($valor['pasta'].'/index.php') ){
            include($valor['pasta'].'/index.php');
            include($valor['pasta'].'/'.MOD_CONFIG);
            if( (
                    is_bool($modInfo['responser'])
                    AND $modInfo['responser'] == true
                )
                OR $modInfo['responser']['actived'] == true )
            {
                $responser = true;
            }
        }

        if( $responser == true ){


            //include($valor['pasta'].'/front_painel.inc.php');

            /**
             * Toma conteúdos
             */
            $conteudo = $modulo->retornaResumo();
            //pr($conteudo);

            
            if(!empty($conteudo)){
                ?>
                <div class="painel front_painel">
                    <div class="titulo">
                        <h2><?php
                        /**
                         * Título do módulo
                         */
                        echo $modInfo['nome'];

                        ?></h2>
                    </div>
                    <div class="corpo">
                        <?php
                        /**
                         * Mostra cada categoria com seus textos
                         */
                        /**
                         * Escreve parágrafo introdutório ao módulo
                         */
                        if(!empty($conteudo['intro'])){
                            echo '<p class="intro">'.$conteudo['intro'].'</p>';
                        }

                        /**
                         * Faz um loop por cada conteúdo enviado pelo módulo e
                         * escreve uma lista na tela com títulos
                         */
                        foreach($conteudo as $chave=>$valor){
                            if(is_int($chave)){
                                $params = array(
                                    'estrutura' => $chave,
                                    'permissoes' => $categoriasPermitidas,
                                );
                                if($permissoes->verify($params)){
                                    echo '<ul>';
                                    echo '<li class="titulo">'.$valor['titulo'].'</li>';
                                    if(is_array($valor['conteudo'])){
                                        foreach($valor['conteudo'] as $cChave=>$cValor){
                                            echo '<li class="conteudo">';
                                            /**
                                             * Escreve $chave no aust_node porque o valor é da estrutura.
                                             */
                                            echo '<a href="adm_main.php?section=conteudo&action=editar&aust_node='.$chave.'&w='.$cValor['id'].'">'.$cValor['titulo'].'</a>';
                                            echo '</li>';
                                        }
                                    }
                                    echo '</ul>';
                                }
                            }
                        }
                        //echo $moduloConf['nome'];

                        ?>

                    </div>
                    <div class="rodape"></div>
                </div>
                <?php
            }
            
            unset($modulo);
            //pr($conteudo);
            unset($conteudo);
        }
    }
}
?>

</div>


<?php
/*
	if(($escala == "webmaster" OR $escala == "administrador") AND $senha <> "senha"){
	?>
        <div style="width: 690px; minheight: 20px; border: 1px dotted silver; padding: 5px; margin-top: 15px;">
        	<a name="senhas" style="color: black;"><h3>Senhas</h3></a>
        	<a href="adm_main.php?senha=senha#senhas">Clique aqui para ver as senhas dos usuários</a>
        </div>
    <?
	} else if(($escala == "webmaster" OR $escala == "administrador") AND $senha == "senha") {
		include("inc/inc_admins_passw_retrieve.php");
	}
 *
 */
?>