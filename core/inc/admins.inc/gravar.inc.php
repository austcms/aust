<?php
/*
 * USUÀRIOS
 *
 * -> Arquivo de gravação de dados no DB
 */
    /**
     * Se é edição, busca informações sobre o usuário.
     */

    if(!empty($_POST['w'])){
        $id_w = $_POST['w'];

        $sql = "SELECT
                    *
                FROM
                    admins
                WHERE
                    id='".$_POST['w']."'
                ";
        $query = $conexao->query($sql);
        $query = $query[0];
        //pr($query);

    } else {
        $id_w = 'NULL';
    }

    /**
     * Se $_POST['frmhierarquia'] não existir
     */

    if( empty($_POST['frmhierarquia']) ){
        if ( !empty($query['tipo']) ){
            $hierarquia = "'".$query['tipo']."',";
            $hierarquiaCampo = "tipo,";
        }
    } else {
        $hierarquia = "'".$_POST['frmhierarquia']."',";
        $hierarquiaCampo = "tipo,";
    }

    $sql = "REPLACE INTO
                admins
                (
                    id, $hierarquiaCampo nome,login,senha,
                    email,telefone,celular,sexo,
                    biografia,supervisionado,adddate,autor)
            VALUES
                (
                    '".$id_w."',$hierarquia '".$_POST['frmnome']."','".$_POST['frmlogin']."','".$_POST['frmsenha']."',
                    '".$_POST['frmemail']."','".$_POST['frmtelefone']."','".$_POST['frmcelular']."','".$_POST['frmsexo']."',
                    '".$_POST['frmbiografia']."','".$_POST['frmsupervisionado']."',
                    '".Config::DataParaMySQL()."','".$_POST['frmautor']."')";

                    //echo $sql;

	if($conexao->exec($sql)){
		$resultado = TRUE;
	} else {
		$resultado = FALSE;
	}

	if($resultado){
		$status['classe'] = 'sucesso';
		$status['mensagem'] = '<strong>Sucesso: </strong> Informações salvas com sucesso!';
	} else {
		$status['classe'] = 'insucesso';
		$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro desconhecido, tente novamente.';
	}
	EscreveBoxMensagem($status);

?>
<p><a href="adm_main.php?section=admins">Voltar</a></p>

