<?php
/**
 * REGISTRY
 *
 * @since v0.1.7, 30/05/2009
 */
class Registry {
	protected static $config;

	/**
	 * Escreve uma nova variável com configurações.
	 *
	 * @param string $varName Nome da variável a ser gravada.
	 * @param string $varValor Valor a ser gravado na nova variável.
	 * @return bool Se a variável foi gravada com sucesso.
	 */
	static function write($varName, $varValor){
		return self::$config[$varName] = $varValor;
	}

	/**
	 * Some uma variável a uma array
	 *
	 * @param string $varName Nome da variável a ser gravada (será uma array).
	 * @param string|array $varValor Valor a ser gravado na nova variável.
	 * @return bool Se a variável foi gravada com sucesso.
	 */
	static function add($varName, $varValor){
		if( empty(self::$config[$varName]) ){
			self::$config[$varName] = null;
		}

		$tempVar = self::$config[$varName];

		if( !is_array($tempVar) )
			$tempVar = array();

		array_push( $tempVar , $varValor);
		self::$config[$varName] = $tempVar;
		return true;
	}

	/**
	 * Retorna um valor de uma configuração.
	 *
	 * @param string $varName Nome da configuração que se deseja saber o valor.
	 * @param string $default Valor retornado caso a configuração não exista.
	 * @return string
	 */
	static function read($varName, $default = ''){
		if( !empty(self::$config[$varName]) ){
			return self::$config[$varName];
		} else {
			return $default;
		}
	}
}
?>