<?php
/*
 * EMBED OWN FORM
 * mostra <input> de módulos embedownform
 *
 * Embed Own Form significa que o formulário possui a própria tag <form>, não
 * dependendo do <form> principal
 *
 * É padrão e pode ser copiado para todos os forms
 */
?>
<table border="0" cellpadding="0" cellspacing="0" class="form">
<?php
    /**
     * Lê somente módulos que contém habilidade Embed
     */
    $embed = $module->LeModulosEmbedOwnForm();
    /**
     * Módulos com habilidade embed liberados pelo admin para esta estrutura
     */
    $liberados = $module->LeModulosEmbedOwnFormLiberados($_GET['aust_node']);

    /**
     * Se há algum embed
     */
    if( !empty($embed) AND !empty($liberados)){

        /**
         * Faz um loop por cada um, mostrando seu próprio formulário contido em
         * modulo/nomedomodulo/embed/ownform.php
         */
        foreach($embed AS $chave=>$valor){

			if( !is_file($valor['pasta'].'/embed/embed_info.php') )
				continue;
			
            include($valor['pasta'].'/embed/embed_info.php');
            $pastatmp = array_reverse( explode('/', $valor['pasta']));
            $pastatmp = $pastatmp[0];

            // @todo - a verificação abaixo precisa ser refatorada assim que possível
            if(!$embed_form['liberacao'] OR in_array($pastatmp, $liberados)){
                
                // Verifica se o modo atual (novo conteúdo, edição de conteúdo) é aceitado pelo form
                if(in_array($_GET['action'], $embed_form['ownformactions'])){

                    if(is_file($valor['pasta'].'/embed/ownform.php')){

                        // insere o index.php, carregando também a classe
                        if(is_file($valor['pasta'].'/index.php')){
                            include($valor['pasta'].'/index.php');
                        }

                        // desmancha variável $titulo
                        unset($titulo);
                        if(is_file($valor['pasta'].'/embed/embed_info.php')){
                            include($valor['pasta'].'/embed/embed_info.php');
                            $titulo = $embed_form['titulo'];
                        }

                        if(empty($titulo)){
                            include($valor['pasta'].'/config.php');
                            $titulo = $modInfo['nome'];
                        }

                        ?>
                        <tr>
                            <td colspan="2">
                                <h1>
                                <?php
                                /**
                                 * $título é carregado do arquivo /embed/embed_info.php do módulo que contenha EmbedOwnForm
                                 */
                                echo $titulo;
                                ?>
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            <?php
                            /*
                             * inclui o conteúdo de ownform
                             */
                             include($valor['pasta'].'/embed/ownform.php');
                            ?>
                            </td>
                        </tr>
                        <tr height="10">
                            <td colspan="2"></td>
                        </tr>
                        <?php
                        unset($embed_form);
                    }
                }
            }
        }
    }
?>
</table>