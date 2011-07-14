<?php
class Migration_20101228081500_AddSystempath extends Migrations
{
    function up(){
        $schema = array(
            'table' => 'galeria_fotos_imagens',
            'field' => 'path',
            'type' => "text",
            'position' => 'AFTER tipo'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'galeria_fotos_imagens',
            'field' => 'systempath',
            'type' => "text",
            'position' => 'AFTER tipo'
        );
        $this->addField($schema);

        return true;
    }
    
    function down(){
        $this->dropField('galeria_fotos_imagens', 'path');
        $this->dropField('galeria_fotos_imagens', 'systempath');

        return true;
    }
}
?>
