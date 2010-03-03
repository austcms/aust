<?php
require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/config/variables.php';
require_once 'core/libs/functions/func.php';
#####################################

class ConteudoTest extends PHPUnit_Framework_TestCase
{
    public function setUp(){
        /*
         * MÓDULOS ATUAL
         *
         * Diretório do módulo
         */
        $mod = 'conteudo';

        /*
         * Informações de conexão com banco de dados
         */

        include 'modulos/'.$mod.'/'.MOD_CONFIG;
        include_once 'modulos/'.$mod.'/'.$modInfo['className'].'.php';

        $this->obj = new $modInfo['className'];//new $modInfo['className']();
    }

    function testLoadSql(){
        $this->assertType('string', $this->obj->loadSql() );
        $this->assertType('string', $this->obj->loadSql(
                    array(
                        'resultadosPorPagina' => 0,
                    ))
                );
    }

    function testLoadConfig(){
        $this->assertType('array', $this->obj->loadConfig());
        $this->assertArrayHasKey('className', $this->obj->loadConfig());
    }
    /*
     * ESPECÍFICO DO MÓDULO
     */
    /*
     * CRUD
     */

    function testSave(){
        $this->assertFalse( $this->obj->save() );

        $params = array();

        $this->assertFalse( $this->obj->save($params) );

        $params = array(
            'metodo' => 'criar',
            'frmadddate' => '2010-02-12 19:30:42',
            'frmautor' => '1',
            'w' => '',
            'aust_node' => '2',
            'frmcategoria' => '4',
            'frmtitulo' => 'Notícia de teste',
            'frmtexto' => 'Esta notícia foi inserida via teste unitário',
            'contentTable' => 'textos',
            'submit' => 'Enviar!',
        );

        $this->assertTrue( $this->obj->save($params) );

        $params = array(
            'titulo' => 'Notícia de teste',
        );
    }

    function testSaveEmbeddedModules(){

        $params = array(
            'embedModules' => array (
                0 => array(
                    'className' => 'Privilegios',
                    'dir' => 'modulos/privilegios',
                    'privilegio' => '1',
                    'data' => array(
                        'privid' => array(
                            '0' => '1',
                        )
                    )
                )
            ),
            'options' => array(
                'targetTable' => 'textos',
                'w' => '2',
            )
        );

        $this->assertTrue( $this->obj->saveEmbeddedModules($params) );

    }

    /**
     * @depends testSave
     */
    function testDelete(){

        $params = array(
            'titulo' => 'Notícia de teste',
        );
        
        $this->assertGreaterThan(0, $this->obj->delete('textos', $params) );
        $this->assertFalse( $this->obj->delete('textos', array()) );

        $this->assertEquals( 0, $this->obj->delete('textos', array('titulo' => '123890567')) );
    }

    function testLoad(){
        
    }

    function testLoadEmbed(){

    }
}
?>