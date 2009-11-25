<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/


$c = 0;
if(!empty($_POST)){
    if($_POST['metodo'] == 'criar'){
        set_time_limit(0);

        $h1 = 'Upload de arquivo: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
        echo '<h1>'.$h1.'</h1>';
        // variável que contém erros
        $erro = array();

        // Prepara a variável do arquivo
        $arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : FALSE;
        echo '<p style="color: orange;">Arquivo: '.$arquivo['name'].'</p>';

        // Tamanho máximo do arquivo (em bytes)
        $conf["tamanho"] = 50000000;

        // Formulário postado... executa as ações
        $extinvalido = 'php|php3|html|htm|css|js';
        if($arquivo){
            // Verifica se o mime-type do arquivo é de imagem
            if(!eregi("^image\/(".$extinvalido.")$", $arquivo["type"])){
                // Verifica tamanho do arquivo
                if($arquivo["size"] > $conf["tamanho"]){
                    $erro[] = "Arquivo em tamanho muito grande!
                    A imagem deve ser de no máximo " . $conf["tamanho"] . " bytes.
                    Envie outro arquivo";
                    $status = 'tamanho';
                }
            } else {
                $erro[] = "Arquivo em formato inválido! Os seguinte formatos de arquivos são proibidos: ".$extinvalido;
                $status = 'extensao';
            }

            // Imprime as mensagens de erro
            if(count($erro) == 0){
                // Verificação de dados OK, nenhum erro ocorrido, executa então o upload...
                // Pega extensão do arquivo

                $trocarIsso = array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ü','ú','ÿ','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ü','Ú','?',',',' ');
                $porIsso    = array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','_','_','_');

                $frmarquivo_nome = str_replace($trocarIsso, $porIsso, $arquivo['name']);
                $frmarquivo_nome = strtolower( $frmarquivo_nome );
                $frmarquivo_tipo = $arquivo['type'];
                $frmarquivo_tamanho = $arquivo['size'];

                //ajusta o $_POST para salvar dados no DB
                $_POST['frmarquivo_nome'] = $frmarquivo_nome;
                $_POST['frmarquivo_tipo'] = $frmarquivo_tipo;
                $_POST['frmarquivo_tamanho'] = $frmarquivo_tamanho;
                $_POST['frmarquivo_extensao'] = PegaExtensao($_POST['frmarquivo_nome']);


                //$frmarquivo_nome = urlencode($arquivo['name']);
                //$imagem_nome = str_replace($trocarIsso, $porIsso, $frmfilename);
                //$imagem_nome = urlencode($imagem_nome);
//                $imagem_nome = stri ($imagem_nome);

                // Caminho de onde a imagem ficará
                $imagem_dir = 'uploads/'.$config->PegaData('ano').'/'.$config->PegaData('mes');

                if(!is_dir('../'.$imagem_dir)){
                    mkdir('../'.$imagem_dir, 0777, true); // a permissão só vai funcionar para linux
                    chmod('../'.$imagem_dir, 0777);
                }

                // Faz o upload da imagem
                $frmurl = ''.$imagem_dir .'/';
                $_POST['frmurl'] = $frmurl;
                $_POST['frmlocal'] = 'local';
                //if(copy($_FILES["arquivo"]["tmp_name"], '..'.$frmurl.$frmarquivo_nome)){
                if(move_uploaded_file($arquivo["tmp_name"], '../'.$frmurl.$frmarquivo_nome)){
                    echo "<p>Arquivo enviado com sucesso!</p>";
                } else {
                    $erro[] = '<p>Erro ao inserir arquivo.</p>';
                    $erro[] = '<p>Outros possíveis erros: upload_max_filesize='.ini_get(upload_max_filesize).' - post_max_size='.ini_get(post_max_filesize);
                }
                
            }
        }
    }
    if(count($erro) == 0){
        foreach($_POST as $key=>$valor){
            // se o argumento $_POST contém 'frm' no início
            if(strpos($key, 'frm') === 0){
                $sqlcampo[] = str_replace('frm', '', $key);
                $sqlvalor[] = $valor;

                /*
                 * monta variável $where para verificar se dados do arquivo já existe no DB
                 */

                
                // ajusta os campos da tabela nos quais serão gravados dados
                if($_POST[metodo] == 'criar'){
                    if($c > 0){
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                        $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                        $where .= " AND ".str_replace('frm', '', $key) ."='".$valor."'";
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key);
                        $sqlvalorstr = "'".$valor."'";
                        $where .= str_replace('frm', '', $key) ."='".$valor."'";
                    }
                } else if($_POST[metodo] == 'editar'){
                    if($c > 0){
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                    }
                }

                $c++;
            }
        }

        if($_POST['metodo'] == 'criar'){
            $sql = "SELECT
                        id
                    FROM
                        ".$modulo->LeTabelaDaEstrutura()."
                    WHERE
                        adddate='".$_POST['frmadddate']."' AND
                        arquivo_nome='".$_POST['frmarquivo_nome']."'
                    ";
            $mysql = mysql_query($sql);
            $total = mysql_num_rows($mysql);
            if($total == 0)
                $w = 'NULL';
            else {
                $dados = mysql_fetch_array($mysql);
                $w = $dados['id'];
            }

            $sql = "REPLACE INTO
                        ".$modulo->tabela_criar."
                        (id,$sqlcampostr)
                    VALUES
                        ('".$w."',{$sqlvalorstr})
                        ";


            $h1 = 'Criando: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
        } else if($_POST[metodo] == 'editar'){
            $total = 0;
            $sql = "UPDATE
                        ".$modulo->tabela_criar."
                    SET
                        $sqlcampostr
                    WHERE
                        id='".$_POST['w']."'
                        ";
            $h1 = 'Editando: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
        }
        //echo $sql.'<br>';
        // verifica se dados do arquivo estão no DB
        if(mysql_query($sql)){
            $resultado = TRUE;
            /*
             * carrega módulos que contenham propriedade embed
             */
            $embed = $modulo->LeModulosEmbed();
            if(count($embed)){
                foreach($embed AS $chave=>$valor){
                    foreach($valor AS $chave2=>$valor2){
                        if($chave2 == 'pasta'){
                            if(is_file($valor2.'/embed/gravar.php')){
                                include($valor2.'/embed/gravar.php');
                            }
                        }
                    }
                }
            } // fim do embed

        } else {
            $resultado = FALSE;
        }

        if($resultado){
            $status['classe'] = 'sucesso';
            $status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
        } else {
            $status['classe'] = 'insucesso';
            $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações.';
        }
        EscreveBoxMensagem($status);
    } else {
        echo '<p>Não foi possível efetuar esta operação. Os erros encontrados foram:</p>';
        echo '<ul>';
        foreach($erro as $err){
            echo "<li>" . $err . "</li>";
        }
        echo '</ul>';

    }
	
}
?>
