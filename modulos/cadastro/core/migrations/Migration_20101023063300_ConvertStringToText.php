<?php
/**
 * MOD MIGRATION
 * 
 * Ajusta para text o tipo que antes chamava-se string
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
		
		$fields = Connection::getInstance()->query($sql);
		
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
			$tableName = reset( Connection::getInstance()->query($sql) );
			$tableName = $tableName['valor'];
			
			$describe = Connection::getInstance()->describeTable($tableName, true);
			
			if( $describe[$field['chave']]['Type'] == 'text' ){
				$sqls[] = "UPDATE cadastros_conf SET especie='text' WHERE id='".$field['id']."'";
			}
			
		}

		foreach( $sqls as $sql ){
			Connection::getInstance()->exec($sql);
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
		
		$fields = Connection::getInstance()->query($sql);
		
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
			$tableName = reset( Connection::getInstance()->query($sql) );
			$tableName = $tableName['valor'];
			
			$describe = Connection::getInstance()->describeTable($tableName, true);
			
			if( $describe[$field['chave']]['Type'] == 'varchar(250)' ){
				$sqls[] = "UPDATE cadastros_conf SET especie='string' WHERE id='".$field['id']."'";
			}
			
		}
		
		foreach( $sqls as $sql ){
			Connection::getInstance()->exec($sql);
		}
        return true;
    }

}
?>