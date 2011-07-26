<?php

require_once 'tests/config/auto_include.php';
require_once 'config/nav_permissions.php';

class ImageTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $connection;

	public $images = array(
		
	);

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        
        
        $this->obj = Image::getInstance();
    }

    function testValidation(){
		
		$params = array(
			array(
				'name' => 'meu arquivo.jpg',
				'type' => 'image/jpeg',
				'tmp_name' => 'tests/test_files/test_file.gif',
				'error' => '0',
				'size' => '7777',
			),
			array(
				'name' => 'meu arquivo2.jpg',
				'type' => 'image/png',
				'tmp_name' => '',
				'error' => '0',
				'size' => '7777',
			),
		);
		$this->assertTrue( $this->obj->validate($params[0]) );
		$this->assertFalse( $this->obj->validate($params[1]) );
		$this->assertFalse( $this->obj->validate($params) );
	
    }

	function testGetDimensions(){
		$image = "tests/test_files/image_sample.jpg";
		$result = $this->obj->getDimensions($image);
		$this->assertInternalType('array', $result);
		$this->assertEquals("100", $result["width"]);
		$this->assertEquals("80", $result["height"]);
	}

	function testGetResampleDimensions(){
		$image = "tests/test_files/image_sample.jpg";

		$result = $this->obj->getResampleDimensions($image, "200x160");
		$this->assertInternalType('array', $result);
		$this->assertEquals("100", $result["width"]);
		$this->assertEquals("80", $result["height"]);

		$result = $this->obj->getResampleDimensions($image);
		$this->assertInternalType('array', $result);
		$this->assertEquals("100", $result["width"]);
		$this->assertEquals("80", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "200x");
		$this->assertInternalType('array', $result);
		$this->assertEquals("100", $result["width"]);
		$this->assertEquals("80", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "80x40");
		$this->assertInternalType('array', $result);
		$this->assertEquals("80", $result["width"]);
		$this->assertEquals("64", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "80x");
		$this->assertInternalType('array', $result);
		$this->assertEquals("80", $result["width"]);
		$this->assertEquals("64", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "80");
		$this->assertInternalType('array', $result);
		$this->assertEquals("80", $result["width"]);
		$this->assertEquals("64", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "50x");
		$this->assertInternalType('array', $result);
		$this->assertEquals("50", $result["width"]);
		$this->assertEquals("40", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, " 50x ");
		$this->assertInternalType('array', $result);
		$this->assertEquals("50", $result["width"]);
		$this->assertEquals("40", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "x40");
		$this->assertInternalType('array', $result);
		$this->assertEquals("50", $result["width"]);
		$this->assertEquals("40", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, " x40");
		$this->assertInternalType('array', $result);
		$this->assertEquals("50", $result["width"]);
		$this->assertEquals("40", $result["height"]);

		$result = $this->obj->getResampleDimensions($image, "50");
		$this->assertInternalType('array', $result);
		$this->assertEquals("50", $result["width"]);
		$this->assertEquals("40", $result["height"]);

	}

	function testIsImage(){
		$params = array(
			array(
				'name' => 'meu arquivo.jpg',
				'type' => 'image/jpeg',
				'tmp_name' => 'tests/test_files/test_file.gif',
				'error' => '0',
				'size' => '7777',
			),
			array(
				'name' => 'meu arquivo2.jpg',
				'type' => 'image/rar',
				'tmp_name' => '',
				'error' => '0',
				'size' => '7777',
			),
		);
		$this->assertTrue( $this->obj->isImage($params[0]) );
		$this->assertFalse( $this->obj->isImage($params[1]) );
		$this->assertFalse( $this->obj->isImage($params) );
	}
	
	function testGetExtension(){
		$this->assertEquals("jpg", $this->obj->getExtension('imagem.jpg') );
		$this->assertEquals("gif", $this->obj->getExtension('imagem.jpg.gif') );
		$this->assertEquals("jpeg", $this->obj->getExtension('imagem.jpeg') );
		$this->assertEquals("", $this->obj->getExtension('imagem') );
	}
	
	function test_OrganizeFolders(){
		$this->obj->_organizeFolders("tests/uploads/image_class/");
		$this->assertTrue(
			is_dir("tests/uploads/image_class/".date("Y")),
			"Não está criando diretório do ano atual." );
		$this->assertTrue(
			is_dir("tests/uploads/image_class/".date("Y")."/".date("m")),
			"Não está criando diretório do mês atual." );
		rmdir("tests/uploads/image_class/".date("Y")."/".date("m"));
		rmdir("tests/uploads/image_class/".date("Y"));
	}
	
}
?>