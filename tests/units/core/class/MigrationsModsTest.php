<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class MigrationsModsTest extends PHPUnit_Framework_TestCase
{
	
	function setUp(){
        $this->obj = new MigrationsMods();
	}
	
	function tearDown(){
		$modules = $this->modules();
		foreach( $modules as $module ){
			$this->obj->updateMigration($module);
		}
	}

	function modules(){
		return array(
			"agenda",
			"orders"
		);
	}
	
	function moduleLatestVersion($modName){
		$latestVersion = 0;
        foreach (glob(MODULES_DIR.$modName."/".MIGRATION_MOD_DIR."Migration_*.php") as $filename) {
            $regexp = "/([0-9]{14})/";
            if ( preg_match( $regexp, $filename, $matches) ){
                if( $matches[0] > $latestVersion )
                    $latestVersion = $matches[0];
				
                $latestVersion;
            }
        }
		return $latestVersion;
	}
	
	function uninstallModules(){
		Connection::getInstance()->exec("DELETE FROM modulos WHERE valor IN ('".implode("','", $this->modules())."')");
		$sql = "DELETE FROM migrations_mods WHERE module_name IN ('".implode("','", $this->modules())."')";
		Connection::getInstance()->exec($sql);
	}

	function getModulesVersions(){
		$sql = "SELECT version, module_name FROM migrations_mods WHERE module_name IN ('".implode("','", $this->modules())."')";
		$query = Connection::getInstance()->query($sql);
		$result = array();
		foreach( $query as $value ){
			$result[$value["module_name"]] = $value["version"];
		}
		return $result;
	}

    function testInitialization(){
        $this->obj = new MigrationsMods();
    }

	function testGetModNameFromPath(){
		$this->assertEquals("agenda", $this->obj->getModNameFromPath('agenda'));
		$this->assertEquals("agenda", $this->obj->getModNameFromPath('modules/agenda'));
    }
    

	function testStatus(){
		$this->assertType("array", $this->obj->status());
		$this->assertArrayHasKey("agenda", $this->obj->status());
	}
	
	function testGetActualVersion(){
		$this->uninstallModules();
		$this->assertEquals("0", $this->obj->getActualVersion('agenda'));
		$this->assertEquals("0", $this->obj->getActualVersion('orders'));

		$this->obj->updateMigration('agenda');
		$this->assertEquals($this->moduleLatestVersion('agenda'), $this->obj->getActualVersion('agenda'));
	}
	
	function testGetMissingMigrations(){
		$this->uninstallModules();
		$this->assertContains("Migration_20100406112000_CriarDB", $this->obj->getMissingMigrations('agenda') );
		$this->assertContains("Migration_20101224023400_CreateLocationField", $this->obj->getMissingMigrations('agenda') );
	}
	
	function testUpdateMigration(){
		$this->uninstallModules();
		$modules = $this->modules();
		foreach( $modules as $module ){
			$this->assertTrue( $this->obj->updateMigration($module), "Can't update module ".$module );

			$latestVersion = $this->moduleLatestVersion($module);

			$actualVersion = $this->obj->getActualVersion($module);
			$this->assertEquals($latestVersion, $actualVersion);
		}
		
	}

	function test_checkModVersionInArray(){
		$this->uninstallModules();
		$this->assertArrayHasKey("agenda", $this->obj->_checkModVersionInArray('agenda') );
	}
	
	function test_checkAllModsMigration(){
		$this->uninstallModules();

		$latestVersion = $this->moduleLatestVersion('agenda');
		
		$version = $this->obj->_checkModVersionInArray('agenda');
		$this->assertArrayHasKey("agenda", $version );
		$this->assertEquals($latestVersion, $version['agenda']['migrationVersion'] );
	}
	
	function test_checkModStatus(){
		
	}
	
	function testHasSomeVersion(){
		
	}

    function testIsActualVersion(){
        $this->obj = new MigrationsMods();
		
		$this->obj->updateMigration("agenda");
		$result = $this->obj->isActualVersion("agenda");
		$this->assertTrue($result);
    }


}
?>