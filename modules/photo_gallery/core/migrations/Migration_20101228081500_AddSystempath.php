<?php
class Migration_20101228081500_AddSystempath extends Migrations
{
    function up(){
        $schema = array(
            'table' => 'photo_gallery_images',
            'field' => 'image_path',
            'type' => "text",
            'position' => 'AFTER link'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'photo_gallery_images',
            'field' => 'image_systempath',
            'type' => "text",
            'position' => 'AFTER image_path'
        );
        $this->addField($schema);

        return true;
    }
    
    function down(){
        $this->dropField('photo_gallery_images', 'image_path');
        $this->dropField('photo_gallery_images', 'image_systempath');

        return true;
    }
}
?>
