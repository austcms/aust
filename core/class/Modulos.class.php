<?php
/**
 * Classe dos módulos
 *
 * Contém funcionalidades genéricas do módulos, funcionalidades estas presentes
 * em todos os módulos.
 *
 * @package Classes
 * @name Módulos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1.1
 * @since v0.1.5, 30/05/2009
 */
class Modulos {
/**
 * TABELAS
 */
// TABELA
    protected $db_tabelas;
    protected $sql_das_tabelas;
    protected $sql_registros;
    public $tabela_criar;

    protected $modDbSchema;
    /**
     * VARIÁVEIS DE AMBIENTE
     *
     * Conexão com banco de dados, sistema Aust, entre outros
     */
    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    public $conexao;
    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    public $aust;
    /**
     *
     * @var class Configurações do módulo
     */
    public $config;
    /**
     *
     * @var string Diretório onde estão os módulos
     */
    const MOD_DIR = 'modulos/';
    /**
     *
     * @param array $param:
     *      'conexao': Contém a conexão universal
     */
    function __construct($param) {
        global $aust;
        $this->aust = $aust;

        /**
         * Pega a string global que diz qual é o charset do projeto
         */
        global $aust_charset;

        /**
         * Ajusta a conexao para o módulo
         */
        if( !empty($param['conexao']) ) {
            $this->conexao = $param['conexao'];
        }

        /**
         * Grava configurações do módulo no objeto
         */
        if( !empty($param['config']) ) {
            $this->config = $param['config'];
        }

        /**
         * modDbSchema: Grava o schema se for passado como argumento
         */
        if( !empty($param['modDbSchema']) ) {
            $this->modDbSchema = $param['modDbSchema'];

        }
    }

    /*
     * MÈTODOS DE SUPORTE
     */


    /**
     * trataImagem
     *
     * Trata uma imagem
     *
     * @param array $files O mesmo $_FILE vindo de um formulário
     * @param string $width Valor padrão de largura
     * @param string $height Valor padrão de altura
     * @return array
     */
    function trataImagem($files, $width = "1024", $height = "768") {

        /*
         * Toma dados de $files
         */
        $frmarquivo = $files['tmp_name'];
        $frmarquivo_name = $files['name'];
        $frmarquivo_type = $files['type'];

        /*
         * Abre o arquivo e tomas as informações
         */
        $fppeq = fopen($frmarquivo,"rb");
        $arquivo = fread($fppeq, filesize($frmarquivo));
        fclose($fppeq);

        /*
         * Cria a imagem e toma suas proporções
         */
        $im = imagecreatefromstring($arquivo); //criar uma amostra da imagem original
        $largurao = imagesx($im);// pegar a largura da amostra
        $alturao = imagesy($im);// pegar a altura da amostra

        /*
         * Configura o tamanho da nova imagem
         */
        if($largurao > $width)
            $largurad = $width;
        else
            $largurad = $largurao; // definir a altura da miniatura em px

        $alturad = ($alturao*$largurad)/$largurao; // calcula a largura da imagem a partir da altura da miniatura
        $nova = imagecreatetruecolor($largurad,$alturad); // criar uma imagem em branco
        //imagecopyresized($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);
        imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

        ob_start();
        imagejpeg($nova, '', 100);
        $mynewimage = ob_get_contents();
        ob_end_clean();

        /*
         * Prepara dados resultados para retornar
         */
        imagedestroy($nova);

        $result["filesize"] = strlen($mynewimage);
        //$result["filedata"] = addslashes($mynewimage);
        $result["filedata"] = $mynewimage;
        $result["filename"] = $frmarquivo_name;
        $result["filetype"] = $frmarquivo_type;

        return $result;

    }


    /**
     * VERIFICAÇÕES
     */

    /**
     * verificaInstalacaoTabelas()
     *
     * @return <bool>
     */
    public function verificaInstalacaoTabelas() {
        $schema = $this->modDbSchema;
        foreach( $schema as $tabela=>$valor ) {
            $sql = "DESCRIBE ". $tabela;
            $query = $this->conexao->query($sql);
            if(!$query) {
                return false;
                //$erro[] = $tabela;
            } else {
                return true;
                //$sucesso[] = $tabela;
            }
        }
    }

