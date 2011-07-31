<?php
function getUser(){
	$query = Connection::getInstance()->query("SELECT * FROM admins LIMIT 1");
	$query = reset($query);
	return $query;
}

function installModule($moduleName){
	include(MODULES_DIR.$moduleName.'/'.MOD_CONFIG);

	Connection::getInstance()->exec("DELETE FROM migrations_mods WHERE module_name='".$moduleName."'");
    $modInfo['embedownform'] = (empty($modInfo['embedownform'])) ? false : $modInfo['embedownform'];
    $modInfo['embed'] = (empty($modInfo['embed'])) ? false : $modInfo['embed'];
    $modInfo['somenteestrutura'] = (empty($modInfo['somenteestrutura'])) ? false : $modInfo['somenteestrutura'];

	MigrationsMods::getInstance()->updateMigration($moduleName);
    $param = array(
        'tipo' => 'módulo',
        'chave' => 'dir',
        'valor' => $moduleName,
        'pasta' => $moduleName,
        'modInfo' => $modInfo,
        'autor' => "1",
    );
    return ModulesManager::getInstance()->configuraModulo($param);	
}

function getAdminId(){
	$query = Connection::getInstance()->query("SELECT id FROM admins LIMIT 1");
	$query = reset($query);
	$id = $query["id"];
	return $id;
}

function query($str){
	return Connection::getInstance()->query($str);
}

function login(){
	destroyLogin();
    $_SESSION['login']['id'] = getAdminId();
    $_SESSION['login']['username'] = 'test_user';
	User::getInstance()->type('Webmaster');
}

function destroyLogin(){
	User::getInstance()->userInfo = array();
	User::getInstance()->id = null;
	$_SESSION['login'] = null;	
}
?>