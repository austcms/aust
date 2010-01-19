<?php
/**
 * MIGRATIONS MODS
 *
 * Responsável por lidar com os migrations dos mods, com verificações e etc.
 *
 * @package Migrations
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.6, 18/01/2010
 */
class MigrationsMods
{
    var $conexao;

    function  __construct($conexaoClass) {
        $this->conexao = $conexaoClass;
    }

    /*
     *
     * MÉTODOS DE INSTALAÇÃO
     *
     */

    function updateMigration($modName){
        return false;
    }

    /*
     *
     * VERIFICAÇÕES
     *
     */
    /**
     * status()
     *
     * Retorna o status atual da tabela no formato array
     *
     * @param <string> $modName
     * @return <array> status
     */
    function status($modName = ''){

        if( empty($modName) ){
            return $this->_checkAllModsMigration();
        } else {
            return $this->_checkModStatus($mod);
        }
    }

    /**
     * isActualVersion()
     *
     * Verifica se um determinado módulo está instalado completamente.
     */
    public function isActualVersion($path){
        $migrationVersion = $this->_checkModVersionInArray($path);
        
        if( !empty($migrationVersion[ key($migrationVersion) ]['migrationVersion'] ) )
            $mV = $migrationVersion[ key($migrationVersion) ]['migrationVersion'];
        else
            return false;

        $actualVersion = $this->_checkModActualVersion( $this->getModNameFromPath($path) );

        if( $actualVersion < $mV )
            return false;

        return true;
    }

    /**
     * _checkModsMigration()
     *
     * Verifica *todos* os módulos e retorna true para os que
     * estão na última versão, e falso para o contrário.
     *
     * @return <array>
     */
    function _checkAllModsMigration(){
        $modsDir = MODULOS_DIR;
        $result = array();

        /*
         * Percorre pastas dos módulos, verificando um a um sobre seus migrations.
         */
        foreach( glob($modsDir."*", GLOB_ONLYDIR) as $modDir):
            $return = array_merge_recursive( $this->_checkModVersionInArray($modDir) );
            $situation['mods'][ key($return) ] = $return;
        endforeach;

        /*
         * Verifica se está instalado as últimas versões
         */
        foreach( $situation['mods'] as $modName=>$mod ){
            $return = $this->_comparesActualVersion($mod);
            $result[$modName] = $return;
        }

        return $result;
    }

    /**
     * _checkModVersionInArray()
     * 
     * Verifica a última versão do migration de um módulo.
     * 
     * @param <string> $modDir
     * @return <array>
     */
    public function _checkModVersionInArray($modDir){

        $modName = $this->getModNameFromPath($modDir);
        if( is_dir($modDir)
            AND is_dir($modDir.'/'.MIGRATION_MOD_DIR))
        {
            $modMigrationsDir = $modDir.'/'.MIGRATION_MOD_DIR;
            /*
             * Loop por cada migration, tomando o nome e versão
             * do mesmo.
             */
            $latestVersion = 0;
            foreach (glob($modMigrationsDir."Migration_*.php") as $filename) {
                $regexp = "/([0-9]{14})/";
                if ( preg_match( $regexp, $filename, $matches) ){
                    if( $matches[0] > $latestVersion )
                        $latestVersion = $matches[0];

                    $situation[$modName]['migrationVersion'] = $latestVersion;
                }
            }
        }
        /*
         * Não há migration
         */
        else {
            $situation[$modName] = '0';
        }
        unset($modName);

        return $situation;

    }

    /*
     *
     *
     */
    public function _comparesActualVersion($mod){
        $modName = key($mod);
        $mod = reset($mod);
        if( !empty($mod['migrationVersion']) ){
            if( $mod['migrationVersion'] <= $this->_checkModActualVersion($modName) ){
                $result = true;
            } else {
                $result = false;
            }
        }
        return $result;
    }

    function _checkModStatus($name){
    }

    /**
     * _checkModVersion()
     *
     * Verifica a versão atual de um migration de um módulo.
     *
     * @param <string> $name Nome do diretório do módulo
     * @return <string>
     */
    function _checkModActualVersion($name){
        $sql = 'SELECT version
                FROM migrations_mods
                WHERE module_name="'.$name.'"
                ORDER BY version ASC
                LIMIT 1';
        $result = reset( $this->conexao->query($sql) );
        return $result['version'];
    }

    /*
     *
     * MÉTODOS DE SUPORTE
     *
     */
    /**
     * getModNameFromPath()
     *
     * Pega o nome de um módulo a partir do seu diretório.
     *
     * ex. a partir de modulos/conteudo, será retornado 'conteudo'.
     */
    function getModNameFromPath($path){
        $modName = array_reverse( explode('/', $path) );
        return $modName[0];
    }

    /**
     * hasMigration()
     *
     * Retorna true se um módulo tem Migrations.
     *
     * @param <string> $path
     * @return <bool>
     */
    public function hasMigration($path){
        $modMigrationsDir = $path.'/'.MIGRATION_MOD_DIR;
        /*
         * Loop por cada migration, tomando o nome e versão
         * do mesmo.
         */
        $latestVersion = 0;
        foreach (glob($modMigrationsDir."Migration_*.php") as $filename) {
            $regexp = "/([0-9]{14})/";
            if ( preg_match( $regexp, $filename, $matches) ){
                return true;
            }
        }
        
        return false;
    }
    
}

?>