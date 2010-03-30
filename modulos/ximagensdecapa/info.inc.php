<?php
/*********************************
*
*   IMAGENS DE CAPA
*
*	arquivo contendo informações sobre o respectivo módulo
*
*********************************/

// informações
$modInfo['nome'] = "Imagens (capa de conteúdo)";
$modInfo['descricao'] = "Módulo para inserir imagens de capa em um conteúdo.";

$modInfo['estrutura'] = FALSE; // Se é uma estrutura ou categoria
$modInfo['somenteestrutura'] = FALSE;
$modInfo['embed'] = TRUE;
$modInfo['embedownform'] = true;

// permissões de acesso
$modInfo['liberacao'] = true;

// opções de gerenciamento deste módulo
$modInfo['opcoes'] = Array(
                            'criar' => 'Novo',
                            'listar' => 'Listar');

// itens que serão listados no cabeçalho da listagem
$content_header['campos'] = Array('adddate','titulo','node');
$content_header['campos_nome'] = Array('Data','Título','Categoria');

?>
