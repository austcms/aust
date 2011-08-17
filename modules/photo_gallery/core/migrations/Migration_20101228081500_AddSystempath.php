<?php
class Migration_20101228081500_AddSystempath extends Migrations
{
	function up(){
		$schema = array(
			'table' => 'photo_gallery_images',
			'field' => 'file_path',
			'type' => "text",
			'position' => 'AFTER link'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'photo_gallery_images',
			'field' => 'file_systempath',
			'type' => "text",
			'position' => 'AFTER file_path'
		);
		$this->addField($schema);

		return true;
	}
	
	function down(){
		$this->dropField('photo_gallery_images', 'file_path');
		$this->dropField('photo_gallery_images', 'file_systempath');

		return true;
	}
}
?>
