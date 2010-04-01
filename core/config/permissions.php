<?php
/**
 * Permissões de acesso a estruturas
 *
 * Carrega permissões do Usuário atual durante a inicialização
 *
 * @package Loading
 * @name Permissões
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */

/**
 * Carrega permissões do usuário atual
 */
$params = array(
    'admins_tipos_id' => $administrador->LeRegistro('tipoid'),
    'admins_id' => $administrador->LeRegistro('id'),
    'conexao' => $conexao,
    /*
     * Que tipos de usuários podem acessar Widgets?
     */
    'widgets_viewable' => array(
        "Webmaster", "Root", "Administrador", "Moderador", "Colaboradores"
    )

);



$permissoes = new StructurePermissions($params);
$categoriasPermitidas = $permissoes->read($params);

?>
