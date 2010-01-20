<?php
/**
 * MOD MIGRATION
 *
 * Migration de um módulos
 *
 */
class Migration_20100120102000_AddFieldsToTextos extends Migrations
{
    function up(){
        $schema = array(
            'table' => 'textos',
            'field' => 'titulo_encoded',
            'type' => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
            'position' => 'AFTER titulo'
        );
        $this->addField($schema);

        return true;
    }
    
    function down(){
        $this->dropField('textos', 'titulo_encoded');

        return true;
    }
}
?>
