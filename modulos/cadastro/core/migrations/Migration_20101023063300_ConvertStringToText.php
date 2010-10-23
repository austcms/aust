<?php
/**
 * MOD MIGRATION
 * 
 * As tabelas relacionais Many-to-Many devem ter o campo order, mas em versões
 * anteriores não existia. Este migration ajusta isso.
 * 
 */
class Migration_20101023063300_ConvertStringToText extends Migrations
{
    function up(){

		$sql = "SELECT categorias_id, chave, id
				FROM cadastros_conf
				WHERE 
					tipo='campo' AND
					(
						especie='string'
					)
				";
		
		$fields = $this->connection->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
			$sql = "SELECT valor
					FROM cadastros_conf
					WHERE 
						tipo='estrutura' AND
						(
							chave='tabela'
						)
					";
			$tableName = reset( $this->connection->query($sql) );
			$tableName = $tableName['valor'];
			
			$describe = $this->connection->describeTable($tableName, true);
			
			if( $describe[$field['chave']]['Type'] == 'text' ){
				$sqls[] = "UPDATE cadastros_conf SET especie='text' WHERE id='".$field['id']."'";
			}
			
		}

		foreach( $sqls as $sql ){
			$this->connection->exec($sql);
		}
		
        return true;
    }

    function down(){

		$sql = "SELECT categorias_id, chave, id
				FROM cadastros_conf
				WHERE 
					tipo='campo' AND
					(
						especie='text'
					)
				";
		
		$fields = $this->connection->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
			$sql = "SELECT valor
					FROM cadastros_conf
					WHERE 
						tipo='estrutura' AND
						(
							chave='tabela'
						)
					";
			$tableName = reset( $this->connection->query($sql) );
			$tableName = $tableName['valor'];
			
			$describe = $this->connection->describeTable($tableName, true);
			
			if( $describe[$field['chave']]['Type'] == 'varchar(250)' ){
				$sqls[] = "UPDATE cadastros_conf SET especie='string' WHERE id='".$field['id']."'";
			}
			
		}
		
		foreach( $sqls as $sql ){
			$this->connection->exec($sql);
		}
        return true;
    }

}
?>