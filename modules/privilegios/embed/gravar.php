<?php
/* 
 * EMBED -> gravar
 */

//pr($_POST);

$conteudo_tabela = $_POST["contentTable"];

/*
 * Se $_post[w] está vazio, é um novo conteúdo
 */
if(empty($_POST['w']) AND $_POST['metodo'] == 'criar'){
    /*
     * $insert_id pega o último id inserido do conteúdo principal. Não é
     * seguro pegar este valor aqui, mas sim que o conteúdo
     * principal já tenha salvo o valor em $_POST['w']
     */
    $insert_id = $this->module->connection->lastInsertId();
} elseif(!empty($_POST['w'])) {
    $insert_id = $_POST['w'];
}



// se foi clicado algum item no form de inclusão
if(is_array($_POST['privid'])){
    //echo 'delete';
    //deleta privilégio anterior para fazer a atualização agora
    $sql_delete = "DELETE
                    FROM
                        privilegio_target
                    WHERE
                        target_table='".$conteudo_tabela."' AND
                        target_id='".$insert_id."'
                    ";

    //pr($sql_delete);
    $module->connection->exec($sql_delete);

    
    /*
     * prepara o sql
     */


    $itens = $_POST['privid'];
    foreach($itens as $chave=>$valor){
        $embed_sql[] = "INSERT INTO
                    privilegio_target
                    (privilegio_id, target_table,target_id, created_on, admin_id, type)
                VALUES
                    ('$valor','$conteudo_tabela','$insert_id','".date("Y-m-d")."','".$_POST['frmautor']."', 'content')
                ";
        
    }
    //pr($embed_sql);

    foreach($embed_sql as $valor){
        $module->connection->exec($valor);
    }


} else {
    //deleta privilégio anterior para fazer a atualização agora
    $sql_delete = "DELETE
                    FROM
                        privilegio_target
                    WHERE
                        target_table='".$conteudo_tabela."' AND
                        target_id='".$insert_id."'
                    ";

    $module->connection->exec($sql_delete);
}
?>
