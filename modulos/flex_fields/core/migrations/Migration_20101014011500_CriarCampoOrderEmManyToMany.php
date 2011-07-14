<?php
/**
 * MOD MIGRATION
 * 
 * As tabelas relacionais Many-to-Many devem ter o campo order, mas em versões
 * anteriores não existia. Este migration ajusta isso.
 * 
 */
class Migration_20101014011500_CriarCampoOrderEmManyToMany extends Migrations
{
    function up(){

		$sql = "SELECT *
				FROM cadastros_conf
				WHERE 
					tipo='campo' AND
					(
						especie='relacional_umparamuitos' OR
						especie='relational_onetomany'
					)
				";
		
		$tables = Connection::getInstance()->query($sql);
		
		foreach( $tables as $table ){
	        $schema = array(
	            'table' => $table['referencia'],
	            'field' => 'order_nr',
	            'type' => 'int',
	            'position' => 'AFTER updated_on'
	        );
        	$this->addField($schema);
		}

        return true;
    }

    function down(){

		$sql = "SELECT *
				FROM cadastros_conf
				WHERE 
					tipo='campo' AND
					(
						especie='relacional_umparamuitos' OR
						especie='relational_onetomany'
					)
				";
		
		$tables = Connection::getInstance()->query($sql);
		
		foreach( $tables as $table ){
	        $this->dropField($table['referencia'], 'order_nr');
		}

        return true;
    }

}
?>