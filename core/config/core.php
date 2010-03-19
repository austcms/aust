<?php
/*
 * CONFIGURAÇÕES INTERNAS
 */

Registry::write('permission_needed_dirs',
        array(
            CACHE_DIR,
            CACHE_PUBLIC_DIR,
            CACHE_CSS_CONTENT,
            CACHE_JS_CONTENT,
        ));

Registry::write('default_actions',
        array(
            'Criar' => 'create',
            'Listar' => 'listing',
            'Editar' => 'edit',
        ));
?>
