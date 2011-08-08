<?php
$austNode = $_POST['node'];
$currentNode = (int) $_POST['selected'];
echo BuildDDList( Registry::read('austTable') ,'frmnode_id', User::getInstance()->tipo ,$austNode, $currentNode, false, true);
?>
