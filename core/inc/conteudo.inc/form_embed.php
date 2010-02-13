<?php
    /*
     * EMBED
     * mostra <input> de módulos embed
     *
     * Embed significa que os <input>s aqui mostrados serão enviados juntamente
     * com o <form> principal
     *
     * O arquivo inserido é /embed/form.php do módulo que $embed==true
     */

    $tempmodulo = $modulo;
    $embed = $modulo->LeModulosEmbed();
    
    if( !empty($embed) ){
        foreach( $embed as $valor ){
            $embedIds[] = $valor["id"];
        }
    }

    /*
     * Contém quais as outras estruturas (nodes) que são relacionadas à
     * estrutura atual.
     */
    $embedRelatedNodes = $modulo->getRelatedEmbed($_GET["aust_node"]);



    if( !empty($embed) AND count($embed)){
        ?>
        <tr>
            <td colspan="2">
                <input type="hidden" name="contentTable" value="<?php
                echo $modulo->getContentTable();
                ?>" />
                <br /></td>
        </tr>

        <?php
        /*
        <tr>
            <td colspan="2"><h1>Outras opções</h1></td>
        </tr>
        
        <?php
         * 
         */

        /*
         * Contagem atual do módulo embed
         */
        $embedI = 0;
        foreach($embed AS $chave=>$valor){
            if( in_array( $valor["id"], $embedRelatedNodes ) ){

                // Inicializa algumas variáveis
                    $embed_form = array();

                // Informações do módulo
                if(is_file($valor['pasta'].'/embed/embed_info.php')){
                    include($valor['pasta'].'/embed/embed_info.php');
                }

                include($valor['pasta'].'/'.MOD_CONFIG);
                // Em quais actions este módulo deve ser embed?
                echo '<input type="hidden" name="embed['.$embedI.'][className]" value="'.$modInfo['className'].'" />';
                echo '<input type="hidden" name="embed['.$embedI.'][dir]" value="'.$valor['pasta'].'" />';

                //pr($embed_form);
                if(!empty($embed_form) AND in_array($_GET['action'], $embed_form['actions'])){
                    if(is_file($valor['pasta'].'/embed/form.php')){
                        include($valor['pasta'].'/embed/form.php');
                        //include($valor['pasta'].'/index.php');
                        for($i = 0; $i < count($embed_form); $i++){
                            ?>
                            <tr>
                                <td valign="top" style="padding-top: 3px;"><label><?php echo $embed_form[$i]['propriedade']?>:</label></td>
                                <td valign="top">
                                <?php if(!empty($embed_form[$i]['intro'])){ echo '<p class="explanation">'.$embed_form[$i]['intro'].'</p>'; } ?>
                                <?php echo $embed_form[$i]['input'];?>
                                <?php if(!empty($embed_form[$i]['explanation'])){ echo '<p class="explanation">'.$embed_form[$i]['explanation'].'</p>'; } ?>
                                </td>
                            </tr>
                            <tr height="10">
                                <td colspan="2"><div style="border-top: 1px dashed #e6e6e6; height: 5px; padding-bottom: 3px; margin-top: 5px;"></div></td>
                            </tr>
                            <?php
                            unset($embed_form);
                            //unset($embed);
                        }
                    }
                }
            } // fim if( pode ser embed )
            $embedI++;
        } // fim for()

        unset($embedI);
    }
    //unset($embed);
    //unset($modulo);
    //$modulo = $tempmodulo;

?>
