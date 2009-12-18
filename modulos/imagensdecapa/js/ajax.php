<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Caminho deste arquivo até o root
 */
define('THIS_TO_BASEURL', '../../../');

/**
 * Carrega variáveis contendo comportamentos e Paths
 */
    include(THIS_TO_BASEURL.'core/config/variables.php');

/**
 * Carrega classes
 */
require_once(THIS_TO_BASEURL.CLASS_DIR.'_carrega_classes.inc.php');

/**
 * Propriedades editáveis do sistema. Carrega todas as configurações da aplicação
 */
/**
 * Carrega as configurações de conexão do banco de dados
 */
    include(THIS_TO_BASEURL.CONFIG_DIR.'database.php');
/**
 * Configurações do core do sistema
 */
    include(THIS_TO_BASEURL.CONFIG_DIR.'core.php');




//include('../modulo.class.php');
include('../index.php');

$conexao = new Conexao($dbConn);


header("Content-Type: text/html; charset=".$aust_charset['view'],true);

/*
 * LIBERAÇÃO DE GALERIA DE IMAGENS
 *
 * Grava do DB que determinada estrutura terá galeria de imagens
 */

    if($_POST['action'] == 'selectLiberacao'){
        if($_POST['clicado'] == 'true'){
            $sql = "SELECT
                        id
                    FROM
                        modulos_conf
                    WHERE
                        tipo='liberacao' AND
                        nome='".$_POST['modulo']."' AND
                        valor='".$_POST['estrutura']."'";
            //echo $sql;
            $mysql = mysql_query($sql);
            $t = mysql_num_rows($mysql);
            if($t >= 0){
                $sql = "INSERT INTO
                            modulos_conf
                                (tipo,nome,propriedade,valor)
                        VALUES
                            ('liberacao','".$_POST['modulo']."','estrutura','".$_POST['estrutura']."')
                        ";
                if(mysql_query($sql)) echo '1';
                else echo '0';
            }
        } else {
                $sql = "DELETE FROM
                            modulos_conf
                        WHERE
                            tipo='liberacao' AND
                            nome='".$_POST['modulo']."' AND
                            propriedade='estrutura' AND
                            valor='".$_POST['estrutura']."'
                        ";
                if(mysql_query($sql)) echo '1del';
                else echo '0del';
        }

/**
 * IMAGENS DE CAPA
 *
 * Grava quais estruturas terão imagens de capa
 */
    } elseif($_POST['action'] == 'selectImagensDeCapa'){

        if($_POST['clicado'] == 'true'){
            $sql = "SELECT
                        id
                    FROM
                        modulos_conf
                    WHERE
                        tipo='liberacaoimagensdecapa' AND
                        nome='".$_POST['modulo']."' AND
                        valor='".$_POST['estrutura']."'";
            //echo $sql;
            $mysql = mysql_query($sql);
            $t = mysql_num_rows($mysql);
            if($t >= 0){
                $sql = "INSERT INTO
                            modulos_conf
                                (tipo,nome,propriedade,valor)
                        VALUES
                            ('liberacaoimagensdecapa','".$_POST['modulo']."','estrutura','".$_POST['estrutura']."')
                        ";
                // retorna resultado
                if(mysql_query($sql)) echo '1';
                else echo '0';
            }
        } else {
                $sql = "DELETE FROM
                            modulos_conf
                        WHERE
                            tipo='liberacaoimagensdecapa' AND
                            nome='".$_POST['modulo']."' AND
                            propriedade='estrutura' AND
                            valor='".$_POST['estrutura']."'
                        ";

            // retorna resultado
            if(mysql_query($sql)) echo '1del';
            else echo '0del';
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