    /**
     * verificaInstalacaoRegistro()
     *
     * @return <bool>
     */
    public function verificaInstalacaoRegistro($options = array()) {

        if( !empty($options["pasta"]) ){
            $where = "pasta='".$options["pasta"]."'";
        }

        $sql = "SELECT id FROM modulos WHERE ".$where;
        $query = $this->conexao->query($sql);
        if( !$query ){
            return false;
        } else {
            return true;
        }
    }

    /**
     * saveModConf()
     *
     * Salva configurações de um módulo no banco de dados automaticamente.
     *
     * Para exemplo de como usar, veja o código de configuração do módulo textos
     *
     * @param array $params
     * @return bool
     */
    public function saveModConf($params) {

        global $administrador;

        /*
         * Se for para configurar e tiver dados enviados
         */
        if( !empty($params['conf_type'])
            AND $params['conf_type'] == "mod_conf"
            AND !empty($params['data'])
            AND !empty($params['aust_node']) ) {

            $data = $params["data"];
            $this->conexao->exec("DELETE FROM config WHERE tipo='mod_conf' AND local='".$params["aust_node"]."'");
            foreach( $data as $propriedade=>$valor ) {

                $paramsToSave = array(
                    "table" => "config",
                    "data" => array(
                    "tipo" => "mod_conf",
                    "local" => $params["aust_node"],
                    "autor" => $administrador->LeRegistro("id"),
                    "propriedade" => $propriedade,
                    "valor" => $valor
                    )
                );
                $this->conexao->exec($this->conexao->saveSql($paramsToSave));
            }
        }
        return true;
    }

    function loadModConf($params) {
        $sql = "SELECT * FROM config WHERE tipo='mod_conf' AND local='".$params["aust_node"]."' LIMIT 200";

        $queryTmp = $this->conexao->query($sql, "ASSOC");

        foreach($queryTmp as $valor) {
            $query[$valor["propriedade"]] = $valor;
        }
        return $query;
    }

    /**
     * RESPONSER
     *
     * Carrega conteúdo para leitura externa. Retorna, geralmente, em array.
     *
     * @global Aust $aust
     * @return array
     */
    public function retornaResumo() {
        global $aust;

        /**
         * Configurações específicas deste módulo
         */
        $moduloConf = $this->config['arquitetura'];

        /**
         * Pega as estruturas deste módulo através do método a seguir. Em
         * $param['where'] tem-se uma parte do código SQL necessário para tal.
         */
        $param = array(
            "where" => "tipo='textos' and classe='estrutura'"
        );

        $estruturas = $aust->LeEstruturasParaArray($param);
        /**
         * Se há estruturas instaladas, rodará uma por uma tomando os conteúdos
         */
        if(!empty($estruturas)) {

        /**
         * Se o retorno estiver configurado para array ou vazio, retorna array.
         * Alguns módulos retornam textos diretamente
         */
            if( empty($moduloConf['returnTipo'])
                OR $moduloConf['returnTipo'] == 'array' ) {

            /**
             * Cada estrutura possui várias categorias.
             *
             * Vamos:
             *      - um loop por cada estrutura
             *      - um loop por cada categoria de cada estrutura
             *
             * O resultado será todo guardado em $conteudo
             */
                foreach($estruturas as $chave=>$valor) {

                    $response['intro'] = 'A seguir, os últimos conteúdos.';
                    $categorias = $aust->categoriasFilhas( array( 'pai' => $valor['id'] ) );

                    if(!empty($categorias)) {
                    /**
                     * Pega cada índice contendo id das categorias da respectiva estrutura
                     */
                        foreach($categorias as $cChave=>$cValor) {
                            $tempCategorias[] = $cChave;
                        }

                        /**
                         * Monta SQL
                         *
                         * Monta cláusula WHERE com as categorias selecionadas e
                         * desmancha $tempCategorias
                         */
                        $sql = "SELECT
                                    id, titulo
                                FROM
                                    ".$moduloConf['table']."
                                WHERE ".$moduloConf['foreignKey'] . " IN ('" . implode("','", $tempCategorias) ."')
                                ORDER BY id DESC
                                LIMIT 0,4
                                ";

                        $result = $this->conexao->query($sql);

                        foreach($result as $dados) {
                        /**
                         * Toma os dados do DB e os guarda
                         */
                            $tempResponse[] = $dados;
                        }

                        /**
                         * Organiza array que vai ser retornada nesta função
                         */
                        $response[$valor['id']]['titulo'] = $valor['nome'];
                        $response[$valor['id']]['conteudo'] = (empty($tempResponse)) ? array() : $tempResponse;

                        unset($tempCategorias);
                        unset($tempResponse);
                    }
                }
            }
        }
        return $response = (empty($response)) ? array() : $response;
    }

