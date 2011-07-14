<?php
class Migration_20110616145900_AddContentIdField extends Migrations
{
    function up(){

        $schema = array(
            'table' => 'galeria_fotos_imagens',
            'field' => 'content_id',
            'type' => "int",
            'position' => 'AFTER galeria_foto_id'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'galeria_fotos_imagens',
            'field' => 'category_id',
            'type' => "int",
            'position' => 'AFTER id'
        );
        $this->addField($schema);
		
		return true;
	}
	
	function down(){
        $this->dropField('galeria_fotos_imagens', 'content_id');
		
		return true;
	}
}
?>