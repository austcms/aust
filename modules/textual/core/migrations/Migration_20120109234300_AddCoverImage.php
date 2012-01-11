<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20120109234300_AddCoverImage extends Migrations
{
  function up(){

    $schema = array(
      'table' => 'textual',
      'field' => 'cover_image_file_path',
      'type' => 'text',
      'position' => 'AFTER approved',
      'default' => '0',
    );
    $this->addField($schema);
    $schema = array(
      'table' => 'textual',
      'field' => 'cover_image_file_systempath',
      'type' => 'text',
      'position' => 'AFTER approved',
      'default' => '0',
    );
    $this->addField($schema);
    $schema = array(
      'table' => 'textual',
      'field' => 'cover_image_file_type',
      'type' => 'text',
      'position' => 'AFTER approved',
      'default' => '0',
    );
    $this->addField($schema);
    $schema = array(
      'table' => 'textual',
      'field' => 'cover_image_file_name',
      'type' => 'text',
      'position' => 'AFTER approved',
      'default' => '0',
    );
    $this->addField($schema);
    $schema = array(
      'table' => 'textual',
      'field' => 'cover_image_file_size',
      'type' => 'text',
      'position' => 'AFTER approved',
      'default' => '0',
    );
    $this->addField($schema);

    return true;
  }

  function down(){
    $this->dropField('textual', 'cover_image');
    return true;

  }
}
?>