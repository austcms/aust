<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20110128152000_AddOldFieldsToNewInstallation extends Migrations
{
    function up(){

        $schema = array(
            'table' => 'arquivos',
            'field' => 'titulo_encoded',
            'type' => 'varchar(250)',
            'position' => 'AFTER titulo'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'arquivos',
            'field' => 'systemurl',
            'type' => 'text',
            'position' => 'AFTER url'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'arquivos',
            'field' => 'admin_id',
            'type' => 'int',
            'position' => 'AFTER aprovado'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'arquivos',
            'field' => 'created_on',
            'type' => 'datetime',
            'position' => 'AFTER admin_id'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'arquivos',
            'field' => 'updated_on',
            'type' => 'datetime',
            'position' => 'AFTER admin_id'
        );
        $this->addField($schema);

        return true;
    }

    function down(){
        $this->dropField('arquivos', 'resumo');
        $this->dropField('arquivos', 'admin_id');
        $this->dropField('arquivos', 'created_on');
        $this->dropField('arquivos', 'updated_on');
        return true;

    }
}
?>