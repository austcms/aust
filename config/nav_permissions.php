<?php
/* 
 * Arquivo que contém as permissões de acesso e navegação
 *
 * 'au-permissao' significa que ninguém tem acesso, exceto quem estiver
 * especificado.
 */

$navPermissoes = Array(
                    'categorias' =>
                                Array('new' =>
                                        Array('Webmaster', 'Administrador')),
                    'conf_modulos' =>
                                Array('au-permissao' =>
                                        Array('Webmaster'))

    );

$configPermissoes = array(
    'Geral' => '*',
);

?>