    /**
     * Método responsável por retornar o nome de arquivos que representar
     * determinada interface ou funcionalidade.
     *
     * Retorna o diretório/nome do arquivo que deve ser carregado.
     *
     * @param int $aust_node ID da estrutura a ser carregada
     * @param string $type 'Alias' representando o que é necessário carregar
     * @return string
     */
    public function loadInterface($aust_node = '0', $type = '') {
        $modDir = $this->aust->LeModuloDaEstrutura($aust_node);

        if( $type == 'list' ) {
            if ( is_file( self::MOD_DIR . $modDir . '/view/listar.php' ) ) {
                return self::MOD_DIR . $modDir . '/view/listar.php';
            } else {
                return 'conteudo.inc/listar.inc.php';
            }
        }

        elseif( $type == 'new' ) {
            if ( is_file( self::MOD_DIR . $modDir . '/view/form.php' ) ) {
                return self::MOD_DIR . $modDir . '/view/form.php';
            } else {
            /**
             * @todo - o que fazer se o arquivo não for encontrado
             */
                return 'conteudo.inc/editar.inc.php';
            }
        }

        elseif( $type == 'edit' ) {
            if ( is_file( self::MOD_DIR . $modDir . '/view/edit.php' ) ) {
                return self::MOD_DIR . $modDir . '/view/edit.php';
            } elseif ( is_file( self::MOD_DIR . $modDir . '/view/form.php' ) ) {
                return self::MOD_DIR . $modDir . '/view/form.php';
            } else {
                return 'conteudo.inc/edit.inc.php';
            }
        }

        elseif( $type == 'save' ) {
            if ( is_file( self::MOD_DIR . $modDir . '/view/save.php' ) ) {
                return self::MOD_DIR . $modDir . '/view/save.php';
            } else {
            /**
             * @todo - o que fazer se o arquivo não for encontrado
             */
                return 'conteudo.inc/edit.inc.php';
            }
        }

    }
















