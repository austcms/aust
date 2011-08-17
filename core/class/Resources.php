<?php
/**
 * Resources
 *
 * Aust's Toolbox. Responsible for string treatments, parsing files etc.
 *
 * @since v0.1.9, 11/06/2011
 */
class Resources
{

	static function numberToCurrency($number, $currency){
		$result = "0.00";
		if( $currency == "R$" )
			$result = "R$ " . number_format($number, 2, ',', '.');
		
		return $result;
	}
	
	static function currencyToFloat($string){
		if( $string == "" )
			return 0;

		$result = $string;
		$result = preg_replace('/[^0-9|,|\.]/', "", $result);
		$result = preg_replace('/\,/', ".", $result);
		$result = preg_replace('/\.(..$)/', "x$1", $result);
		$result = preg_replace('/\.(.$)/', "x$1", $result);

		$result = preg_replace('/[\.]/', "", $result);

		$result = preg_replace('/x/', ".", $result);
		
		if( $result == "" )
			return 0;
		return $result;
	}

}