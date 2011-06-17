<?php
/* 
 * Arquivo que contém as permissões de acesso e navegação
 *
 * 'au-permissao' significa que ninguém tem acesso, exceto quem estiver
 * especificado.
 */
$navPermissoes = array(
                    'categorias' =>
                        array('new' =>
                            array(
                                'Webmaster', 'Administrador'
                            )
                        ),
                    'admins' =>
                        array('form' =>
                            array(
                                'Webmaster',
                                'Administrador',
                                //'Moderador'
                            )
                        ),
                        array('au-permissao' =>
                            array(
                                'Webmaster',
                                'Administrador',
                                //'Moderador'
                            )
                        ),
                    'conf_modulos' =>
                        array('au-permissao' =>
                            array('Webmaster')
                        )

    );

$configPermissoes = array(
    'Geral' => '*',
);

class NAVIGATION_PERMISSIONS_CONFIGURATIONS {
    
    static $navegation = array(
        'categorias' =>
            array('new' =>
                array(
                    'Webmaster', 'Administrador'
                )
            ),
        'admins' => array(
            'form' => array(
                'Webmaster',
                'Administrador',
                //'Moderador'
            )
        ),
        array(
            'au-permissao' => array(
                'Webmaster',
                'Administrador',
                //'Moderador'
            )
        ),
        'conf_modulos' => array(
            'au-permissao' => array(
                'Webmaster'
            )
        ),
    );

    static $configurations = array(
        'Geral' => '*'
    );

    static $widgets = array(
        'Webmaster', 'Root', 'Administrador', 'Moderador', 'Colaborador'
    );
}


?>