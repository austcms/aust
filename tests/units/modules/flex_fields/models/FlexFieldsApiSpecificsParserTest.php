<?php
require_once 'tests/config/auto_include.php';
include_once MODULES_DIR.'flex_fields/models/FlexFieldsApiSpecificsParser.php';

class FlexFieldsApiSpecificsParserTest extends PHPUnit_Framework_TestCase
{

	function testParseQuery(){
		$query = array(
			'last_images' => '20',
		);
		
		$result = FlexFieldsApiSpecificsParser::parseQuery($query);
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('last_fields', $result);
	}

	function testLastByFields(){
		$query = array('last_images' => '10');

		$last = FlexFieldsApiSpecificsParser::lastByFields($query);
		$this->assertInternalType('array', $last);
		$this->assertArrayHasKey('images', $last);
		$this->assertArrayHasKey('limit', $last['images']);
		$this->assertEquals('10', $last['images']['limit']);

		$query = array();
		$this->assertEquals(false, FlexFieldsApiSpecificsParser::lastByFields());
	}
}
?>