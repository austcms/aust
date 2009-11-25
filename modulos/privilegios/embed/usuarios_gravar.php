<?php
/* 
 * EMBED -> gravar
 */

// se foi clicado algum item no form de inclusão
if(is_array($_POST['privid'])){

    $usuario_tabela = $modulo->LeTabelaDaEstrutura($_GET['aust_node']);
    // Se $_post[w] está vazio, é um novo conteúdo
    if(empty($_POST['w']) AND $_POST['metodo'] == 'criar'){
        $insert_id = mysql_insert_id();
    } elseif(!empty($_POST['w'])) {
        $insert_id = $_POST['w'];
    }

    //deleta privilégio anterior para fazer a atualização agora
    $sql_delete = "DELETE
                    FROM
                        privilegios_de_usuarios
                    WHERE
                        usuario_tabela='".$usuario_tabela."' AND
                        usuario_id='".$insert_id."'
                    ";
    mysql_query($sql_delete);

    
    /*
     * prepara o sql
     */


    $itens = $_POST['privid'];
    foreach($itens as $chave=>$valor){
        $embed_sql[] = "INSERT INTO
                    privilegios_de_usuarios
                    (privilegios_conf_id,usuario_tabela,usuario_id,adddate,autor)
                VALUES
                    ('$valor','$usuario_tabela','$insert_id','".$_POST['frmadddate']."','".$_POST['frmautor']."')
                ";
    }
    foreach($embed_sql as $valor){
        mysql_query($valor);
    }


}
?>
