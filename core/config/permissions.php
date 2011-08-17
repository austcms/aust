<?php
/**
 * Permissões de acesso a estruturas
 *
 * Carrega permissões do Usuário atual durante a inicialização
 *
 * @since v0.1.5, 30/05/2009
 */

/**
 * Carrega permissões do usuário atual
 */
$param = array(
	'admin_group_id' => User::getInstance()->LeRegistro('group_id'),
	'admins_id' => User::getInstance()->LeRegistro('id'),
	'conexao' => Connection::getInstance(),
	/*
	 * Que tipos de usuários podem acessar Widgets?
	 */
	'widgets_viewable' => array(
		"Webmaster", "Root", "Administrador", "Moderador", "Colaboradores"
	)

);



#$permissoes = StructurePermissions::getInstance();
#$categoriasPermitidas = StructurePermissions::getInstance()->read($params);

?>
