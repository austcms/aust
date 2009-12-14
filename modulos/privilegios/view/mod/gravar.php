<?php
/*
 * GRAVAR.php
 * Arquivo responsável pela criação das tabelas e configurações de privilégios
 *
 * Variáveis necessárias:
 * $_POST -> contendo dados provenientes de formulário
 *
 * ATENÇÃO: este código pega somente variáveis do $_POST iniciando em frm e grava automaticamente no db
 *
 */


$c = 0;

if(!empty($_POST)) {

    /*
     * TIPO DE PRIVILÉGIO
     */
    /*
     * Não Especificado
     *
     * Não foi especificado um tipo de privilégio, assume como sendo este
     * a ser especificado em cada conteúdo
     */
    if( empty($_POST["privilegio_tipo"]) ){
        $_POST["frmtype"] = "content";
    }
    /*
     * Específico por conteúdo
     *
     * A ser especificado em cada conteúdo
     */
    else if( !empty($_POST["privilegio_tipo"])
        AND $_POST["privilegio_tipo"] == "especifico" )
    {
        $_POST["frmtype"] = "content";
    }
    /*
     * Estrutural
     *
     * Define que determinada estrutura está bloqueada
     */
    else if( !empty($_POST["privilegio_tipo"])
        AND $_POST["privilegio_tipo"] == "categoria" )
    {
        $_POST["frmtype"] = "structure";
    }

    foreach($_POST as $key=>$valor) {
    // se o argumento $_POST contém 'frm' no início
        if(strpos($key, 'frm') === 0) {
            $sqlcampo[] = str_replace('frm', '', $key);
            $sqlvalor[] = $valor;
            // ajusta os campos da tabela nos quais serão gravados dados

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
                        ".$modulo->tabela_criar."
                        ($sqlcampostr)
                VALUES
                        ($sqlvalorstr)
            ";



        $h1 = 'Criando: '.$aust->leNomeDaEstrutura($_GET['aust_node']);
    } else if($_POST['metodo'] == 'editar') {
            $sql = "UPDATE
                        ".$modulo->tabela_criar."
                    SET
                        $sqlcampostr
                    WHERE
                        id='".$_POST['w']."'
                    ";
            $h1 = 'Editando: '.$aust->leNomeDaEstrutura($_GET['aust_node']);
        }


    $success = $this->modulo->conexao->exec($sql);
    if( $success !== false ) {
        $insert_id = $this->modulo->conexao->lastInsertId();

        if( !empty($_POST['w']) ){
            $insert_id = $_POST['w'];
        }

        /*
         *
         * TIPO DE PRIVILÉGIO
         *
         */
        /*
         * 'Categoria' significa que uma estrutura inteira está bloqueada.
         */
            $sql_delete = "DELETE FROM privilegio_target WHERE privilegio_id='".$insert_id."'";
            $this->modulo->conexao->exec($sql_delete);

        if( $_POST["privilegio_tipo"] == "categoria" 
            AND !empty($_POST["categoria_id"]) )
        {

            $sql_tipo = "INSERT INTO
                                privilegio_target
                                (privilegio_id, target_table,target_id, type, admin_id, created_on)
                            VALUES
                                ('".$insert_id."','".CoreConfig::read('austTable')."','".$_POST["categoria_id"]."', 'structure','".$_POST['frmadmin_id']."','".date("Y-m-d")."')
                            ";
            $this->modulo->conexao->exec($sql_tipo);
        }

        $resultado = TRUE;
    } else {
        $resultado = FALSE;
    }

    if($resultado) {
        $status['classe'] = 'sucesso';
        $status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
    } else {
        $status['classe'] = 'insucesso';
        $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações.';
    }
    EscreveBoxMensagem($status);

}
?>