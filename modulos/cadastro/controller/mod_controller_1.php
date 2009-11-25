<?php
/**
 * Controller principal deste módulo
 *
 * @package ModController
 * @name nome
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 06/07/2009
 */

class ModController extends ModsController
{

    public function listar(){
        //$this->render('listar');
    }

    /**
     * formulário
     */

    public function form(){

    }

    public function criar(){

        /**
         * Pega todas as informações sobre a estrutura em formato array
         */
        $estrutura = $this->aust->pegaInformacoesDeEstrutura( $this->austNode );

        $infoCadastro = $this->modulo->pegaInformacoesCadastro( $this->austNode );

        //pr($infoCadastro);
        //
        //
        //$this->conexao->query("DESCRIBE ");

        // monta os campos de edição do formulário automaticamente
        //$fields = mysql_num_fields($mysql);
        // pega registros do db
        //$dados['registro'] = mysql_fetch_array($mysql);

            $campos = $infoCadastro["campo"];
            foreach ( $campos as $chave=>$valor ){
                $dados['campos'] = $valor;
                ?>
                <tr>
                    <?php
                    /*
                     * Mostra inputs automaticamente.
                     *
                     * Engine:
                     *      Pega os registros da tabela do cadastro e os registros
                     *      da tabela cadastros_conf e verifica cada um, tentando
                     *      coincindi-los. Se algum campo não consta na tabela cadastros_conf
                     *      não é mostrado seu input, pois provavelmente é um campo
                     *      de configuração.
                     *
                     */
                    $type  = $valor["especie"];
                    if(!empty($dados['campos']['valor'])){
                        ?>
                        <td valign="top"><?php echo $dados['campos']['valor']?></td>
                        <td valign="top">
                        <?php
                        echo $type;
                        /*
                         * Mostra os campos do formulário de acordo com o tipo de dados da tabela
                         */
                        if($type == 'string'){

                            // se o campo for do tipo "campoarquivo", ou seja, se for para upload de arquivo
                            /**
                             * @todo - leitura de campos de arquivos
                             */
                            if($type == 'campoarquivo'){
                                $sql = "SELECT
                                            *
                                        FROM
                                            ".$modulo->LeTabelaDaEstrutura($_GET['aust_node'])."_arquivos
                                        WHERE
                                            tipo='".$dados['campos']['chave']."' AND
                                            referencia='".$_GET['w']."'
                                            ";
                                $result = mysql_query($sql);

                                $dados['arquivo'] = mysql_fetch_array($result);
                                echo '<a href="../'.$dados['arquivo']['url'].$dados['arquivo']['arquivo_nome'].'" target="_blank">';
                                echo $dados['arquivo']['arquivo_nome'];
                                echo '</a>';


                            // senão
                            }
                            /**
                             * Texto normal
                             */
                            else {
                                ?>
                                <input type="text" name="frm<?php echo mysql_field_name($mysql, $i);?>" disabled="disabled" value="<?php echo $dados['registro'][mysql_field_name($mysql, $i)]?>" />
                                <?
                            }
                            ?>


                        <?php } elseif($type == 'date'){ ?>
                            <input type="text" name="frm<?php echo mysql_field_name($mysql, $i);?>" disabled="disabled" value="<?php echo date("d/m/Y",strtotime($dados['registro'][mysql_field_name($mysql, $i)]));?>" />
                        <?php } elseif($type == 'blob'){ ?>
                            <textarea name="frm<?php echo mysql_field_name($mysql, $i);?>" rows="8" disabled="disabled" cols="30"><?php echo $dados['registro'][mysql_field_name($mysql, $i)]?></textarea>
                        <?php } elseif($type == 'int'){
                                if($type == 'camporelacional_umparaum'){
                                    $sql_conf = "
                                                SELECT
                                                    id, ".$dados['campos']['ref_campo']." AS ref_campo
                                                FROM
                                                    ".$dados['campos']['ref_tabela']."
                                                WHERE
                                                    id=".$dados['registro'][mysql_field_name($mysql, $i)]."
                                                ";
                        //echo $sql_conf;
                                    $result = mysql_query($sql_conf);
                                    if(mysql_num_rows($result)){
                                        $dados['relacional'] = mysql_fetch_array($result);
                                        echo $dados['relacional']['ref_campo'];
                                    } else {
                                        echo 'Não há registro.';
                                    }
                                    ?>

                                    <?
                                }
                            }
                        ?>
                        <p class="explanation"><?php echo $dados['campos']['comentario'];?></p>

                        </td>
                        <?php
                    }
                    ?>
                </tr>
            <?php
            }
        





        $this->render('form');
    }

    public function editar(){

        
        $this->render('form');
    }

    public function save(){
        
    }
    
}
?>