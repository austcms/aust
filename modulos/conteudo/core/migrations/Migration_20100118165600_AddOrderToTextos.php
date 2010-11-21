<?php
/**
 * MOD MIGRATION
 *
 * Migration de um módulos
 *
 */
class Migration_20100118165600_AddOrderToTextos extends Migrations
{
    function up(){
        $schema = array(
            'table' => 'textos',
            'field' => 'ordem',
            'type' => 'VARCHAR(50)',
            'position' => 'AFTER id'
        );
        $this->addField($schema);

        return true;
    }
    
    function down(){
        $this->dropField('textos', 'ordem');
        return true;
    }
}
?>
