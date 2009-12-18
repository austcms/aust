<?php
/*********************************
*
* CADASTRO DE PESSOAS
*
* arquivo contendo informações sobre o respectivo módulo
*
*********************************/

// informações
$modInfo['nome'] = "Cadastros";
$modInfo['descricao'] = "Este módulo é o responsável pelo cadastro de pessoas.";
$modInfo['estrutura'] = TRUE; // Se é uma estrutura ou categoria
/**
 * [somenteestrutura] indica se a estrutura conterá categorias ou não.
 */
$modInfo['somenteestrutura'] = TRUE;
$modInfo['embed'] = FALSE;

// opções de gerenciamento deste módulo
$modInfo['opcoes'] = Array(
                            'listar' => 'Listar'
                            );

// itens que serão listados no cabeçalho da listagem
$content_header['campos'] = Array('adddate','titulo','node');
$content_header['campos_nome'] = Array('Data','Título','Categoria');

?>
