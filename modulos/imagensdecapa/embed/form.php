<?php

/*
 * CAMPO 0
 */

        // se for para editar, deixa checkbox com propriedade 'check'
        $conteudo_tabela = $modulo->LeTabelaDaEstrutura();
        $privid_result = array();

        // ajusta chave
        $embed_form[0]['propriedade'] = 'Imagem de capa';

        // pega todos os privilégios
        unset($temp);
        $temp = $temp.'Selecione um arquivo: <input type="file" name="capaarquivo" /><br />';

        // guarda o parágrafo de introdução, explicativo
        //$embed_form[0]['intro'] = '';
        // salva os <inputs> criados
        $embed_form[0]['input'] = '<div>'.$temp.'</div>';
        // guarda o parágrafo de explicação
        $embed_form[0]['explanation'] = '';




?>



