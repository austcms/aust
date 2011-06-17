<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/

$c = 0;
if(!empty($_POST)) {

    $_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);

    foreach($_POST as $key=>$valor) {
        // se o argumento $_POST contém 'frm' no início
        if(strpos($key, 'frm') === 0) {
            $sqlcampo[] = str_replace('frm', '', $key);
            $sqlvalor[] = $valor;
            // ajusta os campos da tabela nos quais serão gravados dados
            $valor = addslashes($valor);
            if($_POST['metodo'] == 'create') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                    $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                } else {
                    $sqlcampostr = str_replace('frm', '', $key);
                    $sqlvalorstr = "'".$valor."'";
                }
            } else if($_POST['metodo'] == 'edit') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                } else {
                    $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                }
            }

            $c++;
        }
    }


    if($_POST['metodo'] == 'create') {
        $sql = "INSERT INTO
                    ".$modulo->useThisTable()."
                    ($sqlcampostr)
                VALUES
                    ($sqlvalorstr)
                ";


        $h1 = 'Criando: '.Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']);
    } else if($_POST['metodo'] == 'edit') {
        $sql = "UPDATE
                    ".$modulo->useThisTable()."
                SET
                $sqlcampostr
                WHERE
                id='".$_POST['w']."'
                ";
        $h1 = 'Editando: '.Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']);
    }

    $query = $this->modulo->connection->exec($sql);
    if($query !== false) {
        $resultado = TRUE;

        // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
        if($_POST['metodo'] == 'criar') {
            $_POST['w'] = $this->modulo->connection->conn->lastInsertId();
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
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
