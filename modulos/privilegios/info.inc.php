<?php
/*********************************
*
* PRIVILÉGIOS
*
* arquivo contendo informações sobre o respectivo módulo
*
*********************************/

// informações
$modInfo['nome'] = "Privilégios";
$modInfo['descricao'] = "Este módulo é o responsável por dar privilégios aos usuários.";
$modInfo['somenteestrutura'] = TRUE;
$modInfo['embed'] = TRUE;

// opções de gerenciamento deste módulo
$modInfo['opcoes'] = Array(
                            'criar' => 'Novo grupo',
                            'listar' => 'Listar'
                            );

// itens que serão listados no cabeçalho da listagem
$content_header['campos'] = Array('adddate','valor','node_patriarca');
$content_header['campos_nome'] = Array('Data','Título','Categoria');

?>
