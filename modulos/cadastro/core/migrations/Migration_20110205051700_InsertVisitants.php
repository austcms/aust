<?php
/**
 * MOD MIGRATION
 * 
 * Insere campo visitants em tabelas antigas de cadastro
 * 
 */
class Migration_20110205051700_InsertVisitants extends Migrations
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

		$fields = $this->connection->query($sql);
		foreach( $fields as $field ){
	        $schema = array(
	            'table' => $field['valor'],
	            'field' => 'visitants',
	            'type' => 'int',
	            'position' => 'AFTER updated_on',
				'default' => '0',
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
		
		$fields = $this->connection->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
	        $this->dropField($field['valor'], 'visitants');
		}

        return true;
    }

}
?>