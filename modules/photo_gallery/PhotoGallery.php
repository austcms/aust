<?php
/**
 * Module's model class
 *
 * @since v0.1.5, 30/05/2009
 */

class PhotoGallery extends Module
{

	public $mainTable = "photo_gallery";
	
	public $date = array(
		'standardFormat' => '%d/%m/%Y',
		'created_on' => 'created_on',
		'updated_on' => 'created_on'
	);
	public $authorField = "admin_id";

}
?>