<?php
if( $_GET['lib'] == 'show_sql_debug_messages' && isset($_POST["show"]) ){
	$sql = "UPDATE config SET valor='".$_POST["show"]."' WHERE propriedade='show_sql_debug_messages'";
    Connection::getInstance()->exec($sql);
}
?>