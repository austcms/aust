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
            'table' => 'imagens',
            'field' => 'path',
            'type' => 'text',
            'position' => 'AFTER ref_id'
        );
        $this->addField($schema);
        
        $schema = array(
            'table' => 'imagens',
            'field' => 'systempath',
            'type' => 'text',
            'position' => 'AFTER ref_id'
        );
        $this->addField($schema);

        return true;
    }

    function down(){
        $this->dropField('imagens', 'path');
        $this->dropField('imagens', 'systempath');
        return true;
    }
}
?>