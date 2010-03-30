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
    public $lastSaveId;
    
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
                        'limit' => 0,
                    ))
                );
        $this->assertEquals('textos', $this->obj->useThisTable() );

        /*
         * Verifica SQL gerado, se é gerado corretamente
         */
        $sql = $this->obj->loadSql( array('') );
        $sql = preg_replace('/\n|\t/Us', "", preg_replace('/\s{2,}/s', " ", $sql));
        $this->assertEquals( trim("SELECT id, titulo, visitantes, categoria AS cat, ".
                        "DATE_FORMAT(".$this->obj->date['created_on'].", '".$this->obj->date['standardFormat']."') as adddate, ".
                        "(SELECT nome FROM categorias AS c WHERE id=cat ) AS node ".
                        "FROM textos WHERE 1=1 ".
                        "ORDER BY id DESC ".
                        "LIMIT 0,25"),
                        trim($sql) );

        unset($sql);
        $sql = $this->obj->loadSql( array('page'=>3, 'id'=>'1') );
        $sql = preg_replace('/\n|\t/Us', "", preg_replace('/\s{2,}/s', " ", $sql));
        $this->assertEquals( trim("SELECT id, titulo, visitantes, categoria AS cat, ".
                        "DATE_FORMAT(".$this->obj->date['created_on'].", '".$this->obj->date['standardFormat']."') as adddate, ".
                        "(SELECT nome FROM categorias AS c WHERE id=cat ) AS node ".
                        "FROM textos WHERE 1=1 AND id='1' ".
                        "ORDER BY id DESC ".
                        "LIMIT 50,25"),
                        trim($sql) );

        unset($sql);
        $sql = $this->obj->loadSql( array('page'=>3, 'id'=>'1', 'austNode' => array('3'=>'categoria1','4'=>'categoria1')) );
        $sql = preg_replace('/\n|\t/Us', "", preg_replace('/\s{2,}/s', " ", $sql));
        $this->assertEquals( trim("SELECT id, titulo, visitantes, categoria AS cat, ".
                        "DATE_FORMAT(".$this->obj->date['created_on'].", '".$this->obj->date['standardFormat']."') as adddate, ".
                        "(SELECT nome FROM categorias AS c WHERE id=cat ) AS node ".
                        "FROM textos WHERE 1=1 AND id='1' AND categoria IN ('3','4') ".
                        "ORDER BY id DESC ".
                        "LIMIT 50,25"),
                        trim($sql) );
    }

    function testUseThisTable(){
        $this->assertEquals('textos', $this->obj->useThisTable() );
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
            'frmtitulo' => 'Notícia de teste123456789',
            'frmtexto' => 'Esta notícia foi inserida via teste unitário',
            'contentTable' => 'textos',
            'submit' => 'Enviar!',
        );

        $this->assertTrue( $this->obj->save($params) );

        $params = array(
            'titulo' => 'Notícia de teste123456789',
        );

        /**
         * Verifica se realmente salvou os dados
         *
         *      Conteudo
         */
            $sql = "SELECT id FROM textos WHERE titulo='".$params['titulo']."'";
            $this->assertArrayHasKey(0,
                    $this->obj->connection->query($sql), 'Não salvou o conteúdo'
                );

        /**
         * A exclusão dos registros acontece dentro de outro teste
         * abaixo.
         */

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

    function test_OrganizesLoadedData(){
        $params = array(
            array(
                'id' => '5',
                'titulo' => 'titulo'
            ),
            array(
                'titulo' => 'titulo2'
            )
        );

        $this->assertEquals(
                array(
                    '5' => array(
                        'id' => '5',
                        'titulo' => 'titulo'
                    ),
                    '6' => array(
                        'titulo' => 'titulo2'
                    )
                ),
                $this->obj->_organizesLoadedData($params)
            );
        $this->assertEquals(
                array(
                    '5'
                ),
                $this->obj->loadedIds
            );

    }

    /**
     * @depends testSave
     */
    function testDelete(){

        $params = array(
            'metodo' => 'criar',
            'frmadddate' => '2010-02-12 19:30:42',
            'frmautor' => '1',
            'w' => '',
            'aust_node' => '2',
            'frmcategoria' => '4',
            'frmtitulo' => 'Notícia de teste123456789',
            'frmtexto' => 'Esta notícia foi inserida via teste unitário',
            'contentTable' => 'textos',
            'submit' => 'Enviar!',
        );

        $this->assertTrue( $this->obj->save($params) );

        $this->assertTrue( $this->obj->delete($this->obj->connection->lastInsertId()) );

        //$params = array(
//            'titulo' => 'Notícia de teste123456789',
//        );
        
        //$this->assertGreaterThan(0, $this->obj->delete('textos', $params) );
        //$this->assertFalse( $this->obj->delete('textos', array()) );

        //$this->assertEquals( 0, $this->obj->delete('textos', array('titulo' => '123890567')) );
    }



    function testSaveAndLoadWithEmbedData(){
        /*
         * Salva dados no DB para que se possa testar
         */

        /*
         * Simula
         */
        $sql = "INSERT INTO categorias (nome,tipo) VALUES ('teste7777','privilegios')";
        $this->obj->connection->query($sql);
        $catLastInsertId = $this->obj->connection->lastInsertId();
        $sql = "INSERT INTO modulos_conf (categoria_id,tipo,propriedade,valor) VALUES ('$catLastInsertId','relacionamentos','id','teste7777')";
        $this->obj->connection->query($sql);
        $modConfLastInsertId = $this->obj->connection->lastInsertId();

        /*
         * Salva um arquivo sem upload
         */
        $data = array(
            'method' => 'create',
            'w' => '',
            'aust_node' => $catLastInsertId,
            'frmcategoria' => $catLastInsertId,
            'frmtitulo' => 'teste7777',
            'embed' => array(
                '0' => array(
                    'className' => 'Privilegios',
                    'dir' => 'modulos/privilegios',
                    'privilegio' => '1',
                    'data' => array(
                        'privid' => array(
                            '0' => '2',
                            '1' => '3',
                        )
                    )
                )
            )
        );

        $this->assertTrue( $this->obj->save($data, array()) );



        /**
         * Simula
         *
         * Verifica se realmente salvou os dados
         *
         *      Arquivos -> Privilégios
         */
            $sql = "SELECT id FROM textos WHERE titulo='teste7777'";
            $this->assertArrayHasKey(0,
                    $this->obj->connection->query($sql),
                    'Não salvou o arquivo.'
                );
            $lastInsertId = $this->obj->w;
            $sqlPriv = "SELECT id FROM privilegio_target WHERE target_id='$lastInsertId' AND privilegio_id='2' AND target_table='textos'";
            $this->assertArrayHasKey(0,
                $this->obj->connection->query($sqlPriv) , 'Não salvou privilégio'
            );

        /*
         * VERIFICA LOAD()
         */
        $params = array(
            'austNode' => array($catLastInsertId => 'teste7777'),
        );

        $result = $this->obj->load($params);

        /**
         * Exclui dados do DB antes dos testes, senão os dados ficam todos
         * no DB, pois com um erro acima este código não é rodado.
         */
        $this->obj->connection->query("DELETE FROM textos WHERE titulo='teste7777'");
        $this->obj->connection->query("DELETE FROM categorias WHERE nome='teste7777'");
        $this->obj->connection->query("DELETE FROM privilegio_target WHERE target_id='$lastInsertId' AND (privilegio_id IN ('2','3')) AND target_table='textos'");
        $sql = "DELETE FROM modulos_conf WHERE id='$modConfLastInsertId' AND valor='teste7777'";
        $this->obj->connection->query($sql);

        //var_dump($result);
        $this->assertArrayHasKey(0, $result, "Module::load() não funcionando" );
        $this->assertArrayHasKey('Privilegios', reset($result), "Dados embed de Module::load() não retornam" );



        //$this->assertArrayHasKey(0, $this->obj->load() );
    }

}
?>