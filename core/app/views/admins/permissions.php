<?php
/* 
 * Arquivo contendo a interface de usuário para configuração de permissões
 */



?>
<span class="permissoes_page">
<h2>
	Permissões de Usuários
</h2>
<p>
	Nesta tela você pode dar permissões especiais a usuários e definir
	"quem edita o que". Somente administradores têm acesso a esta tela.
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
	é administrador, poderá editar somente moderadores e colaboradores).
</p>
<p>
	À direita, em <strong>Permissões Atuais</strong>, você encontra as seções do gerenciador. Basta marcar quais
	conteúdos o usuário pode editar. Se nenhum estiver selecionado, o usuário
	pode editar todas.
</p>
<p>
	<strong>Exemplo:</strong> João é moderador e suponhamos que temos Notícias e Artigos.
	Se você configurar que João só poderá editar Notícias,
	ele não acessará nada mais. Entretanto, se após isto você configurar que moderadores podem acessar
	somente Artigos, João acessará Artigos (pois ele é um moderador) e aquilo ao qual ele tem permissão,
	Notícias. Para que João passe a acessar somente o que moderadores podem acessar, desmarque todas
	as opções, assim ele passará a obedecer as regras impostas aos moderadores.
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
				if(User::getInstance()->LeRegistro('group') == 'Webmaster'){
					$adminsTiposCarregar = array('Webmaster');
				} else {
					$adminsTiposCarregar = array('Webmaster', 'Administrador');
				}

				/**
				 * Lista os tipos de usuários
				 */
				$adminsTipos = Connection::getInstance()->find(array(
											'table' => 'admin_groups',
											'conditions' => array(
												'NOT' => array(
													'name' => $adminsTiposCarregar,
												),
											),
											'fields' => array('id', 'name'),
										), 'all'
				);
				foreach($adminsTipos as $value){
					?>
					<input type="radio" name="agente" onclick="javascript: mostraPermissoes('<?php echo $value['id']; ?>','userTipo')" id="<?php echo $value['name']; ?>" name="<?php echo $value['id']?>" /> <?php echo $value['name']; ?><br />
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
				/**
				 * Lista os tipos de usuários
				 */
				$adminsTipos = Connection::getInstance()->find(
										array(
											'table' => 'admins',
											'join' => array(
												'LEFT JOIN admin_groups ON admins.admin_group_id=admin_groups.id',
											),
											
											'conditions' => array(
												'NOT' => array(
													'admins.id' => User::getInstance()->LeRegistro('id'),
													'admin_groups.name' => 'Webmaster'
												),
											),
											'fields' => array('admins.id', 'admins.name'),
											'order' => array('admin_groups.name ASC', 'admins.name ASC')
										), 'all'
				);
				foreach($adminsTipos as $value){
					?>
					<input type="radio" name="agente" onclick="mostraPermissoes('<?php echo $value['id']; ?>','user')" id="<?php echo $value['name']; ?>" name="<?php echo $value['id']?>" /> <?php echo $value['name']; ?><br />
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
</span>