<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include("../../../au-conf.php");
include('../../../class/conexao.class.php');
include('../../../class/modulos.class.php');
include('../modulo.class.php');
$conexao = new Conexao($db['servidor'],$db['db'],$db['usuario'],$db['senha']);
$modulo = new Cadastro;


/*
 * Função para retornar os cadastros do sistema no formato para <select>
 */
header("Content-Type: text/html; charset=".$aust_charset['view'],true);
if($_POST['action'] == 'LeCadastros'){
    $sql = "SELECT
                *
            FROM
                categorias
            WHERE
                tipo='cadastro'";
    $result = mysql_query($sql);
    if(mysql_num_rows($result) > 0){
        while($dados = mysql_fetch_array($result)){
            echo '<option value="'.$modulo->LeTabelaDaEstrutura($dados['id']).'">'.$dados['nome'].'</option>';
        }
    }
    
} elseif($_POST['action'] == 'LeCampos'){
    $sql = "SELECT
                *
            FROM
                ".$_POST['tabela']."
            LIMIT 0,1
        ";
    $result = mysql_query($sql);
    $fields = mysql_num_fields($result);
    for ($i=0; $i < $fields; $i++) {
        echo '<option value="'.mysql_field_name($result, $i).'">'.mysql_field_name($result, $i).'</option>';
    }

}



?>

