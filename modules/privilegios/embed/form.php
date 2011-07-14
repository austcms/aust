
<?php

/*
 * CAMPO 0
 */

        // se for para editar, deixa checkbox com propriedade 'check'

        $mainTable = $module->getContentTable();
        $privid_result = array();
        if(!empty($_GET['w'])){
            $temp_w = $_GET['w'];
            
            $sql = "SELECT
                        privilegio_id
                    FROM
                        privilegio_target
                    WHERE
                        target_table='".$mainTable."' AND
                        target_id='".$temp_w."'
                    ";
            $result = $module->connection->query($sql);

            foreach($result as $dados_priv){
                $privid_result[] = $dados_priv['privilegio_id'];
            }
        }

        // ajusta chave
        $embed_form[0]['propriedade'] = 'Privilégio';

        // pega todos os privilégios
        $sql = "SELECT
                    pc.id, pc.titulo
                FROM
                    privilegios AS pc
                WHERE
                    pc.type='content'
                ";
        $result = $module->connection->query($sql);
        //pr($result);
        //$result = $result[0];
        foreach($result as $dados){
            $temp_act = '';
            if(in_array($dados['id'], $privid_result)){
                $temp_act = 'checked="checked"';
            }
            $temp = $temp.'<input type="checkbox" name="embed['.$embedI.'][data][privid][]" '.$temp_act.' value="'.$dados['id'].'" /> '.$dados['titulo'].'<br />';
        }

        // guarda o parágrafo de introdução, explicativo
        $embed_form[0]['intro'] = 'Selecione os privilégios necessários para usuários acessarem este conteúdo.';
        // salva os <inputs> criados
        $embed_form[0]['input'] = '<div>'.
                                    ''.
                                    '<input type="hidden" name="embed['.$embedI.'][privilegio]" value="1" />'.
                                    $temp.'</div>';
        // guarda o parágrafo de explicação
        $embed_form[0]['explanation'] = '';




?>

