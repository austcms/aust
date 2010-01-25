<?php
	

if(!empty($_GET['action'])){
    include('admins.inc/'.$_GET['action'].'.inc.php');
} else {

    ?>
    <h2>Usuários</h2>
    <p>
        Nesta página você pode gerenciar todos os usuários que administram o site.
    </p>
    <p>
        Selecione abaixo o que você deseja fazer:
    </p>
    <div class="action_options">
        <ul style="list-style: none;">
        <li>
            <a href="adm_main.php?section=admins&action=form&fm=criar" style="text-decoration: none;"><img src="img/layoutv1/adicionar.jpg" border="0" /></a>
            <a href="adm_main.php?section=admins&action=form&fm=criar">Cadastrar um novo usuário</a>
        </li>
        <li>
            <a href="adm_main.php?section=admins&action=form&fm=editar" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" border="0" /></a>
            <a href="adm_main.php?section=admins&action=form&fm=editar">Editar suas próprias informações</a>
        </li>
        <li class="sem_icone">
            <a href="adm_main.php?section=admins&action=passw">Minha senha</a>
        </li>
        <li>
            <a href="adm_main.php?section=admins&action=listar" style="text-decoration: none;"><img src="img/layoutv1/list.jpg" border="0" /></a>
            <a href="adm_main.php?section=admins&action=listar">Listar e editar os usuários cadastrados</a>
        </li>
        </ul>

    </div>
    <?php
}
?>
<h2 style="margin-top: 20px;">
    Permissões de Usuários
</h2>
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

<div class="widget_group">

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
    <div class="widget">
        <div class="titulo">
            <h3>Tipos de usuários</h3>
        </div>
        <div class="content">
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
        <div class="footer"></div>
    </div>


    <?php
    /*
     * Listagem de usuários
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Usuários cadastrados</h3>
        </div>
        <div class="content">
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
        <div class="footer"></div>
    </div>
</div>



<div class="widget_group">
    <div class="widget">
        <div class="titulo">
            <h3>Permissões atuais</h3>
        </div>
        <div class="content">
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
        <div class="footer"></div>
    </div>
</div>
	
