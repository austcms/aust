<div class="title_column">
	<h2>
		Editar Hook
	</h2>
</div>

<?php
$hook = new Hook();
$data = $hook->find($_GET["id"]);
$data = reset($data);

include(INC_DIR."hooks.inc/_form.inc.php");
?>
