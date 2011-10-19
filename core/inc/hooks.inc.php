<div id="structure_configuration">
	<?php
	if( empty($_GET['action']) )
		$_GET['action'] = 'index';
	
	include(INC_DIR."hooks.inc/".$_GET['action'].".inc.php");
	?>
	<br />
</div>