<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'core/class/SQLObject.class.php';
require_once 'core/class/Conexao.class.php';
require_once 'core/class/Administrador.class.php';
require_once 'core/config/variables.php';
require_once 'core/libs/functions/func.php';


#####################################

require_once 'core/class/Modulos.class.php';


class ConteudoTest extends PHPUnit_Framework_TestCase
{
    public $dbConfig = array();

    public $conexao;

    public $moduleForTesting = 'conteudo';

    public function setUp(){

        /*
         * Informações de conexão com banco de dados
         */
        require 'tests/config/database.php';
        $this->dbConfig = $dbConn;
        
        $this->conexao = new Conexao($this->dbConfig);

        require MODULOS_DIR.$this->moduleForTesting.'/core/config/config.php';
        require_once MODULOS_DIR.$this->moduleForTesting.'/'.$modInfo['className'].'.php';

        $_SESSION['login']['id'] = 1;

        $params = array(
            'conexao' => $this->conexao,
            'user' => new Administrador($this->conexao),
        );
        
        $this->obj = new $modInfo['className']( $params );
    }

    function testGetSQLForListing(){
        $this->assertType('string', $this->obj->getSqlForListing() );
        $this->assertType('string', $this->obj->getSqlForListing(
                    array(
                        'resultadosPorPagina' => 0,
                    ))
                );
    }

    /*
     * ESPECÍFICO DO MÓDULO
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

}
?>