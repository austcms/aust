<?php
class Privilegios extends Module
{

    public $mainTable = "privilegios";

    public function __construct($param = '') {
        
        $this->tabela_criar = "privilegios";
        parent::__construct($param);

    }

    /*
     *
     * CRUD
     *
     */

    public function saveEmbed($params = array()){
		if( empty($params) )
			return false;
		
        $data = $params['data'];

        /*
         * Último ID inserido.
         */
        $w = $params['w'];
        /*
         * Esta é a tabela principal do módulo pai
         */
        $targetTable = $params['targetTable'];

        /*
         * Se $w está vazio, é um novo conteúdo
         */
        if(empty($w) AND $params['metodo'] == 'criar'){
            /*
             * $insert_id pega o último id inserido do conteúdo principal. Não é
             * seguro pegar este valor aqui, mas sim que o conteúdo
             * principal já tenha salvo o valor em $w
             */
            $insert_id = Connection::getInstance()->lastInsertId();
        } elseif(!empty($w)) {
            $insert_id = $w;
        }

        /*
         * Se foi clicado algum item no form de inclusão
         */
        if(is_array($data['privid'])){
            /*
             * Deleta privilégio anterior para fazer a atualização agora
             */
            $sql_delete = "DELETE
                            FROM
                                privilegio_target
                            WHERE
                                target_table='".$targetTable."' AND
                                target_id='".$insert_id."'
                            ";

            Connection::getInstance()->exec($sql_delete);

            /*
             * Prepara o sql
             */
            $itens = $data['privid'];
            
            foreach( $itens as $valor ){
                $embed_sql[] = "INSERT INTO
                                    privilegio_target
                                    (privilegio_id, target_table,
                                    target_id, created_on, admin_id, type)
                                VALUES
                                    ('$valor','$targetTable','$insert_id',
                                    '".date("Y-m-d")."',
                                    '".User::getInstance()->getId()."', 'content')
                                ";

            }

            foreach($embed_sql as $valor){
                Connection::getInstance()->exec($valor);
                $this->w = Connection::getInstance()->lastInsertId();
            }


        } else {
            /*
             * Deleta privilégio anterior para fazer a atualização agora
             */
            $sql_delete = "DELETE
                            FROM
                                privilegio_target
                            WHERE
                                target_table='".$targetTable."' AND
                                target_id='".$insert_id."'
                            ";

            Connection::getInstance()->exec($sql_delete);
        }

        return true;
        
    }

    public function loadEmbed($param){
        $this->mainTable = "privilegio_target";

        return parent::loadEmbed($param);

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
        $result = Connection::getInstance()->query($sql);
        $return = array();
        foreach( $result as $key=>$valor ){
            $return[] = reset($valor);
        }
        return $return;
    }

    /**
     * SQLParaListagem()
     * 
     * funções de verificação ou leitura automática do DB.
     *
     * @param <array> $param
     * @return <array>
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