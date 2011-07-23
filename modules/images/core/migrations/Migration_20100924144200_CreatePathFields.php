<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100924144200_CreatePathFields extends Migrations
{
    function up(){

        $schema = array(
            'table' => 'images',
            'field' => 'image_path',
            'type' => 'text',
            'position' => 'AFTER image_type'
        );
        $this->addField($schema);
        
        $schema = array(
            'table' => 'images',
            'field' => 'image_systempath',
            'type' => 'text',
            'position' => 'AFTER image_type'
        );
        $this->addField($schema);

        return true;
    }

    function down(){
        $this->dropField('images', 'image_path');
        $this->dropField('images', 'image_systempath');
        return true;
    }
}
?>