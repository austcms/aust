<?php
// require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/config/variables.php';
require_once 'core/libs/functions/func.php';
#####################################

class AgendaTest extends PHPUnit_Framework_TestCase
{
	public $lastSaveId;
	
	public function setUp(){
		/*
		 * MÓDULOS ATUAL
		 *
		 * Diretório do módulo
		 */
		$this->mod = 'agenda';

		/*
		 * Informações de conexão com banco de dados
		 */

		include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
		include_once MODULES_DIR.$this->mod.'/'.$modInfo['className'].'.php';

		$this->obj = new $modInfo['className'];//new $modInfo['className']();
	}

	/*
	 * verifica se todas as configurações do arquivo config.php existem no método
	 * loadModConf()
	 */
	function testConfigurationsExists(){
		
		include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
		$configurations = $this->obj->loadModConf();
		foreach( $modInfo['configurations'] as $key=>$value ){
			$this->assertArrayHasKey($key, $configurations);
		}
	}



}
?>