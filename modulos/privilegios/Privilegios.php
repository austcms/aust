<?php
class Privilegios extends Modulos {

// TABELA
    protected $db_tabelas;
    protected $sql_das_tabelas;
    protected $sql_registros;
    public $tabela_criar;

    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    public $conexao;
    /**
     *
     * @var class Configurações do módulo
     */
    public $config;
	/*********************************
	*
	*	funções de ação
	*
	*********************************/

    function __construct($param = '') {
        // define qual é a tabela principal
        $this->tabela_criar = "privilegios";
        parent::__construct($param);

        //$this->tabela_criar = "privilegios_conf";
        /*
        // sql das tabelas
        $this->db_tabelas[] = "privilegios_conf";
        $this->sql_das_tabelas[] = "
			CREATE TABLE privilegios_conf (
				id int NOT NULL auto_increment,
				tipo varchar(80) {$charset},
				chave varchar(120) {$charset},
				valor text {$charset},
				nome varchar(120) {$charset},
				comentario text {$charset},
				descricao text {$charset},
				ref_tabela varchar(120) {$charset},
				ref_campo varchar(120) {$charset},
				referencia varchar(120) {$charset},
				especie varchar(120) {$charset} COMMENT 'Se é específico de um módulo ou não (ex: privilégio do módulo texto)',
				classe varchar(120) {$charset} COMMENT 'Se é padrão do sistema ou não',
				necessario bool,
				restrito bool,
				publico bool,
				desativado bool,
				desabilitado bool,
				bloqueado bool,
				aprovado int,
				categorias_id int,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
            ";

        $this->db_tabelas[] = "privilegios_de_conteudos";
        $this->sql_das_tabelas[] = "
			CREATE TABLE privilegios_de_conteudos (
				id int NOT NULL auto_increment,
				tipo varchar(80) {$charset},
				privilegios_conf_id varchar(80) {$charset},
                conteudo_tabela varchar(120) {$charset},
                conteudo_id int,
                expiradate datetime,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
            ";

        $this->db_tabelas[] = "privilegios_de_usuarios";
        $this->sql_das_tabelas[] = "
			CREATE TABLE privilegios_de_usuarios (
				id int NOT NULL auto_increment,
				tipo varchar(80) {$charset},
				privilegios_conf_id varchar(80) {$charset},
                usuario_tabela varchar(120) {$charset},
                usuario_id int,
                expiradate datetime,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
            ";

        $this->sql_registros[] = "INSERT INTO privilegios_conf(tipo,chave,valor,classe) VALUES ('grupo','nome','Cadastrados','padrão')";
        */


    }

    public function getRelatedCategories($austNode){
        $sql = "
                SELECT
                    valor
                FROM
                    modulos_conf
                WHERE
                    categoria_id='$austNode' AND
                    tipo='relacionamentos'
                ";
        //echo $sql;
        $result = $this->conexao->query($sql);
        $return = array();
        foreach( $result as $key=>$valor ){
            $return[] = reset($valor);
        }
        return $return;
    }

    /*
     * funções de verificação ou leitura automática do DB
     */
    public function SQLParaListagem($param) {
    // configura e ajusta as variáveis
        $categorias = $param['categorias'];
        $metodo = $param['metodo'];
        $w = $param['id'];
        // se $categorias estiver vazio (nunca deverá acontecer)
        if(!empty($categorias)) {
            $order = ' ORDER BY id ASC';
            //$where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor) {
                if($c == 0)
                    $where = $where . 'categorias_id=\''.$key.'\'';
                else
                    $where = $where . ' OR categorias_id=\''.$key.'\'';
                $c++;
            }
        }
        // SQL para verificar na tabela CADASTRO_CONF quais campos existem
        $sql = "SELECT
                    *, categoria_id AS cat,
                    (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node,
                    (	SELECT
                            CONCAT(nome, ', ', IFNULL(patriarca,'toda estrutura'))
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node_patriarca,

                    DATE_FORMAT(created_on, '%d/%m/%Y') as adddate
                    FROM
                            ".$this->tabela_criar." AS conf
                ORDER BY adddate DESC
                ";
        return $sql;
    }


    /*
     * Função para retornar o nome da tabela de dados de uma estrutura da cadastro
     */
    public function LeTabelaDeDados($param) {
        return $this->tabela_criar;
    }



}

?>