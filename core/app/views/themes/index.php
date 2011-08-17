<?php
if( !empty($_GET['current_theme']) ){
	if( is_dir(THEMES_DIR.$_GET['current_theme']) ){

		$params = array(
			'themeName' => $_GET['current_theme'],
			'userId' => User::getInstance()->getId(),
		);

		if( Themes::getInstance()->setTheme($params) ){
			header( 'Location: adm_main.php?section=themes&status=installed' );
			exit();
		}
	}
}

?>
<h2>
	Aparência
</h2>
<p>
	Personalize o seu gerenciador com os temas abaixo. Outros usuários não serão afetados com sua alteração.
</p>

<div id="themes">

	<?php
		$mensagem = array(
			'classe' => 'sucesso',
			'mensagem' => '<strong>Sucesso: </strong> Tema Instalado!',
		
		);

		if( !empty($_GET['status']) && $_GET['status'] == 'installed' ){
			EscreveBoxMensagem($mensagem);
		}
		$i = 0;
		foreach( Themes::getInstance()->getThemes() as $theme ){
			$i++;
			?>

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

			<?php
			if( $i >= 3 ){
				?>
				<br clear="all" />
				<?php
				$i = 0;
			}
		}

	?>
</div>
<br clear="all" />
<p>
	<a href="adm_main.php"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>



