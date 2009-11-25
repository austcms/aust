<?php
/* 
 * EMBED -> gravar
 */

$conteudo_tabela = $modulo->LeTabelaDaEstrutura();
// Se $_post[w] está vazio, é um novo conteúdo
if(empty($_POST['w']) AND $_POST['metodo'] == 'criar'){
    $insert_id = mysql_insert_id();
} elseif(!empty($_POST['w'])) {
    $insert_id = $_POST['w'];
}

// se foi clicado algum item no form de inclusão
if(is_array($_POST['privid'])){

    //deleta privilégio anterior para fazer a atualização agora
    $sql_delete = "DELETE
                    FROM
                        privilegios_de_conteudos
                    WHERE
                        conteudo_tabela='".$conteudo_tabela."' AND
                        conteudo_id='".$insert_id."'
                    ";

    mysql_query($sql_delete);

    
    /*
     * prepara o sql
     */


    $itens = $_POST['privid'];
    foreach($itens as $chave=>$valor){
        $embed_sql[] = "INSERT INTO
                    privilegios_de_conteudos
                    (privilegios_conf_id,conteudo_tabela,conteudo_id,adddate,autor)
                VALUES
                    ('$valor','$conteudo_tabela','$insert_id','".$_POST['frmadddate']."','".$_POST['frmautor']."')
                ";
    }
    foreach($embed_sql as $valor){
        mysql_query($valor);
    }


} else {
    //deleta privilégio anterior para fazer a atualização agora
    $sql_delete = "DELETE
                    FROM
                        privilegios_de_conteudos
                    WHERE
                        conteudo_tabela='".$conteudo_tabela."' AND
                        conteudo_id='".$insert_id."'
                    ";

    mysql_query($sql_delete);
}
?>
