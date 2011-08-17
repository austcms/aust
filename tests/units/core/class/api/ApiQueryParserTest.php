<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ApiQueryParserTest extends PHPUnit_Framework_TestCase
{

	public function setUp(){
		$this->obj = new ApiQueryParser();
	}

	function testOrder(){
		$query['order'] = 'name;id';
		$this->assertEquals("mainTable.name asc,mainTable.id asc", $this->obj->order($query));

		$query['order'] = 'id;name';
		$this->assertEquals("mainTable.id asc,mainTable.name asc", $this->obj->order($query));

		$query['order'] = 'id+desc;name+asc';
		$this->assertEquals("mainTable.id desc,mainTable.name asc", $this->obj->order($query));

		$query['order'] = 'id;name+desc';
		$this->assertEquals("mainTable.id asc,mainTable.name desc", $this->obj->order($query));
	}

	function testLimit(){
		$query['limit'] = '12';
		$this->assertEquals('12', $this->obj->limit($query));

		$query['limit'] = null;
		$this->assertEquals('100', $this->obj->limit($query));
	}
	
	function testFields(){
		$query['fields'] = '*';
		$this->assertEquals('*', $this->obj->fields($query));

		unset($query['fields']);
		$this->assertEquals('*', $this->obj->fields($query));

		$query['fields'] = 'title;number';
		$this->assertInternalType('array', $this->obj->fields($query));
		$this->assertContains('title', $this->obj->fields($query));
		$this->assertContains('number', $this->obj->fields($query));
	}

	function testWhere(){
		// case #2.1
		$query = array('where_title' => 'new+service+offers');
		$result = $this->obj->where($query);
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('title', $result);
		$this->assertRegExp('/new service offers/i', $result['title']);

		// case #2.2
		$query = array(
			'where_title' => 'new+service+offers',
			'where_text' => 'public*'
		);
		$result = $this->obj->where($query);
		$this->assertTrue( count($result) == 2 );
		$this->assertArrayHasKey('title', $result);
		$this->assertArrayHasKey('text', $result);
		$this->assertArrayNotHasKey('id', $result);
		$this->assertEquals('new service offers', $result['title']);
		$this->assertEquals('public%', $result['text']);

		// case #2.3
		$query = array('where_id' => '10');
		$result = $this->obj->where($query);
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('id', $result);
		$this->assertEquals('10', $result['id']);

		// case #2.3
		$query = array(
			'where_title' => 'query+one;query+two*'
		);
		$result = $this->obj->where($query);
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey('title', $result);
		$this->assertInternalType('array', $result['title']);
		$this->assertEquals('query one', $result['title'][0]);
		$this->assertEquals('query two%', $result['title'][1]);
		unset($query);


	}

	function testGetStructure(){
		$newsId = Fixture::getInstance()->createApiData();
		
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
		
		$sql = "SELECT id FROM taxonomy WHERE name='News' AND class='structure'";
		$newsId = Connection::getInstance()->query($sql);
		$newsId = reset($newsId);
		$newsId = $newsId['id'];
		
		$query['query'] = 'News';

		$return = $this->obj->structureId($query);
		$this->assertEquals(array($newsId), $return);
	}
	
	
}
?>