<div class="title_column">
	<h2>
		Hooks
	</h2>
</div>

<?php
$hooksObject = new Hook();
$hook = $hooksObject->instantiateHookEngine($_GET["hook_engine"]);

include(INC_DIR."hooks.inc/_form.inc.php")
?>
