<?php
class Migration_20110128152000_AddOldFieldsToNewInstallation extends Migrations
{
	function up(){
		$schema = array(
			'table' => 'files',
			'field' => 'title_encoded',
			'type' => 'varchar(250)',
			'position' => 'AFTER title'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'files',
			'field' => 'systemurl',
			'type' => 'text',
			'position' => 'AFTER url'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'files',
			'field' => 'created_on',
			'type' => 'datetime',
			'position' => 'AFTER approved'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'files',
			'field' => 'updated_on',
			'type' => 'datetime',
			'position' => 'AFTER created_on'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'files',
			'field' => 'admin_id',
			'type' => 'int',
			'position' => 'AFTER updated_on'
		);
		$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('files', 'title_encoded');
		$this->dropField('files', 'systemurl');
		$this->dropField('files', 'admin_id');
		$this->dropField('files', 'created_on');
		$this->dropField('files', 'updated_on');
		return true;
	}
}
?>