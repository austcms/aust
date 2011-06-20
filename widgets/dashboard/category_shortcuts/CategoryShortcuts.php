<?php
class CategoryShortcuts extends Widget
{

    /**
     * getStructures()
     *
     * Retorna todas as estruturas do site.
     *
     * @return <array>
     */
    function getStructures(){

        $sql = "SELECT
                    *
                FROM
                    categorias
                WHERE
                    classe='estrutura'
                ";
        
        $query = Connection::getInstance()->query($sql);

        $est = array();
        foreach( $query as $chave=>$valor ){

            if( !$this->envParams['permissoes']->verify($valor['id']) ){
                continue;
            }

            $est[$valor['id']]['nome'] = $valor['nome'];
            $est[$valor['id']]['tipo'] = $valor['tipo'];
            $est[$valor['id']]['id'] = $valor['id'];
        }
        return $est;
        
    }

}
?>
