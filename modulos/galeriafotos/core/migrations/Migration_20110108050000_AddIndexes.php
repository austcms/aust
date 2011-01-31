<?php
class Migration_20110108050000_AddIndexes extends Migrations
{
    function up(){
		$sql = "CREATE INDEX ordem_idx ON galeria_fotos_imagens (ordem)";
		$this->connection->exec($sql);
		$sql = "CREATE INDEX galeria_id_idx ON galeria_fotos_imagens (galeria_foto_id)";
		$this->connection->exec($sql);
		
		$sql = "ALTER TABLE galeria_fotos_imagens ENGINE = InnoDB";
		$this->connection->exec($sql);
		
		return true;
	}
	
	function down(){
		
		return true;
	}
}
?>