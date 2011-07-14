<?php
/**
 * MOD MIGRATION
 * 
 * Insere campo node_id em tabelas antigas de cadastro
 * 
 */
class Migration_20101130114000_InsertNodeId extends Migrations
{
    function up(){

		$sql = "SELECT categorias_id, valor, id
				FROM cadastros_conf
				WHERE 
					tipo='estrutura' AND
					(
						chave='tabela'
					)
				";

		$fields = Connection::getInstance()->query($sql);
		foreach( $fields as $field ){
	        $schema = array(
	            'table' => $field['valor'],
	            'field' => 'node_id',
	            'type' => 'int',
	            'position' => 'AFTER id'
	        );
        	$this->addField($schema);
		}
		
        return true;
    }

    function down(){

		$sql = "SELECT categorias_id, valor, id
				FROM cadastros_conf
				WHERE 
					tipo='estrutura' AND
					(
						chave='tabela'
					)
				";
		
		$fields = Connection::getInstance()->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
	        $this->dropField($field['valor'], 'node_id');
		}

        return true;
    }

}
?>