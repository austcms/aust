<?php
/*
 * Tests all API query strings
 */
require_once 'tests/config/auto_include.php';

class FlexFieldsApiSpecificsTest extends PHPUnit_Framework_TestCase
{
	
	/*
	 * A id of any record in the News table
	 */
	public $newsId;

	/*
	 * We're creating a News structure, then inserting some data and 
	 * saving some images within a record.
	 * 
	 */
    public function setUp(){
		installModule('textual');
		
		Connection::getInstance()->exec('DELETE FROM flex_fields_config');
		Fixture::getInstance()->createApiData();
		Connection::getInstance()->exec('DELETE FROM news');
		Connection::getInstance()->exec('DELETE FROM news_files');
		Connection::getInstance()->exec('DELETE FROM news_images');
		Connection::getInstance()->exec('DELETE FROM textual');
		/* insert news */
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

		/* insert news' images */
		$sql = "INSERT INTO news_images
					(maintable_id, type, title,
					file_systempath,
					file_path,
					file_name, file_type, reference_field)
					VALUES
						('".$this->newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/123.jpg',
						'uploads/2011/08/123.jpg',
						'123.jpg', 'image/jpeg', 'images'
						),
						('".$this->newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/456.jpg',
						'uploads/2011/08/456.jpg',
						'456.jpg', 'image/jpeg', 'images'
						),
						('".$this->newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/789.jpg',
						'uploads/2011/08/789.jpg',
						'789.jpg', 'image/jpeg', 'images'
						)
		";
		Connection::getInstance()->exec($sql);
		
		/* insert news' images */
		$sql = "INSERT INTO news_files
					(maintable_id, type, title,
					file_systempath,
					file_path,
					file_name, file_type, reference_field)
					VALUES
						('".$this->newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/123.mp3',
						'uploads/2011/08/123.mp3',
						'123.mp3', 'image/mp3', 'a_song'
						),
						('".$this->newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/456.mp3',
						'uploads/2011/08/456.mp3',
						'456.mp3', 'image/mp3', 'a_song'
						),
						('".$this->newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/789.mp3',
						'uploads/2011/08/789.mp3',
						'789.mp3', 'image/mp3', 'a_song'
						)
		";
		Connection::getInstance()->exec($sql);		
    	
	}
	
	function testExpectations(){
		$this->assertTrue( Connection::getInstance()->hasTable('news_files') );
		$filesResult = Connection::getInstance()->query('select * from news_files');
		$empty = empty( $filesResult );
		$this->assertFalse( $empty );
		$this->assertEquals( 3, count($filesResult) );
	}
	
	/*
	 *
	 * 4 Retrieve Content with custom module API options
	 *
	 */
	 function testFlexFieldsUseCaseLotsOfImages(){
		$api = new Api();
		$result = $api->dispatch("query=news&include_fields=images", false);
		$result = json_decode($result, true);
		$this->assertEquals(3, count($result['result']));
		$this->assertEquals($this->newsId, $result['result'][0]['id']);
		$this->assertEquals("New Service Offers Music in Quantity, Not by Song", $result['result'][0]['title']);
		
		$item = $result['result'][0];
		
		$this->assertEquals(3, count($result['result'][0]['images']), 'FlexFields\' images are not being loaded.');
	 }

		 function testFlexFieldsUseCaseLotsOfFiles(){
			$api = new Api();
			$result = $api->dispatch("query=news&include_fields=a_song", false);
			$result = json_decode($result, true);
			$this->assertEquals(3, count($result['result']));
			$this->assertEquals($this->newsId, $result['result'][0]['id']);
			$this->assertEquals("New Service Offers Music in Quantity, Not by Song", $result['result'][0]['title']);
		
			$item = $result['result'][0];
		
			$this->assertEquals(3, count($result['result'][0]['a_song']), 'FlexFields\' files are not being loaded.');
		 }

	 function testRetrievingTheLastFourteenPhotosInsertedInFlexFields(){
		$api = new Api();
		$result = $api->dispatch("query=news&last_images=2", false);
		$result = json_decode($result, true);
		$this->assertEquals(2, count($result['result']));
		$this->assertEquals('image/jpeg', $result['result'][0]['file_type']);
	 }

}
?>