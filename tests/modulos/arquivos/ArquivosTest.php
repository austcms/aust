<?php
require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/config/variables.php';
#####################################

class ArquivosTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){

		installModule('privilegios');

		$moduleName = 'arquivos';
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
        ModulesManager::getInstance()->configuraModulo($param);
		
        /*
         * MÓDULOS ATUAL
         *
         * Diretório do módulo
         */
        $this->mod = 'arquivos';

        /*
         * Informações de conexão com banco de dados
         */

        include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
        include_once MODULES_DIR.$this->mod.'/'.$modInfo['className'].'.php';
        
        $this->obj = new $modInfo['className'];//new $modInfo['className']();
        $this->obj->testMode = true;

    }

    function testParseURL(){
        $this->assertEquals('http://localhost/testes.php',
                $this->obj->parseUrl('http://localhost/testes/../testes.php') );

        $this->assertEquals('http://localhost/testes/testes.php',
                $this->obj->parseUrl('http://localhost/testes/./testes.php') );

        $this->assertEquals('localhost/testes/testes.php',
                $this->obj->parseUrl('localhost//testes/./testes.php') );
    }

    function testGetSystemUrl(){
        $this->assertEquals(getcwd().'/file/path', $this->obj->_getSystemUrl('/file/path'));
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

    /*
     * Não testa o upload propriamente disto, pois para isto é necessário
     * uma requisição POST.
     */
    function testUploadFile(){

        $fileDir[] = 'tests/test_files/test_file.gif';
        $files = array(
            'file1' => array(
                'name' => 'up_'.basename($fileDir[0]),
                'size' => filesize($fileDir[0]),
                'type' => mime_content_type($fileDir[0]),
                'tmp_name' => getcwd().'/'.$fileDir[0],
                'error' => 0,
            ),
        );
        $this->assertType('integer', $files['file1']['size']);

        $this->obj->uploadSubDir = 'tests/'.$this->obj->uploadSubDir;
        $this->obj->uploadFile($files);
        /*
         * Deve ocorrer um erro
         */
        $this->assertEquals('upload_error', $this->obj->status['uploadFile'][ $files['file1']['name'] ], 'Status do erro: '.$this->obj->status['uploadFile'][ $files['file1']['name'] ] );

        $this->assertFileNotExists( $this->obj->uploadSubDir.'test_file.gif' );
    }

    function testAjustFilename(){
        $this->assertEquals('meu_arquivo.test', $this->obj->_adjustFilename('meu_arquivo.test', false));
        $this->assertEquals('meu_arquivo.test', $this->obj->_adjustFilename('Méú_ârquivo.TEsT', false));
        $this->assertRegExp("/.*_meu_arquivo.test/", $this->obj->_adjustFilename('Méú_ârquivo.TEsT', true));
    }

    function testGetExtension(){
        $this->assertEquals( 'test', $this->obj->getExtension('meu_arquivo.test') );
        $this->assertFalse( $this->obj->getExtension('meu_arquivo') );
    }

    /*
     * CRUD
     */

    function testGenerateSqlFromForm(){
        $post = array(
            //'w' => '1',
            'naocampo' => 'naocampo',
            'frmcampo1' => 'valor1',
        );

        /*
         * INSERT sql
         */
        $this->assertEquals('arquivos', $this->obj->useThisTable() );
        $sql = $this->obj->generateSqlFromForm($post, 'new');
        $sql = preg_replace('/\n|\t/Us', "", $sql);
        $sql = preg_replace('/\s{2,}/s', " ", $sql);
        $this->assertEquals( trim("INSERT INTO arquivos (campo1) VALUES ('valor1')"),
                trim($sql) );

        /*
         * UPDATE sql
         */
        $post['w'] = 1;
        $sql = $this->obj->generateSqlFromForm($post, 'edit');
        $sql = preg_replace('/\n|\t/Us', "", $sql);
        $sql = preg_replace('/\s{2,}/s', " ", $sql);
        $this->assertEquals( trim("UPDATE arquivos SET campo1='valor1' WHERE id='1'"),
                trim($sql) );

    }

    function testSaveAndLoad(){
        /*
         * Salva um arquivo sem upload
         */

        $data = array(
            'method' => 'create',
            'w' => '',
            'aust_node' => '777',
            'frmadmin_id' => '1',
            'frmcategoria_id' => '777',
            'frmtitulo' => 'arquivo_de_teste_unitario',
            'embed' => array(
                '0' => array(
                    'className' => 'Privilegios',
                    'dir' => MODULES_DIR.'privilegios',
                    'privilegio' => '1',
                    'data' => array(
                        'privid' => array(
                            '0' => '2',
                        )
                    )
                )
            )
        );

        $this->assertTrue( $this->obj->save($data, array()) );

        /**
         * Verifica se realmente salvou os dados
         *
         *      Arquivos -> Privilégios
         */
            $sql = "SELECT id FROM arquivos WHERE titulo='arquivo_de_teste_unitario' AND categoria_id='777'";
            $this->assertArrayHasKey(0,
                    $this->obj->connection->query($sql), 'Não salvou o arquivo'
                );
            $lastInsertId = $this->obj->w;
            $sqlPriv = "SELECT id FROM privilegio_target WHERE target_id='$lastInsertId' AND privilegio_id='2' AND target_table='arquivos'";
            $this->assertArrayHasKey(0,
                $this->obj->connection->query($sqlPriv) , 'Não salvou privilégio'
            );

        /**
         * Exclui dados do DB
         */
        $this->obj->connection->query("DELETE FROM arquivos WHERE titulo='arquivo_de_teste_unitario' AND categoria_id='777'");
        $this->obj->connection->query("DELETE FROM privilegio_target WHERE target_id='$lastInsertId' AND privilegio_id='2' AND target_table='arquivos'");

    }

    function testLoadSql(){
        function testLoadSql(){
            $this->assertType('string', $this->obj->loadSql() );
            $this->assertType('string', $this->obj->loadSql(
                        array(
                            'resultadosPorPagina' => 0,
                        ))
                    );
        }
    }

}
?>