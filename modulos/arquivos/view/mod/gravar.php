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
    $resultado = $modulo->save($_POST, $_FILES);

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
