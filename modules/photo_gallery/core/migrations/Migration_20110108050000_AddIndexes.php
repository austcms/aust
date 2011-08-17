<?php
class Migration_20110108050000_AddIndexes extends Migrations
{
	function up(){
		$sql = "CREATE INDEX order_idx ON photo_gallery_images (order_nr)";
		Connection::getInstance()->exec($sql);
		$sql = "CREATE INDEX gallery_id_idx ON photo_gallery_images (gallery_id)";
		Connection::getInstance()->exec($sql);
		
		$sql = "ALTER TABLE photo_gallery_images ENGINE = InnoDB";
		Connection::getInstance()->exec($sql);
		
		return true;
	}
	
	function down(){
		
		return true;
	}
}
?>