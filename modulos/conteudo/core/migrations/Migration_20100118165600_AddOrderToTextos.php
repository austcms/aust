<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
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
        
    }
}
?>
