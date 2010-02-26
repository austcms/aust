<?php

/*
 *
 * EMBED SAVE
 * 
 */

/*
 * carrega módulos que contenham propriedade embed
 */
$embed = $this->LeModulosEmbed();

$embedIds = array();
if( is_array($embed) ){
    foreach( $embed as $valor ){
        $embedIds[] = $valor["id"];
    }
}

/*
 * Contém quais as outras estruturas (nodes) que são relacionadas à
 * estrutura atual.
 */

$embedRelatedNodes = $this->getRelatedEmbed($_GET["aust_node"]);

pr($embed);
/*
 * Salva o objeto do módulo atual para fazer embed
 */
if( !empty($embed) ) {
/*
 * Caso tenha embed, serão carregados modulos embed. O objeto do módulo atual
 * é $modulo, sendo que dos embed também. Então guardamos $modulo,
 * fazemos unset nele e reccaregamos no final do script.
 */

    $tempmodulo = $modulo;
    unset($modulo);
    foreach($embed AS $chave=>$valor) {

        /*
         * Há relacionamento entre as estruturas
         */
        if( in_array( $valor["id"], $embedRelatedNodes ) ){

            foreach($valor AS $chave2=>$valor2) {
                if($chave2 == 'pasta') {
                    if(is_file($valor2.'/embed/gravar.php')) {
                        include($valor2.'/index.php');
                        pr($modconf);
                        //include($valor2.'/embed/gravar.php');
                    }
                }
            }
            
        }
        
    } // fim do foreach por cada estrutura com embed
    $modulo = $tempmodulo;
} // fim do embed
?>
