<?php
/**
 * Configurações de acesso ao Banco de Dados
 *
 */
/**
 * Conexão local de acesso ao DB com conteúdos
 */

class DATABASE_CONFIG
{
    static $dbConn = array(
        'server' => 'localhost',
        'database' => 'aust',
        'username' => 'root',
        'password' => '', 
        'encoding' => 'utf8',
		// 'port' => '8888', // if you ever need special port access
    );
}

?>