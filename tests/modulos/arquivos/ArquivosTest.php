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
        /*
         * MÓDULOS ATUAL
         *
         * Diretório do módulo
         */
        $mod = 'arquivos';

        /*
         * Informações de conexão com banco de dados
         */

        include 'modulos/'.$mod.'/'.MOD_CONFIG;
        include_once 'modulos/'.$mod.'/'.$modInfo['className'].'.php';
        
        $this->obj = new $modInfo['className'];//new $modInfo['className']();

    }

    function testParseURL(){
        $this->assertEquals('http://localhost/testes.php',
                $this->obj->parseUrl('http://localhost/testes/../testes.php') );
        $this->assertEquals('http://localhost/testes/testes.php',
                $this->obj->parseUrl('http://localhost/testes/./testes.php') );
        $this->assertEquals('localhost//testes/testes.php',
                $this->obj->parseUrl('localhost//testes/./testes.php') );
    }

    function testGetSystemUrl(){
        $this->assertEquals(getcwd().'/file/path', $this->obj->_getSystemUrl('/file/path'));
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
        $this->assertNull( $this->obj->status['uploadFile'][ $files['file1']['name'] ], 'Status do erro: '.$this->obj->status['uploadFile'][ $files['file1']['name'] ] );

        $this->assertFileNotExists( $this->obj->uploadSubDir.'test_file.gif' );
    }

    function testAjustFilename(){
        $this->assertEquals('meu_arquivo.test', $this->obj->_adjustFilename('meu_arquivo.test', false));
        $this->assertEquals('meu_arquivo.test', $this->obj->_adjustFilename('Méú_ârquivo.TEsT', false));
        $this->assertRegExp("/.*_meu_arquivo.test/", $this->obj->_adjustFilename('Méú_ârquivo.TEsT', true));
    }

    function testSave(){

    }

    function testLoad(){

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

    function testDelete(){

    }

}
?>