<?php
/**
* Permissões de Usuários
*/

$conditions = (empty($_POST['id'])) ? array() : array('id' => $_POST['id']);

/**
 * Lê informações do usuário ou grupo selecionado, para que
 * quando for configurar uma permissão, associe ao respectivo ID
 */
if( !empty($_POST['id']) ){
    if( $_POST['tipo'] == 'userTipo'){
        $agente = $conexao->find(array(
                                    'table' => 'admins_tipos',
                                    'conditions' => array(
                                        'id' => $_POST['id'],
                                    ),
                                    'fields' => array('id', 'nome'),
                                ), 'all'
        );
    } else {
        $agente = $conexao->find(array(
                                    'table' => 'admins',
                                    'conditions' => array(
                                        'id' => $_POST['id'],
                                    ),
                                    'fields' => array('id', 'nome'),
                                ), 'all'
        );
    }
}

    //pr($agente);
/**
 * Se nenhuma ação deve ser tomada, escreve checkboxes
 *
 *
 */
if(empty($_GET['action'])){
    ?>
    <p>Permissões > <strong><?php echo $agente['0']['nome']; ?></strong></p>
    <?php

    $categorias = $conexao->find(array(
                                    'table' => 'categorias',
                                    'conditions' => array(
                                        //'id' => $_POST['id'],
                                        'classe' => 'estrutura',
                                    ),
                                    'fields' => array('id', 'nome', 'classe', 'tipo_legivel'),
                                ), 'all'
    );

    /**
     * CarregaPermissões
     */
    if($_POST['tipo'] == 'userTipo'){
        $permissoesCondition = array('admins_tipos_id' => $_POST['id']);
    } elseif($_POST['tipo'] == 'user'){
        $permissoesCondition = array('admins_id' => $_POST['id']);
    }
    $permissoes = $conexao->find(array(
                                    'table' => 'admins_permissions',
                                    'conditions' => $permissoesCondition,

                                    'fields' => array('categorias_id'),
                                ), 'all'
    );

    $categoriasChecked = array();
    foreach($permissoes as $valor){
        $categoriasChecked[] = $valor['categorias_id'];
    }


    foreach($categorias as $valor){

        /**
         * Se for estrutura, deixa negrito
         */
        if($valor['classe'] == 'estrutura'){
            echo '<strong>';
        }
        ?>
        <input type="checkbox" id="<?php echo $valor['nome']; ?>" <?php if(in_array($valor['id'], $categoriasChecked)) echo 'checked="true"'; ?> onchange="alteraPermissao('tipo=<?php echo $_POST['tipo']; ?>&agentid=<?php echo $agente['0']['id']; ?>&categoria=<?php echo $valor['id']; ?>', this)" value="<?php echo $valor['nome']; ?>" /> <?php echo $valor['nome']; ?> (<?php echo $valor['tipo_legivel']; ?>)<br />
        <?php
        if($valor['classe'] == 'estrutura'){
            echo '</strong>';
        }
    }
/**
 * 'ACTION == altera_permissao'
 *
 * Se é para alterar uma permissão
 *
 *
 *
 */
} elseif($_GET['action'] == 'altera_permissao'){

    /**
     * Cria permissão
     */
    if($_POST['value'] == 'true'){
        /**
         * Se for para uma categoria de usuários (ex.: Administradores, Moderadores, etc)
         */
        if($_POST['tipo'] == 'userTipo'){
            $agenteTipo = 'admins_tipos_id';
        /**
         * Permissão relacionada a um usuário
         */
        } elseif($_POST['tipo'] == 'user'){
            $agenteTipo = 'admins_id';
        }
        /**
         * Cria SQL
         */
        $sql = "INSERT INTO
                    admins_permissions
                    (".$agenteTipo.",categorias_id,tipo,adddate)
                VALUES
                    ('".$_POST['agentid']."','".$_POST['categoria']."','permit','".DataParaMySQL('horario')."')
                ";

    /**
     * Deleta Permissão
     */
    } else {
        /**
         * Se for para uma categoria de usuários (ex.: Administradores, Moderadores, etc)
         */
        if($_POST['tipo'] == 'userTipo'){
            $agenteTipo = 'admins_tipos_id';
        /**
         * Permissão relacionada a um usuário
         */
        } elseif($_POST['tipo'] == 'user'){
            $agenteTipo = 'admins_id';
        }

        $sql = "DELETE FROM
                    admins_permissions
                WHERE
                    ".$agenteTipo."='".$_POST['agentid']."' AND
                    categorias_id='".$_POST['categoria']."'
                ";
    }

    if($conexao->exec($sql)){
        echo '1';
    } else {
        echo '0';
    }
}

?>
