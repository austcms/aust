<?php
/**
 * MIGRATIONS
 *
 * Contém os métodos para executar os migrations, seja do Core ou dos módulos.
 *
 * @package Migrations
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.6, 18/01/2010
 */
class Migrations
{
    var $conexao;

    function __construct($conexao){
        
    }

    function up(){

    }

    function down(){

    }

    function createTable($table, $schema){
        echo $table;
    }
    
}

?>