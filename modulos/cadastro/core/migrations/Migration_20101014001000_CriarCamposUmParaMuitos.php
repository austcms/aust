<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20101014001000_CriarCamposUmParaMuitos extends Migrations
{
    function up(){

        $schema = array(
            'table' => 'cadastros_conf',
            'field' => 'ref_child_field',
            'type' => 'VARCHAR(60)',
            'position' => 'AFTER ref_campo'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'cadastros_conf',
            'field' => 'ref_parent_field',
            'type' => 'VARCHAR(60)',
            'position' => 'AFTER ref_campo'
        );
        $this->addField($schema);

        return true;
    }

    function down(){
        $this->dropField('cadastros_conf', 'ref_parent_field');
        $this->dropField('cadastros_conf', 'ref_child_field');
        return true;
    }

}
?>