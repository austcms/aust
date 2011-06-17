<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="expires" content="Mon, 19 Feb 2024 11:12:01 GMT" />
    <title><?php echo $this->Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node'])?> - Versão para impressão</title>
    <link rel="stylesheet" type="text/css" href="<?php echo UI_PATH; ?>css/special/print.css" media="screen, print" />
</head>
<body>
<?php
/**
 * Formulário deste módulo
 *
 * @package ModView
 * @name form
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 09/07/2009
 */
/**
 * Informações deste cadastro
 */
$infoCadastro = $modulo->pegaInformacoesCadastro($austNode);

if( !empty($_GET["w"]) ){
    $w = $_GET['w'];
}

//pr($infoCadastro);
?>
<h2>Cadastro: <?php echo $this->Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node'])?></h2>

<div id="print">
<table>
    <tbody>
    <?php

    /**
     * MOSTRA FORMULÁRIO DINÂMICO
     */

        /**
         * Se edição
         */
        if( !empty($_GET['w']) ){
            /**
             * Cria INPUT Hidden com ID
             */
        }

    /**
     * Campos
     */
    //pr($infoCadastro);
    $tabelaCadastro = $infoCadastro["estrutura"]['tabela']["valor"];

    /*
     *
     * FORMULÁRIO DE CADASTRO
     *
     * O formulário é criado automaticamente
     *
     */
    foreach( $camposForm as $chave=>$valor ){

        unset($inputType);
        $select = array();
        $checkbox = array();

        if( array_key_exists($valor['nomeFisico'], $divisorTitles) ){
            ?>
            <tr valign="top">
                <td class="divisor" colspan="2">
                <h3><?php echo $divisorTitles[$valor['nomeFisico']]['valor']; ?></h3>
                <?php
                if( !empty($divisorTitles[$valor['nomeFisico']]['comentario']) ){
                    echo '<p>'.$divisorTitles[$valor['nomeFisico']]['comentario'].'</p>';
                }
                ?>
                </td>
            </tr>
            <?php
        }

        /**
         * RELACIONAL UM PARA UM
         */
        if( $valor["tipo"]["especie"] == "relacional_umparaum" ){
            $sql = "SELECT id,".$valor["tipo"]["tabelaReferenciaCampo"]." FROM ".$valor["tipo"]["tabelaReferencia"];
            $selectValues = $conexao->query($sql);
            //pr($sql);
            //$select["selected"] = "3";
            $inputType = "select";
            foreach($selectValues as $tabelaReferenciaResult){
                $select["options"][ $tabelaReferenciaResult["id"] ] = $tabelaReferenciaResult[ $valor["tipo"]["tabelaReferenciaCampo"] ];
            }

        }
        /*
         * RELACIONAL UM PARA MUITOS
         *
         * Monta checkboxes do campo que é do tipo relacional um-para-muitos
         */
        else if($valor["tipo"]["especie"] == "relacional_umparamuitos") {

            $referencia = $valor["tipo"]["tabelaReferencia"];
            $tabelaRelacional = $valor["tipo"]["referencia"];
            $campo = $valor["tipo"]["tabelaReferenciaCampo"];
            $sql = "SELECT
                        t.id, t.$campo
                    FROM
                        ".$referencia." AS t
                    ORDER BY t.$campo ASC
                    ";
            $checkboxes = $modulo->connection->query($sql);

            $inputType = "checkbox";
            foreach($checkboxes as $tabelaReferenciaResult){
                $checkbox["options"][ $tabelaReferenciaResult["id"] ] = $tabelaReferenciaResult[ $campo ];
            }

            /*
             * Se for edição, pega os dados que estão salvos neste campo
             */

            if( !empty($w) ){
                $sql = "SELECT
                            t.id, t.".$referencia."_id AS referencia
                        FROM
                            ".$tabelaRelacional." AS t
                        ORDER BY
                            t.id ASC
                        ";

                $values = $modulo->connection->query($sql);
                if( empty($values)){
                    $values = array();
                } else {
                    foreach( $values as $id ){
                        $valor["valor"][] = $id["referencia"];
                    }
                }
            }
        } elseif( $valor['tipo']['tipoFisico'] == 'date' ){
            $inputType = "date";
        } elseif( $valor['tipo']['tipoFisico'] == 'text' ){
            $inputType = "textarea";
        }

        if( empty($valor["valor"]) ){
            $valor["valor"] = "";
        }


        if( empty($inputType) ){
            $inputType = "";
        }

        //pr($inputType);

        /**
         * Cria INPUT
         */
        ?>
        <tr valign="top">
            <td class="label">
            <?php
            echo $valor["label"];
            ?>:
            </td>
            <td valign="top">
            <?php
            if( empty($valor["valor"]) )
                echo '-';
            else
                echo $valor["valor"];
            ?>
            </td>
        </tr>
        <?php
        /*
        echo $form->input( $chave, array(
                                        "label" => $valor["label"],
                                        "select" => $select,
                                        "checkbox" => $checkbox,
                                        "value" => $valor["valor"],
                                        "type" => $inputType,
                                    )
                            );
         *
         */
    }



    ?>
    </tbody>
</table>
</div>

</body>
</html>