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

                unset($camposStr);
                unset($valorStr);
                /**
                 * INSERT
                 */
                if( empty($data[$tabela]["id"]) ){
                    /**
                     * Loop por cada campo e seus valores
                     */
                    foreach( $campos as $campo=>$valor ){

                        /*
                         * Múltiplos Inserts
                         */
                        if( is_int($campo) ){
                            
                            foreach( $valor as $multipleInsertsCampo=>$multipleInsertsValor ){
                                //pr($valor);
                                $camposStrMultiplo[] = $multipleInsertsCampo;
                                $valorStrMultiplo[] = $multipleInsertsValor;
                                $tempSql = "INSERT INTO
                                                ".$tabela."
                                                    (".implode(",", $camposStrMultiplo).")
                                            VALUES
                                                ('".implode("','", $valorStrMultiplo)."')
                                            ";
                                /**
                                 * SQL deste campo
                                 */
                                $sql[] = $tempSql;

                                unset($valorStrMultiplo);
                                unset($camposStrMultiplo);
                                unset($tempSql);
                            }

                        }
                        /*
                         * Inclusão normal única
                         */
                        else if( is_string($campo) ){
                            $camposStr[] = $campo;
                            $valorStr[] = addslashes( $valor);
                        }
                    }

                    if( !empty($camposStr)
                        AND !empty($valorStr) )
                    {

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
                        unset($tempSql);
                    }
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
                            $camposStr[] = $campo."='".addslashes($valor)."'";
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

                    Connection::getInstance()->exec($instrucao);
                }
                
                return true;
            }
        }

        return false;
    }
}

?>