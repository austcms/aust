<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ApiTransactionTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        $this->obj = new ApiTransaction();
    }

	function createContent(){
		Fixture::getInstance()->createApiData();
		Connection::getInstance()->exec('DELETE FROM news');
		$sql = "INSERT INTO news
					(title, text)
					VALUES
						('New Service Offers Music in Quantity, Not by Song',
						'Daniel Ek, the 28-year-old co-founder and public face of <a href=\"whatever\">Spotify</a>, the European digital music service, paced around the company’s loftlike Manhattan office on Tuesday afternoon, clutching two mobile phones that buzzed constantly.'
						),
						('Amazon Takes On California',
						'SAN FRANCISCO — Amazon, the world’s largest online merchant, has an ambitious and far-reaching new agenda: it wants to rewrite tax policy for the Internet era.'
						),
						('Google+ Improves on Facebook',
						'Google, the most popular Web site on earth, is worried about the second-most popular site. That, of course, would be Facebook.'
						)
				";
		Connection::getInstance()->exec($sql);
	}
	
	function testConvertGetStringToArray(){
		$get = "api.json?query=News&order=title;id&limit=2&fields=*";
		$array = $this->obj->ensureArray($get);
		$this->assertArrayHasKey('query', $array);
		$this->assertArrayHasKey('order', $array);
		$this->assertArrayHasKey('limit', $array);
		$this->assertArrayHasKey('fields', $array);
		$this->assertEquals('News', $array['query']);
		$this->assertEquals('title;id', $array['order']);
		$this->assertEquals('2', $array['limit']);
		$this->assertEquals('*', $array['fields']);

		$get = array("query" => "News", "order" => "title;id", "limit" => "2", "fields" => "*");
		$array = $this->obj->ensureArray($get);
		$this->assertArrayHasKey('query', $array);
		$this->assertArrayHasKey('order', $array);
		$this->assertArrayHasKey('limit', $array);
		$this->assertArrayHasKey('fields', $array);
		$this->assertEquals('News', $array['query']);
		$this->assertEquals('title;id', $array['order']);
		$this->assertEquals('2', $array['limit']);
		$this->assertEquals('*', $array['fields']);

		$get = "query=News&without_a_key&order=title;id&limit=2&fields=*";
		$array = $this->obj->ensureArray($get);
		$this->assertArrayHasKey('query', $array);
		$this->assertArrayHasKey('order', $array);
		$this->assertArrayHasKey('limit', $array);
		$this->assertArrayHasKey('fields', $array);
		$this->assertEquals('News', $array['query']);
		$this->assertEquals('title;id', $array['order']);
		$this->assertEquals('2', $array['limit']);
		$this->assertEquals('*', $array['fields']);
		$this->assertContains('without_a_key', $array);
		
	}

    function testRetrieveConfiguration(){
		$params = array(
			'configuration' => 'site_title',
			'query' => 'News'
		);
		$return = $this->obj->retrieveFrom($params);
		$this->assertEquals('configuration', $return);
    }

    function testRetrieveStructureData(){
		$params = array(
			'query' => 'News'
		);
		$return = $this->obj->retrieveFrom($params);
		$this->assertEquals('structure', $return);
    }

    function testGetConfigurationFields(){
		$params = array(
			'configuration' => 'site_title'
		);
		$return = $this->obj->getConfigurationFields($params);
		$this->assertTrue( in_array('site_title', $return) );

		$params = array(
			'configuration' => 'site_title;other_field'
		);
		$return = $this->obj->getConfigurationFields($params);
		$this->assertContains('site_title', $return);
		$this->assertContains('other_field', $return);
    }
		
    function testAskVersionJson(){
		$params = array(
			'data_format' => 'json',
			'version' => 'true'
		);
		$return = $this->obj->perform($params);
		$this->assertEquals('{"result":"0.0.1"}', $return);
    }
	
	// returns Array
	function testGetDataIsArray(){
		$this->createContent();
		
		$query = array(
			'query' => 'News',
			'order' => 'title;id',
			'limit' => 2,
			'fields' => '*'
		);

		$return = $this->obj->getData($query);
		$this->assertInternalType('array', $return);
		$this->assertEquals(2, count($return));
		$this->assertEquals('Amazon Takes On California', $return[0]['title']);
	}
	
	// returns Array
	function testGetDataWithDifferentFieldsSpecified(){
		$this->createContent();
		
		$query = array(
			'query' => 'News',
			'limit' => 2,
			'fields' => 'text'
		);

		$return = $this->obj->getData($query);
		$this->assertInternalType('array', $return);
		$this->assertEquals(2, count($return));
		$this->assertArrayHasKey('text', $return[0]);
		$this->assertArrayNotHasKey('title', $return[0]);
	}
	
	// returns Array
	function testGetDataWithConditionOfTitle(){
		$this->createContent();
		
		// API SPEC #2.1
		$query = array(
			'query' => 'News',
			'fields' => 'title;text',
			'where_title' => 'new+service+offers+music+in+quantity,+not+by+song',
		);

		$return = $this->obj->getData($query);
		$this->assertInternalType('array', $return);
		$this->assertEquals(1, count($return));
		$this->assertArrayHasKey('text', $return[0]);
		$this->assertEquals('New Service Offers Music in Quantity, Not by Song', $return[0]['title']);
		$this->assertContains('co-founder and public face', $return[0]['text']);
	}
	
	
	
	// returns Array
	function testGetDataWithConditionOfTitleAndWordInText(){
		$this->createContent();

		// API SPEC #2.2
		$query = array(
			'query' => 'News',
			'fields' => 'id;title;text',
			'where_title' => 'new+service+offers+music+in+quantity,+not+by+song',
			'where_text' => '*public*',
		);

		$return = $this->obj->getData($query);
		$this->assertInternalType('array', $return);
		$this->assertEquals(1, count($return));
		$this->assertArrayHasKey('text', $return[0]);
		$this->assertEquals('New Service Offers Music in Quantity, Not by Song', $return[0]['title']);
		$this->assertContains('co-founder and public face', $return[0]['text']);

	}
	
	// returns Array
	function testGetDataWithConditionOfTwoPossibleTitles(){
		$this->createContent();
		
		// API SPEC #2.4
		$query = array(
			'query' => 'News',
			'fields' => 'id;title;text',
			'where_title' => '*new+service+offers*;*google*',
		);

		$return = $this->obj->getData($query);
		$this->assertInternalType('array', $return);
		$this->assertEquals(2, count($return));
		$this->assertArrayHasKey('text', $return[0]);
		$this->assertEquals('New Service Offers Music in Quantity, Not by Song', $return[0]['title']);
		$this->assertContains('co-founder and public face', $return[0]['text']);
	}
	
	// returns JSON
	function testPerform(){
		$this->createContent();
		$query = array(
			'query' => 'News',
			'order' => 'title;id',
			'limit' => 2,
			'fields' => '*'
		);

		$return = $this->obj->perform($query);
		$this->assertInternalType('string', $return);
		$return = json_decode($return, true);
		$return = $return['result'];
		$this->assertEquals(2, count($return));
		$this->assertEquals('Amazon Takes On California', $return[0]['title']);

		$query = array(
			'query' => 'News',
			'order' => 'id+desc',
			'limit' => 4,
			'fields' => '*'
		);

		$return = $this->obj->perform($query);
		$this->assertInternalType('string', $return);
		$return = json_decode($return, true);
		$return = $return['result'];
		$this->assertEquals(3, count($return));
		$this->assertEquals('Google+ Improves on Facebook', $return[0]['title']);
		
		
	}
	
	
}
?>