<?php
/* 
 * Arquivo contendo a interface de usuário para configuração de permissões
 */

$widgets = new Widgets($envParams, $administrador->getId());

if( !empty($_GET['i']) ){
    if( is_dir(WIDGETS_DIR.'dashboard/'.$_GET['i']) ){

        $params = array(
            'name' => $_GET['i'],
            'admin_id' => $administrador->getId(),
            'column_nr' => $_GET['column_nr'],
            'path' => 'dashboard/'.$_GET['i'],
        );

        if( $widgets->installWidget($params) ){
            header( 'Location: adm_main.php' );
            exit();
        }
    }
}

$c = $widgets->getInstalledWidgets($params);

?>
<h2>
    Temas
</h2>

<?php

//pr($themes->getThemes());
    foreach( $themes->getThemes() as $theme ){
        //include($theme['path'].'/info.php');
        echo '<img src="'.$theme['screenshotFile'].'" />';
        echo $theme['name'];
    }

?>