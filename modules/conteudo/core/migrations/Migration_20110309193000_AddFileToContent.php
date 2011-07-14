<?php
class Migration_20110309193000_AddFileToContent extends Migrations
{
    function up(){

        $schema = array(
            'table' => 'textos',
            'field' => 'file_systempath',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'textos',
            'field' => 'file_path',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'textos',
            'field' => 'file_size',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'textos',
            'field' => 'file_ext',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'textos',
            'field' => 'file_type',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'textos',
            'field' => 'file_name',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

        $schema = array(
            'table' => 'textos',
            'field' => 'original_file_name',
            'type' => "text",
            'position' => 'AFTER ref'
        );
        $this->addField($schema);

		return true;
    }
    
    function down(){
        $this->dropField('textos', 'file_systempath');
        $this->dropField('textos', 'file_path');
        $this->dropField('textos', 'file_size');
        $this->dropField('textos', 'file_ext');
        $this->dropField('textos', 'file_type');
        $this->dropField('textos', 'file_name');
        $this->dropField('textos', 'original_file_name');

    }
}
?>
