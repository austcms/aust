<?php
/*
 * Tests all modules at once, verifying required files. Ideal for creating new files.
 */
// require_once 'PHPUnit/Framework.php';

require_once 'tests/config/auto_include.php';

class ModulesAcceptanceTest extends PHPUnit_Framework_TestCase
{
	
	public $modulesDirs = array();
	function setUp(){
		
		$this->modulesDirs = array();
		foreach (glob(MODULES_DIR."*", GLOB_ONLYDIR) as $filename) {
			$this->modulesDirs[] = $filename."/";
		}

	}

	function testDirectoryNamingShouldNotBeUppercase(){
		foreach( $this->modulesDirs as $module ){
			$uppercase = preg_match("/[A-Z]/", $module);
			$lowercase = preg_match("/[a-z]/", $module);
			$correctName = (!$uppercase && $lowercase);
			$this->assertTrue( $correctName, $module." has wrong characters." );
		}
	}

	function testHasConfigFile(){
		foreach( $this->modulesDirs as $module ){
			$this->assertTrue( file_exists( $module.MOD_CONFIG ), $module." has no config file." );
		}
	}

	function testHasControlPanelFileForStructure(){
		foreach( $this->modulesDirs as $module ){
			$this->assertTrue( file_exists( $module.MOD_VIEW_DIR.CONTROL_PANEL_DISPATCHER."/structure_configuration.php" ), $module." has no config file for structure configuration." );
		}
	}

	function testConfigHasKeys(){
		$shouldHaveKeys = array(
			"name",
			"className",
			"description",
			"actions",
			"configurations"
		);
		foreach( $this->modulesDirs as $module ){
			require $module.MOD_CONFIG;
			foreach( $shouldHaveKeys as $key ){
				$this->assertArrayHasKey( $key, $modInfo, $module."doesn't have ".$key." key.");
			}
		}
	}

	function testHasAModelDefined(){
		foreach( $this->modulesDirs as $module ){
			require $module.MOD_CONFIG;
			$this->assertTrue( file_exists( $module.$modInfo['className'].".php" ), $module." has no model file." );
		}
	}

	function testHasAControllerDir(){
		foreach( $this->modulesDirs as $module ){
			$this->assertTrue( file_exists( $module.MOD_CONTROLLER_DIR ), $module." has no controller dir." );
		}
	}

	function testHasAViewDir(){
		foreach( $this->modulesDirs as $module ){
			$this->assertTrue( file_exists( $module.MOD_VIEW_DIR ), $module." has no view dir." );
		}
	}

}
?>