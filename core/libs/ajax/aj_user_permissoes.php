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
 *
 * CHECKBOXES PARA DEFINIÇÃO DE PERMISSÕES
 *
 * Se nenhuma ação deve ser tomada, escreve checkboxes.
 *
 * Mostra as actions possíveis em cada estrutura (create, edit, listing, etc).
 * Lista as estruturas e abaixo os actions.
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

                                    'fields' => array('categorias_id', 'action'),
                                ), 'all'
    );

    /*
     * Define os actions principais
     */
    $actions = Registry::read('default_actions');

    $categoriasChecked = array();
    foreach($permissoes as $valor){
        if( !empty($valor['action']) )
            $categoriasChecked[ $valor['categorias_id'] ][$valor['action']] = true;
    }

    //pr($categoriasChecked);

    /*
     * HelperFunction
     *
     * Escreve 'checked' nos devidos actions a seguir
     */
    function isCheckedPermission($structure, $action){
        global $categoriasChecked;

        if( empty($categoriasChecked[$structure]) )
            return false;

        if( !empty($categoriasChecked[$structure][$action])
            AND $categoriasChecked[$structure][$action] == true )
        {
            return 'checked="true"';
        }
        return false;

    }

    foreach($categorias as $valor){

        /**
         * Se for estrutura, deixa negrito
         */
        ?>
        <div class="structure">
        <div class="title">
            <?php echo $valor['nome']; ?>
        </div>
        <div class="actions">
            <?php
            /*
             *
             * ACTIONS POSSÍVEIS
             *
             * Lista os actions (create,edit, listing, etc)
             * com um checkbox em cada um.
             * 
             */
            foreach( $actions as $action_name=>$action ){
                ?>
                <input
                    type="checkbox"
                    id="<?php echo $valor['nome'].'_'.$action; ?>"
                     <?php echo isCheckedPermission($valor['id'], $action) ?>
                    onchange="alteraPermissao('tipo=<?php echo $_POST['tipo']; ?>&agentid=<?php echo $agente['0']['id']; ?>&categoria=<?php echo $valor['id']; ?>&action=<?php echo $action; ?>', this)"
                    value="<?php echo $valor['nome']; ?>" />
                    <?php echo $action_name; ?>
                <?php
            }
            ?>
        </div>
        </div>
        <?php
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

    pr($_POST);
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
                    (".$agenteTipo.",categorias_id,tipo,adddate,action)
                VALUES
                    ('".$_POST['agentid']."','".$_POST['categoria']."','permit','".DataParaMySQL('horario')."', '".$_POST['action']."')
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
                    categorias_id='".$_POST['categoria']."' AND
                    action='".$_POST['action']."'
                ";
    }

    if($conexao->exec($sql)){
        echo '1';
    } else {
        echo '0';
    }
}

?>
