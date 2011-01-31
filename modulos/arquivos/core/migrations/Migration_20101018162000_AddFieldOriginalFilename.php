<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20101018162000_AddFieldOriginalFilename extends Migrations
{
    function up(){

        $schema = array(
            'table' => 'arquivos',
            'field' => 'original_filename',
            'type' => 'VARCHAR(250)',
            'position' => 'AFTER systemurl'
        );
        $this->addField($schema);

        return true;
    }

    function down(){
        $this->dropField('arquivos', 'original_filename');
        return true;

    }
}
?>