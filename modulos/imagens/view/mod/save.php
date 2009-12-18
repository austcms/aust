<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/

$c = 0;

//pr($_FILES);

if( $_POST["metodo"] == "criar" AND !empty($_FILES) AND $_FILES["frmarquivo"]["size"] > 0 ){
    $save = true;
} else if( $_POST["metodo"] == "editar" ) {
    $save = true;
} else {
    $save = false;
}

if( !empty($_POST) AND $save  ) {
//if( false ) {

    /*
     * FILES
     *
     * Prepara campos relativos ao arquivo que foi feito upload
     */
    
    if( !empty($_FILES) AND $_FILES["frmarquivo"]["size"] > 0 ){
        $imagem = $modulo->trataImagem($_FILES);
        $_POST["frmbytes"] = $imagem["filesize"];
        $_POST["frmdados"] = $imagem["filedata"];
        $_POST["frmnome"] = $imagem["filename"];
        $_POST["frmtipo"] = $imagem["filetype"];

    }

    /*
     * Prepara a ordem da imagem
     */
    if( empty($_POST["frmordem"]) ){
        // seleciona a última ordem do banco de dados
        $sql = "SELECT
                    ordem
                FROM
                    imagens
                WHERE
                    categoria='".$_POST['aust_node']."'
                ORDER BY
                    ordem asc
                ";
        //echo $sql;
        $query = $modulo->conexao->query($sql);
        $total = $modulo->conexao->count($sql);

        $ordem = 0;
        foreach ( $query as $dados ){
            $curordem = $dados["ordem"];
            if ($curordem >= $ordem)
                $ordem = $curordem+1;
        }

        /*
         * Se não há imagens ainda, $ordem = 1
         */
        if ($ordem == 0)
            $ordem = 1;

        /*
         * Últimos ajustes de campos a serem inseridos
         */
        $_POST["frmordem"] = $ordem;
    } // fim ordem automática

    $_POST["frmcategoria"] = $_POST["aust_node"];
    $_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);

    /*
     * GROUPED_DATA
     *
     * Alguns dados, como data, precisam ser mostrados em mais de um input.
     *
     * O formato adequado é grouped_data[nome_da_coluna_no_db][nome_do_campo]
     */
    if( !empty($_POST["grouped_data"]) ){

        $gD = $_POST["grouped_data"];


        foreach( $gD as $chave=>$coluna ){

            $gDR = groupedDataFormat($coluna);
            //if( !empty($gDR) ){
                $_POST["frm".$chave] = $gDR;
            //}

        }
        
    }

    /*
     * Prepara os campos que serão usados para gerar o SQL de INSERT
     */

    foreach($_POST as $key=>$valor) {
    // se o argumento $_POST contém 'frm' no início
        if(strpos($key, 'frm') === 0) {
            $sqlcampo[] = str_replace('frm', '', $key);
            $sqlvalor[] = $valor;
            // ajusta os campos da tabela nos quais serão gravados dados
            $valor = addslashes($valor);
            if($_POST['metodo'] == 'criar') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                    $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                } else {
                    $sqlcampostr = str_replace('frm', '', $key);
                    $sqlvalorstr = "'".$valor."'";
                }
            } else if($_POST['metodo'] == 'editar') {
                    if($c > 0) {
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                    }
                }

            $c++;
        }
    }



    if($_POST['metodo'] == 'criar') {
        $sql = "INSERT INTO
                    ".$this->modulo->tabela_criar."
                    ($sqlcampostr)
                VALUES
                    ($sqlvalorstr)
            ";


        $h1 = 'Criando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
    } else if($_POST['metodo'] == 'editar') {
            $sql = "UPDATE
                    ".$this->modulo->tabela_criar."
                SET
                $sqlcampostr
                WHERE
                    id='".$_POST['w']."'
                ";
            $h1 = 'Editando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
    }

    $query = $this->modulo->conexao->exec($sql);
    //echo $sql;
    //$query = false;
    if($query) {
        $resultado = TRUE;

        // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
        if($_POST['metodo'] == 'criar') {
            $_POST['w'] = $this->modulo->conexao->conn->lastInsertId();
        }


        /*
         * carrega módulos que contenham propriedade embed
         */
        $embed = $this->modulo->LeModulosEmbed();

        // salva o objeto do módulo atual para fazer embed
        if( !empty($embed) ) {
            /*
             * Caso tenha embed, serão carregados modulos embed. O objeto do módulo atual
             * é $modulo, sendo que dos embed também. Então guardamos $modulo,
             * fazemos unset nele e reccaregamos no final do script.
             */

            $tempmodulo = $modulo;
            unset($modulo);
            foreach($embed AS $chave=>$valor) {
                foreach($valor AS $chave2=>$valor2) {
                    if($chave2 == 'pasta') {
                        if(is_file($valor2.'/embed/gravar.php')) {
                            include($valor2.'/index.php');
                            include($valor2.'/embed/gravar.php');
                        }
                    }
                }
            }
            $modulo = $tempmodulo;
        } // fim do embed

    } else {
        $resultado = FALSE;
    }

    if($resultado) {
        $status['classe'] = 'sucesso';
        $status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
    } else {
        $status['classe'] = 'insucesso';
        $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
    }
    EscreveBoxMensagem($status);

}
?>
<br />
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
