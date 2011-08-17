<?php
/**
 * MIGRATIONS MODS
 *
 * Responsável por lidar com os migrations dos mods, com verificações e etc.
 *
 * @since v0.1.6, 18/01/2010
 */
class MigrationsMods
{
	/**
	 *
	 * @var <object> Conexão principal com o DB
	 */
	public $conexao;

	function  __construct() {
		$this->conexao = Connection::getInstance();
	}

	/**
	 * getInstance()
	 *
	 * Para Singleton
	 *
	 * @staticvar <object> $instance
	 * @return <Conexao object>
	 */
	static function getInstance(){
		static $instance;

		if( !$instance ){
			$instance[0] = new MigrationsMods;
		}

		return $instance[0];

	}

	/*
	 *
	 * MÉTODOS DE INSTALAÇÃO
	 *
	 */

	function updateMigration($path){

		$missingMigrations = $this->getMissingMigrations($path);
		/*
		 * Roda cada um dos migrations que estão faltando
		 */
		$modName = $path;
		foreach( $missingMigrations as $name ){

			require_once MODULES_DIR.$path.'/'.MIGRATION_MOD_DIR.$name.'.php';
			$migration = new $name($modName);
			
			if( !$migration->goUp() ){
				echo 'ERRO! Migration '.$name.' com erro!';
				return false;
				break;
			}
			unset($migration);
		}

		return true;
	}

	public function getMissingMigrations($path){
		
		$actualVersion = $this->getActualVersion($path);
		$missingMigrations = array();
		$i = 0;

		$modMigrationsDir = MODULES_DIR.$path.'/'.MIGRATION_MOD_DIR;
		foreach (glob($modMigrationsDir."Migration_*.php") as $filename) {
			$regexp = "/([0-9]{14})/";

			if( preg_match( $regexp, $filename, $matches)
				AND $matches[0] > $actualVersion )
			{
				$missingMigrations[] = basename( $filename, '.php' );
			}
		}
		sort($missingMigrations);
		return $missingMigrations;

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

		$actualVersion = $this->getActualVersion( $path );
		
		if( $actualVersion < $mV )
			return false;

		return true;
	}

	public function hasSomeVersion($path){

		$actualVersion = $this->getActualVersion( $path );

		if( $actualVersion > 0 )
			return true;

		return false;
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
		$modsDir = MODULES_DIR;
		$result = array();

		/*
		 * Percorre pastas dos módulos, verificando um a um sobre seus migrations.
		 */
		foreach( glob($modsDir."*", GLOB_ONLYDIR) as $modDir){

			$return = array_merge_recursive( $this->_checkModVersionInArray($modDir) );
			$situation['mods'][ key($return) ] = $return;
		}
		
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
		$modDir = $modDir;
		$modName = $this->getModNameFromPath($modDir);
		$modMigrationsDir = MODULES_DIR.$modName.'/'.MIGRATION_MOD_DIR;

		if( is_dir($modMigrationsDir) ){
			$modMigrationsDir = MODULES_DIR.$modName.'/'.MIGRATION_MOD_DIR;
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

	/**
	 * _comparesActualVersion()
	 *
	 * @param ? $mod
	 * @return <bool>
	 */
	public function _comparesActualVersion($mod){
		$modName = key($mod);
		$mod = reset($mod);
		$result = false;
		if( !empty($mod['migrationVersion']) ){
			if( $mod['migrationVersion'] <= $this->getActualVersion($modName) ){
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
	function getActualVersion($name){
		$name = $name;
		$sql = 'SELECT version
				FROM migrations_mods
				WHERE module_name="'.$name.'"
				ORDER BY version DESC
				LIMIT 1';

		$sqlReturn = Connection::getInstance()->query($sql);
		$result = reset( $sqlReturn );

		if( $result['version'] > 0 )
			return $result['version'];
		
		return '0';
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
	 * ex. a partir de modules/conteudo, será retornado 'conteudo'.
	 */
	function getModNameFromPath($modName){
		$modName = array_reverse(explode("/", $modName));
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
		$modMigrationsDir = MODULES_DIR.$path.'/'.MIGRATION_MOD_DIR;
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