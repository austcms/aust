<?php

/*
 * CAMPO 0
 */

        // se for para editar, deixa checkbox com propriedade 'check'
        $usuario_tabela = $modulo->LeTabelaDaEstrutura($_GET['aust_node']);
        $privid_result = array();
        if(!empty($_GET['w'])){
            $temp_w = $_GET['w'];
            
            $sql = "SELECT
                        privilegios_conf_id
                    FROM
                        privilegios_de_usuarios
                    WHERE
                        usuario_tabela='".$usuario_tabela."' AND
                        usuario_id='".$temp_w."'
                    ";
            $result = mysql_query($sql);
            while($dados_priv = mysql_fetch_array($result)){
                $privid_result[] = $dados_priv['privilegios_conf_id'];
            }
        }

        // ajusta chave
        $embed_form[0]['propriedade'] = 'Privilégio';

        // pega todos os privilégios
        $sql = "SELECT
                    id, valor
                FROM
                    privilegios_conf AS pc
                ";
        $result = mysql_query($sql);
        while($dados = mysql_fetch_array($result)){
            $temp_act = '';
            if(in_array($dados['id'], $privid_result)){
                $temp_act = 'checked="checked"';
            }
            $temp = $temp.'<input type="checkbox" name="privid[]" '.$temp_act.' value="'.$dados['id'].'" /> '.$dados['valor'].'<br />';
        }

        // guarda o parágrafo de introdução, explicativo
        $embed_form[0]['intro'] = 'Selecione os privilégios necessários para usuários acessarem este conteúdo.';
        // salva os <inputs> criados
        $embed_form[0]['input'] = '<div><input type="hidden" name="privilegio" value="1" />'.$temp.'</div>';
        // guarda o parágrafo de explicação
        $embed_form[0]['explanation'] = '';




?>

