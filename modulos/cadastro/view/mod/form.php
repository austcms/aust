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
<h2>Cadastro: <?php echo $this->aust->leNomeDaEstrutura($_GET['aust_node'])?></h2>
<p>
    <?php
    echo $formIntro;
    ?>
</p>

<?php
echo $form->create( $infoCadastro["estrutura"]["tabela"]["valor"] );
/*
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&action=gravar">
 * 
 */
?>
<input type="hidden" name="metodo" value="<?php echo $_GET["action"];?>" />
<input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
<input type="hidden" name="frmautor" value="<?php echo $administrador->LeRegistro('id');?>">
<input type="hidden" name="w" value="<?php ifisset($_GET['w']);?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode;?>">


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
        echo $form->input( "id", array(
                                        "type" => "hidden",
                                        "value" => $_GET["w"],
                                    )
                            );
    }

/**
 * Campos
 */

$tabelaCadastro = $infoCadastro["estrutura"]["valor"];

/*
 *
 * FORMULÁRIO DE CADASTRO
 *
 * O formulário é criado automaticamente
 *
 */
foreach( $camposForm as $chave=>$valor ){

    unset($select);
    unset($checkbox);
    unset($inputType);

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
    echo $form->input( $chave, array(
                                    "label" => $valor["label"],
                                    "select" => $select,
                                    "checkbox" => $checkbox,
                                    "value" => $valor["valor"],
                                    "type" => $inputType,
                                )
                        );
    ?>
    <p class="explanation">
    <?php echo $valor["comentario"] ?>
    </p>
    <?php
}


echo $form->end();
?>
