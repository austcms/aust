<?php
if( $_GET['lib'] == 'show_sql_debug_messages' && isset($_POST["show"]) ){
	$sql = "UPDATE ".Config::getInstance()->table." SET value='".$_POST["show"]."' WHERE property='show_sql_debug_messages'";
	Connection::getInstance()->exec($sql);
}
?>