    /**
     * @todo - ajustar código para baixo
     */
    /**
     * Salva dados sobre o módulo na base de dados.
     *
     * Usado após a criação das tabelas do módulo.
     *
     * @param array $param
     * @return bool
     */
    function configuraModulo($param) {

    /**
     * Ajusta cada variável enviada como parâmetro
     */
    /**
     * $tipo:
     */
        $tipo = (empty($param['tipo'])) ? '' : $param['tipo'];
        /**
         * $chave:
         */
        $chave = (empty($param['chave'])) ? '' : $param['chave'];
        /**
         * $valor:
         */
        $valor = (empty($param['valor'])) ? '' : $param['valor'];
        /**
         * $pasta:
         */
        $pasta = (empty($param['pasta'])) ? '' : $param['pasta'];
        /**
         * $modInfo:
         */
        $modInfo = (empty($param['modInfo'])) ? '' : $param['modInfo'];
        /**
         * $autor:
         */
        $autor = (empty($param['autor'])) ? '' : $param['autor'];



        $sql = "INSERT INTO
                    modulos
                        (tipo,chave,valor,pasta,nome,descricao,embed,embedownform,somenteestrutura,autor)
                VALUES
                    ('$tipo','$chave','$valor','$pasta','".$modInfo['nome']."','".$modInfo['descricao']."','".$modInfo['embed']."','".$modInfo['embedownform']."','".$modInfo['somenteestrutura']."','$autor')
            ";
        if($this->conexao->exec($sql, 'CREATE_TABLE')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     *
     *	funções de verificação ou leitura
     *
     */
    function leModulos() {

        $modulos = $this->conexao->query("SELECT * FROM modulos");
        //pr($modulos);
        return $modulos;

        $diretorio = 'modulos/'; // pega o endereço do diretório
        foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
            if (is_dir ($pastas)) {
                if( is_file($pastas.'/'.MOD_CONFIG )) {
                    if( include($pastas.'/'.MOD_CONFIG )) {
                    //include_once($pastas.'/index.php');
                        if(!empty($modInfo['nome'])) {
                            $str = $result_format;
                            $str = str_replace("&%nome", $modInfo['nome'] , $str);
                            $str = str_replace("&%descricao", $modInfo['descricao'], $str);
                            $str = str_replace("&%pasta", str_replace($diretorio,"",$pastas), $str);
                            $str = str_replace("&%diretorio", str_replace($diretorio,"",$pastas), $str);
                            echo $str;
                            if($c < $t-1) {
                                echo $chardivisor;
                            } else {
                                echo $charend;
                            }
                            $c++;
                        }
                        unset($modulo);
                    }

                }
            }
        }
    }


    // retorna o nome da tabela da estrutura
    function LeTabelaDaEstrutura($param='') {
        return $this->tabela_criar;
    }

    /*
     * Retorna os módulos que tem a propriedade Embed como TRUE
     */
    function LeModulosEmbed() {
        $sql = "SELECT
                    DISTINCT pasta, nome, chave, valor
                FROM
                    modulos
                WHERE
                    embed='1'
                ";
        $query = $this->conexao->query($sql);
        $i = 0;
        $return = '';

        foreach($query as $dados) {
            $return[$i]['pasta'] = $dados['pasta'];
            $return[$i]['nome'] = $dados['nome'];
            $return[$i]['chave'] = $dados['chave'];
            $return[$i]['valor'] = $dados['valor'];
            $i++;
        }
        return $return;
    }

    /**
     * Retorna os módulos que tem a propriedade EmbedOwnForm = TRUE
     *
     * EmbedOwnForm significa módulos que vão dentro de formulário de
     * inclusão/edição de conteúdo, exceto aqueles que tem seu próprio formulário
     *
     * @return array Todos os módulos com habilidade Embed
     */

    function LeModulosEmbedOwnForm() {
        $sql = "SELECT
                    DISTINCT pasta, nome, chave, valor
                FROM
                    modulos
                WHERE
                    embedownform='1'
                ";
        $query = $this->conexao->query($sql);

        $return = '';
        $i = 0;
        foreach($query as $dados) {
            $return[$i]['pasta'] = $dados['pasta'];
            $return[$i]['nome'] = $dados['nome'];
            $return[$i]['chave'] = $dados['chave'];
            $return[$i]['valor'] = $dados['valor'];
            $i++;
        }
        return $return;
    }

    /**
     * Retorna somente EmbedOwnForms liberados para serem mostrados
     * na $estrutura indicada.
     *
     * @return array
     */
    function leModulosEmbedOwnFormLiberados($estrutura) {
        $sql = "SELECT
                    id, nome
                FROM
                    modulos_conf
                WHERE
                    valor='".$estrutura."'
                ";
        $query = $this->conexao->query($sql);

        $return = '';
        foreach($query as $dados) {
            $return[] = $dados['nome'];
        }
        return $return;
    }

    /*
     * retorna o nome de cada módulo e suas informações em formato array
     */
    function LeModulosParaArray() {
        $sql = "SELECT
                    DISTINCT pasta, nome, chave, valor
                FROM
                    modulos
                ";
        $query = $this->conexao->query($sql);
        $i = 0;
        foreach($query as $dados) {
            $return[$i]['pasta'] = $dados['pasta'];
            $return[$i]['nome'] = $dados['nome'];
            $return[$i]['chave'] = $dados['chave'];
            $return[$i]['valor'] = $dados['valor'];
            $i++;
        }
        return $return;
    }




    /*
     *
     * TRATAMENTO DE IMAGENS
     *
     */

    function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality) {
        if (empty($src_image) || empty($dst_image)) { return false; }
        if ($quality <= 1) {
            $temp = imagecreatetruecolor ($dst_w + 1, $dst_h + 1);
            imagecopyresized ($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
            imagecopyresized ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
            imagedestroy ($temp);
        } elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
            $tmp_w = $dst_w * $quality;
            $tmp_h = $dst_h * $quality;
            $temp = imagecreatetruecolor ($tmp_w + 1, $tmp_h + 1);
            imagecopyresized ($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
            imagecopyresampled ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
            imagedestroy ($temp);
        } else {
            imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }
        return true;
    }


}


?>