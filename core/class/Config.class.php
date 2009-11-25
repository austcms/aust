<?php

class Config {
    private $Opcoes; // variável possuindo as configurações
    private $OpcoesNomes; // variável possuindo as configurações
    public $self;

    public $options;
    public $conexao;

    // Inicializa Configurações
    function __construct( $params ) {
        $this->conexao = $params['conexao'];
        $this->self = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

        /*
        $sql = "SELECT * FROM config";

        $mysql = mysql_query($sql);
        $total = mysql_num_rows($mysql);

        if($total == 0) {
            $sql2[] = "INSERT INTO config (nome,propriedade,valor) VALUES ('Nome do site','sitename','')";
            $sql2[] = "INSERT INTO config (nome,propriedade,valor) VALUES ('Email do suporte técnico','suportetecnicoemail','')";
            foreach($sql2 as $key=>$valor) {
                mysql_query($valor);
            }
            $sql = "SELECT * FROM config";
            $mysql = mysql_query($sql);
        }
        // se existém registros no DB config
        //if(count($_SESSION['conf']) < $total){
        while($dados = mysql_fetch_array($mysql)) {
        //$this->AjustaOpcoes($dados[propriedade], $dados[valor]);
            $loaded_config[$dados[propriedade]] = $dados[valor];
        }
        $_SESSION['conf'] = $loaded_config;

        //}

        $session_config = Array();
        $session_config = $_SESSION['conf'];
        foreach ($session_config as $chave=>$valor) {
            $this->AjustaOpcoes($chave, $valor);
        }
         *
         */

    }

    // Atualiza Configurações
    function AtualizaConfig(){
            $this->__construct();
    }

    // Acessa a variável que guarda as configurações e retorna seu valor
    public function LeOpcao($propriedade, $metodo = ''){
            if(empty($this->Opcoes[$propriedade])){
                    if($metodo == 'form'){
                            return '';
                    } else if(empty($metodo)) {
                            return ''; //return 'con'.$propriedade.'fig01';
                    }
            } else {
                    return $this->Opcoes[$propriedade];
            }
    }

    function updateOptions($params){

        $this->conexao->exec("UPDATE config SET valor='".$params["valor"]."' WHERE id='".$params["id"]."'");

        return '<span style="color: green;">Configuração salva com sucesso!</span>';
    }
    
    // Ajusta a configuração
    function ajustaOpcoes($params){ //$propriedade, $valor, $nome = '', $tipo = '', $local = ''){

        $this->options[$params["tipo"]][$params["propriedade"]] = $params;
        return true;
        /*
        $this->Opcoes[$propriedade] = $valor;
        $this->OpcoesNomes[$propriedade] = $nome;
        $this->OpcoesTipo[$propriedade] = $tipo;
        $this->OpcoesLocal[$propriedade] = $local;
         * 
         */
    }

    // Grava as configurações definitivamente no banco de dados
    public function gravaConfig() {

        foreach($this->options as $tipo=>$valor) {

            $sql = "SELECT id FROM config WHERE tipo='".$tipo."' AND propriedade='".key($valor)."'";
            $query = $this->conexao->count($sql);

            if( $query ) {
                $valores = reset($valor);
                $sql = "UPDATE
                            config
                        SET
                            valor='".$valores["valor"]."'
                        WHERE
                            propriedade='".key($valor)."'
                            AND tipo='".$tipo."'
                            ";
            } else {

                $valores = reset($valor);

                foreach( $valores as $coluna=>$info ){
                    $colunas[] = $coluna;
                    $infos[] = $info;
                }

                $sql = "INSERT INTO
                            config
                                (".implode(",", $colunas).")
                        VALUES
                            ('".implode("','", $infos)."')";
            }

            //echo $sql."<br>";
            if( !$this->conexao->exec($sql) ) {
                $erro[] = key($valor);
            }
        }

        if(count($erro) == 0) {
            return '<span style="color: green;">Configuração salva com sucesso!</span>';
        } else {
            return '<span style="color: red;">Ocorreu um erro desconhecido. Algumas opções não foram salvas.</span>';
        }

    }

    // Cria formulário
    function getOptions($params){

        $where = (empty($params["where"])) ? '' : 'WHERE '. $params["where"];


        $sql = "SELECT * FROM config $where ORDER BY tipo ASC";
        //echo 'oijoij';
        $query = $this->conexao->query($sql);
        //pr($query);

        if( count($query) ){

            return $query;

        } else {
            return false;
        }
    }

    public function ContaConfig(){
        return count($this->options);
    }

    // retorna a data de agora no formato certo para o campo MySQL DATETIME
    function DataParaMySQL($param = ''){
        if(empty($param) or $param == 'datetime'){
            return date("Y-m-d H:i:s");
        } elseif($param == 'date'){
            return date("Y-m-d");
        }
    }
    
    // retorna a data
    function PegaData($formato){
        $formato = StrToLower($formato);
        if ($formato == "dia") return date("d");
        else if ($formato == "mes") return date("m");
        else if ($formato == "ano") return date("Y");
        else if ($formato == "hora") return date("H");
        else if ($formato == "minuto") return date("i");
        else if ($formato == "segundo") return date("s");
    }


}

?>