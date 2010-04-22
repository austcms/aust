<?php
/*
 * CONFIGURAÇÕES INTERNAS
 */

/*
 * Diretórios com Permissão de Acesso Necessária
 */
    Registry::write('permission_needed_dirs',
            array(
                CACHE_DIR,
                CACHE_PUBLIC_DIR,
                CACHE_CSS_CONTENT,
                CACHE_JS_CONTENT,
            ));

/*
 * Actions padrão de Módulos
 */
    Registry::write('default_actions',
            array(
                'Criar' => 'create',
                'Listar' => 'listing',
                'Editar' => 'edit',
            ));

/*
 * Tema padrão (quando nenhum outro está selecionado)
 */
    Registry::write('defaultTheme','classic_blue')

?>
