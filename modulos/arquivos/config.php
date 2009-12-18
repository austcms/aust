<?php
/*********************************
*
* CADASTRO DE PESSOAS
*
* arquivo contendo informações sobre o respectivo módulo
*
*********************************/

// informações obrigatórias
$modInfo['nome'] = "Arquivos";
$modInfo['descricao'] = "Este módulo é o responsável pelo upload e gerenciamento de arquivos.";
$modInfo['estrutura'] = TRUE; // Se é uma estrutura ou categoria
$modInfo['somenteestrutura'] = FALSE;
$modInfo['infra_estrutura'] = FALSE; // privilégios, por exemplo, é uma infraestrutura, enquanto texto não
$modInfo['embed'] = FALSE;

// opções de gerenciamento deste módulo
$modInfo['opcoes'] = Array(
                            'form' => 'Novo',
                            'listar' => 'Listar'
                            );

// itens que serão listados no cabeçalho da listagem

$content_header['campos'] = Array('data','titulo','node');
$content_header['campos_nome'] = Array('Data','Título','Categoria');



?>
