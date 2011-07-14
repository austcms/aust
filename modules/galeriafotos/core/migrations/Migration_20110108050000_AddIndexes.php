<?php
class Migration_20110108050000_AddIndexes extends Migrations
{
    function up(){
		$sql = "CREATE INDEX ordem_idx ON galeria_fotos_imagens (ordem)";
		Connection::getInstance()->exec($sql);
		$sql = "CREATE INDEX galeria_id_idx ON galeria_fotos_imagens (galeria_foto_id)";
		Connection::getInstance()->exec($sql);
		
		$sql = "ALTER TABLE galeria_fotos_imagens ENGINE = InnoDB";
		Connection::getInstance()->exec($sql);
		
		return true;
	}
	
	function down(){
		
		return true;
	}
}
?>