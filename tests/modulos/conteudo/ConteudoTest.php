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
        $this->mod = 'conteudo';

        /*
         * Informações de conexão com banco de dados
         */

        include 'modulos/'.$this->mod.'/'.MOD_CONFIG;
        include_once 'modulos/'.$this->mod.'/'.$modInfo['className'].'.php';

        $this->obj = new $modInfo['className'];//new $modInfo['className']();
    }

	function test_LimitSql(){
		
		// test #1.1
			$params = array(
				'page' => 1,
				'limit' => 20,
			);
			$this->assertEquals(' LIMIT 0,20', $this->obj->_limitSql($params), 'test #1.1' );
		// test #1.2
			$params = array(
				'page' => 3,
				'limit' => 20,
			);
			$this->assertEquals(' LIMIT 40,20', $this->obj->_limitSql($params), 'test #1.2' );
		// test #1.3
			$params = array(
				'page' => -1,
				'limit' => 50,
			);
			$this->assertEquals(' LIMIT 0,50', $this->obj->_limitSql($params), 'test #1.3' );
			
		// test #1.4
			$params = array(
			);
			$this->assertEquals(' LIMIT 0,'.$this->obj->defaultLimit, $this->obj->_limitSql($params), 'test #1.4' );

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
        $sql = $this->obj->loadSql( array('page'=>3, 'id'=>'1', 'austNode' => array('3'=>'','4'=>'')) );
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
            'method' => 'criar',
            'frmadddate' => '2010-02-12 19:30:42',
            'frmautor' => '1',
            'w' => '',
            'aust_node' => '2',
            'frmcategoria' => '777',
            'frmtitulo' => 'teste7777',
            'frmtexto' => 'Esta notícia foi inserida via teste unitário',
            'contentTable' => 'textos',
            'submit' => 'Enviar!',
        );

        $this->assertTrue( $this->obj->save($params) );

        $params = array(
            'titulo' => 'teste7777',
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

    function testSimplesLoads(){

        $sql = "INSERT INTO textos (titulo, categoria) VALUES ('teste7777_777','777')";
        $this->obj->connection->exec($sql);
        $catLastInsertId = $this->obj->connection->lastInsertId();

        $load = $this->obj->load($catLastInsertId);
        $this->assertArrayHasKey(0,
                $load,
                "Não carregou por id. \n".$this->obj->lastSql
            );

        $this->assertArrayHasKey(0,
                $this->obj->load( array('austNode' => '777') ),
                'Não carregou por aust_node.'
            );
        
        $this->obj->connection->query("DELETE FROM textos WHERE titulo='teste7777_777'");


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

    function testDelete(){

        $params = array(
            'method' => 'criar',
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
            'austNode' => array($catLastInsertId=>''),
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

    // alguns campos configurados não podem ser vazios.
    function testReplaceFieldsValueIfEmpty(){

        /*
         * Inserir dados no DB para testes.
         */
        $data = array(
            'method' => 'create',
            'w' => '',
            'aust_node' => '7777',
            'frmcategoria' =>  '7777',
            'frmtitulo' => '',
            'frmtexto' => 'teste7777',
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

        $params = array(
            'austNode' => array('7777'=>''),
        );
        $query = $this->obj->load($params);

        $this->obj->connection->exec("DELETE FROM textos WHERE texto='teste7777' OR categoria='7777'");

        /*
         * Análise do Teste
         */
        $config = $this->obj->loadConfig();
        if( !empty($config['replaceFieldsValueIfEmpty']) ){

			$query = $this->obj->replaceFieldsValueIfEmpty($query);
            foreach( $query as $value ){

                foreach( $config['replaceFieldsValueIfEmpty'] as $field=>$fieldRule ){
                    //var_dump($query);
                    if( empty($value[$field]) ){
                        $this->fail('Resultado da query tem um campo vazio ('.$field.'), replaceFieldsValueIfEmpty() não funcionando.');
                    }

                }
            }
        }
        //$this->assertArrayHasKey(0, $result, "Module::load() não funcionando" );
    }

	/*
	 *
	 * Carrega configurações de módulos e campos (quando existem)
	 *
	 */
    function testLoadModConf(){
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $sql = "INSERT INTO config
                    (tipo,local,nome,propriedade,valor)
                VALUES
                    ('mod_conf','777','teste7777','working_test','1')
                ";
        $this->obj->connection->query($sql);
        $catLastInsertId = $this->obj->connection->lastInsertId();

        $this->obj->config = array(
            'configurations' => array(
                'working_test' => array(
                    "value" => "",
                    "label" => "Working?",
                    "inputType" => "checkbox",
                ),
	            'working_test2' => array(
	                "value" => "",
	                "label" => "Working?",
	                "inputType" => "checkbox",
	            ),
            ),
			'field_configurations' => array(
			    'teste' => array(
					'field_type' => 'image',
			        "value" => "",
			        "label" => "Working?",
			        "inputType" => "checkbox",
			    ),
			)
        );
		
		/* MODULE */
        /* start test #1 */
            $result = $this->obj->loadModConf(777);
            $this->assertArrayHasKey(
                    'working_test',
                    $result,
                    'Teste #1.1 falhou');

            $this->assertEquals(
                    '1',
                    $result['working_test']['value'],
                    'Teste #1.2 falhou');
            $this->assertEquals(
                    'checkbox',
                    $result['working_test']['inputType'],
                    'Teste #1.3 falhou');
            $this->assertArrayHasKey(
                    'working_test2',
                    $result,
                    'Teste #1.4 falhou');

        /* start test #2 */
            $tmpAustNode = $this->obj->austNode;
            $this->obj->austNode = 777;

            $this->assertArrayHasKey(
                    'working_test',
                    $this->obj->loadModConf(),
                    'Teste #2.1 falhou');
            $this->assertArrayHasKey(
                    'inputType',
                    $this->obj->loadModConf('working_test'),
                    'Teste #2.2 falhou');
            $this->assertEquals(
                    'checkbox',
                    $result['working_test']['inputType'],
                    'Teste #2.3 falhou');

            $this->obj->austNode = $tmpAustNode;

        /* test #3 */
            $this->assertEquals('1', $this->obj->getStructureConfig('working_test'));
            $this->assertArrayHasKey(
                    'id',
                    $this->obj->getStructureConfig('working_test', false)
                );

        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
    }

	/*
	 * verifica se todas as configurações do arquivo config.php existem no método
	 * loadModConf()
	 */
	function testConfigurationsExists(){
		
        include 'modulos/'.$this->mod.'/'.MOD_CONFIG;
		$configurations = $this->obj->loadModConf();
		foreach( $modInfo['configurations'] as $key=>$value ){
			$this->assertArrayHasKey($key, $configurations);
		}
	}


    function testGetGeneratedUrl(){

        $params = array(
            'method' => 'criar',
            'frmadddate' => '2010-02-12 19:30:42',
            'frmautor' => '1',
            'w' => '',
            'frmcategoria' => '777',
            'frmtitulo' => 'testGetGeneratedUrl',
            'frmtexto' => 'Esta notícia foi inserida via teste unitário',
            'contentTable' => 'textos',
            'submit' => 'Enviar!',
        );

        $this->obj->save($params);
        $lastId = $this->obj->connection->lastInsertId();
        $this->obj->fieldsToLoad = "*";
        $this->obj->load($lastId);

        /* test #1 */
            $str = "http://mywebsite.com/news/%id/%title_encoded";
            $this->obj->structureConfig = array(
                'generate_preview_url' => array(
                    "valor" => $str,
                    "label" => "Generate Preview?",
                    "inputType" => "text",
                ),
            );
            $this->obj->austNode = 777;

            $this->obj->w = null;
            $this->assertFalse($this->obj->getGeneratedUrl() );

            $this->obj->w = $lastId;
            $genUrl = $this->obj->getGeneratedUrl();
            $this->assertEquals(
                    "http://mywebsite.com/news/".$lastId."/testgetgeneratedurl",
                    $genUrl,
                    "test #1.1"
                );

        $this->obj->connection->query("DELETE FROM textos WHERE categoria='777' AND titulo='testGetGeneratedUrl'");
    }


}
?>