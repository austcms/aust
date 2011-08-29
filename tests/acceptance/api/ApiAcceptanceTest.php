<?php
/*
 * Tests all API query strings
 */
require_once 'tests/config/auto_include.php';

class APIAcceptanceTest extends PHPUnit_Framework_TestCase
{
	
	/*
	 * A id of any record in the News table
	 */
	public $newsId;

    public function setUp(){
		installModule('textual');
		
		Fixture::getInstance()->createApiData();
		Connection::getInstance()->exec('DELETE FROM news');
		Connection::getInstance()->exec('DELETE FROM textual');
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

		$news = Connection::getInstance()->query("SELECT id FROM news LIMIT 1");
		$this->newsId = $news[0]['id'];
		
		$site = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE class='site' LIMIT 1");
		$siteId = $site[0]['id'];
		
		$params = array(
            'name' => "Articles",
            'site' => $siteId,
            'module' => 'textual',
            'author' => '1'
        );
        
		$result = Aust::getInstance()->createStructure($params);

		$sql = "INSERT INTO textual
					(node_id, title, text)
					VALUES
						('".$result."', 'New Service Offers Music in Quantity, Not by Song',
						'Daniel Ek, the 28-year-old co-founder and public face of <a href=\"whatever\">Spotify</a>, the European digital music service, paced around the company’s loftlike Manhattan office on Tuesday afternoon, clutching two mobile phones that buzzed constantly.'
						),
						('".$result."', 'Amazon Takes On California',
						'SAN FRANCISCO — Amazon, the world’s largest online merchant, has an ambitious and far-reaching new agenda: it wants to rewrite tax policy for the Internet era.'
						),
						('".$result."', 'Google+ Improves on Facebook',
						'Google, the most popular Web site on earth, is worried about the second-most popular site. That, of course, would be Facebook.'
						)
				";
		Connection::getInstance()->exec($sql);
    	

	}
	
	/*
	 *
	 * 1 Basic content queries
	 *
	 */
    function testOrderLimitAndAllFields(){
		$api = new Api();
		$result = $api->dispatch("query=News&order=title;id&limit=2&fields=*", false);
		$result = json_decode($result, true);
		$this->assertEquals(2, count($result['result']));
    }

    function testUnorderedSpecificFields(){
		$api = new Api();
		$result = $api->dispatch("query=News&fields=title;text", false);
		$result = json_decode($result, true);
		$this->assertArrayHasKey('title', $result['result'][0]);
		$this->assertArrayHasKey('text', $result['result'][0]);
		$this->assertArrayNotHasKey('id', $result['result'][0]);
    }

    function testSpecifyingAModule(){
		$api = new Api();
		$result = $api->dispatch("query=News&module=flex_fields&where_id=".$this->newsId."&fields=title", false);
		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));
		$this->assertArrayHasKey('title', $result['result'][0]);
		$this->assertArrayNotHasKey('id', $result['result'][0]);
    }

	    function testSpecifyingAWrongModule(){
			$api = new Api();
			$result = $api->dispatch("query=News&module=unknown_module&where_id=2&fields=title", false);
			$result = json_decode($result, true);
			$empty = empty($result['result']);
			$this->assertTrue($empty);
	    }

	/*
	 *
	 * 2 Retrieve content with WHERE
	 *
	 */
    function testUsingWhere(){
		$api = new Api();
		$result = $api->dispatch("query=News&where_title=new+service+offers", false);
		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));

		$api = new Api();
		$result = $api->dispatch("query=Articles&where_title=new+service+offers", false);
		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));
    }

    function testUsingMultipleWhereStatements(){
		$api = new Api();
		$result = $api->dispatch("query=News&where_title=new+service+offers&where_text=Google*", false);
		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));

		$api = new Api();
		$result = $api->dispatch("query=News&where_title=new+service+offers&where_text=opsss*", false);
		$result = json_decode($result, true);
		$empty = empty($result['result']);
		$this->assertTrue($empty);
    }

    function testByIdUsingWhere(){
		$api = new Api();
		$result = $api->dispatch("query=News&where_id=".$this->newsId, false);
		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));
		$this->assertEquals($this->newsId, $result['result'][0]['id'], "query=News&where_id=".$this->newsId);
    }

    function testTwoPossibleValuesForTheSameField(){
		$api = new Api();
		$result = $api->dispatch("query=News&where_title=*new+service+offers*;google*", false);
		$result = json_decode($result, true);
		$this->assertEquals(2, count($result['result']));

		$api = new Api();
		$result = $api->dispatch("query=News&where_title=*new+service+offers*;opssss*", false);
		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));
    }

	/*
	 *
	 * For "4 Retrieve content with custom module API options",
	 * see some module specific API tests.
	 *
	 */
	
	/*
	 *
	 * OTHER QUERIES
	 *
	 */
	/*
	 *
	 * 1 Get configuration value
	 *
	 */
	 function testGetConfigurationValueByProperty(){
		/*
		 * We populate the 'configurations' table, test the api, then delete everything.
		 * 
		 */
		Connection::getInstance()->exec(
			"INSERT INTO configurations (name, property, value, type)
			 VALUES 
				('Not a site name', 'not_a_site_name', 'Whatever', 'general'),
				('Site name', 'site_name', 'Aust website', 'general')"
		);
		$api = new Api();
		$result = $api->dispatch("configuration=site_name", false);
		Connection::getInstance()->exec("DELETE FROM configurations");

		$result = json_decode($result, true);
		$this->assertEquals(1, count($result['result']));
		$this->assertEquals('Aust website', $result['result']['site_name']);
	 }

}
?>