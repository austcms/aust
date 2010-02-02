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

<br />
<h2>
    Permissões de Usuários
</h2>
<p>
    Nesta tela você pode dar permissões especiais a usuários e definir
    "quem edita o que". Somente administradores têm acesso a esta tela.
</p>
<a href="adm_main.php?section=permissoes">Editar permissões</a>
    <?php
}
?>