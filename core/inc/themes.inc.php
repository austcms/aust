<?php
if( !empty($_GET['current_theme']) ){
    if( is_dir(THEMES_DIR.$_GET['current_theme']) ){

        $params = array(
            'themeName' => $_GET['current_theme'],
            'userId' => $administrador->getId(),
        );

        if( $themes->setTheme($params) ){
            header( 'Location: adm_main.php?section=themes&status=installed' );
            exit();
        }
    }
}

//$c = $widgets->getInstalledWidgets($params);

?>
<h2>
    Temas
</h2>

<?php
    $mensagem = array(
        'classe' => 'sucesso',
        'mensagem' => '<strong>Sucesso: </strong> Tema Instalado!',
        
    );
    $mensagem_erro = array(
        'classe' => 'insucesso',
        'mensagem' => '<strong>Erro: </strong> Ocorreu um erro desconhecido. Tente novamente. '.
            'Se o problema prosseguir, contacte um administrador.',
    );


    if($_GET['status'] == 'installed' ){
        EscreveBoxMensagem($mensagem);
    }else{
        EscreveBoxMensagem($mensagem_erro);
    }
    foreach( $themes->getThemes() as $theme ){
    ?>

    <div id="themes">
        <div class="theme">

            <div class="theme_name">
                <h3> <?php echo $theme['name']; ?> </h3>
            </div>

            <div class="screenshot">
                <?php echo '<img src="'.$theme['screenshotFile'].'" />'; ?>
            </div>
            <div class="instalar">
                <a href="adm_main.php?section=themes&current_theme=<?php echo $theme['themeName']?>"></a>
            </div>
        </div>
    </div>

    <?php
    }

?>


