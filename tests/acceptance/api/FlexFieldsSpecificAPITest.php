<?php
/*
 * Tests all API query strings
 */
require_once 'tests/config/auto_include.php';

class FlexFieldsSpecificAPITest extends PHPUnit_Framework_TestCase
{
	
	/*
	 * A id of any record in the News table
	 */
	public $newsId;

    public function setUp(){
		installModule('textual');
		
		Connection::getInstance()->exec('DELETE FROM flex_fields_config');
		Fixture::getInstance()->createApiData();
		Connection::getInstance()->exec('DELETE FROM news');
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
		
		$site = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE class='site' LIMIT 1");
    	
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
		//pr($result);
		$this->assertEquals(3, count($result['result']));
		$this->assertEquals($this->newsId, $result['result'][0]['id']);
		$this->assertEquals("New Service Offers Music in Quantity, Not by Song", $result['result'][0]['title']);
		
		$item = $result['result'][0];
		
		$this->assertEquals(3, count($result['result'][0]['images']), 'FlexFields\' images are not being loaded.');
	 }

	 function testRetrievingTheLastFourteenPhotosInsertedInFlexFields(){
		$api = new Api();
		$result = $api->dispatch("?", false);
		$result = json_decode($result, true);
//		$this->assertEquals(2, count($result['result']));
	 }

}
?>