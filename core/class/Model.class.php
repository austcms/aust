<?php
/**
 * Arquivo que representa a estrutura controller de um MV
 *
 * @package MVC
 * @name Controller
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.5, 22/06/2009
 */
class Model extends Aust
{

    public function save($data){

        if( is_array($data) ){
            /**
             * Loop por cada tabela com valores enviados
             */
            foreach($data as $tabela=>$campos){

                /**
                 * INSERT
                 */
                if( empty($data[$tabela]["id"]) ){
                    /**
                     * Loop por cada campo e seus valores
                     */
                    foreach( $campos as $campo=>$valor ){
                        $camposStr[] = $campo;
                        $valorStr[] = $valor;
                    }

                    $tempSql = "INSERT INTO
                                    ".$tabela."
                                        (".implode(",", $camposStr).")
                                VALUES
                                    ('".implode("','", $valorStr)."')
                                ";
                    /**
                     * SQL deste campo
                     */
                    $sql[] = $tempSql;
                }
                /**
                 * UPDATE
                 */
                else {

                    $w = $data[$tabela]["id"];
                    unset($data[$tabela]["id"]);
                    /**
                     * Loop por cada campo e seus valores
                     */
                    foreach( $campos as $campo=>$valor ){
                        if( $campo != "id" ){
                            $camposStr[] = $campo."='".$valor."'";
                        }
                        
                    }

                    $tempSql = "UPDATE
                                    ".$tabela."
                                SET
                                    ".implode(",", $camposStr)."
                                WHERE
                                    id='".$w."'
                                ";
                    /**
                     * SQL deste campo
                     */
                    $sql[] = $tempSql;
                }
            }

            if( count($sql) > 0 ){
                foreach( $sql as $instrucao ){
                    $this->conexao->exec($instrucao);
                }
                
                return true;
            }
        }

        return false;
    }
}

?>