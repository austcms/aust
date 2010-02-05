<?php
/*
 * USUÀRIOS
 *
 * -> Arquivo de gravação de dados no DB
*/
/**
 * Se é edição, busca informações sobre o usuário.
 */
$resultado = false;
if(!empty($_POST)) {

    if( empty($_POST['frmsenha']))
        unset($_POST['frmsenha']);

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
                    admins
                    ($sqlcampostr)
              VALUES
                    ($sqlvalorstr)
                ";

    } else if($_POST['metodo'] == 'editar') {
        $sql = "UPDATE
                    admins
                SET
                    $sqlcampostr
                WHERE
                    id='".$_POST['w']."'";
    }

    if($conexao->exec($sql) !== false) {
        $resultado = true;
    } else {
        $resultado = false;
    }
}
    if($resultado) {
        $status['classe'] = 'sucesso';
        $status['mensagem'] = '<strong>Sucesso: </strong> Informações salvas com sucesso!';
    } else {
        $status['classe'] = 'insucesso';
        $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro desconhecido. Tente novamente. '.
            'Se o problema prosseguir, contacte um administrador.';
    }
    EscreveBoxMensagem($status);

?>
<p><a href="adm_main.php?section=admins">Voltar</a></p>

