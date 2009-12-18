<?php
/**
 * ACTIONS
 *
 * Ações especiais
 *
 * Este arquivo contém ações especiais, como deletar conteúdos
 *
 * @package Conteúdo
 * @name Actions
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1, 01/01/2009
 */
/**
 *
 * O módulo tem um arquivo específico de actions?
 *
 * Carrega modulo/actions.php se existir
 */
if(is_file( Modulos::MOD_DIR .$aust->LeModuloDaEstrutura($_GET['aust_node']).'/'.MOD_ACTIONS.FILE ) ){
    include( Modulos::MOD_DIR .$aust->LeModuloDaEstrutura($_GET['aust_node']).'/'.MOD_ACTIONS.FILE );
} else {

    /**
     * Ajusta $block para evitar erros no código
     */
    if( empty($block) ){
        $block = '';
    }

    /**
     * Bloquear o acesso a determinado conteúdo
     */
    if($block == "block"){
        $sql = "UPDATE $section
                SET
                    bloqueado='bloqueado'
                WHERE
                    id='$w'
                ";
        if ($modulo->conexao->exec($sql)){
        ?>
            <div style="width: 680px; display: table;">
                <div style="background: red; padding: 15px; text-align: center;">
                    <p style="color: white; margin: 0px;">
                        O conte&uacute;do foi bloqueado com sucesso! Entretanto, ele n&atilde;o foi deletado.
                    </p>

                    <?php
                    if($escala == "administrador"
                    OR $escala == "moderador"
                    OR $escala == "webmaster"){
                    ?>
                        <p style="color: white; margin: 0px;">
                            <a href="adm_main.php?section=<?php echo $section;?>&action=<?php echo $action;?>&block=delete&w=<?php echo $w; ?><?php echo $addurl;?>" style="text-decoration: underline; color: white;">-> Clique aqui para apagar o conte&uacute;do definitivamente <- </a>
                        </p>
                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        } else {
            echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
        }
    }
    
    /**
     * Desbloquear determinado conteúdo
     */
    else if($block == "unblock"){
        $sql = "UPDATE $section
                SET
                    bloqueado='livre',
                    publico='sim'
                WHERE
                    id='$w'
                ";
        if ($modulo->conexao->exec($sql)){
        ?>
            <div style="width: 680px; display: table;">
                <div style="background: green; padding: 15px; text-align: center;">
                    <p style="color: white; margin: 0px;">
                        O conte&uacute;do foi desbloqueado com sucesso! Agora ele aparecer&aacute; no site.
                    </p>
                </div>
            </div>
        <?php
        } else {
            echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
        }
    }

    /**
     * Deletar determinado(s) conteúdo(s)
     */
    elseif( !empty($_POST['deletar']) ){
        /*
         * Identificar tabela que deve ser excluida
         */

        // se não estiver confirmada a exclusão
        if((empty($_GET['confirm'])) AND !empty($_POST['itens']) AND (count($_POST['itens']) > 0)){
        ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&confirm=delete" name="repost">
            <input type="hidden" name="deletar" value="deletar" />
            <?php
            $itens = $_POST['itens'];
            foreach($itens as $key=>$valor){
                echo '<input type="hidden" name="itens[]" value="'.$valor.'" />';
            }
            $status['classe'] = 'pergunta';
            $status['mensagem'] = '<strong>
                    Tem certeza que deseja apagar o(s) item(ns) selecionado(s)?
                    </strong>
                    <br />
                    <a href="#" onclick="document.repost.submit(); return false">Sim</a> -
                    <a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=listar">N&atilde;o</a>';
            EscreveBoxMensagem($status);
            ?>
            </form>
        <?php
        /**
         * CONFIRMADA A EXCLUSÃO
         */
        } elseif( $_GET['confirm'] == "delete" AND !empty($_POST['itens']) ){

            $itens = $_POST['itens'];
            $c = 0;
            foreach($itens as $key=>$valor){
                if($c > 0){
                    $where = $where." OR id='".$valor."'";
                } else {
                    $where = "id='".$valor."'";
                }
                $c++;
            }
            $sql = "DELETE FROM
                        ".$modulo->LeTabelaDaEstrutura($_GET['aust_node'])."
                    WHERE
                        $where
                        ";


            if($modulo->conexao->exec($sql)){
                $resultado = TRUE;
            } else {
                $resultado = FALSE;
            }

            if($resultado){
                $status['classe'] = 'sucesso';
                $status['mensagem'] = '<strong>Sucesso: </strong> Os dados foram excluídos com sucesso.';
            } else {
                $status['classe'] = 'insucesso';
                $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao excluir os dados.';
            }
            EscreveBoxMensagem($status);

        } elseif( empty($_POST['itens']) ){
            $status['classe'] = 'alerta';
            $status['mensagem'] = 'Nenhum item selecionado.';
            EscreveBoxMensagem($status);
        }
    }
}

?>