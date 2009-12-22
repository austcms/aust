<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/

$c = 0;
if(!empty($_POST)){

    $_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);

	foreach($_POST as $key=>$valor){
		// se o argumento $_POST contém 'frm' no início 
		if(strpos($key, 'frm') === 0){
			$sqlcampo[] = str_replace('frm', '', $key);
			$sqlvalor[] = $valor;
			// ajusta os campos da tabela nos quais serão gravados dados
            $valor = addslashes($valor);
			if($_POST['metodo'] == 'criar'){
				if($c > 0){
					$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
					$sqlvalorstr = $sqlvalorstr.",'".$valor."'";
				} else {
					$sqlcampostr = str_replace('frm', '', $key);
					$sqlvalorstr = "'".$valor."'";
				}
			} else if($_POST['metodo'] == 'editar'){
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
		$sql = "INSERT INTO
					".$this->modulo->tabela_criar."
					($sqlcampostr)
				VALUES
					($sqlvalorstr)
					";
					
					
		$h1 = 'Criando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
	} else if($_POST['metodo'] == 'editar'){
		$sql = "UPDATE
					".$this->modulo->tabela_criar."
				SET
					$sqlcampostr
				WHERE
					id='".$_POST['w']."'
					";
		$h1 = 'Editando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
	}
        //echo $sql;
        
    $query = $this->modulo->conexao->exec($sql);
    
    if($query !== false){
        $resultado = TRUE;

        // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
        if($_POST['metodo'] == 'criar'){
            $_POST['w'] = $this->modulo->conexao->conn->lastInsertId();
        }


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
		$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
	}
	EscreveBoxMensagem($status);
	
}
?>
<br />
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
