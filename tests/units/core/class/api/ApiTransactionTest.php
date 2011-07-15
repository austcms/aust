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
	
    function testAskVersionJson(){
		$params = array(
			'data_format' => 'json',
			'version' => 'true'
		);
		$return = $this->obj->perform($params);
		$this->assertEquals('{"result":"0.0.1"}', $return);
    }
	
	// returns Array
	function testGetData(){
		$this->createContent();
		
		$query = array(
			'query' => 'News',
			'order' => 'title;id',
			'limit' => 2,
			'fields' => '*'
		);

		$return = $this->obj->getData($query);
		$this->assertType('array', $return);
		$this->assertEquals(2, count($return));
		$this->assertEquals('Amazon Takes On California', $return[0]['title']);

		$query = array(
			'query' => 'News',
			'order' => 'id+desc',
			'limit' => 4,
			'fields' => '*'
		);

		$return = $this->obj->getData($query);
		$this->assertType('array', $return);
		$this->assertEquals(3, count($return));
		$this->assertEquals('Google+ Improves on Facebook', $return[0]['title']);
		
		
	}

	// returns Array
	function testPerform(){
		$this->createContent();
		$query = array(
			'query' => 'News',
			'order' => 'title;id',
			'limit' => 2,
			'fields' => '*'
		);

		$return = $this->obj->perform($query);
		$this->assertType('string', $return);
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
		$this->assertType('string', $return);
		$return = json_decode($return, true);
		$return = $return['result'];
		$this->assertEquals(3, count($return));
		$this->assertEquals('Google+ Improves on Facebook', $return[0]['title']);
		
		
	}
	
	
}
?>