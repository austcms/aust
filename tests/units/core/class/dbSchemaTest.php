<?php
// require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/class/dbSchema.php';

#####################################

class dbSchemaTest extends PHPUnit_Framework_TestCase
{
    function setUp(){
    }

	function fixture(){
        $schema['st_agenda'] = array(
            "id" => "int auto_increment",
            "categoria_id" => "int",
            "title" => "text COMMENT 'O título do evento.'",
            "description" => "text COMMENT 'Descrição do evento.'",
            "occurs_all_day" => "smallint COMMENT '1 se dura todo o dia.'",
            "actor_is_user" => "smallint",
            "actor_admin_id" => "int",
            "actor_admin_name" => "varchar(200)",
            "start_datetime" => "datetime",
            "end_datetime" => "datetime",
            "created_on" => "datetime",
            "updated_on" => "datetime",
            "admin_id" => "int",
            "dbSchemaTableProperties" => array(
                "PRIMARY KEY" => "(id)",
                "UNIQUE" => "id (id)",
            )
        );
		return $schema;
	}
	
    function tearDown(){
        $dbSchema = false;
    }

	function testSetSchema(){
        $dbSchema = new dbSchema();
		$this->assertTrue( $dbSchema->setSchema($this->fixture()) );
		$this->assertFalse( $dbSchema->setSchema(false) );
	}
	
    function testSchemaIsSet(){
        $dbSchema = new dbSchema();
		$dbSchema->setSchema($this->fixture());
		
        $this->assertType('array', $dbSchema->dbSchema );
    }

    function testVerificaSchemaExistente(){
        $dbSchema = new dbSchema();
		$dbSchema->setSchema($this->fixture());
        
		$this->assertType('integer', $dbSchema->verificaSchema() );
    }

    function testTabelasAtuais(){
        $dbSchema = new dbSchema();
		$dbSchema->setSchema($this->fixture());
		
        $this->assertType('array', $dbSchema->tabelasAtuais());
    }

    function testIsDbSchemaFormatOk(){
        $dbSchema = new dbSchema();
		
        $this->assertTrue($dbSchema->isDbSchemaFormatOk($this->fixture()));
        $this->assertFalse($dbSchema->isDbSchemaFormatOk('blabla'));
    }

    function testSql(){
        $dbSchema = new dbSchema();
		$fixture = $this->fixture();
		$sql = $dbSchema->sql($fixture);
		$sql = reset($sql);
        $this->assertRegExp('/CREATE TABLE st_agenda .* evento\.\', description text COMMENT/', $sql );
    }

}
?>