<?php
/**
 * MIGRATIONS MODS
 *
 * Responsável por lidar com os migrations dos mods
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

    function up(){

    }

    function down(){

    }

    function status($modName = ''){

        if( empty($modName) ){
            return $this->_checkModsMigration();
        } else {
            return $this->_checkModStatus($mod);
        }

    }

    /**
     * _checkModsMigration()
     *
     * Verifica *todos* os módulos e retorna true para os que
     * estão na última versão, e falso para o contrário.
     *
     * @return <type>
     */
    function _checkModsMigration(){
        $modsDir = MODULOS_DIR;
        $result = array();

        foreach( glob($modsDir."*", GLOB_ONLYDIR) as $modDir) {
            //echo var_dump(is_dir($modDir.'/'.MIGRATION_MOD_DIR)).'<br>';

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

                        $situation['mods'][$modName]['migrationVersion'] = $latestVersion;
                    }
                }
            }
            /*
             * Não há migration
             */
            else {
                $situation['mods'][$modName] = '0';
            }
            unset($modName);
        }

        /*
         * Verifica se está instalado as últimas versões
         */
        foreach( $situation['mods'] as $modName=>$mod ){
            if( !empty($mod['migrationVersion']) ){
                if( $mod['migrationVersion'] <= $this->_checkModVersion($modName) ){
                    $result[$modName] = true;
                } else {
                    $result[$modName] = false;
                }
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
    function _checkModVersion($name){
        $sql = 'SELECT version
                FROM migrations_mods
                WHERE module_name="'.$name.'"
                ORDER BY version ASC
                LIMIT 1';
        $result = reset( $this->conexao->query($sql) );
        return $result['version'];
    }
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
    
}

?>