<?php
class Migration_20101228045200_ChangeOrdemToInt extends Migrations
{
    function up(){

		$sql = "ALTER TABLE textos CHANGE ordem ordem INT(11) DEFAULT '5' NOT NULL";
		$this->connection->exec($sql);
        return true;
    }
    
    function down(){
		$sql = "ALTER TABLE textos CHANGE ordem ordem varchar(50) DEFAULT '5' NOT NULL";
		$this->connection->exec($sql);
        return true;
    }
}
?>
