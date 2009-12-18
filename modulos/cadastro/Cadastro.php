<?php
/**
 * Classe do módulo
 *
 * @package Módulos
 * @name Cadastro
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6, 09/07/2009
 */
class Cadastro extends Modulos {

    function __construct($param = ''){


        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);

    }

    /**
     * Retorna todas as informações sobre o cadastro.
     *
     * Pega todas as informações da tabela cadastros_conf onde categorias_id
     * é igual ao austNode especificado.
     *
     * @param int $austNode
     * @return array
     */
    public function pegaInformacoesCadastro( $austNode ){
        /**
         * Busca na tabela cadastros_conf por informações relacionadas ao
         * austNode selecionado.
         */
        $temp = $this->conexao->query(
            "SELECT * FROM cadastros_conf WHERE categorias_id='".$austNode."' ORDER BY ordem ASC",
            PDO::FETCH_ASSOC
        );
        foreach( $temp as $chave=>$valor ){
            $result[ $valor["tipo"] ][ $valor["chave"] ] = $valor;
        }
        return $result;
    }

    /**
     * Retorna informações sobre tipagem física da respectiva
     * tabela.
     *
     * @param array $params
     *      'tabela': qual tabela deve ser analisada
     *      'by': indica qual o índice deve ser usado
     *          ex.: se 'Field', o índice de retorno é o nome do
     *          campo.
     * @return array Retorna as características físicas da tabela
     */
    public function pegaInformacoesTabelaFisica( $params ){
        /**
         * DESCRIBE tabela
         *
         * Toma informações físicas sobre a tabela
         */
        if ( !empty( $params["tabela"] ) ){
            $temp = $this->conexao->query("DESCRIBE ".$params["tabela"], "ASSOC");
        }

        /**
         * $param["by"]
         *
         * Se o resultado deve ser retornado com uma determinada informação
         * como índice.
         */
        if( !empty($params["by"]) ){
            foreach($temp as $chave=>$valor){
                $result[ $valor[ $params["by"] ] ] = $valor;
            }
        } else {
            $result = $temp;
        }

        return $result;
    }







    /**
     * VERIFICAÇÕES E LEITURAS AUTOMÁTICAS DO DB
     */
    
    public function SQLParaListagem($param){
        // configura e ajusta as variáveis
        $categorias = $param['categorias'];
        $metodo = $param['metodo'];
        $w = $param['id'];

        /**
         * Se $categorias estiver vazio (nunca deverá acontecer)
         */
        if(!empty($categorias)){
            $order = ' ORDER BY id ASC';
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor){
                if($c == 0)
                    $where = $where . 'categorias_id=\''.$key.'\'';
                else
                    $where = $where . ' OR categorias_id=\''.$key.'\'';
                $c++;
            }
        }
        
        /**
         *  SQL para verificar na tabela CADASTRO_CONF quais campos existem
         */
        $sql = "SELECT
                    *, categorias_id AS cat,
                    (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node
                FROM
                    ".$this->config["arquitetura"]["table"]." AS conf ".
                $where.
                $order;
        /**
         * Campos carregados
         */
        $result = $this->conexao->query($sql, "ASSOC");

        /**
         * Configurações
         */
        $tP = "tabelaPrincipal";
        /**
         * Monta algumas arrays para montar um novo SQL definitivo
         *
         * $i = int
         */
        $i = 0;
        foreach($result as $dados){

            if ( in_array( $dados['tipo'], array('campo', 'campopw', 'campoarquivo', 'camporelacional_umparaum')) ){

                if($dados['listagem'] > 0 ){

                    if( $dados["especie"] == "relacional_umparaum" ){
                        $leftJoin[ $dados["chave"] ]["ref_tabela"] = $dados["ref_tabela"];
                        $leftJoin[ $dados["chave"] ]["ref_campo"] = $dados["ref_campo"];
                        $leftJoin[ $dados["chave"] ]["campoNome"] = $dados["valor"];
                    } else {
                        $mostrar['valor'][] = $dados['valor'];
                        $mostrar['chave'][] = $tP.".".$dados['chave']." AS '".$dados["valor"]."'";
                    }
                }

                $campos['valor'][] = $dados['valor'];
                $campos['chave'][] = $dados['chave'];

            } else if($dados['tipo'] == 'estrutura' AND $dados['chave'] == 'tabela'){
                $est['tabela'][] = $dados['valor'];
                $est['node'][] = $dados['categorias_id'];
            }
            $i++;
        }
        /**
         * LEFT JOIN?
         */
        if( !empty($leftJoin) ){
            $leftJoinTmp = $leftJoin;
            unset($leftJoin);

            if( is_array($leftJoinTmp) ){

                foreach( $leftJoinTmp as $chave=>$valor ){
                    /*
                     * Se há um LeftJOIN, elimina os campo destes do query
                     * principal
                     */
                    unset($mostrar[$chave]);

                    $refTabela = $valor["ref_tabela"];
                    $refCampo = $valor["ref_campo"];

                    $leftJoinCampos[$chave] = $refTabela.".".$refCampo." AS '".$valor["campoNome"]."'";
                    $leftJoin[ $refTabela ] = "LEFT JOIN ".$refTabela." AS ".$refTabela." ON ".$tP.".".$chave."=".$refTabela.".id";
                }
            }
            $virgula = ",";
        }
        /**
         * Segurança
         */
        else {
            $leftJoinCampos = array();
            $leftJoin = array();
            $virgula = "";
        }
        
        /**
         * Novo SQL
         */
        if( $metodo == "listar" ){

            if( empty($mostrar) ){
                $mostrar = "id,";

            } else {
                $mostrar = implode(",", $mostrar["chave"]).",";

            }

            $sql = "SELECT
                        ".$tP.".id,
                        $mostrar
                        ".implode(", ", $leftJoinCampos).$virgula."
                        ".$tP.".approved AS des_aprovado
                    FROM
                        ".$est["tabela"][0]." AS ".$tP."

                    ".implode(" ", $leftJoin)."

                    ORDER BY
                        ".$tP.".id DESC
                    LIMIT 0,30

                    ";
        } elseif( $metodo == "editar" ){
            $sql = "SELECT
                        id, ".implode(",", $campos["chave"])."
                    FROM
                        ".$est["tabela"][0]."
                    WHERE
                        id=".$w."
                    ";
        }
        
        return $sql;
    } // fim SQLParaListagem()

    /**
     * Função para retonar a tabela de dados de uma estrutra de cadastro
     *
     * @param mixed $param contém o id ou nome da estrutura desejada
     * @return array 
     */
    public function LeTabelaDaEstrutura($param){

        /**
         * $param é uma integer
         */
        if( is_int($param) or $param > 0 ){
            $estrutura = "categorias.id='".$param."'";
        }
        /**
         * $param é uma string
         */
        elseif( is_string($param) ){
            $estrutura = "categorias.nome='".$param."'";
        }

        $sql = "SELECT
                    cadastros_conf.valor AS valor
                FROM
                    cadastros_conf, categorias
                WHERE
                    categorias.id=cadastros_conf.categorias_id AND
                    {$estrutura} AND
                    cadastros_conf.tipo='estrutura' AND
                    cadastros_conf.chave='tabela'
                LIMIT 0,1";
                //echo $sql;
                
        $resultado = $this->conexao->query($sql);
        $dados = $resultado[0];
        return $dados['valor'];
    }

    /*
     * Função para retornar o nome da tabela de dados de uma estrutura da cadastro
     */
    public function LeTabelaDeDados($param){
        if(is_int($param) or $param > 0){
            $estrutura = "categorias.id='".$param."'";
        } elseif(is_string($param)){
            $estrutura = "categorias.nome='".$param."'";
        }

        $sql = "SELECT
                    cadastros_conf.valor AS valor
                FROM
                    cadastros_conf, categorias
                WHERE
                    categorias.id=cadastros_conf.categorias_id AND
                    {$estrutura} AND
                    cadastros_conf.tipo='estrutura' AND
                    cadastros_conf.chave='tabela'
                LIMIT 0,1";
                //echo $sql;
        $mysql = $this->conexao->query($sql);
        $dados = $mysql[0];
        return $dados['valor'];
    }

    /*
     * Cria tabela responsável por guardar arquivos
     */
    function CriaTabelaArquivo($param){
        global $aust_charset;
        if (!empty($aust_charset['db']) and !empty($aust_charset['db_collate'])) {
                $charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
        }

        $sql = "SELECT
                    id
                FROM
                    ".$param['tabela']."_arquivos
                LIMIT 0,1
                ";
        $result = $this->conexao->query($sql);
        if( count($result) == 0 ){
            $sql_arquivos =
                            "CREATE TABLE ".$param['tabela']."_arquivos(
                            id int auto_increment,
                            titulo varchar(120) {$charset},
                            descricao text {$charset},
                            local varchar(80) {$charset},
                            url text {$charset},
                            arquivo_nome varchar(250) {$charset},
                            arquivo_tipo varchar(250) {$charset},
                            arquivo_tamanho varchar(250) {$charset},
                            arquivo_extensao varchar(10) {$charset},
                            tipo varchar(80) {$charset},
                            referencia varchar(120) {$charset},
                            categorias_id int,
                            adddate datetime,
                            autor int,
                            PRIMARY KEY (id),
                            UNIQUE id (id)
                        ) ".$charset;
            if( $this->conexao->exec($sql_arquivos) ){
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return 0;
        }
        return 0;
    }

    /*
     * Le informações do db
     */
    function LeDadosDoDB($tabela, $campo, $valor_condicao, $campo_condicao=''){

        if(empty($campo_condicao)){
            $where = "WHERE id='".$valor_condicao."'";
        } else {
            $where = "WHERE ".$campo_condicao."='".$valor_condicao."'";
        }
        $sql = "SELECT
                    ".$campo."
                FROM
                    ".$tabela."
                ".$where."
                LIMIT 0,1
                ";
        //echo $sql;
        $result = mysql_query($sql);
        if(mysql_num_row > 0){
            $dados = mysql_fetch_array($result);
            return $dados[$campo];
        } else {
            return 0;
        }
        return 0;
    }

    function PegaConfig($param){
        // ajusta variáveis
        $estrutura = $param['estrutura'];
        $chave = $param['chave'];
        // se a categoria passada estiver em formato Integer
        if(is_int($estrutura) or $estrutura > 0){
            $sql = "SELECT
                        *
                    FROM
                        cadastros_conf
                    WHERE
                        categorias_id='".$estrutura."' AND
                        chave='".$chave."'
                    ";
        } elseif(is_string($estrutura)){
            // se o parâmetro $param for uma string
            $sql = "SELECT
                        cadastros_conf.valor AS valor
                    FROM
                        cadastros_conf,categorias
                    WHERE
                        cadastros_conf.categorias_id=categorias.id AND
                        categorias.tipo='cadastro' AND
                        categorias.nome='".$estrutura."' AND
                        cadastros_conf.chave='".$chave."'
                    ";
        }

        $result = $this->conexao->query($sql);
        if( count($result) > 0 ){
            $dados = $result[0];
            return $dados;
         } else {
            return FALSE;
         }

    }

    /**
     * INTERFACE DE SETUP
     *
     * Métodos para o setup de novas estruturas
     */

    public function setupAnalisaCamposTipagemFisica(){
        
    }

}

?>