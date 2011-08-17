<?php
class Migration_20110616145900_AddContentIdField extends Migrations
{
	function up(){

		$schema = array(
			'table' => 'photo_gallery_images',
			'field' => 'content_id',
			'type' => "int",
			'position' => 'AFTER gallery_id'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'photo_gallery_images',
			'field' => 'node_id',
			'type' => "int",
			'position' => 'AFTER id'
		);
		$this->addField($schema);
		
		return true;
	}
	
	function down(){
		$this->dropField('photo_gallery_images', 'content_id');
		$this->dropField('photo_gallery_images', 'node_id');
		
		return true;
	}
}
?>