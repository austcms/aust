<?php
/* 
 * Arquivo contendo a interface de usuário para configuração de permissões
 */



?>
<h1>
    Permissões de Usuários
</h1>
<p>
    Nesta tela você pode dar permissões especiais a usuários e definir
    "quem edita o que". Somente administradores têm acesso a estas opções.
</p>
<h2>
    Como funciona
</h2>
<p>
    À esquerda, em <strong>Tipos de Usuários</strong> e
    <strong>Usuários Cadastrados</strong>,
    selecione quem você quer dar permissões. Pode ser um grupo de
    usuários (moderadores, colaboradores) ou um usuário cadastrado específico.
    Você só pode configurar quem está abaixo de você na hierarquia (se você
    é administrar, poderá editar somente moderadores e colaboradores).
</p>

<p>
    À direita, em <strong>Permissões Atuais</strong>, você encontra as seções do gerenciador. Basta marcar quais
    conteúdos o usuário pode editar. Se nenhum estiver selecionado, o usuário
    pode editar todas.
</p>

<div class="painel-metade">

    <?php
    /**
     * Formulário de configuração de permissões
     */

    ?>

    <?php
    /*
     * LISTAGEM DAS ESTRUTURAS CRIADAS
     */
    ?>
    <div class="painel">
        <div class="titulo">
            <h2>Tipos de usuários</h2>
        </div>
        <div class="corpo">
            <p>Os tipos de usuários presentes.</p>
            <ul>
            <?php
                /**
                 * Se Webmaster, pode editar configurações de Administradores
                 */
                if($administrador->LeRegistro('tipo') == 'Webmaster'){
                    $adminsTiposCarregar = array('Webmaster');
                } else {
                    $adminsTiposCarregar = array('Webmaster', 'Administrador');
                }

                /**
                 * Lista os tipos de usuários
                 */
                $adminsTipos = $conexao->find(array(
                                            'table' => 'admins_tipos',
                                            'conditions' => array(
                                                'NOT' => array(
                                                    'nome' => $adminsTiposCarregar,
                                                ),
                                            ),
                                            'fields' => array('id', 'nome'),
                                        ), 'all'
                );
                foreach($adminsTipos as $valor){
                    ?>
                    <input type="radio" name="agente" onchange="mostraPermissoes('<?php echo $valor['id']; ?>','userTipo')" id="<?php echo $valor['nome']; ?>" name="<?php echo $valor['id']?>" /> <?php echo $valor['nome']; ?><br />
                    <?php
                }
            ?>
            </ul>
        </div>
        <div class="rodape"></div>
    </div>


    <?php
    /*
     * Listagem de usuários
     */
    ?>
    <div class="painel">
        <div class="titulo">
            <h2>Usuários cadastrados</h2>
        </div>
        <div class="corpo">
            <p>
                
            </p>
            <ul>
            <?php
            //echo $administrador->LeRegistro('id');
                /**
                 * Lista os tipos de usuários
                 */
                $adminsTipos = $conexao->find(
                                        array(
                                            'table' => 'admins',
                                            'join' => array(
                                                'LEFT JOIN admins_tipos ON admins.tipo=admins_tipos.id',
                                            ),
                                            
                                            'conditions' => array(
                                                'NOT' => array(
                                                    'admins.id' => $administrador->LeRegistro('id'),
                                                    'admins_tipos.nome' => 'Webmaster'
                                                ),
                                            ),
                                            'fields' => array('admins.id', 'admins.nome'),
                                            'order' => array('admins_tipos.nome ASC', 'admins.nome ASC')
                                        ), 'all'
                );
                foreach($adminsTipos as $valor){
                    ?>
                    <input type="radio" name="agente" onchange="mostraPermissoes('<?php echo $valor['id']; ?>','user')" id="<?php echo $valor['nome']; ?>" name="<?php echo $valor['id']?>" /> <?php echo $valor['nome']; ?><br />
                    <?php
                }
            ?>
            </ul>
        </div>
        <div class="rodape"></div>
    </div>
</div>



<div class="painel-metade painel-dois">
<div class="painel">
    <div class="titulo">
        <h2>Permissões atuais</h2>
    </div>
    <div class="corpo">
        <div id="permissoesAtuais">
        <p>
            Selecione ao lado:
        </p>
        <p style="text-align: center;">
            <br />
            um tipo de usuário
            <br />
            <strong>ou</strong>
            <br />
            um usuário
        </p>
        <?php



        ?>
        </div>


    </div>
    <div class="rodape"></div>
</div>
</div>