<?php
/* 
 * Arquivo contendo a interface de usuário para configuração de permissões
 */

$widgets = new Widgets($envParams, User::getInstance()->getId());

if( !empty($_GET['i']) ){
	if( is_dir(WIDGETS_DIR.'dashboard/'.$_GET['i']) ){

		$params = array(
			'name' => $_GET['i'],
			'admin_id' => User::getInstance()->getId(),
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
	Componentes do Painel
</h2>
<p>
	Componentes do Painel (ou Widgets) são pedaços de código que você pode
	instalar na página principal do gerenciador para ajudá-lo a administrar
	melhor o seu site.
</p>
<?php
$allWidgets = $widgets->getWidgets();

?>
<ul>
	<?php
	foreach($allWidgets as $basename=>$widget){
		?>
		<li>
			<?php echo $widget['title'] ?>:
			<?php echo $widget['description'] ?>
			<br />
			<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&column_nr=<?php echo $_GET['column_nr'] ?>&i=<?php echo $basename ?>">Instalar agora</a>
			<br />
			<br />
		</li>
		<?php
	}
	?>
</ul>