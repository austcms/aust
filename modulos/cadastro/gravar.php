<?php
/*
 * GRAVAR.php
 * Arquivo responsável pela criação das tabelas e configurações de um novo cadastro
 *
 * Variáveis necessárias:
 * $_POST -> contendo dados provenientes de formulário
 *
 */


$c = 0;
	if(!empty($_POST)){


//	if(mysql_query($sql)){
		$resultado = TRUE;
       /*
         * carrega módulos que contenham propriedade embed
         */
        $embed = $modulo->LeModulosEmbed();
        if(count($embed)){
            foreach($embed AS $chave=>$valor){
                foreach($valor AS $chave2=>$valor2){
                    if($chave2 == 'pasta'){
                        if(is_file($valor2.'/embed/usuarios_gravar.php')){
                            include($valor2.'/embed/usuarios_gravar.php');
                        }
                    }
                }
            }
        }

	//} else {
		//$resultado = FALSE;
	//}

	if($resultado){
		$status['classe'] = 'sucesso';
		$status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
	} else {
		$status['classe'] = 'insucesso';
		$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações.';
	}
	EscreveBoxMensagem($status);
	
}
?>