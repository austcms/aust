<?php
/**
 * MIGRATIONS
 *
 * Contém os métodos para executar os migrations, seja do Core ou dos módulos.
 *
 * @package Migrations
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.6, 18/01/2010
 */
class Migrations
{
    var $conexao;

    /**
     * Versão do Migration atual.
     *
     * @var <string>
     */
    var $version = '';
    /**
     * Nome do módulo atual.
     *
     * @var <string>
     */
    var $modName = '';

    function __construct($modName, $conexao){
        $this->conexao = $conexao;
        $this->modName = $modName;

        $regexp = "/([0-9]{14})/";

        if( preg_match( $regexp, get_class($this), $matches) ){
            $this->version = $matches[0];
        }
    }

    /**
     * goUp()
     *
     * Executa o método para avançar a versão e salva o
     * resultado no DB.
     *
     * @return <bool>
     */
    public function goUp(){

        if( $this->up() ){
            $sql = "INSERT INTO
                        migrations_mods
                        (version, module_name)
                    VALUES
                        ('".$this->version."', '".$this->modName."')
                    ";
            $this->conexao->exec($sql);

            return true;
        }

        return false;

    }

    function up(){
        return true;
    }

    function down(){
        return true;
    }

    /**
     * createTable()
     *
     * Cria uma tabela usando $schema como um Schema ou como uma string
     * sql.
     *
     * @param <mixed> $schema
     * @return <bool>
     */
    function createTable($schema){
        $c = $this->conexao;
        if( is_array($schema) ){
            $schema = new dbSchema( $schema, $c );
            if( $schema->isDbSchemaFormatOk() ){

                if( is_array($schema->sql())){
                
                    foreach( $schema->sql() as $tabela=>$sql ){
                        /**
                         * @todo - deve-se fazer com que se a
                         * função a seguir não funcione ou retorne
                         * falso, mostrar uma mensagem de erro.
                         */
                        $c->exec($sql);
                    }
                }
            }
        } else if( is_string($schema) ){
            if( !$c->exec($schema) )
                return false;
        }

        return true;
    }

    public function dropTable($table, $field){
        $sql = "ALTER TABLE ".
                    $options['table'].
                " DROP COLUMN ".
                $options['field'];

        $this->conexao->exec($sql);
    }

    public function addField($options = array()){
        if( is_array($options) ){

            if( !array_key_exists('table', $options)
                OR !array_key_exists('field', $options)
                OR !array_key_exists('type', $options) )
                return false;


            $position = '';
            if( !empty($options['position']) )
                $position = $options['position'];
            
            $sql = "ALTER TABLE ".
                        $options['table'].
                    " ADD COLUMN ".
                    $options['field'].
                    " ". $options['type'].' '.$position;

            $this->conexao->exec($sql);
        }
    }

}

?>