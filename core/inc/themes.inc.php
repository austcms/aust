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
    foreach( $themes->getThemes() as $theme ){
    ?>

    <div id="themes">
        <div class="theme">

            <div class="theme_name">
                <?php echo $theme['name']; ?>
            </div>

            <div class="screenshot">
                <?php echo '<img src="'.$theme['screenshotFile'].'" />'; ?>
            </div>
        </div>
    </div>

    <?php
    }

?>


