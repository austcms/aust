<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/


$c = 0;

/*
 * Carrega configurações automáticas do DB
 */
$params = array(
    "aust_node" => $_POST["aust_node"],
);
$moduloConfig = $modulo->loadModConf($params);

if(!empty($_POST)){
    if($_POST['metodo'] == 'criar'){
        set_time_limit(0);

        $h1 = 'Upload de arquivo: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
        echo '<h1>'.$h1.'</h1>';
        // variável que contém erros
        $erro = array();

        // Prepara a variável do arquivo
        //pr($_FILES);
        $arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : FALSE;
        echo '<p style="color: orange;">Arquivo: '.$arquivo['name'].'</p>';

        // Tamanho máximo do arquivo (em bytes)
        $conf["tamanho"] = 500000000;

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
                $frmarquivo_nome = substr( sha1(strtolower( $frmarquivo_nome ) ), 0, 6 ).'_'.strtolower( $frmarquivo_nome );
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
                $imagem_dir = 'uploads/'.date('Y').'/'.date('m');

                if( !empty($moduloConfig["upload_path"]["valor"]) )
                    $rel_dir = $moduloConfig["upload_path"]["valor"];
                else
                    $rel_dir = '';

                
                if(!is_dir($rel_dir.$imagem_dir)){
                    /*
                     * A permissão só vai funcionar para linux
                     */
                    mkdir($rel_dir.$imagem_dir, 0777, true); 
                    chmod($rel_dir.$imagem_dir, 0777);
                }

                /*
                 * Retorna os endereços do novo arquivo
                 */
                $urlBaseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
                $frmurl = $urlBaseDir.$imagem_dir .'/'.$frmarquivo_nome;
                $_POST['frmurl'] = $frmurl;

                /*
                 * Pega $systemurl
                 */
                $current_dir = getcwd();
                chdir($rel_dir);
                $_POST['frmsystemurl'] = getcwd().'/'.$imagem_dir .'/'.$frmarquivo_nome;
                chdir($current_dir);

                /*
                 * Faz o upload da imagem
                 */
                if(move_uploaded_file($arquivo["tmp_name"], $_POST["frmsystemurl"])){
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
            
            $mysql = $this->modulo->conexao->exec($sql);
            $total = count($mysql);
            if($total == 0)
                $w = 'NULL';
            else {
                $dados = $mysql;
                $w = $dados[0]['id'];
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
        //
        // verifica se dados do arquivo estão no DB
        if( $this->modulo->conexao->exec($sql) ){
            $resultado = TRUE;


            /*
             *
             * EMBED SAVE
             *
             */
           include(INC_DIR.'conteudo.inc/embed_save.php');


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
